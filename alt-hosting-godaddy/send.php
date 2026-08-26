<?php
/**
 * JGO Lending - veterans enquiry form handler.
 *
 * Receives the POST from veterans/index.html and relays it to the destination
 * inbox using the Resend API.
 *
 * The Resend API key is NEVER in this file and never reaches the browser.
 * It lives in jgo-config.php, which must sit outside the public web root.
 *
 * Requires PHP 7.4+ with cURL.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

/**
 * Send a JSON error and stop. The browser only ever sees a generic message;
 * detail goes to the server error log.
 */
function jgo_fail($status, $message)
{
    http_response_code($status);
    echo json_encode(array('error' => $message), JSON_UNESCAPED_SLASHES);
    exit;
}

/* ---------------------------------------------------------------------------
 * Config.
 *
 * Preferred location is one level above the web root so the file can never be
 * served even if PHP is misconfigured. Falls back to sitting beside the web
 * root on hosts that do not allow files above it.
 * ------------------------------------------------------------------------- */
$configPaths = array(
    __DIR__ . '/../../jgo-config.php',  // preferred: outside public_html
    __DIR__ . '/../jgo-config.php',     // fallback: inside public_html
);

$config = null;
foreach ($configPaths as $configPath) {
    if (is_file($configPath)) {
        $config = require $configPath;
        break;
    }
}

if (!is_array($config) || empty($config['resend_api_key']) || empty($config['from']) || empty($config['to'])) {
    error_log('veterans/send.php: jgo-config.php missing or incomplete');
    jgo_fail(500, 'Server not configured');
}

if (!isset($_SERVER['REQUEST_METHOD']) || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    jgo_fail(405, 'Method not allowed');
}

/* ---------------------------------------------------------------------------
 * Honeypot.
 *
 * The page strips this field before sending, so anything arriving with it
 * filled did not come from a human using the form. Return an ordinary success
 * so a bot cannot tell it was caught.
 * ------------------------------------------------------------------------- */
if (!empty($_POST['company'])) {
    echo json_encode(array('ok' => true));
    exit;
}

/* ---------------------------------------------------------------------------
 * Light per-IP throttle. This endpoint can send email, so it must not become
 * an open relay for whoever finds the URL.
 * ------------------------------------------------------------------------- */
$limit  = isset($config['rate_limit_per_hour']) ? (int)$config['rate_limit_per_hour'] : 5;
$ip     = isset($_SERVER['REMOTE_ADDR']) ? (string)$_SERVER['REMOTE_ADDR'] : 'unknown';
$bucket = sys_get_temp_dir() . '/jgo_veterans_' . hash('sha256', $ip) . '.json';
$now    = time();
$hits   = array();

if (is_file($bucket)) {
    $decoded = json_decode((string)file_get_contents($bucket), true);
    if (is_array($decoded)) {
        foreach ($decoded as $t) {
            if (is_int($t) && $t > $now - 3600) {
                $hits[] = $t;
            }
        }
    }
}

if (count($hits) >= $limit) {
    jgo_fail(429, 'Too many requests');
}

$hits[] = $now;
@file_put_contents($bucket, json_encode($hits), LOCK_EX);

/* ---------------------------------------------------------------------------
 * Read and validate.
 *
 * Only the seven fields the brief allows. The form does not collect income,
 * DVA payment amounts, medical details, date of birth or account information,
 * and none is accepted here either.
 * ------------------------------------------------------------------------- */
function jgo_field($key, $max)
{
    $value = isset($_POST[$key]) ? trim((string)$_POST[$key]) : '';
    $stripped = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F]/u', '', $value);
    $value = ($stripped === null) ? '' : $stripped;
    return function_exists('mb_substr') ? mb_substr($value, 0, $max) : substr($value, 0, $max);
}

$name    = jgo_field('name', 200);
$email   = jgo_field('email', 320);
$phone   = jgo_field('phone', 60);
$goal    = jgo_field('goal', 100);
$status  = jgo_field('service_status', 100);
$notes   = jgo_field('notes', 2000);
$sawPost = !empty($_POST['soldier_on']);

