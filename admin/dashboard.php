<?php
// Menu
include_once '_header.php';
?>

<!-- Navbar -->
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center">
        <i class="bx bx-search fs-4 lh-0"></i>
      </div>
    </div>

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- User -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <i class="menu-icon tf-icons bx bx-user fs-3 w-px-40 h-auto rounded-circle"></i>
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="#">
              <div class="d-flex">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <i class="menu-icon tf-icons bx bx-user"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block"><?php echo $_SESSION['name']; ?></span>
                  <small class="text-muted">
                    <?php
                    if ($_SESSION['role'] == 1) {
                      echo 'Admin';
                    } elseif ($_SESSION['role'] == 2) {
                      echo 'Sub-admin';
                    } elseif ($_SESSION['role'] == 3) {
                      echo 'News Anchor';
                    } elseif ($_SESSION['role'] == 0) {
                      echo 'Oprator';
                    }
                    ?>
                  </small>
                </div>
              </div>
            </a>
          </li>
          <li><div class="dropdown-divider"></div></li>
          <li>
            <a class="dropdown-item" href="setting.php">
              <i class="bx bx-cog me-2"></i>
              <span class="align-middle">Settings</span>
            </a>
          </li>
          <li><div class="dropdown-divider"></div></li>
          <li>
            <a class="dropdown-item" href="logout.php">
              <i class="bx bx-power-off me-2"></i>
              <span class="align-middle">Log Out</span>
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    
    <!-- Welcome Header Banner -->
    <div class="row">
      <div class="col-lg-12 mb-4">
        <div class="card bg-gradient-primary text-white" style="background: linear-gradient(135deg, #1e90ff 0%, #0d5ea8 100%); border: none;">
          <div class="d-flex align-items-center row">
            <div class="col-sm-8">
              <div class="card-body p-4">
                <h3 class="text-white mb-2" style="font-weight: 700;">Welcome back, <?php echo $_SESSION['name']; ?>! 🎉</h3>
                <p class="mb-0 text-white-50">Here is what's happening on your news portal today. Check visitor analytics, write posts, and manage settings.</p>
              </div>
            </div>
            <div class="col-sm-4 text-center text-sm-left">
              <div class="card-body pb-0 px-0 px-md-4 d-none d-sm-block">
                <img src="../assets/admin/img/man-with-laptop-light.png" height="120" loading="lazy" alt="View User" />
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php
    // Fetch stats
    // Total Visitors
    $visitor_query = mysqli_query($conn, 'SELECT COUNT(*) AS count FROM visitors');
    $visitor_data = mysqli_fetch_assoc($visitor_query);
    $total_visitors = $visitor_data['count'] ?? 0;

    // Active Posts
    $active_post_query = mysqli_query($conn, "SELECT COUNT(*) AS count FROM post WHERE postStatus = 'Y'");
    $active_post_data = mysqli_fetch_assoc($active_post_query);
    $active_posts = $active_post_data['count'] ?? 0;

    // Pending Posts
    $pending_post_query = mysqli_query($conn, "SELECT COUNT(*) AS count FROM post WHERE postStatus = 'W'");
    $pending_post_data = mysqli_fetch_assoc($pending_post_query);
    $pending_posts = $pending_post_data['count'] ?? 0;

    // Categories
    $category_query = mysqli_query($conn, 'SELECT COUNT(*) AS count FROM category');
    $category_data = mysqli_fetch_assoc($category_query);
    $total_categories = $category_data['count'] ?? 0;

    // Total Users
    $user_query = mysqli_query($conn, 'SELECT COUNT(*) AS count FROM user');
    $user_data = mysqli_fetch_assoc($user_query);
    $total_users = $user_data['count'] ?? 0;
    ?>

    <!-- Stats metric cards -->
    <div class="row">
      <!-- Total Visitors Card -->
      <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #1e90ff !important;">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="text-muted font-weight-bold" style="font-size: 13px; text-transform: uppercase;">Total Visitors</span>
              <div class="avatar bg-light-primary rounded p-1" style="background: rgba(30,144,255,0.1); color: #1e90ff;">
                <i class="bx bx-show fs-3"></i>
              </div>
            </div>
            <h3 class="mb-1 font-weight-bold"><?php echo number_format($total_visitors); ?></h3>
          </div>
        </div>
      </div>
      <!-- Active Posts Card -->
      <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #10b981 !important;">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="text-muted font-weight-bold" style="font-size: 13px; text-transform: uppercase;">Active Articles</span>
              <div class="avatar bg-light-success rounded p-1" style="background: rgba(16,185,129,0.1); color: #10b981;">
                <i class="bx bx-check-circle fs-3"></i>
              </div>
            </div>
            <h3 class="mb-1 font-weight-bold"><?php echo number_format($active_posts); ?></h3>
          </div>
        </div>
      </div>
      <!-- Pending Approvals Card -->
      <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #f59e0b !important;">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="text-muted font-weight-bold" style="font-size: 13px; text-transform: uppercase;">Pending Approval</span>
              <div class="avatar bg-light-warning rounded p-1" style="background: rgba(245,158,11,0.1); color: #f59e0b;">
                <i class="bx bx-time fs-3"></i>
              </div>
            </div>
            <h3 class="mb-1 font-weight-bold"><?php echo number_format($pending_posts); ?></h3>
          </div>
        </div>
      </div>
      <!-- Categories Card -->
      <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
        <div class="card border-0 shadow-sm" style="border-left: 4px solid #8b5cf6 !important;">
          <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-2">
              <span class="text-muted font-weight-bold" style="font-size: 13px; text-transform: uppercase;">Total Categories</span>
              <div class="avatar bg-light-info rounded p-1" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                <i class="bx bx-category fs-3"></i>
              </div>
            </div>
            <h3 class="mb-1 font-weight-bold"><?php echo number_format($total_categories); ?></h3>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4">
      <!-- Visitor Trend Area Chart -->
      <div class="col-md-7 mb-4 mb-md-0">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title font-weight-bold mb-4">Visitor Traffic Overview</h5>
            <div id="visitorChart"></div>
          </div>
        </div>
      </div>
      <!-- Category distribution Donut Chart -->
      <div class="col-md-5">
        <div class="card border-0 shadow-sm h-100">
          <div class="card-body">
            <h5 class="card-title font-weight-bold mb-4">Articles by Category</h5>
            <div id="categoryChart"></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Action Cards Grid -->
    <h5 class="font-weight-bold mb-3 mt-2">Quick Administration Actions</h5>
    <div class="row">
      <!-- Add New Post -->
      <div class="col-md-6 col-lg-3 mb-4">
        <div class="card border-0 shadow-sm text-center h-100 transition-hover">
          <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
            <div class="p-3 bg-light-primary rounded-circle mb-3" style="background: rgba(30,144,255,0.1); color: #1e90ff;">
              <i class="bx bx-plus fs-1"></i>
            </div>
            <h5 class="font-weight-bold mb-1">Create Article</h5>
            <p class="text-muted small mb-3">Compose and publish a news story</p>
            <a href="new-post.php" class="btn btn-outline-primary btn-sm w-100 rounded-pill">Create Post</a>
          </div>
        </div>
      </div>

      <!-- View & Manage Posts -->
      <div class="col-md-6 col-lg-3 mb-4">
        <div class="card border-0 shadow-sm text-center h-100 transition-hover">
          <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
            <div class="p-3 bg-light-success rounded-circle mb-3" style="background: rgba(16,185,129,0.1); color: #10b981;">
              <i class="bx bx-list-ul fs-1"></i>
            </div>
            <h5 class="font-weight-bold mb-1">Manage Articles</h5>
            <p class="text-muted small mb-3">Edit, delete, and view all posts</p>
            <a href="view-post.php" class="btn btn-outline-success btn-sm w-100 rounded-pill">View Posts</a>
          </div>
        </div>
      </div>

      <?php if ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) { ?>
        <!-- Manage Categories -->
        <div class="col-md-6 col-lg-3 mb-4">
          <div class="card border-0 shadow-sm text-center h-100 transition-hover">
            <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
              <div class="p-3 bg-light-info rounded-circle mb-3" style="background: rgba(139,92,246,0.1); color: #8b5cf6;">
                <i class="bx bx-category-alt fs-1"></i>
              </div>
              <h5 class="font-weight-bold mb-1">Categories</h5>
              <p class="text-muted small mb-3">Add & manage category filters</p>
              <a href="view-category.php" class="btn btn-outline-secondary btn-sm w-100 rounded-pill" style="color: #8b5cf6; border-color: #8b5cf6;">Manage Categories</a>
            </div>
          </div>
        </div>

        <!-- Manage Users -->
        <div class="col-md-6 col-lg-3 mb-4">
          <div class="card border-0 shadow-sm text-center h-100 transition-hover">
            <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
              <div class="p-3 bg-light-danger rounded-circle mb-3" style="background: rgba(239,68,68,0.1); color: #ef4444;">
                <i class="bx bx-user-voice fs-1"></i>
              </div>
              <h5 class="font-weight-bold mb-1">Team & Users</h5>
              <p class="text-muted small mb-3">Add anchors and operators</p>
              <a href="view-user.php" class="btn btn-outline-danger btn-sm w-100 rounded-pill">Manage Users</a>
            </div>
          </div>
        </div>
      <?php } else { ?>
        <!-- User Settings (anchor / operator fallback) -->
        <div class="col-md-6 col-lg-3 mb-4">
          <div class="card border-0 shadow-sm text-center h-100 transition-hover">
            <div class="card-body d-flex flex-column align-items-center justify-content-center p-4">
              <div class="p-3 bg-light-danger rounded-circle mb-3" style="background: rgba(239,68,68,0.1); color: #ef4444;">
                <i class="bx bx-cog fs-1"></i>
              </div>
              <h5 class="font-weight-bold mb-1">My Settings</h5>
              <p class="text-muted small mb-3">Update your profile settings</p>
              <a href="setting.php" class="btn btn-outline-danger btn-sm w-100 rounded-pill">Edit Settings</a>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>

  </div>
</div>

<?php
// Query categories and post counts for dynamic donut chart
$chart_categories = [];
$chart_counts = [];
$chart_sql = 'SELECT category.category_name, COUNT(post.post_id) AS post_count 
              FROM category 
              LEFT JOIN post ON post.category = category.category_id 
              GROUP BY category.category_id';
$chart_result = mysqli_query($conn, $chart_sql);
if ($chart_result && mysqli_num_rows($chart_result) > 0) {
  while ($chart_row = mysqli_fetch_assoc($chart_result)) {
    $chart_categories[] = $chart_row['category_name'];
    $chart_counts[] = (int) $chart_row['post_count'];
  }
}
?>

<!-- Style Overrides -->
<style>
  .transition-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease !important;
  }
  .transition-hover:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
  }
