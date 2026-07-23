<?php
include 'config.php';
include_once ('./database/functions.php');
if (empty($baseurl)) {
  redirect('./404.php');
}

// Fetch settings first to reuse across the page, avoiding duplicate queries
$settings_query = mysqli_query($conn, 'SELECT * FROM settings');
$settings = mysqli_fetch_assoc($settings_query) ?: [];

$page = basename($_SERVER['PHP_SELF']);
$auth = basename($_SERVER['SCRIPT_NAME']);

// Initialize default variables for meta tags to prevent undefined variable notices
$page_title = '';
$page_description = '';
$post_img = '';
$post_share = '';

switch ($page) {
  case 'single.php':
    if (isset($_GET['id'])) {
      $single = mysqli_real_escape_string($conn, base64_decode($_GET['id']));
      $sql_title = "SELECT * FROM post WHERE post_id = '{$single}'";
      $result_title = mysqli_query($conn, $sql_title);
      if ($result_title && $row_title = mysqli_fetch_assoc($result_title)) {
        $post_share = $page . '?id=' . urlencode($_GET['id']);
        $post_img = $row_title['post_img'] ?? '';
        $page_description = $row_title['title'] ?? '';
        $page_title = $row_title['title'] ?? '';
      }
    } else {
      $page_title = 'All Category';
    }
    break;

  case 'category.php':
    if (isset($_GET['cid'])) {
      $category = mysqli_real_escape_string($conn, base64_decode($_GET['cid']));
      $sql_title = "SELECT * FROM category WHERE category_id = '{$category}'";
      $result_title = mysqli_query($conn, $sql_title);
      if ($result_title && $row_title = mysqli_fetch_assoc($result_title)) {
        $category_name = $row_title['category_name'] ?? '';
        $category_title = $row_title['categoryTitle'] ?? '';
        $page_title = trim($category_name . ' | ' . $category_title);
      }
    } else {
      $page_title = 'All Category';
    }
    break;

  case 'author.php':
    if (isset($_GET['aid'])) {
      $author = mysqli_real_escape_string($conn, base64_decode($_GET['aid']));
      $sql_title = "SELECT * FROM user WHERE user_id = '{$author}'";
      $result_title = mysqli_query($conn, $sql_title);
      if ($result_title && $row_title = mysqli_fetch_assoc($result_title)) {
        $first_name = $row_title['first_name'] ?? '';
        $last_name = $row_title['last_name'] ?? '';
        $page_title = 'News By ' . trim($first_name . ' ' . $last_name);
      }
    } else {
      $page_title = 'No Post Found | ' . ($settings['websitename'] ?? '');
    }
    break;

  case 'search.php':
    if (isset($_GET['search'])) {
      $page_title = strtoupper($_GET['search']);
    } else {
      $page_title = 'No Search Result Found | ' . ($settings['websitename'] ?? '');
    }
    break;

  default:
    $page_title = $settings['websitename'] ?? '';
    break;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Character encoding and Viewport (Must be first for rendering) -->
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  
  <title><?php echo htmlspecialchars($page_title); ?></title>

  <!-- SEO Meta Tags -->
  <meta name="description" content="<?php echo htmlspecialchars($page_description ?: $page_title); ?>">
  <meta name="keywords" content="<?php echo htmlspecialchars($settings['keywords'] ?? ''); ?>">
  <?php if ($auth === 'author.php'): ?>
    <meta name="author" content="<?php echo htmlspecialchars($page_title); ?>">
  <?php endif; ?>
  <meta name="robots" content="index, follow">
  <meta name="rating" content="general">
  <meta name="distribution" content="global">
  <meta http-equiv="content-language" content="en">
  <meta http-equiv="Permissions-Policy" content="interest-cohort=()">
  <meta name="google-adsense-account" content="ca-pub-8896362105504152">

  <!-- Canonical URL -->
  <link rel="canonical" href="<?php echo htmlspecialchars(($page === 'single.php') ? ($settings['websiteUrl'] . '/' . $post_share) : $settings['websiteUrl']); ?>">

  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo htmlspecialchars($settings['websiteUrl'] ?? ''); ?>">
  <meta property="og:title" content="<?php echo htmlspecialchars($page_title); ?>">
  <meta property="og:description" content="<?php echo htmlspecialchars(($page === 'single.php') ? $page_description : ($settings['websiteTitle'] ?? '')); ?>">
  <meta property="og:image" content="<?php echo htmlspecialchars(($page === 'single.php') ? ($settings['websiteUrl'] . '/assets/postImage/' . $post_img) : ($settings['websiteUrl'] . '/assets/images/' . $settings['logo'])); ?>">

  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="<?php echo htmlspecialchars($settings['websiteUrl'] ?? ''); ?>">
  <meta property="twitter:title" content="<?php echo htmlspecialchars($page_title); ?>">
  <meta property="twitter:description" content="<?php echo htmlspecialchars(($page === 'single.php') ? $page_description : ($settings['websiteTitle'] ?? '')); ?>">
  <meta property="twitter:image" content="<?php echo htmlspecialchars(($page === 'single.php') ? ($settings['websiteUrl'] . '/assets/postImage/' . $post_img) : ($settings['websiteUrl'] . '/assets/images/' . $settings['logo'])); ?>">

  <!-- Favicon -->
  <link rel="shortcut icon" href="<?php echo htmlspecialchars($baseurl . '/assets/images/' . ($settings['favicon'] ?? '')); ?>" type="image/x-icon">

  <!-- Stylesheets -->
  <link rel="stylesheet" href="<?php echo htmlspecialchars($baseurl); ?>/assets/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="<?php echo htmlspecialchars($baseurl); ?>/assets/css/style.css">
  <link rel="preload" as="font" href="font.woff2" type="font/woff2" crossorigin="anonymous">

  <!-- Modern Header Styling -->
  <style>
    /* Google Fonts Import for modern typography */
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap');

    :root {
      --primary-color: #1e90ff;
      --primary-hover: #0077e6;
      --dark-bg: #0f172a;
      --light-bg: #f8fafc;
      --text-main: #1e293b;
      --text-muted: #64748b;
      --border-color: #e2e8f0;
    }

    body {
      font-family: 'Plus Jakarta Sans', sans-serif !important;
    }

    /* Top Bar Styling */
    .top-bar {
      background: var(--dark-bg);
      color: #94a3b8;
      font-size: 13px;
      padding: 10px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
      font-weight: 500;
    }

    .top-bar-inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .top-bar-left {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .top-bar-date {
      display: flex;
      align-items: center;
      gap: 6px;
      font-size: 12px;
      color: #94a3b8;
    }

    .top-bar-date i {
      color: var(--primary-color);
    }

    .trending-ticker {
      display: flex;
      align-items: center;
      gap: 8px;
      overflow: hidden;
      max-width: 500px;
    }

    .trending-badge {
      background: var(--primary-color);
      color: white;
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      padding: 2px 8px;
      border-radius: 4px;
      letter-spacing: 0.5px;
      animation: pulse 2s infinite;
      white-space: nowrap;
    }

    .trending-ticker-text {
      white-space: nowrap;
      text-overflow: ellipsis;
      overflow: hidden;
      color: #e2e8f0;
      text-decoration: none;
      transition: color 0.2s;
      font-size: 12px;
    }

    .trending-ticker-text:hover {
      color: var(--primary-color);
      text-decoration: none;
    }

    .top-bar-right {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .top-bar-social a {
      color: #94a3b8;
      transition: color 0.2s, transform 0.2s;
      display: inline-block;
      margin-left: 10px;
    }

    .top-bar-social a:hover {
      color: var(--primary-color);
      transform: translateY(-1px);
    }

    /* Main Branding Header Row */
    .main-header {
      background: #ffffff;
      padding: 20px 0;
      border-bottom: 1px solid var(--border-color);
    }

    .main-header-inner {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .logo-container {
      max-width: 220px;
      display: inline-block;
    }

    .logo-container img {
      width: 100%;
      height: auto;
      transition: transform 0.3s ease;
    }

    .logo-container:hover img {
      transform: scale(1.03);
    }

    /* Modern Search Bar */
    .header-search {
      position: relative;
      max-width: 320px;
      width: 100%;
    }

    .header-search form {
      position: relative;
      display: flex;
      align-items: center;
      width: 100%;
      margin: 0;
    }

    .header-search input {
      width: 100%;
      padding: 10px 45px 10px 20px;
      border-radius: 30px;
      border: 1px solid var(--border-color);
      font-size: 14px;
      background: var(--light-bg);
      color: var(--text-main);
      font-weight: 500;
      transition: all 0.3s ease;
      outline: none;
    }

    .header-search input:focus {
      border-color: var(--primary-color);
      background: #ffffff;
      box-shadow: 0 0 0 4px rgba(30, 144, 255, 0.1);
    }

    .header-search button {
      position: absolute;
      right: 5px;
      background: var(--primary-color);
      color: white;
      border: none;
      width: 34px;
      height: 34px;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      transition: background-color 0.2s, transform 0.2s;
    }

    .header-search button:hover {
      background: var(--primary-hover);
      transform: scale(1.05);
    }

    /* Sticky Navbar Styling */
    .modern-navbar {
      background: #ffffff;
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
      position: sticky;
      top: 0;
      z-index: 1000;
      border-bottom: 1px solid var(--border-color);
    }

    .navbar-wrapper {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }

    .menu-toggle {
      display: none;
      background: none;
      border: none;
      font-size: 20px;
      color: var(--text-main);
      padding: 15px 20px;
      cursor: pointer;
      outline: none;
      transition: color 0.3s;
    }

    .menu-toggle:hover {
      color: var(--primary-color);
    }

    .modern-navbar .menu {
      display: flex;
      list-style: none;
      margin: 0;
      padding: 0;
      gap: 5px;
      flex-wrap: wrap;
      width: 100%;
    }

    .modern-navbar .menu > li {
      display: inline-block !important;
    }

    .modern-navbar .menu > li > a {
      display: block !important;
      padding: 18px 20px !important;
      color: var(--text-main) !important;
      font-size: 13px !important;
      font-weight: 700 !important;
      text-transform: uppercase !important;
      letter-spacing: 0.5px !important;
      position: relative !important;
      transition: color 0.3s ease !important;
      text-decoration: none !important;
      background: transparent !important;
    }

    .modern-navbar .menu > li > a::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 20px;
      right: 20px;
      height: 3px;
      background-color: var(--primary-color);
      transform: scaleX(0);
      transform-origin: bottom right;
      transition: transform 0.3s ease;
      border-radius: 3px 3px 0 0;
    }

    .modern-navbar .menu > li > a:hover {
      color: var(--primary-color) !important;
    }

    .modern-navbar .menu > li > a:hover::after {
      transform: scaleX(1);
      transform-origin: bottom left;
    }

    /* Active State */
    .modern-navbar .menu > li > a.active {
      color: var(--primary-color) !important;
    }

    .modern-navbar .menu > li > a.active::after {
      transform: scaleX(1);
    }

    @keyframes pulse {
      0% {
        box-shadow: 0 0 0 0 rgba(30, 144, 255, 0.4);
      }
      70% {
        box-shadow: 0 0 0 6px rgba(30, 144, 255, 0);
      }
      100% {
        box-shadow: 0 0 0 0 rgba(30, 144, 255, 0);
      }
    }

    @media (max-width: 768px) {
      .top-bar-inner, .main-header-inner {
        flex-direction: column;
        gap: 12px;
        text-align: center;
      }
      
      .trending-ticker {
        max-width: 100%;
      }

      .header-search {
        max-width: 100%;
      }

      .navbar-wrapper {
        flex-direction: column;
        align-items: stretch;
      }

      .menu-toggle {
        display: block;
        align-self: flex-start;
      }

      .modern-navbar .menu {
        display: none;
        flex-direction: column;
        width: 100%;
        gap: 0;
        border-top: 1px solid var(--border-color);
        padding-bottom: 10px;
      }

      .modern-navbar .menu.show {
        display: flex;
      }

      .modern-navbar .menu > li {
        width: 100%;
        text-align: left;
      }

      .modern-navbar .menu > li > a {
        padding: 12px 20px !important;
        border-bottom: 1px solid var(--light-bg);
        width: 100%;
      }

      .modern-navbar .menu > li > a::after {
        display: none !important;
      }
    }
  </style>
</head>

<body>
  <?php
  // Get and escape the visitor's IP address for security
  $ipAddress = mysqli_real_escape_string($conn, $_SERVER['REMOTE_ADDR']);

  // Check if the visitor is unique
  $visit_query = mysqli_query($conn, "SELECT COUNT(*) AS count FROM visitors WHERE ip = '$ipAddress'");
  $visit_data = mysqli_fetch_assoc($visit_query);
  $count = $visit_data['count'] ?? 0;

  if ($count == 0) {
    // Insert the unique visitor's IP address into the database
    $insert_query = "INSERT INTO visitors (ip) VALUES ('$ipAddress')";
    mysqli_query($conn, $insert_query) or die('Database Error: Sorry');
  }

  // Fetch the latest post for the trending ticker
  $latest_post_query = mysqli_query($conn, "SELECT post_id, title FROM post WHERE postStatus = 'Y' ORDER BY post_id DESC LIMIT 1");
  $latest_post = $latest_post_query ? mysqli_fetch_assoc($latest_post_query) : null;
  ?>

  <!-- MODERN TOP BAR -->
  <div class="top-bar">
    <div class="container">
      <div class="top-bar-inner">
        <div class="top-bar-left">
          <div class="top-bar-date">
            <i class="fa fa-calendar"></i>
            <span><?php echo date("l, F d, Y"); ?></span>
          </div>
          <?php if ($latest_post): ?>
            <div class="trending-ticker">
              <span class="trending-badge">Trending</span>
              <a href="single.php?id=<?php echo base64_encode($latest_post['post_id']); ?>" class="trending-ticker-text">
                <?php echo htmlspecialchars($latest_post['title']); ?>
              </a>
            </div>
          <?php endif; ?>
        </div>
        <div class="top-bar-right">
          <div class="top-bar-social">
            <a href="#"><i class="fa fa-facebook"></i></a>
            <a href="#"><i class="fa fa-twitter"></i></a>
            <a href="#"><i class="fa fa-instagram"></i></a>
            <a href="#"><i class="fa fa-linkedin"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- MAIN HEADER / BRANDING -->
  <div class="main-header">
    <div class="container">
      <div class="main-header-inner">
        <!-- LOGO -->
        <a href="./" class="logo-container">
          <img src="./assets/images/<?php echo htmlspecialchars($settings['logo'] ?? ''); ?>" alt="<?php echo htmlspecialchars($settings['websitename'] ?? ''); ?>" loading="lazy">
        </a>

        <!-- SEARCH BAR -->
        <div class="header-search">
          <form action="search.php" method="GET">
            <input type="text" name="search" placeholder="Search news..." required>
            <button type="submit">
              <i class="fa fa-search"></i>
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- STICKY NAVBAR -->
  <div class="modern-navbar">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <div class="navbar-wrapper">
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
              <i class="fa fa-bars"></i> Menu
            </button>
            <?php
            $cat_id = isset($_GET['cid']) ? base64_decode($_GET['cid']) : null;

            $menu_sql = "SELECT DISTINCT category.category_id, category.category_name, post.category, post.postStatus 
                         FROM category 
                         LEFT JOIN post ON post.category = category.category_id 
                         WHERE post.postStatus = 'Y'";
            $menu_result = mysqli_query($conn, $menu_sql);
            ?>
            <ul class="menu" id="navMenu">
              <li>
                <a class="<?php echo ($auth === 'index.php') ? 'active' : ''; ?>" href="./">Home</a>
              </li>
              <?php
              if ($menu_result && mysqli_num_rows($menu_result) > 0) {
                while ($row = mysqli_fetch_assoc($menu_result)) {
                  $active = ($cat_id !== null && $row['category_id'] == $cat_id) ? 'active' : '';
                  $encoded_cid = base64_encode($row['category_id']);
                  $category_name = htmlspecialchars($row['category_name'] ?? '');
                  echo "<li><a class='{$active}' href='category.php?cid={$encoded_cid}'>{$category_name}</a></li>";
                }
              }
              ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const menuToggle = document.getElementById('menuToggle');
      const navMenu = document.getElementById('navMenu');
      
      if (menuToggle && navMenu) {
        menuToggle.addEventListener('click', function() {
          navMenu.classList.toggle('show');
          
          // Toggle icon between bars and times
          const icon = menuToggle.querySelector('i');
          if (icon) {
            if (navMenu.classList.contains('show')) {
              icon.classList.remove('fa-bars');
              icon.classList.add('fa-times');
            } else {
              icon.classList.remove('fa-times');
              icon.classList.add('fa-bars');
            }
          }
        });
      }
    });
  </script>