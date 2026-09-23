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
    $savedUrl = $settings['websiteUrl'] ?? 'https://earnbro.site';
} else {
    $settings = [];
    $savedUrl = 'https://earnbro.site';
}

// Adapt baseurl for local development without breaking production domain
$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = in_array($httpHost, ['localhost', '127.0.0.1', '::1']) || strpos($httpHost, '192.168.') === 0;

if ($isLocal) {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $projectDir = basename(__DIR__);
    $baseurl = $protocol . '://' . $httpHost . '/' . $projectDir;
} else {
    $baseurl = !empty($savedUrl) ? rtrim($savedUrl, '/') : 'https://earnbro.site';
}

if (empty($baseurl)) {
    redirect('./404.php');
}
