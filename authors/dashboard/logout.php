<?php 

session_start();
include_once "../app/_DBconnect.php";

session_unset();
session_destroy();

session_start();
$_SESSION['logout'] = "Session Logout Successful..";
echo "<script>window.location.href='../';</script>";

?>