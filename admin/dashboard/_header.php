<?php session_start();
include_once('../app/_DBconnect.php');

if(!isset($_SESSION['username'])){
  echo"<script>window.location.href='../';</script>";
}

$settingd = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings")); $url = $settingd['websiteUrl'];?>
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="<?php echo $url; ?>/assets/"
  data-template="vertical-menu-template-free">

<head>
  <meta charset="utf-8" />
  <meta name="viewport"
    content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

  <title>Admin | Dashboard</title>

  <meta name="description" content="<?php echo $settingd['keywords']; ?>" />
  <meta name="keywords" content="<?php echo $settingd['keywords']; ?>">
  
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="<?php echo $url; ?>/assets/images/<?php echo $settingd['favicon']; ?>"/>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

  <!-- Icons. Uncomment required icon fonts -->
  <link rel="stylesheet" href="..//assets/vendor/fonts/boxicons.css" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="..//assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="..//assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="..//assets/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="..//assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <link rel="stylesheet" href="..//assets/vendor/libs/apex-charts/apex-charts.css" />

  <!-- Page CSS -->

  <!-- Helpers -->
  <script src="..//assets/vendor/js/helpers.js"></script>
  <script src="../assets/js/config.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
  
  <script src="../assets/summernotes/jquery-3.4.1.slim.min.js"></script>
  <link rel="stylesheet" href="../assets/summernotes/summernote-lite.min.css">
  <script src="../assets/summernotes/summernote-lite.min.js"></script>
   
</head>

