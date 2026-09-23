<?php
// Function to generate social media share links
if (!function_exists('sociallink')) {
    function sociallink($url, $title, $image, $description)
    {
        $facebookLink = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url);
        $whatsappLink = 'https://api.whatsapp.com/send?text=' . urlencode($title . "\n" . $url);
        $instagramLink = 'https://www.instagram.com/?url=' . urlencode($url);
        $linkedinLink = 'https://www.linkedin.com/shareArticle?url=' . urlencode($url) . '&title=' . urlencode($title) . '&summary=' . urlencode($description) . '&source=' . urlencode($url);

        return [
            'facebook' => $facebookLink,
            'whatsapp' => $whatsappLink,
            'instagram' => $instagramLink,
            'linkedin' => $linkedinLink
        ];
    }
}

// redirect
if (!function_exists('redirect')) {
    function redirect(string $url)
    {
        header("location:$url");
        exit;
    }
}

// set session
if (!function_exists('setSession')) {
    function setSession(string $key, string $value)
    {
        $_SESSION[$key] = $value;
    }
}

// get session
if (!function_exists('getSession')) {
    function getSession(string $key)
    {
        return $_SESSION[$key] ?? null;
    }
}

// unset session
if (!function_exists('unsetSession')) {
    function unsetSession(string $key)
    {
        unset($_SESSION[$key]);
    }
}

// destroy session
if (!function_exists('destroySession')) {
    function destroySession()
    {
        session_destroy();
    }
}

// base url
if (!function_exists('base_url')) {
    function base_url()
    {
        return $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    }
}

// Helper to safely get post image with lightweight SVG fallback
if (!function_exists('getPostThumb')) {
    function getPostThumb($imgName, $baseUrl = '')
    {
        $placeholder = (!empty($baseUrl) ? rtrim($baseUrl, '/') : '.') . '/assets/images/post-placeholder.svg';
        if (empty($imgName)) {
            return $placeholder;
        }
        $cleanName = basename($imgName);
        $diskPath = dirname(__DIR__) . '/assets/postImage/' . $cleanName;
        if (!file_exists($diskPath) || filesize($diskPath) === 0) {
            return $placeholder;
        }
        return (!empty($baseUrl) ? rtrim($baseUrl, '/') : '.') . '/assets/postImage/' . htmlspecialchars($cleanName);
    }
}

// Helper to safely get website logo with fallback
if (!function_exists('getWebsiteLogo')) {
    function getWebsiteLogo($logoName, $baseUrl = '')
    {
        $placeholder = (!empty($baseUrl) ? rtrim($baseUrl, '/') : '.') . '/assets/images/logo-placeholder.svg';
        if (empty($logoName)) {
            return $placeholder;
        }
        $cleanName = basename($logoName);
        $diskPath = dirname(__DIR__) . '/assets/images/' . $cleanName;
        if (!file_exists($diskPath) || filesize($diskPath) === 0) {
            return $placeholder;
        }
        return (!empty($baseUrl) ? rtrim($baseUrl, '/') : '.') . '/assets/images/' . htmlspecialchars($cleanName);
    }
}

// Generate URL-safe slug
if (!function_exists('slugify')) {
    function slugify($text)
    {
        if (empty($text)) {
            return 'item';
        }
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text), '-'));
        if (empty($slug)) {
            $slug = 'post-' . time();
        }
        return $slug;
    }
}

// Generate clean hierarchical post URL: /{category_slug}/{post_slug}
if (!function_exists('getPostUrl')) {
    function getPostUrl($post, $baseUrl = '')
    {
        $base = !empty($baseUrl) ? rtrim($baseUrl, '/') : '';
        if (is_array($post)) {
            $catSlug = !empty($post['category_slug']) ? trim($post['category_slug']) : '';
            $postSlug = !empty($post['post_slug']) ? trim($post['post_slug']) : '';
            $postId = $post['post_id'] ?? 0;

            if (!empty($postSlug)) {
                if (!empty($catSlug)) {
                    return $base . '/' . rawurlencode($catSlug) . '/' . rawurlencode($postSlug);
                }
                return $base . '/post/' . rawurlencode($postSlug);
            }
            if (!empty($postId)) {
                return $base . '/single.php?id=' . base64_encode($postId);
            }
        }
        return $base . '/';
    }
}

