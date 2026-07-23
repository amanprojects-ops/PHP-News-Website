<?php include_once "_header.php";
include_once "_subHeader.php";

if (isset($_GET['page'])) {
    $page = (int)$_GET['page'];
} else {
    $page = 1;
}
$limit = 100;
$offset = ($page - 1) * $limit;
$visitorQ = "SELECT * FROM visitors ORDER BY id DESC LIMIT {$offset},{$limit}";
$vistiorR = mysqli_query($conn, $visitorQ);
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Visitor Lists Table Card -->
        <div class="card">
            <div class="card-header border-bottom py-3">
                <h5 class="card-title mb-0">Visitor Lists</h5>
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 10%;">#</th>
                            <th style="width: 50%;">VISITOR IP</th>
                            <th style="width: 40%;">VISITOR DATE & TIME</th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php 
                        if ($vistiorR && mysqli_num_rows($vistiorR) > 0) {
                            $serial = $offset + 1;
                            while ($visitorD = mysqli_fetch_assoc($vistiorR)) {
                            ?>
                            <tr>
                                <td><strong><?php echo $serial ?></strong></td>
                                <td><span class="fw-semibold text-primary"><?php echo htmlspecialchars($visitorD['ip']); ?></span></td>
                                <td><span class="badge bg-label-primary"><?php echo htmlspecialchars($visitorD['timestamp']); ?></span></td>
                            </tr>
                            <?php $serial++; } 
                        } else {
                            echo "<tr><td colspan='3' class='text-center py-4 text-muted'>No visitors found.</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>

            <?php
            $pagination = "SELECT * FROM visitors";
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