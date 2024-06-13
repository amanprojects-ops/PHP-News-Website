<?php
include "config.php";
if(empty($baseurl) != 0){
    echo "<script>window.location.href='404.php';</script>";
}
$page = basename($_SERVER['PHP_SELF']);
switch ($page) {
  case "single.php":
    $single = mysqli_real_escape_string($conn, base64_decode($_GET['id']));
    if (isset($_GET['id'])) {
      $sql_title = "SELECT * FROM post WHERE post_id = '{$single}'";
      $result_title = mysqli_query($conn, $sql_title);
      $row_title = mysqli_fetch_assoc($result_title);
      $post_share = $page."?id=".$_GET['id'];
      $post_img = $row_title['post_img'];
      $page_description = $row_title['title'];
      $page_title = @$row_title['title'];
    } else {
      $page_title = "All Category";
    }
    break;
  case "category.php":
    @$category = mysqli_real_escape_string($conn, base64_decode($_GET['cid']));
    // base64_decode()
    if (isset($_GET['cid'])) {
      $sql_title = "SELECT * FROM category WHERE category_id = '{$category}'";
      $result_title = mysqli_query($conn, $sql_title);
      $row_title = mysqli_fetch_assoc($result_title);
      $page_title = @$row_title['category_name']." | ". @$row_title['categoryTitle'];
    } else {
      $page_title = "All Category";
    }
    break;
  case "author.php":
    @$author = mysqli_real_escape_string($conn, base64_decode($_GET['aid']));
    if (isset($_GET['aid'])) {
      $sql_title = "SELECT * FROM user WHERE user_id = '{$author}'";
      $result_title = mysqli_query($conn, $sql_title);
      $row_title = mysqli_fetch_assoc($result_title);
      $page_title = "News By " . @$row_title['first_name'] . " " . @$row_title['last_name'];
    } else {
      $page_title = "No Post Found";
    }
    break;
  case "search.php":
    @$search = strtoupper($_GET['search']);
    if (isset($_GET['search'])) {

      $page_title = $search;
    } else {
      $page_title = "No Search Result Found";
    }
    break;
  default:
    $sql_title = "SELECT websitename FROM settings";
    $result_title = mysqli_query($conn, $sql_title);
    $row_title = mysqli_fetch_assoc($result_title);
    $page_title = $row_title['websitename'];
    break;
}
// $baseurl = "https://connectbihar.in";
//echo $baseurl;
$auth = basename($_SERVER['SCRIPT_NAME']);

$settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings"));
// echo ("<pre>");
// print_r(basename($_SERVER['SCRIPT_NAME']));  
// echo ("</pre>");
// die();

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="description" content="<?php echo @$page_title; ?>">
  <meta name="keywords" content="<?php echo @$settings['keywords']; ?>">
  <?php
  if ($auth == 'author.php') {
    echo "<meta name='author' content='$page_title'>";
  } ?>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="index, follow">
  <meta http-equiv="refresh" content="60">
  <meta charset="UTF-8">
  <meta property="og:title" content="<?php echo @$page_title; ?>">
  <meta property="og:description" content="<?php echo @$page_title; ?>">
  <link rel="canonical" href="<?php if($page == 'single.php'){echo @$settings['websiteUrl']."/".$post_share;}else{echo @$settings['websiteUrl'];} ?>" />
  <link rel="shortcut icon"  href="<?php echo @$settings['websiteUrl']."/assets/images/".$settings['favicon']; ?>"/>
  <meta name="google-adsense-account" content="ca-pub-8896362105504152">
  
  <meta name="rating" content="general" />
  <meta http-equiv="content-language" content="en" />
  <meta name="distribution" content="global" />
  <meta name="robots" content="index, follow" />
  <meta http-equiv="Permissions-Policy" content="interest-cohort=()">
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="<?php echo @$settings['websiteUrl']; ?>">
  <meta property="og:title" content="<?php echo @$page_title; ?>">
  <meta property="og:description" content="<?php if($page == 'single.php'){echo @$page_description;}else{echo @$settings['websiteTitle'];}?>">
  <meta property="og:image" content="<?php if($page == 'single.php'){echo @$settings['websiteUrl']."/assets/postImage/".$post_img;}else{echo @$settings['websiteUrl']."/assets/images/".$settings['logo'];}?>">

  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="<?php echo @$settings['websiteUrl']; ?>">
  <meta property="twitter:title" content="<?php echo @$page_title; ?>">
  <meta property="twitter:description" content="<?php if($page == 'single.php'){echo @$page_description;}else{echo @$settings['websiteTitle'];}?>">
  <meta property="twitter:image" content="<?php if($page == 'single.php'){echo @$settings['websiteUrl']."/assets/postImage/".$post_img;}else{echo @$settings['websiteUrl']."/assets/images/".$settings['logo'];}?>">


  <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
  <title><?php echo $page_title; ?></title>
  <link rel="shortcut icon" href="<?php echo $baseurl; ?>/assets/images/<?php echo $settings['favicon'];?>" type="image/x-icon">
  <!-- Bootstrap -->
  <link rel="stylesheet" href="<?php echo $baseurl; ?>/css/bootstrap.min.css" />
  <!-- Font Awesome Icon -->
  <link rel="stylesheet" href="<?php echo $baseurl; ?>/css/font-awesome.css">
  <!-- Custom stlylesheet -->
  <link rel="stylesheet" href="<?php echo $baseurl; ?>/css/style.css">
  <link rel="preload" as="font" href="font.woff2" type="font/woff2" crossorigin="anonymous">
  <!--// Canonical URL-->
