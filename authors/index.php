<?php session_start();
include_once ('./app/_DBconnect.php');
$settingd = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings")); ?>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="../assets/"
    data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Login | Dashboard</title>

    <meta name="description" content="" />
     <meta name="keywords" content="<?php echo $settingd['keywords']; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/images/<?php echo $settingd['favicon']; ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="./assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="./assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="./assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="./assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="./assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="./assets/vendor/js/helpers.js"></script>
    <script src="./assets/js/config.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
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
    }
    elseif (isset($_SESSION['success'])) {
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
                                    <img src="../assets/images/<?php echo $settingd['logo']; ?>" alt="<?php echo $settingd['websitename']; ?>" style="width: 100%;height: 70px;">
                                </span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Welcome to <?php echo $settingd['websitename']; ?>! 👋</h4>
                        <p class="mb-4">Please sign-in to your account</p>

                        <form id="formAuthentication" class="mb-3" action="./app/app.php" method="POST">
                            <div class="mb-3">
                                <label for="" class="form-label">Username</label>
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
                                <button class="btn btn-primary d-grid w-100" name="loginBtn" type="submit">Sign
                                    in</button>
                            </div>
                        </form>

                        <!-- <p class="text-center">
                            <a href="register.php">
                                <span>Create an account</span>
                            </a>
                        </p> -->
                    </div>
                </div>
                <!-- /Register -->
            </div>
        </div>
    </div>

    <!-- / Content -->

    <div class="buy-now">
        <a href="https://technicalaman.co.in" target="_blank" class="btn btn-danger btn-buy-now">💖 Technical Baba 💕</a>
    </div>
    <!-- build:js assets/vendor/js/core.js -->
    <script src="./assets/vendor/libs/jquery/jquery.js"></script>
    <script src="./assets/vendor/libs/popper/popper.js"></script>
    <script src="./assets/vendor/js/bootstrap.js"></script>
    <script src="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="./assets/vendor/js/menu.js"></script>
    <!-- Main JS -->
    <script src="./assets/js/main.js"></script>
    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>