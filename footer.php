<div id ="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
              <?php
                include_once("config.php");$setting = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM settings LIMIT 1")); ?>
                <span><?php echo "&copy; All rights reserved 2019 - " . date("Y"). " " . @$setting['footerdesc']; ?></span>
             
            </div>
        </div>
        <div class='d-flex justify-content-around my-3'>
            <a href="./contactus.php" class="badge badge-light">Contact Us</a>
            <a href="./aboutus.php" class="badge badge-light">About Us</a>
            <a href="./disclaimer.php" class="badge badge-light">Disclaimer</a>
            <a href="./privacy-policy.php" class="badge badge-light">Privacy Policy</a>
        </div>
    </div>
</div>
</body>
</html>
