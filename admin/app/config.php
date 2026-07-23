<?php
// Custom Functions
include_once (__DIR__ . '/../../database/functions.php');

// Database configuration
$hostname = 'localhost';
$username = 'root';
$password = '';
$dbname = 'public_paper';

$conn = mysqli_connect($hostname, $username, $password, $dbname);
if (!$conn) {
    redirect('../404.php');
}

$limit = 10;
?>