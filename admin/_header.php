<?php session_start();
include_once ('app/config.php');

if (!isset($_SESSION['username'])) {
  header('Location: index.php');
  exit();
}

$settingd = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT * FROM settings'));
$url = $settingd['websiteUrl']; ?>
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

  <!-- Icons. Boxicons CDN (Upgraded) -->
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />

  <!-- Core CSS -->
  <link rel="stylesheet" href="../assets/admin/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../assets/admin/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/admin/css/demo.css" />

  <!-- Vendors CSS -->
  <link rel="stylesheet" href="../assets/admin/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

  <link rel="stylesheet" href="../assets/admin/vendor/libs/apex-charts/apex-charts.css" />

  <!-- Page CSS -->

  <!-- Helpers -->
  <script src="../assets/admin/vendor/js/helpers.js"></script>
  <script src="../assets/admin/js/config.js"></script>
  <script src="../assets/admin/vendor/libs/apex-charts/apexcharts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 
  
  <script src="../assets/admin/vendor/libs/jquery/jquery.js"></script>
  <link rel="stylesheet" href="../assets/admin/summernotes/summernote-lite.min.css">
  <script src="../assets/admin/summernotes/summernote-lite.min.js"></script>
  
 
</head>

<body>
  <?php
  $session_alerts = ['error' => 'User Inactive', 'warning' => 'Approval Pending'];
  foreach ($session_alerts as $key => $value) {
    if (isset($_SESSION[$key]) && $_SESSION[$key] == $value) {
      $safe_value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
      echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
      Swal.fire({
        position: 'center',
        icon: '{$key}',
        title: '{$safe_value}',
        text: 'Please contact support for assistance.',
        showConfirmButton: false,
        timer: 2500
      }).then(() => {
        window.location.href = 'index.php';
      });
    });
    </script>";
      unset($_SESSION[$key]);
      exit();
    }
  }
  $request_url = basename($_SERVER['REQUEST_URI']);
  ?>
  
  <!-- Layout wrapper -->
  <div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
      <!-- Menu -->
      <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" style="border-right: 1px solid rgba(226, 232, 240, 0.8); box-shadow: 4px 0 24px rgba(0,0,0,0.02);">
        <div class="app-brand demo" style="padding: 20px 1.5rem;">
          <a href="./" class="app-brand-link">
            <span class="app-brand-logo-full">
              <img src="<?php echo $url; ?>/assets/images/<?php echo $settingd['logo']; ?>" alt="<?php echo $settingd['websitename']; ?>"
                style="max-height: 40px; width: auto; object-fit: contain; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.05));">
            </span>            
          </a>
        </div>

        <div class="menu-inner-shadow"></div>

        <ul class="menu-inner py-1" style="margin-top: 10px;">
          <!-- Dashboard -->
          <li class="menu-item <?php if ($request_url == 'dashboard' || $request_url == 'index.php') { echo 'active'; } ?>">
            <a href="./" class="menu-link">
              <i class="menu-icon tf-icons bx bx-grid-alt"></i>
              <div data-i18n="Analytics" style="font-weight: 600; letter-spacing: 0.3px;">Dashboard</div>
            </a>
          </li>
          
          <li class="menu-header small text-uppercase" style="margin-top: 10px;">
            <span class="menu-header-text" style="display: flex; align-items: center; gap: 8px;">
              <span style="width: 12px; height: 1px; background-color: #cbd5e1; display: inline-block;"></span> 
              Content
            </span>
          </li>

          <!-- Post Sections -->
          <li class="menu-item <?php if ($request_url == 'view-post.php' || $request_url == 'new-post.php' || $request_url == 'update-post.php') {
  echo 'active open';
} ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-news"></i>
              <div data-i18n="Layouts" style="font-weight: 600; letter-spacing: 0.3px;">Posts</div>
            </a>

            <ul class="menu-sub">
              <li class="menu-item <?php if ($request_url == 'view-post.php') { echo 'active'; } ?>">
                <a href="view-post.php" class="menu-link">
                  <div data-i18n="Without menu">View All</div>
                </a>
              </li>
              <li class="menu-item <?php if ($request_url == 'new-post.php') { echo 'active'; } ?>">
                <a href="new-post.php" class="menu-link">
                  <div data-i18n="Without navbar">Create New</div>
                </a>
              </li>              
            </ul>
          </li>

          <!-- Category Sections -->
          <li class="menu-item <?php if ($request_url == 'view-category.php' || $request_url == 'new-category.php' || $request_url == 'update-category.php') {
  echo 'active open';
} ?>">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-category-alt"></i>
              <div data-i18n="Extended UI" style="font-weight: 600; letter-spacing: 0.3px;">Categories</div>
            </a>
            <ul class="menu-sub ">
              <li class="menu-item <?php if ($request_url == 'view-category.php') { echo 'active'; } ?>">
                <a href="view-category.php" class="menu-link">
                  <div data-i18n="Perfect Scrollbar">View All</div>
                </a>
              </li>
              <?php if ($_SESSION['role'] == 1) { ?>
              <li class="menu-item <?php if ($request_url == 'new-category.php') { echo 'active'; } ?>">
                <a href="new-category.php" class="menu-link">
                  <div data-i18n="Perfect Scrollbar">Create New</div>
                </a>
              </li>
              <?php } ?>             
            </ul>
          </li>
          
          <!-- Users Sections -->
          <?php if ($_SESSION['role'] == 1 || $_SESSION['role'] == 2 || $_SESSION['role'] == 3) { ?>
          <li class="menu-header small text-uppercase" style="margin-top: 10px;">
            <span class="menu-header-text" style="display: flex; align-items: center; gap: 8px;">
              <span style="width: 12px; height: 1px; background-color: #cbd5e1; display: inline-block;"></span> 
              Administration
            </span>
          </li>
          <li class="menu-item <?php if ($request_url == 'view-user.php' || $request_url == 'new-user.php' || $request_url == 'update-user.php') {
    echo 'active open';
  } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-group"></i>
              <div data-i18n="Account Settings" style="font-weight: 600; letter-spacing: 0.3px;">Users</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item <?php if ($request_url == 'view-user.php') {
    echo 'active';
  } ?>">
                <a href="view-user.php" class="menu-link">
                  <div data-i18n="Account">View All</div>
                </a>
              </li>
              <li class="menu-item <?php if ($request_url == 'new-user.php') {
    echo 'active';
  } ?>">
                <a href="new-user.php" class="menu-link">
                  <div data-i18n="Account">Create New</div>
                </a>
              </li>           
            </ul>
          </li>
          <?php
}
if ($_SESSION['author_id'] == 1 && $_SESSION['role'] == 1) {
  ?>
          <li class="menu-header small text-uppercase" style="margin-top: 10px;">
            <span class="menu-header-text" style="display: flex; align-items: center; gap: 8px;">
              <span style="width: 12px; height: 1px; background-color: #cbd5e1; display: inline-block;"></span> 
              System Configuration
            </span>
          </li>

          <!-- Website Settings -->
          <li class="menu-item <?php if ($request_url == 'manage-website.php' || $request_url == 'view-visitor.php') {
    echo 'active open';
  } ?>">
            <a href="javascript:void(0)" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-cog"></i>
              <div data-i18n="User interface" style="font-weight: 600; letter-spacing: 0.3px;">Manage Website</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item <?php if ($request_url == 'manage-website.php') {
    echo 'active';
  } ?>">
                <a href="manage-website.php" class="menu-link">
                  <div data-i18n="Accordion">Web Settings</div>
                </a>
              </li>
              <li class="menu-item <?php if ($request_url == 'view-visitor.php') {
    echo 'active';
  } ?>">
                <a href="view-visitor.php" class="menu-link">
                  <div data-i18n="Accordion">Visitor Lists</div>
                </a>
              </li>
            </ul>
          </li>
          <li class="menu-item <?php if ($request_url == 'messages.php') {
    echo 'active open';
  } ?>">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
              <i class="menu-icon tf-icons bx bx-envelope-open"></i>
              <div data-i18n="Misc" style="font-weight: 600; letter-spacing: 0.3px;">Messages</div>
            </a>
            <ul class="menu-sub">
              <li class="menu-item <?php if ($request_url == 'messages.php') {
    echo 'active';
  } ?>">
                <a href="messages.php" class="menu-link">
                  <div data-i18n="Error">Inbox Messages</div>
                </a>
              </li>
            </ul>
          </li>
          <?php } ?>
          
          <!-- Help & Supports -->
          <li class="menu-header small text-uppercase" style="margin-top: 10px;">
            <span class="menu-header-text" style="display: flex; align-items: center; gap: 8px;">
              <span style="width: 12px; height: 1px; background-color: #cbd5e1; display: inline-block;"></span> 
              Help & Support
            </span>
          </li>
          <li class="menu-item <?php if ($request_url == 'technical-support.php') {
  echo 'active';
} ?>">
            <a href="technical-support.php" target="_blank" class="menu-link">
              <i class="menu-icon tf-icons bx bx-support"></i>
              <div data-i18n="Support" style="font-weight: 600; letter-spacing: 0.3px;">Technical Support</div>
            </a>
          </li>

        </ul>
      </aside>
      <!-- / Menu -->
      <!-- Layout container -->
      <div class="layout-page">
      