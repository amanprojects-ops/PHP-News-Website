<?php
/**
 * Telegram Webhook Handler
 * ────────────────────────
 * This file receives incoming updates from Telegram via webhook.
 * Register this file's public HTTPS URL using the "Set Webhook" button
 * in Admin → Settings Manager → Telegram Services.
 *
 * SECURITY:
 *  - This file should be publicly accessible via HTTPS.
 *  - Only Telegram's servers send POST requests to this endpoint.
 *  - Optionally add a secret token check (Telegram header X-Telegram-Bot-Api-Secret-Token).
 *
 * USAGE:
 *  1. Save your Bot Token via Admin → Settings Manager → Telegram Services.
 *  2. Enter this file's full URL in the Webhook Runner.
 *  3. Click "Set Webhook".
 */

// ── Bootstrap ────────────────────────────────────────────────────────────────
$rootDir = dirname(dirname(__DIR__));
require_once $rootDir . '/database/functions.php';

// DB connection
$hostname = 'localhost';
$dbUser   = 'root';
$dbPass   = '';
$dbName   = 'blog2';

$conn = @mysqli_connect($hostname, $dbUser, $dbPass, $dbName);

// Only accept POST from Telegram
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method Not Allowed');
}

// Read raw body
$rawBody = file_get_contents('php://input');
$update  = json_decode($rawBody, true);

if (empty($update)) {
    http_response_code(400);
    exit('Bad Request');
}

// Log incoming update (optional — writes to a log file)
$logFile = __DIR__ . '/webhook-log.txt';
if (is_writable(dirname($logFile))) {
    $logEntry = '[' . date('Y-m-d H:i:s') . '] ' . substr($rawBody, 0, 500) . PHP_EOL;
    @file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// ── Handle Incoming Update ────────────────────────────────────────────────────
// This is a minimal handler — extend with your own bot command logic below.

$message = $update['message'] ?? $update['channel_post'] ?? null;

if ($message) {
    $chatId   = $message['chat']['id'] ?? '';
    $text     = $message['text'] ?? '';
    $fromUser = $message['from']['username'] ?? $message['from']['first_name'] ?? 'User';

    // Example: respond to /start command
    if (trim($text) === '/start' || trim($text) === '/ping') {
        // Load bot settings from DB and send response
        if ($conn) {
            require_once __DIR__ . '/telegram_bot.php';
            $bot = getTelegramBotFromSettings($conn);
            if ($bot) {
                $sQ = mysqli_query($conn, "SELECT websitename FROM settings LIMIT 1");
                $s  = $sQ ? mysqli_fetch_assoc($sQ) : [];
                $siteName = $s['websitename'] ?? 'News Portal';
                $bot->sendMessage("👋 Hello <b>{$fromUser}</b>! I am the official bot for <b>{$siteName}</b>.\n\nI'll notify you about new articles and updates.", (string)$chatId);
            }
        }
    }
}

// Always return 200 OK to Telegram so it stops retrying
http_response_code(200);
echo 'OK';

if (isset($conn) && $conn) {
    mysqli_close($conn);
}