<body>
  <?php
   if ($_SESSION['userStatus'] == 'N') {
    echo "<script>
    Swal.fire({
      position: 'center',
      icon: 'info',
      title: 'Sorry Your is account Inactive.',
      showConfirmButton: false,
      timer: 2000
    });
    </script>";
    unset($_SESSION['userStatus']);
    echo "<script>window.location.href='logout.php';</script>";
  }
  if ($_SESSION['userStatus'] == 'W') {
    echo "<script>
    Swal.fire({
      position: 'center',
      icon: 'info',
      title: 'Sorry Your is account Approved.',
      showConfirmButton: false,
      timer: 2000
    });
    </script>";
    unset($_SESSION['userStatus']);
    echo "<script>window.location.href='logout.php';</script>";
  }
  $request_url = basename($_SERVER['REQUEST_URI']);
  // echo "<script>alert('$request_url');</script>";
  ?>
  <?php include_once('./_message.php'); ?>
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
        <div class="app-brand demo">
          <a href="./" class="app-brand-link">
            <span class="">
              <img src="<?php echo $url; ?>/assets/images/<?php echo $settingd['logo']; ?>" alt="<?php echo $settingd['websitename']; ?>"
                style="width: 100%;">
            </span>
            <!-- <span class="app-brand-text demo menu-text fw-bolder ms-2"></span> -->
          </a>

          <a href="./" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
          </a>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1">
          <!-- Dashboard -->
          <li class="menu-item <?php if($request_url == 'dashboard' || $request_url == 'index.php'){echo "active";}?>">
            <a href="./" class="menu-link">
              <i class="menu-icon tf-icons bx bx-home-circle"></i>
              <div data-i18n="Analytics">Dashboard</div>
            </a>
          </li>
          <!-- Layouts -->
          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Post Tables</span>
          </li>

          <li class="menu-item <?php if ($request_url == 'view-post.php' || $request_url == 'new-post.php' || $request_url == 'update-post.php') {
            echo "active open";
          } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-spreadsheet"></i>
              <div data-i18n="Layouts">Post Dashboard</div>
            </a>

            <ul class="menu-sub">
              <li class="menu-item <?php if ($request_url == 'view-post.php') {echo "active";} ?>">
                <a href="view-post.php" class="menu-link">
                  <div data-i18n="Without menu">View Posts</div>
                </a>
              </li>
              <li class="menu-item <?php if ($request_url == 'new-post.php') {echo "active";} ?>">
                <a href="new-post.php" class="menu-link">
                  <div data-i18n="Without navbar">Add Post</div>
                </a>
              </li>              
            </ul>
          </li>

          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Category Tables</span>
          </li>
          <!-- Category Sections -->
          <li class="menu-item <?php if ($request_url == 'view-category.php' || $request_url == 'new-category.php' || $request_url == 'update-category.php') {
            echo "active open";
          } ?>">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="bx bx-cube bx-sm"></i>
              <div data-i18n="Extended UI">&nbsp;Category Dashboard</div>
            </a>
            <ul class="menu-sub ">
              <li class="menu-item <?php if ($request_url == 'view-category.php') {
                echo "active";
              } ?>">
                <a href="view-category.php" class="menu-link">
                  <div data-i18n="Perfect Scrollbar">View Categorys</div>
                </a>
              </li>
              <?php if($_SESSION['role'] == 1){?>
              <li class="menu-item <?php if ($request_url == 'new-category.php') {echo "active";} ?>">
                <a href="new-category.php" class="menu-link">
                  <div data-i18n="Perfect Scrollbar">Add Category</div>
                </a>
              </li>
              <?php } ?>             
            </ul>
          </li>
          <!-- Users Sections -->
          <?php if($_SESSION['role'] == 1 || $_SESSION['role'] == 2 || $_SESSION['role'] == 3){ ?>
          <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Users Tables</span>
          </li>
          <li class="menu-item <?php if ($request_url == 'view-user.php' || $request_url == 'new-user.php' || $request_url == 'update-user.php') {
              echo "active open";
            } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-user"></i>
              <div data-i18n="Account Settings">Users Dashboard</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item <?php if ($request_url == 'view-user.php') {
                  echo "active";
                } ?>">
                <a href="view-user.php" class="menu-link">
                  <div data-i18n="Account">View Users</div>
                </a>
              </li>
              <li class="menu-item <?php if ($request_url == 'new-user.php') {
                  echo "active";
                } ?>">
                <a href="new-user.php" class="menu-link">
                  <div data-i18n="Account">Add User</div>
                </a>
              </li>           
            </ul>
          </li>
          <?php }
          if($_SESSION['author_id'] == 1 && $_SESSION['role'] == 1){
          ?>
          <li class="menu-header small text-uppercase"><span class="menu-header-text">Website settings</span></li>

          <!-- Website Settings -->
          <li class="menu-item <?php if ($request_url == 'manage-website.php' || $request_url == 'view-visitor.php') {
              echo "active open";
            } ?>">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <!-- <i class="menu-icon tf-icons bx bx-box"></i> -->
              <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                style="fill: rgba(0, 0, 0, 1);">
                <path
                  d="M12 16c2.206 0 4-1.794 4-4s-1.794-4-4-4-4 1.794-4 4 1.794 4 4 4zm0-6c1.084 0 2 .916 2 2s-.916 2-2 2-2-.916-2-2 .916-2 2-2z">
                </path>
                <path
                  d="m2.845 16.136 1 1.73c.531.917 1.809 1.261 2.73.73l.529-.306A8.1 8.1 0 0 0 9 19.402V20c0 1.103.897 2 2 2h2c1.103 0 2-.897 2-2v-.598a8.132 8.132 0 0 0 1.896-1.111l.529.306c.923.53 2.198.188 2.731-.731l.999-1.729a2.001 2.001 0 0 0-.731-2.732l-.505-.292a7.718 7.718 0 0 0 0-2.224l.505-.292a2.002 2.002 0 0 0 .731-2.732l-.999-1.729c-.531-.92-1.808-1.265-2.731-.732l-.529.306A8.1 8.1 0 0 0 15 4.598V4c0-1.103-.897-2-2-2h-2c-1.103 0-2 .897-2 2v.598a8.132 8.132 0 0 0-1.896 1.111l-.529-.306c-.924-.531-2.2-.187-2.731.732l-.999 1.729a2.001 2.001 0 0 0 .731 2.732l.505.292a7.683 7.683 0 0 0 0 2.223l-.505.292a2.003 2.003 0 0 0-.731 2.733zm3.326-2.758A5.703 5.703 0 0 1 6 12c0-.462.058-.926.17-1.378a.999.999 0 0 0-.47-1.108l-1.123-.65.998-1.729 1.145.662a.997.997 0 0 0 1.188-.142 6.071 6.071 0 0 1 2.384-1.399A1 1 0 0 0 11 5.3V4h2v1.3a1 1 0 0 0 .708.956 6.083 6.083 0 0 1 2.384 1.399.999.999 0 0 0 1.188.142l1.144-.661 1 1.729-1.124.649a1 1 0 0 0-.47 1.108c.112.452.17.916.17 1.378 0 .461-.058.925-.171 1.378a1 1 0 0 0 .471 1.108l1.123.649-.998 1.729-1.145-.661a.996.996 0 0 0-1.188.142 6.071 6.071 0 0 1-2.384 1.399A1 1 0 0 0 13 18.7l.002 1.3H11v-1.3a1 1 0 0 0-.708-.956 6.083 6.083 0 0 1-2.384-1.399.992.992 0 0 0-1.188-.141l-1.144.662-1-1.729 1.124-.651a1 1 0 0 0 .471-1.108z">
                </path>
              </svg>
              <div data-i18n="User interface">&nbsp;Manage Website</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item <?php if ($request_url == 'manage-website.php') {
                  echo "active";
                } ?>">
                <a href="manage-website.php" class="menu-link">
                  <div data-i18n="Accordion">Web Settings</div>
                </a>
              </li>
              <li class="menu-item <?php if ($request_url == 'view-visitor.php') {
                  echo "active";
                } ?>">
                <a href="view-visitor.php" class="menu-link">
                  <div data-i18n="Accordion">Visitor Lists</div>
                </a>
              </li>
            </ul>
          </li>
          <li class="menu-item <?php if ($request_url == 'messages.php') {
              echo "active open";
            } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-envelope"></i>
              <div data-i18n="Misc">Messages</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item <?php if ($request_url == 'messages.php') {
                  echo "active";
                } ?>">
                <a href="messages.php" class="menu-link">
                  <div data-i18n="Error">Message</div>
                </a>
              </li>
            </ul>
          </li>
          <?php } ?>
          <!-- Help & Supports -->
          <li class="menu-header small text-uppercase"><span class="menu-header-text">Supports Team</span></li>
          <li class="menu-item <?php if ($request_url == 'technical-support.php') {
            echo "active";
          } ?>">
            <a href="technical-support.php" target="_blank" class="menu-link">
              <i class="menu-icon tf-icons bx bx-support"></i>
              <div data-i18n="Support">Support</div>
            </a>
          </li>

        </ul>
      </aside>
      <!-- / Menu -->
      <!-- Layout container -->
      <div class="layout-page">
      