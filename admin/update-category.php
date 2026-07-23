<?php include_once "_header.php";
include_once "_subHeader.php"; 

$category_id = isset($_GET['categoryid']) ? base64_decode($_GET['categoryid']) : 0;
$query = mysqli_query($conn, "SELECT * FROM category WHERE category_id = '{$category_id}'");
$categoryData = ($query && mysqli_num_rows($query) > 0) ? mysqli_fetch_assoc($query) : null;
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card mb-4">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">Update Category</h5>
                        <small class="text-muted float-end"><?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></small>
                    </div>
                    <div class="card-body py-4">
                        <?php if ($categoryData) { ?>
                            <form method="POST" action="app/app.php">
                                <div class="mb-3">
                                    <label class="form-label" for="categoryName">Category Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="categoryName" name="categoryName" value="<?php echo htmlspecialchars($categoryData['category_name'] ?? ''); ?>" required>
                                    <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category_id); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="categoryTitle">Category Title / Description <span class="text-danger">*</span></label>
                                    <textarea id="categoryTitle" name="categoryTitle" class="form-control" rows="3" required><?php echo htmlspecialchars($categoryData['categoryTitle'] ?? ''); ?></textarea>
                                </div>                                
                                <button type="submit" name="updateCategory" class="btn btn-primary">
                                    <i class="bx bx-save me-1"></i> Update Category
                                </button>
                            </form>
                        <?php } else { ?>
                            <div class="alert alert-warning mb-0">Category not found or invalid ID.</div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- / Content -->
<?php include_once "_footer.php"; ?>