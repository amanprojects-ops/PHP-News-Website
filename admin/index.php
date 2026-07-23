<?php session_start();
include_once ('./app/config.php');

if (isset($_SESSION['username'])) {
    header("Location: dashboard.php");
    exit();
}

$settingd = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM settings'));
$url = $settingd['websiteUrl']; ?>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $url; ?>/assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login | Dashboard</title>

    <meta name="description" content="<?php echo $settingd['keywords']; ?>" />
    <meta name="keywords" content="<?php echo $settingd['keywords']; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $url; ?>/assets/images/<?php echo $settingd['favicon']; ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/admin/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/admin/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/admin/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/admin/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="../assets/admin/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="../assets/admin/vendor/js/helpers.js"></script>
    <script src="../assets/admin/js/config.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Custom Premium Styling overrides -->
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #311042 100%) !important;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient background lights */
        body::before {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(30, 144, 255, 0.15) 0%, transparent 70%);
            top: 10%;
            left: 10%;
            pointer-events: none;
            z-index: 0;
        }

        body::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(147, 51, 234, 0.12) 0%, transparent 70%);
            bottom: 10%;
            right: 10%;
            pointer-events: none;
            z-index: 0;
        }

        .container-xxl {
            position: relative;
            z-index: 10;
        }

        .authentication-wrapper {
            min-height: auto !important;
        }

        /* Glassmorphism Card style */
        .card {
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            backdrop-filter: blur(20px);
            border-radius: 20px !important;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3) !important;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.4) !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .card-body {
            padding: 40px !important;
        }

        .app-brand img {
            max-height: 50px !important;
            object-fit: contain;
            filter: drop-shadow(0 2px 8px rgba(0, 0, 0, 0.2));
            transition: transform 0.3s ease;
        }

        .app-brand img:hover {
            transform: scale(1.05);
        }

        h4 {
            color: #ffffff !important;
            font-weight: 700 !important;
            text-align: center;
            margin-top: 15px;
        }

        p.text-muted, p.mb-4 {
            color: #94a3b8 !important;
            text-align: center;
            font-size: 14px;
        }

        /* Input Styling */
        .form-label {
            color: #cbd5e1 !important;
            font-weight: 600 !important;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            transition: all 0.3s ease !important;
        }

        .form-control:focus {
            border-color: #1e90ff !important;
            background: rgba(255, 255, 255, 0.08) !important;
            box-shadow: 0 0 0 4px rgba(30, 144, 255, 0.15) !important;
        }

        .form-control::placeholder {
            color: #64748b !important;
        }

        .input-group-text {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            color: #94a3b8 !important;
            border-radius: 0 10px 10px 0 !important;
            padding: 12px 16px !important;
        }

        .input-group-merge .form-control {
            border-radius: 10px 0 0 10px !important;
        }

        .input-group-merge:focus-within .input-group-text {
            border-color: #1e90ff !important;
        }

        /* Links */
        small, a {
            color: #1e90ff !important;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        small:hover, a:hover {
            color: #60a5fa !important;
            text-decoration: underline;
        }

        /* Primary Button */
        .btn-primary {
            background: linear-gradient(135deg, #1e90ff 0%, #0077e6 100%) !important;
            border: none !important;
            padding: 12px 20px !important;
            font-size: 15px !important;
            font-weight: 700 !important;
            border-radius: 10px !important;
            box-shadow: 0 4px 12px rgba(30, 144, 255, 0.25) !important;
            transition: all 0.3s ease !important;
            color: #ffffff !important;
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(30, 144, 255, 0.4) !important;
            color: #ffffff !important;
        }

        .btn-primary:active {
            transform: translateY(1px) !important;
        }

        /* Custom Watermark Overlay in center background */
        .bg-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-15deg);
            font-size: 7rem;
            font-weight: 900;
            color: rgba(255, 255, 255, 0.015);
            text-transform: uppercase;
            letter-spacing: 12px;
            pointer-events: none;
            user-select: none;
            z-index: 1;
            white-space: nowrap;
        }

        /* Fixed watermark Badge */
        .watermark-badge {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 9999;
        }

        .watermark-badge a {
            display: flex;
            align-items: center;
            gap: 6px;
            background: rgba(30, 144, 255, 0.1);
            color: #1e90ff !important;
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid rgba(30, 144, 255, 0.2);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none !important;
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(30, 144, 255, 0.15);
        }

        .watermark-badge a:hover {
            background: #1e90ff;
            color: #ffffff !important;
            border-color: #1e90ff;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(30, 144, 255, 0.3);
        }

        /* Technical Baba Badge */
        .buy-now a {
            background: rgba(239, 68, 68, 0.1) !important;
            color: #ef4444 !important;
            border: 1px solid rgba(239, 68, 68, 0.2) !important;
            backdrop-filter: blur(10px) !important;
            border-radius: 20px !important;
            padding: 8px 16px !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.1) !important;
            font-weight: 600 !important;
            font-size: 13px !important;
            transition: all 0.3s ease !important;
        }

        .buy-now a:hover {
            background: #ef4444 !important;
            color: #ffffff !important;
            border-color: #ef4444 !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(239, 68, 68, 0.3) !important;
        }
    </style>
