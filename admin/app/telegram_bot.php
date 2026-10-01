<?php
/**
 * TelegramBot — Full CRUD Service Class
 *
 * Supports:
 *  • sendMessage()      — Send text or HTML message to group or channel
 *  • editMessage()      — Edit an existing message by message_id
 *  • deleteMessage()    — Delete a message by message_id
 *  • sendPhoto()        — Send photo with optional caption
 *  • pinMessage()       — Pin a message in chat
 *  • unpinMessage()     — Unpin a message from chat
 *  • setWebhook()       — Register webhook URL with Telegram
 *  • deleteWebhook()    — Remove registered webhook
 *  • getWebhookInfo()   — Get current webhook status
 *
 * Settings are loaded from DB `settings` table automatically (no hardcoded tokens).
 */

if (!class_exists('TelegramBot')) {
    class TelegramBot
    {
        private string $botToken;
        private string $groupId;
        private string $channelId;
        private string $apiBase;
        private array  $lastResponse = [];
        private array  $logs = [];

        /**
         * Constructor — accepts credentials directly or loads from DB.
         *
         * @param string $botToken   Bot API token
         * @param string $groupId    Telegram Group chat_id (negative, e.g. -100...)
         * @param string $channelId  Telegram Channel username or chat_id (e.g. @mychannel)
         */
        public function __construct(string $botToken = '', string $groupId = '', string $channelId = '')
        {
            $this->botToken  = $botToken;
            $this->groupId   = $groupId;
            $this->channelId = $channelId;
            $this->apiBase   = 'https://api.telegram.org/bot';
        }

        // ── Internal helpers ─────────────────────────────────────────────────

        private function log(string $msg): void
        {
            $this->logs[] = '[' . date('H:i:s') . '] ' . $msg;
        }

        public function getLogs(): array
        {
            return $this->logs;
        }

        public function getLastResponse(): array
        {
            return $this->lastResponse;
        }

        /**
         * Execute a Telegram Bot API method via HTTP POST (cURL).
         *
         * @param string $method    API method name (e.g. sendMessage)
         * @param array  $payload   Request parameters
         * @return array            Decoded API response
         */
        private function call(string $method, array $payload): array
        {
            if (empty($this->botToken)) {
                $this->log("ERROR: Bot token is not configured.");
                return ['ok' => false, 'description' => 'Bot token is not configured.'];
            }

            $url = $this->apiBase . $this->botToken . '/' . $method;
            $this->log("API → {$method} | chat: " . ($payload['chat_id'] ?? 'N/A'));

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_SSL_VERIFYPEER => false,
            ]);

            $raw = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($raw === false || !empty($curlError)) {
                $this->log("cURL ERROR: {$curlError}");
                return ['ok' => false, 'description' => 'cURL error: ' . $curlError];
            }

            $response = json_decode($raw, true) ?? ['ok' => false, 'description' => 'Invalid JSON response (HTTP ' . $httpCode . ')'];
            $this->lastResponse = $response;

            if (!empty($response['ok'])) {
                $this->log("SUCCESS: {$method}");
            } else {
                $this->log("FAILED [{$httpCode}]: " . ($response['description'] ?? 'Unknown error'));
            }

            return $response;
        }

        /**
         * Resolve target chat_id based on target string.
         * Target can be:  'group', 'channel', or any explicit chat_id / username.
         */
        private function resolveChatId(string $target): string
        {
            if ($target === 'group') {
                return $this->groupId;
            }
            if ($target === 'channel') {
                return $this->channelId;
            }
            return $target; // explicit chat_id or username passed
        }

        // ── Public CRUD Methods ──────────────────────────────────────────────

        /**
         * Send a text message to group, channel, or explicit chat.
         *
         * @param string $text        Message text (HTML tags supported if parse_mode = HTML)
         * @param string $target      'group' | 'channel' | explicit chat_id
         * @param string $parseMode   'HTML' (default) | 'Markdown' | ''
         * @param bool   $disablePreview  Disable link preview
         * @return array              API response with message_id on success
         */
        public function sendMessage(
            string $text,
            string $target = 'group',
            string $parseMode = 'HTML',
            bool $disablePreview = true
        ): array {
            $chatId = $this->resolveChatId($target);

            if (empty($chatId)) {
                $this->log("ERROR: chat_id is empty for target '{$target}'.");
                return ['ok' => false, 'description' => "No chat_id configured for target: {$target}"];
            }

            $payload = [
                'chat_id'                  => $chatId,
                'text'                     => $text,
                'disable_web_page_preview' => $disablePreview ? 'true' : 'false',
            ];

            if (!empty($parseMode)) {
                $payload['parse_mode'] = $parseMode;
            }

            return $this->call('sendMessage', $payload);
        }

        /**
         * Edit an existing message's text.
         *
         * @param int    $messageId   Telegram message_id to edit
         * @param string $newText     New text content
         * @param string $target      'group' | 'channel' | explicit chat_id
         * @param string $parseMode   'HTML' | 'Markdown' | ''
         * @return array              API response
         */
        public function editMessage(
            int $messageId,
            string $newText,
            string $target = 'group',
            string $parseMode = 'HTML'
        ): array {
            $chatId = $this->resolveChatId($target);

            if (empty($chatId)) {
                return ['ok' => false, 'description' => "No chat_id configured for target: {$target}"];
            }

            $payload = [
                'chat_id'    => $chatId,
                'message_id' => $messageId,
                'text'       => $newText,
            ];

            if (!empty($parseMode)) {
                $payload['parse_mode'] = $parseMode;
            }

            return $this->call('editMessageText', $payload);
        }

        /**
         * Delete a message from the chat.
         *
         * @param int    $messageId   Telegram message_id to delete
         * @param string $target      'group' | 'channel' | explicit chat_id
         * @return array              API response
         */
        public function deleteMessage(int $messageId, string $target = 'group'): array
        {
            $chatId = $this->resolveChatId($target);

            if (empty($chatId)) {
                return ['ok' => false, 'description' => "No chat_id configured for target: {$target}"];
            }

            return $this->call('deleteMessage', [
                'chat_id'    => $chatId,
                'message_id' => $messageId,
            ]);
        }

        /**
         * Send a photo to the chat (URL or local file path).
         *
         * @param string $photo      Publicly accessible URL or absolute local path
         * @param string $caption    Caption text (HTML supported)
         * @param string $target     'group' | 'channel' | explicit chat_id
         * @param string $parseMode  'HTML' | 'Markdown' | ''
         * @return array             API response with message_id on success
         */
        public function sendPhoto(
            string $photo,
            string $caption = '',
            string $target = 'group',
            string $parseMode = 'HTML'
        ): array {
            $chatId = $this->resolveChatId($target);

            if (empty($chatId)) {
                return ['ok' => false, 'description' => "No chat_id configured for target: {$target}"];
            }

            $payload = ['chat_id' => $chatId];

            // Attempt to resolve local path if it's our own image URL (Fix for localhost/private networks)
            $localPath = $photo;
            if (filter_var($photo, FILTER_VALIDATE_URL)) {
                $basename = basename(parse_url($photo, PHP_URL_PATH));
                $possibleLocal = realpath(__DIR__ . '/../../assets/postImage/' . rawurldecode($basename));
                if ($possibleLocal && file_exists($possibleLocal)) {
                    $localPath = $possibleLocal;
                }
            }

            // If local file path, attach as multipart upload
            if (file_exists($localPath)) {
                $payload['photo'] = new CURLFile(realpath($localPath));
            } else {
                $payload['photo'] = $photo; // URL string
            }

            if (!empty($caption)) {
                $payload['caption'] = $caption;
                if (!empty($parseMode)) {
                    $payload['parse_mode'] = $parseMode;
                }
            }

            return $this->call('sendPhoto', $payload);
        }

        /**
         * Pin a message in the chat.
         *
         * @param int    $messageId      Message to pin
         * @param string $target         'group' | 'channel' | explicit chat_id
         * @param bool   $silentNotify   If true, do NOT notify members
         * @return array                 API response
         */
        public function pinMessage(int $messageId, string $target = 'group', bool $silentNotify = false): array
        {
            $chatId = $this->resolveChatId($target);

            return $this->call('pinChatMessage', [
                'chat_id'              => $chatId,
                'message_id'           => $messageId,
                'disable_notification' => $silentNotify ? 'true' : 'false',
            ]);
        }

        /**
         * Unpin a specific message (or all messages if messageId = 0).
         *
         * @param int    $messageId   0 to unpin all, or specific message_id
         * @param string $target      'group' | 'channel' | explicit chat_id
         * @return array              API response
         */
        public function unpinMessage(int $messageId = 0, string $target = 'group'): array
        {
            $chatId = $this->resolveChatId($target);

            if ($messageId === 0) {
                return $this->call('unpinAllChatMessages', ['chat_id' => $chatId]);
            }

            return $this->call('unpinChatMessage', [
                'chat_id'    => $chatId,
                'message_id' => $messageId,
            ]);
        }

        // ── Webhook Management ────────────────────────────────────────────────

        /**
         * Register a Webhook URL with Telegram.
         *
         * @param string $webhookUrl   Full HTTPS URL for Telegram to POST updates to
         * @return array               API response
         */
        public function setWebhook(string $webhookUrl): array
        {
            return $this->call('setWebhook', [
                'url'             => $webhookUrl,
                'max_connections' => 40,
            ]);
        }

        /**
         * Delete the registered webhook (revert to getUpdates polling).
         *
         * @param bool $dropPendingUpdates  Discard queued updates on deletion
         * @return array                    API response
         */
        public function deleteWebhook(bool $dropPendingUpdates = false): array
        {
            return $this->call('deleteWebhook', [
                'drop_pending_updates' => $dropPendingUpdates ? 'true' : 'false',
            ]);
        }

        /**
         * Get current webhook configuration info.
         *
         * @return array  API response with url, pending_update_count, last_error_date etc.
         */
        public function getWebhookInfo(): array
        {
            return $this->call('getWebhookInfo', []);
        }

        /**
         * Get bot info (useful for verifying token is valid).
         *
         * @return array  API response with bot username, id etc.
         */
        public function getMe(): array
        {
            return $this->call('getMe', []);
        }
        /**
         * Get updates (recent messages/events) to find chat IDs.
         *
         * @return array  API response
         */
        public function getUpdates(): array
        {
            return $this->call('getUpdates', []);
        }
    }
}

