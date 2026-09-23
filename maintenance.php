<?php
// Send 503 Service Unavailable header with Retry-After for proper SEO handling
http_response_code(503);
header('Retry-After: 3600');

if (!isset($settings)) {
    @include_once 'config.php';
}

$siteName = htmlspecialchars($settings['websitename'] ?? 'News Portal');
$siteLogo = !empty($settings['logo']) ? 'assets/images/' . htmlspecialchars($settings['logo']) : '';
$siteFavicon = !empty($settings['favicon']) ? 'assets/images/' . htmlspecialchars($settings['favicon']) : '';
$contactEmail = htmlspecialchars($settings['workEmail'] ?? 'contact@newsportal.com');
$maintenanceMsg = !empty($settings['maintenanceMsg']) ? htmlspecialchars($settings['maintenanceMsg']) : 'Our portal is currently undergoing scheduled maintenance and system upgrades. We will be back online shortly.';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance | <?php echo $siteName; ?></title>
    <?php if (!empty($siteFavicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo $siteFavicon; ?>">
    <?php endif; ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e2e8f0;
            padding: 20px;
            margin: 0;
        }
        .maintenance-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 48px 36px;
            max-width: 640px;
            width: 100%;
            text-align: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.8s ease-out;
        }
        .brand-logo {
            max-height: 55px;
            max-width: 220px;
            object-fit: contain;
            margin-bottom: 24px;
            filter: drop-shadow(0 2px 8px rgba(0,0,0,0.3));
        }
        .maintenance-img {
            max-height: 190px;
            width: auto;
            margin: 15px auto 25px auto;
            animation: float 3s ease-in-out infinite;
        }
        .badge-status {
            display: inline-block;
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.3);
            padding: 6px 16px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }
        .title {
            font-size: 28px;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 12px;
            letter-spacing: -0.5px;
        }
        .description {
            color: #94a3b8;
            font-size: 15px;
            line-height: 1.7;
            margin-bottom: 28px;
        }
        .support-box {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 14px 20px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
            color: #cbd5e1;
        }
        .support-box a {
            color: #38bdf8;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        .support-box a:hover {
            color: #7dd3fc;
            text-decoration: underline;
        }
        .admin-login {
            margin-top: 30px;
            font-size: 12px;
            color: #64748b;
        }
        .admin-login a {
            color: #94a3b8;
            text-decoration: none;
        }
        .admin-login a:hover {
            color: #e2e8f0;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
    </style>
</head>
<body>
    <div class="maintenance-card">
        <?php if (!empty($siteLogo) && file_exists(__DIR__ . '/' . $siteLogo)): ?>
            <img src="<?php echo $siteLogo; ?>" alt="<?php echo $siteName; ?>" class="brand-logo">
        <?php endif; ?>

        <div>
            <span class="badge-status"><i class="fa fa-wrench me-1"></i> Under Scheduled Maintenance</span>
        </div>

        <?php if (file_exists(__DIR__ . '/underMentenance.png')): ?>
            <div>
                <img src="underMentenance.png" alt="Maintenance in progress" class="maintenance-img">
            </div>
        <?php endif; ?>

        <h1 class="title">We'll Be Right Back!</h1>
        <p class="description">
            <?php echo nl2br($maintenanceMsg); ?>
        </p>

        <?php if (!empty($contactEmail)): ?>
            <div>
                <div class="support-box">
                    <i class="fa fa-envelope text-info"></i>
                    <span>Urgent query? Contact our desk:</span>
                    <a href="mailto:<?php echo $contactEmail; ?>"><?php echo $contactEmail; ?></a>
                </div>
            </div>
        <?php endif; ?>

        <div class="admin-login">
            Are you a site administrator? <a href="admin/index.php">Admin Login &rarr;</a>
        </div>
    </div>
</body>
</html>
