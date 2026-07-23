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

?>