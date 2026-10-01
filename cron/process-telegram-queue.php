<?php
/**
 * ═══════════════════════════════════════════════════════════════════════════
 *  CRON JOB: Telegram Action Queue Processor
 * ═══════════════════════════════════════════════════════════════════════════
 *
 * Processes pending Telegram notifications from `telegram_queue` table.
 * Each queue item is sent as a structured action log message to Group/Channel.
 *
 * SETUP (Linux crontab - run every minute):
 *   * * * * * php /path/to/cron/process-telegram-queue.php >> /path/to/logs/tg-queue.log 2>&1
 *
 * SETUP (Windows Task Scheduler):
 *   Program: C:\php\php.exe  (or C:\xampp\php\php.exe)
 *   Arguments: C:\xampp\htdocs\PHP-News-Website\cron\process-telegram-queue.php
 *   Schedule: Every 1 minute
 *
 * SETUP (XAMPP — quick test from browser or CLI):
 *   php C:\xampp\htdocs\PHP-News-Website\cron\process-telegram-queue.php
 */

// ── Bootstrap ─────────────────────────────────────────────────────────────────
define('CRON_RUN', true);

$rootDir = dirname(__DIR__);
require_once $rootDir . '/database/functions.php';
require_once $rootDir . '/database/migration.php';
require_once $rootDir . '/admin/app/telegram_bot.php';
require_once $rootDir . '/database/Mailer.php';

// ── Logging Helper ────────────────────────────────────────────────────────────
function qLog(string $message): void
{
    $ts = date('Y-m-d H:i:s');
    echo "[{$ts}] {$message}" . PHP_EOL;
}

// ── DB Connection ─────────────────────────────────────────────────────────────
require_once $rootDir . '/config.php';
if (!isset($conn) || !$conn) {
    qLog("ERROR: Cannot connect to database.");
    exit(1);
}

ensureSettingsSchema($conn);
ensureTelegramQueueSchema($conn);

// ── Check Telegram Enabled ───────────────────────────────────────────────────
$settingsQ = mysqli_query($conn, "SELECT * FROM `settings` LIMIT 1");
$settings  = $settingsQ ? mysqli_fetch_assoc($settingsQ) : [];

if (empty($settings['telegramEnabled'])) {
    qLog("INFO: Telegram notifications disabled. Exiting.");
    mysqli_close($conn);
    exit(0);
}

$bot = getTelegramBotFromSettings($conn);
if (!$bot) {
    qLog("ERROR: Telegram bot token not configured.");
    mysqli_close($conn);
    exit(1);
}

$siteName = $settings['websitename'] ?? 'News Portal';

// ── Action → Emoji & Label Map ───────────────────────────────────────────────
$actionMap = [
    'approve'   => ['emoji' => '✅', 'label' => 'Post Approved',                    'color' => '🟢'],
    'published' => ['emoji' => '📰', 'label' => 'New Article Published',            'color' => '🔵'],
    'reject'    => ['emoji' => '❌', 'label' => 'Post Rejected',                    'color' => '🔴'],
    'draft'     => ['emoji' => '📝', 'label' => 'Post Saved as Draft',              'color' => '🟡'],
    'new_post'  => ['emoji' => '🆕', 'label' => 'New Post Submitted (Pending)',     'color' => '🟠'],
    'resubmit'  => ['emoji' => '🔄', 'label' => 'Post Resubmitted for Approval',   'color' => '🟣'],
];

