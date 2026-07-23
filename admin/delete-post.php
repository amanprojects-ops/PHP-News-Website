<?php
session_start();
if (isset($_GET['check'])) {
    include_once ('app/config.php');

    $agent = mysqli_real_escape_string($conn, $_GET['check']);
    $postid = mysqli_real_escape_string($conn, base64_decode($_GET['postid']));

    $checkQ = "SELECT * FROM post WHERE post_id = '{$postid}' && postStatus = 'Y'";
    $checkR = mysqli_query($conn, $checkQ);
    if (mysqli_num_rows($checkR) > 0) {
        $_SESSION['warning'] = 'This Post is Connect Delete Post is Active.';
        echo "<script>window.location.href='../dashboard/view-post.php';</script>";
    } else {
        $check2Q = "SELECT * FROM post WHERE post_id = '{$postid}' && postStatus = 'W'";
        $check2R = mysqli_query($conn, $check2Q);
        if (mysqli_num_rows($check2R) > 0) {
            $_SESSION['warning'] = 'This Post is Connect Delete Post is Draft Save Please Rajected Frist.';
            echo "<script>window.location.href='../dashboard/view-post.php';</script>";
        } else {
            $image = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM post WHERE post_id = '{$postid}'"));
            unlink('../images/' . $image['post_img']);
            $deleteQ = "DELETE FROM `post` WHERE post_id = '{$postid}'";
            if (mysqli_query($conn, $deleteQ)) {
                $_SESSION['success'] = 'Post Delete Successsful..';
                echo "<script>window.location.href='../dashboard/view-post.php';</script>";
            } else {
                $_SESSION['error'] = 'Post Connect Delete Plz Try Again ..';
                echo "<script>window.location.href='../dashboard/view-post.php';</script>";
            }
        }
    }
}

?>