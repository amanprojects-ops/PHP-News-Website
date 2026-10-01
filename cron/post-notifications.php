<?php
/**
 * ═══════════════════════════════════════════════════════
 *  CRON JOB: Post Notification Dispatcher
 * ═══════════════════════════════════════════════════════
 *
 * This standalone cron script:
 *  1. Finds all approved posts that have NOT yet been sent to Telegram.
 *  2. Sends them to configured Telegram Group and/or Channel.
 *  3. Marks posts as notified via `telegram_notified` flag.
 *
 * SETUP (Linux crontab - run every 5 minutes):
 *   * / 5 * * * * php /path/to/cron/post-notifications.php >> /path/to/logs/cron.log 2>&1
 *
 * SETUP (Windows Task Scheduler):
 *   Program: C:\php\php.exe
 *   Arguments: C:\xampp\htdocs\PHP-News-Website\cron\post-notifications.php
 *   Schedule: Every 5 minutes
 *
 * NOTE: Post writer email notifications are handled by the queue processor
 *       (cron/process-telegram-queue.php) via queueAuthorEmailNotification() in app.php.
 */

// ── Bootstrap ─────────────────────────────────────────────────────────────────
define('CRON_RUN', true);

// Resolve paths relative to this file
$rootDir = dirname(__DIR__);
require_once $rootDir . '/database/functions.php';
require_once $rootDir . '/database/migration.php';
require_once $rootDir . '/admin/app/telegram_bot.php';

// ── DB Connection ─────────────────────────────────────────────────────────────
$hostname = 'localhost';
$username = 'root';
$password = '';
$dbname   = 'blog2';

$conn = mysqli_connect($hostname, $username, $password, $dbname);
if (!$conn) {
    cronLog("ERROR: Cannot connect to database.");
    exit(1);
}

// Run migrations (ensure columns exist)
ensureSettingsSchema($conn);

// ── Logging Helper ────────────────────────────────────────────────────────────
function cronLog(string $message): void
{
    $ts = date('Y-m-d H:i:s');
    echo "[{$ts}] {$message}" . PHP_EOL;
}

// ── Load Settings ─────────────────────────────────────────────────────────────
$settingsQ = mysqli_query($conn, "SELECT * FROM `settings` LIMIT 1");
$settings  = $settingsQ ? mysqli_fetch_assoc($settingsQ) : [];

if (empty($settings['telegramEnabled'])) {
    cronLog("INFO: Telegram notifications are disabled in system settings. Exiting.");
    mysqli_close($conn);
    exit(0);
}

$bot = getTelegramBotFromSettings($conn);
if (!$bot) {
    cronLog("ERROR: Telegram bot token not configured.");
    mysqli_close($conn);
    exit(1);
}

// ── Ensure `telegram_notified` column exists in `post` table ─────────────────
$postCols = [];
$colRes = mysqli_query($conn, "SHOW COLUMNS FROM `post`");
if ($colRes) {
    while ($cr = mysqli_fetch_assoc($colRes)) {
        $postCols[strtolower($cr['Field'])] = true;
    }
}
if (!isset($postCols['telegram_notified'])) {
    mysqli_query($conn, "ALTER TABLE `post` ADD COLUMN `telegram_notified` TINYINT(1) NOT NULL DEFAULT 0 AFTER `postStatus`");
    cronLog("INFO: Added `telegram_notified` column to `post` table.");
}

// ── Fetch pending approved posts not yet sent ─────────────────────────────────
$pendingQ = "
    SELECT p.post_id, p.title, p.sort_details, p.post_img, p.post_slug,
           c.category_name, c.category_slug
    FROM `post` p
    LEFT JOIN `category` c ON p.category = c.category_id
    WHERE p.postStatus = 'Y'
      AND (p.telegram_notified IS NULL OR p.telegram_notified = 0)
    ORDER BY p.post_id ASC
    LIMIT 20
";

$pendingRes = mysqli_query($conn, $pendingQ);