if ($name === '' || $email === '' || $phone === '' || $goal === '' || $status === '') {
    jgo_fail(422, 'Missing required field');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    jgo_fail(422, 'Invalid email');
}

$allowedGoals = array('Buy my first home', 'Buy my next home', 'Refinance', 'Invest', 'Not sure yet');
$allowedStatus = array('Ex-serving', 'Currently serving', 'Partner of a veteran', 'Prefer not to say');

if (!in_array($goal, $allowedGoals, true) || !in_array($status, $allowedStatus, true)) {
    jgo_fail(422, 'Unexpected selection');
}

/* ---------------------------------------------------------------------------
 * Compose.
 *
 * Header injection is not possible here because every value travels in the
 * JSON body and never in a mail header. Values are still escaped for the HTML
 * part of the message.
 * ------------------------------------------------------------------------- */
function jgo_esc($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$rows = array(
    'Name'                    => $name,
    'Email'                   => $email,
    'Phone'                   => $phone,
    'Looking to'              => $goal,
    'Service status'          => $status,
    'Anything we should know' => ($notes !== '' ? $notes : '-'),
    'Saw the Soldier On post' => ($sawPost ? 'Yes' : 'No'),
);

$html  = '<div style="font-family:Arial,Helvetica,sans-serif;font-size:15px;color:#111111">';
$html .= '<p style="font-size:17px;font-weight:700;margin:0 0 4px">New veterans enquiry</p>';
$html .= '<p style="margin:0 0 16px;color:#555555">From jgolending.com.au/veterans</p>';
$html .= '<table cellpadding="6" cellspacing="0" border="0" style="border-collapse:collapse">';

foreach ($rows as $label => $value) {
    $html .= '<tr>';
    $html .= '<td style="border-bottom:1px solid #eeeeee;color:#555555;vertical-align:top;white-space:nowrap">' . jgo_esc($label) . '</td>';
    $html .= '<td style="border-bottom:1px solid #eeeeee;font-weight:600">' . nl2br(jgo_esc($value)) . '</td>';
    $html .= '</tr>';
}

$html .= '</table>';

if ($sawPost) {
    $html .= '<p style="margin:16px 0 0;padding:10px 12px;background:#FAF7C8;border-left:3px solid #9D722D">';
    $html .= 'This enquirer mentioned the Soldier On post. $600 goes to Soldier On when their loan settles.';
    $html .= '</p>';
}

$html .= '</div>';

$text = "New veterans enquiry\nFrom jgolending.com.au/veterans\n\n";
foreach ($rows as $label => $value) {
    $text .= $label . ': ' . $value . "\n";
}

/* ---------------------------------------------------------------------------
 * Send via Resend.
 * ------------------------------------------------------------------------- */
$payload = array(
    'from'     => $config['from'],
    'to'       => is_array($config['to']) ? $config['to'] : array($config['to']),
    'reply_to' => array($email),
    'subject'  => 'Veterans enquiry - ' . $name,
    'html'     => $html,
    'text'     => $text,
);

if (!function_exists('curl_init')) {
    error_log('veterans/send.php: cURL extension not available');
    jgo_fail(500, 'Server not configured');
}

$ch = curl_init('https://api.resend.com/emails');
curl_setopt_array($ch, array(
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 15,
    CURLOPT_HTTPHEADER     => array(
        'Authorization: Bearer ' . $config['resend_api_key'],
        'Content-Type: application/json',
    ),
    CURLOPT_POSTFIELDS     => json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
));

$response = curl_exec($ch);
$httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

/*
 * Anything other than a 2xx must reach the browser as a failure, so the page
 * shows its error state, keeps the typed values, and does not fire the Lead
 * pixel event. A false success here would teach Meta to find people whose
 * enquiries never arrive.
 */
if ($response === false || $httpCode < 200 || $httpCode >= 300) {
    error_log('veterans/send.php: Resend failed. HTTP ' . $httpCode . ' ' . $curlErr . ' ' . (string)$response);
    jgo_fail(502, 'Could not send');
}

echo json_encode(array('ok' => true));
