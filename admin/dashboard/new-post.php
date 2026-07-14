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
                            <h5 class="mb-0">Add New Post</h5>
                            <small class="text-muted float-end"><?php echo $_SESSION['name']; ?></small>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-dismissible" id="message" role="alert" style='display:none;'>

                            </div>
                            <form id="newPost" action="../app/app.php" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-fullname">Post Title <span class="text-danger fs-5">*</span></label>
                                    <input type="text" class="form-control" name="post_title" id="post_title" placeholder="Enter Post Title">
                                    <input type="hidden" name="author_id" value='<?php echo $_SESSION['author_id']; ?>'>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-company">Post Short Description <span class="text-danger fs-5">*</span></label>
                                    <input type="text" class="form-control" name="post_short_desc" id="post_short_desc" placeholder="Enter Post Short Description.">
                                </div>
                                <div class="mb-3">
                                    <label for="formFile" class="form-label">Upload Post Image <span class="text-danger fs-5">*</span></label>
                                    <input class="form-control mb-3" type="file" id="postImage" name="postImage">
                                    <div class='card mb-4'>
                                        <img class='card-img' id='postImgShow' src="">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleFormControlSelect1" class="form-label">Category <span class="text-danger fs-5">*</span></label>
                                    <?php include_once('../app/_DBconnect.php');
                                    $categoryQ = "SELECT * FROM category WHERE categoryStatus = 'Y'";
                                    $categoryR = mysqli_query($conn, $categoryQ);
                                    if (mysqli_num_rows($categoryR) > 0) { ?>
                                        <select class="form-select" id="post_category" name="post_category">
                                            <option selected disabled>Select Category</option>
                                            <?php
                                            while ($categoryD = mysqli_fetch_assoc($categoryR)) {
                                                echo "<option value='{$categoryD['category_id']}'>{$categoryD['category_name']}</option>";
                                            } ?>
                                        </select>
                                    <?php } ?>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-message">Post Description <span class="text-danger fs-5">*</span></label>
                                    <textarea name="description" id="description" class="form-control summernote" placeholder="Enter Post Description."></textarea>
                                    <input type="hidden" name="saveNew_post">
                                </div>
                                <button type="button" id="saveNew_post" class="btn btn-primary">Add New Post</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="container">

</div>
</div>
<script>
    $('.summernote').summernote({
        placeholder: 'Connect Bihar Education Portal Create New Posts.',
        tabsize: 2,
        height: 220,
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
    $("#postImgShow").hide();
    $("#postImage").change(function() {
        readURL(this);
        $("#postImgShow").show();
    });
</script>
<script>
    $(document).ready(function() {
        $('#saveNew_post').click(function() {
            var title = $('#post_title').val();
            var shortDesc = $('#post_short_desc').val();
            var postImage = $('#postImage').val();
            var post_category = $('#post_category').val();
            var description = $('#description').val();
            if (title == '' || shortDesc == '' || postImage == '' || post_category == '' || description == '') {

                $('#message').removeClass('alert-success').addClass('alert-danger').html('All field are required.').fadeIn(3000, function() {
                    $(this).fadeOut(3000);
                });

            } else {
                $('#newPost').submit();
            }

        });
    });
</script>
<!-- / Content -->
<?php include_once "_footer.php"; ?>