</style>

<!-- Chart script implementation -->
<script>
  function initDashboardCharts() {
    if (typeof ApexCharts === 'undefined') return;

    // Categories donut chart
    var categoryEl = document.querySelector("#categoryChart");
    if (categoryEl) {
      categoryEl.innerHTML = '';
      var categoryOptions = {
        series: <?php echo json_encode($chart_counts); ?>,
        chart: {
          type: 'donut',
          height: 280
        },
        labels: <?php echo json_encode($chart_categories); ?>,
        responsive: [{
          breakpoint: 480,
          options: {
            chart: {
              width: 200
            },
            legend: {
              position: 'bottom'
            }
          }
        }],
        colors: ['#1e90ff', '#10b981', '#f59e0b', '#8b5cf6', '#06b6d4', '#ec4899']
      };

      var categoryChart = new ApexCharts(categoryEl, categoryOptions);
      categoryChart.render();
    }

    // Visitor trends area chart
    var visitorEl = document.querySelector("#visitorChart");
    if (visitorEl) {
      visitorEl.innerHTML = '';
      var visitorOptions = {
        series: [{
          name: 'Weekly Visitors',
          data: [120, 190, 150, 280, 240, 390, 420]
        }],
        chart: {
          height: 280,
          type: 'area',
          toolbar: {
            show: false
          }
        },
        dataLabels: {
          enabled: false
        },
        stroke: {
          curve: 'smooth',
          colors: ['#1e90ff']
        },
        xaxis: {
          categories: ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"]
        },
        fill: {
          type: 'gradient',
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.1,
            stops: [0, 90, 100]
          }
        }
      };

      var visitorChart = new ApexCharts(visitorEl, visitorOptions);
      visitorChart.render();
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
  } else {
    initDashboardCharts();
  }
</script>

<?php
include_once '_footer.php';
?>