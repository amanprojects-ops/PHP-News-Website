<?php

/**
 * PHP News Website — Installation Wizard
 * Step-based web installer for first-time setup
 */

// ─── Lock after install ───────────────────────────────────────────────────────
if (file_exists(__DIR__ . '/install.lock')) {
    die('
    <!DOCTYPE html><html><head>
    <meta charset="UTF-8"><title>Already Installed</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>*{box-sizing:border-box}body{font-family:Inter,sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#0f172a,#1e1b4b);}
    .box{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.1);border-radius:20px;padding:48px 40px;text-align:center;max-width:440px;width:90%;}
    .icon{font-size:48px;margin-bottom:16px;}h1{color:#fff;font-size:22px;margin:0 0 10px;}p{color:#94a3b8;font-size:14px;}
    a{display:inline-block;margin-top:20px;padding:10px 24px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border-radius:8px;font-weight:600;text-decoration:none;}
    </style></head><body>
    <div class="box"><div class="icon">✅</div>
    <h1>Already Installed</h1>
    <p>This project is already installed. Delete <code>install/install.lock</code> to re-run the wizard.</p>
    <a href="../">Go to Website</a>&nbsp;<a href="../admin">Go to Admin</a>
    </div></body></html>
    ');
}

session_start();

// ─── Helper to update configuration files ─────────────────────────────────────
function update_config_file($file_path, $host, $user, $pass, $name)
{
    if (!file_exists($file_path)) return false;
    $content = file_get_contents($file_path);
    $content = preg_replace('/\$hostname\s*=\s*\'[^\']*\'|\$hostname\s*=\s*"[^"]*"/', '$hostname = \'' . addslashes($host) . '\'', $content);
    $content = preg_replace('/\$username\s*=\s*\'[^\']*\'|\$username\s*=\s*"[^"]*"/', '$username = \'' . addslashes($user) . '\'', $content);
    $content = preg_replace('/\$password\s*=\s*\'[^\']*\'|\$password\s*=\s*"[^"]*"/', '$password = \'' . addslashes($pass) . '\'', $content);
    $content = preg_replace('/\$dbname\s*=\s*\'[^\']*\'|\$dbname\s*=\s*"[^"]*"/', '$dbname = \'' . addslashes($name) . '\'', $content);
    return file_put_contents($file_path, $content) !== false;
}

// ─── Step definitions ─────────────────────────────────────────────────────────
$steps = [
    1 => ['title' => 'Welcome', 'icon' => 'fa-hand-wave'],
    2 => ['title' => 'Requirements', 'icon' => 'fa-list-check'],
    3 => ['title' => 'Database', 'icon' => 'fa-database'],
    4 => ['title' => 'Site Config', 'icon' => 'fa-gear'],
    5 => ['title' => 'Admin Account', 'icon' => 'fa-user-shield'],
    6 => ['title' => 'Finish', 'icon' => 'fa-party-horn'],
];

$current_step = isset($_GET['step']) ? (int) $_GET['step'] : 1;
$current_step = max(1, min(6, $current_step));

$error = '';
$success = '';

// ─── Process POST ─────────────────────────────────────────────────────────────

/* Step 3: Test & save DB config */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $current_step === 3) {
    $db_host = trim($_POST['db_host'] ?? 'localhost');
    $db_user = trim($_POST['db_user'] ?? 'root');
    $db_pass = trim($_POST['db_pass'] ?? '');
    $db_name = trim($_POST['db_name'] ?? 'news_website');
    $import = isset($_POST['import_sql']);

    // Test connection
    $conn = @new mysqli($db_host, $db_user, $db_pass);
    if ($conn->connect_errno) {
        $error = 'Cannot connect to MySQL: ' . $conn->connect_error;
    } else {
        // Create DB if not exists
        $conn->query("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn->select_db($db_name);

        // Import SQL if requested
        if ($import) {
            $sql_file = __DIR__ . '/database.sql';
            if (!file_exists($sql_file)) {
                $sql_file = __DIR__ . '/../install/database.sql';
            }
            if (file_exists($sql_file)) {
                $sql = file_get_contents($sql_file);
                try {
                    // Use multi_query to correctly parse SQL
                    if ($conn->multi_query($sql)) {
                        do {
                            if ($res = $conn->store_result()) {
                                $res->free();
                            }
                        } while ($conn->more_results() && $conn->next_result());
                    }
                    if ($conn->errno) {
                        $error = 'SQL import completed with minor warnings (tables may already exist).';
                    } else {
                        $success = 'Database tables imported successfully!';
                    }
                } catch (Throwable $e) {
                    // If tables already exist or minor SQL notice occurs, log notice rather than crash
                    if (strpos($e->getMessage(), 'already exists') !== false) {
                        $success = 'Database connection established (tables already existed).';
                    } else {
                        $error = 'SQL Import note: ' . $e->getMessage();
                    }
                }
            } else {
                $error = 'SQL file (database.sql) not found. Please import manually via phpMyAdmin.';
            }
        }


        if (empty($error) || strpos($error, 'already exist') !== false || strpos($error, 'Note:') !== false) {
            // Update admin/app/config.php and root config.php
            update_config_file(__DIR__ . '/../admin/app/config.php', $db_host, $db_user, $db_pass, $db_name);
            update_config_file(__DIR__ . '/../config.php', $db_host, $db_user, $db_pass, $db_name);

            $_SESSION['db_host'] = $db_host;
            $_SESSION['db_user'] = $db_user;
            $_SESSION['db_pass'] = $db_pass;
            $_SESSION['db_name'] = $db_name;

            header('Location: ?step=4');
            exit;
        }
    }
}

/* Step 4: Save site config */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $current_step === 4) {
    $site_url = rtrim(trim($_POST['site_url'] ?? ''), '/');
    $site_name = trim($_POST['site_name'] ?? 'ConnectBihar');

    $_SESSION['site_url'] = $site_url;
    $_SESSION['site_name'] = $site_name;

    // Update settings table if DB is connected
    try {
        $db = @new mysqli(
            $_SESSION['db_host'] ?? 'localhost',
            $_SESSION['db_user'] ?? 'root',
            $_SESSION['db_pass'] ?? '',
            $_SESSION['db_name'] ?? 'news_website'
        );
        if (!$db->connect_errno) {
            $su = $db->real_escape_string($site_url);
            $sn = $db->real_escape_string($site_name);

            $check = $db->query("SELECT * FROM settings LIMIT 1");
            if ($check && $check->num_rows > 0) {
                $db->query("UPDATE settings SET websiteUrl='{$su}', websitename='{$sn}'");
            } else {
                $db->query("INSERT INTO settings (websiteUrl, websitename) VALUES ('{$su}', '{$sn}')");
            }
        }
    } catch (Exception $e) {
    }

    header('Location: ?step=5');
    exit;
}

/* Step 5: Create admin account */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $current_step === 5) {
    $admin_user = trim($_POST['admin_user'] ?? 'Technicalbaba');
    $admin_pass = trim($_POST['admin_pass'] ?? '');
    $admin_confirm = trim($_POST['admin_confirm'] ?? '');
    $skip_admin = isset($_POST['skip_admin']);

    if ($skip_admin) {
        $_SESSION['admin_user'] = 'Technicalbaba';
        file_put_contents(__DIR__ . '/install.lock', date('Y-m-d H:i:s'));
        header('Location: ?step=6');
        exit;
    }

    if (empty($admin_user) || empty($admin_pass)) {
        $error = 'Username and password are required.';
    } elseif ($admin_pass !== $admin_confirm) {
        $error = 'Passwords do not match.';
    } elseif (strlen($admin_pass) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        try {
            $db = new mysqli(
                $_SESSION['db_host'] ?? 'localhost',
                $_SESSION['db_user'] ?? 'root',
                $_SESSION['db_pass'] ?? '',
                $_SESSION['db_name'] ?? 'news_website'
            );
            if ($db->connect_errno) {
                $error = 'DB connection failed: ' . $db->connect_error;
            } else {
                $hashed = md5($admin_pass);
                $u = $db->real_escape_string($admin_user);

                $check = $db->query("SELECT * FROM user WHERE role = 1 OR username='{$u}' LIMIT 1");
                if ($check && $check->num_rows > 0) {
                    $db->query("UPDATE user SET username='{$u}', password='{$hashed}', role=1, userStatus='Y' WHERE role=1 OR username='{$u}' LIMIT 1");
                } else {
                    $db->query("INSERT INTO user (username, first_name, last_name, email, password, role, userStatus) VALUES ('{$u}', 'Admin', 'User', 'admin@connectbihar.in', '{$hashed}', 1, 'Y')");
                }

                $_SESSION['admin_user'] = $admin_user;

                // Write lock file
                file_put_contents(__DIR__ . '/install.lock', date('Y-m-d H:i:s'));

                header('Location: ?step=6');
                exit;
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// ─── Check requirements ───────────────────────────────────────────────────────
function check_requirements()
{
    $checks = [];
    $checks['PHP >= 7.4'] = version_compare(PHP_VERSION, '7.4.0', '>=');
    $checks['MySQLi Extension'] = extension_loaded('mysqli');
    $checks['GD / Image Extension'] = extension_loaded('gd');
    $checks['File Uploads Enabled'] = (bool) ini_get('file_uploads');
    $checks['admin/app/config.php Writable'] = is_writable(dirname(__DIR__) . '/admin/app/config.php') || is_writable(dirname(__DIR__) . '/admin/app/');
    $checks['config.php Writable'] = is_writable(dirname(__DIR__) . '/config.php') || !file_exists(dirname(__DIR__) . '/config.php');
    $checks['Session Support'] = function_exists('session_start');
    return $checks;
}

$requirements = check_requirements();
$all_pass = !in_array(false, $requirements);

// ─── Detect base URL ──────────────────────────────────────────────────────────
function detect_base_url()
{
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $script = dirname(dirname($_SERVER['SCRIPT_NAME']));
    $script = rtrim($script, '/');
    return $protocol . '://' . $host . $script;
}

$detected_url = detect_base_url();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation Wizard — PHP News Website</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ── Reset & Base ── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --accent: #6366f1;
            --accent2: #8b5cf6;
            --grad: linear-gradient(135deg, #6366f1, #8b5cf6);
            --success: #22c55e;
            --danger: #ef4444;
            --warning: #f59e0b;
            --dark: #0f172a;
            --dark2: #1e293b;
            --border: rgba(255,255,255,0.1);
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --card-bg: rgba(255,255,255,0.05);
            --input-bg: rgba(255,255,255,0.07);
            --r: 14px;
        }

        html { font-size: 16px; }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 60%, #0f172a 100%);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            display: flex;
            flex-direction: column;
        }

        /* ── Animated bg ── */
        .bg-shapes { position: fixed; inset: 0; overflow: hidden; pointer-events: none; z-index: 0; }
        .bg-shapes span {
            position: absolute; border-radius: 50%; opacity: 0.04;
            animation: floatBg 12s ease-in-out infinite;
        }
        .bg-shapes span:nth-child(1) { width: 500px; height: 500px; background:#6366f1; top:-150px; right:-100px; animation-delay:0s; }
        .bg-shapes span:nth-child(2) { width: 350px; height: 350px; background:#8b5cf6; bottom:-100px; left:-80px; animation-delay:3s; animation-direction:reverse; }
        .bg-shapes span:nth-child(3) { width: 250px; height: 250px; background:#0ea5e9; top:40%; left:5%; animation-delay:6s; }
        .bg-shapes span:nth-child(4) { width: 180px; height: 180px; background:#ec4899; bottom:20%; right:10%; animation-delay:9s; }
        @keyframes floatBg { 0%,100%{transform:translateY(0) rotate(0)} 50%{transform:translateY(-30px) rotate(8deg)} }

        /* ── Layout ── */
        .wizard-wrap {
            position: relative; z-index: 1;
            max-width: 860px; width: 100%;
            margin: 40px auto; padding: 0 16px;
            flex: 1;
        }

        /* ── Header ── */
        .wizard-header { text-align: center; margin-bottom: 36px; }
        .wizard-logo {
            width: 64px; height: 64px; border-radius: 18px;
            background: var(--grad); display: inline-flex;
            align-items: center; justify-content: center;
            font-size: 26px; color: #fff; margin-bottom: 16px;
            box-shadow: 0 12px 36px rgba(99,102,241,.45);
        }
        .wizard-header h1 { font-size: 26px; font-weight: 800; letter-spacing: -0.5px; }
        .wizard-header p { color: var(--text-muted); font-size: 14px; margin-top: 6px; }

        /* ── Step Progress ── */
        .step-track {
            display: flex; align-items: flex-start;
            justify-content: center; gap: 0;
            margin-bottom: 36px; overflow-x: auto;
            padding-bottom: 4px;
        }
        .step-item {
            display: flex; flex-direction: column;
            align-items: center; position: relative; flex: 1; min-width: 80px;
        }
        .step-item:not(:last-child)::after {
            content: ''; position: absolute;
            top: 20px; left: calc(50% + 20px);
            width: calc(100% - 40px); height: 2px;
            background: rgba(255,255,255,0.1);
            z-index: 0;
        }
        .step-item.done:not(:last-child)::after { background: var(--accent); }
        .step-item.active:not(:last-child)::after { background: rgba(99,102,241,.3); }

        .step-circle {
            width: 40px; height: 40px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 16px; font-weight: 700; position: relative; z-index: 1;
            border: 2px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
            color: var(--text-muted);
            transition: all 0.3s;
        }
        .step-item.done .step-circle { background: var(--accent); border-color: var(--accent); color: #fff; }
        .step-item.active .step-circle { background: var(--grad); border-color: var(--accent); color: #fff; box-shadow: 0 0 0 4px rgba(99,102,241,.25); }
        .step-label { font-size: 11px; font-weight: 600; color: var(--text-muted); margin-top: 6px; text-align: center; letter-spacing: 0.2px; }
        .step-item.active .step-label, .step-item.done .step-label { color: var(--text); }

        /* ── Card ── */
        .wizard-card {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px;
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            box-shadow: 0 30px 80px rgba(0,0,0,.4);
            animation: fadeUp 0.45s cubic-bezier(0.34,1.56,0.64,1) both;
        }
        @keyframes fadeUp { from{opacity:0;transform:translateY(24px) scale(.98)} to{opacity:1;transform:none} }

        .card-title {
            font-size: 20px; font-weight: 800; margin-bottom: 6px;
            display: flex; align-items: center; gap: 10px;
        }
        .card-title i { color: var(--accent); font-size: 18px; }
        .card-subtitle { color: var(--text-muted); font-size: 14px; margin-bottom: 28px; }

        /* ── Form ── */
        .field-group { margin-bottom: 18px; }
        label.field-label { display: block; font-size: 12.5px; font-weight: 600; color: var(--text-muted); margin-bottom: 7px; letter-spacing: 0.3px; }
        .field-input {
            width: 100%; padding: 11px 14px;
            background: var(--input-bg);
            border: 1.5px solid rgba(255,255,255,0.12);
            border-radius: 10px; color: #fff;
            font-size: 14px; font-family: 'Inter', sans-serif;
            outline: none; transition: all 0.25s;
        }
        .field-input:focus { border-color: var(--accent); background: rgba(99,102,241,0.12); box-shadow: 0 0 0 3px rgba(99,102,241,.2); }
        .field-input::placeholder { color: rgba(255,255,255,0.3); }
        .field-hint { font-size: 11.5px; color: var(--text-muted); margin-top: 5px; }

        .field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        @media(max-width:600px) { .field-row { grid-template-columns: 1fr; } }

        /* ── Checkbox ── */
        .checkbox-wrap { display: flex; align-items: flex-start; gap: 10px; padding: 14px; background: rgba(255,255,255,0.04); border-radius: 10px; border: 1px solid rgba(255,255,255,0.08); }
        .checkbox-wrap input[type="checkbox"] { width: 18px; height: 18px; accent-color: var(--accent); flex-shrink: 0; margin-top: 2px; cursor: pointer; }
        .checkbox-text { font-size: 13px; color: var(--text); }
        .checkbox-text span { display: block; font-size: 11.5px; color: var(--text-muted); margin-top: 2px; }

        /* ── Buttons ── */
        .btn-primary {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 12px 26px; background: var(--grad);
            border: none; border-radius: 10px; color: #fff;
            font-size: 14px; font-weight: 700; font-family: 'Inter', sans-serif;
            cursor: pointer; transition: all 0.25s;
            box-shadow: 0 6px 20px rgba(99,102,241,.4);
            text-decoration: none;
        }
        .btn-primary:hover { opacity: .9; transform: translateY(-1px); box-shadow: 0 10px 28px rgba(99,102,241,.5); }
        .btn-primary:active { transform: scale(.97); }
        .btn-outline {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 11px 22px; background: transparent;
            border: 1.5px solid rgba(255,255,255,0.15);
            border-radius: 10px; color: var(--text-muted);
            font-size: 14px; font-weight: 600; font-family: 'Inter', sans-serif;
            cursor: pointer; transition: all 0.25s; text-decoration: none;
        }
        .btn-outline:hover { border-color: rgba(255,255,255,.35); color: #fff; }
        .btn-actions { display: flex; align-items: center; gap: 12px; margin-top: 28px; }

        /* ── Alert boxes ── */
        .alert { display: flex; align-items: flex-start; gap: 12px; padding: 14px 16px; border-radius: 10px; font-size: 13.5px; margin-bottom: 20px; }
        .alert i { font-size: 16px; flex-shrink: 0; margin-top: 1px; }
        .alert-error { background: rgba(239,68,68,.12); border: 1px solid rgba(239,68,68,.3); color: #fca5a5; }
        .alert-error i { color: #ef4444; }
        .alert-success { background: rgba(34,197,94,.1); border: 1px solid rgba(34,197,94,.25); color: #86efac; }
        .alert-success i { color: #22c55e; }
        .alert-info { background: rgba(14,165,233,.1); border: 1px solid rgba(14,165,233,.2); color: #7dd3fc; }
        .alert-info i { color: #0ea5e9; }

        /* ── Requirements list ── */
        .req-list { display: flex; flex-direction: column; gap: 10px; }
        .req-item {
            display: flex; align-items: center; gap: 12px;
            padding: 12px 16px; border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.07);
            background: rgba(255,255,255,0.04);
            font-size: 13.5px;
        }
        .req-item .req-icon { width: 28px; height: 28px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 13px; flex-shrink: 0; }
        .req-ok  .req-icon { background: rgba(34,197,94,.15); color: #22c55e; }
        .req-fail .req-icon { background: rgba(239,68,68,.15); color: #ef4444; }
        .req-item .req-name { flex: 1; font-weight: 500; }
        .req-badge { font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 20px; }
        .req-ok  .req-badge { background: rgba(34,197,94,.15); color: #22c55e; }
        .req-fail .req-badge { background: rgba(239,68,68,.15); color: #ef4444; }

        /* ── Welcome bullets ── */
        .feature-list { display: flex; flex-direction: column; gap: 12px; margin: 20px 0; }
        .feature-item { display: flex; align-items: center; gap: 12px; font-size: 14px; }
        .feature-icon { width: 36px; height: 36px; border-radius: 10px; background: rgba(99,102,241,.15); display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 14px; flex-shrink: 0; }

        /* ── Finish screen ── */
        .finish-card { text-align: center; padding: 20px 0; }
        .finish-icon { font-size: 64px; margin-bottom: 16px; animation: popIn .5s cubic-bezier(0.34,1.56,0.64,1) both; }
        @keyframes popIn { from{transform:scale(0) rotate(-20deg);opacity:0} to{transform:none;opacity:1} }
        .finish-card h2 { font-size: 26px; font-weight: 800; margin-bottom: 8px; }
        .finish-card p { color: var(--text-muted); font-size: 14px; max-width: 400px; margin: 0 auto 28px; }
        .finish-links { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

        .cred-box { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.1); border-radius: 12px; padding: 18px 22px; margin: 20px 0; text-align: left; }
        .cred-box h4 { font-size: 13px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.8px; margin-bottom: 12px; }
        .cred-row { display: flex; align-items: center; justify-content: space-between; font-size: 13.5px; padding: 6px 0; border-bottom: 1px solid rgba(255,255,255,.06); }
        .cred-row:last-child { border-bottom: none; }
        .cred-row .label { color: var(--text-muted); }
        .cred-row .value { font-weight: 600; font-family: monospace; color: #c4b5fd; }

        /* ── Footer ── */
        .wizard-footer { text-align: center; padding: 20px; color: var(--text-muted); font-size: 12px; position: relative; z-index: 1; }

        /* ── PHP version badge ── */
        .php-badge { display: inline-flex; align-items: center; gap: 6px; background: rgba(99,102,241,.15); color: #a5b4fc; font-size: 12px; font-weight: 600; padding: 4px 12px; border-radius: 20px; margin-bottom: 16px; }
    </style>
</head>
<body>
<div class="bg-shapes">
    <span></span><span></span><span></span><span></span>
</div>

<div class="wizard-wrap">
    <!-- Header -->
    <div class="wizard-header">
        <div class="wizard-logo"><i class="fa-solid fa-newspaper"></i></div>
        <h1>PHP News Website</h1>
        <p>Installation Wizard — Set up your news portal in minutes</p>
    </div>

    <!-- Step Track -->
    <div class="step-track">
        <?php foreach ($steps as $num => $step): ?>
        <div class="step-item <?php
    if ($num < $current_step)
        echo 'done';
    elseif ($num === $current_step)
        echo 'active';
?>">
            <div class="step-circle">
                <?php if ($num < $current_step): ?>
                    <i class="fa-solid fa-check"></i>
                <?php else: ?>
                    <?php echo $num; ?>
                <?php endif; ?>
            </div>
            <div class="step-label"><?php echo $step['title']; ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Card -->
    <div class="wizard-card">

    <?php if ($current_step === 1): /* ── WELCOME ── */ ?>
        <div class="card-title"><i class="fa-solid fa-hand-wave"></i> Welcome to the Installer</div>
        <div class="card-subtitle">This wizard will guide you through setting up PHP News Website on your server.</div>

        <div class="php-badge"><i class="fa-brands fa-php"></i> PHP <?php echo PHP_VERSION; ?> detected</div>

        <div class="feature-list">
            <div class="feature-item"><div class="feature-icon"><i class="fa-solid fa-database"></i></div> Automatically configure your MySQL database connection</div>
            <div class="feature-item"><div class="feature-icon"><i class="fa-solid fa-upload"></i></div> Import database schema and default tables from <code>install/database.sql</code></div>
            <div class="feature-item"><div class="feature-icon"><i class="fa-solid fa-globe"></i></div> Configure website URL and site settings</div>
            <div class="feature-item"><div class="feature-icon"><i class="fa-solid fa-user-shield"></i></div> Setup administrator account for the dashboard</div>
            <div class="feature-item"><div class="feature-icon"><i class="fa-solid fa-lock"></i></div> Automatically lock the installer after setup</div>
        </div>

        <div class="alert alert-info">
            <i class="fa-solid fa-circle-info"></i>
            <div>Make sure <strong>Apache/Nginx</strong> and <strong>MySQL</strong> are running before proceeding. Have your database credentials ready.</div>
        </div>

        <div class="btn-actions">
            <a href="?step=2" class="btn-primary"><i class="fa-solid fa-arrow-right"></i> Start Installation</a>
        </div>

    <?php elseif ($current_step === 2): /* ── REQUIREMENTS ── */ ?>
        <div class="card-title"><i class="fa-solid fa-list-check"></i> System Requirements</div>
        <div class="card-subtitle">Checking if your server meets all requirements to run PHP News Website.</div>

        <div class="req-list">
            <?php foreach ($requirements as $name => $pass): ?>
            <div class="req-item <?php echo $pass ? 'req-ok' : 'req-fail'; ?>">
                <div class="req-icon">
                    <i class="fa-solid <?php echo $pass ? 'fa-check' : 'fa-times'; ?>"></i>
                </div>
                <span class="req-name"><?php echo $name; ?></span>
                <span class="req-badge"><?php echo $pass ? 'OK' : 'FAILED'; ?></span>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (!$all_pass): ?>
        <div class="alert alert-error" style="margin-top:20px;">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>Some requirements are not met. Please fix the issues above before continuing.</div>
        </div>
        <?php else: ?>
        <div class="alert alert-success" style="margin-top:20px;">
            <i class="fa-solid fa-circle-check"></i>
            <div>All requirements are met! You can proceed to the next step.</div>
        </div>
        <?php endif; ?>

        <div class="btn-actions">
            <a href="?step=1" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
            <?php if ($all_pass): ?>
            <a href="?step=3" class="btn-primary"><i class="fa-solid fa-arrow-right"></i> Continue</a>
            <?php endif; ?>
        </div>

    <?php elseif ($current_step === 3): /* ── DATABASE ── */ ?>
        <div class="card-title"><i class="fa-solid fa-database"></i> Database Configuration</div>
        <div class="card-subtitle">Enter your MySQL database credentials. The database will be created if it doesn't exist.</div>

        <?php if ($error): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <div><?php echo $error; ?></div></div>
        <?php endif; ?>
        <?php if ($success): ?>
        <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <div><?php echo $success; ?></div></div>
        <?php endif; ?>

        <form method="POST" action="?step=3">
            <div class="field-row">
                <div class="field-group">
                    <label class="field-label">Database Host</label>
                    <input type="text" name="db_host" class="field-input" value="localhost" placeholder="localhost" required>
                    <div class="field-hint">Usually <code>localhost</code> for XAMPP/WAMP</div>
                </div>
                <div class="field-group">
                    <label class="field-label">Database Name</label>
                    <input type="text" name="db_name" class="field-input" value="news_website" placeholder="news_website" required>
                    <div class="field-hint">Will be created automatically if it doesn't exist</div>
                </div>
            </div>
            <div class="field-row">
                <div class="field-group">
                    <label class="field-label">Database Username</label>
                    <input type="text" name="db_user" class="field-input" value="root" placeholder="root" required>
                </div>
                <div class="field-group">
                    <label class="field-label">Database Password</label>
                    <input type="password" name="db_pass" class="field-input" placeholder="Leave blank for XAMPP default">
                    <div class="field-hint">Leave blank if using XAMPP with default settings</div>
                </div>
            </div>
            <div class="field-group" style="margin-top:6px;">
                <div class="checkbox-wrap">
                    <input type="checkbox" name="import_sql" id="import_sql" checked>
                    <label for="import_sql" class="checkbox-text">
                        Import database tables automatically
                        <span>Imports all tables from <code>install/database.sql</code>. Uncheck if you've already imported.</span>
                    </label>
                </div>
            </div>
            <div class="btn-actions">
                <a href="?step=2" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-plug"></i> Connect & Continue</button>
            </div>
        </form>

    <?php elseif ($current_step === 4): /* ── SITE CONFIG ── */ ?>
        <div class="card-title"><i class="fa-solid fa-gear"></i> Site Configuration</div>
        <div class="card-subtitle">Configure your news portal's URL and basic settings.</div>

        <?php if ($error): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <div><?php echo $error; ?></div></div>
        <?php endif; ?>

        <form method="POST" action="?step=4">
            <div class="field-group">
                <label class="field-label">Site URL (Base URL)</label>
                <input type="url" name="site_url" class="field-input" value="<?php echo htmlspecialchars($detected_url); ?>" placeholder="http://localhost/PHP-News-Website" required>
                <div class="field-hint">Auto-detected. No trailing slash. Example: <code>http://localhost/PHP-News-Website</code></div>
            </div>
            <div class="field-group">
                <label class="field-label">Site Name</label>
                <input type="text" name="site_name" class="field-input" value="ConnectBihar" placeholder="My News Portal" required>
            </div>
            <div class="btn-actions">
                <a href="?step=3" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-arrow-right"></i> Save & Continue</button>
            </div>
        </form>

    <?php elseif ($current_step === 5): /* ── ADMIN ACCOUNT ── */ ?>
        <div class="card-title"><i class="fa-solid fa-user-shield"></i> Admin Account</div>
        <div class="card-subtitle">Set up your administrator login credentials for the admin dashboard.</div>

        <?php if ($error): ?>
        <div class="alert alert-error"><i class="fa-solid fa-circle-exclamation"></i> <div><?php echo $error; ?></div></div>
        <?php endif; ?>

        <div class="alert alert-info">
            <i class="fa-solid fa-circle-info"></i>
            <div>The default admin username is <strong>Technicalbaba</strong> (from database.sql). You can update it or set a new password below.</div>
        </div>

        <form method="POST" action="?step=5">
            <div class="field-group">
                <label class="field-label">Admin Username</label>
                <input type="text" name="admin_user" class="field-input" value="Technicalbaba" placeholder="Technicalbaba" required>
            </div>
            <div class="field-row">
                <div class="field-group">
                    <label class="field-label">New Password</label>
                    <input type="password" name="admin_pass" id="admin_pass" class="field-input" placeholder="Minimum 6 characters" required>
                </div>
                <div class="field-group">
                    <label class="field-label">Confirm Password</label>
                    <input type="password" name="admin_confirm" id="admin_confirm" class="field-input" placeholder="Repeat password" required>
                    <div class="field-hint" id="pw-match-msg" style="display:none;"></div>
                </div>
            </div>
            <div class="field-group" style="margin-top:6px;">
                <div class="checkbox-wrap">
                    <input type="checkbox" id="skip_admin" name="skip_admin">
                    <label for="skip_admin" class="checkbox-text">
                        Keep existing admin credentials
                        <span>Skip password update — use default credentials from the SQL import</span>
                    </label>
                </div>
            </div>
            <div class="btn-actions">
                <a href="?step=4" class="btn-outline"><i class="fa-solid fa-arrow-left"></i> Back</a>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-flag-checkered"></i> Finish Setup</button>
            </div>
        </form>

    <?php elseif ($current_step === 6): /* ── FINISH ── */ ?>
        <div class="finish-card">
            <div class="finish-icon">🎉</div>
            <h2>Installation Complete!</h2>
            <p>Your PHP News Website has been successfully configured and is ready to use.</p>

            <div class="cred-box">
                <h4><i class="fa-solid fa-key" style="color:var(--accent);margin-right:6px;"></i> Admin Credentials</h4>
                <div class="cred-row">
                    <span class="label">Admin URL</span>
                    <span class="value"><?php echo htmlspecialchars(($_SESSION['site_url'] ?? '') . '/admin'); ?></span>
                </div>
                <div class="cred-row">
                    <span class="label">Username</span>
                    <span class="value"><?php echo htmlspecialchars($_SESSION['admin_user'] ?? 'Technicalbaba'); ?></span>
                </div>
                <div class="cred-row">
                    <span class="label">Database</span>
                    <span class="value"><?php echo htmlspecialchars($_SESSION['db_name'] ?? 'news_website'); ?> @ <?php echo htmlspecialchars($_SESSION['db_host'] ?? 'localhost'); ?></span>
                </div>
            </div>

            <div class="alert alert-info" style="text-align:left;">
                <i class="fa-solid fa-shield-halved"></i>
                <div><strong>Security Note:</strong> The installer has been locked automatically. Delete <code>install/install.lock</code> only if you need to re-run the wizard.</div>
            </div>

            <div class="finish-links">
                <a href="<?php echo htmlspecialchars(($_SESSION['site_url'] ?? '') . '/admin'); ?>" class="btn-primary">
                    <i class="fa-solid fa-gauge-high"></i> Go to Admin Panel
                </a>
                <a href="<?php echo htmlspecialchars($_SESSION['site_url'] ?? ''); ?>/" class="btn-outline">
                    <i class="fa-solid fa-newspaper"></i> Visit Website
                </a>
            </div>
        </div>
    <?php endif; ?>

    </div><!-- /.wizard-card -->
</div><!-- /.wizard-wrap -->

<div class="wizard-footer">
    PHP News Website &nbsp;·&nbsp; Installation Wizard &nbsp;·&nbsp; <span>Made with ❤️ by Aman Projects</span>
</div>

<script>
// Live password match check
var pw = document.getElementById('admin_pass');
var cf = document.getElementById('admin_confirm');
var msg = document.getElementById('pw-match-msg');
var skip = document.getElementById('skip_admin');

if (cf && pw) {
    cf.addEventListener('input', function() {
        if (!msg) return;
        msg.style.display = 'block';
        if (pw.value === cf.value && cf.value !== '') {
            msg.innerHTML = '<i class="fa-solid fa-check-circle" style="color:#22c55e;"></i> Passwords match';
            msg.style.color = '#86efac';
        } else {
            msg.innerHTML = '<i class="fa-solid fa-times-circle" style="color:#ef4444;"></i> Passwords do not match';
            msg.style.color = '#fca5a5';
        }
    });
}

if (skip) {
    skip.addEventListener('change', function() {
        var fields = document.querySelectorAll('#admin_pass, #admin_confirm');
        fields.forEach(function(f) { f.disabled = skip.checked; f.required = !skip.checked; });
    });
}
</script>
</body>
</html>
