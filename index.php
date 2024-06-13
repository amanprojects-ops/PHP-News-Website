<?php include 'header.php'; ?>
<div id="main-content">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <!-- post-container -->
        <div class="post-container">
          <?php
          include "config.php";
          // echo $_SERVER['REQUEST_SCHEME'] . "://".$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
          // echo "<pre>";
          // print_r($_SERVER);
          // echo "</pre>";
          /* Calculate Offset Code */
          // $limit = 3;
          if (isset($_GET['page'])) {
            $page = $_GET['page'];
          } else {
            $page = 1;
          }
          $offset = ($page - 1) * $limit;

          $sql = "SELECT post.post_id, post.title, post.description, post.sort_details,post.post_date,post.author,
                        category.category_name,user.username,post.category,post.post_img FROM post
                        LEFT JOIN category ON post.category = category.category_id
                        LEFT JOIN user ON post.author = user.user_id WHERE postStatus = 'Y'
                        ORDER BY post.post_id DESC LIMIT {$offset},{$limit}";

          $result = mysqli_query($conn, $sql);
          if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              //echo "<pre>";
             // print_r($row);
             // echo "</pre>";
          ?>
              <div class="post-content">
                <div class="row">
                  <div class="col-md-4">
                    <a class="post-img" href="single.php?id=<?php echo base64_encode(@$row['post_id']); ?>"><img loading="lazy" src="./assets/postImage/<?php echo @$row['post_img']; ?>" alt="<?php echo substr(@$row['title'], 0, 100); ?>" style="width: 100%; height: 100%;" /></a>
                  </div>
                  <div class="col-md-8">
                    <div class="inner-content clearfix">
                      <h3><a href='single.php?id=<?php echo base64_encode(@$row['post_id']); ?>'><?php echo @$row['title']; ?></a></h3>
                      <div class="post-information">
                        <span>
                          <i class="fa fa-tags" aria-hidden="true"></i>
                          <a href='category.php?cid=<?php echo base64_encode(@$row['category']); ?>'><?php echo @$row['category_name']; ?></a>
                        </span>
                        <span>
                          <i class="fa fa-user" aria-hidden="true"></i>
                          <a href='author.php?aid=<?php echo base64_encode(@$row['author']); ?>'><?php echo @$row['username']; ?></a>
                        </span>
                        <span>
                          <i class="fa fa-calendar" aria-hidden="true"></i>
                          <?php echo @$row['post_date']; ?>
                        </span>
                      </div>
                      <p class="description"><hr>
                       <?php echo substr(@$row['sort_details'],0,150)." ..."; ?>
                      </p>
                      <a class='read-more pull-right' href='single.php?id=<?php echo base64_encode(@$row['post_id']); ?>'>read more</a>
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
          $sql1 = "SELECT * FROM post WHERE postStatus = 'Y'";
          $result1 = mysqli_query($conn, $sql1);

          if (mysqli_num_rows($result1) > 0) {

            $total_records = mysqli_num_rows($result1);

            $total_page = ceil($total_records / $limit);

            echo '<ul class="pagination admin-pagination">';
            if ($page > 1) {
              echo '<li><a href="index.php?page=' . ($page - 1) . '">Prev</a></li>';
            }
            for ($i = 1; $i <= $total_page; $i++) {
              if ($i == $page) {
                $active = "active";
              } else {
                $active = "";
              }
              echo '<li class="' . $active . '"><a href="index.php?page=' . $i . '">' . $i . '</a></li>';
            }
            if ($total_page > $page) {
              echo '<li><a href="index.php?page=' . ($page + 1) . '">Next</a></li>';
            }
            echo '</ul>';
          }
          ?>
        </div><!-- /post-container -->
      </div>
      <?php include 'sidebar.php'; ?>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>