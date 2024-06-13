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

          if (isset($_GET['cid'])) {
            // FETCH URL DATA IN CATEGORY ID IN URL =============================
            $cat_id = mysqli_real_escape_string($conn, base64_decode($_GET['cid']));
            // TOTAL COUNT OF POST IN DATABASE FOR ACTIVE POST/ARTICLE ============
            $categoryQ = "SELECT * FROM category WHERE category_id = {$cat_id}";
            $categoryR = mysqli_query($conn, $categoryQ);
            $categoryD = mysqli_fetch_assoc($categoryR);

            // Loading Post on database ===========================================
            $postQ = "SELECT post.post_id, post.title, post.description,post.sort_details,post.post_date,post.author,
                      category.category_name,user.username,post.category,post.post_img FROM post
                      LEFT JOIN category ON post.category = category.category_id
                      LEFT JOIN user ON post.author = user.user_id
                      WHERE post.category = {$cat_id} &&  postStatus = 'Y'
                      ORDER BY post.post_id DESC LIMIT {$offset},{$limit}";
          } else {

            $postQ = "SELECT post.post_id, post.title, post.description,post.post_date,post.author,
                        category.category_name,user.username,post.category,post.post_img FROM post
                        LEFT JOIN category ON post.category = category.category_id
                        LEFT JOIN user ON post.author = user.user_id WHERE postStatus = 'Y'
                        ORDER BY post.post_id DESC LIMIT {$offset},{$limit}";
          }
          $postR = mysqli_query($conn, $postQ);
          if (isset($_GET['cid'])) {
            echo "<h2 class='page-heading text-uppercase'> {$categoryD['category_name']}</h2>";
          } else {
            echo "<h2 class='page-heading text-uppercase'>All Category</h2>";
          }

          ?>

          <?php
          if (mysqli_num_rows($postR) > 0) {
            while ($postD = mysqli_fetch_assoc($postR)) {
          ?>
              <div class="post-content">
                <div class="row">
                  <div class="col-md-4">
                    <a class="post-img" href="single.php?id=<?php echo base64_encode(@$postD['post_id']); ?>"><img loading="lazy" src="./assets/postImage/<?php echo @$postD['post_img']; ?>" alt="<?php echo substr(@$postD['title'],0,100); ?>" /></a>
                  </div>
                  <div class="col-md-8">
                    <div class="inner-content clearfix">
                      <h3><a href='single.php?id=<?php echo base64_encode(@$postD['post_id']); ?>'><?php echo @$postD['title']; ?></a></h3>
                      <div class="post-information">
                        <span>
                          <i class="fa fa-tags" aria-hidden="true"></i>
                          <a href='category.php?cid=<?php echo base64_encode(@$postD['category']); ?>'><?php echo @$postD['category_name']; ?></a>
                        </span>
                        <span>
                          <i class="fa fa-user" aria-hidden="true"></i>
                          <a href='author.php?aid=<?php echo base64_encode(@$postD['author']); ?>'><?php echo @$postD['username']; ?></a>
                        </span>
                        <span>
                          <i class="fa fa-calendar" aria-hidden="true"></i>
                          <?php echo $postD['post_date']; ?>
                        </span>
                      </div>
                      <p class="description"><hr>
                        <?php echo substr(@$postD['sort_details'], 0, 130) . "..."; ?>
                      </p>
                      <a class='read-more pull-right' href='single.php?id=<?php echo base64_encode(@$postD['post_id']); ?>'>read more</a>
                    </div>
                  </div>
                </div>
              </div>
          <?php
            }
          } else {
            echo "<h2>No Record Found.</h2>";
          }
          if (isset($_GET['cid'])) {
            // show pagination

            $paginationQ = "SELECT * FROM post WHERE category = {$cat_id} && postStatus = 'Y'";
            $paginationR = mysqli_query($conn, $paginationQ);

            if (mysqli_num_rows($paginationR) > 0) {

              $total_records = mysqli_num_rows($paginationR);

              $total_page = ceil($total_records / $limit);

              echo '<ul class="pagination admin-pagination">';
              if ($page > 1) {
                echo '<li><a href="?cid=' . base64_encode($cat_id) . '&page=' . ($page - 1) . '">Prev</a></li>';
              }
              for ($i = 1; $i <= $total_page; $i++) {
                if ($i == $page) {
                  $active = "active";
                } else {
                  $active = "";
                }
                echo '<li class="' . $active . '"><a href="?cid=' . base64_encode($cat_id) . '&page=' . $i . '">' . $i . '</a></li>';
              }
              if ($total_page > $page) {
                echo '<li><a href="?cid=' . base64_encode($cat_id) . '&page=' . ($page + 1) . '">Next</a></li>';
              }

              echo '</ul>';
            }
          } else {
            // show pagination
            $paginationQ = "SELECT * FROM post WHERE postStatus = 'Y'";
            $paginationR = mysqli_query($conn, $paginationQ);
            if (mysqli_num_rows($paginationR) > 0) {

              $total_records = mysqli_num_rows($paginationR);

              $total_page = ceil($total_records / $limit);

              echo '<ul class="pagination admin-pagination">';
              if ($page > 1) {
                echo '<li><a href="?page=' . ($page - 1) . '">Prev</a></li>';
              }
              for ($i = 1; $i <= $total_page; $i++) {
                if ($i == $page) {
                  $active = "active";
                } else {
                  $active = "";
                }
                echo '<li class="' . $active . '"><a href="?page=' . $i . '">' . $i . '</a></li>';
              }
              if ($total_page > $page) {
                echo '<li><a href="?page=' . ($page + 1) . '">Next</a></li>';
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