// ── Build Message for Queue Item ─────────────────────────────────────────────
function buildActionMessage(array $item, array $actionMap, string $siteName, string $targetType = 'channel'): string
{
    $action = $item['action_type'];
    $info   = $actionMap[$action] ?? ['emoji' => '📋', 'label' => ucfirst($action), 'color' => '⚪'];
    $extra  = !empty($item['extra_data']) ? json_decode($item['extra_data'], true) : [];

    $title      = htmlspecialchars($item['post_title'] ?? 'Untitled', ENT_QUOTES);
    $catName    = htmlspecialchars($item['category_name'] ?? '', ENT_QUOTES);
    $actorName  = htmlspecialchars($item['actor_name'] ?? 'System', ENT_QUOTES);
    $authorName = htmlspecialchars($extra['author_name'] ?? '', ENT_QUOTES);
    $postUrl    = $item['post_url'] ?? '';
    $shortDesc  = $extra['sort_details'] ?? '';

    // For 'published' action → build rich article card
    if ($action === 'published') {
        $msg  = "{$info['emoji']} <b>{$title}</b>\n\n";
        if (!empty($catName)) {
            $msg .= "🏷️ Category: <i>{$catName}</i>\n";
        }
        if ($targetType === 'group' && !empty($authorName)) {
            $msg .= "✍️ Author: <i>{$authorName}</i>\n";
        }
        if (!empty($shortDesc)) {
            $msg .= "\n" . htmlspecialchars($shortDesc, ENT_QUOTES) . "\n";
        }
        if (!empty($postUrl)) {
            $msg .= "\n🔗 <a href=\"{$postUrl}\">Read Full Article</a>\n";
        }
        $msg .= "\n— <i>{$siteName}</i>";
        return $msg;
    }

    // For action logs → structured status message
    $msg  = "{$info['color']} {$info['emoji']} <b>{$info['label']}</b>\n";
    $msg .= "━━━━━━━━━━━━━━━━━━━━\n\n";
    $msg .= "📄 <b>Post:</b> {$title}\n";

    if (!empty($catName)) {
        $msg .= "🏷️ <b>Category:</b> <i>{$catName}</i>\n";
    }
    if (!empty($authorName)) {
        $msg .= "✍️ <b>Author:</b> {$authorName}\n";
    }
    $msg .= "👤 <b>Action by:</b> {$actorName}\n";
    $msg .= "🕐 <b>Time:</b> " . date('d M Y, h:i A') . "\n";

    if (!empty($postUrl) && $action === 'approve') {
        $msg .= "\n🔗 <a href=\"{$postUrl}\">View Post</a>\n";
    }

    $msg .= "\n— <i>{$siteName}</i>";
    return $msg;
}

// ── Fetch Pending Queue Items ────────────────────────────────────────────────
$batchSize = 20;
$pendingQ  = mysqli_query($conn,
    "SELECT * FROM `telegram_queue`
     WHERE `status` = 'pending' AND `attempts` < `max_attempts`
     ORDER BY `created_at` ASC
     LIMIT {$batchSize}"
);

if (!$pendingQ || mysqli_num_rows($pendingQ) === 0) {
    qLog("INFO: Queue empty — nothing to process.");
    mysqli_close($conn);
    exit(0);
}

$sent   = 0;
$failed = 0;

