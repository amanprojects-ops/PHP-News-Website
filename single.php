<?php include 'header.php'; ?>
<div id="main-content">
    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <!-- post-container -->
                <div class="post-container overflow-auto">
                    <?php
                    include 'config.php';
                    include_once './database/functions.php';
                    $settingd = mysqli_fetch_assoc(mysqli_query($conn, 'SELECT websiteUrl FROM settings'));
                    if (isset($_GET['resolved_post_id'])) {
                        $postId = (int)$_GET['resolved_post_id'];
                        $sql = "SELECT post.*, category.category_name, category.category_slug, user.username, user.user_id 
                                FROM post
                                LEFT JOIN category ON post.category = category.category_id
                                LEFT JOIN user ON post.author = user.user_id
                                WHERE post.post_id = {$postId}";
                    } elseif (isset($_GET['slug'])) {
                        $slugSafe = mysqli_real_escape_string($conn, strtolower(trim($_GET['slug'])));
                        $sql = "SELECT post.*, category.category_name, category.category_slug, user.username, user.user_id 
                                FROM post
                                LEFT JOIN category ON post.category = category.category_id
                                LEFT JOIN user ON post.author = user.user_id
                                WHERE post.post_slug = '{$slugSafe}'";
                    } elseif (isset($_GET['id'])) {
                        $post_id = mysqli_real_escape_string($conn, base64_decode($_GET['id']));
                        $sql = "SELECT post.*, category.category_name, category.category_slug, user.username, user.user_id 
                                FROM post
                                LEFT JOIN category ON post.category = category.category_id
                                LEFT JOIN user ON post.author = user.user_id
                                WHERE post.post_id = '{$post_id}' && postStatus = 'Y'";
                    } else {
                        $sql = "SELECT post.*, category.category_name, category.category_slug, user.username, user.user_id 
                                FROM post
                                LEFT JOIN category ON post.category = category.category_id
                                LEFT JOIN user ON post.author = user.user_id WHERE postStatus = 'Y' ORDER BY post.post_id DESC LIMIT 1";
                    }

                    $result = mysqli_query($conn, $sql);
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $postCanonicalUrl = getPostUrl($row, $baseurl);
                            $url = !empty($postCanonicalUrl) ? $postCanonicalUrl : ($settingd['websiteUrl'] ?? $baseurl);
                            $title = $row['title'];
                            $image = getPostThumb($row['post_img'], $baseurl);
                            $description = $row['description'];

                            $sociallink = sociallink($url, $title, $image, $description);
                            ?>
                            <div class="post-content single-post">
                                <h3><?php echo @$row['title']; ?></h3>
                                <div class="card-body">
                                    <div class="post-information">
                                        <span>
                                            <i class="fa fa-tags" aria-hidden="true"></i>
                                            <a href='<?php echo getCategoryUrl($row['category_slug'] ?? $row['category'], $baseurl); ?>'><?php echo htmlspecialchars($row['category_name'] ?? ''); ?></a>
                                        </span>
                                        <span>
                                            <i class="fa fa-user" aria-hidden="true"></i>
                                            <a href='<?php echo getAuthorUrl($row['username'], $baseurl); ?>'><?php echo htmlspecialchars($row['username'] ?? ''); ?></a>
                                        </span>
                                        <span>
                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                            <?php echo @$row['post_date']; ?>
                                        </span>
                                        <span>
                                            <a href="<?php echo $sociallink['instagram']; ?>" target='_blank'><i class="fa fa-instagram" aria-hidden="true"></i>instagram</a>
                                        </span>
                                        
                                        <span>
                                            <a href='<?php echo $sociallink['whatsapp']; ?>' target='_blank'><i class="fa fa-whatsapp" aria-hidden="true"></i>Whatsapp</a>
                                        </span>
                                        <span>
                                            <a href='<?php echo $sociallink['facebook']; ?>' target='_blank'><i class="fa fa-facebook" aria-hidden="true"></i>Facebook</a>
                                        </span>
                                        <!--<span>-->
                                        <!--    <a hreg='<?php // echo $sociallink['telegram']; ?>' target='_blank'><i class="fa fa-telegram" aria-hidden="true"></i>Telegram</a>-->
                                        <!--</span>-->
                                        <span>
                                            <a href='<?php echo $sociallink['linkedin']; ?>' target='_blank'><i class="fa fa-linkedin" aria-hidden="true"></i>Linkedin</a>
                                        </span>
                                    </div>
                                    <img class="single-feature-image" loading="lazy" src="<?php echo getPostThumb(@$row['post_img'], $baseurl); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" onerror="this.onerror=null;this.src='<?php echo $baseurl; ?>/assets/images/post-placeholder.svg';" />
                                    <p class="description">
                                        <?php echo @$row['description']; ?>
                                    </p>
                                </div>

                            </div>
                        <?php
                        }
                    } else {
                        echo '<h2>Post Article can&#8216;t Found.</h2><hr>';
                        ?>

                        <div class="row">
                            <?php
                            include 'config.php';

                            /* Calculate Offset Code */
                            // $limit = 3;
                            $sql = "SELECT post.post_id, post.title, post.post_date,
                                            category.category_name,post.category,post.post_img FROM post
                                            LEFT JOIN category ON post.category = category.category_id where postStatus = 'Y'
                                            ORDER BY post.post_id DESC LIMIT {$limit}";

                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    ?>
                                    <div class="col-sm-4 mb-2">
                                        <div class="card">
                                            <div class="card-body">

                                                <div class="recent-post">
                                                    <a class="post-img" href="single.php?id=<?php echo base64_encode(@$row['post_id']); ?>">
                                                        <img class="card-img" loading="lazy" src="<?php echo getPostThumb(@$row['post_img'], $baseurl); ?>" alt="<?php echo htmlspecialchars(substr(@$row['title'], 0, 50)); ?>" style="width: 14rem; min-height: 7.5rem; object-fit: cover;" onerror="this.onerror=null;this.src='<?php echo $baseurl; ?>/assets/images/post-placeholder.svg';" />
                                                    </a>
                                                    <div class="post-content">
                                                        <h5><a href="single.php?id=<?php echo base64_encode(@$row['post_id']); ?>"><?php echo substr(@$row['title'], 0, 50); ?></a></h5>
                                                        <span>
                                                            <i class="fa fa-tags" aria-hidden="true"></i>
                                                            <a href='category.php?cid=<?php echo base64_encode(@$row['category']); ?>'><?php echo @$row['category_name']; ?></a>
                                                        </span>
                                                        <span>
                                                            <i class="fa fa-calendar" aria-hidden="true"></i>
                                                            <?php echo @$row['post_date']; ?>
                                                        </span>
                                                        <a class="read-more" href="single.php?id=<?php echo base64_encode(@$row['post_id']); ?>">read more</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                            <?php }
                            } ?>
                        </div>

                    <?php } ?>
                </div>
                <!-- /post-container -->
            </div>
            <?php include 'sidebar.php'; ?>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>