<!-- Navbar / Top Header -->
<nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
    <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
      <i class="bx bx-menu bx-sm"></i>
    </a>
  </div>

  <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
    <div class="navbar-nav align-items-center">
      <div class="nav-item d-flex align-items-center">
        <i class="bx bx-search fs-4 lh-0 me-2 text-muted"></i>
        <span class="fw-semibold text-muted d-none d-sm-inline-block">Control Panel</span>
      </div>
    </div>

    <ul class="navbar-nav flex-row align-items-center ms-auto">
      <!-- User Dropdown -->
      <li class="nav-item navbar-dropdown dropdown-user dropdown">
        <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
          <div class="avatar avatar-online">
            <i class="menu-icon tf-icons bx bx-user fs-3 w-px-40 h-auto rounded-circle"></i>
          </div>
        </a>
        <ul class="dropdown-menu dropdown-menu-end">
          <li>
            <a class="dropdown-item" href="setting.php">
              <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                  <div class="avatar avatar-online">
                    <i class="menu-icon tf-icons bx bx-user fs-3"></i>
                  </div>
                </div>
                <div class="flex-grow-1">
                  <span class="fw-semibold d-block"><?php echo htmlspecialchars($_SESSION['name'] ?? $_SESSION['username'] ?? 'User'); ?></span>
                  <small class="text-muted">
                    <?php
                    $role = $_SESSION['role'] ?? 0;
                    if ($role == 1) {
                      echo 'Admin';
                    } elseif ($role == 2) {
                      echo 'Sub-admin';
                    } elseif ($role == 3) {
                      echo 'News Anchor';
                    } else {
                      echo 'Operator';
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
            <a class="dropdown-item text-danger" href="logout.php">
              <i class="bx bx-power-off me-2"></i>
              <span class="align-middle">Log Out</span>
            </a>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</nav>
<!-- / Navbar -->
