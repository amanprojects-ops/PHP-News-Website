<?php include_once '_header.php';
include_once '_subHeader.php'; ?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <?php
        $settings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT websiteUrl FROM settings"));
        if (isset($_GET['postid'])) {
            $postId = base64_decode($_GET['postid']);
            $selectPost = "SELECT * FROM post WHERE post_id = '{$postId}'";
            $checkQuery = mysqli_query($conn, $selectPost);
            if ($checkQuery && mysqli_num_rows($checkQuery) > 0) {
                $postQuery = mysqli_fetch_assoc($checkQuery);
                $categoryQ = "SELECT * FROM category WHERE categoryStatus= 'Y'";
                $categoryR = mysqli_query($conn, $categoryQ);
                ?>
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="card mb-4">
                            <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                                <h5 class="mb-0">Update Post</h5>
                                <small class="text-muted float-end"><?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></small>
                            </div>
                            <div class="card-body py-4">
                                <form action="app/app.php" method="POST" enctype="multipart/form-data">
                                    <div class="mb-3">
                                        <label class="form-label" for="post_title">Post Title <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="post_title" name="post_title" value="<?php echo htmlspecialchars($postQuery['title'] ?? ''); ?>" required>
                                        <input type="hidden" name="postId" value="<?php echo htmlspecialchars($postQuery['post_id'] ?? ''); ?>">
                                        <input type="hidden" name="authorId" value="<?php echo htmlspecialchars($_SESSION['author_id'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="postShortDesc">Post Short Description <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="postShortDesc" name="postShortDesc" value="<?php echo htmlspecialchars($postQuery['sort_details'] ?? ''); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="postImg" class="form-label">Upload Post Image</label>
                                        <input class="form-control mb-3" id="postImg" type="file" name="newImage">
                                        <div class="card mb-3 text-center bg-light p-2">
                                            <img class="img-fluid rounded" id="postImgShow" src="../assets/postImage/<?php echo htmlspecialchars($postQuery['post_img'] ?? ''); ?>" alt="<?php echo htmlspecialchars($postQuery['title'] ?? ''); ?>" style="max-height: 250px; object-fit: contain;">
                                        </div>
                                        <input class="form-control" type="hidden" name="oldImage" value="<?php echo htmlspecialchars($postQuery['post_img'] ?? ''); ?>">
                                        <input type="hidden" name="oldCategory" value="<?php echo htmlspecialchars($postQuery['category'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label for="newCategory" class="form-label">Category <span class="text-danger">*</span></label>
                                        <select class="form-select" id="newCategory" name="newCategory" required>
                                            <?php
                                            if ($categoryR && mysqli_num_rows($categoryR) > 0) {
                                                while ($categoryD = mysqli_fetch_assoc($categoryR)) {
                                                    $select = ($categoryD['category_id'] == $postQuery['category']) ? 'selected' : '';
                                                    echo "<option $select value='{$categoryD['category_id']}'>" . htmlspecialchars($categoryD['category_name']) . "</option>";
                                                }
                                            } else {
                                                echo '<option selected disabled>Add Category</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label" for="description">Post Description <span class="text-danger">*</span></label>
                                        <textarea id="description" name="description" class="form-control summernote" required><?php echo htmlspecialchars($postQuery['description'] ?? ''); ?></textarea>
                                    </div>
                                    <button type="submit" name="updatePost" class="btn btn-primary">
                                        <i class="bx bx-save me-1"></i> Update Post
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            }
        } else {
            ?>
            <div class="row">
                <div class="col-lg-6 mx-auto text-center py-5">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Select Post to Update</h5>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="bx bx-list-ul me-1"></i> Select Post
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" style="max-height: 300px; overflow-y: auto;">
                                    <?php
                                    $list = mysqli_query($conn, "SELECT * FROM post ORDER BY post_id DESC");
                                    if ($list && mysqli_num_rows($list) > 0) {
                                        while ($postList = mysqli_fetch_assoc($list)) {
                                            $title = htmlspecialchars(substr($postList['title'], 0, 50));
                                            $pId = base64_encode($postList['post_id']);
                                            echo "<li><a class='dropdown-item' href='?postid={$pId}'>{$title}</a></li>";
                                        }
                                    } else {
                                        echo "<li><span class='dropdown-item text-muted'>No posts found</span></li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>
</div>

<script>
    $('.summernote').summernote({
        placeholder: 'Write your post description here...',
        tabsize: 2,
        height: 250,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'clear']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
            ['insert', ['link', 'picture', 'video']],
            ['view', ['fullscreen', 'codeview']]
        ]
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#postImgShow').attr('src', e.target.result);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#postImg").change(function() {
        readURL(this);
    });
</script>

<!-- / Content -->
<?php include_once '_footer.php'; ?>