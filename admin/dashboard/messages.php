<?php include_once "_header.php";
include_once "_subHeader.php";
include_once("../app/_DBconnect.php");
if (isset($_GET['page'])) {
    $page = $_GET['page'];
} else {
    $page = 1;
}
// $limit = 3;
$offset = ($page - 1) * $limit;
$selectMessage = "SELECT * FROM `contact_us` ORDER BY id DESC LIMIT {$offset},{$limit}";
$MessageQuery = mysqli_query($conn, $selectMessage); ?>
?>
<!-- Content -->
<div class="container-fluid flex-grow-1 container-p-y">

    <!-- Layout Demo -->
    <div class="container my-5">

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
                <h5 class="card-title">Message Lists</h5>              
            </div>
            <div class="table-responsive text-nowrap">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>
                                <button disabled="disabled" class="btn btn-outline-secoundary">NO.</button>
                            </th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Sander Name</button></th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Sander Email</button>
                            </th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Messages</button></th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Sand Date</button></th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Actions</button></th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if (mysqli_num_rows($MessageQuery) > 0) {
                            $serial = $offset + 1;
                            while ($MessageD = mysqli_fetch_assoc($MessageQuery)) {
                                ?>
                                <tr>
                                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>
                                            <?php echo $serial ?>
                                        </strong></td>
                                    <td><strong>
                                            <?php echo $MessageD['name']; ?>
                                        </strong></td>
                                    <td>
                                        <?php echo $MessageD['email']; ?>
                                    </td>
                                    <td>
                                        <?php echo $MessageD['message']; ?>
                                    </td>
                                    <td>
                                        <?php $date = explode('-',$MessageD['created_at']);echo $date[2]."-".$date['1']."-".$date['0'];?>
                                    </td>

                                    <td>
                                        <div class="d-inline-block text-nowrap">
                                            <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end m-0">
                                                <button type="button" class="dropdown-item messageShow"
                                                    data-mid="<?php echo $MessageD['id']; ?>" data-bs-toggle="modal"
                                                    data-bs-target="#showMessage"><i class='bx bx-show'></i>&nbsp; View</button>
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
            <div class="card-footer text-muted">
                <?php

                $pagination = "SELECT * FROM contact_us";
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
        </div>
        <!--/ Layout Demo -->
    </div>
</div>


<!-- / Content -->
<?php include_once "_footer.php"; ?>