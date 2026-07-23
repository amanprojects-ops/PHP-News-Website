<?php include_once "_header.php";
include_once "_subHeader.php";

if (isset($_GET['check-user'])) {
    $userid = mysqli_real_escape_string($conn, base64_decode($_GET['userid']));

    $loadQ = "SELECT * FROM user WHERE user_id = '{$userid}'";

    $loadR = mysqli_query($conn, $loadQ);
    if ($loadR && mysqli_num_rows($loadR) > 0) {
        $loadData = mysqli_fetch_assoc($loadR);
    ?>
    <!-- Content wrapper -->
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="card mb-4">
                        <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                            <h5 class="mb-0">Update User Profile</h5>
                            <small class="text-muted float-end text-uppercase">
                                @<?php echo htmlspecialchars($loadData['username'] ?? ''); ?>
                            </small>
                        </div>
                        <div class="card-body py-4">
                            <form action="../app/app.php" method="POST">
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-3 mb-md-0">
                                            <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($loadData['user_id'] ?? ''); ?>">
                                                <input type="text" class="form-control" id="first_name" name="first_name"
                                                    value="<?php echo htmlspecialchars($loadData['first_name'] ?? ''); ?>" placeholder="Enter First Name" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="last_name">Last Name <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                                <input type="text" class="form-control" id="last_name" name="last_name"
                                                    value="<?php echo htmlspecialchars($loadData['last_name'] ?? ''); ?>" placeholder="Enter Last Name" required>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="userEmail">Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                        <input type="email" id="userEmail" name="userEmail" class="form-control"
                                            value="<?php echo htmlspecialchars($loadData['email'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="userMobile">Phone No <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                        <input type="text" id="userMobile" name="userMobile" maxlength="10"
                                            class="form-control phone-mask" value="<?php echo htmlspecialchars($loadData['phone'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="form-label" for="role">User Role <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-shield-quarter"></i></span>
                                        <select class="form-select" name="role" id="role" required>
                                            <?php
                                            $userRole = $loadData['role'] ?? 0;
                                            ?>
                                            <option value="1" <?php echo ($userRole == 1) ? 'selected' : ''; ?>>Admin</option>
                                            <option value="2" <?php echo ($userRole == 2) ? 'selected' : ''; ?>>Sub-Admin</option>
                                            <option value="3" <?php echo ($userRole == 3) ? 'selected' : ''; ?>>News Anchor</option>
                                            <option value="0" <?php echo ($userRole == 0) ? 'selected' : ''; ?>>Operator</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="submit" name="userUpdate" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Update User
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- / Content -->
    <?php
    } else {
        $_SESSION['warning'] = "User Data Not Found. Try again.";
        echo "<script>window.location.href='view-user.php';</script>";
    }
} else {
    echo "<script>window.location.href='view-user.php';</script>";
}

include_once "_footer.php"; ?>