</head>

<body>
    <!-- Center watermark overlay -->
    <div class="bg-watermark">AMAN PROJECTS</div>

    <!-- Content -->
    <?php
    if (isset($_SESSION['logout'])) {
        echo "<script>
    Swal.fire({
      position: 'center',
      icon: 'success',
      title: '{$_SESSION['logout']}',
      showConfirmButton: false,
      timer: 2000
    });
    </script>";
        unset($_SESSION['logout']);
    } elseif (isset($_SESSION['error'])) {
        echo "<script>
    Swal.fire({
      position: 'center',
      icon: 'error',
      title: '{$_SESSION['error']}',
      showConfirmButton: false,
      timer: 2000
    })
    </script>";
        unset($_SESSION['error']);
    } elseif (isset($_SESSION['warning'])) {
        echo "<script>
    Swal.fire({
      position: 'center',
      icon: 'warning',
      title: '{$_SESSION['warning']}',
      showConfirmButton: false,
      timer: 2000
    })
    </script>";
        unset($_SESSION['warning']);
    } elseif (isset($_SESSION['success'])) {
        echo "<script>
    Swal.fire({
      position: 'center',
      icon: 'success',
      title: '{$_SESSION['success']}',
      showConfirmButton: false,
      timer: 2000
    })
    </script>";
        unset($_SESSION['success']);
    }
    ?>

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="./" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img src="<?php echo $url; ?>/assets/images/<?php echo $settingd['logo']; ?>" alt="<?php echo $settingd['websitename']; ?>" style="width: 100%;height: 70px;">
                                </span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Welcome to <?php echo $settingd['websitename']; ?>! 👋</h4>
                        <p class="mb-4">Please sign-in to your account</p>

                        <form id="formAuthentication" class="mb-3" action="./app/app.php" method="POST">
                            <div class="mb-3">
                                <label for="logUsername" class="form-label">Username</label>
                                <input type="text" class="form-control" id="logUsername" name="logUsername"
                                    placeholder="Enter your email or username" autofocus />
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="logPassword">Password</label>
                                    <a href="forgot-password.php">
                                        <small>Forgot Password?</small>
                                    </a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="logPassword" class="form-control" name="logPassword"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <button class="btn btn-primary d-grid w-100" name="loginBtn" type="submit">Sign in</button>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>

    <!-- Floating Watermark Badge -->
    <div class="watermark-badge">
        <a href="https://amanprojects.com" target="_blank">
            <i class="bx bx-award"></i> Aman Projects
        </a>
    </div>

    <div class="buy-now">
        <a href="https://technicalaman.co.in" target="_blank" class="btn btn-danger btn-buy-now">💖 Technical Baba 💕</a>
    </div>

    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/admin/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/admin/vendor/libs/popper/popper.js"></script>
    <script src="../assets/admin/vendor/js/bootstrap.js"></script>
    <script src="../assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/admin/vendor/js/menu.js"></script>
    <!-- Main JS -->
    <script src="../assets/admin/js/main.js"></script>
    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>