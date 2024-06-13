<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitoer Counter</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
</head>

<body>
<?php
include_once "config.php";
$ip = $_SERVER['REMOTE_ADDR'];
$sql = "SELECT * FROM visitors WHERE ip='$ip'";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    $sql = "INSERT INTO visitors (ip) VALUES ('$ip')";
    $conn->query($sql);
}
$sql = "SELECT COUNT(DISTINCT ip) as count FROM visitors";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$count = $row['count'];

echo "Number of unique visitors: " . $count;
?>
</body>

</html>


<!-- 

Array
(
    [LSPHP_ProcessGroup] => on
    [PATH] => /usr/local/bin:/bin:/usr/bin
    [HTTP_ACCEPT] => text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9
    [HTTP_ACCEPT_ENCODING] => gzip, deflate, br
    [HTTP_ACCEPT_LANGUAGE] => en-IN,en-GB;q=0.9,en-US;q=0.8,en;q=0.7
    [HTTP_HOST] => connectbihar.in
    [HTTP_REFERER] => https://connectbihar.in/
    [HTTP_USER_AGENT] => Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36
    [HTTP_CACHE_CONTROL] => max-age=0
    [HTTP_SEC_CH_UA] => "Not_A Brand";v="99", "Google Chrome";v="109", "Chromium";v="109"
    [HTTP_SEC_CH_UA_MOBILE] => ?0
    [HTTP_SEC_CH_UA_PLATFORM] => "Windows"
    [HTTP_DNT] => 1
    [HTTP_UPGRADE_INSECURE_REQUESTS] => 1
    [HTTP_SEC_FETCH_SITE] => same-origin
    [HTTP_SEC_FETCH_MODE] => navigate
    [HTTP_SEC_FETCH_USER] => ?1
    [HTTP_SEC_FETCH_DEST] => document
    [DOCUMENT_ROOT] => /home/u532579921/domains/connectbihar.in/public_html
    [REMOTE_ADDR] => 2401:4900:3cb3:dba1:60ec:c4b8:3d37:591b
    [REMOTE_PORT] => 61145
    [SERVER_ADDR] => 2a02:4780:11:772:0:1fbe:8651:1
    [SERVER_NAME] => connectbihar.in
    [SERVER_ADMIN] => 
    [SERVER_PORT] => 443
    [REQUEST_SCHEME] => https
    [REQUEST_URI] => /
    [HTTPS] => on
    [CRAWLER_USLEEP] => 1000
    [CRAWLER_LOAD_LIMIT_ENFORCE] => 25
    [X_SPDY] => HTTP3
    [SSL_PROTOCOL] => QUIC
    [SSL_CIPHER] => TLS_AES_128_GCM_SHA256
    [SSL_CIPHER_USEKEYSIZE] => 128
    [SSL_CIPHER_ALGKEYSIZE] => 128
    [SCRIPT_FILENAME] => /home/u532579921/domains/connectbihar.in/public_html/index.php
    [QUERY_STRING] => 
    [SCRIPT_URI] => https://connectbihar.in/
    [SCRIPT_URL] => /
    [SCRIPT_NAME] => /index.php
    [SERVER_PROTOCOL] => HTTP/1.1
    [SERVER_SOFTWARE] => LiteSpeed
    [REQUEST_METHOD] => GET
    [X-LSCACHE] => on,crawler,esi,combine
    [PHP_SELF] => /index.php
    [REQUEST_TIME_FLOAT] => 1676195259.9162
    [REQUEST_TIME] => 1676195259
)

 -->