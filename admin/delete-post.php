<?php
session_start();
if (isset($_GET['check'])) {
    include_once ('app/config.php');

    $agent = mysqli_real_escape_string($conn, $_GET['check']);
    $postid = mysqli_real_escape_string($conn, base64_decode($_GET['postid']));

    $checkQ = "SELECT * FROM post WHERE post_id = '{$postid}' && postStatus = 'Y'";
    $checkR = mysqli_query($conn, $checkQ);
    if (mysqli_num_rows($checkR) > 0) {
        $_SESSION['warning'] = 'Cannot delete an active post. Please reject it first.';
        header('Location: view-post.php');
        exit();
    } else {
        $check2Q = "SELECT * FROM post WHERE post_id = '{$postid}' && postStatus = 'W'";
        $check2R = mysqli_query($conn, $check2Q);
        if (mysqli_num_rows($check2R) > 0) {
            $_SESSION['warning'] = 'Cannot delete a drafted post. Please reject it first.';
            header('Location: view-post.php');
            exit();
        } else {
            $image = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM post WHERE post_id = '{$postid}'"));
            $imagePath = '../assets/postImage/' . $image['post_img'];
            if (file_exists($imagePath) && !empty($image['post_img'])) {
                unlink($imagePath);
            }
            $deleteQ = "DELETE FROM `post` WHERE post_id = '{$postid}'";
            if (mysqli_query($conn, $deleteQ)) {
                $_SESSION['success'] = 'Post deleted successfully.';
                header('Location: view-post.php');
                exit();
            } else {
                $_SESSION['error'] = 'Failed to delete post. Please try again.';
                header('Location: view-post.php');
                exit();
            }
        }
    }
} else {
    header('Location: view-post.php');
    exit();
}
?>