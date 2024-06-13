<?php
include "config.php";
if(empty($baseurl) != 0){
    echo "<script>window.location.href='404.php';</script>";
}
else{
 echo "<script>window.location.href='../';</script>";   
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Under Maintenance</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            color: #333;
            text-align: center;
            margin: 50px;
        }

        .container {
            max-width: 600px;
            margin: auto;
        }

        .logo {
            width: 400;
            height: 300;
            margin-bottom: 20px;
            border-radius: 15px;
        }

        h1 {
            color: #ff4500;
        }
    </style>
</head>
<body>
    <div class="container">
        <img class="logo" src="./underMentenance.png" Width='400' height='300' alt="Under Maintenance">
        <!--<h1></h1>-->
        <p>We're sorry for the inconvenience. The site is currently undergoing maintenance.<br>Please check back later.</p>
    </div>
</body>
</html>
