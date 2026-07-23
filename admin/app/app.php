<?php
session_start();
include_once 'config.php';

// Reusable File Upload Function
function handleUpload($fileKey, $targetDir, $allowedExts = ['jpeg', 'jpg', 'png', 'webp', 'avif', 'gif', 'svg'], $maxSize = 2097152)
{
    if (!isset($_FILES[$fileKey]) || empty($_FILES[$fileKey]['name'])) {
        return ['error' => 'No file uploaded.'];
    }

    $file_name = $_FILES[$fileKey]['name'];
    $file_size = $_FILES[$fileKey]['size'];
    $file_tmp = $_FILES[$fileKey]['tmp_name'];
    $file_ext = explode('.', $file_name);
    $fileActualExt = strtolower(end($file_ext));

    if (!in_array($fileActualExt, $allowedExts)) {
        return ['error' => 'This file extension is not allowed. Please choose a valid image file.'];
    }

    if ($file_size > $maxSize) {
        return ['error' => 'File size must be 2MB or lower.'];
    }

    $new_name = time() . '-' . basename($file_name);
    $target = $targetDir . $new_name;

    if (move_uploaded_file($file_tmp, $target)) {
        return ['success' => $new_name];
    }

    return ['error' => 'Failed to process file. Please try again.'];
}

