<?php include 'header.php'; ?>
<div id="main-content">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <!-- post-container -->
        <div class="post-container">
          <?php
          include "config.php";
          $auth_id = 0;
          $authorData = null;

          if (isset($_GET['author'])) {
            $authorUsernameSafe = mysqli_real_escape_string($conn, trim($_GET['author']));
            $userQ = mysqli_query($conn, "SELECT * FROM user WHERE username = '{$authorUsernameSafe}' LIMIT 1");
            if ($userQ && mysqli_num_rows($userQ) > 0) {
              $authorData = mysqli_fetch_assoc($userQ);
              $auth_id = (int)$authorData['user_id'];
            }
          } elseif (isset($_GET['aid'])) {
            $auth_id = (int)mysqli_real_escape_string($conn, base64_decode($_GET['aid']));
            $userQ = mysqli_query($conn, "SELECT * FROM user WHERE user_id = {$auth_id} LIMIT 1");
            if ($userQ && mysqli_num_rows($userQ) > 0) {
              $authorData = mysqli_fetch_assoc($userQ);
            }
          }

          if ($authorData) {
            $authorName = htmlspecialchars($authorData['username']);
          ?>
            <h2 class="page-heading text-uppercase">Posts By <?php echo $authorName; ?></h2>
            <?php

            /* Calculate Offset Code */
            $limit = 5;
            if (isset($_GET['page'])) {
              $page = (int)$_GET['page'];
            } else {
              $page = 1;
            }
            $offset = ($page - 1) * $limit;

            $sql = "SELECT post.post_id, post.title, post.post_slug, post.description, post.sort_details, post.post_date, post.author,
                  category.category_name, category.category_slug, user.username, post.category, post.post_img FROM post
                  LEFT JOIN category ON post.category = category.category_id
                  LEFT JOIN user ON post.author = user.user_id
                  WHERE post.author = {$auth_id} && postStatus = 'Y'
                  ORDER BY post.post_id DESC LIMIT {$offset},{$limit}";

            $result = mysqli_query($conn, $sql);
            if ($result && mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) {
                $postUrl = getPostUrl($row, $baseurl);
                $catUrl = getCategoryUrl($row, $baseurl);
                $authorUrl = getAuthorUrl($row['username'], $baseurl);
            ?>
                <div class="post-content">
                  <div class="row">
                    <div class="col-md-4">
                      <a class="post-img" href="<?php echo $postUrl; ?>"><img loading="lazy" src="<?php echo getPostThumb($row['post_img'], $baseurl); ?>" alt="<?php echo htmlspecialchars($row['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='<?php echo $baseurl; ?>/assets/images/post-placeholder.svg';" /></a>
                    </div>
                    <div class="col-md-8">
                      <div class="inner-content clearfix">
                        <h3><a href='<?php echo $postUrl; ?>'><?php echo htmlspecialchars($row['title']); ?></a></h3>
                        <div class="post-information">
                          <span>
                            <i class="fa fa-tags" aria-hidden="true"></i>
                            <a href='<?php echo $catUrl; ?>'><?php echo htmlspecialchars($row['category_name']); ?></a>
                          </span>
                          <span>
                            <i class="fa fa-user" aria-hidden="true"></i>
                            <a href='<?php echo $authorUrl; ?>'><?php echo htmlspecialchars($row['username']); ?></a>
                          </span>
                          <span>
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                            <?php echo @$row['post_date']; ?>
                          </span>
                        </div>
                        <p class="description"><hr>
                           <?php echo substr(@$row['sort_details'],0,150)." ..."; ?>
                        </p>
                        <a class='read-more pull-right' href='<?php echo $postUrl; ?>'>read more</a>
                      </div>
                    </div>
                  </div>
                </div>
          <?php
              }
            } else {
              echo "<h2>No Record Found.</h2>";
            }

            // show pagination
            $countSql = "SELECT COUNT(*) AS total FROM post WHERE author = {$auth_id} && postStatus = 'Y'";
            $countRes = mysqli_query($conn, $countSql);
            $total_records = ($countRes) ? (int)mysqli_fetch_assoc($countRes)['total'] : 0;

            if ($total_records > 0) {
              $total_page = ceil($total_records / $limit);
              if ($total_page > 1) {
                $pageBase = !empty($authorData['username']) ? ($baseurl . '/author/' . rawurlencode($authorData['username']) . '?page=') : ('?aid=' . base64_encode($auth_id) . '&page=');

                echo '<ul class="pagination admin-pagination">';
                if ($page > 1) {
                  echo '<li><a href="' . $pageBase . ($page - 1) . '">Prev</a></li>';
                }
                for ($i = 1; $i <= $total_page; $i++) {
                  $active = ($i == $page) ? "active" : "";
                  echo '<li class="' . $active . '"><a href="' . $pageBase . $i . '">' . $i . '</a></li>';
                }
                if ($total_page > $page) {
                  echo '<li><a href="' . $pageBase . ($page + 1) . '">Next</a></li>';
                }
                echo '</ul>';
              }
            }
          } else {
            echo "<h2>Author Not Found.</h2>";
          }
          ?>
        </div><!-- /post-container -->
      </div>
      <?php include 'sidebar.php'; ?>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>