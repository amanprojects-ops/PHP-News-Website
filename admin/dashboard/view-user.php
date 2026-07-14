<?php include_once "_header.php";
include_once "_subHeader.php"; ?>
<?php
include_once "../app/_DBconnect.php";

if (isset($_GET['page'])) {
    $page = @$_GET['page'];
} else {
    $page = 1;
}
// $limit = 3;
$offset = ($page - 1) * $limit;
if ($_SESSION['author_id'] != 1) {
    $selectUser = "SELECT * FROM user WHERE taken={$_SESSION['author_id']} ORDER BY user_id DESC
        LIMIT {$offset},{$limit}";
} else {
    $selectUser = "SELECT * FROM user ORDER BY user_id DESC
        LIMIT {$offset},{$limit}";

}

$userQuery = mysqli_query($conn, $selectUser); ?>
<!-- Content -->
<div class="container-fluid flex-grow-1 container-p-y">

    <!-- Layout Demo -->
    <div class="container my-5">
        <div class="row g-4 mb-4">

            <div class="col-sm-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Inactive Users</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php include_once "../app/_DBconnect.php";
                                    if ($_SESSION['author_id'] != 1) {
                                        $userCounts = "SELECT COUNT(*) AS userCount FROM user WHERE userStatus = 'N' && taken={$_SESSION['author_id']}";
                                    } else {
                                        $userCounts = "SELECT COUNT(*) AS userCount FROM user WHERE userStatus = 'N'";

                                    }
                                    $userResult = mysqli_query($conn, $userCounts);
                                    if (mysqli_num_rows($userResult) > 0) {
                                        while ($userData = mysqli_fetch_assoc($userResult)) { ?>
                                            <h4 class="mb-0 me-2">
                                                <?php echo @$userData['userCount']; ?>
                                            </h4>
                                        <?php }
                                    } ?>
                                    <!-- <small class="text-success">(+18%)</small> -->
                                </div>
                                <!-- <small>Last week analytics </small> -->
                            </div>
                            <span class="badge bg-label-danger rounded p-2">
                                <i class="bx bx-error bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Active Users</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php  include_once "../app/_DBconnect.php";
                                     if ($_SESSION['author_id'] != 1) {
                                        $UserCounts = "SELECT COUNT(*) AS catCount FROM user WHERE UserStatus = 'Y' && taken={$_SESSION['author_id']}";
                                     } else {
                                        $UserCounts = "SELECT COUNT(*) AS catCount FROM user WHERE UserStatus = 'Y'";

                                     }
                                     $UserResult = mysqli_query($conn, $UserCounts);
                                     if (mysqli_num_rows($UserResult) > 0) {
                                            while ($UserData = mysqli_fetch_assoc($UserResult)) { ?>
                                            <h4 class="mb-0 me-2">
                                                <?php echo @$UserData['catCount']; ?>
                                            </h4>
                                        <?php } } ?>
                                    <!-- <small class="text-danger">(-14%)</small> -->
                                </div>
                                <!-- <small>Last week analytics</small> -->
                            </div>
                            <span class="badge bg-label-success rounded p-2">
                                <!-- <i class="bx bx-group bx-sm"></i> -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    style="fill: rgba(0, 0, 0, 1);">
                                    <circle cx="17" cy="4" r="2"></circle>
                                    <path
                                        d="M15.777 10.969a2.007 2.007 0 0 0 2.148.83l3.316-.829-.483-1.94-3.316.829-1.379-2.067a2.01 2.01 0 0 0-1.272-.854l-3.846-.77a1.998 1.998 0 0 0-2.181 1.067l-1.658 3.316 1.789.895 1.658-3.317 1.967.394L7.434 17H3v2h4.434c.698 0 1.355-.372 1.715-.971l1.918-3.196 5.169 1.034 1.816 5.449 1.896-.633-1.815-5.448a2.007 2.007 0 0 0-1.506-1.33l-3.039-.607 1.772-2.954.417.625z">
                                    </path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Save User draft</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php include_once "../app/_DBconnect.php";
                                    if($_SESSION['author_id'] != 1){
                                    $UserCounts = "SELECT COUNT(*) AS catCount FROM user WHERE UserStatus = 'W' && taken={$_SESSION['author_id']}";
                                }else{
                                        $UserCounts = "SELECT COUNT(*) AS catCount FROM user WHERE UserStatus = 'W'";

                                    }
                                    $UserResult = mysqli_query($conn, $UserCounts);
                                    if (mysqli_num_rows($UserResult) > 0) {
                                        while ($UserData = mysqli_fetch_assoc($UserResult)) { ?>
                                            <h4 class="mb-0 me-2">
                                                <?php echo @$UserData['catCount']; ?>
                                            </h4>
                                        <?php }
                                    } ?>
                                    <!-- <small class="text-success">(+42%)</small> -->
                                </div>
                                <!-- <small>Last week analytics</small> -->
                            </div>
                            <span class="badge bg-label-warning rounded p-2">
                                <!-- <i class="bx bx-user-voice bx-sm"></i> -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    style="fill: rgba(0, 0, 0, 1);">
                                    <path
                                        d="M19.903 8.586a.997.997 0 0 0-.196-.293l-6-6a.997.997 0 0 0-.293-.196c-.03-.014-.062-.022-.094-.033a.991.991 0 0 0-.259-.051C13.04 2.011 13.021 2 13 2H6c-1.103 0-2 .897-2 2v16c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2V9c0-.021-.011-.04-.013-.062a.952.952 0 0 0-.051-.259c-.01-.032-.019-.063-.033-.093zM16.586 8H14V5.414L16.586 8zM6 20V4h6v5a1 1 0 0 0 1 1h5l.002 10H6z">
                                    </path>
                                    <path d="M8 12h8v2H8zm0 4h8v2H8zm0-8h2v2H8z"></path>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="divider text-success">
            <div class="divider-text text-danger">
                <i class="bx bx-star"></i>
                <i class="bx bx-star"></i>
                <i class="bx bx-star"></i>
            </div>
        </div>
        <div class="card">
            <!-- <h5 class="card-header">Hoverable rows</h5> -->
            <div class="card-header border-bottom">
                <h5 class="card-title">User Lists</h5>
                <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
                    <div class="col-md-4">
                        <a class="btn btn-primary" href="new-user.php">
                            <span>
                                <i class="bx bx-plus me-0 me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add New User</span>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <button disabled="disabled" class="btn btn-outline-secoundary">NO.</button>
                            </th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Name</button></th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Username</button></th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Mobile</button></th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Role</button>
                            </th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Status</button>
                            </th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Actions</button></th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if (mysqli_num_rows($userQuery) > 0) {
                            $serial = $offset + 1;
                            while ($userD = mysqli_fetch_assoc($userQuery)) {
                                ?>
                                <tr>
                                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>
                                            <?php echo $serial ?>
                                        </strong></td>
                                    <td><strong>
                                            <?php echo @$userD['first_name'] . " " . @$userD['last_name']; ?>
                                        </strong></td>
                                    <td>
                                        <?php echo @$userD['username']; ?>
                                    </td>
                                    <td>
                                        <?php echo @$userD['phone']; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if (@$userD['role'] == 1) {
                                            echo 'Admin User';
                                        } elseif (@$userD['role'] == 2) {
                                            echo 'Sub-Admin User';
                                        } elseif (@$userD['role'] == 3) {
                                            echo 'News Anchor';
                                        } else {
                                            echo 'Oprator';
                                        } ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($userD['userStatus'] == 'Y') {
                                            echo '<span class="badge bg-label-success me-1">Active</span></td>';
                                        } elseif ($userD['userStatus'] == 'N') {
                                            echo '<span class="badge bg-label-danger me-1">Inactive</span></td>';
                                        } elseif ($userD['userStatus'] == 'W') {
                                            echo '<span class="badge bg-label-warning me-1">Save Draft</span></td>';
                                        }
                                        $author = base64_encode(@$_SESSION['author_id']);
                                        ?>

                                    <td>
                                        <div class="d-inline-block text-nowrap">
                                            <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end m-0">
                                                <button type="button" class="dropdown-item userShow"
                                                    data-uid="<?php echo @$userD['user_id']; ?>" data-bs-toggle="modal"
                                                    data-bs-target="#showUser"><i class='bx bx-show'></i>&nbsp; View
                                                    User</button>
                                                <?php if($_SESSION['role'] == 1){if($userD['userStatus'] == 'Y'){?>
                                                    <a class="dropdown-item"
                                                    href="update-user.php?check-user=<?php echo @$author; ?>&userid=<?php echo base64_encode(@$userD['user_id']); ?>"
                                                    class="btn btn-sm btn-icon"><i class="bx bx-edit"></i>&nbsp; Update User</a>
                                                     <?php } } if($userD['userStatus'] == 'N' || $userD['userStatus'] == 'W'){ ?>
                                                <a class="dropdown-item"
                                                    href="update-user.php?check-user=<?php echo @$author; ?>&userid=<?php echo base64_encode(@$userD['user_id']); ?>"
                                                    class="btn btn-sm btn-icon"><i class="bx bx-edit"></i>&nbsp; Update User</a>
                                                <?php } ?>
                                                <?php if($_SESSION['role'] == 1){ ?>
                                                <a class="dropdown-item"
                                                    href="delete-user.php?check-user=<?php echo @$author; ?>&userid=<?php echo base64_encode(@$userD['user_id']); ?>"
                                                    class="btn btn-sm btn-icon delete-record"><i
                                                        class="bx bx-trash"></i>&nbsp;Delete User</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php $serial++;
                            }
                        } else {
                            echo "<tr><td></td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
            <?php

            $pagination = "SELECT * FROM user WHERE taken={$_SESSION['author_id']}";
            $pagiResult = mysqli_query($conn, $pagination);
            if (mysqli_num_rows($pagiResult) > 0) {
                $total_records = mysqli_num_rows($pagiResult);
                $total_pages = ceil($total_records / $limit); ?>
                <div class="card-footer text-muted">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
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
        <!--/ Layout Demo -->
    </div>
</div>


<!-- / Content -->
<?php include_once "_footer.php"; ?>