// ── Process Each Queue Item ──────────────────────────────────────────────────
while ($item = mysqli_fetch_assoc($pendingQ)) {
    $queueId = (int)$item['id'];
    $action  = $item['action_type'];
    $target  = $item['target'] ?? 'both';

    qLog("Processing queue #{$queueId}: [{$action}] " . mb_substr($item['post_title'] ?? '', 0, 50));

    // Mark as processing
    mysqli_query($conn, "UPDATE `telegram_queue` SET `status` = 'processing', `attempts` = `attempts` + 1 WHERE `id` = {$queueId}");

    // ── EMAIL QUEUE ITEMS (target = 'email') ──────────────────────────────
    if ($target === 'email') {
        $extra = !empty($item['extra_data']) ? json_decode($item['extra_data'], true) : [];
        $toEmail      = $extra['to_email'] ?? '';
        $emailSubject = $extra['email_subject'] ?? 'Post Status Update';
        $emailBody    = $extra['email_body'] ?? '';

        if (empty($toEmail) || empty($emailBody)) {
            qLog("  ✗ Email skipped — missing recipient or body.");
            mysqli_query($conn, "UPDATE `telegram_queue` SET `status` = 'failed', `error_message` = 'Missing email or body', `processed_at` = NOW() WHERE `id` = {$queueId}");
            $failed++;
            continue;
        }

        qLog("  📧 Sending email to: {$toEmail}");
        $mailResult = sendSystemMail($conn, $toEmail, $emailSubject, $emailBody);

        if (!empty($mailResult['success'])) {
            mysqli_query($conn, "UPDATE `telegram_queue` SET `status` = 'sent', `processed_at` = NOW() WHERE `id` = {$queueId}");
            qLog("  ✔ Email sent successfully to {$toEmail}");
            $sent++;
        } else {
            $errMsg = mysqli_real_escape_string($conn, $mailResult['message'] ?? 'Email send failed');
            $currentAttempts = (int)$item['attempts'] + 1;
            if ($currentAttempts >= (int)$item['max_attempts']) {
                mysqli_query($conn, "UPDATE `telegram_queue` SET `status` = 'failed', `error_message` = '{$errMsg}', `processed_at` = NOW() WHERE `id` = {$queueId}");
                qLog("  ✗ Email FAILED permanently after {$currentAttempts} attempts: {$errMsg}");
            } else {
                mysqli_query($conn, "UPDATE `telegram_queue` SET `status` = 'pending', `error_message` = '{$errMsg}' WHERE `id` = {$queueId}");
                qLog("  ⚠ Email will retry (attempt {$currentAttempts}/{$item['max_attempts']})");
            }
            $failed++;
        }
        usleep(300000); // 0.3s between emails
        continue;
    }

    // ── TELEGRAM QUEUE ITEMS ──────────────────────────────────────────────
    $imageUrl = $item['image_url'] ?? '';

    $groupOk   = true;
    $channelOk = true;

    // Send to Group
    if (($target === 'group' || $target === 'both') && !empty($settings['telegramGroupId'])) {
        $msgGroup = buildActionMessage($item, $actionMap, $siteName, 'group');
        // For 'published' posts, try sending photo first
        if ($action === 'published' && !empty($imageUrl)) {
            $r = $bot->sendPhoto($imageUrl, $msgGroup, 'group', 'HTML');
            if (empty($r['ok'])) {
                $r = $bot->sendMessage($msgGroup, 'group');
            }
        } else {
            $r = $bot->sendMessage($msgGroup, 'group');
        }
        if (empty($r['ok'])) {
            qLog("  ✗ Group send failed: " . ($r['description'] ?? 'Unknown'));
            $groupOk = false;
        } else {
            qLog("  ✓ Group: msg_id=" . ($r['result']['message_id'] ?? 'N/A'));
        }
    }

    // Send to Channel
    if (($target === 'channel' || $target === 'both') && !empty($settings['telegramChannelId'])) {
        $msgChannel = buildActionMessage($item, $actionMap, $siteName, 'channel');
        // For 'published' posts, try sending photo first
        if ($action === 'published' && !empty($imageUrl)) {
            $r = $bot->sendPhoto($imageUrl, $msgChannel, 'channel', 'HTML');
            if (empty($r['ok'])) {
                $r = $bot->sendMessage($msgChannel, 'channel');
            }
        } else {
            $r = $bot->sendMessage($msgChannel, 'channel');
        }
        if (empty($r['ok'])) {
            qLog("  ✗ Channel send failed: " . ($r['description'] ?? 'Unknown'));
            $channelOk = false;
        } else {
            qLog("  ✓ Channel: msg_id=" . ($r['result']['message_id'] ?? 'N/A'));
        }
    }

    // Update queue status
    if ($groupOk && $channelOk) {
        mysqli_query($conn, "UPDATE `telegram_queue` SET `status` = 'sent', `processed_at` = NOW() WHERE `id` = {$queueId}");
        qLog("  ✔ Queue #{$queueId} marked as sent.");
        $sent++;
    } else {
        $errMsg = mysqli_real_escape_string($conn, ($r['description'] ?? 'Partial or full failure'));
        $currentAttempts = (int)$item['attempts'] + 1;
        if ($currentAttempts >= (int)$item['max_attempts']) {
            mysqli_query($conn, "UPDATE `telegram_queue` SET `status` = 'failed', `error_message` = '{$errMsg}', `processed_at` = NOW() WHERE `id` = {$queueId}");
            qLog("  ✗ Queue #{$queueId} FAILED permanently after {$currentAttempts} attempts.");
        } else {
            mysqli_query($conn, "UPDATE `telegram_queue` SET `status` = 'pending', `error_message` = '{$errMsg}' WHERE `id` = {$queueId}");
            qLog("  ⚠ Queue #{$queueId} will retry (attempt {$currentAttempts}/{$item['max_attempts']}).");
        }
        $failed++;
    }

    // Rate limit: 0.5s between messages
    usleep(500000);
}

// ── Cleanup old sent items (older than 7 days) ───────────────────────────────
mysqli_query($conn, "DELETE FROM `telegram_queue` WHERE `status` = 'sent' AND `processed_at` < DATE_SUB(NOW(), INTERVAL 7 DAY)");

qLog("──────────────────────────────────");
qLog("Summary: Sent={$sent}, Failed={$failed}");
qLog("Queue processing completed.");

mysqli_close($conn);
exit(0);
