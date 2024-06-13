<?php
session_start();
if (isset($_GET['check-cate'])) {
    include_once("../app/_DBconnect.php");
    
    $agent = mysqli_real_escape_string($conn, $_GET['check-cate']);
    $categoryid = mysqli_real_escape_string($conn, base64_decode($_GET['categoryid']));

    $checkQ = "SELECT * FROM post WHERE category = '{$categoryid}'";
    $checkR = mysqli_query($conn, $checkQ);
    if (mysqli_num_rows($checkR) > 0) {
        $_SESSION['error'] = "This Category is Connet Delete Category is Use for Post.";
        echo "<script>window.location.href='../dashboard/view-category.php';</script>";
    } else {
        $check2Q = "SELECT * FROM category WHERE category_id = '{$categoryid}' && categoryStatus = 'W'";
        $check2R = mysqli_query($conn, $check2Q);
        if (mysqli_num_rows($check2R) > 0) {
            $_SESSION['warning'] = "This Category is Connect Delete Category is Draft Save Please Rajected Frist.";
            echo "<script>window.location.href='../dashboard/view-category.php';</script>";
        } else {
            $check3Q = "SELECT * FROM category WHERE category_id = '{$categoryid}' && categoryStatus = 'Y'";
            $check3R = mysqli_query($conn, $check3Q);
            if (mysqli_num_rows($check3R) > 0) {
                $_SESSION['warning'] = "This Category is Connet Delete Category is Active.";
                echo "<script>window.location.href='../dashboard/view-category.php';</script>";
            } else {
                $deleteQ = "DELETE FROM `category` WHERE category_id = '{$categoryid}'";
                if (mysqli_query($conn, $deleteQ)) {
                    $_SESSION['success'] = "Category Delete Successsful..";
                    echo "<script>window.location.href='../dashboard/view-category.php';</script>";
                } else {
                    $_SESSION['error'] = "Category Connect Delete Plz Try Again ..";
                    echo "<script>window.location.href='../dashboard/view-category.php';</script>";
                }
            }
        }
    }
}


?>