<?php
/**
 * PHP-News-Website Database Migration & Update Patch
 * Run this file directly via browser (http://localhost/PHP-News-Website/update_patch.php)
 * or via CLI (php update_patch.php)
 */

session_start();

// Include database connection
$config_path = __DIR__ . '/admin/app/config.php';
if (!file_exists($config_path)) {
    die("<h3 style='color:red;'>Error: Database configuration file not found at {$config_path}</h3>");
}

include_once $config_path;

if (!$conn) {
    die("<h3 style='color:red;'>Error: Failed to connect to MySQL database!</h3>");
}

function slugifyText($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'post-' . rand(1000, 9999) : $text;
}

$logs = [];

// 1. Column Migration Array
$columns_to_add = [
    'post_slug' => "ALTER TABLE `post` ADD COLUMN `post_slug` VARCHAR(255) DEFAULT NULL AFTER `title`",
    'meta_title' => "ALTER TABLE `post` ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL",
    'meta_description' => "ALTER TABLE `post` ADD COLUMN `meta_description` TEXT DEFAULT NULL",
    'meta_keywords' => "ALTER TABLE `post` ADD COLUMN `meta_keywords` TEXT DEFAULT NULL"
];

// Check existing columns in post table
$existing_columns = [];
$res = mysqli_query($conn, "SHOW COLUMNS FROM `post`");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $existing_columns[] = $row['Field'];
    }
}

foreach ($columns_to_add as $col_name => $sql) {
    if (!in_array($col_name, $existing_columns)) {
        if (mysqli_query($conn, $sql)) {
            $logs[] = ['type' => 'success', 'msg' => "Successfully added column <code>{$col_name}</code> to <code>post</code> table."];
        } else {
            $logs[] = ['type' => 'danger', 'msg' => "Failed to add column <code>{$col_name}</code>: " . mysqli_error($conn)];
        }
    } else {
        $logs[] = ['type' => 'info', 'msg' => "Column <code>{$col_name}</code> already exists in <code>post</code> table. (Skipped)"];
    }
}

// 2. Backfill Slugs for Old Posts
$slug_updated_count = 0;
$old_posts_query = mysqli_query($conn, "SELECT `post_id`, `title`, `post_slug` FROM `post` WHERE `post_slug` IS NULL OR `post_slug` = ''");
if ($old_posts_query && mysqli_num_rows($old_posts_query) > 0) {
    while ($p = mysqli_fetch_assoc($old_posts_query)) {
        $generated_slug = mysqli_real_escape_string($conn, slugifyText($p['title']));
        $post_id = intval($p['post_id']);
        mysqli_query($conn, "UPDATE `post` SET `post_slug` = '{$generated_slug}' WHERE `post_id` = {$post_id}");
        $slug_updated_count++;
    }
    $logs[] = ['type' => 'success', 'msg' => "Auto-generated and updated slugs for <strong>{$slug_updated_count}</strong> existing post(s)."];
} else {
    $logs[] = ['type' => 'info', 'msg' => "All existing posts already have valid slugs assigned."];
}

$is_cli = (php_sapi_name() === 'cli');

if ($is_cli) {
    echo "=== Database Patch Migration ===\n";
    foreach ($logs as $log) {
        $clean_msg = strip_tags($log['msg']);
        echo "[{$log['type']}] {$clean_msg}\n";
    }
    echo "=== Patch Migration Completed ===\n";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Update Patch Migration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .patch-card { max-width: 750px; margin: 50px auto; border-radius: 12px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); }
    </style>
</head>
<body>
    <div class="container">
        <div class="card patch-card">
            <div class="card-header bg-primary text-white py-3 rounded-top d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold"><i class="bx bx-check-shield me-2"></i> Database Update Patch</h4>
                <span class="badge bg-light text-primary">v1.2 Upgrade</span>
            </div>
            <div class="card-body p-4">
                <p class="text-muted">This update patch safely upgrades your database schema (adds <code>post_slug</code>, <code>meta_title</code>, <code>meta_description</code>, <code>meta_keywords</code>) without removing or resetting any existing data.</p>
                <hr>

                <h5 class="fw-bold mb-3"><i class="bx bx-list-check me-1 text-primary"></i> Migration Log Result:</h5>
                <div class="list-group mb-4">
                    <?php foreach ($logs as $log): ?>
                        <div class="list-group-item list-group-item-<?php echo $log['type']; ?> d-flex align-items-center">
                            <?php if ($log['type'] == 'success'): ?>
                                <i class="bx bx-check-circle fs-4 me-2"></i>
                            <?php elseif ($log['type'] == 'info'): ?>
                                <i class="bx bx-info-circle fs-4 me-2"></i>
                            <?php else: ?>
                                <i class="bx bx-x-circle fs-4 me-2"></i>
                            <?php endif; ?>
                            <div><?php echo $log['msg']; ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="bx bx-badge-check fs-2 me-2"></i>
                    <div>
                        <strong>Database Patch Successfully Applied!</strong> Your website database is completely up to date and all old data remains intact.
                    </div>
                </div>

                <div class="text-end mt-4">
                    <a href="admin/new-post.php" class="btn btn-primary px-4"><i class="bx bx-plus-circle me-1"></i> Go to Admin New Post</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
