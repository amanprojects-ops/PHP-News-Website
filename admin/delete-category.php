<?php
session_start();
if (isset($_GET['check-cate'])) {
    include_once ('app/config.php');

    $agent = mysqli_real_escape_string($conn, $_GET['check-cate']);
    $categoryid = mysqli_real_escape_string($conn, base64_decode($_GET['categoryid']));

    $checkQ = "SELECT * FROM post WHERE category = '{$categoryid}'";
    $checkR = mysqli_query($conn, $checkQ);
    if (mysqli_num_rows($checkR) > 0) {
        $_SESSION['error'] = 'This category cannot be deleted as it is currently used by a post.';
        header('Location: view-category.php');
        exit();
    } else {
        $check2Q = "SELECT * FROM category WHERE category_id = '{$categoryid}' && categoryStatus = 'W'";
        $check2R = mysqli_query($conn, $check2Q);
        if (mysqli_num_rows($check2R) > 0) {
            $_SESSION['warning'] = 'Cannot delete a drafted category. Please reject it first.';
            header('Location: view-category.php');
            exit();
        } else {
            $check3Q = "SELECT * FROM category WHERE category_id = '{$categoryid}' && categoryStatus = 'Y'";
            $check3R = mysqli_query($conn, $check3Q);
            if (mysqli_num_rows($check3R) > 0) {
                $_SESSION['warning'] = 'Cannot delete an active category. Please reject it first.';
                header('Location: view-category.php');
                exit();
            } else {
                $deleteQ = "DELETE FROM `category` WHERE category_id = '{$categoryid}'";
                if (mysqli_query($conn, $deleteQ)) {
                    $_SESSION['success'] = 'Category deleted successfully.';
                    header('Location: view-category.php');
                    exit();
                } else {
                    $_SESSION['error'] = 'Failed to delete category. Please try again.';
                    header('Location: view-category.php');
                    exit();
                }
            }
        }
    }
} else {
    header('Location: view-category.php');
    exit();
}
?>