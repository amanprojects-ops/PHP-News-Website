<?php include_once "_header.php";
include_once "_subHeader.php"; ?>

<?php
include_once "../app/_DBconnect.php";

if (isset($_GET['page'])) {
    $page = $_GET['page'];
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
        WHERE category = '{$categoryId}'
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
        WHERE post.author = '{$author_id}' && category = '{$categoryId}'
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
// Post Status Filter in Dashboard cades ============================================
$pStatusQ = "SELECT postStatus FROM post";
$pStatusR = mysqli_query($conn, $pStatusQ);

?>
<!-- Content -->
<div class="container-fluid flex-grow-1 container-p-y">

    <!-- Layout Demo -->
    <div class="container my-5">
        <div class="row g-4 mb-4">
            <!-- post card details code -->
            <div class="col-sm-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-start justify-content-between">
                            <div class="content-left">
                                <span>Inactive Posts</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php include_once "../app/_DBconnect.php";
                                    if ($_SESSION['author_id'] == 1) {
                                        $inactive = "SELECT COUNT(*) AS inactive FROM post WHERE postStatus = 'N'";
                                    } else {
                                        $inactive = "SELECT COUNT(*) AS inactive FROM post WHERE postStatus = 'N' && author = '{$_SESSION['author_id']}'";
                                    }
                                    $inactiveResult = mysqli_query($conn, $inactive);
                                    if (mysqli_num_rows($inactiveResult) > 0) {
                                        while ($inactiveData = mysqli_fetch_assoc($inactiveResult)) { ?>
                                            <h4 class="mb-0 me-2">
                                                <?php echo $inactiveData['inactive']; ?>
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
                                <span>Active Posts</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php include_once "../app/_DBconnect.php";
                                    if ($_SESSION['author_id'] == 1) {
                                    $postActive = "SELECT COUNT(*) AS postActives FROM post WHERE postStatus = 'Y'";
                                    }else{
                                    $postActive = "SELECT COUNT(*) AS postActives FROM post WHERE postStatus = 'Y' && author = '{$_SESSION['author_id']}'";
                                    }
                                    $postActiveResult = mysqli_query($conn, $postActive);
                                    if (mysqli_num_rows($postActiveResult) > 0) {
                                        while ($postActiveData = mysqli_fetch_assoc($postActiveResult)) { ?>
                                            <h4 class="mb-0 me-2">
                                                <?php echo $postActiveData['postActives']; ?>
                                            </h4>
                                        <?php }
                                    } ?>
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
                                <span>Save Post draft</span>
                                <div class="d-flex align-items-end mt-2">
                                    <?php include_once "../app/_DBconnect.php";
                                    if ($_SESSION['author_id'] == 1) {
                                        $pwait = "SELECT COUNT(*) AS pwait FROM post WHERE postStatus = 'W'";
                                    } else {
                                        $pwait = "SELECT COUNT(*) AS pwait FROM post WHERE postStatus = 'W' && author = '{$_SESSION['author_id']}'";
                                    }
                                    $pwaitResult = mysqli_query($conn, $pwait);
                                    if (mysqli_num_rows($pwaitResult) > 0) {
                                        while ($pwaitData = mysqli_fetch_assoc($pwaitResult)) { ?>
                                            <h4 class="mb-0 me-2">
                                                <?php echo $pwaitData['pwait']; ?>
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
                <h5 class="card-title">Post Lists</h5>
                <div class="d-flex justify-content-between align-items-center row py-3 gap-3 gap-md-0">
                    <div class="col-md-4">
                        <a class="btn btn-primary" href="new-post.php">
                            <span>
                                <i class="bx bx-plus me-0 me-sm-1"></i>
                                <span class="d-none d-sm-inline-block">Add New Post</span>
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
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Title</button></th>
                            <th>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-outline-secondary dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                        Cagegory
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="./view-post.php">All Posts</a></li>
                                        <?php while ($categoryD = mysqli_fetch_assoc($categoryR)) {
                                            $encode_category = base64_encode($categoryD['category_id']);
                                            echo '<li><a class="dropdown-item" href="?catID=' . $encode_category . '">' . $categoryD['category_name'] . '</a></li>';
                                        } ?>
                                    </ul>
                                </div>
                            </th>
                            <th>
                                <div class="btn-group"><button disabled="disabled"
                                        class="btn btn-outline-secoundary">Author</button></div>
                            </th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Post Date</button></th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Status</button>
                            </th>
                            <th><button disabled="disabled" class="btn btn-outline-secoundary">Actions</button></th>
                        </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                        <?php if (mysqli_num_rows($postQuery) > 0) {
                            $serial = $offset + 1;
                            while ($postD = mysqli_fetch_assoc($postQuery)) {
                                ?>
                                <tr>
                                    <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong>
                                            <?php echo $serial ?>
                                        </strong></td>
                                    <td><strong>
                                            <?php echo substr($postD['title'], 0, 30) . "..."; ?>
                                        </strong></td>
                                    <td>
                                        <?php echo $postD['category_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $postD['first_name'] . " " . $postD['last_name']; ?>
                                    </td>
                                    <td>
                                        <?php echo $postD['post_date']; ?>
                                    </td>
                                    <td>
                                        <?php
                                        if ($postD['postStatus'] == 'Y') {
                                            echo '<span class="badge bg-label-success me-1">Active</span></td>';
                                        } elseif ($postD['postStatus'] == 'N') {
                                            echo '<span class="badge bg-label-danger me-1">Inactive</span></td>';
                                        } elseif ($postD['postStatus'] == 'W') {
                                            echo '<span class="badge bg-label-warning me-1">Save Draft</span></td>';
                                        }
                                        ?>

                                    <td>
                                        <div class="d-inline-block text-nowrap">
                                            <button class="btn btn-sm btn-icon dropdown-toggle hide-arrow"
                                                data-bs-toggle="dropdown"><i class="bx bx-dots-vertical-rounded"></i></button>
                                            <div class="dropdown-menu dropdown-menu-end m-0">
                                                <button type="button" class="dropdown-item postShow"
                                                    data-pid="<?php echo $postD['post_id']; ?>" data-bs-toggle="modal"
                                                    data-bs-target="#showPost"><i class='bx bx-show'></i>&nbsp; View
                                                    Post</button>
                                                <?php if ($_SESSION['role'] == 1 || $_SESSION['role'] == 2) { ?>
                                                    <a class="dropdown-item"
                                                        href="update-post.php?postid=<?php echo base64_encode($postD['post_id']); ?>"
                                                        class="btn btn-sm btn-icon"><i class="bx bx-edit"></i>&nbsp; Update Post</a>
                                                <?php } elseif ($postD['postStatus'] == 'N') { ?>
                                                    <a class="dropdown-item"
                                                        href="update-post.php?postid=<?php echo base64_encode($postD['post_id']); ?>"
                                                        class="btn btn-sm btn-icon"><i class="bx bx-edit"></i>&nbsp; Update Post</a>
                                                <?php }
                                                if ($_SESSION['role'] == 1) { ?>
                                                    <a class="dropdown-item"
                                                        href="delete-post.php?check=<?php echo base64_encode($_SESSION['author_id']) ?>&postid=<?php echo base64_encode($postD['post_id']); ?>"
                                                        class="btn btn-sm btn-icon delete-record"><i
                                                            class="bx bx-trash"></i>&nbsp;Delete Post</a>
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
            if (mysqli_num_rows($pagiResult) > 0) {
                $total_records = mysqli_num_rows($pagiResult);
                $total_pages = ceil($total_records / $limit); ?>
                <div class="card-footer text-muted">
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php
                            if (isset($_REQUEST['catID'])) {
                                $decodeCatid = base64_encode($categoryId);
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
        <!--/ Layout Demo -->
    </div>
</div>


<!-- / Content -->
<?php include_once "_footer.php"; ?>