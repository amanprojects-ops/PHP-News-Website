<?php
/**
 * Earn Bro - Central Dynamic URL Router & Multi-Slug Engine
 * Supports clean hierarchical URLs, Category & Author routing, and 301 redirects for Alias Slugs.
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/database/functions.php';

// Extract requested relative URL path
$rawUrl = $_GET['url'] ?? '';
$cleanUrl = trim(parse_url($rawUrl, PHP_URL_PATH), '/');

if (empty($cleanUrl)) {
    include __DIR__ . '/index.php';
    exit;
}

$segments = array_values(array_filter(explode('/', $cleanUrl)));
$numSegments = count($segments);
$firstSegment = strtolower(trim($segments[0] ?? ''));
$secondSegment = isset($segments[1]) ? trim($segments[1]) : '';
$thirdSegment = isset($segments[2]) ? trim($segments[2]) : '';

// 1. Static Pages
if ($firstSegment === 'about-us' || $firstSegment === 'about') {
    include __DIR__ . '/aboutus.php';
    exit;
}

if ($firstSegment === 'contact-us' || $firstSegment === 'contact') {
    include __DIR__ . '/contactus.php';
    exit;
}

if ($firstSegment === 'sitemap.xml' || $firstSegment === 'sitemap') {
    include __DIR__ . '/sitemap.php';
    exit;
}

// 2. Category Routing: /category/{category_slug} (and optional /category/{slug}/page/{n})
if ($firstSegment === 'category') {
    if (!empty($secondSegment)) {
        $_GET['slug'] = $secondSegment;
        if ($thirdSegment === 'page' && isset($segments[3]) && is_numeric($segments[3])) {
            $_GET['page'] = (int)$segments[3];
        }
        include __DIR__ . '/category.php';
        exit;
    }
    header('Location: ' . $baseurl . '/', true, 301);
    exit;
}

// 3. Author Routing: /author/{username} (and optional /author/{username}/page/{n})
if ($firstSegment === 'author') {
    if (!empty($secondSegment)) {
        $_GET['author'] = $secondSegment;
        if ($thirdSegment === 'page' && isset($segments[3]) && is_numeric($segments[3])) {
            $_GET['page'] = (int)$segments[3];
        }
        include __DIR__ . '/author.php';
        exit;
    }
    header('Location: ' . $baseurl . '/', true, 301);
    exit;
}

// 4. Search Routing: /search/{term}
if ($firstSegment === 'search') {
    if (!empty($secondSegment)) {
        $_GET['search'] = urldecode($secondSegment);
        if ($thirdSegment === 'page' && isset($segments[3]) && is_numeric($segments[3])) {
            $_GET['page'] = (int)$segments[3];
        }
    }
    include __DIR__ . '/search.php';
    exit;
}

// 5. Explicit Post Routing: /post/{slug}
if ($firstSegment === 'post' && !empty($secondSegment)) {
    routePostSlug($conn, $secondSegment, '', $baseurl);
    exit;
}

// 6. Two-Segment Hierarchical Routing: /{category_slug}/{post_slug}
if ($numSegments === 2) {
    routePostSlug($conn, $secondSegment, $firstSegment, $baseurl);
    exit;
}

// 7. Single-Segment Fallback: /{slug}
if ($numSegments === 1) {
    // Check if it's a category slug
    $catSafe = mysqli_real_escape_string($conn, $firstSegment);
    $checkCat = mysqli_query($conn, "SELECT category_id, category_slug FROM category WHERE category_slug = '{$catSafe}' LIMIT 1");
    if ($checkCat && mysqli_num_rows($checkCat) > 0) {
        $cRow = mysqli_fetch_assoc($checkCat);
        header('Location: ' . $baseurl . '/category/' . rawurlencode($cRow['category_slug']), true, 301);
        exit;
    }

    // Check if it's a post slug
    routePostSlug($conn, $firstSegment, '', $baseurl);
    exit;
}

// Fallback: 404
header("HTTP/1.0 404 Not Found");
include __DIR__ . '/404.php';
exit;

/**
 * Resolve post slug and handle 301 redirects for alias/historical slugs
 */
function routePostSlug($conn, $requestedSlug, $requestedCatSlug = '', $baseurl = '') {
    $cleanSlug = strtolower(trim($requestedSlug));
    $safeSlug = mysqli_real_escape_string($conn, $cleanSlug);

    // Query post_slugs indexed table with post and category joins
    $query = "SELECT ps.post_id, ps.slug AS matched_slug, ps.is_primary,
                     p.title, p.post_slug AS primary_slug, p.category, p.postStatus,
                     c.category_slug, c.category_name
              FROM post_slugs ps
              JOIN post p ON ps.post_id = p.post_id
              LEFT JOIN category c ON p.category = c.category_id
              WHERE ps.slug = '{$safeSlug}'
              LIMIT 1";
    $res = mysqli_query($conn, $query);

    // Fallback search directly on post table in case post_slugs wasn't backfilled for this post
    if (!$res || mysqli_num_rows($res) === 0) {
        $queryDirect = "SELECT p.post_id, p.post_slug AS matched_slug, 1 AS is_primary,
                               p.title, p.post_slug AS primary_slug, p.category, p.postStatus,
                               c.category_slug, c.category_name
                        FROM post p
                        LEFT JOIN category c ON p.category = c.category_id
                        WHERE p.post_slug = '{$safeSlug}'
                        LIMIT 1";
        $res = mysqli_query($conn, $queryDirect);
    }

    if (!$res || mysqli_num_rows($res) === 0) {
        header("HTTP/1.0 404 Not Found");
        include __DIR__ . '/404.php';
        exit;
    }

    $post = mysqli_fetch_assoc($res);

    // If post status is not published ('Y'), restrict to logged-in users
    if ($post['postStatus'] !== 'Y') {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (empty($_SESSION['username'])) {
            header("HTTP/1.0 404 Not Found");
            include __DIR__ . '/404.php';
            exit;
        }
    }

    $canonicalCat = !empty($post['category_slug']) ? $post['category_slug'] : 'post';
    $canonicalSlug = $post['primary_slug'];
    $canonicalUrl = rtrim($baseurl, '/') . '/' . rawurlencode($canonicalCat) . '/' . rawurlencode($canonicalSlug);

    // Check if 301 Permanent Redirect is required:
    // 1) Accessed via an alias/old slug (is_primary == 0)
    // 2) Accessed with wrong category slug in URL
    // 3) Accessed via direct /post/{slug} or /{slug} without category
    $needsRedirect = false;

    if ((int)$post['is_primary'] === 0) {
        $needsRedirect = true;
    } elseif (!empty($requestedCatSlug) && strtolower($requestedCatSlug) !== strtolower($canonicalCat)) {
        $needsRedirect = true;
    } elseif (empty($requestedCatSlug) && !empty($canonicalCat)) {
        $needsRedirect = true;
    }

    if ($needsRedirect) {
        header('Location: ' . $canonicalUrl, true, 301);
        exit;
    }

    // Set variables and serve single post
    $_GET['slug'] = $canonicalSlug;
    $_GET['resolved_post_id'] = $post['post_id'];
    $_GET['id'] = base64_encode($post['post_id']); // Backward compatibility
    include __DIR__ . '/single.php';
    exit;
}
