<?php
include_once 'header.php';

// Fetch dynamic popular categories
$popular_cats = [];
$cat_query = "SELECT c.category_id, c.category_name, COUNT(p.post_id) as post_count 
              FROM category c 
              LEFT JOIN post p ON c.category_id = p.category AND p.postStatus = 'Y' 
              GROUP BY c.category_id 
              ORDER BY post_count DESC LIMIT 6";
$cat_result = mysqli_query($conn, $cat_query);
if ($cat_result && mysqli_num_rows($cat_result) > 0) {
  while ($c_row = mysqli_fetch_assoc($cat_result)) {
    $popular_cats[] = $c_row;
  }
}

// Fetch dynamic recent news
$recent_posts = [];
$post_query = "SELECT p.post_id, p.title, p.post_date, p.post_img, c.category_name, c.category_id 
               FROM post p 
               LEFT JOIN category c ON p.category = c.category_id 
               WHERE p.postStatus = 'Y' 
               ORDER BY p.post_id DESC LIMIT 3";
$post_result = mysqli_query($conn, $post_query);
if ($post_result && mysqli_num_rows($post_result) > 0) {
  while ($p_row = mysqli_fetch_assoc($post_result)) {
    $recent_posts[] = $p_row;
  }
}
?>

<main class="py-5 bg-light min-vh-75">
  <div class="container">
    
    <!-- 404 Hero Banner -->
    <div class="row justify-content-center text-center my-4">
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 p-md-5 rounded-4 bg-white">
          <div class="position-relative d-inline-block mb-3">
            <h1 class="display-1 fw-bold text-primary mb-0" style="font-size: 6rem; letter-spacing: -3px;">404</h1>
            <span class="badge bg-warning text-dark position-absolute top-0 start-100 translate-middle rounded-pill px-3 py-2 fs-6">
              Page Not Found
            </span>
          </div>
          <h2 class="h3 fw-bold text-dark mb-3">Oops! We couldn't find that page</h2>
          <p class="text-muted mb-4 fs-6 mx-auto" style="max-width: 540px;">
            The page you are looking for might have been removed, had its name changed, or is temporarily unavailable on 
            <strong><?php echo htmlspecialchars($settings['websitename'] ?? 'our website'); ?></strong>.
          </p>

          <!-- Search Bar -->
          <div class="row justify-content-center mb-4">
            <div class="col-md-9">
              <form action="search.php" method="GET" class="d-flex gap-2 shadow-sm rounded-pill p-1 bg-light border">
                <input type="text" name="search" class="form-control border-0 bg-transparent px-3" placeholder="Search news, topics, or keywords..." required>
                <button type="submit" class="btn btn-primary rounded-pill px-4">
                  <i class="fa fa-search me-1"></i> Search
                </button>
              </form>
            </div>
          </div>

          <!-- Quick Action Buttons -->
          <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="index.php" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
              <i class="fa fa-home me-1"></i> Back to Homepage
            </a>
            <button onclick="history.back()" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
              <i class="fa fa-arrow-left me-1"></i> Previous Page
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Dynamic Popular Categories Section -->
    <?php if (!empty($popular_cats)): ?>
      <div class="mt-5">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h4 class="fw-bold mb-0 text-dark"><i class="fa fa-th-large text-primary me-2"></i>Explore Categories</h4>
          <a href="index.php" class="text-primary text-decoration-none fw-semibold">View All <i class="fa fa-angle-right"></i></a>
        </div>
        <div class="row g-3">
          <?php foreach ($popular_cats as $cat): ?>
            <div class="col-6 col-md-4 col-lg-2">
              <a href="category.php?cid=<?php echo base64_encode($cat['category_id']); ?>" class="card h-100 border-0 shadow-sm text-decoration-none text-center p-3 rounded-3 hover-card">
                <div class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($cat['category_name']); ?></div>
                <small class="text-muted"><?php echo (int)$cat['post_count']; ?> Articles</small>
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Dynamic Recent News Articles Section -->
    <?php if (!empty($recent_posts)): ?>
      <div class="mt-5 mb-4">
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h4 class="fw-bold mb-0 text-dark"><i class="fa fa-newspaper-o text-primary me-2"></i>Latest Headlines</h4>
          <a href="index.php" class="text-primary text-decoration-none fw-semibold">More News <i class="fa fa-angle-right"></i></a>
        </div>
        <div class="row g-4">
          <?php foreach ($recent_posts as $post): ?>
            <div class="col-md-4">
              <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden hover-card">
                <?php if (!empty($post['post_img'])): ?>
                  <img src="./assets/postImage/<?php echo htmlspecialchars($post['post_img']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($post['title']); ?>" style="height: 180px; object-fit: cover;">
                <?php endif; ?>
                <div class="card-body">
                  <span class="badge bg-primary-soft text-primary mb-2"><?php echo htmlspecialchars($post['category_name'] ?? 'General'); ?></span>
                  <h5 class="card-title h6 fw-bold">
                    <a href="single.php?id=<?php echo base64_encode($post['post_id']); ?>" class="text-dark text-decoration-none">
                      <?php echo htmlspecialchars(substr($post['title'], 0, 70)) . (strlen($post['title']) > 70 ? '...' : ''); ?>
                    </a>
                  </h5>
                  <p class="card-text small text-muted mb-0">
                    <i class="fa fa-calendar me-1"></i> <?php echo date('M d, Y', strtotime($post['post_date'])); ?>
                  </p>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

  </div>
</main>

<style>
.hover-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.hover-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
}
.bg-primary-soft {
  background-color: rgba(30, 144, 255, 0.1);
}
</style>

<?php include_once 'footer.php'; ?>