<link rel="canonical" href="https://example.com/correct-url" />

</head>

<body>
    <?php
    
    // Get the visitor's IP address
    $ipAddress = $_SERVER['REMOTE_ADDR'];
    
    // Check if the visitor is unique
     $visit = "SELECT COUNT(*) AS count FROM visitors WHERE ip = '$ipAddress'";
     $visitR = mysqli_query($conn,$visit);
     $visitData = mysqli_fetch_assoc($visitR);
     $count = $visitData['count'];
    
     if ($count == 0) {
        // Insert the unique visitor's IP address into the database
        $insertI = "INSERT INTO `visitors`(`ip`) VALUES ('{$ipAddress}')";
        mysqli_query($conn,$insertI) or die("Sorry");
        
     }
    
    // Get the total count of unique visitors
    // $total = mysqli_query($conn,"SELECT COUNT(*) AS total FROM visitors");
    // echo "<pre>";
    // print_r($total);
    // echo "</pre>";
        
    ?>
    
  <style>
    .custom-counter {
      position: absolute;
      left: 0;
      top: 30%;
    }
  </style>

  <!-- HEADER -->
  <div id="header">
    <!-- container -->
    <div class="container">
      <!-- row -->
      <div class="row justify-content-center">
        <!-- LOGO -->
        <div class=" col-md-offset-4 col-md-4"><a href="./" id="logo"><img src="./assets/images/<?php echo $settings['logo'] ?>" alt="<?php echo $settings['websitename'] ?>"loading="lazy"></a></div>
        <!-- /LOGO -->
      </div>
    </div>
  </div>
  <!-- /HEADER -->
  <!-- Menu Bar -->
  <div id="menu-bar">
    <div class="container">
      <div class="row">
        <div class="col-md-12">
          <?php
          include "config.php";

          if (isset($_GET['cid'])) {
            $cat_id = base64_decode($_GET['cid']);
          }

          $sql = "SELECT DISTINCT category.category_id,category.category_name,post.category,post.postStatus FROM `category` LEFT JOIN post ON post.category = category.category_id WHERE post.postStatus = 'Y'";
          $result = mysqli_query($conn, $sql);

          // $postCountQ = "SELECT COUNT(*) AS activePost FROM post WHERE postStatus = 'Y'";
          // $postCountR = mysqli_query($conn, $postCountQ);
          // $activePosts = mysqli_fetch_all($postCountR, MYSQLI_ASSOC);
          // echo ("<pre>");
          // print_r($activePosts[0]['activePost']);  
          // echo ("</pre>");

          ?>
          <ul class='menu'>
            <li><a class='<?php if ($auth == 'index.php') {
                            echo "active";
                          } else {
                            echo "";
                          } ?>' href='./'>Home</a></li>
            <?php if (mysqli_num_rows($result) > 0) {
              $active = "";
              while ($row = mysqli_fetch_assoc($result)) {
                if (isset($_GET['cid'])) {
                  if ($row['category_id'] == $cat_id) {
                    $active = "active";
                  } else {
                    $active = "";
                  }
                }
                $caten = base64_encode($row['category_id']);
                echo "<li><a class='{$active}' href='category.php?cid={$caten}'>{$row['category_name']}</a></li>";
              } ?>
          </ul>
        <?php } ?>
        </div>
      </div>
    </div>
  </div>
  <!-- /Menu Bar -->