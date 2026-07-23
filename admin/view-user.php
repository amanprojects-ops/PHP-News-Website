<?php include_once "_header.php";
include_once "_subHeader.php"; ?>
<?php
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}
$offset = ($page - 1) * $limit;
if ($_SESSION['role'] == 1) {
    $selectUser = "SELECT * FROM user ORDER BY user_id DESC LIMIT {$offset},{$limit}";
} else {
    $selectUser = "SELECT * FROM user WHERE taken={$_SESSION['author_id']} ORDER BY user_id DESC LIMIT {$offset},{$limit}";
}

$userQuery = mysqli_query($conn, $selectUser); ?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Stat Cards Row -->
        <div class="row g-4 mb-4">
            <!-- Inactive Users Card -->
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-semibold d-block mb-1">Inactive Users</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php
                                    if ($_SESSION['role'] == 1) {
                                        $userCounts = "SELECT COUNT(*) AS userCount FROM user WHERE userStatus = 'N'";
                                    } else {
                                        $userCounts = "SELECT COUNT(*) AS userCount FROM user WHERE userStatus = 'N' && taken={$_SESSION['author_id']}";
                                    }
                                    $userResult = mysqli_query($conn, $userCounts);
                                    if ($userResult && mysqli_num_rows($userResult) > 0) {
                                        while ($userData = mysqli_fetch_assoc($userResult)) { ?>
                                            <h3 class="card-title mb-0 me-2">
                                                <?php echo $userData['userCount']; ?>
                                            </h3>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                            <span class="badge bg-label-danger rounded p-2">
                                <i class="bx bx-user-x bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Users Card -->
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-semibold d-block mb-1">Active Users</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php
                                     if ($_SESSION['role'] == 1) {
                                        $UserCounts = "SELECT COUNT(*) AS userCount FROM user WHERE userStatus = 'Y'";
                                     } else {
                                        $UserCounts = "SELECT COUNT(*) AS userCount FROM user WHERE userStatus = 'Y' && taken={$_SESSION['author_id']}";
                                     }
                                     $UserResult = mysqli_query($conn, $UserCounts);
                                     if ($UserResult && mysqli_num_rows($UserResult) > 0) {
                                            while ($UserData = mysqli_fetch_assoc($UserResult)) { ?>
                                            <h3 class="card-title mb-0 me-2">
                                                <?php echo $UserData['userCount']; ?>
                                            </h3>
                                        <?php } } ?>
                                </div>
                            </div>
                            <span class="badge bg-label-success rounded p-2">
                                <i class="bx bx-user-check bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Draft Users Card -->
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-semibold d-block mb-1">Saved Drafts</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php
                                    if ($_SESSION['role'] == 1) {
                                        $UserCounts = "SELECT COUNT(*) AS userCount FROM user WHERE userStatus = 'W'";
                                    } else {
                                        $UserCounts = "SELECT COUNT(*) AS userCount FROM user WHERE userStatus = 'W' && taken={$_SESSION['author_id']}";
                                    }
                                    $UserResult = mysqli_query($conn, $UserCounts);
                                    if ($UserResult && mysqli_num_rows($UserResult) > 0) {
                                        while ($UserData = mysqli_fetch_assoc($UserResult)) { ?>
                                            <h3 class="card-title mb-0 me-2">
                                                <?php echo $UserData['userCount']; ?>
                                            </h3>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                            <span class="badge bg-label-warning rounded p-2">
                                <i class="bx bx-user-voice bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Lists Table Card -->
        <div class="card">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between py-3">
                <h5 class="card-title mb-0">User Lists</h5>
                <a class="btn btn-primary" href="new-user.php">
                    <i class="bx bx-plus me-1"></i> Add New User
                </a>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 25%;">NAME</th>
                            <th style="width: 20%;">USERNAME</th>
                            <th style="width: 15%;">MOBILE</th>
                            <th style="width: 15%;">ROLE</th>
                            <th style="width: 10%;">STATUS</th>
                            <th style="width: 10%;" class="text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if ($userQuery && mysqli_num_rows($userQuery) > 0) {
                            $serial = $offset + 1;
                            while ($userD = mysqli_fetch_assoc($userQuery)) {
                                ?>
                                <tr>
                                    <td><strong><?php echo $serial ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars(($userD['first_name'] ?? '') . " " . ($userD['last_name'] ?? '')); ?></strong></td>
                                    <td>
                                        <?php echo htmlspecialchars($userD['username'] ?? ''); ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo htmlspecialchars($userD['phone'] ?? 'N/A'); ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        if (($userD['role'] ?? 0) == 1) {
                                            echo '<span class="badge bg-label-primary">Admin User</span>';
                                        } elseif (($userD['role'] ?? 0) == 2) {
                                            echo '<span class="badge bg-label-info">Sub-Admin User</span>';
                                        } elseif (($userD['role'] ?? 0) == 3) {
                                            echo '<span class="badge bg-label-secondary">News Anchor</span>';
                                        } else {
                                            echo '<span class="badge bg-label-warning">Operator</span>';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (($userD['userStatus'] ?? '') == 'Y') {
                                            echo '<span class="badge bg-label-success me-1">Active</span>';
                                        } elseif (($userD['userStatus'] ?? '') == 'N') {
                                            echo '<span class="badge bg-label-danger me-1">Inactive</span>';
                                        } elseif (($userD['userStatus'] ?? '') == 'W') {
                                            echo '<span class="badge bg-label-warning me-1">Save Draft</span>';
                                        }
                                        $author = base64_encode($_SESSION['author_id']);
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-block text-nowrap">
                                            <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end m-0">
                                                <button type="button" class="dropdown-item userShow"
                                                    data-uid="<?php echo $userD['user_id']; ?>" data-bs-toggle="modal"
                                                    data-bs-target="#showUser"><i class='bx bx-show me-1'></i> View User</button>
                                                <?php if ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) { ?>
                                                    <a class="dropdown-item"
                                                        href="update-user.php?check-user=<?php echo $author; ?>&userid=<?php echo base64_encode($userD['user_id']); ?>"><i class="bx bx-edit me-1"></i> Update User</a>
                                                <?php } ?>
                                                <?php if ($_SESSION['role'] == 1) { ?>
                                                    <a class="dropdown-item delete-record text-danger"
                                                        href="delete-user.php?check-user=<?php echo $author; ?>&userid=<?php echo base64_encode($userD['user_id']); ?>"><i class="bx bx-trash me-1"></i> Delete User</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php $serial++;
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-4 text-muted'>No users found.</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>

            <?php
            if ($_SESSION['role'] == 1) {
                $pagination = "SELECT * FROM user";
            } else {
                $pagination = "SELECT * FROM user WHERE taken={$_SESSION['author_id']}";
            }
            $pagiResult = mysqli_query($conn, $pagination);
            if ($pagiResult && mysqli_num_rows($pagiResult) > 0) {
                $total_records = mysqli_num_rows($pagiResult);
                $total_pages = ceil($total_records / $limit); ?>
                <div class="card-footer text-muted border-top py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <?php
                            if ($page > 1) {
                                echo '<li class="page-item prev"><a class="page-link" href="?page=' . ($page - 1) . '"><i class="tf-icon bx bx-chevrons-left"></i></a></li>';
                            }

                            for ($i = 1; $i <= $total_pages; $i++) {
                                if ($i == $page) {
                                    $active = "active";
                                } else {
                                    $active = "";
                                }
                                echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                            }
                            if ($total_pages > $page) {
                                echo '<li class="page-item next"><a class="page-link" href="?page=' . ($page + 1) . '"><i class="tf-icon bx bx-chevrons-right"></i></a></li>';
                            }
                            ?>
                        </ul>
                    </nav>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- / Content -->
<?php include_once "_footer.php"; ?>