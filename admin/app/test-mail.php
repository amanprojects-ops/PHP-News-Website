<?php
session_start();
include_once 'config.php';
include_once __DIR__ . '/../../database/Mailer.php';

header('Content-Type: application/json');

// Check Super Admin Authentication
if (!isset($_SESSION['username']) || !isset($_SESSION['role']) || (int) $_SESSION['role'] !== 1) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access. Only Super Administrators can test email settings.',
        'logs' => ['Access denied: Insufficient privileges.']
    ]);
    exit;
}

// Check Request Method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.',
        'logs' => ['Error: Expected POST request.']
    ]);
    exit;
}

// CSRF Protection
$token = $_POST['csrf_token'] ?? '';
if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
    echo json_encode([
        'success' => false,
        'message' => 'CSRF verification failed. Please refresh the page and try again.',
        'logs' => ['Error: Invalid or expired security token.']
    ]);
    exit;
}

$testRecipient = filter_var(trim($_POST['test_email'] ?? ''), FILTER_VALIDATE_EMAIL);
if (!$testRecipient) {
    echo json_encode([
        'success' => false,
        'message' => 'Please provide a valid recipient email address for testing.',
        'logs' => ['Error: Recipient email is missing or improperly formatted.']
    ]);
    exit;
}

// Fetch active settings
$settingsQuery = mysqli_query($conn, "SELECT * FROM `settings` LIMIT 1");
$settings = $settingsQuery ? mysqli_fetch_assoc($settingsQuery) : [];

// Check if test parameters were provided in POST or if we should use DB settings
$driver = trim($_POST['mailDriver'] ?? ($settings['mailDriver'] ?? 'mail'));
$host = trim($_POST['smtpHost'] ?? ($settings['smtpHost'] ?? ''));
$port = (int) ($_POST['smtpPort'] ?? ($settings['smtpPort'] ?? 587));
$user = trim($_POST['smtpUser'] ?? ($settings['smtpUser'] ?? ''));
$pass = isset($_POST['smtpPass']) && $_POST['smtpPass'] !== '' ? $_POST['smtpPass'] : ($settings['smtpPass'] ?? '');
$encryption = trim($_POST['smtpEncryption'] ?? ($settings['smtpEncryption'] ?? 'tls'));
$fromEmail = trim($_POST['smtpFromEmail'] ?? ($settings['smtpFromEmail'] ?? ($settings['workEmail'] ?? 'admin@newsportal.com')));
$fromName = trim($_POST['smtpFromName'] ?? ($settings['smtpFromName'] ?? ($settings['websitename'] ?? 'News Portal')));

// Compose Test Email HTML
$siteName = htmlspecialchars($settings['websitename'] ?? 'News Portal');
$timeStr = date('Y-m-d H:i:s T');
$bodyHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background: #f4f6f8; margin: 0; padding: 20px; }
        .card { max-width: 580px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #1e90ff, #0056b3); padding: 25px 30px; text-align: center; color: #ffffff; }
        .content { padding: 30px; color: #334155; line-height: 1.6; }
        .badge { display: inline-block; background: #e0f2fe; color: #0369a1; padding: 4px 12px; border-radius: 20px; font-weight: bold; font-size: 13px; }
        .details-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; margin: 20px 0; font-size: 13px; }
        .footer { padding: 20px; text-align: center; font-size: 12px; color: #94a3b8; border-top: 1px solid #f1f5f9; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h2 style="margin:0; font-size: 24px;">📧 SMTP Test Succeeded!</h2>
            <p style="margin: 6px 0 0 0; opacity: 0.9; font-size: 14px;">{$siteName} - System Email Manager</p>
        </div>
        <div class="content">
            <p>Congratulations! Your email server settings are properly configured and operating smoothly.</p>
            <div class="details-box">
                <div><strong>Mail Driver:</strong> {$driver}</div>
                <div><strong>SMTP Host:</strong> {$host}:{$port}</div>
                <div><strong>Encryption:</strong> {$encryption}</div>
                <div><strong>Sender:</strong> {$fromName} &lt;{$fromEmail}&gt;</div>
                <div><strong>Dispatched At:</strong> {$timeStr}</div>
            </div>
            <p style="font-size: 13px; color: #64748b;">This message was triggered from your Admin System Settings test panel to verify SMTP delivery.</p>
        </div>
        <div class="footer">
            &copy; {$siteName}. All rights reserved.
        </div>
    </div>
</body>
</html>
HTML;

$subject = "SMTP Test Message - " . ($settings['websitename'] ?? 'News Portal');

if (strtolower($driver) === 'smtp') {
    if (empty($host)) {
        echo json_encode([
            'success' => false,
            'message' => 'SMTP Host is missing. Please enter your SMTP server address.',
            'logs' => ['Error: SMTP Host is not configured.']
        ]);
        exit;
    }

    $mailer = new SystemMailer();
    try {
        $mailer->connect($host, $port, $encryption, 15);
        if (!empty($user)) {
            $mailer->authenticate($user, $pass);
        }
        $mailer->send($testRecipient, $subject, $bodyHtml, $fromEmail, $fromName, true);

        echo json_encode([
            'success' => true,
            'message' => "Test email successfully delivered to {$testRecipient} via SMTP ({$host}).",
            'logs' => $mailer->getLogs()
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage(),
            'logs' => $mailer->getLogs()
        ]);
    }
} else {
    // Native PHP Mail
    $subjectHeader = "=?UTF-8?B?" . base64_encode($subject) . "?=";
    $headers = [];
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-type: text/html; charset=UTF-8";
    $headers[] = "From: " . (!empty($fromName) ? "=?UTF-8?B?" . base64_encode($fromName) . "?= <{$fromEmail}>" : "<{$fromEmail}>");
    $headers[] = "Reply-To: <{$fromEmail}>";
    $headers[] = "X-Mailer: PHP/" . phpversion();

    $sent = @mail($testRecipient, $subjectHeader, $bodyHtml, implode("\r\n", $headers));
    if ($sent) {
        echo json_encode([
            'success' => true,
            'message' => "Test email dispatched via PHP native mail() to {$testRecipient}.",
            'logs' => ["Sent via mail() to {$testRecipient}"]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'PHP native mail() returned false. Please verify your server mail configuration or switch to SMTP.',
            'logs' => ['PHP mail() failed to dispatch message.']
        ]);
    }
}