/**
 * Factory helper: create TelegramBot instance from DB settings.
 *
 * @param mysqli $conn   Active database connection
 * @return TelegramBot|null
 */
if (!function_exists('getTelegramBotFromSettings')) {
    function getTelegramBotFromSettings($conn): ?TelegramBot
    {
        if (!$conn) {
            return null;
        }

        $q = mysqli_query($conn, "SELECT `telegramBotToken`, `telegramGroupId`, `telegramChannelId` FROM `settings` LIMIT 1");
        if (!$q || mysqli_num_rows($q) === 0) {
            return null;
        }

        $s = mysqli_fetch_assoc($q);
        $token   = trim($s['telegramBotToken']  ?? '');
        $groupId = trim($s['telegramGroupId']    ?? '');
        $chanId  = trim($s['telegramChannelId']  ?? '');

        if (empty($token)) {
            return null;
        }

        return new TelegramBot($token, $groupId, $chanId);
    }
}

/**
 * High-level helper: send a new post notification to Telegram group & channel.
 *
 * @param mysqli $conn      DB connection
 * @param array  $post      Associative array: title, sort_details, post_img, post_url, category_name
 * @param string $target    'group' | 'channel' | 'both'
 * @return array            Result summary
 */
if (!function_exists('sendPostToTelegram')) {
    function sendPostToTelegram($conn, array $post, string $target = 'both'): array
    {
        $bot = getTelegramBotFromSettings($conn);
        if (!$bot) {
            return ['ok' => false, 'message' => 'Telegram bot is not configured.'];
        }

        // Fetch website settings for post URL
        $sq = mysqli_query($conn, "SELECT `websiteUrl`, `websitename` FROM `settings` LIMIT 1");
        $ws = $sq ? mysqli_fetch_assoc($sq) : [];

        $siteUrl  = rtrim($ws['websiteUrl'] ?? '', '/');
        $siteName = $ws['websitename'] ?? 'News Portal';

        $title      = htmlspecialchars($post['title'] ?? 'New Post', ENT_QUOTES);
        $shortDesc  = htmlspecialchars($post['sort_details'] ?? '', ENT_QUOTES);
        $catName    = htmlspecialchars($post['category_name'] ?? '', ENT_QUOTES);
        $postUrl    = !empty($post['post_url']) ? $post['post_url'] : ($siteUrl . '/');
        $imageUrl   = !empty($post['post_img']) ? ($siteUrl . '/assets/postImage/' . rawurlencode(basename($post['post_img']))) : '';

        // Fetch Author Name if missing
        $authorName = $post['author_name'] ?? '';
        if (empty($authorName) && !empty($post['author'])) {
            $authorId = (int)$post['author'];
            $authorQ = mysqli_query($conn, "SELECT CONCAT(first_name, ' ', last_name) AS author_name FROM user WHERE user_id = {$authorId} LIMIT 1");
            if ($authorQ && $authorRow = mysqli_fetch_assoc($authorQ)) {
                $authorName = $authorRow['author_name'];
            }
        }

        // Build HTML message base
        $msgBase  = "📰 <b>" . $title . "</b>\n\n";
        if (!empty($catName)) {
            $msgBase .= "🏷️ Category: <i>" . $catName . "</i>\n";
        }
        
        $msgGroup = $msgBase;
        if (!empty($authorName)) {
            $msgGroup .= "✍️ Author: <i>" . htmlspecialchars($authorName, ENT_QUOTES) . "</i>\n";
        }
        
        $msgChannel = $msgBase; // Channel without author

        if (!empty($shortDesc)) {
            $descText = "\n" . mb_substr(strip_tags($shortDesc), 0, 200) . (mb_strlen(strip_tags($shortDesc)) > 200 ? '...' : '') . "\n";
            $msgGroup .= $descText;
            $msgChannel .= $descText;
        }

        $linkText = "\n🔗 <a href=\"" . $postUrl . "\">Read Full Article</a>";
        $linkText .= "\n\n— <i>" . $siteName . "</i>";

        $msgGroup .= $linkText;
        $msgChannel .= $linkText;

        $results = [];

        if ($target === 'group' || $target === 'both') {
            if (!empty($imageUrl)) {
                $r = $bot->sendPhoto($imageUrl, $msgGroup, 'group', 'HTML');
                // Fall back to sendMessage if photo fails
                if (empty($r['ok'])) {
                    $r = $bot->sendMessage($msgGroup, 'group');
                }
            } else {
                $r = $bot->sendMessage($msgGroup, 'group');
            }
            $results['group'] = $r;
        }

        if ($target === 'channel' || $target === 'both') {
            if (!empty($imageUrl)) {
                $r = $bot->sendPhoto($imageUrl, $msgChannel, 'channel', 'HTML');
                if (empty($r['ok'])) {
                    $r = $bot->sendMessage($msgChannel, 'channel');
                }
            } else {
                $r = $bot->sendMessage($msgChannel, 'channel');
            }
            $results['channel'] = $r;
        }

        $overallOk = !empty(array_filter($results, fn($r) => !empty($r['ok'])));

        return [
            'ok'      => $overallOk,
            'results' => $results,
            'logs'    => $bot->getLogs(),
        ];
    }
}

