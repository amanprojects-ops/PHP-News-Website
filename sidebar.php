<?php
// Ensure config & database connection are present
if (!isset($conn)) {
  include_once 'config.php';
}

// Fetch Recent Posts for Sidebar Widget
$sidebar_recent_posts = [];
$sidebar_post_limit = $limit ?? 5;
$sidebar_post_query = "SELECT p.post_id, p.title, p.post_date, p.post_img, p.category, c.category_name 
                      FROM post p 
                      LEFT JOIN category c ON p.category = c.category_id 
                      WHERE p.postStatus = 'Y' 
                      ORDER BY p.post_id DESC LIMIT {$sidebar_post_limit}";
$sidebar_post_res = mysqli_query($conn, $sidebar_post_query);
if ($sidebar_post_res && mysqli_num_rows($sidebar_post_res) > 0) {
  while ($s_row = mysqli_fetch_assoc($sidebar_post_res)) {
    $sidebar_recent_posts[] = $s_row;
  }
}

// Fetch Popular Categories for Sidebar Widget
$sidebar_categories = [];
$sidebar_cat_query = "SELECT c.category_id, c.category_name, COUNT(p.post_id) as post_count 
                      FROM category c 
                      LEFT JOIN post p ON c.category_id = p.category AND p.postStatus = 'Y' 
                      GROUP BY c.category_id 
                      ORDER BY post_count DESC LIMIT 6";
$sidebar_cat_res = mysqli_query($conn, $sidebar_cat_query);
if ($sidebar_cat_res && mysqli_num_rows($sidebar_cat_res) > 0) {
  while ($c_row = mysqli_fetch_assoc($sidebar_cat_res)) {
    $sidebar_categories[] = $c_row;
  }
}
?>

