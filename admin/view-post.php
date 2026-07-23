<?php include_once "_header.php";
include_once "_subHeader.php"; ?>

<?php
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}
$offset = ($page - 1) * $limit;
//News Post Listing Codes ===================================================
$author_id = $_SESSION['author_id'];
// Admin View Post Query 
if ($_SESSION['role'] == 1) {
    if (isset($_REQUEST['catID'])) {
        $categoryId = base64_decode($_REQUEST['catID']);
        $selectPost = "SELECT * FROM `post`
        LEFT JOIN category ON category.category_id = post.category 
        LEFT JOIN user ON user.user_id = post.author 
        WHERE post.category = '{$categoryId}'
        ORDER BY post_id DESC
        LIMIT {$offset},{$limit}";
    } else {
        $selectPost = "SELECT * FROM `post`
        LEFT JOIN category ON category.category_id = post.category 
        LEFT JOIN user ON user.user_id = post.author 
        ORDER BY post_id DESC
        LIMIT {$offset},{$limit}";
    }
} else {
    if (isset($_REQUEST['catID'])) {
        $categoryId = base64_decode($_REQUEST['catID']);
        $selectPost = "SELECT * FROM `post`
        LEFT JOIN category ON category.category_id = post.category 
        LEFT JOIN user ON user.user_id = post.author 
        WHERE post.author = '{$author_id}' && post.category = '{$categoryId}'
        ORDER BY post_id DESC
        LIMIT {$offset},{$limit}";
    } else {
        $selectPost = "SELECT * FROM `post`
        LEFT JOIN category ON category.category_id = post.category 
        LEFT JOIN user ON user.user_id = post.author 
        WHERE post.author = {$author_id}
        ORDER BY post_id DESC
        LIMIT {$offset},{$limit}";
    }
}

