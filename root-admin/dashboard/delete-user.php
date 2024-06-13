<?php session_start(); 
if(isset($_GET['check-user'])){
    // Trying To the database
    include_once("../app/_DBconnect.php");
    // Store to the dynamic url data in variable -------------------
    $userid = mysqli_real_escape_string($conn,trim(base64_decode($_GET['userid'])));
    $sessin_user = mysqli_real_escape_string($conn,trim(base64_decode($_GET['check-user'])));
    // Check in to the user used on user data in post table
    $checkQ = "SELECT * FROM post WHERE author = '{$userid}'";
    // Run Check Query in post table
    $checkR = mysqli_query($conn,$checkQ);
    // find to the post table result in post table and filter 
    if(mysqli_num_rows($checkR)>0){
        $_SESSION['warning'] = "<b>Warning</b> User Data used for post. Try Again.";
        echo "<script>window.location.href='../dashboard/view-user.php';</script>";
    }else{
        // Check User for Delete permission only for owner
        if($sessin_user != 1){
            $_SESSION['warning'] = "<b>Sorry</b> Your are not a Super Admin Or Website Owner Try Again.";
            echo "<script>window.location.href='../dashboard/view-user.php';</script>";
        }
        else{
            if($_SESSION['author_id'] != $sessin_user){
                $_SESSION['warning'] = "<b>Sorry</b> Session are not proper. Please Try Again.";
                echo "<script>window.location.href='../dashboard/view-user.php';</script>";
            }else{
                $deleteQ = "DELETE FROM `user` WHERE user_id = '{$userid}'";
                if (mysqli_query($conn, $deleteQ)) {
                    $_SESSION['success'] = "User Delete Successsful..";
                    echo "<script>window.location.href='../dashboard/view-user.php';</script>";
                } else {
                    $_SESSION['error'] = "User Connect Delete Plz Try Again ..";
                    echo "<script>window.location.href='../dashboard/view-user.php';</script>";
                }
            }
        }
    }
}

?>