<?php include 'header.php'; ?>
<div id="main-content">
  <div class="container">
    <div class="row">
      <div class="col-md-8">
        <!-- post-container -->
        <div class="post-container">
          <?php
          include "config.php";

          // Get the tag from URL
          $tag_slug = '';
          $tag_display = '';

          if (isset($_GET['tag'])) {
            $tag_slug = strtolower(trim($_GET['tag']));
            $tag_display = ucwords(str_replace('-', ' ', $tag_slug));
          }

          if (!empty($tag_slug)) {
            /* Calculate Offset Code */
            $limit = isset($limit) ? $limit : 5;
            if (isset($_GET['page'])) {
              $page = max(1, (int)$_GET['page']);
            } else {
              $page = 1;
            }
            $offset = ($page - 1) * $limit;

            // Search for the tag in meta_keywords field
            // Match exact tag (surrounded by commas, at start, or at end)
            $tagSafe = mysqli_real_escape_string($conn, $tag_slug);
            $tagSearch = mysqli_real_escape_string($conn, str_replace('-', ' ', $tag_slug));

            $sql = "SELECT post.post_id, post.title, post.post_slug, post.description, post.sort_details, post.post_date, post.author, post.meta_keywords,
                    category.category_name, category.category_slug, user.username, post.category, post.post_img FROM post
                    LEFT JOIN category ON post.category = category.category_id
                    LEFT JOIN user ON post.author = user.user_id
                    WHERE (post.meta_keywords LIKE '%{$tagSearch}%' OR post.meta_keywords LIKE '%{$tagSafe}%') && postStatus = 'Y'
                    ORDER BY post.post_id DESC LIMIT {$offset},{$limit}";

            $result = mysqli_query($conn, $sql);
          ?>
            <div class="tag-page-header mb-4 p-4 rounded-4 border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
              <div class="d-flex align-items-center gap-3">
                <div class="tag-icon-wrapper d-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(255,255,255,0.2); backdrop-filter: blur(10px);">
                  <i class="fa fa-tag text-white" style="font-size: 1.3rem;"></i>
                </div>
                <div>
                  <h2 class="page-heading text-white mb-1" style="font-size: 1.5rem; font-weight: 700;">#<?php echo htmlspecialchars($tag_display); ?></h2>
                  <?php
                  // Count total posts for this tag
                  $countSql = "SELECT COUNT(*) AS total FROM post WHERE (meta_keywords LIKE '%{$tagSearch}%' OR meta_keywords LIKE '%{$tagSafe}%') AND postStatus = 'Y'";
                  $countRes = mysqli_query($conn, $countSql);
                  $total_records = ($countRes) ? (int)mysqli_fetch_assoc($countRes)['total'] : 0;
                  ?>
                  <small class="text-white" style="opacity: 0.85;"><?php echo $total_records; ?> post<?php echo $total_records !== 1 ? 's' : ''; ?> found</small>
                </div>
              </div>
            </div>

          <?php
            if ($result && mysqli_num_rows($result) > 0) {
              while ($postD = mysqli_fetch_assoc($result)) {
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
                        <?php
                        // Show post tags as badges
                        if (!empty($postD['meta_keywords'])) {
                            $postTags = array_filter(array_map('trim', explode(',', $postD['meta_keywords'])));
                            if (!empty($postTags)) {
                                echo '<div class="d-flex flex-wrap gap-1 mt-2 mb-2">';
                                foreach (array_slice($postTags, 0, 5) as $pTag) {
                                    $isActive = (strtolower($pTag) === str_replace('-', ' ', $tag_slug)) ? 'bg-primary text-white' : 'bg-light text-secondary';
                                    echo '<a href="' . getTagUrl($pTag, $baseurl) . '" class="badge ' . $isActive . ' text-decoration-none px-2 py-1 rounded-pill border" style="font-size: 0.7rem; transition: all 0.3s ease;">#' . htmlspecialchars($pTag) . '</a>';
                                }
                                echo '</div>';
                            }
                        }
                        ?>
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
              echo "<h2>No posts found for this tag.</h2>";
            }

            // Pagination
            if ($total_records > 0) {
              $total_page = ceil($total_records / $limit);
              if ($total_page > 1) {
                $pageBase = $baseurl . '/tag/' . rawurlencode($tag_slug) . '?page=';

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
            // No tag specified — show all available tags
          ?>
            <h2 class="page-heading text-uppercase mb-4"><i class="fa fa-tags me-2"></i>All Tags</h2>
            <?php
            $allTagsQuery = "SELECT meta_keywords FROM post WHERE postStatus = 'Y' AND meta_keywords IS NOT NULL AND meta_keywords != ''";
            $allTagsRes = mysqli_query($conn, $allTagsQuery);
            $allTagsCounts = [];
            if ($allTagsRes && mysqli_num_rows($allTagsRes) > 0) {
                while ($row = mysqli_fetch_assoc($allTagsRes)) {
                    $postTags = array_map('trim', explode(',', $row['meta_keywords']));
                    foreach ($postTags as $tag) {
                        if (!empty($tag)) {
                            $lower = strtolower($tag);
                            if (!isset($allTagsCounts[$lower])) {
                                $allTagsCounts[$lower] = ['name' => $tag, 'count' => 0];
                            }
                            $allTagsCounts[$lower]['count']++;
                        }
                    }
                }
            }
            arsort($allTagsCounts);

            if (!empty($allTagsCounts)) {
                echo '<div class="d-flex flex-wrap gap-2">';
                foreach ($allTagsCounts as $slug => $tagData) {
                    $tagUrl = getTagUrl($slug, $baseurl);
                    $fontSize = min(1.3, max(0.8, 0.7 + ($tagData['count'] * 0.05)));
                    echo '<a href="' . $tagUrl . '" class="badge bg-light text-secondary text-decoration-none px-3 py-2 rounded-pill border hover-pill" style="font-size: ' . $fontSize . 'rem; transition: all 0.3s ease;">#' . htmlspecialchars(ucwords($tagData['name'])) . ' <span class="ms-1 opacity-50">(' . $tagData['count'] . ')</span></a>';
                }
                echo '</div>';
            } else {
                echo '<p>No tags found yet.</p>';
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
