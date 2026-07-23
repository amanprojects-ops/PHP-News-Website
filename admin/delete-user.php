<?php
session_start();
if (isset($_GET['check-user'])) {
    // Trying To the database
    include_once ('app/config.php');
    // Store to the dynamic url data in variable -------------------
    $userid = mysqli_real_escape_string($conn, trim(base64_decode($_GET['userid'])));
    $sessin_user = mysqli_real_escape_string($conn, trim(base64_decode($_GET['check-user'])));
    // Check in to the user used on user data in post table
    $checkQ = "SELECT * FROM post WHERE author = '{$userid}'";
    // Run Check Query in post table
    $checkR = mysqli_query($conn, $checkQ);
    // find to the post table result in post table and filter
    if (mysqli_num_rows($checkR) > 0) {
        $_SESSION['warning'] = '<b>Warning:</b> User data is currently associated with a post. Cannot delete.';
        header('Location: view-user.php');
        exit();
    } else {
        // Check User for Delete permission only for owner
        if ($sessin_user != 1) {
            $_SESSION['warning'] = '<b>Sorry:</b> You do not have Super Admin privileges to delete this user.';
            header('Location: view-user.php');
            exit();
        } else {
            if ($_SESSION['author_id'] != $sessin_user) {
                $_SESSION['warning'] = '<b>Sorry:</b> Invalid session data. Please try again.';
                header('Location: view-user.php');
                exit();
            } else {
                $deleteQ = "DELETE FROM `user` WHERE user_id = '{$userid}'";
                if (mysqli_query($conn, $deleteQ)) {
                    $_SESSION['success'] = 'User deleted successfully.';
                    header('Location: view-user.php');
                    exit();
                } else {
                    $_SESSION['error'] = 'Failed to delete user. Please try again.';
                    header('Location: view-user.php');
                    exit();
                }
            }
        }
    }
} else {
    header('Location: view-user.php');
    exit();
}
?>