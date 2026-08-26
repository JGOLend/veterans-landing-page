/**
 * JGO Lending - veterans landing page Worker.
 *
 * Serves the static page from the assets binding and handles the enquiry
 * form POST at /api/send, relaying it to the Resend API. This replaces the
 * PHP handler the GoDaddy plan used; the validation rules are the same.
 *
 * Configuration:
 *   RESEND_API_KEY  - secret, set in the dashboard or via wrangler secret put.
 *                     NEVER in this file or in wrangler.jsonc.
 *   MAIL_FROM       - var. Must be on a domain verified in Resend.
 *   MAIL_TO         - var. Where enquiries land.
 *   RATE_LIMIT      - KV binding for the per-IP hourly throttle.
 */

const ALLOWED_GOALS = [
  "Buy my first home",
  "Buy my next home",
  "Refinance",
  "Invest",
  "Not sure yet",
];

const ALLOWED_STATUS = [
  "Ex-serving",
  "Currently serving",
  "Partner of a veteran",
  "Prefer not to say",
];

const RATE_LIMIT_PER_HOUR = 5;

export default {
  async fetch(request, env) {
    const url = new URL(request.url);
    if (url.pathname === "/api/send") {
      if (request.method !== "POST") {
        return json({ error: "Method not allowed" }, 405);
      }
      return handleSend(request, env);
    }
    return env.ASSETS.fetch(request);
  },
};

function json(body, status) {
  return new Response(JSON.stringify(body), {
    status: status || 200,
    headers: {
      "Content-Type": "application/json; charset=utf-8",
      "X-Content-Type-Options": "nosniff",
    },
  });
}

/* Keep printable characters plus tab (9), newline (10) and carriage return
   (13); drop other C0 control characters. Written as code-point checks so no
   escape sequences are needed anywhere in this file. */
function stripControls(s) {
  let out = "";
  for (const ch of s) {
    const c = ch.codePointAt(0);
    if (c > 31 || c === 9 || c === 10 || c === 13) {
      out += ch;
    }
  }
  return out;
}

function field(form, key, max) {
  const raw = form.get(key);
  if (typeof raw !== "string") return "";
  return stripControls(raw).trim().slice(0, max);
}

function esc(s) {
  return String(s)
    .split("&").join("&amp;")
    .split("<").join("&lt;")
    .split(">").join("&gt;")
    .split('"').join("&quot;")
    .split("'").join("&#39;");
}

async function handleSend(request, env) {
  let form;
  try {
    form = await request.formData();
  } catch (e) {
    return json({ error: "Bad request" }, 400);
  }

  /* Honeypot. The page strips this field before sending, so anything arriving
     with it filled is not a human on the form. Return an ordinary success so
     a bot cannot tell it was caught. */
  if (field(form, "company", 200) !== "") {
    return json({ ok: true });
  }

  /* Per-IP hourly throttle. A public endpoint that sends email must not be
     usable as an open relay. Degrades gracefully if KV is not bound. */
  if (env.RATE_LIMIT) {
    try {
      const ip = request.headers.get("CF-Connecting-IP") || "unknown";
      const key = "rl:" + ip;
      const now = Math.floor(Date.now() / 1000);
      const stored = await env.RATE_LIMIT.get(key, "json");
      const hits = Array.isArray(stored)
        ? stored.filter((t) => typeof t === "number" && t > now - 3600)
        : [];
      if (hits.length >= RATE_LIMIT_PER_HOUR) {
        return json({ error: "Too many requests" }, 429);
      }
      hits.push(now);
      await env.RATE_LIMIT.put(key, JSON.stringify(hits), { expirationTtl: 3600 });
    } catch (e) {
      // A rate-limit storage failure must not block a genuine enquiry.
      console.error("rate limit check failed:", e);
    }
  }

  /* Only the seven briefed fields. The form does not collect income, DVA
     payment amounts, medical details, DOB or account information, and none
     is accepted here either. */
  const name = field(form, "name", 200);
  const email = field(form, "email", 320);
  const phone = field(form, "phone", 60);
  const goal = field(form, "goal", 100);
  const status = field(form, "service_status", 100);
  const notes = field(form, "notes", 2000);
  const sawPost = form.get("soldier_on") != null && form.get("soldier_on") !== "";

  if (!name || !email || !phone || !goal || !status) {
    return json({ error: "Missing required field" }, 422);
  }
  const at = email.indexOf("@");
  const dot = email.lastIndexOf(".");
  if (at < 1 || dot < at + 2 || dot === email.length - 1 || email.includes(" ")) {
    return json({ error: "Invalid email" }, 422);
  }
  if (!ALLOWED_GOALS.includes(goal) || !ALLOWED_STATUS.includes(status)) {
    return json({ error: "Unexpected selection" }, 422);
  }

  if (!env.RESEND_API_KEY || !env.MAIL_FROM || !env.MAIL_TO) {
    console.error("worker not configured: RESEND_API_KEY / MAIL_FROM / MAIL_TO missing");
    return json({ error: "Server not configured" }, 500);
  }

  const rows = [
    ["Name", name],
    ["Email", email],
    ["Phone", phone],
    ["Looking to", goal],
    ["Service status", status],
    ["Anything we should know", notes !== "" ? notes : "-"],
    ["Saw the Soldier On post", sawPost ? "Yes" : "No"],
  ];

  let html =
    '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#111111">' +
    '<p style="font-size:17px;font-weight:700;margin:0 0 4px">New veterans enquiry</p>' +
    '<p style="margin:0 0 16px;color:#555555">From the veterans landing page</p>' +
    '<table cellpadding="6" cellspacing="0" border="0" style="border-collapse:collapse">';
  for (const [label, value] of rows) {
    html +=
      "<tr>" +
      '<td style="border-bottom:1px solid #eeeeee;color:#555555;vertical-align:top;white-space:nowrap">' +
      esc(label) +
      "</td>" +
      '<td style="border-bottom:1px solid #eeeeee;font-weight:600">' +
      esc(value).split("\n").join("<br>") +
      "</td>" +
      "</tr>";
  }
  html += "</table>";
  if (sawPost) {
    html +=
      '<p style="margin:16px 0 0;padding:10px 12px;background:#FAF7C8;border-left:3px solid #9D722D">' +
      "This enquirer mentioned the Soldier On post. $600 goes to Soldier On when their loan settles." +
      "</p>";
  }
  html += "</div>";

  let text = "New veterans enquiry\nFrom the veterans landing page\n\n";
  for (const [label, value] of rows) {
    text += label + ": " + value + "\n";
  }

  /* Anything other than a 2xx from Resend must reach the browser as a
     failure, so the page shows its error state, keeps the typed values, and
     does not fire the Lead pixel event. A false success here would teach
     Meta to find people whose enquiries never arrive. */
  let resendResponse;
  try {
    resendResponse = await fetch("https://api.resend.com/emails", {
      method: "POST",
      headers: {
        Authorization: "Bearer " + env.RESEND_API_KEY,
        "Content-Type": "application/json",
      },
      body: JSON.stringify({
        from: env.MAIL_FROM,
        to: [env.MAIL_TO],
        reply_to: [email],
        subject: "Veterans enquiry - " + name,
        html: html,
        text: text,
      }),
    });
  } catch (e) {
    console.error("resend request failed:", e);
    return json({ error: "Could not send" }, 502);
  }

  if (!resendResponse.ok) {
    console.error(
      "resend rejected:",
      resendResponse.status,
      await resendResponse.text().catch(() => "")
    );
    return json({ error: "Could not send" }, 502);
  }

  return json({ ok: true });
}
