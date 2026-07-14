<?php include_once "_header.php";
    include_once "_subHeader.php";

    include_once('../app/_DBconnect.php');
    if (isset($_GET['page'])) {
    $page = @$_GET['page'];
    } else {
        $page = 1;
    }
    $limit = 1000;
    $offset = ($page - 1) * $limit;
    $visitorQ = "SELECT * FROM visitors ORDER BY id DESC LIMIT {$offset},{$limit}";
    $vistiorR = mysqli_query($conn,$visitorQ);

    if(mysqli_num_rows($vistiorR)>0){?>
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
                 <h5 class="card-title">Visitor Lists</h5>
                 
             </div>
             <div class="table-responsive text-nowrap">
                 <table class="table table-hover">
                     <thead>
                         <tr>
                             <th>No.</th>
                             <th>Visitor Ip</th>
                             <th>Visitor Date</th>
                             <!-- <th><button disabled="disabled" class="btn btn-outline-secoundary">Actions</button></th> -->
                         </tr>
                     </thead>
                     <tbody class="table-border-bottom-0">
                         <?php 
                            $serial = $offset + 1;
                         while($visitorD = mysqli_fetch_assoc($vistiorR)){
                        ?>
                        <tr>
                            <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $serial ?></strong></td>
                            <td><strong><?php echo $visitorD['ip'] ?></strong></td>
                            <td><span class="badge bg-label-primary me-1"><?php echo $visitorD['timestamp'] ?></span></td>
                            <!-- <td>
                                <div class="d-inline-block text-nowrap">
                                    <a href='delete-visitor.php' class="btn rounded-pill btn-icon btn-outline-danger"><i class="bx bx-trash"></i></a>
                                </div>
                            </td> -->
                        </tr>
                        <?php $serial++; } ?>
                     </tbody>
                 </table>
             </div>
            <?php

            $pagination = "SELECT * FROM visitors";
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
<?php } ?>

 <!-- / Content -->
 <?php include_once "_footer.php"; ?>