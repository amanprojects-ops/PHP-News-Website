<?php
session_start();
include_once 'config.php';

// Reusable Secure File Upload Function
function handleUpload($fileKey, $targetDir, $allowedExts = ['jpeg', 'jpg', 'png', 'webp', 'ico'], $maxSize = 2097152)
{
    if (!isset($_FILES[$fileKey]) || empty($_FILES[$fileKey]['name'])) {
        return ['error' => 'No file selected for upload.'];
    }

    if ($_FILES[$fileKey]['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'File upload error code: ' . $_FILES[$fileKey]['error']];
    }

    $file_name = $_FILES[$fileKey]['name'];
    $file_size = $_FILES[$fileKey]['size'];
    $file_tmp = $_FILES[$fileKey]['tmp_name'];

    // Enforce extension whitelist
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $fileActualExt = strtolower($file_ext);

    if (!in_array($fileActualExt, $allowedExts, true)) {
        return ['error' => 'File extension .' . htmlspecialchars($fileActualExt) . ' is not allowed. Only ' . implode(', ', $allowedExts) . ' files are permitted.'];
    }

    if ($file_size > $maxSize) {
        return ['error' => 'File size exceeds 2MB limit.'];
    }

    // Verify real MIME type using finfo
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file_tmp);
        finfo_close($finfo);

        $allowedMimes = [
            'jpg'  => ['image/jpeg', 'image/pjpeg'],
            'jpeg' => ['image/jpeg', 'image/pjpeg'],
            'png'  => ['image/png', 'image/x-png'],
            'webp' => ['image/webp'],
            'ico'  => ['image/x-icon', 'image/vnd.microsoft.icon', 'image/ico', 'application/octet-stream'],
            'gif'  => ['image/gif']
        ];

        $validMime = false;
        if (isset($allowedMimes[$fileActualExt]) && in_array($mime, $allowedMimes[$fileActualExt], true)) {
            $validMime = true;
        }

        if (!$validMime && $fileActualExt !== 'ico') {
            return ['error' => 'File content does not match allowed image MIME type. Upload rejected for security.'];
        }
    }

    // Verify image integrity with getimagesize (for non-ico images)
    if ($fileActualExt !== 'ico') {
        $imgInfo = @getimagesize($file_tmp);
        if ($imgInfo === false) {
            return ['error' => 'Uploaded file is not a valid image.'];
        }
    }

    // Cryptographically secure randomized filename to prevent collisions and directory traversal
    $prefix = preg_replace('/[^a-zA-Z0-9_-]/', '', strtolower($fileKey));
    $randomHash = bin2hex(random_bytes(6));
    $new_name = $prefix . '_' . time() . '_' . $randomHash . '.' . $fileActualExt;

    // Ensure target directory exists
    if (!is_dir($targetDir)) {
        @mkdir($targetDir, 0755, true);
    }

    $target = rtrim($targetDir, '/') . '/' . $new_name;

    if (move_uploaded_file($file_tmp, $target)) {
        return ['success' => $new_name];
    }

    return ['error' => 'Failed to process and move uploaded file.'];
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

        // Multi-Slug History & Aliases Management
        $oldPostRes = mysqli_query($conn, "SELECT post_slug, slug_aliases FROM post WHERE post_id = '{$postId}'");
        $oldPost = $oldPostRes ? mysqli_fetch_assoc($oldPostRes) : [];
        $oldPrimarySlug = $oldPost['post_slug'] ?? '';
        $currentAliases = $oldPost['slug_aliases'] ?? '';

        $newPrimarySlug = slugify(!empty($_POST['post_slug']) ? $_POST['post_slug'] : $postTitle);
        $enteredAliases = trim($_POST['slug_aliases'] ?? '');

        $combinedAliases = [];
        if (!empty($enteredAliases)) {
            $combinedAliases = array_merge($combinedAliases, explode(',', $enteredAliases));
        }
        if (!empty($currentAliases)) {
            $combinedAliases = array_merge($combinedAliases, explode(',', $currentAliases));
        }
        // Auto-archive old primary slug into aliases so backlinks never 404
        if (!empty($oldPrimarySlug) && $oldPrimarySlug !== $newPrimarySlug) {
            $combinedAliases[] = $oldPrimarySlug;
        }

        $uniqueAliases = [];
        foreach ($combinedAliases as $al) {
            $clean = slugify($al);
            if (!empty($clean) && $clean !== $newPrimarySlug && !in_array($clean, $uniqueAliases)) {
                $uniqueAliases[] = $clean;
            }
        }
        $finalAliasesStr = implode(', ', $uniqueAliases);
        $finalAliasesSafe = mysqli_real_escape_string($conn, $finalAliasesStr);
        $postSlugSafe = mysqli_real_escape_string($conn, $newPrimarySlug);

        $metaTitle = mysqli_real_escape_string($conn, trim($_POST['meta_title'] ?? ''));
        $metaDescription = mysqli_real_escape_string($conn, trim($_POST['meta_description'] ?? ''));
        $metaKeywords = mysqli_real_escape_string($conn, trim($_POST['meta_keywords'] ?? ''));

        $sql = "UPDATE `post` SET postStatus = 'W', `title` = '{$postTitle}', `post_slug` = '{$postSlugSafe}', `slug_aliases` = '{$finalAliasesSafe}', `sort_details` = '{$shortDescription}', `description` = '{$description}', `category` = {$postCategory}, `post_img` = '{$image_name}', `meta_title` = '{$metaTitle}', `meta_description` = '{$metaDescription}', `meta_keywords` = '{$metaKeywords}' WHERE `post_id` = '{$postId}'";

        if (mysqli_query($conn, $sql)) {
            syncPostSlugs($conn, $postId, $newPrimarySlug, $finalAliasesStr);
            setSession('success', 'Post updated successfully with multi-slug sync.');
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

        $clean_slug = slugify(!empty($_POST['post_slug']) ? $_POST['post_slug'] : $post_title);
        $post_slug_safe = mysqli_real_escape_string($conn, $clean_slug);

        $raw_aliases = trim($_POST['slug_aliases'] ?? '');
        $parsedAliases = [];
        if (!empty($raw_aliases)) {
            foreach (explode(',', $raw_aliases) as $a) {
                $c = slugify($a);
                if (!empty($c) && $c !== $clean_slug && !in_array($c, $parsedAliases)) {
                    $parsedAliases[] = $c;
                }
            }
        }
        $finalAliasesStr = implode(', ', $parsedAliases);
        $finalAliasesSafe = mysqli_real_escape_string($conn, $finalAliasesStr);

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
            $new_name = '';
        }

        $addpQuery = "INSERT INTO `post`(`title`, `post_slug`, `slug_aliases`, `sort_details`, `description`, `post_img`, `category`, `author`, `post_date`, `meta_title`, `meta_description`, `meta_keywords`) VALUES ('{$post_title}', '{$post_slug_safe}', '{$finalAliasesSafe}', '{$post_details}', '{$description}', '{$new_name}', '{$post_category}', '{$author_id}', '{$date}', '{$meta_title}', '{$meta_description}', '{$meta_keywords}')";

        if (mysqli_query($conn, $addpQuery)) {
            $newPostId = mysqli_insert_id($conn);
            syncPostSlugs($conn, $newPostId, $clean_slug, $finalAliasesStr);
            setSession('success', 'New post saved successfully with clean multi-slug routing. <strong>Waiting for approval.</strong>');
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
        $checkC1Q = "SELECT * FROM post WHERE category='{$categoryid}'";
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

        $catSlugInput = trim($_POST['category_slug'] ?? '');
        $catSlug = slugify(!empty($catSlugInput) ? $catSlugInput : $categoryName);
        $catSlugSafe = mysqli_real_escape_string($conn, $catSlug);

        $update = "UPDATE category SET category_name='{$categoryName}', category_slug='{$catSlugSafe}', categoryTitle='{$categoryTitle}' WHERE category_id = '{$category_id}'";

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

        $catSlugInput = trim($_POST['category_slug'] ?? '');
        $catSlug = slugify(!empty($catSlugInput) ? $catSlugInput : $categoryName);
        $catSlugSafe = mysqli_real_escape_string($conn, $catSlug);

        $insert = "INSERT INTO category(category_name, category_slug, categoryTitle, author) VALUES ('{$categoryName}', '{$catSlugSafe}', '{$categoryTitle}', '{$_SESSION['author_id']}')";

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
    // System Settings: General & Contact ===================================================
    elseif (isset($_POST['settingUpdate']) || isset($_POST['updateGeneralSettings'])) {
        if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
            setSession('error', 'Unauthorized access.');
            redirect('../dashboard.php');
        }
        $csrf = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            setSession('error', 'Security verification failed (Invalid CSRF token).');
            redirect('../manage-website.php?tab=general');
        }

        $webName = trim($_POST['webName'] ?? '');
        $webTitle = trim($_POST['webTitle'] ?? '');
        $webUrl = rtrim(trim($_POST['webUrl'] ?? ''), '/');
        $webEmail = trim($_POST['webEmail'] ?? '');
        $contactPhone = trim($_POST['contactPhone'] ?? '');
        $contactAddress = trim($_POST['contactAddress'] ?? '');
        $webFooter = trim($_POST['webFooter'] ?? '');
        $maintenanceMode = isset($_POST['maintenanceMode']) ? 1 : 0;
        $maintenanceMsg = trim($_POST['maintenanceMsg'] ?? '');

        $stmt = mysqli_prepare($conn, "UPDATE `settings` SET `websitename`=?, `websiteTitle`=?, `websiteUrl`=?, `workEmail`=?, `contactPhone`=?, `contactAddress`=?, `footerdesc`=?, `maintenanceMode`=?, `maintenanceMsg`=?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "sssssssis", $webName, $webTitle, $webUrl, $webEmail, $contactPhone, $contactAddress, $webFooter, $maintenanceMode, $maintenanceMsg);
            if (mysqli_stmt_execute($stmt)) {
                setSession('success', 'General website settings updated successfully.');
            } else {
                setSession('error', 'Failed to update website settings: ' . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            setSession('error', 'Database query preparation failed.');
        }
        redirect('../manage-website.php?tab=general');
    }
    // System Settings: SEO & Social Profiles ===============================================
    elseif (isset($_POST['updateSeoSettings'])) {
        if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
            setSession('error', 'Unauthorized access.');
            redirect('../dashboard.php');
        }
        $csrf = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            setSession('error', 'Security verification failed (Invalid CSRF token).');
            redirect('../manage-website.php?tab=seo');
        }

        $webKeyword = trim($_POST['webKeyword'] ?? '');
        $metaDescription = trim($_POST['metaDescription'] ?? '');
        $metaAuthor = trim($_POST['metaAuthor'] ?? '');
        $robotsIndex = trim($_POST['robotsIndex'] ?? 'index, follow');
        $socialFacebook = trim($_POST['socialFacebook'] ?? '');
        $socialTwitter = trim($_POST['socialTwitter'] ?? '');
        $socialInstagram = trim($_POST['socialInstagram'] ?? '');
        $socialLinkedin = trim($_POST['socialLinkedin'] ?? '');
        $socialYoutube = trim($_POST['socialYoutube'] ?? '');
        $socialWhatsapp = trim($_POST['socialWhatsapp'] ?? '');

        $stmt = mysqli_prepare($conn, "UPDATE `settings` SET `keywords`=?, `metaDescription`=?, `metaAuthor`=?, `robotsIndex`=?, `socialFacebook`=?, `socialTwitter`=?, `socialInstagram`=?, `socialLinkedin`=?, `socialYoutube`=?, `socialWhatsapp`=?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssssssssss", $webKeyword, $metaDescription, $metaAuthor, $robotsIndex, $socialFacebook, $socialTwitter, $socialInstagram, $socialLinkedin, $socialYoutube, $socialWhatsapp);
            if (mysqli_stmt_execute($stmt)) {
                setSession('success', 'SEO and social profile settings updated successfully.');
            } else {
                setSession('error', 'Failed to update SEO settings: ' . mysqli_stmt_error($stmt));
            }
            mysqli_stmt_close($stmt);
        } else {
            setSession('error', 'Database query preparation failed.');
        }
        redirect('../manage-website.php?tab=seo');
    }
    // System Settings: Email & SMTP ========================================================
    elseif (isset($_POST['updateSmtpSettings'])) {
        if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
            setSession('error', 'Unauthorized access.');
            redirect('../dashboard.php');
        }
        $csrf = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            setSession('error', 'Security verification failed (Invalid CSRF token).');
            redirect('../manage-website.php?tab=smtp');
        }

        $mailDriver = trim($_POST['mailDriver'] ?? 'mail');
        $smtpHost = trim($_POST['smtpHost'] ?? '');
        $smtpPort = (int)($_POST['smtpPort'] ?? 587);
        $smtpUser = trim($_POST['smtpUser'] ?? '');
        $smtpPass = trim($_POST['smtpPass'] ?? '');
        $smtpEncryption = trim($_POST['smtpEncryption'] ?? 'tls');
        $smtpFromEmail = trim($_POST['smtpFromEmail'] ?? '');
        $smtpFromName = trim($_POST['smtpFromName'] ?? '');

        if ($smtpPass !== '') {
            $stmt = mysqli_prepare($conn, "UPDATE `settings` SET `mailDriver`=?, `smtpHost`=?, `smtpPort`=?, `smtpUser`=?, `smtpPass`=?, `smtpEncryption`=?, `smtpFromEmail`=?, `smtpFromName`=?");
            mysqli_stmt_bind_param($stmt, "ssisssss", $mailDriver, $smtpHost, $smtpPort, $smtpUser, $smtpPass, $smtpEncryption, $smtpFromEmail, $smtpFromName);
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE `settings` SET `mailDriver`=?, `smtpHost`=?, `smtpPort`=?, `smtpUser`=?, `smtpEncryption`=?, `smtpFromEmail`=?, `smtpFromName`=?");
            mysqli_stmt_bind_param($stmt, "ssissss", $mailDriver, $smtpHost, $smtpPort, $smtpUser, $smtpEncryption, $smtpFromEmail, $smtpFromName);
        }

        if ($stmt && mysqli_stmt_execute($stmt)) {
            setSession('success', 'Email and SMTP configuration updated successfully.');
            mysqli_stmt_close($stmt);
        } else {
            setSession('error', 'Failed to update SMTP settings.');
        }
        redirect('../manage-website.php?tab=smtp');
    }
    // System Settings: Analytics & Custom Code =============================================
    elseif (isset($_POST['updateCustomCode'])) {
        if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
            setSession('error', 'Unauthorized access.');
            redirect('../dashboard.php');
        }
        $csrf = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            setSession('error', 'Security verification failed (Invalid CSRF token).');
            redirect('../manage-website.php?tab=custom');
        }

        $googleAnalytics = trim($_POST['googleAnalytics'] ?? '');
        $googleAdsense = trim($_POST['googleAdsense'] ?? '');
        $customHeadCode = trim($_POST['customHeadCode'] ?? '');
        $customFooterCode = trim($_POST['customFooterCode'] ?? '');

        $stmt = mysqli_prepare($conn, "UPDATE `settings` SET `googleAnalytics`=?, `googleAdsense`=?, `customHeadCode`=?, `customFooterCode`=?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "ssss", $googleAnalytics, $googleAdsense, $customHeadCode, $customFooterCode);
            if (mysqli_stmt_execute($stmt)) {
                setSession('success', 'Custom scripts and analytics settings updated successfully.');
            } else {
                setSession('error', 'Failed to update custom scripts.');
            }
            mysqli_stmt_close($stmt);
        } else {
            setSession('error', 'Database query preparation failed.');
        }
        redirect('../manage-website.php?tab=custom');
    }
    // Settings Logo Updation Codes =========================================================
    elseif (isset($_POST['webLogobtn'])) {
        if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
            setSession('error', 'Unauthorized access.');
            redirect('../dashboard.php');
        }
        $csrf = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            setSession('error', 'Security verification failed.');
            redirect('../manage-website.php?tab=branding');
        }

        if (isset($_FILES['webLogo']['name']) && !empty($_FILES['webLogo']['name'])) {
            $uploadResult = handleUpload('webLogo', '../../assets/images/');
            if (isset($uploadResult['error'])) {
                setSession('error', $uploadResult['error']);
            } else {
                $webLogo = $uploadResult['success'];
                $stmt = mysqli_prepare($conn, "UPDATE `settings` SET `logo`=?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $webLogo);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    setSession('success', 'Website logo updated successfully.');
                } else {
                    setSession('error', 'Failed to update website logo.');
                }
            }
        }
        redirect('../manage-website.php?tab=branding');
    }
    // Settings Favicon Updation Codes =============================================================
    elseif (isset($_POST['webfaviconbtn'])) {
        if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
            setSession('error', 'Unauthorized access.');
            redirect('../dashboard.php');
        }
        $csrf = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            setSession('error', 'Security verification failed.');
            redirect('../manage-website.php?tab=branding');
        }

        if (isset($_FILES['webfavicon']['name']) && !empty($_FILES['webfavicon']['name'])) {
            $uploadResult = handleUpload('webfavicon', '../../assets/images/', ['ico', 'png', 'jpg', 'jpeg', 'webp']);
            if (isset($uploadResult['error'])) {
                setSession('error', $uploadResult['error']);
            } else {
                $webfavicon = $uploadResult['success'];
                $stmt = mysqli_prepare($conn, "UPDATE `settings` SET `favicon`=?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $webfavicon);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    setSession('success', 'Website favicon updated successfully.');
                } else {
                    setSession('error', 'Failed to update website favicon.');
                }
            }
        }
        redirect('../manage-website.php?tab=branding');
    }
    // Settings Watter Mark Updation Codes =========================================================
    elseif (isset($_POST['webwattermarkbtn'])) {
        if (!isset($_SESSION['role']) || (int)$_SESSION['role'] !== 1) {
            setSession('error', 'Unauthorized access.');
            redirect('../dashboard.php');
        }
        $csrf = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            setSession('error', 'Security verification failed.');
            redirect('../manage-website.php?tab=branding');
        }

        if (isset($_FILES['webwatterMark']['name']) && !empty($_FILES['webwatterMark']['name'])) {
            $uploadResult = handleUpload('webwatterMark', '../../assets/images/');
            if (isset($uploadResult['error'])) {
                setSession('error', $uploadResult['error']);
            } else {
                $webwatterMark = $uploadResult['success'];
                $stmt = mysqli_prepare($conn, "UPDATE `settings` SET `watterMark`=?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "s", $webwatterMark);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    setSession('success', 'Website watermark updated successfully.');
                } else {
                    setSession('error', 'Failed to update website watermark.');
                }
            }
        }
        redirect('../manage-website.php?tab=branding');
    }
    // Add Slug Alias (Multi-Slug Redirect) ========================================================
    elseif (isset($_POST['add_slug_alias'])) {
        $isAjax = !empty($_POST['is_ajax']);
        $csrf = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'CSRF verification failed.']);
                exit;
            }
            setSession('error', 'Security verification failed.');
            redirect('../manage-slugs.php');
        }

        $postId = (int)($_POST['post_id'] ?? 0);
        $aliasInput = trim($_POST['alias_slug'] ?? '');
        $cleanAlias = slugify($aliasInput);

        if ($postId <= 0 || empty($cleanAlias)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Invalid post ID or empty slug alias.']);
                exit;
            }
            setSession('error', 'Please enter a valid alias slug.');
            redirect('../manage-slugs.php?post_id=' . $postId);
        }

        $postRes = mysqli_query($conn, "SELECT post_id, author, post_slug, slug_aliases FROM post WHERE post_id = {$postId} LIMIT 1");
        if (!$postRes || mysqli_num_rows($postRes) === 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Post not found.']);
                exit;
            }
            setSession('error', 'Post not found.');
            redirect('../manage-slugs.php');
        }
        $postData = mysqli_fetch_assoc($postRes);

        if ($_SESSION['role'] != 1 && $_SESSION['role'] != 2 && $postData['author'] != $_SESSION['author_id']) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized action.']);
                exit;
            }
            setSession('error', 'Unauthorized access.');
            redirect('../manage-slugs.php');
        }

        if ($cleanAlias === $postData['post_slug']) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => "Slug '$cleanAlias' is already the primary canonical URL for this post."]);
                exit;
            }
            setSession('warning', "Slug '{$cleanAlias}' is already the primary canonical URL for this post.");
            redirect('../manage-slugs.php?post_id=' . $postId);
        }

        $escapedAlias = mysqli_real_escape_string($conn, $cleanAlias);
        $collisionRes = mysqli_query($conn, "SELECT post_id FROM post_slugs WHERE slug = '{$escapedAlias}' AND post_id != {$postId} LIMIT 1");
        if ($collisionRes && mysqli_num_rows($collisionRes) > 0) {
            $coll = mysqli_fetch_assoc($collisionRes);
            $msg = "The slug '{$cleanAlias}' is already reserved for Post #{$coll['post_id']}. Please choose a unique slug.";
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => $msg]);
                exit;
            }
            setSession('error', $msg);
            redirect('../manage-slugs.php?post_id=' . $postId);
        }

        $currentAliases = [];
        if (!empty($postData['slug_aliases'])) {
            foreach (explode(',', $postData['slug_aliases']) as $a) {
                $c = slugify($a);
                if (!empty($c) && !in_array($c, $currentAliases)) {
                    $currentAliases[] = $c;
                }
            }
        }

        if (!in_array($cleanAlias, $currentAliases)) {
            $currentAliases[] = $cleanAlias;
        }

        $syncRes = syncPostSlugs($conn, $postId, $postData['post_slug'], $currentAliases);
        if ($syncRes) {
            $msg = "Alias redirect '/{$cleanAlias}' added successfully (301 redirect active).";
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'success', 'message' => $msg, 'alias' => $cleanAlias, 'post_id' => $postId]);
                exit;
            }
            setSession('success', $msg);
        } else {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Failed to synchronize slug redirect index.']);
                exit;
            }
            setSession('error', 'Failed to synchronize slug redirect index.');
        }
        redirect('../manage-slugs.php?post_id=' . $postId);
    }
    // Remove Slug Alias ==========================================================================
    elseif (isset($_POST['remove_slug_alias'])) {
        $isAjax = !empty($_POST['is_ajax']);
        $csrf = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'CSRF verification failed.']);
                exit;
            }
            setSession('error', 'Security verification failed.');
            redirect('../manage-slugs.php');
        }

        $postId = (int)($_POST['post_id'] ?? 0);
        $aliasInput = trim($_POST['alias_slug'] ?? '');
        $cleanAlias = slugify($aliasInput);

        if ($postId <= 0 || empty($cleanAlias)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Invalid post ID or alias.']);
                exit;
            }
            setSession('error', 'Invalid post ID or alias.');
            redirect('../manage-slugs.php?post_id=' . $postId);
        }

        $postRes = mysqli_query($conn, "SELECT post_id, author, post_slug, slug_aliases FROM post WHERE post_id = {$postId} LIMIT 1");
        if (!$postRes || mysqli_num_rows($postRes) === 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Post not found.']);
                exit;
            }
            setSession('error', 'Post not found.');
            redirect('../manage-slugs.php');
        }
        $postData = mysqli_fetch_assoc($postRes);

        if ($_SESSION['role'] != 1 && $_SESSION['role'] != 2 && $postData['author'] != $_SESSION['author_id']) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized action.']);
                exit;
            }
            setSession('error', 'Unauthorized access.');
            redirect('../manage-slugs.php');
        }

        $currentAliases = [];
        if (!empty($postData['slug_aliases'])) {
            foreach (explode(',', $postData['slug_aliases']) as $a) {
                $c = slugify($a);
                if (!empty($c) && $c !== $cleanAlias && !in_array($c, $currentAliases)) {
                    $currentAliases[] = $c;
                }
            }
        }

        syncPostSlugs($conn, $postId, $postData['post_slug'], $currentAliases);
        $msg = "Alias redirect '/{$cleanAlias}' removed successfully.";
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => $msg, 'alias' => $cleanAlias, 'post_id' => $postId]);
            exit;
        }
        setSession('success', $msg);
        redirect('../manage-slugs.php?post_id=' . $postId);
    }
    // Update Canonical Primary Slug ==============================================================
    elseif (isset($_POST['update_primary_slug'])) {
        $isAjax = !empty($_POST['is_ajax']);
        $csrf = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'CSRF verification failed.']);
                exit;
            }
            setSession('error', 'Security verification failed.');
            redirect('../manage-slugs.php');
        }

        $postId = (int)($_POST['post_id'] ?? 0);
        $newPrimaryInput = trim($_POST['primary_slug'] ?? '');
        $cleanNewPrimary = slugify($newPrimaryInput);
        $preserveOld = !empty($_POST['preserve_alias']);

        if ($postId <= 0 || empty($cleanNewPrimary)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Primary slug cannot be empty.']);
                exit;
            }
            setSession('error', 'Primary slug cannot be empty.');
            redirect('../manage-slugs.php?post_id=' . $postId);
        }

        $postRes = mysqli_query($conn, "SELECT post_id, author, post_slug, slug_aliases FROM post WHERE post_id = {$postId} LIMIT 1");
        if (!$postRes || mysqli_num_rows($postRes) === 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Post not found.']);
                exit;
            }
            setSession('error', 'Post not found.');
            redirect('../manage-slugs.php');
        }
        $postData = mysqli_fetch_assoc($postRes);

        if ($_SESSION['role'] != 1 && $_SESSION['role'] != 2 && $postData['author'] != $_SESSION['author_id']) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized action.']);
                exit;
            }
            setSession('error', 'Unauthorized access.');
            redirect('../manage-slugs.php');
        }

        $escapedNewPrimary = mysqli_real_escape_string($conn, $cleanNewPrimary);
        $collisionRes = mysqli_query($conn, "SELECT post_id FROM post_slugs WHERE slug = '{$escapedNewPrimary}' AND post_id != {$postId} LIMIT 1");
        if ($collisionRes && mysqli_num_rows($collisionRes) > 0) {
            $coll = mysqli_fetch_assoc($collisionRes);
            $msg = "Slug '{$cleanNewPrimary}' is already in use by Post #{$coll['post_id']}. Please choose a unique slug.";
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => $msg]);
                exit;
            }
            setSession('error', $msg);
            redirect('../manage-slugs.php?post_id=' . $postId);
        }

        $oldPrimary = $postData['post_slug'];
        $currentAliases = [];
        if (!empty($postData['slug_aliases'])) {
            foreach (explode(',', $postData['slug_aliases']) as $a) {
                $c = slugify($a);
                if (!empty($c) && $c !== $cleanNewPrimary && !in_array($c, $currentAliases)) {
                    $currentAliases[] = $c;
                }
            }
        }

        if ($preserveOld && !empty($oldPrimary) && $oldPrimary !== $cleanNewPrimary) {
            if (!in_array($oldPrimary, $currentAliases)) {
                $currentAliases[] = $oldPrimary;
            }
        }

        syncPostSlugs($conn, $postId, $cleanNewPrimary, $currentAliases);
        $msg = "Primary canonical slug updated to '/{$cleanNewPrimary}'." . ($preserveOld ? " Previous slug '/{$oldPrimary}' will now 301-redirect to it." : "");
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => $msg, 'new_slug' => $cleanNewPrimary, 'post_id' => $postId]);
            exit;
        }
        setSession('success', $msg);
        redirect('../manage-slugs.php?post_id=' . $postId);
    }
    // Update Category Slug Direct ===============================================================
    elseif (isset($_POST['update_category_slug_direct'])) {
        $isAjax = !empty($_POST['is_ajax']);
        $csrf = $_POST['csrf_token'] ?? '';
        if (empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $csrf)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'CSRF verification failed.']);
                exit;
            }
            setSession('error', 'Security verification failed.');
            redirect('../manage-slugs.php?tab=categories');
        }

        if ($_SESSION['role'] != 1 && $_SESSION['role'] != 2) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized action.']);
                exit;
            }
            setSession('error', 'Unauthorized access.');
            redirect('../manage-slugs.php?tab=categories');
        }

        $categoryId = (int)($_POST['category_id'] ?? 0);
        $catSlugInput = trim($_POST['category_slug'] ?? '');
        $cleanCatSlug = slugify($catSlugInput);

        if ($categoryId <= 0 || empty($cleanCatSlug)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => 'Category slug cannot be empty.']);
                exit;
            }
            setSession('error', 'Category slug cannot be empty.');
            redirect('../manage-slugs.php?tab=categories');
        }

        $escapedCatSlug = mysqli_real_escape_string($conn, $cleanCatSlug);
        $collRes = mysqli_query($conn, "SELECT category_id FROM category WHERE category_slug = '{$escapedCatSlug}' AND category_id != {$categoryId} LIMIT 1");
        if ($collRes && mysqli_num_rows($collRes) > 0) {
            $msg = "Category slug '{$cleanCatSlug}' is already in use by another category.";
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['status' => 'error', 'message' => $msg]);
                exit;
            }
            setSession('error', $msg);
            redirect('../manage-slugs.php?tab=categories');
        }

        mysqli_query($conn, "UPDATE category SET category_slug = '{$escapedCatSlug}' WHERE category_id = {$categoryId}");
        $msg = "Category slug updated to '/category/{$cleanCatSlug}'.";
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'success', 'message' => $msg, 'category_id' => $categoryId, 'category_slug' => $cleanCatSlug]);
            exit;
        }
        setSession('success', $msg);
        redirect('../manage-slugs.php?tab=categories');
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