/**
 * ═══════════════════════════════════════════════════════════════════════════
 *  ASYNC QUEUE SYSTEM — Fast DB insert, processed by cron in background
 * ═══════════════════════════════════════════════════════════════════════════
 */

/**
 * Queue a raw Telegram action for background processing.
 * This is a lightweight DB INSERT — completes in microseconds, never blocks.
 *
 * @param mysqli $conn          Active DB connection
 * @param string $actionType    Action type: approve|reject|draft|resubmit|new_post|published
 * @param int    $postId        Post ID
 * @param string $postTitle     Post title
 * @param string $categoryName  Category name
 * @param string $postUrl       Full post URL
 * @param string $imageUrl      Post image URL (optional)
 * @param string $actorName     Name of user who triggered the action
 * @param string $target        'group' | 'channel' | 'both'
 * @param array  $extraData     Any additional data (stored as JSON)
 * @return bool                 True if queued successfully
 */
if (!function_exists('queueTelegramAction')) {
    function queueTelegramAction(
        $conn,
        string $actionType,
        int $postId = 0,
        string $postTitle = '',
        string $categoryName = '',
        string $postUrl = '',
        string $imageUrl = '',
        string $actorName = '',
        string $target = 'both',
        array $extraData = []
    ): bool {
        if (!$conn) return false;

        // Quick check: is Telegram enabled?
        $sQ = mysqli_query($conn, "SELECT `telegramEnabled` FROM `settings` LIMIT 1");
        $sR = $sQ ? mysqli_fetch_assoc($sQ) : [];
        if (empty($sR['telegramEnabled'])) {
            return false; // Telegram disabled — skip silently
        }

        // Ensure queue table exists
        if (function_exists('ensureTelegramQueueSchema')) {
            ensureTelegramQueueSchema($conn);
        }

        $stmt = mysqli_prepare($conn,
            "INSERT INTO `telegram_queue` (`action_type`, `post_id`, `post_title`, `category_name`, `post_url`, `image_url`, `actor_name`, `extra_data`, `target`, `status`)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')"
        );

        if (!$stmt) return false;

        $extraJson = !empty($extraData) ? json_encode($extraData, JSON_UNESCAPED_UNICODE) : null;

        mysqli_stmt_bind_param($stmt, "sissssss" . "s",
            $actionType, $postId, $postTitle, $categoryName,
            $postUrl, $imageUrl, $actorName, $extraJson, $target
        );

        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $ok;
    }
}

