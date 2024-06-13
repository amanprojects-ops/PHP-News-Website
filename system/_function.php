<?php // Function to generate social media share links
function sociallink($url, $title, $image, $description) {
    // Facebook
    $facebookLink = "https://www.facebook.com/sharer/sharer.php?u=" . urlencode($url);

    // WhatsApp
    $whatsappLink = "https://api.whatsapp.com/send?text=" . urlencode($title . "\n" . $url);

    // Instagram
    $instagramLink = "https://www.instagram.com/?url=" . urlencode($url);

    // LinkedIn
    $linkedinLink = "https://www.linkedin.com/shareArticle?url=" . urlencode($url) . "&title=" . urlencode($title) . "&summary=" . urlencode($description) . "&source=" . urlencode($url);

    // You can add more social media platforms as needed

    $array = ['facebook' => $facebookLink,'whatsapp' => $whatsappLink,'instagram' => $instagramLink,'linkedin' => $linkedinLink];
    return $array;
}?>