if (!$pendingRes || mysqli_num_rows($pendingRes) === 0) {
    cronLog("INFO: No pending posts to notify. All up-to-date.");
    mysqli_close($conn);
    exit(0);
}

$siteUrl  = rtrim($settings['websiteUrl'] ?? '', '/');
$siteName = $settings['websitename'] ?? 'News Portal';
$sent     = 0;
$failed   = 0;

while ($post = mysqli_fetch_assoc($pendingRes)) {
    $postId   = (int)$post['post_id'];
    $title    = $post['title'] ?? 'New Post';

    // Build full post URL
    $post['post_url'] = getPostUrl($post, $siteUrl);

    cronLog("Processing post #{$postId}: " . mb_substr($title, 0, 60));

    // Build message
    $imageUrl = !empty($post['post_img'])
        ? ($siteUrl . '/assets/postImage/' . rawurlencode(basename($post['post_img'])))
        : '';

    $shortDesc = mb_substr(strip_tags($post['sort_details'] ?? ''), 0, 200);
    $catName   = htmlspecialchars($post['category_name'] ?? '');

    $msg  = "📰 <b>" . htmlspecialchars($title, ENT_QUOTES) . "</b>\n\n";
    if (!empty($catName)) {
        $msg .= "🏷️ Category: <i>{$catName}</i>\n";
    }
    if (!empty($shortDesc)) {
        $msg .= "\n" . $shortDesc . (mb_strlen($post['sort_details'] ?? '') > 200 ? '...' : '') . "\n";
    }
    $msg .= "\n🔗 <a href=\"{$post['post_url']}\">Read Full Article</a>";
    $msg .= "\n\n— <i>" . htmlspecialchars($siteName) . "</i>";

    $groupOk   = true;
    $channelOk = true;

    // Send to Group
    if (!empty($settings['telegramGroupId'])) {
        if (!empty($imageUrl)) {
            $r = $bot->sendPhoto($imageUrl, $msg, 'group', 'HTML');
            if (empty($r['ok'])) {
                $r = $bot->sendMessage($msg, 'group');
            }
        } else {
            $r = $bot->sendMessage($msg, 'group');
        }
        if (empty($r['ok'])) {
            cronLog("  ✗ Group send failed: " . ($r['description'] ?? 'Unknown error'));
            $groupOk = false;
        } else {
            cronLog("  ✓ Group: message_id=" . ($r['result']['message_id'] ?? 'N/A'));
        }
    }

    // Send to Channel
    if (!empty($settings['telegramChannelId'])) {
        if (!empty($imageUrl)) {
            $r = $bot->sendPhoto($imageUrl, $msg, 'channel', 'HTML');
            if (empty($r['ok'])) {
                $r = $bot->sendMessage($msg, 'channel');
            }
        } else {
            $r = $bot->sendMessage($msg, 'channel');
        }
        if (empty($r['ok'])) {
            cronLog("  ✗ Channel send failed: " . ($r['description'] ?? 'Unknown error'));
            $channelOk = false;
        } else {
            cronLog("  ✓ Channel: message_id=" . ($r['result']['message_id'] ?? 'N/A'));
        }
    }

    // Mark as notified only if at least one succeeded
    if ($groupOk && $channelOk) {
        mysqli_query($conn, "UPDATE `post` SET `telegram_notified` = 1 WHERE `post_id` = {$postId}");
        cronLog("  ✔ Post #{$postId} marked as notified.");
        $sent++;
    } else {
        cronLog("  ⚠ Post #{$postId} NOT marked — will retry next run.");
        $failed++;
    }

    // Brief pause to avoid Telegram rate limits (30 messages/sec group limit)
    usleep(500000); // 0.5 second between posts
}

cronLog("──────────────────────────────────");
cronLog("Summary: Sent={$sent}, Failed={$failed}");
cronLog("Cron completed.");

mysqli_close($conn);
exit(0);
