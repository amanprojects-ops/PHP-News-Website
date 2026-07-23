<?php include_once '_header.php';
include_once '_subHeader.php'; ?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card mb-4">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">Add New Post</h5>
                        <small class="text-muted float-end">Author: <?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></small>
                    </div>
                    <div class="card-body py-4">
                        <div class="alert alert-dismissible" id="message" role="alert" style="display:none;"></div>
                        <form id="newPost" action="app/app.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label" for="post_title">Post Title <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="post_title" id="post_title" placeholder="Enter Post Title" required>
                                <input type="hidden" name="author_id" value="<?php echo htmlspecialchars($_SESSION['author_id'] ?? ''); ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="post_short_desc">Post Short Description <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="post_short_desc" id="post_short_desc" placeholder="Enter Post Short Description." required>
                            </div>
                            <div class="mb-3">
                                <label for="postImage" class="form-label">Upload Post Image <span class="text-danger">*</span></label>
                                <input class="form-control mb-3" type="file" id="postImage" name="postImage" required>
                                <div class="card mb-3" id="postImgShowWrapper" style="display:none;">
                                    <img class="card-img" id="postImgShow" src="" style="max-height: 250px; object-fit: contain;">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="post_category" class="form-label">Category <span class="text-danger">*</span></label>
                                <?php
                                $categoryQ = "SELECT * FROM category WHERE categoryStatus = 'Y'";
                                $categoryR = mysqli_query($conn, $categoryQ);
                                if ($categoryR && mysqli_num_rows($categoryR) > 0) {
                                    ?>
                                    <select class="form-select" id="post_category" name="post_category" required>
                                        <option value="" selected disabled>Select Category</option>
                                        <?php
                                        while ($categoryD = mysqli_fetch_assoc($categoryR)) {
                                            echo "<option value='{$categoryD['category_id']}'>" . htmlspecialchars($categoryD['category_name']) . '</option>';
                                        }
                                        ?>
                                    </select>
                                <?php } ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label" for="description">Post Description <span class="text-danger">*</span></label>
                                <textarea name="description" id="description" class="form-control summernote" placeholder="Enter Post Description."></textarea>
                                <input type="hidden" name="saveNew_post">
                            </div>
                            <button type="button" id="saveNew_post" class="btn btn-primary">
                                <i class="bx bx-plus me-1"></i> Add New Post
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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
                $('#postImgShowWrapper').show();
            };

            reader.readAsDataURL(input.files[0]);
        }
    }
    $("#postImage").change(function() {
        readURL(this);
    });

    $(document).ready(function() {
        $('#saveNew_post').click(function() {
            var title = $('#post_title').val();
            var shortDesc = $('#post_short_desc').val();
            var postImage = $('#postImage').val();
            var post_category = $('#post_category').val();
            var description = $('#description').val();
            if (title == '' || shortDesc == '' || postImage == '' || post_category == '' || description == '') {

                $('#message').removeClass('alert-success').addClass('alert-danger').html('All fields are required.').fadeIn(500, function() {
                    setTimeout(function() { $('#message').fadeOut(500); }, 3000);
                });

            } else {
                $('#newPost').submit();
            }

        });
    });
</script>
<!-- / Content -->
<?php include_once '_footer.php'; ?>