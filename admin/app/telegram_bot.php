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

            // If local file path, attach as multipart upload
            if (file_exists($photo)) {
                $payload['photo'] = new CURLFile(realpath($photo));
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

        // Build HTML message
        $msg  = "📰 <b>" . $title . "</b>\n\n";
        if (!empty($catName)) {
            $msg .= "🏷️ Category: <i>" . $catName . "</i>\n";
        }
        if (!empty($shortDesc)) {
            $msg .= "\n" . mb_substr(strip_tags($shortDesc), 0, 200) . (mb_strlen(strip_tags($shortDesc)) > 200 ? '...' : '') . "\n";
        }
        $msg .= "\n🔗 <a href=\"" . $postUrl . "\">Read Full Article</a>";
        $msg .= "\n\n— <i>" . $siteName . "</i>";

        $results = [];

        if ($target === 'group' || $target === 'both') {
            if (!empty($imageUrl)) {
                $r = $bot->sendPhoto($imageUrl, $msg, 'group', 'HTML');
                // Fall back to sendMessage if photo fails
                if (empty($r['ok'])) {
                    $r = $bot->sendMessage($msg, 'group');
                }
            } else {
                $r = $bot->sendMessage($msg, 'group');
            }
            $results['group'] = $r;
        }

        if ($target === 'channel' || $target === 'both') {
            if (!empty($imageUrl)) {
                $r = $bot->sendPhoto($imageUrl, $msg, 'channel', 'HTML');
                if (empty($r['ok'])) {
                    $r = $bot->sendMessage($msg, 'channel');
                }
            } else {
                $r = $bot->sendMessage($msg, 'channel');
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