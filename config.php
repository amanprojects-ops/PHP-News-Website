<?php
include_once (__DIR__ . '/database/functions.php');
include_once (__DIR__ . '/database/migration.php');

// Database configuration
$hostname = 'localhost';
$username = 'root';
$password = '';
$dbname = 'blog2';

$conn = mysqli_connect($hostname, $username, $password, $dbname);
if (!$conn) {
    redirect('./404.php');
}

ensureSettingsSchema($conn);

$limit = 5;
$settings_query = mysqli_query($conn, 'SELECT * FROM settings');
if ($settings_query && mysqli_num_rows($settings_query) > 0) {
    $settings = mysqli_fetch_assoc($settings_query);
    $baseurl = $settings['websiteUrl'] ?? '';
} else {
    $settings = [];
    $baseurl = '';
}

if (empty($baseurl)) {
    redirect('./404.php');
}