// Only process POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Users Login Process Code =======================================================
    if (isset($_POST['loginBtn'])) {
        $logUsername = mysqli_real_escape_string($conn, trim($_POST['logUsername']));
        $logPassword = mysqli_real_escape_string($conn, md5(trim($_POST['logPassword'])));

        $login = "SELECT * FROM user WHERE username = '" . $logUsername . "' && password = '" . $logPassword . "'";
        $logResult = mysqli_query($conn, $login);
        if (mysqli_num_rows($logResult) > 0) {
            while ($logData = mysqli_fetch_assoc($logResult)) {
                setSession('name', $logData['first_name'] . ' ' . $logData['last_name']);
                setSession('username', $logData['username']);
                setSession('phone', $logData['phone']);
                setSession('email', $logData['email']);
                setSession('role', $logData['role']);
                setSession('author_id', $logData['user_id']);
                setSession('userStatus', $logData['userStatus']);
                setSession('success', 'You have successfully logged in.');
                redirect('../dashboard.php');
            }
        } else {
            setSession('warning', 'Invalid username or password.');
            redirect('../');
        }
    }
    // Post Rajection Codes =================================================
    elseif (isset($_POST['postRajected'])) {
        $postid = mysqli_real_escape_string($conn, $_POST['postR']);
        $postEnId = mysqli_real_escape_string($conn, base64_encode($_POST['postC']));
        $rajectQ = "UPDATE post SET postStatus ='N' WHERE post_id='{$postid}'";

        if (mysqli_query($conn, $rajectQ)) {
            setSession('success', 'Post rejected successfully.');
        } else {
            setSession('warning', 'Failed to reject post.');
        }
        redirect('../view-post.php?catID=' . $postEnId);
    }
    // Post Approved Codes ======================================================
    elseif (isset($_POST['postApproved'])) {
        $postid = mysqli_real_escape_string($conn, $_POST['postA']);
        $postEnId = mysqli_real_escape_string($conn, base64_encode($_POST['postC']));
        $postidkey = base64_encode($postid);

        // check exists file in dir
        $checkImg = mysqli_fetch_assoc(mysqli_query($conn, "SELECT post_img FROM post WHERE post_id = '{$postid}'"));
        $checkFile = '../../assets/postImage/' . $checkImg['post_img'];

        if (file_exists($checkFile)) {
            $postApproveQ = "UPDATE post SET postStatus ='Y' WHERE post_id='{$postid}'";
            if (mysqli_query($conn, $postApproveQ)) {
                setSession('success', 'Post approved successfully.');
            } else {
                setSession('warning', 'Failed to approve post.');
            }
            redirect('../view-post.php?catID=' . $postEnId);
        } else {
            setSession('warning', 'Post image not found. Please re-upload.');
            redirect('../update-post.php?postid=' . $postidkey);
        }
    }
    // Update Post Details Codes =======================================================
    elseif (isset($_POST['updatePost'])) {
        $postKey = base64_encode(trim($_POST['postId']));

        if (empty($_FILES['newImage']['name'])) {
            $image_name = trim($_POST['oldImage']);
        } else {
            $uploadResult = handleUpload('newImage', '../../assets/postImage/', ['jpeg', 'jpg', 'png', 'webp', 'avif']);
            if (isset($uploadResult['error'])) {
                setSession('warning', $uploadResult['error']);
                redirect('../update-post.php?postid=' . $postKey);
            } else {
                $image_name = $uploadResult['success'];
                @unlink('../../assets/postImage/' . $_POST['oldImage']);
            }
        }

        $postId = mysqli_real_escape_string($conn, trim($_POST['postId']));
        $description = mysqli_real_escape_string($conn, trim($_POST['description']));
        $shortDescription = mysqli_real_escape_string($conn, trim($_POST['postShortDesc']));
        $postCategory = mysqli_real_escape_string($conn, trim($_POST['newCategory']));
        $enCodeCategory = base64_encode(trim($_POST['newCategory']));
        $postTitle = mysqli_real_escape_string($conn, trim($_POST['post_title']));
        
        $postSlug = isset($_POST['post_slug']) && !empty(trim($_POST['post_slug'])) ? trim($_POST['post_slug']) : $postTitle;
        $postSlug = mysqli_real_escape_string($conn, strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $postSlug))));

        $metaTitle = mysqli_real_escape_string($conn, trim($_POST['meta_title'] ?? ''));
        $metaDescription = mysqli_real_escape_string($conn, trim($_POST['meta_description'] ?? ''));
        $metaKeywords = mysqli_real_escape_string($conn, trim($_POST['meta_keywords'] ?? ''));

        $sql = "UPDATE `post` SET postStatus = 'W', `title` = '{$postTitle}', `post_slug` = '{$postSlug}', `sort_details` = '{$shortDescription}', `description` = '{$description}', `category` = {$postCategory}, `post_img` = '{$image_name}', `meta_title` = '{$metaTitle}', `meta_description` = '{$metaDescription}', `meta_keywords` = '{$metaKeywords}' WHERE `post_id` = '{$postId}'";

        if (mysqli_query($conn, $sql)) {
            setSession('success', 'Post updated successfully.');
        } else {
            setSession('error', 'Failed to update post.');
        }
        redirect('../view-post.php?catID=' . $enCodeCategory);
    }
    // Add New Posts Codes =====================================================================================
    elseif (isset($_POST['saveNew_post'])) {
        $post_title = mysqli_real_escape_string($conn, trim($_POST['post_title']));
        $post_details = mysqli_real_escape_string($conn, trim($_POST['post_short_desc']));
        $post_category = mysqli_real_escape_string($conn, trim($_POST['post_category']));
        $description = mysqli_real_escape_string($conn, trim($_POST['description']));
        $author_id = mysqli_real_escape_string($conn, trim($_POST['author_id']));
        $date = date('d-m-Y');

        $post_slug = isset($_POST['post_slug']) && !empty(trim($_POST['post_slug'])) ? trim($_POST['post_slug']) : $post_title;
        $post_slug = mysqli_real_escape_string($conn, strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $post_slug))));

        $meta_title = mysqli_real_escape_string($conn, trim($_POST['meta_title'] ?? ''));
        $meta_description = mysqli_real_escape_string($conn, trim($_POST['meta_description'] ?? ''));
        $meta_keywords = mysqli_real_escape_string($conn, trim($_POST['meta_keywords'] ?? ''));

        if (isset($_FILES['postImage']) && !empty($_FILES['postImage']['name'])) {
            $uploadResult = handleUpload('postImage', '../../assets/postImage/', ['jpeg', 'jpg', 'png', 'webp', 'avif']);
            if (isset($uploadResult['error'])) {
                setSession('warning', $uploadResult['error']);
                redirect('../new-post.php');
            } else {
                $new_name = $uploadResult['success'];
            }
        } else {
            $new_name = '';  // Consider handling if image is mandatory
        }

        $addpQuery = "INSERT INTO `post`(`title`, `post_slug`, `sort_details`, `description`, `post_img`, `category`, `author`, `post_date`, `meta_title`, `meta_description`, `meta_keywords`) VALUES ('{$post_title}', '{$post_slug}', '{$post_details}', '{$description}', '{$new_name}', '{$post_category}', '{$author_id}', '{$date}', '{$meta_title}', '{$meta_description}', '{$meta_keywords}')";

        if (mysqli_query($conn, $addpQuery)) {
            setSession('success', 'New post saved successfully. <strong>Waiting for approval.</strong>');
        } else {
            setSession('error', 'Failed to save new post. <strong>Please try again.</strong>');
        }
        redirect('../new-post.php');
    }
    // Category Approved Codes ================================================
    elseif (isset($_POST['categoryApproved'])) {
        $categoryid = mysqli_real_escape_string($conn, $_POST['categoryA']);
        $rajectQ = "UPDATE category SET categoryStatus ='Y' WHERE category_id='{$categoryid}'";

        if (mysqli_query($conn, $rajectQ)) {
            setSession('success', 'Category approved successfully.');
        } else {
            setSession('error', 'Failed to approve category.');
        }
        redirect('../view-category.php');
    }
    // Category Rajected Codes ================================================
    elseif (isset($_POST['categoryRajected'])) {
        $categoryid = mysqli_real_escape_string($conn, $_POST['categoryR']);
        $checkC1Q = "SELECT * FROM post WHERE category = '{$categoryid}'";

        if (mysqli_num_rows(mysqli_query($conn, $checkC1Q)) > 0) {
            setSession('warning', 'This category cannot be rejected as it is currently in use.');
        } else {
            $rajectQ = "UPDATE category SET categoryStatus ='N' WHERE category_id='{$categoryid}'";
            if (mysqli_query($conn, $rajectQ)) {
                setSession('success', 'Category rejected successfully.');
            } else {
                setSession('warning', 'Failed to reject category.');
            }
        }
        redirect('../view-category.php');
    }
    // Category Update Codes =======================================================
    elseif (isset($_POST['updateCategory'])) {
        $categoryName = mysqli_real_escape_string($conn, $_POST['categoryName']);
        $categoryTitle = mysqli_real_escape_string($conn, $_POST['categoryTitle']);
        $category_id = mysqli_real_escape_string($conn, $_POST['category_id']);

        $update = "UPDATE category SET category_name='{$categoryName}',categoryTitle='{$categoryTitle}' WHERE category_id = '{$category_id}'";

        if (mysqli_query($conn, $update)) {
            setSession('success', 'Category updated successfully.');
        } else {
            setSession('error', 'Failed to update category.');
        }
        redirect('../view-category.php');
    }
    // Category Add Codes ===============================================
    elseif (isset($_POST['addCategory'])) {
        $categoryName = mysqli_real_escape_string($conn, $_POST['categoryName']);
        $categoryTitle = mysqli_real_escape_string($conn, $_POST['categoryTitle']);

        $insert = "INSERT INTO category(category_name,categoryTitle,author)VALUES('{$categoryName}','{$categoryTitle}','{$_SESSION['author_id']}')";

        if (mysqli_query($conn, $insert)) {
            setSession('success', 'Category added successfully.');
        } else {
            setSession('error', 'Failed to add category.');
        }
        redirect('../view-category.php');
    }
    // User Rajected Codes =================================================================
    elseif (isset($_POST['userRajected'])) {
        $userid = mysqli_real_escape_string($conn, $_POST['userR']);
        $userQ = "UPDATE user SET userStatus = 'N' WHERE user_id = '{$userid}'";

        if (mysqli_query($conn, $userQ)) {
            setSession('success', 'User rejected successfully.');
        } else {
            setSession('warning', 'Failed to reject user.');
        }
        redirect('../view-user.php');
    }
    // User Approved Codes =================================================================
    elseif (isset($_POST['userApproved'])) {
        $userid = mysqli_real_escape_string($conn, $_POST['userA']);
        $userQ = "UPDATE user SET userStatus = 'Y' WHERE user_id = '{$userid}'";

        if (mysqli_query($conn, $userQ)) {
            setSession('success', 'User approved successfully.');
        } else {
            setSession('error', 'Failed to approve user.');
        }
        redirect('../view-user.php');
    }
    // Users Updates Codes =================================================================
    elseif (isset($_POST['userUpdate'])) {
        $user_id = mysqli_real_escape_string($conn, trim($_POST['user_id']));
        $first_name = mysqli_real_escape_string($conn, trim($_POST['first_name']));
        $last_name = mysqli_real_escape_string($conn, trim($_POST['last_name']));
        $userMobile = mysqli_real_escape_string($conn, trim($_POST['userMobile']));
        $userEmail = mysqli_real_escape_string($conn, trim($_POST['userEmail']));
        $role = mysqli_real_escape_string($conn, trim($_POST['role']));

        $updateQ = "UPDATE `user` SET `first_name`='{$first_name}',`last_name`='{$last_name}',`phone`='{$userMobile}',`email`='{$userEmail}',`role`= {$role} WHERE user_id ='{$user_id}'";

        if (mysqli_query($conn, $updateQ)) {
            setSession('success', 'User data updated successfully.');
        } else {
            setSession('error', 'Failed to update user data.');
        }
        redirect('../view-user.php');
    }
    // Add New User Codes ==================================================================
    elseif (isset($_POST['addUser'])) {
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
            setSession('success', 'New user created successfully.');
        } else {
            setSession('error', 'Failed to create new user.');
        }
        redirect('../view-user.php');
    }
    // Update user Setting code ============================================================
    elseif (isset($_POST['userProfile'])) {
        $userid = mysqli_real_escape_string($conn, trim($_POST['userid']));
        $name = explode(' ', $_POST['userFull']);
        $first_name = mysqli_real_escape_string($conn, trim($name[0]));
        $last_name = mysqli_real_escape_string($conn, trim(isset($name[1]) ? $name[1] : ''));
        $userEmail = mysqli_real_escape_string($conn, trim($_POST['userEmail']));
        $username = mysqli_real_escape_string($conn, trim($_POST['username']));
        $userMobile = mysqli_real_escape_string($conn, trim($_POST['userMobile']));

        $updateQ = "UPDATE `user` SET `username`='{$username}',`first_name`='{$first_name}',`last_name`='{$last_name}',`phone`='{$userMobile}',`email`='{$userEmail}' WHERE user_id = '{$userid}'";

        if (mysqli_query($conn, $updateQ)) {
            setSession('success', 'User profile updated successfully.');
        } else {
            setSession('error', 'Failed to update user profile.');
        }
        redirect('../setting.php');
    }
    // Settings Updation Codes =============================================================
    elseif (isset($_POST['settingUpdate'])) {
        $websiteName = mysqli_real_escape_string($conn, trim($_POST['webName']));
        $webFooter = mysqli_real_escape_string($conn, trim($_POST['webFooter']));
        $webEmail = mysqli_real_escape_string($conn, trim($_POST['webEmail']));
        $webKeyword = mysqli_real_escape_string($conn, trim($_POST['webKeyword']));

        $settingQ = "UPDATE `settings` SET `websitename`='{$websiteName}',`footerdesc`='{$webFooter}',`keywords`='{$webKeyword}',`workEmail`='{$webEmail}'";

        if (mysqli_query($conn, $settingQ)) {
            setSession('success', 'Website settings updated successfully.');
        } else {
            setSession('error', 'Failed to update website settings.');
        }
        redirect('../manage-website.php');
    }
    // Settings Logo Updation Codes =========================================================
    elseif (isset($_POST['webLogobtn'])) {
        if (isset($_FILES['webLogo']['name']) && !empty($_FILES['webLogo']['name'])) {
            $uploadResult = handleUpload('webLogo', '../../assets/images/');
            if (isset($uploadResult['error'])) {
                setSession('error', $uploadResult['error']);
            } else {
                $webLogo = $uploadResult['success'];
                $settingQ = "UPDATE `settings` SET `logo`='{$webLogo}'";
                if (mysqli_query($conn, $settingQ)) {
                    setSession('success', 'Website logo updated successfully.');
                } else {
                    setSession('error', 'Failed to update website logo.');
                }
            }
        }
        redirect('../manage-website.php');
    }
    // Settings Favicon Updation Codes =============================================================
    elseif (isset($_POST['webfaviconbtn'])) {
        if (isset($_FILES['webfavicon']['name']) && !empty($_FILES['webfavicon']['name'])) {
            $uploadResult = handleUpload('webfavicon', '../../assets/images/');
            if (isset($uploadResult['error'])) {
                setSession('error', $uploadResult['error']);
            } else {
                $webfavicon = $uploadResult['success'];
                $settingQ = "UPDATE `settings` SET `favicon`='{$webfavicon}'";
                if (mysqli_query($conn, $settingQ)) {
                    setSession('success', 'Website favicon updated successfully.');
                } else {
                    setSession('error', 'Failed to update website favicon.');
                }
            }
        }
        redirect('../manage-website.php');
    }
    // Settings Watter Mark Updation Codes =========================================================
    elseif (isset($_POST['webwattermarkbtn'])) {
        if (isset($_FILES['webwatterMark']['name']) && !empty($_FILES['webwatterMark']['name'])) {
            $uploadResult = handleUpload('webwatterMark', '../../assets/images/');
            if (isset($uploadResult['error'])) {
                setSession('error', $uploadResult['error']);
            } else {
                $webwatterMark = $uploadResult['success'];
                $settingQ = "UPDATE `settings` SET `watterMark`='{$webwatterMark}'";
                if (mysqli_query($conn, $settingQ)) {
                    setSession('success', 'Website watermark updated successfully.');
                } else {
                    setSession('error', 'Failed to update website watermark.');
                }
            }
        }
        redirect('../manage-website.php');
    }
    // Change Password Code =======================================================================
    elseif (isset($_POST['changeUserPassword'])) {
        $username = mysqli_real_escape_string($conn, trim($_POST['username']));
        $mobile = mysqli_real_escape_string($conn, trim($_POST['mobile']));
        $password = mysqli_real_escape_string($conn, trim(md5($_POST['userPassword'])));

        $runQ = "UPDATE `user` SET `password` = '{$password}' WHERE username = '{$username}' && phone = '{$mobile}'";

        if (mysqli_query($conn, $runQ)) {
            setSession('success', 'Password changed successfully.');
        } else {
            setSession('error', 'Failed to change password.');
        }
        redirect('../');
    }
    // Fallback error page
    else {
        redirect('../404.php');
    }
} else {
    // If accessed via GET or other method
    redirect('../404.php');
}

// Connection Close ==========================================================================
if (isset($conn) && $conn) {
    mysqli_close($conn);
}
