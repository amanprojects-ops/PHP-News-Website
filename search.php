<?php include 'header.php'; ?>
<div id="main-content">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <!-- post-container -->
        <div class="post-container">
          <?php
          include "config.php";
          if (isset($_GET['search'])) {
            $search_term = mysqli_real_escape_string($conn, $_GET['search']);
          ?>
            <h2 class="page-heading">Search : <?php echo $search_term; ?></h2>
            <?php

            /* Calculate Offset Code */
            $limit = 3;
            if (isset($_GET['page'])) {
              $page = $_GET['page'];
            } else {
              $page = 1;
            }
            $offset = ($page - 1) * $limit;

            $sql = "SELECT post.post_id, post.title, post.description,post.post_date,post.author,
                    category.category_name,user.username,post.category,post.post_img FROM post
                    LEFT JOIN category ON post.category = category.category_id
                    LEFT JOIN user ON post.author = user.user_id
                    WHERE post.title LIKE '%{$search_term}%' OR post.description LIKE '%{$search_term}%' && postStatus = 'Y'
                    ORDER BY post.post_id DESC LIMIT {$offset},{$limit}";

            $result = mysqli_query($conn, $sql) or die("Query Failed.");
            if (mysqli_num_rows($result) > 0) {
              while ($row = mysqli_fetch_assoc($result)) { ?>

                <div class="post-content">
                  <div class="row">
                    <div class="col-md-4">
                      <a class="post-img" href="single.php?id=<?php echo base64_encode($row['post_id']); ?>"><img loading="lazy" src="./assets/postImage/<?php echo $row['post_img']; ?>" alt="<?php echo $row['title']; ?>" /></a>
                    </div>
                    <div class="col-md-8">
                      <div class="inner-content clearfix">
                        <h3><a href='single.php?id=<?php echo  base64_encode($row['post_id']); ?>'><?php echo $row['title']; ?></a></h3>
                        <div class="post-information">
                          <span>
                            <i class="fa fa-tags" aria-hidden="true"></i>
                            <a href='category.php?cid=<?php echo  base64_encode($row['category']); ?>'><?php echo $row['category_name']; ?></a>
                          </span>
                          <span>
                            <i class="fa fa-user" aria-hidden="true"></i>
                            <a href='author.php?aid=<?php echo  base64_encode($row['author']); ?>'><?php echo $row['username']; ?></a>
                          </span>
                          <span>
                            <i class="fa fa-calendar" aria-hidden="true"></i>
                            <?php echo $row['post_date']; ?>
                          </span>
                        </div>
                        <p class="description">
                          <?php echo substr(@$row['short_description'], 0, 130) . "..."; ?>
                        </p>
                        <a class='read-more pull-right' href='single.php?id=<?php echo base64_encode($row['post_id']); ?>'>read more</a>
                      </div>
                    </div>
                  </div>
                </div>
          <?php
              }
            } else {
              echo "<h2>No Record Found.</h2>";?>
            <div class="row">
                            <?php
                            include "config.php";

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
                                                        <img class="card-img" loading="lazy" src="./assets/postImage/<?php echo @$row['post_img']; ?>" alt="<?php echo substr(@$row['title'], 0, 50); ?>" style="width: 14rem;min-height: 7.5rem;" />
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
                            }  ?>
                        </div>

            <?php }

            // show pagination
            $sql1 = "SELECT * FROM post
                            WHERE post.title LIKE '%{$search_term}%' && postStatus = 'Y'";
            $result1 = mysqli_query($conn, $sql1);

            if (mysqli_num_rows($result1) > 0) {

              $total_records = mysqli_num_rows($result1);

              $total_page = ceil($total_records / $limit);

              echo '<ul class="pagination admin-pagination">';
              if ($page > 1) {
                echo '<li><a href="search.php?search=' . $search_term . '&page=' . ($page - 1) . '">Prev</a></li>';
              }
              for ($i = 1; $i <= $total_page; $i++) {
                if ($i == $page) {
                  $active = "active";
                } else {
                  $active = "";
                }
                echo '<li class="' . $active . '"><a href="search.php?search=' . $search_term . '&page=' . $i . '">' . $i . '</a></li>';
              }
              if ($total_page > $page) {
                echo '<li><a href="search.php?search=' . $search_term . '&page=' . ($page + 1) . '">Next</a></li>';
              }

              echo '</ul>';
            }
          } else {
            echo "<h2>No Record Found.</h2>";
          }
          ?>
        </div><!-- /post-container -->
      </div>
      <?php include 'sidebar.php'; ?>
    </div>
  </div>
</div>
<?php include 'footer.php'; ?>