$postQuery = mysqli_query($conn, $selectPost);
//Category Listing Codes ========================================================
$categoryQ = "SELECT * FROM category WHERE categoryStatus = 'Y'";
$categoryR = mysqli_query($conn, $categoryQ);
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Stat Cards Row -->
        <div class="row g-4 mb-4">
            <!-- Inactive Posts Card -->
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-semibold d-block mb-1">Inactive Posts</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php
                                    if ($_SESSION['role'] == 1) {
                                        $inactive = "SELECT COUNT(*) AS inactive FROM post WHERE postStatus = 'N'";
                                    } else {
                                        $inactive = "SELECT COUNT(*) AS inactive FROM post WHERE postStatus = 'N' && author = '{$_SESSION['author_id']}'";
                                    }
                                    $inactiveResult = mysqli_query($conn, $inactive);
                                    if ($inactiveResult && mysqli_num_rows($inactiveResult) > 0) {
                                        while ($inactiveData = mysqli_fetch_assoc($inactiveResult)) { ?>
                                            <h3 class="card-title mb-0 me-2">
                                                <?php echo $inactiveData['inactive']; ?>
                                            </h3>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                            <span class="badge bg-label-danger rounded p-2">
                                <i class="bx bx-x-circle bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Active Posts Card -->
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-semibold d-block mb-1">Active Posts</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php
                                    if ($_SESSION['role'] == 1) {
                                        $postActive = "SELECT COUNT(*) AS postActives FROM post WHERE postStatus = 'Y'";
                                    } else {
                                        $postActive = "SELECT COUNT(*) AS postActives FROM post WHERE postStatus = 'Y' && author = '{$_SESSION['author_id']}'";
                                    }
                                    $postActiveResult = mysqli_query($conn, $postActive);
                                    if ($postActiveResult && mysqli_num_rows($postActiveResult) > 0) {
                                        while ($postActiveData = mysqli_fetch_assoc($postActiveResult)) { ?>
                                            <h3 class="card-title mb-0 me-2">
                                                <?php echo $postActiveData['postActives']; ?>
                                            </h3>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                            <span class="badge bg-label-success rounded p-2">
                                <i class="bx bx-check-circle bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Draft Posts Card -->
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-semibold d-block mb-1">Saved Drafts</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php
                                    if ($_SESSION['role'] == 1) {
                                        $pwait = "SELECT COUNT(*) AS pwait FROM post WHERE postStatus = 'W'";
                                    } else {
                                        $pwait = "SELECT COUNT(*) AS pwait FROM post WHERE postStatus = 'W' && author = '{$_SESSION['author_id']}'";
                                    }
                                    $pwaitResult = mysqli_query($conn, $pwait);
                                    if ($pwaitResult && mysqli_num_rows($pwaitResult) > 0) {
                                        while ($pwaitData = mysqli_fetch_assoc($pwaitResult)) { ?>
                                            <h3 class="card-title mb-0 me-2">
                                                <?php echo $pwaitData['pwait']; ?>
                                            </h3>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                            <span class="badge bg-label-warning rounded p-2">
                                <i class="bx bx-time-five bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Posts Table Card -->
        <div class="card">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between py-3">
                <h5 class="card-title mb-0">Posts List</h5>
                <a class="btn btn-primary" href="new-post.php">
                    <i class="bx bx-plus me-1"></i> Add New Post
                </a>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 35%;">TITLE</th>
                            <th style="width: 15%;">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Category Filter
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="./view-post.php">All Posts</a></li>
                                        <?php if ($categoryR && mysqli_num_rows($categoryR) > 0) {
                                            while ($categoryD = mysqli_fetch_assoc($categoryR)) {
                                                $encode_category = base64_encode($categoryD['category_id']);
                                                echo '<li><a class="dropdown-item" href="?catID=' . $encode_category . '">' . htmlspecialchars($categoryD['category_name']) . '</a></li>';
                                            }
                                        } ?>
                                    </ul>
                                </div>
                            </th>
                            <th style="width: 15%;">AUTHOR</th>
                            <th style="width: 15%;">DATE</th>
                            <th style="width: 10%;">STATUS</th>
                            <th style="width: 5%;" class="text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if ($postQuery && mysqli_num_rows($postQuery) > 0) {
                            $serial = $offset + 1;
                            while ($postD = mysqli_fetch_assoc($postQuery)) {
                                ?>
                                <tr>
                                    <td><strong><?php echo $serial ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars(substr($postD['title'], 0, 45)) . (strlen($postD['title']) > 45 ? '...' : ''); ?></strong></td>
                                    <td>
                                        <span class="badge bg-label-info">
                                            <?php echo htmlspecialchars($postD['category_name'] ?? 'Uncategorized'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(($postD['first_name'] ?? '') . " " . ($postD['last_name'] ?? '')); ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo htmlspecialchars($postD['post_date'] ?? ''); ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        if ($postD['postStatus'] == 'Y') {
                                            echo '<span class="badge bg-label-success me-1">Active</span>';
                                        } elseif ($postD['postStatus'] == 'N') {
                                            echo '<span class="badge bg-label-danger me-1">Inactive</span>';
                                        } elseif ($postD['postStatus'] == 'W') {
                                            echo '<span class="badge bg-label-warning me-1">Save Draft</span>';
                                        }
                                        ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-block text-nowrap">
                                            <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end m-0">
                                                <button type="button" class="dropdown-item postShow"
                                                    data-pid="<?php echo $postD['post_id']; ?>" data-bs-toggle="modal"
                                                    data-bs-target="#showPost"><i class='bx bx-show me-1'></i> View Post</button>
                                                <?php if ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) { ?>
                                                    <a class="dropdown-item"
                                                        href="update-post.php?postid=<?php echo base64_encode($postD['post_id']); ?>"><i class="bx bx-edit me-1"></i> Update Post</a>
                                                <?php } elseif ($postD['postStatus'] == 'N') { ?>
                                                    <a class="dropdown-item"
                                                        href="update-post.php?postid=<?php echo base64_encode($postD['post_id']); ?>"><i class="bx bx-edit me-1"></i> Update Post</a>
                                                <?php }
                                                if ($_SESSION['role'] == 1) { ?>
                                                    <a class="dropdown-item delete-record text-danger"
                                                        href="delete-post.php?check=<?php echo base64_encode($_SESSION['author_id']) ?>&postid=<?php echo base64_encode($postD['post_id']); ?>"><i class="bx bx-trash me-1"></i> Delete Post</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php $serial++;
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-4 text-muted'>No posts found.</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>

            <?php
            if (isset($_REQUEST['catID'])) {
                $decodeCat = base64_decode($_REQUEST['catID']);
                if ($_SESSION['role'] == 1) {
                    $pagination = "SELECT * FROM post WHERE category = '{$decodeCat}'";
                } else {
                    $pagination = "SELECT * FROM post WHERE post.author = '{$author_id}' && category = '{$decodeCat}'";
                }
            } else {
                if ($_SESSION['role'] == 1) {
                    $pagination = "SELECT * FROM post";
                } else {
                    $pagination = "SELECT * FROM post WHERE post.author = '{$author_id}'";
                }
            }

            $pagiResult = mysqli_query($conn, $pagination);
            if ($pagiResult && mysqli_num_rows($pagiResult) > 0) {
                $total_records = mysqli_num_rows($pagiResult);
                $total_pages = ceil($total_records / $limit); ?>
                <div class="card-footer text-muted border-top py-3">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mb-0">
                            <?php
                            if (isset($_REQUEST['catID'])) {
                                $decodeCatid = urlencode($_REQUEST['catID']);
                                if ($page > 1) {
                                    echo '<li class="page-item prev"><a class="page-link" href="?catID=' . $decodeCatid . '&page=' . ($page - 1) . '"><i class="tf-icon bx bx-chevrons-left"></i></a></li>';
                                }

                                for ($i = 1; $i <= $total_pages; $i++) {
                                    if ($i == $page) {
                                        $active = "active";
                                    } else {
                                        $active = "";
                                    }
                                    echo '<li class="page-item ' . $active . '"><a class="page-link" href="?catID=' . $decodeCatid . '&page=' . $i . '">' . $i . '</a></li>';
                                }
                                if ($total_pages > $page) {
                                    echo '<li class="page-item next"><a class="page-link" href="?catID=' . $decodeCatid . '&page=' . ($page + 1) . '"><i class="tf-icon bx bx-chevrons-right"></i></a></li>';
                                }
                            } else {
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