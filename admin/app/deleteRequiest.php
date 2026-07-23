<?php
session_start();
include_once('_DBconnect.php');

if (!isset($_SESSION['username'])) {
    header("Location: ../index.php");
    exit();
}

// Handle delete requests dynamically
if (isset($_POST['action']) && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);
    $action = mysqli_real_escape_string($conn, $_POST['action']);
    
    if ($action === 'deletePost' && ($_SESSION['role'] == 1 || $_SESSION['role'] == 2)) {
        $query = "DELETE FROM post WHERE post_id = '{$id}'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Post deleted successfully.";
        } else {
            $_SESSION['warning'] = "Failed to delete post.";
        }
    } elseif ($action === 'deleteCategory' && $_SESSION['role'] == 1) {
        $query = "DELETE FROM category WHERE category_id = '{$id}'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "Category deleted successfully.";
        } else {
            $_SESSION['warning'] = "Failed to delete category.";
        }
    } elseif ($action === 'deleteUser' && $_SESSION['role'] == 1) {
        $query = "DELETE FROM user WHERE user_id = '{$id}'";
        if (mysqli_query($conn, $query)) {
            $_SESSION['message'] = "User deleted successfully.";
        } else {
            $_SESSION['warning'] = "Failed to delete user.";
        }
    }
}
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
