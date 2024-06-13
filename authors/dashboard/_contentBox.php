<!-- Content -->

<div class="container-xxl flex-grow-1 container-p-y">
  <div class="row">
    <div class="col-lg-8 mb-4 order-0">
      <div class="card">
        <div class="d-flex align-items-end row">
          <div class="col-sm-7">
            <div class="card-body">
              <h5 class="card-title text-primary">Welcome
                <?php echo $_SESSION['name']; ?>! 🎉
              </h5>

              <!-- <a href="javascript:;" class="btn btn-sm btn-outline-primary">View Badges</a> -->
            </div>
          </div>
          <div class="col-sm-5 text-center text-sm-left">
            <div class="card-body pb-0 px-0 px-md-4">
              <img src="../assets/img/man-with-laptop-light.png" height="140" loading="lazy" alt="View User"
                data-app-dark-img="/man-with-laptop-dark.png"
                data-app-light-img="/man-with-laptop-light.png" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-4 col-md-4 order-1">
      <div class="row">
        <div class="col-lg-6 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">

              </div>
              <span class="fw-semibold d-block mb-1">Total Visitor</span>
              <?php include_once "../app/_DBconnect.php";
              $visitor = "SELECT COUNT(*) AS visitor FROM visitors";
              $visitorResult = mysqli_query($conn, $visitor);
              if (mysqli_num_rows($visitorResult) > 0) {
                while ($visitorData = mysqli_fetch_assoc($visitorResult)) { ?>
                  <h3 class="card-title mb-2">
                    <?php echo $visitorData['visitor']; ?>
                  </h3>
                <?php }
              } ?>
              <!-- <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +72.80%</small> -->
            </div>
          </div>
        </div>
        <div class="col-lg-6 col-md-12 col-6 mb-4">
          <div class="card">
            <div class="card-body">
              <div class="card-title d-flex align-items-start justify-content-between">

              </div>
              <span>Active Post</span>
              <?php include_once "../app/_DBconnect.php";
              $postCounts = "SELECT COUNT(*) AS postCount FROM post WHERE postStatus = 'Y'";
              $postResult = mysqli_query($conn, $postCounts);
              if (mysqli_num_rows($postResult) > 0) {
                while ($postData = mysqli_fetch_assoc($postResult)) { ?>
                  <h3 class="card-title text-nowrap mb-1">
                    <?php echo $postData['postCount']; ?>
                  </h3>
                <?php }
              } ?>
              <!-- <small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i> +28.42%</small> -->
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Total Revenue -->
  </div>
  <!-- / Content -->
  <div class="row mb-5">
    <div class="col-md-6 col-lg-4">
      <div class="card mb-3 text-center">
        <div class="card-body">
          <h5 class="card-title">Add New Post</h5>
          <a href='new-post.php' class="btn btn-primary btn-lg"> <i class="bx bx-plus me-0 me-sm-1"></i> Add New
            Post</a>
        </div>
      </div>
    </div>
    <?php if ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) { ?>
      <div class="col-md-6 col-lg-4">
        <div class="card text-center mb-3">
          <div class="card-body">
            <h5 class="card-title">Add New Category</h5>
            <a href='new-category.php' class="btn btn-primary btn-lg"> <i class="bx bx-plus me-0 me-sm-1"></i> Add New
              Category</a>
          </div>
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="card text-center mb-3">
          <div class="card-body">
            <h5 class="card-title">Add New User</h5>
            <a href='new-user.php' class="btn btn-primary btn-lg"> <i class="bx bx-plus me-0 me-sm-1"></i> Add New
              User</a>
          </div>
        </div>
      </div>

    <?php } else {
      if ($_SESSION['role'] == 3) { ?>
        <div class="col-md-6 col-lg-4">
          <div class="card text-center mb-3">
            <div class="card-body">
              <h5 class="card-title">Add New User</h5>
              <a href='new-user.php' class="btn btn-primary btn-lg"> <i class="bx bx-plus me-0 me-sm-1"></i> Add New
                User</a>
            </div>
          </div>
        </div>
      <?php }
    } ?>
  </div>
  <div class="row mb-5">
    <div class="col-md-6 col-lg-4">
      <div class="card mb-3 text-center">
        <div class="card-body">
          <h5 class="card-title">View Posts</h5>
          <a href='view-post.php' class="btn btn-primary btn-lg"><i class='bx bx-list-ol'></i>&nbsp; Show Posts</a>
        </div>
      </div>
    </div>
    <?php if ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) { ?>
      <div class="col-md-6 col-lg-4">
        <div class="card text-center mb-3">
          <div class="card-body">
            <h5 class="card-title">View Category</h5>
            <a href='view-category.php' class="btn btn-primary btn-lg"><i class='bx bx-list-ol'></i>&nbsp; Show
              Category</a>
          </div>
         
        </div>
      </div>
      <div class="col-md-6 col-lg-4">
        <div class="card text-center mb-3">
          <div class="card-body">
            <h5 class="card-title">View User</h5>
            <a href='view-user.php' class="btn btn-primary btn-lg"><i class='bx bx-list-ol'></i>&nbsp; Show User</a>
          </div>
        </div>
      </div>
    <?php } elseif ($_SESSION['role'] == 3) { ?>
      <div class="col-md-6 col-lg-4">
        <div class="card text-center mb-3">
          <div class="card-body">
            <h5 class="card-title">View User</h5>
            <a href='view-user.php' class="btn btn-primary btn-lg"><i class='bx bx-list-ol'></i>&nbsp; Show User</a>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>
</div>