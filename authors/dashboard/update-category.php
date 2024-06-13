<?php include_once "_header.php";
include_once "_subHeader.php"; 
include_once("../app/_DBconnect.php");
$category_id = base64_decode($_GET['categoryid']);
$query = mysqli_query($conn,"SELECT * FROM category WHERE category_id = '{$category_id}'");
$categoryData = mysqli_fetch_assoc($query);
?>
<!-- Content -->
<div class="container-fluid flex-grow-1 container-p-y">

    <!-- Layout Demo -->
    <div class="container my-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Update Category</h5>
                            <small class="text-muted float-end"><?php echo $_SESSION['name'] ?></small>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="../app/app.php">
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-fullname">Category Name <span class="text-danger fs-5">*</span></label>
                                    <input type="text" class="form-control" id="categoryName" name="categoryName" value="<?php echo $categoryData['category_name']; ?>" required>
                                    <input type="hidden" name="category_id" value="<?php echo $category_id ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-company">Category Title <span class="text-danger fs-5">*</span></label>
                                    <textarea id="categoryTitle" name="categoryTitle" class="form-control" required><?php echo $categoryData['categoryTitle']; ?></textarea>
                                </div>                                
                                <button type="submit" name="updateCategory" class="btn btn-primary">Update Category</button>
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