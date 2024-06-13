<?php session_start();
include_once('./app/_DBconnect.php');
$settingd = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings"));$url = $settingd['websiteUrl']; ?>
<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $url; ?>/assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Forgot Password Basic - Pages | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

    <meta name="description" content="<?php echo $settingd['keywords']; ?>" />
    <meta name="keywords" content="<?php echo $settingd['keywords']; ?>">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo $url; ?>/assets/images/<?php echo $settingd['favicon']; ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

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

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="./assets/js/config.js"></script>
</head>

<body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-4">
                <!-- Forgot Password -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="index.html" class="app-brand-link gap-2">
                                <span class="app-brand-logo demo">
                                    <img src="<?php echo $url; ?>/assets/images/<?php echo $settingd['logo']; ?>" alt="<?php echo $settingd['websitename']; ?>" style="width: 100%;height: 70px;">
                                </span>
                            </a>
                        </div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Forgot Password? 🔒</h4>
                        <form id="formAuthentication" class="mb-3" action="index.html" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" autofocus />
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Mobile</label>
                                <input type="text" class="form-control" id="mobile" name="mobile" placeholder="Enter your mobile" autofocus />
                            </div>
                            <button type="button" class="dropdown-item btn btn-primary d-grid w-100 searchUsername" data-bs-toggle="modal" data-bs-target="#ShowUser"> 🔑 Search User</button>
                            <!-- <button class="btn btn-primary d-grid w-100">🔒 Reset Password</button> -->
                        </form>
                        <div class="text-center">
                            <a href="index.php" class="d-flex align-items-center justify-content-center">
                                <i class="bx bx-chevron-left scaleX-n1-rtl bx-sm"></i>Back to login</a>
                        </div>
                    </div>
                </div>
                <!-- /Forgot Password -->
            </div>
        </div>
    </div>
    <div class="modal fade" id="ShowUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" id="lodaingUserD" role="document">
            <div class="modal-content fetched-data">
                
            </div>
        </div>
    </div>
    <!-- / Content -->
    <div class="buy-now">
            <a href="https://technicalaman.co.in/" target="_blank" class="btn btn-danger btn-buy-now">💖 Technical Baba 💕</a>
          </div> 
    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="./assets/vendor/libs/jquery/jquery.js"></script>
    <script src="./assets/vendor/libs/popper/popper.js"></script>
    <script src="./assets/vendor/js/bootstrap.js"></script>
    <script src="./assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="./assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->

    <!-- Main JS -->
    <script src="./assets/js/main.js"></script>
    <!-- Page JS -->
    <script src="./assets/js/jquery-3.6.4.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.searchUsername').click(function(e) {
                e.preventDefault();

                var userName = $('#username').val();
                var userMobile = $('#mobile').val();
                var useR = "";

                $.ajax({
                    url: 'app/lodaing-user.php',
                    type: 'POST',
                    data: {
                        searchUser: useR,
                        username: userName,
                        usermobile: userMobile
                    },
                    success: function(userdata) {
                        $('.fetched-data').html(userdata);
                    }
                });

            });
        });
    </script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>

</html>