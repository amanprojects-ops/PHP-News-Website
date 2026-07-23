<?php
session_start();
include_once 'config.php';

// Users Login Process Code =======================================================
if (isset($_POST['loginBtn'])) {
    $logUsername = mysqli_real_escape_string($conn, trim($_POST['logUsername']));
    $logPassword = mysqli_real_escape_string($conn, md5(trim($_POST['logPassword'])));

    $login = "SELECT * FROM user WHERE username = '" . $logUsername . "' && password = '" . $logPassword . "'";
    $logResult = mysqli_query($conn, $login);
    if (mysqli_num_rows($logResult) > 0) {
        while ($logData = mysqli_fetch_assoc($logResult)) {
            $_SESSION['name'] = $logData['first_name'] . ' ' . $logData['last_name'];
            $_SESSION['username'] = $logData['username'];
            $_SESSION['phone'] = $logData['phone'];
            $_SESSION['email'] = $logData['email'];
            $_SESSION['role'] = $logData['role'];
            $_SESSION['author_id'] = $logData['user_id'];
            $_SESSION['userStatus'] = $logData['userStatus'];
            $_SESSION['success'] = 'You are successful logged in.';
            echo "<script>window.location.href='../dashboard.php';</script>";
        }
    } else {
        $_SESSION['warning'] = 'Username & Password are Can&apos;t Matched';
        echo "<script>window.location.href='../';</script>";
    }
}

// Post Rajection Codes =================================================
if (isset($_POST['postRajected'])) {
    $postid = mysqli_real_escape_string($conn, $_POST['postR']);
    $postEnId = mysqli_real_escape_string($conn, base64_encode($_POST['postC']));
    $rajectQ = "UPDATE post SET postStatus ='N' WHERE post_id='{$postid}'";

    if (mysqli_query($conn, $rajectQ)) {
        $_SESSION['success'] = 'Post Rajected Successful.';
        echo "<script>window.location.href='../view-post.php?catID={$postEnId}';</script>";
    } else {
        $_SESSION['warning'] = 'Post Raject Unsuccessful.';
        echo "<script>window.location.href='../view-post.php?catID={$postEnId}';</script>";
    }
}

// Post Approved Codes ======================================================
if (isset($_POST['postApproved'])) {
    $postid = mysqli_real_escape_string($conn, $_POST['postA']);
    $postEnId = mysqli_real_escape_string($conn, base64_encode($_POST['postC']));
    $postidkey = base64_encode($postid);
    // check exists file in dir
    $checkImg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT post_img FROM post WHERE post_id = '{$postid}'"));
    $checkFile = '../../assets/postImage/' . $checkImg['post_img'];

    if (file_exists($checkFile)) {
        $postApproveQ = "UPDATE post SET postStatus ='Y' WHERE post_id='{$postid}'";
        if (mysqli_query($conn, $postApproveQ)) {
            $_SESSION['success'] = 'Post Approved Successful.';
            echo "<script>window.location.href='../view-post.php?catID={$postEnId}';</script>";
        } else {
            $_SESSION['warning'] = 'Post Approved Unsuccessful.';
            echo "<script>window.location.href='../view-post.php?catID={$postEnId}';</script>";
        }
    } else {
        $_SESSION['warning'] = 'Post Image are Not Found. Reupload here.';
        echo "<script>window.location.href='../update-post.php?postid={$postidkey}';</script>";
    }
}

// Update Post Details Codes =======================================================