<div id="sidebar" class="col-md-4">
  
  <!-- Recent News Widget with Images -->
  <?php if (!empty($sidebar_recent_posts)): ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white sidebar-widget">
      <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
            <i class="fa fa-flash text-warning"></i> Recent Headlines
          </h5>
          <span class="badge bg-primary-soft text-primary rounded-pill px-2 py-1 small">Latest</span>
        </div>
        
        <div class="sidebar-recent-list d-flex flex-column gap-3">
          <?php foreach ($sidebar_recent_posts as $post): ?>
            <div class="d-flex gap-3 align-items-center sidebar-post-item pb-2 border-bottom border-light">
              <?php if (!empty($post['post_img'])): ?>
                <a href="single.php?id=<?php echo base64_encode($post['post_id']); ?>" class="flex-shrink-0 rounded-3 overflow-hidden" style="width: 75px; height: 60px;">
                  <img loading="lazy" src="./assets/postImage/<?php echo htmlspecialchars($post['post_img']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;" class="sidebar-img-zoom">
                </a>
              <?php else: ?>
                <a href="single.php?id=<?php echo base64_encode($post['post_id']); ?>" class="flex-shrink-0 rounded-3 bg-light d-flex align-items-center justify-content-center text-muted" style="width: 75px; height: 60px;">
                  <i class="fa fa-newspaper-o fs-4"></i>
                </a>
              <?php endif; ?>
              
              <div class="flex-grow-1">
                <a href="category.php?cid=<?php echo base64_encode($post['category']); ?>" class="badge bg-light text-primary text-decoration-none mb-1 fw-semibold small">
                  <?php echo htmlspecialchars($post['category_name'] ?? 'News'); ?>
                </a>
                <h6 class="mb-1 fw-bold" style="font-size: 0.875rem; line-height: 1.35;">
                  <a href="single.php?id=<?php echo base64_encode($post['post_id']); ?>" class="text-dark text-decoration-none sidebar-title-link">
                    <?php echo htmlspecialchars(substr($post['title'], 0, 55)) . (strlen($post['title']) > 55 ? '...' : ''); ?>
                  </a>
                </h6>
                <small class="text-muted" style="font-size: 0.75rem;">
                  <i class="fa fa-calendar me-1"></i> <?php echo date('M d, Y', strtotime($post['post_date'])); ?>
                </small>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Popular Categories Widget -->
  <?php if (!empty($sidebar_categories)): ?>
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white sidebar-widget">
      <div class="card-body p-4">
        <h5 class="fw-bold text-dark mb-3 border-bottom pb-2 d-flex align-items-center gap-2">
          <i class="fa fa-folder-open text-primary"></i> Popular Categories
        </h5>
        <div class="list-group list-group-flush">
          <?php foreach ($sidebar_categories as $cat): ?>
            <a href="category.php?cid=<?php echo base64_encode($cat['category_id']); ?>" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-2 border-0 rounded-3 mb-1 px-2 hover-bg-light">
              <span class="fw-semibold text-dark small"><i class="fa fa-angle-right me-2 text-primary"></i> <?php echo htmlspecialchars($cat['category_name']); ?></span>
              <span class="badge bg-primary-soft text-primary rounded-pill small"><?php echo (int) $cat['post_count']; ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  <?php endif; ?>

  <!-- Trending Tags Widget -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white sidebar-widget">
    <div class="card-body p-4">
      <h5 class="fw-bold text-dark mb-3 border-bottom pb-2 d-flex align-items-center gap-2">
        <i class="fa fa-tags text-success"></i> Trending Topics
      </h5>
      <div class="d-flex flex-wrap gap-2">
        <?php foreach ($sidebar_categories as $cat): ?>
          <a href="category.php?cid=<?php echo base64_encode($cat['category_id']); ?>" class="badge bg-light text-secondary text-decoration-none px-3 py-2 rounded-pill border hover-pill">
            #<?php echo htmlspecialchars($cat['category_name']); ?>
          </a>
        <?php endforeach; ?>
        <a href="index.php" class="badge bg-light text-secondary text-decoration-none px-3 py-2 rounded-pill border hover-pill">#BreakingNews</a>
        <a href="index.php" class="badge bg-light text-secondary text-decoration-none px-3 py-2 rounded-pill border hover-pill">#Updates</a>
      </div>
    </div>
  </div>

  <!-- Publisher Card Widget -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary text-white sidebar-widget">
    <div class="card-body p-4 text-center">
      <?php if (!empty($settings['logo'])): ?>
        <img src="./assets/images/<?php echo htmlspecialchars($settings['logo']); ?>" alt="<?php echo htmlspecialchars($settings['websitename'] ?? ''); ?>" class="img-fluid mb-2" style="max-height: 50px;">
      <?php endif; ?>
      <h6 class="fw-bold mb-2"><?php echo htmlspecialchars($settings['websitename'] ?? 'News Portal'); ?></h6>
      <p class="small text-white-50 mb-3" style="font-size: 0.8rem;">
        <?php echo htmlspecialchars(substr($settings['footerdesc'] ?? 'Stay updated with live news coverage.', 0, 100)); ?>
      </p>
      <div class="d-flex justify-content-center gap-2">
        <a href="aboutus.php" class="btn btn-light btn-sm rounded-pill text-primary fw-semibold px-3">About Us</a>
        <a href="contactus.php" class="btn btn-outline-light btn-sm rounded-pill fw-semibold px-3">Contact Desk</a>
      </div>
    </div>
  </div>

</div>

<style>
.sidebar-widget {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.sidebar-widget:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06) !important;
}
.sidebar-title-link:hover {
  color: #1e90ff !important;
}
.sidebar-img-zoom {
  transition: transform 0.3s ease;
}
.sidebar-post-item:hover .sidebar-img-zoom {
  transform: scale(1.08);
}
.bg-primary-soft {
  background-color: rgba(30, 144, 255, 0.12);
}
.hover-bg-light:hover {
  background-color: #f8fafc;
}
.hover-pill:hover {
  background-color: #1e90ff !important;
  color: #ffffff !important;
  border-color: #1e90ff !important;
}
</style>