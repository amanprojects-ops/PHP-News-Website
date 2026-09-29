<?php
/**
 * Telegram API AJAX Endpoint
 * Handles: get_me, set_webhook, delete_webhook, get_webhook_info, send_test
 * Called via AJAX from manage-website.php Telegram tab.
 */

session_start();
header('Content-Type: application/json');

include_once 'config.php';
include_once 'telegram_bot.php';

// Auth: Super Admin only
if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
    echo json_encode(['ok' => false, 'description' => 'Unauthorized access.']);
    exit;
}

// CSRF check
$csrf = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
    echo json_encode(['ok' => false, 'description' => 'CSRF verification failed.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'description' => 'Invalid request method.']);
    exit;
}

$action = trim($_POST['action'] ?? '');

// Load bot from DB settings
$bot = getTelegramBotFromSettings($conn);
if (!$bot && $action !== 'get_me') {
    echo json_encode(['ok' => false, 'description' => 'Telegram bot token is not configured. Please save your Bot Token first.']);
    exit;
}
// For get_me we need a bot too
if (!$bot) {
    echo json_encode(['ok' => false, 'description' => 'Telegram bot token is not configured.']);
    exit;
}

switch ($action) {

    // Verify bot token
    case 'get_me':
        $result = $bot->getMe();
        echo json_encode([
            'ok'          => !empty($result['ok']),
            'description' => $result['description'] ?? ($result['ok'] ? 'Bot verified.' : 'Invalid token.'),
            'result'      => $result['result'] ?? null,
            'logs'        => $bot->getLogs(),
        ]);
        break;

    // Register webhook
    case 'set_webhook':
        $webhookUrl = trim($_POST['webhook_url'] ?? '');
        if (empty($webhookUrl) || !filter_var($webhookUrl, FILTER_VALIDATE_URL)) {
            echo json_encode(['ok' => false, 'description' => 'Invalid or empty webhook URL.']);
            break;
        }
        if (strpos($webhookUrl, 'https://') !== 0) {
            echo json_encode(['ok' => false, 'description' => 'Webhook URL must start with https://.']);
            break;
        }
        $result = $bot->setWebhook($webhookUrl);
        echo json_encode([
            'ok'          => !empty($result['ok']),
            'description' => $result['description'] ?? ($result['ok'] ? 'Webhook registered.' : 'Failed.'),
            'result'      => $result['result'] ?? null,
            'logs'        => $bot->getLogs(),
        ]);
        break;

    // Delete webhook
    case 'delete_webhook':
        $result = $bot->deleteWebhook(false);
        echo json_encode([
            'ok'          => !empty($result['ok']),
            'description' => $result['description'] ?? ($result['ok'] ? 'Webhook removed.' : 'Failed.'),
            'result'      => $result['result'] ?? null,
            'logs'        => $bot->getLogs(),
        ]);
        break;

    // Get webhook info
    case 'get_webhook_info':
        $result = $bot->getWebhookInfo();
        echo json_encode([
            'ok'          => !empty($result['ok']),
            'description' => $result['description'] ?? 'OK',
            'result'      => $result['result'] ?? null,
            'logs'        => $bot->getLogs(),
        ]);
        break;

    // Send test message
    case 'send_test':
        $target  = in_array($_POST['target'] ?? 'group', ['group', 'channel']) ? $_POST['target'] : 'group';
        $message = trim($_POST['message'] ?? '');
        if (empty($message)) {
            echo json_encode(['ok' => false, 'description' => 'Test message cannot be empty.']);
            break;
        }
        $result = $bot->sendMessage($message, $target, 'HTML');
        echo json_encode([
            'ok'          => !empty($result['ok']),
            'description' => $result['description'] ?? ($result['ok'] ? 'Message sent.' : 'Failed to send.'),
            'result'      => $result['result'] ?? null,
            'logs'        => $bot->getLogs(),
        ]);
        break;

    default:
        echo json_encode(['ok' => false, 'description' => 'Unknown action: ' . htmlspecialchars($action)]);
        break;
}

if (isset($conn) && $conn) {
    mysqli_close($conn);
}
