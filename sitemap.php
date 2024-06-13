<?php 
require_once "./config.php";
$baseurl = $_SERVER['SERVER_NAME'];

// BaseUrl : $baseurl
// Connection Name : $conn 
// select category 
$sql = "SELECT post.post_id,post.author,
       category.category_name,category.category_id,post.category FROM post
       LEFT JOIN category ON post.category = category.category_id
       LEFT JOIN user ON post.author = user.user_id";
$select_post = mysqli_query($conn,$sql);

header("Content-type:application/xml; charset=utf-8");
echo'<!--?xml version="1.0" encoding="UTF-8"?-->'.PHP_EOL;
echo'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.PHP_EOL;
      
       echo'<sitemap>'.PHP_EOL;
       while($select_post_data = mysqli_fetch_assoc($select_post)){
       echo'<loc>'.$baseurl."/category.php?cid=".base64_encode($select_post_data['category']).'</loc>'.PHP_EOL;
       echo '<lastmod>'.date("Y-m-d H:i:s").'</lastmod>';
       echo '<chanhefreq>daily</chanhefreq>'.PHP_EOL;
       
       echo'<loc>'.$baseurl."/author.php?aid=".base64_encode($select_post_data['author']).'</loc>';
       echo '<lastmod>'.date("Y-m-d H:i:s").'</lastmod>';
       echo '<chanhefreq>daily</chanhefreq>'.PHP_EOL; 
       echo '<priority>0.8000</priority>';
       
       echo'<loc>'.$baseurl."/single.php?id=".base64_encode($select_post_data['post_id']).'</loc>';
       echo '<lastmod>'.date("Y-m-d H:i:s").'</lastmod>';
       echo '<chanhefreq>daily</chanhefreq>'.PHP_EOL; 
       echo '<priority>0.8000</priority>';
    }
       echo'</sitemap>'.PHP_EOL;
       echo'</sitemapindex>'.PHP_EOL;
/*
 echo'<sitemap>'.PHP_EOL;        
       echo'<loc>'.$baseurl.'</loc>'.PHP_EOL;
       echo '<lastmod>'.date("Y-m-d H:i:s").'</lastmod>';
       echo '<chanhefreq>daily</chanhefreq>'.PHP_EOL;
       echo '<priority>1.0000</priority>';
       echo'</sitemap>';
       echo'<sitemap>'.PHP_EOL;        
       echo'<loc>'.$baseurl."/authors/".'</loc>'.PHP_EOL;
       echo '<lastmod>'.date("Y-m-d H:i:s").'</lastmod>';
       echo '<chanhefreq>daily</chanhefreq>'.PHP_EOL;
       echo '<priority>1.0000</priority>';
       echo'</sitemap>';
*/







?>