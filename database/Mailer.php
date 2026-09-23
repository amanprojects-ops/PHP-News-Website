<?php
/**
 * Lightweight, Zero-Dependency SMTP Mailer Engine
 * Supports standard SMTP, STARTTLS (port 587), and SSL (port 465) via native PHP stream sockets.
 */

if (!class_exists('SystemMailer')) {
    class SystemMailer
    {
        private $socket = null;
        private $logs = [];
        private $timeout = 15;

        private function log($message)
        {
            $this->logs[] = '[' . date('H:i:s') . '] ' . $message;
        }

        public function getLogs()
        {
            return $this->logs;
        }

        private function getResponse()
        {
            $response = '';
            while ($line = fgets($this->socket, 515)) {
                $response .= $line;
                // If 4th character is space or line length is short, response is complete
                if (isset($line[3]) && $line[3] === ' ') {
                    break;
                }
            }
            $this->log('SERVER: ' . trim($response));
            return $response;
        }

        private function sendCommand($command, $expectedCodes = [])
        {
            if (!is_array($expectedCodes)) {
                $expectedCodes = [$expectedCodes];
            }

            // Mask password if sending auth credentials
            $logCmd = $command;
            $this->log('CLIENT: ' . $logCmd);

            fwrite($this->socket, $command . "\r\n");
            $response = $this->getResponse();
            $code = (int) substr($response, 0, 3);

            if (!empty($expectedCodes) && !in_array($code, $expectedCodes, true)) {
                throw new Exception("SMTP command failed ({$code}): " . trim($response));
            }

            return ['code' => $code, 'response' => $response];
        }

        public function connect($host, $port = 587, $encryption = 'tls', $timeout = 15)
        {
            $this->timeout = $timeout;
            $encryption = strtolower(trim($encryption));
            $protocol = ($encryption === 'ssl') ? 'ssl://' : 'tcp://';
            $remoteSocket = $protocol . $host . ':' . $port;

            $this->log("Connecting to {$remoteSocket} (timeout: {$timeout}s)...");

            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]);

            $errno = 0;
            $errstr = '';
            $this->socket = @stream_socket_client($remoteSocket, $errno, $errstr, $timeout, STREAM_CLIENT_CONNECT, $context);

            if (!$this->socket) {
                throw new Exception("Could not connect to SMTP host {$host}:{$port} ({$errno}: {$errstr})");
            }

            stream_set_timeout($this->socket, $timeout);

            // Read initial 220 banner
            $banner = $this->getResponse();
            $code = (int) substr($banner, 0, 3);
            if ($code !== 220) {
                throw new Exception("Unexpected greeting banner: " . trim($banner));
            }

            // Send EHLO
            $clientDomain = !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost';
            $this->sendCommand("EHLO " . $clientDomain, 250);

            // Upgrade to TLS if requested
            if ($encryption === 'tls') {
                $this->log("Initiating STARTTLS negotiation...");
                $this->sendCommand("STARTTLS", 220);

                $cryptoMethod = STREAM_CRYPTO_METHOD_TLS_CLIENT;
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;
                }
                if (defined('STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT')) {
                    $cryptoMethod |= STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT;
                }

                $cryptoSuccess = @stream_socket_enable_crypto($this->socket, true, $cryptoMethod);
                if (!$cryptoSuccess) {
                    throw new Exception("Failed to establish secure TLS encryption with {$host}");
                }

                $this->log("TLS handshake completed successfully.");
                // Re-send EHLO after TLS handshake as per RFC 3207
                $this->sendCommand("EHLO " . $clientDomain, 250);
            }

            return true;
        }

        public function authenticate($username, $password)
        {
            if (empty($username)) {
                return true; // No auth required
            }

            $this->log("Authenticating as " . $username);
            $this->sendCommand("AUTH LOGIN", 334);

            // Send Base64 Username
            $this->log("CLIENT: [Base64 Username sent]");
            fwrite($this->socket, base64_encode($username) . "\r\n");
            $res = $this->getResponse();
            if ((int) substr($res, 0, 3) !== 334) {
                throw new Exception("Username rejected by SMTP server: " . trim($res));
            }

            // Send Base64 Password
            $this->log("CLIENT: [Base64 Password sent]");
            fwrite($this->socket, base64_encode($password) . "\r\n");
            $res = $this->getResponse();
            if ((int) substr($res, 0, 3) !== 235) {
                throw new Exception("Authentication credentials rejected (code " . substr($res, 0, 3) . "): " . trim($res));
            }

            $this->log("Authentication successful.");
            return true;
        }

        public function send($to, $subject, $body, $fromEmail, $fromName = '', $isHtml = true)
        {
            if (!$this->socket) {
                throw new Exception("Cannot send mail without an active SMTP connection.");
            }

            $this->sendCommand("MAIL FROM:<" . $fromEmail . ">", 250);
            $this->sendCommand("RCPT TO:<" . $to . ">", [250, 251]);
            $this->sendCommand("DATA", 354);

            // Construct MIME message headers
            $fromHeader = !empty($fromName) ? "=?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>" : "<{$fromEmail}>";
            $subjectHeader = "=?UTF-8?B?" . base64_encode($subject) . "?=";

            $headers = [];
            $headers[] = "Date: " . date('r');
            $headers[] = "From: " . $fromHeader;
            $headers[] = "To: <" . $to . ">";
            $headers[] = "Subject: " . $subjectHeader;
            $headers[] = "MIME-Version: 1.0";
            if ($isHtml) {
                $headers[] = "Content-Type: text/html; charset=UTF-8";
            } else {
                $headers[] = "Content-Type: text/plain; charset=UTF-8";
            }
            $headers[] = "Content-Transfer-Encoding: 8bit";
            $headers[] = "X-Mailer: SystemMailer PHP News Portal";

            $message = implode("\r\n", $headers) . "\r\n\r\n";

            // Escape single leading period in message lines (dot-stuffing)
            $cleanBody = str_replace("\r\n.", "\r\n..", $body);
            $message .= $cleanBody . "\r\n.";

            $this->log("CLIENT: [Sending message body (" . strlen($cleanBody) . " bytes)]");
            fwrite($this->socket, $message . "\r\n");

            $res = $this->getResponse();
            if ((int) substr($res, 0, 3) !== 250) {
                throw new Exception("SMTP server refused message submission: " . trim($res));
            }

            $this->log("Message sent successfully!");

            // Polite QUIT
            try {
                $this->sendCommand("QUIT", 221);
            } catch (Exception $e) {
                // Ignore disconnect issues
            }

            @fclose($this->socket);
            $this->socket = null;

            return true;
        }
    }
}

