<?php
/**
 * Dynamic Database Schema Migration for System Settings
 * Automatically checks and ensures all required columns exist in the `settings` table.
 */

if (!function_exists('ensureSettingsSchema')) {
    function ensureSettingsSchema($conn)
    {
        static $checked = false;
        if ($checked) {
            return true;
        }

        if (!$conn) {
            return false;
        }

        // Cache check in session if session is active
        if (session_status() === PHP_SESSION_ACTIVE && isset($_SESSION['settings_schema_verified'])) {
            $checked = true;
            return true;
        }

        // Define expected columns and their definitions
        $expectedColumns = [
            'websitename'       => 'VARCHAR(60) DEFAULT NULL',
            'favicon'           => 'VARCHAR(255) DEFAULT NULL',
            'logo'              => 'VARCHAR(255) DEFAULT NULL',
            'footerdesc'        => 'VARCHAR(255) DEFAULT NULL',
            'keywords'          => 'TEXT DEFAULT NULL',
            'watterMark'        => 'VARCHAR(255) DEFAULT NULL',
            'workEmail'         => 'VARCHAR(191) DEFAULT NULL',
            'websiteTitle'      => 'VARCHAR(255) DEFAULT NULL',
            'websiteUrl'        => 'VARCHAR(999) NOT NULL DEFAULT \'\'',
            'contactPhone'      => 'VARCHAR(50) DEFAULT NULL',
            'contactAddress'    => 'TEXT DEFAULT NULL',
            'metaDescription'   => 'TEXT DEFAULT NULL',
            'metaAuthor'        => 'VARCHAR(100) DEFAULT NULL',
            'robotsIndex'       => 'VARCHAR(50) NOT NULL DEFAULT \'index, follow\'',
            'socialFacebook'    => 'VARCHAR(255) DEFAULT NULL',
            'socialTwitter'     => 'VARCHAR(255) DEFAULT NULL',
            'socialInstagram'   => 'VARCHAR(255) DEFAULT NULL',
            'socialLinkedin'    => 'VARCHAR(255) DEFAULT NULL',
            'socialYoutube'     => 'VARCHAR(255) DEFAULT NULL',
            'socialWhatsapp'    => 'VARCHAR(255) DEFAULT NULL',
            'googleAnalytics'   => 'TEXT DEFAULT NULL',
            'googleAdsense'     => 'VARCHAR(100) DEFAULT NULL',
            'customHeadCode'    => 'TEXT DEFAULT NULL',
            'customFooterCode'  => 'TEXT DEFAULT NULL',
            'maintenanceMode'   => 'TINYINT(1) NOT NULL DEFAULT 0',
            'maintenanceMsg'    => 'TEXT DEFAULT NULL',
            'mailDriver'        => 'VARCHAR(20) NOT NULL DEFAULT \'mail\'',
            'smtpHost'          => 'VARCHAR(191) DEFAULT NULL',
            'smtpPort'          => 'INT(5) NOT NULL DEFAULT 587',
            'smtpUser'          => 'VARCHAR(191) DEFAULT NULL',
            'smtpPass'          => 'TEXT DEFAULT NULL',
            'smtpEncryption'    => 'VARCHAR(10) NOT NULL DEFAULT \'tls\'',
            'smtpFromEmail'     => 'VARCHAR(191) DEFAULT NULL',
            'smtpFromName'      => 'VARCHAR(191) DEFAULT NULL'
        ];

        // Fetch existing columns in settings table
        $existingColumns = [];
        $res = mysqli_query($conn, "SHOW COLUMNS FROM `settings`");
        if ($res) {
            while ($row = mysqli_fetch_assoc($res)) {
                $existingColumns[strtolower($row['Field'])] = true;
            }
        } else {
            // Table doesn't exist yet, create it
            $createTableSql = "CREATE TABLE IF NOT EXISTS `settings` (
                `websitename` varchar(60) DEFAULT NULL,
                `favicon` varchar(255) DEFAULT NULL,
                `logo` varchar(255) DEFAULT NULL,
                `footerdesc` varchar(255) DEFAULT NULL,
                `keywords` text DEFAULT NULL,
                `watterMark` varchar(255) DEFAULT NULL,
                `workEmail` varchar(191) DEFAULT NULL,
                `websiteTitle` varchar(225) DEFAULT NULL,
                `websiteUrl` varchar(999) NOT NULL,
                `contactPhone` varchar(50) DEFAULT NULL,
                `contactAddress` text DEFAULT NULL,
                `metaDescription` text DEFAULT NULL,
                `metaAuthor` varchar(100) DEFAULT NULL,
                `robotsIndex` varchar(50) NOT NULL DEFAULT 'index, follow',
                `socialFacebook` varchar(255) DEFAULT NULL,
                `socialTwitter` varchar(255) DEFAULT NULL,
                `socialInstagram` varchar(255) DEFAULT NULL,
                `socialLinkedin` varchar(255) DEFAULT NULL,
                `socialYoutube` varchar(255) DEFAULT NULL,
                `socialWhatsapp` varchar(255) DEFAULT NULL,
                `googleAnalytics` text DEFAULT NULL,
                `googleAdsense` varchar(100) DEFAULT NULL,
                `customHeadCode` text DEFAULT NULL,
                `customFooterCode` text DEFAULT NULL,
                `maintenanceMode` tinyint(1) NOT NULL DEFAULT 0,
                `maintenanceMsg` text DEFAULT NULL,
                `mailDriver` varchar(20) NOT NULL DEFAULT 'mail',
                `smtpHost` varchar(191) DEFAULT NULL,
                `smtpPort` int(5) NOT NULL DEFAULT 587,
                `smtpUser` varchar(191) DEFAULT NULL,
                `smtpPass` text DEFAULT NULL,
                `smtpEncryption` varchar(10) NOT NULL DEFAULT 'tls',
                `smtpFromEmail` varchar(191) DEFAULT NULL,
                `smtpFromName` varchar(191) DEFAULT NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
            return mysqli_query($conn, $createTableSql);
        }

        // Add any missing columns safely
        foreach ($expectedColumns as $col => $definition) {
            if (!isset($existingColumns[strtolower($col)])) {
                $alterSql = "ALTER TABLE `settings` ADD `{$col}` {$definition}";
                mysqli_query($conn, $alterSql);
            }
        }

        // Ensure at least one default row exists
        $checkRow = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM `settings`");
        if ($checkRow) {
            $count = mysqli_fetch_assoc($checkRow)['cnt'] ?? 0;
            if ($count == 0) {
                mysqli_query($conn, "INSERT INTO `settings` (`websitename`, `websiteUrl`, `mailDriver`, `robotsIndex`, `websiteTitle`, `workEmail`, `footerdesc`) VALUES ('Earn Bro', 'https://earnbro.site', 'mail', 'index, follow', 'Earn Bro - Online Earning, Blogging & Technology Tips', 'contact@earnbro.site', 'Earn Bro © 2026. All Rights Reserved. earnbro.site')");
            }
        }

        if (session_status() === PHP_SESSION_ACTIVE) {
            $_SESSION['settings_schema_verified'] = true;
        }

        return true;
    }
}
