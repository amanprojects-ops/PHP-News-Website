<?php

$conn = mysqli_connect("localhost","u362577928_newsUsername","Technical@#7320","u362577928_newsDb");
if(!$conn){
    echo "<script>window.location.href='./404.php';</script>";
}
$limit = 5;
$settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings"));
$baseurl = $settings['websiteUrl'];
?>
