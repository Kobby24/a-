<?php

/**
 * Static contact form email handler.
 *
 * This version expects plain POST fields such as first_name, last_name, email,
 * phone, and message. It also keeps backward compatibility with the older
 * WPForms nested field structure so it can safely handle either format.
 */

function readFieldValue($source, $keys, $default = '')
{
    $value = $source;

    foreach ($keys as $key) {
        if (!is_array($value) || !array_key_exists($key, $value)) {
            return $default;
        }

        $value = $value[$key];
    }

    return is_string($value) ? trim($value) : trim((string) $value);
}

function sanitizeText($value)
{
    $value = preg_replace('/[\r\n\t]+/', ' ', (string) $value);
    $value = strip_tags($value);
    $value = preg_replace('/\s+/', ' ', $value);
    return trim($value);
}

function safeHeaderValue($value)
{
    $value = str_replace(["\r", "\n"], '', (string) $value);
    return preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Method Not Allowed</title></head><body><p>Method not allowed.</p></body></html>';
    exit;
}

$recipient = 'kobbygilbert233@gmail.com';
$fromEmail = 'kobbygilbert233@gmail.com';

$firstName = trim((string) ($_POST['first_name'] ?? readFieldValue($_POST['wpforms']['fields'] ?? [], [0, 'first'], '')));
$lastName  = trim((string) ($_POST['last_name'] ?? readFieldValue($_POST['wpforms']['fields'] ?? [], [0, 'last'], '')));
$email     = trim((string) ($_POST['email'] ?? readFieldValue($_POST['wpforms']['fields'] ?? [], [1], '')));
$phone     = trim((string) ($_POST['phone'] ?? readFieldValue($_POST['wpforms']['fields'] ?? [], [3], '')));
$message   = trim((string) ($_POST['message'] ?? readFieldValue($_POST['wpforms']['fields'] ?? [], [2], '')));

$fullName = trim($firstName . ' ' . $lastName);

$sanitizedName = sanitizeText($fullName);
$sanitizedEmail = filter_var($email, FILTER_SANITIZE_EMAIL);
$sanitizedPhone = sanitizeText($phone);
$sanitizedMessage = sanitizeText($message);

if ($sanitizedName === '' || !filter_var($sanitizedEmail, FILTER_VALIDATE_EMAIL) || $sanitizedMessage === '') {
    http_response_code(400);
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Missing Information</title></head><body><p>Name, a valid email, and message are required.</p></body></html>';
    exit;
}

$recipient = safeHeaderValue($recipient);
$fromEmail = safeHeaderValue($fromEmail);
$sanitizedEmail = safeHeaderValue($sanitizedEmail);
$sanitizedPhone = safeHeaderValue($sanitizedPhone);

$subject = 'New Contact Form Submission';
$body = "Name: {$sanitizedName}\n";
$body .= "Email: {$sanitizedEmail}\n";
$body .= "Phone: " . ($sanitizedPhone !== '' ? $sanitizedPhone : 'Not provided') . "\n\n";
$body .= "Message:\n{$sanitizedMessage}\n";

$headers = [
    'From: ' . $fromEmail,
    'Reply-To: ' . $sanitizedEmail,
    'Content-Type: text/plain; charset=UTF-8',
    'X-Mailer: PHP/' . phpversion()
];

$success = mail($recipient, $subject, $body, implode("\r\n", $headers));

if ($success) {
    echo '<!doctype html><html><head><meta charset="utf-8"><title>Message Sent</title></head><body style="font-family:Arial,sans-serif;padding:40px;max-width:680px;margin:40px auto;color:#1f2937;line-height:1.6;">';
    echo '<h2 style="margin-bottom:12px;">Thank you.</h2>';
    echo '<p>Your message has been sent successfully.</p>';
    echo '</body></html>';
    exit;
}

http_response_code(500);
echo '<!doctype html><html><head><meta charset="utf-8"><title>Message Failed</title></head><body style="font-family:Arial,sans-serif;padding:40px;max-width:680px;margin:40px auto;color:#1f2937;line-height:1.6;">';
echo '<h2 style="margin-bottom:12px;">Unable to send message</h2>';
echo '<p>Please try again later.</p>';
echo '</body></html>';