/**
 * Queue a structured action log notification for a post status change.
 * Builds the structured log and inserts into queue — no API calls, instant return.
 *
 * @param mysqli $conn        Active DB connection
 * @param string $action      'approve' | 'reject' | 'draft' | 'resubmit' | 'new_post' | 'published'
 * @param int    $postId      Post ID
 * @param string $target      'group' | 'channel' | 'both'
 * @return bool               True if queued
 */
if (!function_exists('queueTelegramActionLog')) {
    function queueTelegramActionLog($conn, string $action, int $postId, string $target = 'both'): bool
    {
        if (!$conn || $postId <= 0) return false;

        // Fetch post details
        $pQ = mysqli_query($conn, "SELECT p.post_id, p.title, p.post_img, p.sort_details, p.post_slug, p.author,
                                          c.category_name, c.category_slug,
                                          CONCAT(u.first_name, ' ', u.last_name) AS author_name
                                   FROM `post` p
                                   LEFT JOIN `category` c ON p.category = c.category_id
                                   LEFT JOIN `user` u ON p.author = u.user_id
                                   WHERE p.post_id = {$postId} LIMIT 1");
        $post = $pQ ? mysqli_fetch_assoc($pQ) : null;
        if (!$post) return false;

        // Fetch site settings
        $sQ = mysqli_query($conn, "SELECT `websiteUrl`, `websitename` FROM `settings` LIMIT 1");
        $ws = $sQ ? mysqli_fetch_assoc($sQ) : [];
        $siteUrl = rtrim($ws['websiteUrl'] ?? '', '/');

        // Build URLs
        $postUrl  = getPostUrl($post, $siteUrl);
        $imageUrl = !empty($post['post_img'])
            ? ($siteUrl . '/assets/postImage/' . rawurlencode(basename($post['post_img'])))
            : '';

        // Actor = currently logged-in admin/editor
        $actorName = $_SESSION['name'] ?? ($_SESSION['username'] ?? 'System');

        return queueTelegramAction(
            $conn,
            $action,
            $postId,
            $post['title'] ?? '',
            $post['category_name'] ?? '',
            $postUrl,
            $imageUrl,
            $actorName,
            $target,
            [
                'author_name'  => $post['author_name'] ?? '',
                'author_id'    => $post['author'] ?? '',
                'sort_details' => mb_substr(strip_tags($post['sort_details'] ?? ''), 0, 200),
                'site_name'    => $ws['websitename'] ?? 'News Portal',
            ]
        );
    }
}

/**
 * ═══════════════════════════════════════════════════════════════════════════
 *  EMAIL NOTIFICATION SYSTEM — Author email on post status changes
 * ═══════════════════════════════════════════════════════════════════════════
 */

/**
 * Generate a beautiful HTML email template for post status changes.
 *
 * @param string $action      'approve' | 'reject' | 'draft' | 'new_post' | 'resubmit'
 * @param string $postTitle   Post title
 * @param string $authorName  Author's full name
 * @param string $postUrl     Full URL to the post
 * @param string $siteName    Website name
 * @param string $categoryName Category name (optional)
 * @return array              ['subject' => ..., 'body' => ...]
 */
if (!function_exists('getPostStatusEmailTemplate')) {
    function getPostStatusEmailTemplate(
        string $action,
        string $postTitle,
        string $authorName,
        string $postUrl = '',
        string $siteName = 'News Portal',
        string $categoryName = ''
    ): array {
        $actionConfig = [
            'approve' => [
                'subject'  => '✅ Your Post Has Been Approved!',
                'heading'  => 'Post Approved',
                'color'    => '#22c55e',
                'icon'     => '✅',
                'message'  => 'Great news! Your post has been reviewed and <strong>approved</strong>. It is now live on the website and visible to all readers.',
                'showLink' => true,
            ],
            'reject' => [
                'subject'  => '❌ Your Post Has Been Rejected',
                'heading'  => 'Post Rejected',
                'color'    => '#ef4444',
                'icon'     => '❌',
                'message'  => 'Unfortunately, your post has been <strong>rejected</strong> after review. Please check the post content, make necessary improvements, and resubmit for approval.',
                'showLink' => false,
            ],
            'draft' => [
                'subject'  => '📝 Post Saved as Draft',
                'heading'  => 'Draft Saved',
                'color'    => '#eab308',
                'icon'     => '📝',
                'message'  => 'Your post has been saved as a <strong>draft</strong>. You can continue editing and submit it for review whenever you\'re ready.',
                'showLink' => false,
            ],
            'new_post' => [
                'subject'  => '🆕 Post Submitted for Approval',
                'heading'  => 'Submission Received',
                'color'    => '#f97316',
                'icon'     => '🆕',
                'message'  => 'Your post has been successfully <strong>submitted for approval</strong>. Our editorial team will review it shortly. You will receive an email once a decision is made.',
                'showLink' => false,
            ],
            'resubmit' => [
                'subject'  => '🔄 Post Resubmitted for Approval',
                'heading'  => 'Resubmission Received',
                'color'    => '#a855f7',
                'icon'     => '🔄',
                'message'  => 'Your updated post has been <strong>resubmitted for review</strong>. Our team will review the changes and get back to you soon.',
                'showLink' => false,
            ],
        ];

        $config = $actionConfig[$action] ?? [
            'subject'  => 'Post Status Update',
            'heading'  => 'Status Update',
            'color'    => '#6b7280',
            'icon'     => '📋',
            'message'  => 'Your post status has been updated.',
            'showLink' => false,
        ];

        $subject = $config['subject'] . ' — ' . $siteName;
        $safeTitle   = htmlspecialchars($postTitle, ENT_QUOTES);
        $safeName    = htmlspecialchars($authorName, ENT_QUOTES);
        $safeSite    = htmlspecialchars($siteName, ENT_QUOTES);
        $safeCat     = htmlspecialchars($categoryName, ENT_QUOTES);

        $linkBlock = '';
        if ($config['showLink'] && !empty($postUrl)) {
            $linkBlock = '
                <div style="text-align:center; margin:25px 0;">
                    <a href="' . $postUrl . '" style="display:inline-block; background-color:' . $config['color'] . '; color:#ffffff; text-decoration:none; padding:12px 30px; border-radius:6px; font-size:14px; font-weight:600;">
                        View Published Post →
                    </a>
                </div>';
        }

        $categoryBlock = '';
        if (!empty($safeCat)) {
            $categoryBlock = '
                <tr>
                    <td style="padding:6px 12px; color:#6b7280; font-size:13px; border-bottom:1px solid #f3f4f6;">Category</td>
                    <td style="padding:6px 12px; font-size:13px; border-bottom:1px solid #f3f4f6;">' . $safeCat . '</td>
                </tr>';
        }

        $body = '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>' . $config['heading'] . '</title>
        </head>
        <body style="font-family:\'Segoe UI\',Arial,sans-serif; background-color:#f8fafc; margin:0; padding:0; -webkit-text-size-adjust:100%;">
            <div style="max-width:600px; margin:30px auto; background-color:#ffffff; border-radius:12px; box-shadow:0 4px 20px rgba(0,0,0,0.08); overflow:hidden;">

                <!-- Header -->
                <div style="background:linear-gradient(135deg, ' . $config['color'] . ', ' . $config['color'] . 'cc); padding:30px 20px; text-align:center;">
                    <div style="font-size:40px; margin-bottom:8px;">' . $config['icon'] . '</div>
                    <h1 style="margin:0; color:#ffffff; font-size:22px; font-weight:700; letter-spacing:0.5px;">
                        ' . $config['heading'] . '
                    </h1>
                </div>

                <!-- Body -->
                <div style="padding:30px 25px; color:#1f2937; line-height:1.7;">
                    <p style="margin:0 0 15px;">Hello <strong>' . $safeName . '</strong>,</p>
                    <p style="margin:0 0 20px;">' . $config['message'] . '</p>

                    <!-- Post Details Card -->
                    <div style="background-color:#f9fafb; border:1px solid #e5e7eb; border-radius:8px; padding:0; margin:20px 0; overflow:hidden;">
                        <div style="background-color:#f3f4f6; padding:10px 15px; border-bottom:1px solid #e5e7eb;">
                            <strong style="color:#374151; font-size:13px; text-transform:uppercase; letter-spacing:0.5px;">Post Details</strong>
                        </div>
                        <table style="width:100%; border-collapse:collapse;">
                            <tr>
                                <td style="padding:8px 12px; color:#6b7280; font-size:13px; width:100px; border-bottom:1px solid #f3f4f6;">Title</td>
                                <td style="padding:8px 12px; font-size:13px; font-weight:600; color:#111827; border-bottom:1px solid #f3f4f6;">' . $safeTitle . '</td>
                            </tr>
                            ' . $categoryBlock . '
                            <tr>
                                <td style="padding:6px 12px; color:#6b7280; font-size:13px; border-bottom:1px solid #f3f4f6;">Status</td>
                                <td style="padding:6px 12px; font-size:13px; border-bottom:1px solid #f3f4f6;">
                                    <span style="display:inline-block; background-color:' . $config['color'] . '20; color:' . $config['color'] . '; padding:2px 10px; border-radius:20px; font-size:12px; font-weight:600;">
                                        ' . $config['heading'] . '
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding:6px 12px; color:#6b7280; font-size:13px;">Date</td>
                                <td style="padding:6px 12px; font-size:13px;">' . date('d M Y, h:i A') . '</td>
                            </tr>
                        </table>
                    </div>

                    ' . $linkBlock . '

                    <p style="margin:20px 0 0; font-size:13px; color:#6b7280;">
                        If you have any questions, please contact the editorial team.
                    </p>
                </div>

                <!-- Footer -->
                <div style="background-color:#f9fafb; padding:18px 25px; text-align:center; border-top:1px solid #e5e7eb;">
                    <p style="margin:0; font-size:12px; color:#9ca3af;">
                        &copy; ' . date('Y') . ' ' . $safeSite . '. All rights reserved.
                    </p>
                </div>
            </div>
        </body>
        </html>';

        return ['subject' => $subject, 'body' => $body];
    }
}

/**
 * Queue an email notification to the post author for a status change.
 * Uses the same telegram_queue table with action_type prefixed 'email_'.
 * Processed by the cron queue processor — instant return, no blocking.
 *
 * @param mysqli $conn      Active DB connection
 * @param string $action    'approve' | 'reject' | 'draft' | 'new_post' | 'resubmit'
 * @param int    $postId    Post ID
 * @return bool             True if queued
 */
if (!function_exists('queueAuthorEmailNotification')) {
    function queueAuthorEmailNotification($conn, string $action, int $postId): bool
    {
        if (!$conn || $postId <= 0) return false;

        // Fetch post + author details
        $pQ = mysqli_query($conn, "SELECT p.post_id, p.title, p.post_img, p.sort_details, p.post_slug, p.author,
                                          c.category_name, c.category_slug,
                                          u.email AS author_email,
                                          CONCAT(u.first_name, ' ', u.last_name) AS author_name
                                   FROM `post` p
                                   LEFT JOIN `category` c ON p.category = c.category_id
                                   LEFT JOIN `user` u ON p.author = u.user_id
                                   WHERE p.post_id = {$postId} LIMIT 1");
        $post = $pQ ? mysqli_fetch_assoc($pQ) : null;
        if (!$post || empty($post['author_email'])) return false;

        // Fetch site settings
        $sQ = mysqli_query($conn, "SELECT `websiteUrl`, `websitename` FROM `settings` LIMIT 1");
        $ws = $sQ ? mysqli_fetch_assoc($sQ) : [];
        $siteUrl  = rtrim($ws['websiteUrl'] ?? '', '/');
        $siteName = $ws['websitename'] ?? 'News Portal';

        // Build post URL
        $postUrl = getPostUrl($post, $siteUrl);

        // Generate email template
        $template = getPostStatusEmailTemplate(
            $action,
            $post['title'] ?? 'Untitled',
            $post['author_name'] ?? 'Author',
            $postUrl,
            $siteName,
            $post['category_name'] ?? ''
        );

        // Ensure queue table exists
        if (function_exists('ensureTelegramQueueSchema')) {
            ensureTelegramQueueSchema($conn);
        }

        // Queue into telegram_queue with email_ prefix
        $emailAction = 'email_' . $action;
        $stmt = mysqli_prepare($conn,
            "INSERT INTO `telegram_queue` (`action_type`, `post_id`, `post_title`, `category_name`, `post_url`, `image_url`, `actor_name`, `extra_data`, `target`, `status`)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'email', 'pending')"
        );

        if (!$stmt) return false;

        $extraJson = json_encode([
            'to_email'      => $post['author_email'],
            'author_name'   => $post['author_name'] ?? '',
            'email_subject' => $template['subject'],
            'email_body'    => $template['body'],
            'site_name'     => $siteName,
        ], JSON_UNESCAPED_UNICODE);

        $actorName = $_SESSION['name'] ?? ($_SESSION['username'] ?? 'System');
        $imageUrl  = '';

        mysqli_stmt_bind_param($stmt, "sisssss" . "s",
            $emailAction, $postId, $post['title'] ?? '',
            $post['category_name'] ?? '', $postUrl, $imageUrl,
            $actorName, $extraJson
        );

        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        return $ok;
    }
}