/**
 * Universal Mail Sending Helper
 * Automatically uses system SMTP configuration or PHP native mail() fallback.
 */
if (!function_exists('sendSystemMail')) {
    function sendSystemMail($conn, $to, $subject, $bodyHtml, $customFromName = null, $customFromEmail = null)
    {
        $logs = [];
        $settings = [];

        if ($conn) {
            $q = mysqli_query($conn, "SELECT * FROM `settings` LIMIT 1");
            if ($q && mysqli_num_rows($q) > 0) {
                $settings = mysqli_fetch_assoc($q);
            }
        }

        $driver = strtolower($settings['mailDriver'] ?? 'mail');
        $fromEmail = !empty($customFromEmail) ? $customFromEmail : (!empty($settings['smtpFromEmail']) ? $settings['smtpFromEmail'] : ($settings['workEmail'] ?? 'noreply@newsportal.com'));
        $fromName = !empty($customFromName) ? $customFromName : (!empty($settings['smtpFromName']) ? $settings['smtpFromName'] : ($settings['websitename'] ?? 'News Portal'));

        // If driver is SMTP, attempt SMTP connection
        if ($driver === 'smtp') {
            $host = $settings['smtpHost'] ?? '';
            $port = (int) ($settings['smtpPort'] ?? 587);
            $user = $settings['smtpUser'] ?? '';
            $pass = $settings['smtpPass'] ?? '';
            $encryption = $settings['smtpEncryption'] ?? 'tls';

            if (empty($host)) {
                return [
                    'success' => false,
                    'message' => 'SMTP Host is not configured in System Settings.',
                    'logs' => ['Error: SMTP Host is missing.']
                ];
            }

            $mailer = new SystemMailer();
            try {
                $mailer->connect($host, $port, $encryption, 15);
                if (!empty($user)) {
                    $mailer->authenticate($user, $pass);
                }
                $mailer->send($to, $subject, $bodyHtml, $fromEmail, $fromName, true);

                return [
                    'success' => true,
                    'message' => 'Email sent successfully via SMTP (' . htmlspecialchars($host) . ').',
                    'logs' => $mailer->getLogs()
                ];
            } catch (Exception $e) {
                return [
                    'success' => false,
                    'message' => $e->getMessage(),
                    'logs' => $mailer->getLogs()
                ];
            }
        }

        // Native PHP mail() fallback
        $subjectHeader = "=?UTF-8?B?" . base64_encode($subject) . "?=";
        $headers = [];
        $headers[] = "MIME-Version: 1.0";
        $headers[] = "Content-type: text/html; charset=UTF-8";
        $headers[] = "From: " . (!empty($fromName) ? "=?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>" : "<{$fromEmail}>");
        $headers[] = "Reply-To: <{$fromEmail}>";
        $headers[] = "X-Mailer: PHP/" . phpversion();

        $sent = @mail($to, $subjectHeader, $bodyHtml, implode("\r\n", $headers));
        if ($sent) {
            return [
                'success' => true,
                'message' => 'Email dispatched successfully via PHP native mail().',
                'logs' => ['Sent via PHP mail() to: ' . $to]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'PHP mail() function returned false. Please configure SMTP credentials.',
                'logs' => ['PHP native mail() failed. Check sendmail_path or switch to SMTP driver.']
            ];
        }
    }
}
