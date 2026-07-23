<?php
include_once "header.php";

// Fetch dynamic site metrics
$total_posts = 0;
$total_cats = 0;
$total_authors = 0;
$total_visitors = 0;

$p_count_res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM post WHERE postStatus = 'Y'");
if ($p_count_res && $row = mysqli_fetch_assoc($p_count_res)) {
  $total_posts = (int)$row['cnt'];
}

$c_count_res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM category");
if ($c_count_res && $row = mysqli_fetch_assoc($c_count_res)) {
  $total_cats = (int)$row['cnt'];
}

$a_count_res = mysqli_query($conn, "SELECT COUNT(*) as cnt FROM user");
if ($a_count_res && $row = mysqli_fetch_assoc($a_count_res)) {
  $total_authors = (int)$row['cnt'];
}

$v_count_res = mysqli_query($conn, "SELECT COUNT(DISTINCT ip) as cnt FROM visitors");
if ($v_count_res && $row = mysqli_fetch_assoc($v_count_res)) {
  $total_visitors = (int)$row['cnt'];
}

// Fetch team members / authors
$authors = [];
$author_query = "SELECT user_id, first_name, last_name, username, role FROM user ORDER BY user_id ASC LIMIT 4";
$author_res = mysqli_query($conn, $author_query);
if ($author_res && mysqli_num_rows($author_res) > 0) {
  while ($a_row = mysqli_fetch_assoc($author_res)) {
    $authors[] = $a_row;
  }
}

