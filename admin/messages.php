<?php include_once "_header.php";
include_once "_subHeader.php";

if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}
$offset = ($page - 1) * $limit;
$selectMessage = "SELECT * FROM `contact_us` ORDER BY id DESC LIMIT {$offset},{$limit}";
$MessageQuery = mysqli_query($conn, $selectMessage);
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Message Lists Table Card -->
        <div class="card">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0">Message Lists</h5>              
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%;">#</th>
                            <th style="width: 20%;">SENDER NAME</th>
                            <th style="width: 25%;">SENDER EMAIL</th>
                            <th style="width: 35%;">MESSAGE</th>
                            <th style="width: 10%;">SENT DATE</th>
                            <th style="width: 5%;" class="text-center">ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if ($MessageQuery && mysqli_num_rows($MessageQuery) > 0) {
                            $serial = $offset + 1;
                            while ($MessageD = mysqli_fetch_assoc($MessageQuery)) {
                                ?>
                                <tr>
                                    <td><strong><?php echo $serial; ?></strong></td>
                                    <td><strong><?php echo htmlspecialchars($MessageD['name'] ?? ''); ?></strong></td>
                                    <td>
                                        <a href="mailto:<?php echo htmlspecialchars($MessageD['email'] ?? ''); ?>"><?php echo htmlspecialchars($MessageD['email'] ?? ''); ?></a>
                                    </td>
                                    <td>
                                        <span class="text-truncate d-inline-block" style="max-width: 300px;" title="<?php echo htmlspecialchars($MessageD['message'] ?? ''); ?>">
                                            <?php echo htmlspecialchars($MessageD['message'] ?? ''); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?php 
                                            $created = $MessageD['created_at'] ?? '';
                                            echo $created ? date('d-m-Y', strtotime($created)) : 'N/A';
                                            ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-inline-block text-nowrap">
                                            <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end m-0">
                                                <button type="button" class="dropdown-item messageShow text-primary"
                                                    data-mid="<?php echo $MessageD['id']; ?>" data-bs-toggle="modal"
                                                    data-bs-target="#showMessage"><i class="bx bx-show me-1"></i> View Message</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <?php $serial++;
                            }
                        } else {
                            echo "<tr><td colspan='6' class='text-center py-4 text-muted'>No contact messages found.</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
            <?php
            $pagination = "SELECT * FROM contact_us";
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
                                $active = ($i == $page) ? "active" : "";
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