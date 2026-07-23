<?php include_once "_header.php";
include_once "_subHeader.php"; ?>
<?php
if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}
$offset = ($page - 1) * $limit;
$selectCategory = "SELECT * FROM `category`
        LEFT JOIN user ON user.user_id = category.author 
        ORDER BY category_id DESC
        LIMIT {$offset},{$limit}";
$categoryQuery = mysqli_query($conn, $selectCategory);?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Stat Cards Row -->
        <div class="row g-4 mb-4">
            <!-- Inactive Categories Card -->
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-semibold d-block mb-1">Inactive Categories</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php
                                    $CategoryCounts = "SELECT COUNT(*) AS catCount FROM category WHERE categoryStatus = 'N'";
                                    $CategoryResult = mysqli_query($conn, $CategoryCounts);
                                    if ($CategoryResult && mysqli_num_rows($CategoryResult) > 0) {
                                        while ($CategoryData = mysqli_fetch_assoc($CategoryResult)) { ?>
                                            <h3 class="card-title mb-0 me-2">
                                                <?php echo $CategoryData['catCount']; ?>
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

            <!-- Active Categories Card -->
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-semibold d-block mb-1">Active Categories</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php
                                    $CategoryCounts = "SELECT COUNT(*) AS catCount FROM category WHERE categoryStatus = 'Y'";
                                    $CategoryResult = mysqli_query($conn, $CategoryCounts);
                                    if ($CategoryResult && mysqli_num_rows($CategoryResult) > 0) {
                                        while ($CategoryData = mysqli_fetch_assoc($CategoryResult)) { ?>
                                            <h3 class="card-title mb-0 me-2">
                                                <?php echo $CategoryData['catCount']; ?>
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

            <!-- Draft Categories Card -->
            <div class="col-sm-6 col-xl-4">
                <div class="card h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span class="fw-semibold d-block mb-1">Saved Drafts</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php
                                    $CategoryCounts = "SELECT COUNT(*) AS catCount FROM category WHERE categoryStatus = 'W'";
                                    $CategoryResult = mysqli_query($conn, $CategoryCounts);
                                    if ($CategoryResult && mysqli_num_rows($CategoryResult) > 0) {
                                        while ($CategoryData = mysqli_fetch_assoc($CategoryResult)) { ?>
                                            <h3 class="card-title mb-0 me-2">
                                                <?php echo $CategoryData['catCount']; ?>
                                            </h3>
                                        <?php }
                                    } ?>
                                </div>
                            </div>
                            <span class="badge bg-label-warning rounded p-2">
                                <i class="bx bx-folder-open bx-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Lists Table Card -->
        <div class="card">
            <div class="card-header border-bottom d-flex align-items-center justify-content-between py-3">
                <h5 class="card-title mb-0">Category Lists</h5>
                <?php if ($_SESSION['role'] == 1) { ?>
                <a class="btn btn-primary" href="new-category.php">
                    <i class="bx bx-plus me-1"></i> Add New Category
                </a>
                <?php } ?>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 35%;">TITLE</th>
                            <th style="width: 20%;">CATEGORY</th>
                            <th style="width: 15%;">AUTHOR</th>
                            <th style="width: 15%;">DATE</th>
                            <th style="width: 5%;">STATUS</th>
                            <th style="width: 5%;" class="text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if ($categoryQuery && mysqli_num_rows($categoryQuery) > 0) {
                            $serial = $offset + 1;
                            while ($categoryD = mysqli_fetch_assoc($categoryQuery)) {
                                ?>
                                <tr>
                                    <td><strong><?php echo $serial ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars(substr($categoryD['categoryTitle'] ?? '', 0, 45)) . (strlen($categoryD['categoryTitle'] ?? '') > 45 ? '...' : ''); ?></strong></td>
                                    <td>
                                        <span class="badge bg-label-primary">
                                            <?php echo htmlspecialchars($categoryD['category_name'] ?? ''); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars(($categoryD['first_name'] ?? '') . " " . ($categoryD['last_name'] ?? '')); ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?php echo htmlspecialchars($categoryD['categoryDate'] ?? ''); ?></small>
                                    </td>
                                    <td>
                                        <?php
                                        if ($categoryD['categoryStatus'] == 'Y') {
                                            echo '<span class="badge bg-label-success me-1">Active</span>';
                                        } elseif ($categoryD['categoryStatus'] == 'N') {
                                            echo '<span class="badge bg-label-danger me-1">Inactive</span>';
                                        } elseif ($categoryD['categoryStatus'] == 'W') {
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
                                                <button type="button" class="dropdown-item CategoryShow"
                                                    data-cid="<?php echo $categoryD['category_id']; ?>" data-bs-toggle="modal"
                                                    data-bs-target="#showCategory"><i class='bx bx-show me-1'></i> View Category</button>
                                                <?php if ($_SESSION['role'] == 1) { ?>
                                                <a class="dropdown-item"
                                                    href="update-category.php?check-cate=<?php echo $author; ?>&categoryid=<?php echo base64_encode($categoryD['category_id']); ?>"><i class="bx bx-edit me-1"></i> Update Category</a>
                                                <a class="dropdown-item delete-record text-danger"
                                                    href="delete-category.php?check-cate=<?php echo $author; ?>&categoryid=<?php echo base64_encode($categoryD['category_id']); ?>"><i class="bx bx-trash me-1"></i> Delete Category</a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php $serial++;
                            }
                        } else {
                            echo "<tr><td colspan='7' class='text-center py-4 text-muted'>No categories found.</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>

            <?php
            $pagination = "SELECT * FROM category";
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