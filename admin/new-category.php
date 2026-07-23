<?php include_once "_header.php";
include_once "_subHeader.php"; ?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card mb-4">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">Add New Category</h5>
                        <small class="text-muted float-end">Creator: <?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></small>
                    </div>
                    <div class="card-body py-4">
                        <form method="POST" action="app/app.php">
                            <div class="mb-3">
                                <label class="form-label" for="categoryName">Category Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="categoryName" name="categoryName" placeholder="Enter Category Name (e.g. RESULTS, SPORTS)" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="categoryTitle">Category Title / Description <span class="text-danger">*</span></label>
                                <textarea id="categoryTitle" name="categoryTitle" class="form-control" rows="3" placeholder="Enter Category Title description." required></textarea>
                            </div>
                            
                            <button type="submit" name="addCategory" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Add Category
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- / Content -->
<?php include_once "_footer.php"; ?>