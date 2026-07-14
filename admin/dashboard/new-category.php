<?php include_once "_header.php";
include_once "_subHeader.php"; ?>
<!-- Content -->
<div class="container-fluid flex-grow-1 container-p-y">

    <!-- Layout Demo -->
    <div class="container my-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Add New Category</h5>
                            <small class="text-muted float-end"><?php echo $_SESSION['name'] ?></small>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="../app/app.php">
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-fullname">Category Name <span class="text-danger fs-5">*</span></label>
                                    <input type="text" class="form-control" id="categoryName" name="categoryName" placeholder="Enter Category Name">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-company">Category Title <span class="text-danger fs-5">*</span></label>
                                    <textarea id="categoryTitle" name="categoryTitle" class="form-control" placeholder="Enter Category Title."></textarea>
                                </div>
                                
                                <button type="submit" name="addCategory" class="btn btn-primary">Add New Category</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- / Content -->
<?php include_once "_footer.php"; ?>