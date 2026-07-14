<?php 
if(isset($_POST['checkUsername'])){
    include_once('../app/_DBconnect.php');
    $username = mysqli_real_escape_string($conn,trim($_POST['username']));

    $checkQ = "SELECT username FROM user WHERE username = '{$username}'";
    $checkR = mysqli_query($conn,$checkQ);
    if(mysqli_num_rows($checkR)>0){
        echo false;
    }else{
        echo true;
    }
}


?>