// Generate clean category URL: /category/{category_slug}
if (!function_exists('getCategoryUrl')) {
    function getCategoryUrl($category, $baseUrl = '')
    {
        $base = !empty($baseUrl) ? rtrim($baseUrl, '/') : '';
        $catSlug = '';
        if (is_array($category)) {
            $catSlug = !empty($category['category_slug']) ? trim($category['category_slug']) : '';
            if (empty($catSlug) && !empty($category['category_id'])) {
                return $base . '/category.php?cid=' . base64_encode($category['category_id']);
            }
        } else {
            $catSlug = trim($category);
        }
        if (!empty($catSlug)) {
            return $base . '/category/' . rawurlencode($catSlug);
        }
        return $base . '/';
    }
}

// Generate clean author URL: /author/{username}
if (!function_exists('getAuthorUrl')) {
    function getAuthorUrl($author, $baseUrl = '')
    {
        $base = !empty($baseUrl) ? rtrim($baseUrl, '/') : '';
        $username = '';
        if (is_array($author)) {
            $username = !empty($author['username']) ? trim($author['username']) : ($author['author'] ?? '');
        } else {
            $username = trim($author);
        }
        if (!empty($username)) {
            return $base . '/author/' . rawurlencode($username);
        }
        return $base . '/';
    }
}

// Synchronize post primary slug & alias slugs into post_slugs index table
if (!function_exists('syncPostSlugs')) {
    function syncPostSlugs($conn, $postId, $primarySlug, $aliases = '')
    {
        $postId = (int)$postId;
        if ($postId <= 0 || empty($primarySlug)) {
            return false;
        }

        $cleanPrimary = slugify($primarySlug);
        $primarySafe = mysqli_real_escape_string($conn, $cleanPrimary);

        // Process aliases
        $aliasList = [];
        if (is_string($aliases) && !empty(trim($aliases))) {
            $rawAliases = explode(',', $aliases);
            foreach ($rawAliases as $al) {
                $cleaned = slugify($al);
                if (!empty($cleaned) && $cleaned !== $cleanPrimary && !in_array($cleaned, $aliasList)) {
                    $aliasList[] = $cleaned;
                }
            }
        } elseif (is_array($aliases)) {
            foreach ($aliases as $al) {
                $cleaned = slugify($al);
                if (!empty($cleaned) && $cleaned !== $cleanPrimary && !in_array($cleaned, $aliasList)) {
                    $aliasList[] = $cleaned;
                }
            }
        }

        $aliasesStr = implode(', ', $aliasList);
        $aliasesSafe = mysqli_real_escape_string($conn, $aliasesStr);

        // Update post table
        mysqli_query($conn, "UPDATE `post` SET `post_slug` = '{$primarySafe}', `slug_aliases` = '{$aliasesSafe}' WHERE `post_id` = {$postId}");

        // Delete existing slugs for this post that are neither primary nor in the new aliases list
        $keepSlugs = array_merge([$cleanPrimary], $aliasList);
        $escapedKeep = array_map(function($s) use ($conn) {
            return "'" . mysqli_real_escape_string($conn, $s) . "'";
        }, $keepSlugs);
        $keepIn = implode(',', $escapedKeep);
        mysqli_query($conn, "DELETE FROM `post_slugs` WHERE `post_id` = {$postId} AND `slug` NOT IN ({$keepIn})");

        // Upsert primary slug
        mysqli_query($conn, "INSERT INTO `post_slugs` (`post_id`, `slug`, `is_primary`) VALUES ({$postId}, '{$primarySafe}', 1) ON DUPLICATE KEY UPDATE `post_id` = {$postId}, `is_primary` = 1");

        // Upsert aliases
        foreach ($aliasList as $alias) {
            $aliasSafe = mysqli_real_escape_string($conn, $alias);
            mysqli_query($conn, "INSERT INTO `post_slugs` (`post_id`, `slug`, `is_primary`) VALUES ({$postId}, '{$aliasSafe}', 0) ON DUPLICATE KEY UPDATE `post_id` = {$postId}, `is_primary` = 0");
        }

        return true;
    }
}
?>