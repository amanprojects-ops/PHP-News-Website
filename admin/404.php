<?php 
include_once '_header.php'; 
include_once '_subHeader.php'; 
?>

<!-- Content wrapper -->
<div class="content-wrapper">
  <!-- Content -->
  <div class="container-xxl flex-grow-1 container-p-y">
    <div class="row justify-content-center align-items-center min-vh-75 py-4">
      <div class="col-lg-8 col-md-10 text-center">
        <!-- 404 Card Container -->
        <div class="card border-0 shadow-lg overflow-hidden style-404-card" style="border-radius: 1rem; background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);">
          <div class="card-body p-4 p-sm-5">
            
            <!-- Graphic / Badge Graphic -->
            <div class="misc-illustration my-3 position-relative d-inline-block">
              <div class="illustration-bg-glow"></div>
              <div class="display-1 fw-extrabold mb-0 error-code-text" style="font-size: 5.5rem; font-weight: 800; letter-spacing: -2px; line-height: 1;">
                4<span class="text-warning display-icon-pulse"><i class="bx bx-ghost mx-1"></i></span>4
              </div>
            </div>

            <!-- Title & Description -->
            <h2 class="h3 fw-bold text-dark mb-2">Oops! Page Not Found</h2>
            <p class="text-muted mb-4 mx-auto style-desc" style="max-width: 520px; font-size: 1.025rem; line-height: 1.6;">
              We couldn't find the page you were looking for in the Admin Control Panel. It might have been moved, deleted, or the link may be broken.
            </p>

            <!-- Action Buttons -->
            <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
              <a href="dashboard.php" class="btn btn-primary btn-lg px-4 py-2 shadow-sm rounded-pill d-inline-flex align-items-center gap-2 hover-lift">
                <i class="bx bx-home-circle fs-4"></i>
                <span>Back to Dashboard</span>
              </a>
              <button onclick="history.back()" class="btn btn-outline-secondary btn-lg px-4 py-2 rounded-pill d-inline-flex align-items-center gap-2 hover-lift">
                <i class="bx bx-left-arrow-alt fs-4"></i>
                <span>Go Previous Page</span>
              </button>
            </div>

            <!-- Quick Links Divider -->
            <div class="border-top pt-4 mt-2">
              <p class="text-uppercase text-muted fw-semibold small mb-3" style="letter-spacing: 1px;">Quick Navigation Shortcuts</p>
              <div class="row g-3 justify-content-center">
                <div class="col-6 col-sm-3">
                  <a href="view-post.php" class="card border h-100 p-3 text-decoration-none quick-link-card rounded-3 transition-all">
                    <div class="text-primary mb-2"><i class="bx bx-news fs-2"></i></div>
                    <span class="fw-semibold text-dark small">Manage Posts</span>
                  </a>
                </div>
                <div class="col-6 col-sm-3">
                  <a href="view-category.php" class="card border h-100 p-3 text-decoration-none quick-link-card rounded-3 transition-all">
                    <div class="text-success mb-2"><i class="bx bx-category fs-2"></i></div>
                    <span class="fw-semibold text-dark small">Categories</span>
                  </a>
                </div>
                <div class="col-6 col-sm-3">
                  <a href="view-user.php" class="card border h-100 p-3 text-decoration-none quick-link-card rounded-3 transition-all">
                    <div class="text-info mb-2"><i class="bx bx-group fs-2"></i></div>
                    <span class="fw-semibold text-dark small">Users</span>
                  </a>
                </div>
                <div class="col-6 col-sm-3">
                  <a href="setting.php" class="card border h-100 p-3 text-decoration-none quick-link-card rounded-3 transition-all">
                    <div class="text-warning mb-2"><i class="bx bx-cog fs-2"></i></div>
                    <span class="fw-semibold text-dark small">Settings</span>
                  </a>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
.hover-lift {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 20px rgba(105, 108, 255, 0.25) !important;
}
.quick-link-card {
  transition: all 0.25s ease-in-out;
  background-color: #ffffff;
}
.quick-link-card:hover {
  transform: translateY(-4px);
  border-color: #696cff !important;
  box-shadow: 0 6px 15px rgba(105, 108, 255, 0.12);
}
.display-icon-pulse {
  display: inline-block;
  animation: floatGhost 3s ease-in-out infinite;
}
@keyframes floatGhost {
  0%, 100% { transform: translateY(0) scale(1); }
  50% { transform: translateY(-8px) scale(1.05); }
}
.illustration-bg-glow {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 140px;
  height: 140px;
  background: radial-gradient(circle, rgba(105, 108, 255, 0.15) 0%, rgba(255, 255, 255, 0) 70%);
  border-radius: 50%;
  z-index: 0;
  pointer-events: none;
}
.error-code-text {
  position: relative;
  z-index: 1;
  background: linear-gradient(135deg, #696cff 0%, #3939ac 100%);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}
</style>

<?php include_once '_footer.php'; ?>