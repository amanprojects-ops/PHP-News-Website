<?php
require_once "./config.php";

// Determine base URL dynamically
$site_domain = rtrim($baseurl ?: ('http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')), '/');

// Check if user requested HTML visual sitemap view
if (isset($_GET['view']) && $_GET['view'] === 'html') {
  include_once 'header.php';
  
  // Fetch categories
  $cat_res = mysqli_query($conn, "SELECT category_id, category_name FROM category ORDER BY category_name ASC");
  $all_cats = [];
  if ($cat_res) {
    while ($c = mysqli_fetch_assoc($cat_res)) {
      $all_cats[] = $c;
    }
  }
  
  // Fetch posts
  $post_res = mysqli_query($conn, "SELECT p.post_id, p.title, p.post_date, p.post_img, c.category_name 
                                   FROM post p 
                                   LEFT JOIN category c ON p.category = c.category_id 
                                   WHERE p.postStatus = 'Y' 
                                   ORDER BY p.post_id DESC LIMIT 100");
  $all_posts = [];
  if ($post_res) {
    while ($p = mysqli_fetch_assoc($post_res)) {
      $all_posts[] = $p;
    }
  }
  
  // Fetch authors
  $author_res = mysqli_query($conn, "SELECT user_id, first_name, last_name, username FROM user ORDER BY user_id ASC");
  $all_authors = [];
  if ($author_res) {
    while ($a = mysqli_fetch_assoc($author_res)) {
      $all_authors[] = $a;
    }
  }
  ?>
  <main class="py-5 bg-light">
    <div class="container">
      <div class="text-center mb-5">
        <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-semibold mb-2">
          <i class="fa fa-sitemap me-1"></i> Interactive Site Navigation
        </span>
        <h1 class="display-6 fw-bold text-dark mb-2">HTML Sitemap</h1>
        <p class="text-muted mx-auto" style="max-width: 600px;">
          Explore all articles, news categories, author profiles, and essential pages indexed on <strong><?php echo htmlspecialchars($settings['websitename'] ?? 'Our Portal'); ?></strong>.
        </p>
        <a href="sitemap.php" class="btn btn-outline-primary btn-sm rounded-pill mt-2">
          <i class="fa fa-code me-1"></i> View Raw XML Sitemap
        </a>
      </div>

      <div class="row g-4">
        <!-- Main Pages & Categories -->
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
            <h4 class="h5 fw-bold text-dark mb-3 border-bottom pb-2"><i class="fa fa-globe text-primary me-2"></i>Main Pages</h4>
            <div class="list-group list-group-flush">
              <a href="index.php" class="list-group-item list-group-item-action border-0 px-0 text-dark fw-semibold"><i class="fa fa-home text-muted me-2"></i> Homepage</a>
              <a href="aboutus.php" class="list-group-item list-group-item-action border-0 px-0 text-dark"><i class="fa fa-info-circle text-muted me-2"></i> About Us</a>
              <a href="contactus.php" class="list-group-item list-group-item-action border-0 px-0 text-dark"><i class="fa fa-envelope text-muted me-2"></i> Contact Us</a>
              <a href="privacy-policy.php" class="list-group-item list-group-item-action border-0 px-0 text-dark"><i class="fa fa-shield text-muted me-2"></i> Privacy Policy</a>
              <a href="disclaimer.php" class="list-group-item list-group-item-action border-0 px-0 text-dark"><i class="fa fa-exclamation-triangle text-muted me-2"></i> Disclaimer</a>
            </div>
          </div>

          <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <h4 class="h5 fw-bold text-dark mb-3 border-bottom pb-2"><i class="fa fa-tags text-success me-2"></i>News Categories</h4>
            <div class="list-group list-group-flush">
              <?php foreach ($all_cats as $cat): ?>
                <a href="category.php?cid=<?php echo base64_encode($cat['category_id']); ?>" class="list-group-item list-group-item-action border-0 px-0 text-dark d-flex justify-content-between align-items-center">
                  <span><i class="fa fa-folder-o text-muted me-2"></i> <?php echo htmlspecialchars($cat['category_name']); ?></span>
                  <i class="fa fa-chevron-right small text-muted"></i>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Latest Posts & Authors -->
        <div class="col-lg-8">
          <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white mb-4">
            <h4 class="h5 fw-bold text-dark mb-3 border-bottom pb-2"><i class="fa fa-newspaper-o text-primary me-2"></i>Indexed News Articles (<?php echo count($all_posts); ?>)</h4>
            <div class="row g-3">
              <?php foreach ($all_posts as $post): ?>
                <div class="col-md-6">
                  <div class="p-3 border rounded-3 h-100 bg-light d-flex gap-3 align-items-center">
                    <?php if (!empty($post['post_img'])): ?>
                      <img src="./assets/postImage/<?php echo htmlspecialchars($post['post_img']); ?>" alt="" class="rounded" style="width: 55px; height: 55px; object-fit: cover;">
                    <?php endif; ?>
                    <div>
                      <span class="badge bg-primary-soft text-primary small mb-1"><?php echo htmlspecialchars($post['category_name'] ?? 'General'); ?></span>
                      <h6 class="mb-1 fw-bold" style="font-size: 0.875rem; line-height: 1.3;">
                        <a href="single.php?id=<?php echo base64_encode($post['post_id']); ?>" class="text-dark text-decoration-none">
                          <?php echo htmlspecialchars(substr($post['title'], 0, 50)) . (strlen($post['title']) > 50 ? '...' : ''); ?>
                        </a>
                      </h6>
                      <small class="text-muted"><i class="fa fa-calendar me-1"></i> <?php echo date('M d, Y', strtotime($post['post_date'])); ?></small>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <?php if (!empty($all_authors)): ?>
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
              <h4 class="h5 fw-bold text-dark mb-3 border-bottom pb-2"><i class="fa fa-users text-warning me-2"></i>Journalists &amp; Contributors</h4>
              <div class="row g-3">
                <?php foreach ($all_authors as $auth): 
                  $author_name = trim(($auth['first_name'] ?? '') . ' ' . ($auth['last_name'] ?? ''));
                  if (empty($author_name)) $author_name = $auth['username'];
                ?>
                  <div class="col-6 col-md-4">
                    <a href="author.php?aid=<?php echo base64_encode($auth['user_id']); ?>" class="card border p-3 text-decoration-none text-dark text-center rounded-3 bg-light hover-card">
                      <div class="fw-bold"><i class="fa fa-user-circle me-1 text-primary"></i> <?php echo htmlspecialchars($author_name); ?></div>
                      <small class="text-muted">View Articles</small>
                    </a>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

        </div>
      </div>
    </div>
  </main>
  <?php
  include_once 'footer.php';
  exit();
}

// -------------------------------------------------------------
// XML Sitemap Output Mode (Standard XML Protocol for SEO / AEO / GEO)
// -------------------------------------------------------------
header("Content-Type: application/xml; charset=utf-8");

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" ' . "\n" .
     '        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" ' . "\n" .
     '        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">' . "\n";

// Function to print standard sitemap URL node
function print_sitemap_url($url, $lastmod, $changefreq = 'weekly', $priority = '0.8', $images = []) {
  echo "  <url>\n";
  echo "    <loc>" . htmlspecialchars($url) . "</loc>\n";
  echo "    <lastmod>" . htmlspecialchars($lastmod) . "</lastmod>\n";
  echo "    <changefreq>" . htmlspecialchars($changefreq) . "</changefreq>\n";
  echo "    <priority>" . htmlspecialchars($priority) . "</priority>\n";
  
  if (!empty($images)) {
    foreach ($images as $img) {
      if (!empty($img['loc'])) {
        echo "    <image:image>\n";
        echo "      <image:loc>" . htmlspecialchars($img['loc']) . "</image:loc>\n";
        if (!empty($img['title'])) {
          echo "      <image:title>" . htmlspecialchars($img['title']) . "</image:title>\n";
        }
        echo "    </image:image>\n";
      }
    }
  }
  echo "  </url>\n";
}

$today = date('Y-m-d');

// 1. Homepage URL
print_sitemap_url($site_domain . '/index.php', $today, 'daily', '1.0');

// 2. Static Public Pages
print_sitemap_url($site_domain . '/aboutus.php', $today, 'monthly', '0.7');
print_sitemap_url($site_domain . '/contactus.php', $today, 'monthly', '0.7');
print_sitemap_url($site_domain . '/privacy-policy.php', $today, 'monthly', '0.6');
print_sitemap_url($site_domain . '/disclaimer.php', $today, 'monthly', '0.6');

// 3. Category URLs
$cat_query = "SELECT category_id FROM category ORDER BY category_id DESC";
$cat_res = mysqli_query($conn, $cat_query);
if ($cat_res) {
  while ($cat = mysqli_fetch_assoc($cat_res)) {
    $cat_url = $site_domain . '/category.php?cid=' . base64_encode($cat['category_id']);
    print_sitemap_url($cat_url, $today, 'daily', '0.9');
  }
}

// 4. Author URLs
$author_query = "SELECT user_id FROM user ORDER BY user_id DESC";
$author_res = mysqli_query($conn, $author_query);
if ($author_res) {
  while ($auth = mysqli_fetch_assoc($author_res)) {
    $auth_url = $site_domain . '/author.php?aid=' . base64_encode($auth['user_id']);
    print_sitemap_url($auth_url, $today, 'weekly', '0.6');
  }
}

// 5. Post URLs (with Google Image Sitemap Extension)
$post_query = "SELECT post_id, title, post_date, post_img FROM post WHERE postStatus = 'Y' ORDER BY post_id DESC";
$post_res = mysqli_query($conn, $post_query);
if ($post_res) {
  while ($post = mysqli_fetch_assoc($post_res)) {
    $post_url = $site_domain . '/single.php?id=' . base64_encode($post['post_id']);
    $post_date = !empty($post['post_date']) ? date('Y-m-d', strtotime($post['post_date'])) : $today;
    
    $images = [];
    if (!empty($post['post_img'])) {
      $images[] = [
        'loc' => $site_domain . '/assets/postImage/' . $post['post_img'],
        'title' => $post['title']
      ];
    }
    
    print_sitemap_url($post_url, $post_date, 'weekly', '0.8', $images);
  }
}

echo '</urlset>';
?>