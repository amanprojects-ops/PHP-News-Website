<?php
// Custom Functions
include_once (__DIR__ . '/../../database/functions.php');
include_once (__DIR__ . '/../../database/migration.php');

// Database configuration
$hostname = 'localhost';
$username = 'root';
$password = '';
$dbname = 'blog2';

$conn = mysqli_connect($hostname, $username, $password, $dbname);
if (!$conn) {
    redirect('../404.php');
}

ensureSettingsSchema($conn);

$limit = 10;
?>