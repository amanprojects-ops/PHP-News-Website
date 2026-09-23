<?php include 'header.php'; ?>
<div id="main-content">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <!-- post-container -->
        <div class="post-container">
          <?php
          include "config.php";
          /* Calculate Offset Code */
          if (isset($_GET['page'])) {
            $page = $_GET['page'];
          } else {
            $page = 1;
          }
          $offset = ($page - 1) * $limit;

          $cat_id = 0;
          $categoryD = null;

          if (isset($_GET['slug'])) {
            $catSlugSafe = mysqli_real_escape_string($conn, strtolower(trim($_GET['slug'])));
            $categoryQ = "SELECT * FROM category WHERE category_slug = '{$catSlugSafe}' LIMIT 1";
            $categoryR = mysqli_query($conn, $categoryQ);
            if ($categoryR && mysqli_num_rows($categoryR) > 0) {
              $categoryD = mysqli_fetch_assoc($categoryR);
              $cat_id = (int)$categoryD['category_id'];
            }
          } elseif (isset($_GET['cid'])) {
            $cat_id = (int)mysqli_real_escape_string($conn, base64_decode($_GET['cid']));
            $categoryQ = "SELECT * FROM category WHERE category_id = {$cat_id} LIMIT 1";
            $categoryR = mysqli_query($conn, $categoryQ);
            if ($categoryR && mysqli_num_rows($categoryR) > 0) {
              $categoryD = mysqli_fetch_assoc($categoryR);
            }
          }

          if ($cat_id > 0) {
            $postQ = "SELECT post.post_id, post.title, post.post_slug, post.description, post.sort_details, post.post_date, post.author,
                      category.category_name, category.category_slug, user.username, post.category, post.post_img FROM post
                      LEFT JOIN category ON post.category = category.category_id
                      LEFT JOIN user ON post.author = user.user_id
                      WHERE post.category = {$cat_id} && postStatus = 'Y'
                      ORDER BY post.post_id DESC LIMIT {$offset},{$limit}";
          } else {
            $postQ = "SELECT post.post_id, post.title, post.post_slug, post.description, post.sort_details, post.post_date, post.author,
                      category.category_name, category.category_slug, user.username, post.category, post.post_img FROM post
                      LEFT JOIN category ON post.category = category.category_id
                      LEFT JOIN user ON post.author = user.user_id WHERE postStatus = 'Y'
                      ORDER BY post.post_id DESC LIMIT {$offset},{$limit}";
          }

          $postR = mysqli_query($conn, $postQ);
          if ($categoryD) {
            echo "<h2 class='page-heading text-uppercase'>" . htmlspecialchars($categoryD['category_name']) . "</h2>";
          } else {
            echo "<h2 class='page-heading text-uppercase'>All Categories</h2>";
          }

          ?>

          <?php
          if ($postR && mysqli_num_rows($postR) > 0) {
            while ($postD = mysqli_fetch_assoc($postR)) {
              $postUrl = getPostUrl($postD, $baseurl);
              $catUrl = getCategoryUrl($postD, $baseurl);
              $authorUrl = getAuthorUrl($postD['username'], $baseurl);
          ?>
              <div class="post-content">
                <div class="row">
                  <div class="col-md-4">
                    <a class="post-img" href="<?php echo $postUrl; ?>"><img loading="lazy" src="<?php echo getPostThumb(@$postD['post_img'], $baseurl); ?>" alt="<?php echo htmlspecialchars(substr(@$postD['title'],0,100)); ?>" style="width: 100%; height: 100%; object-fit: cover;" onerror="this.onerror=null;this.src='<?php echo $baseurl; ?>/assets/images/post-placeholder.svg';" /></a>
                  </div>
                  <div class="col-md-8">
                    <div class="inner-content clearfix">
                      <h3><a href='<?php echo $postUrl; ?>'><?php echo htmlspecialchars(@$postD['title']); ?></a></h3>
                      <div class="post-information">
                        <span>
                          <i class="fa fa-tags" aria-hidden="true"></i>
                          <a href='<?php echo $catUrl; ?>'><?php echo htmlspecialchars(@$postD['category_name']); ?></a>
                        </span>
                        <span>
                          <i class="fa fa-user" aria-hidden="true"></i>
                          <a href='<?php echo $authorUrl; ?>'><?php echo htmlspecialchars(@$postD['username']); ?></a>
                        </span>
                        <span>
                          <i class="fa fa-calendar" aria-hidden="true"></i>
                          <?php echo @$postD['post_date']; ?>
                        </span>
                      </div>
                      <p class="description"><hr>
                        <?php echo substr(@$postD['sort_details'], 0, 130) . "..."; ?>
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

          // Pagination
          $countSql = ($cat_id > 0) ? "SELECT COUNT(*) AS total FROM post WHERE category = {$cat_id} && postStatus = 'Y'" : "SELECT COUNT(*) AS total FROM post WHERE postStatus = 'Y'";
          $countRes = mysqli_query($conn, $countSql);
          $total_records = ($countRes) ? (int)mysqli_fetch_assoc($countRes)['total'] : 0;

          if ($total_records > 0) {
            $total_page = ceil($total_records / $limit);
            if ($total_page > 1) {
              $pageBase = '';
              if ($categoryD && !empty($categoryD['category_slug'])) {
                $pageBase = $baseurl . '/category/' . rawurlencode($categoryD['category_slug']) . '?page=';
              } elseif ($cat_id > 0) {
                $pageBase = '?cid=' . base64_encode($cat_id) . '&page=';
              } else {
                $pageBase = '?page=';
              }

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
          ?>
        </div><!-- /post-container -->
      </div>
      <?php include 'sidebar.php'; ?>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>