// Fetch recent published news
$latest_news = [];
$l_news_res = mysqli_query($conn, "SELECT p.post_id, p.title, p.post_date, p.post_img, c.category_name 
                                  FROM post p 
                                  LEFT JOIN category c ON p.category = c.category_id 
                                  WHERE p.postStatus = 'Y' 
                                  ORDER BY p.post_id DESC LIMIT 4");
if ($l_news_res && mysqli_num_rows($l_news_res) > 0) {
  while ($n_row = mysqli_fetch_assoc($l_news_res)) {
    $latest_news[] = $n_row;
  }
}
?>

<main class="py-5 bg-light">
  <div class="container">

    <!-- Hero Header -->
    <div class="row align-items-center mb-5">
      <div class="col-lg-7 mb-4 mb-lg-0">
        <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-semibold mb-2">
          <i class="fa fa-info-circle me-1"></i> About Our News Portal
        </span>
        <h1 class="display-5 fw-bold text-dark mb-3">
          Delivering Trustworthy News &amp; Stories at <span class="text-primary"><?php echo htmlspecialchars($settings['websitename'] ?? 'Our Portal'); ?></span>
        </h1>
        <p class="lead text-muted mb-4">
          <?php echo htmlspecialchars($settings['footerdesc'] ?? 'We are dedicated to providing accurate, real-time, and comprehensive news coverage across technology, politics, sports, entertainment, and local community updates.'); ?>
        </p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="contactus.php" class="btn btn-primary px-4 py-2 rounded-pill shadow-sm">
            <i class="fa fa-envelope me-1"></i> Get In Touch
          </a>
          <a href="index.php" class="btn btn-outline-secondary px-4 py-2 rounded-pill">
            <i class="fa fa-newspaper-o me-1"></i> Read Headlines
          </a>
        </div>
      </div>
      <div class="col-lg-5 text-center">
        <div class="card border-0 shadow-lg rounded-4 p-4 text-center bg-white position-relative">
          <?php if (!empty($settings['logo'])): ?>
            <img src="./assets/images/<?php echo htmlspecialchars($settings['logo']); ?>" alt="<?php echo htmlspecialchars($settings['websitename'] ?? ''); ?>" class="img-fluid mx-auto mb-3" style="max-height: 110px; object-fit: contain;">
          <?php else: ?>
            <div class="py-4 text-primary fs-1 fw-bold"><i class="fa fa-globe"></i></div>
          <?php endif; ?>
          <h4 class="fw-bold text-dark mb-1"><?php echo htmlspecialchars($settings['websitename'] ?? 'News Portal'); ?></h4>
          <p class="text-muted small mb-0"><i class="fa fa-shield text-success me-1"></i> Verified News Media Platform</p>
        </div>
      </div>
    </div>

    <!-- Live Statistics Counter Grid -->
    <div class="row g-4 mb-5">
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white hover-card">
          <div class="text-primary fs-2 mb-2"><i class="fa fa-file-text-o"></i></div>
          <h3 class="fw-bold text-dark mb-1"><?php echo number_format($total_posts); ?>+</h3>
          <span class="text-muted small fw-semibold">Published Articles</span>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white hover-card">
          <div class="text-success fs-2 mb-2"><i class="fa fa-tags"></i></div>
          <h3 class="fw-bold text-dark mb-1"><?php echo number_format($total_cats); ?></h3>
          <span class="text-muted small fw-semibold">News Categories</span>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white hover-card">
          <div class="text-info fs-2 mb-2"><i class="fa fa-users"></i></div>
          <h3 class="fw-bold text-dark mb-1"><?php echo number_format($total_authors); ?></h3>
          <span class="text-muted small fw-semibold">Journalists &amp; Authors</span>
        </div>
      </div>
      <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm rounded-4 p-4 text-center h-100 bg-white hover-card">
          <div class="text-warning fs-2 mb-2"><i class="fa fa-eye"></i></div>
          <h3 class="fw-bold text-dark mb-1"><?php echo number_format($total_visitors); ?>+</h3>
          <span class="text-muted small fw-semibold">Unique Readers</span>
        </div>
      </div>
    </div>

    <!-- Core Values & Mission Section -->
    <div class="row g-4 mb-5">
      <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
          <div class="text-primary fs-3 mb-3"><i class="fa fa-bullseye"></i></div>
          <h4 class="fw-bold text-dark h5 mb-2">Our Mission</h4>
          <p class="text-muted small mb-0">
            To empower our community with unbiased, accurate, and timely journalism, serving as a trusted source for local, national, and global developments.
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
          <div class="text-success fs-3 mb-3"><i class="fa fa-check-circle-o"></i></div>
          <h4 class="fw-bold text-dark h5 mb-2">Accuracy &amp; Integrity</h4>
          <p class="text-muted small mb-0">
            Every story undergoes rigorous editorial checks and verification to ensure factuality, fairness, and absolute transparency in reportage.
          </p>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white">
          <div class="text-warning fs-3 mb-3"><i class="fa fa-bolt"></i></div>
          <h4 class="fw-bold text-dark h5 mb-2">Real-Time Coverage</h4>
          <p class="text-muted small mb-0">
            Our newsroom operates round-the-clock to bring breaking updates, live announcements, and key events directly to your screen.
          </p>
        </div>
      </div>
    </div>

    <!-- Dynamic Editorial Team Grid -->
    <?php if (!empty($authors)): ?>
      <div class="mb-5">
        <div class="text-center mb-4">
          <h3 class="fw-bold text-dark">Editorial Team &amp; Contributors</h3>
          <p class="text-muted small mx-auto" style="max-width: 500px;">Meet the journalists and editors driving our daily reporting.</p>
        </div>
        <div class="row g-4 justify-content-center">
          <?php foreach ($authors as $author): 
            $full_name = trim(($author['first_name'] ?? '') . ' ' . ($author['last_name'] ?? ''));
            if (empty($full_name)) $full_name = $author['username'];
            $role_title = ($author['role'] == 1) ? 'Editor-in-Chief' : (($author['role'] == 2) ? 'Senior Journalist' : 'News Anchor / Reporter');
          ?>
            <div class="col-6 col-md-3 text-center">
              <div class="card border-0 shadow-sm rounded-4 p-4 h-100 bg-white hover-card">
                <div class="mx-auto mb-3 rounded-circle bg-light d-flex align-items-center justify-content-center text-primary fw-bold" style="width: 70px; height: 70px; font-size: 1.5rem;">
                  <i class="fa fa-user"></i>
                </div>
                <h5 class="fw-bold text-dark h6 mb-1"><?php echo htmlspecialchars($full_name); ?></h5>
                <small class="badge bg-light text-secondary rounded-pill"><?php echo htmlspecialchars($role_title); ?></small>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>

    <!-- Dynamic Latest News Highlights -->
    <?php if (!empty($latest_news)): ?>
      <div>
        <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
          <h4 class="fw-bold mb-0 text-dark"><i class="fa fa-flash text-warning me-2"></i>Latest News Coverage</h4>
          <a href="index.php" class="text-primary text-decoration-none fw-semibold">View All News <i class="fa fa-angle-right"></i></a>
        </div>
        <div class="row g-4">
          <?php foreach ($latest_news as $news): ?>
            <div class="col-md-3">
              <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden hover-card">
                <?php if (!empty($news['post_img'])): ?>
                  <img src="./assets/postImage/<?php echo htmlspecialchars($news['post_img']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($news['title']); ?>" style="height: 150px; object-fit: cover;">
                <?php endif; ?>
                <div class="card-body p-3">
                  <span class="badge bg-primary-soft text-primary mb-2 small"><?php echo htmlspecialchars($news['category_name'] ?? 'General'); ?></span>
                  <h6 class="card-title fw-bold mb-2" style="font-size: 0.95rem; line-height: 1.4;">
                    <a href="single.php?id=<?php echo base64_encode($news['post_id']); ?>" class="text-dark text-decoration-none">
                      <?php echo htmlspecialchars(substr($news['title'], 0, 60)) . (strlen($news['title']) > 60 ? '...' : ''); ?>
                    </a>
                  </h6>
                  <small class="text-muted"><i class="fa fa-clock-o me-1"></i> <?php echo date('M d, Y', strtotime($news['post_date'])); ?></small>
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

<?php include_once "footer.php"; ?>