if (isset($_POST['updatePost'])) {
    // print_r($_POST);
    // die();

    $postKey = base64_encode(trim($_POST['postId']));
    if (empty($_FILES['newImage']['name'])) {
        $image_name = trim($_POST['oldImage']);
    } else {
        $errors = array();

        $file_name = $_FILES['newImage']['name'];
        $file_size = $_FILES['newImage']['size'];
        $file_tmp = $_FILES['newImage']['tmp_name'];
        $file_type = $_FILES['newImage']['type'];
        $file_ext = explode('.', $file_name);
        $fileActualExt = strtolower(end($file_ext));
        $extensions = array('jpeg', 'jpg', 'png', 'webp', 'avif');

        if (in_array($fileActualExt, $extensions) === false) {
            $errors[] = 'This extension file not allowed, Please choose a JPG or PNG file.';
        }
        if ($file_size > 2097152) {
            $errors[] = 'File Size Must Be 2MB Or Lower.';
        }
        $new_name = time() . '-' . basename($file_name);
        $target = '../../assets/postImage/' . $new_name;
        $image_name = $new_name;
        if (empty($errors) == true) {
            move_uploaded_file($file_tmp, $target);
            @unlink('../../assets/postImage/' . $_POST['oldImage']);
        } else {
            // print_r($errors);
            // $_SESSION['warning'] = "This Image File is not suppoted.";
            $_SESSION['warning'] = $errors[0];
            echo "<script>window.location.href='../update-post.php?postid={$postKey}';</script>";
            die();
        }
    }
    // $authorid = mysqli_real_escape_string($conn, trim($_POST["authorId"]));, `author`='{$authorid}'
    $postId = mysqli_real_escape_string($conn, trim($_POST['postId']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $shortDescription = mysqli_real_escape_string($conn, trim($_POST['postShortDesc']));
    $postCategory = mysqli_real_escape_string($conn, trim($_POST['newCategory']));
    $enCodeCategory = base64_encode(trim($_POST['newCategory']));
    $postTitle = mysqli_real_escape_string($conn, trim($_POST['post_title']));
    $oldCategory = mysqli_real_escape_string($conn, trim($_POST['oldCategory']));

    $sql = "UPDATE `post` SET postStatus = 'W',`title` = '{$postTitle}',`sort_details` = '{$shortDescription}',`description` = '{$description}' , `category` = {$postCategory}, `post_img` = '{$image_name}' WHERE `post_id` = '{$postId}'";

    $result = mysqli_query($conn, $sql);

    if ($result) {
        $_SESSION['success'] = 'Post Updated Successful.';
        echo "<script>window.location.href='../view-post.php?catID={$enCodeCategory}';</script>";
    } else {
        $_SESSION['error'] = 'Post Update Unsuccessful.';
        echo "<script>window.location.href='../view-post.php?catID={$enCodeCategory}';</script>";
    }
}

// Add New Posts Codes =====================================================================================

if (isset($_POST['saveNew_post'])) {
    $post_title = mysqli_real_escape_string($conn, trim($_POST['post_title']));
    $post_details = mysqli_real_escape_string($conn, trim($_POST['post_short_desc']));
    $post_category = mysqli_real_escape_string($conn, trim($_POST['post_category']));
    $description = mysqli_real_escape_string($conn, trim($_POST['description']));
    $author_id = mysqli_real_escape_string($conn, trim($_POST['author_id']));
    $date = date('d-m-Y');

    if (isset($_FILES['postImage'])) {
        $errors = array();

        $file_name = $_FILES['postImage']['name'];
        $file_size = $_FILES['postImage']['size'];
        $file_tmp = $_FILES['postImage']['tmp_name'];
        $file_type = $_FILES['postImage']['type'];
        $file_ext = explode('.', $file_name);
        $fileActualExt = strtolower(end($file_ext));
        $extensions = array('jpeg', 'jpg', 'png', 'webp', 'avif');

        if (in_array($fileActualExt, $extensions) === false) {
            $errors[] = 'This extension file not allowed, Please choose a JPG or PNG file.';
        }

        if ($file_size > 2097152) {
            $errors[] = 'File size must be 2mb or lower.';
        }

        if ($file_size > 2097152) {
            $errors[] = 'File size must be 2mb or lower.';
        }
        $new_name = time() . '-' . $file_name;
        $target = '../../assets/postImage/' . $new_name;
        if (empty($errors) == true) {
            move_uploaded_file($file_tmp, $target);
        } else {
            $_SESSION['warning'] = $errors[0];
            echo "<script>window.location.href='../new-post.php';</script>";
            die();
        }
    }
    $addpQuery = "INSERT INTO 
    `post`(`title`, `sort_details`, `description`, `post_img`, `category`, `author`, `post_date`) 
    VALUES ('{$post_title}','{$post_details}','{$description}','{$new_name}','{$post_category}','{$author_id}','{$date}')";

    if (mysqli_query($conn, $addpQuery)) {
        $_SESSION['success'] = 'New Post Save Successful.<strong> Wait for Approvel.</strong>';
        echo "<script>window.location.href='../new-post.php';</script>";
    } else {
        $_SESSION['error'] = 'New Post are not Save Successful.<strong> Please Try again.</strong>';
        echo "<script>window.location.href='../new-post.php';</script>";
    }
}

// Category Approved Codes ================================================

if (isset($_POST['categoryApproved'])) {
    $categoryid = mysqli_real_escape_string($conn, $_POST['categoryA']);
    $rajectQ = "UPDATE category SET categoryStatus ='Y' WHERE category_id='{$categoryid}'";
    if (mysqli_query($conn, $rajectQ)) {
        $_SESSION['success'] = 'Category Approved Successful.';
        echo "<script>window.location.href='../dashboard/view-category.php';</script>";
    } else {
        $_SESSION['success'] = 'Category Approved Unsuccessful.';
        echo "<script>window.location.href='../dashboard/view-category.php';</script>";
    }
}
// Category Rajected Codes ================================================

if (isset($_POST['categoryRajected'])) {
    $categoryid = mysqli_real_escape_string($conn, $_POST['categoryR']);
    $checkC1Q = "SELECT * FROM post WHERE category = '{$categoryid}'";
    $checkC1R = mysqli_query($conn, $checkC1Q);
    if (mysqli_num_rows($checkC1R) > 0) {
        $_SESSION['warning'] = 'This Category Use for Post..';
        echo "<script>window.location.href='../dashboard/view-category.php';</script>";
    } else {
        $rajectQ = "UPDATE category SET categoryStatus ='N' WHERE category_id='{$categoryid}'";
        if (mysqli_query($conn, $rajectQ)) {
            $_SESSION['success'] = 'Category Rajected Successful.';
            echo "<script>window.location.href='../dashboard/view-category.php';</script>";
        } else {
            $_SESSION['warning'] = 'Category Rajected Unsuccessful.';
            echo "<script>window.location.href='../dashboard/view-category.php';</script>";
        }
    }
}

// Category Update Codes =======================================================

if (isset($_POST['updateCategory'])) {
    $categoryName = mysqli_real_escape_string($conn, $_POST['categoryName']);
    $categoryTitle = mysqli_real_escape_string($conn, $_POST['categoryTitle']);
    $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);
    $update = "UPDATE category SET category_name='{$categoryName}',categoryTitle='{$categoryTitle}' WHERE category_id = '{$category_id}'";
    if (mysqli_query($conn, $update)) {
        $_SESSION['success'] = 'Category Updated Successful.';
        echo "<script>window.location.href='../dashboard/view-category.php';</script>";
    } else {
        $_SESSION['success'] = 'Category Updated Unsuccessful.';
        echo "<script>window.location.href='../dashboard/view-category.php';</script>";
    }
}

// Category Add Codes ===============================================

if (isset($_POST['addCategory'])) {
    $categoryName = mysqli_real_escape_string($conn, $_POST['categoryName']);
    $categoryTitle = mysqli_real_escape_string($conn, $_POST['categoryTitle']);

    $insert = "INSERT INTO category(category_name,categoryTitle,author)VALUES('{$categoryName}','{$categoryTitle}','{$_SESSION['author_id']}')";
    if (mysqli_query($conn, $insert)) {
        $_SESSION['success'] = 'Category Add Successful.';
        echo "<script>window.location.href='../dashboard/view-category.php';</script>";
    } else {
        $_SESSION['success'] = 'Category Add Unsuccessful.';
        echo "<script>window.location.href='../dashboard/view-category.php';</script>";
    }
}

// User Rajected Codes =================================================================

if (isset($_POST['userRajected'])) {
    $userid = mysqli_real_escape_string($conn, $_POST['userR']);

    $userQ = "UPDATE user SET userStatus = 'N' WHERE user_id = '{$userid}'";

    if (mysqli_query($conn, $userQ)) {
        $_SESSION['success'] = 'User Rajected Successful.';
        echo "<script>window.location.href='../dashboard/view-user.php';</script>";
    } else {
        $_SESSION['warning'] = 'User Rajected Unsuccessful.';
        echo "<script>window.location.href='../dashboard/view-user.php';</script>";
    }
}

// User Approved Codes =================================================================

if (isset($_POST['userApproved'])) {
    $userid = mysqli_real_escape_string($conn, $_POST['userA']);

    $userQ = "UPDATE user SET userStatus = 'Y' WHERE user_id = '{$userid}'";

    if (mysqli_query($conn, $userQ)) {
        $_SESSION['success'] = 'User Approved Successful.';
        echo "<script>window.location.href='../dashboard/view-user.php';</script>";
    } else {
        $_SESSION['error'] = 'User Approved Unsuccessful.';
        echo "<script>window.location.href='../dashboard/view-user.php';</script>";
    }
}

// Users Updates Codes =================================================================
if (isset($_POST['userUpdate'])) {
    // print_r($_POST);
    // die();
    $user_id = mysqli_real_escape_string($conn, trim($_POST['user_id']));
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $userMobile = mysqli_real_escape_string($conn, trim($_POST['userMobile']));
    $userEmail = mysqli_real_escape_string($conn, trim($_POST['userEmail']));
    $role = mysqli_real_escape_string($conn, trim($_POST['role']));

    $updateQ = "UPDATE `user` SET `first_name`='{$first_name}',`last_name`='{$last_name}',`phone`='{$userMobile}',`email`='{$userEmail}',`role`= {$role} WHERE user_id ='{$user_id}'";

    if (mysqli_query($conn, $updateQ)) {
        $_SESSION['success'] = 'User data Updated Successful.';
        echo "<script>window.location.href='../dashboard/view-user.php';</script>";
    } else {
        $_SESSION['error'] = 'User data Updated Unsuccessful.';
        echo "<script>window.location.href='../dashboard/view-user.php';</script>";
    }
}
// Add New User Codes ==================================================================
if (isset($_POST['addUser'])) {
    // echo "<pre>";
    // print_r($_POST);
    // echo "</pre>";
    // die();
    $user_id = mysqli_real_escape_string($conn, trim($_POST['user_id']));
    $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
    $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name']));
    $userMobile = mysqli_real_escape_string($conn, trim($_POST['userMobile']));
    $userEmail = mysqli_real_escape_string($conn, trim($_POST['userEmail']));
    $role = mysqli_real_escape_string($conn, trim($_POST['role']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = mysqli_real_escape_string($conn, trim(md5($_POST['password'])));

    $insertQ = "INSERT INTO user(first_name,last_name,phone,email,role,taken,username,password)VALUES('{$first_name}','{$last_name}','{$userMobile}','{$userEmail}',{$role},'{$user_id}','{$username}','{$password}')";

    if (mysqli_query($conn, $insertQ)) {
        $_SESSION['success'] = 'New User Created Successful.';
        echo "<script>window.location.href='../dashboard/view-user.php';</script>";
    } else {
        $_SESSION['error'] = 'New User Create Unsuccessful.';
        echo "<script>window.location.href='../dashboard/view-user.php';</script>";
    }
}

// Update user Setting code ============================================================
if (isset($_POST['userProfile'])) {
    $userid = mysqli_real_escape_string($conn, trim($_POST['userid']));
    $name = explode(' ', $_POST['userFull']);
    $first_name = mysqli_real_escape_string($conn, trim($name['0']));
    $last_name = mysqli_real_escape_string($conn, trim($name['1']));
    $userEmail = mysqli_real_escape_string($conn, trim($_POST['userEmail']));
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $userMobile = mysqli_real_escape_string($conn, trim($_POST['userMobile']));

    $updateQ = "UPDATE `user` SET `username`='{$username}',`first_name`='{$first_name}',`last_name`='{$last_name}',`phone`='{$userMobile}',`email`='{$userEmail}' WHERE user_id = '{$userid}'";
    if (mysqli_query($conn, $updateQ)) {
        $_SESSION['success'] = 'User Profile Updated Successful.';
        echo "<script>window.location.href='../dashboard/setting.php';</script>";
    } else {
        $_SESSION['error'] = 'User Profile Updated Unsuccessful.';
        echo "<script>window.location.href='../dashboard/setting.php';</script>";
    }
}

// Settings Updation Codes =============================================================
if (isset($_POST['settingUpdate'])) {
    $websiteName = mysqli_real_escape_string($conn, trim($_POST['webName']));
    $webFooter = mysqli_real_escape_string($conn, trim($_POST['webFooter']));
    $webEmail = mysqli_real_escape_string($conn, trim($_POST['webEmail']));
    $webKeyword = mysqli_real_escape_string($conn, trim($_POST['webKeyword']));

    $settingQ = "UPDATE `settings` SET `websitename`='{$websiteName}',`footerdesc`='{$webFooter}',`keywords`='{$webKeyword}',`workEmail`='{$webEmail}'";

    if (mysqli_query($conn, $settingQ)) {
        $_SESSION['success'] = 'Website Settings Updated Successful.';
        echo "<script>window.location.href='../dashboard/manage-website.php';</script>";
    } else {
        $_SESSION['error'] = 'Website Settings not Updated Successful.';
        echo "<script>window.location.href='../dashboard/manage-website.php';</script>";
    }
}

// Settings Logo Updation Codes =========================================================
if (isset($_POST['webLogobtn'])) {
    if (isset($_FILES['webLogo']['name'])) {
        $errors = array();

        $file_name = $_FILES['webLogo']['name'];
        $file_size = $_FILES['webLogo']['size'];
        $file_tmp = $_FILES['webLogo']['tmp_name'];
        $file_type = $_FILES['webLogo']['type'];
        $file_ext = explode('.', $file_name);
        $fileActualExt = strtolower(end($file_ext));
        $extensions = array('jpeg', 'jpg', 'png', 'webp', 'avif', 'gif', 'svg');

        if (in_array($fileActualExt, $extensions) === false) {
            $errors[] = 'This extension file not allowed, Please choose a JPG or PNG file.';
        }

        if ($file_size > 2097152) {
            $errors[] = 'File size must be 2mb or lower.';
        }

        if ($file_size > 2097152) {
            $errors[] = 'File size must be 2mb or lower.';
        }
        $webLogo = time() . '-' . basename($file_name);
        $target = '../../assets/images/' . $webLogo;
        if (empty($errors) == true) {
            move_uploaded_file($file_tmp, $target);
        } else {
            $_SESSION['error'] = 'Connection Time Out Please Try again.';
            die();
        }

        $settingQ = "UPDATE `settings` SET `logo`='{$webLogo}'";

        if (mysqli_query($conn, $settingQ)) {
            $_SESSION['success'] = 'Website Logo Updated Successful.';
            echo "<script>window.location.href='../dashboard/manage-website.php';</script>";
        } else {
            $_SESSION['error'] = 'Website Logo not Updated Successful.';
            echo "<script>window.location.href='../dashboard/manage-website.php';</script>";
        }
    }
}

// Settings Favicon Updation Codes =============================================================
if (isset($_POST['webfaviconbtn'])) {
    // print_r($_FILES);
    // die();
    if (isset($_FILES['webfavicon']['name'])) {
        $errors = array();

        $file_name = $_FILES['webfavicon']['name'];
        $file_size = $_FILES['webfavicon']['size'];
        $file_tmp = $_FILES['webfavicon']['tmp_name'];
        $file_type = $_FILES['webfavicon']['type'];
        $file_ext = explode('.', $file_name);
        $fileActualExt = strtolower(end($file_ext));
        $extensions = array('jpeg', 'jpg', 'png', 'webp', 'avif', 'gif', 'svg');

        if (in_array($fileActualExt, $extensions) === false) {
            $errors[] = 'This extension file not allowed, Please choose a JPG or PNG file.';
        }

        if ($file_size > 2097152) {
            $errors[] = 'File size must be 2mb or lower.';
        }

        if ($file_size > 2097152) {
            $errors[] = 'File size must be 2mb or lower.';
        }
        $webfavicon = time() . '-' . basename($file_name);
        $target = '../../assets/images/' . $webfavicon;
        if (empty($errors) == true) {
            move_uploaded_file($file_tmp, $target);
        } else {
            $_SESSION['error'] = 'Connection Time Out Please Try again.';
            die();
        }

        $settingQ = "UPDATE `settings` SET `favicon`='{$webfavicon}'";

        if (mysqli_query($conn, $settingQ)) {
            $_SESSION['success'] = 'Website favicon Updated Successful.';
            echo "<script>window.location.href='../dashboard/manage-website.php';</script>";
        } else {
            $_SESSION['error'] = 'Website favicon not Updated Successful.';
            echo "<script>window.location.href='../dashboard/manage-website.php';</script>";
        }
    }
}

// Settings Watter Mark Updation Codes =========================================================
if (isset($_POST['webwattermarkbtn'])) {
    // print_r($_FILES);
    if (isset($_FILES['webwatterMark']['name'])) {
        $errors = array();

        $file_name = $_FILES['webwatterMark']['name'];
        $file_size = $_FILES['webwatterMark']['size'];
        $file_tmp = $_FILES['webwatterMark']['tmp_name'];
        $file_type = $_FILES['webwatterMark']['type'];
        $file_ext = explode('.', $file_name);
        $fileActualExt = strtolower(end($file_ext));
        $extensions = array('jpeg', 'jpg', 'png', 'webp', 'avif', 'gif', 'svg');

        if (in_array($fileActualExt, $extensions) === false) {
            $errors[] = 'This extension file not allowed, Please choose a JPG or PNG file.';
        }

        if ($file_size > 2097152) {
            $errors[] = 'File size must be 2mb or lower.';
        }

        if ($file_size > 2097152) {
            $errors[] = 'File size must be 2mb or lower.';
        }
        $webwatterMark = time() . '-' . basename($file_name);
        $target = '../../assets/images/' . $webwatterMark;
        if (empty($errors) == true) {
            move_uploaded_file($file_tmp, $target);
        } else {
            $_SESSION['error'] = $errors[0];
            die();
        }

        $settingQ = "UPDATE `settings` SET `watterMark`='{$webwatterMark}'";

        if (mysqli_query($conn, $settingQ)) {
            $_SESSION['success'] = 'Website WatterMark Updated Successful.';
            echo "<script>window.location.href='../dashboard/manage-website.php';</script>";
        } else {
            $_SESSION['error'] = 'Website Watter Mark not Updated Successful.';
            echo "<script>window.location.href='../dashboard/manage-website.php';</script>";
        }
    }
}

// Change Password Code =======================================================================
if (isset($_POST['changeUserPassword'])) {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $mobile = mysqli_real_escape_string($conn, trim($_POST['mobile']));
    $password = mysqli_real_escape_string($conn, trim(md5($_POST['userPassword'])));

    $runQ = "UPDATE `user` SET `password` = '{$password}' WHERE username = '{$username}' && phone = '{$mobile}'";

    if (mysqli_query($conn, $runQ)) {
        $_SESSION['success'] = 'Passwrod Changed Successful.';
        echo "<script>window.location.href='../';</script>";
    }
}
// Error Redrict Page
else {
    // $_SESSION['warning'] = "Request Time Out. Try Some after time.";
    echo "<script>window.location.href='../dashboard/404.php';</script>";
}

// Connection Close ==========================================================================
mysqli_close($conn);
