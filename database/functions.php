<?php  // Function to generate social media share links
function sociallink($url, $title, $image, $description)
{
    // Facebook
    $facebookLink = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url);

    // WhatsApp
    $whatsappLink = 'https://api.whatsapp.com/send?text=' . urlencode($title . "\n" . $url);

    // Instagram
    $instagramLink = 'https://www.instagram.com/?url=' . urlencode($url);

    // LinkedIn
    $linkedinLink = 'https://www.linkedin.com/shareArticle?url=' . urlencode($url) . '&title=' . urlencode($title) . '&summary=' . urlencode($description) . '&source=' . urlencode($url);

    // You can add more social media platforms as needed

    $array = ['facebook' => $facebookLink, 'whatsapp' => $whatsappLink, 'instagram' => $instagramLink, 'linkedin' => $linkedinLink];
    return $array;
}

// redirect
function redirect(string $url)
{
    header("location:$url");
    exit;  // set session
}

// set session
function setSession(string $key, string $value)
{
    $_SESSION[$key] = $value;
}

// get session
function getSession(string $key)
{
    return $_SESSION[$key] ?? null;
}

// unset session
function unsetSession(string $key)
{
    unset($_SESSION[$key]);
}

// destroy session
function destroySession()
{
    session_destroy();
}

// base url
function base_url()
{
    return $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
}

// Helper to safely get post image with lightweight SVG fallback
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

// Helper to safely get website logo with fallback
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

?>