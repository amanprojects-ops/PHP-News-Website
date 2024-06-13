<?php
$DBserver = "localhost";
$DBuser = "u362577928_newsUsername";
$DBpassword = "Technical@#7320";
$DBname = "u362577928_newsDb";

$conn = mysqli_connect($DBserver,$DBuser,$DBpassword,$DBname);
if(!$conn){
    echo "<script>window.location.href='../404.php';</script>";
}

$baseurl = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$limit = 10;



?>