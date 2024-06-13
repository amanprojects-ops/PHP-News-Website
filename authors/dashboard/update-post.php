<?php include_once '_header.php';
include_once '_subHeader.php'; ?>
<!-- Content -->
<div class='container-fluid flex-grow-1 container-p-y'>
    <?php
    include_once('../app/_DBconnect.php');

    if (isset($_GET['postid'])) {
        $postId = base64_decode($_GET['postid']);
        $selectPost = "SELECT * FROM post WHERE post_id = '{$postId}'";
        $checkQuery = mysqli_query($conn, $selectPost);
        $postQuery = mysqli_fetch_assoc($checkQuery);
        if (mysqli_num_rows($checkQuery) > 0) {
            $categoryQ = "SELECT * FROM category WHERE categoryStatus= 'Y'";
            $categoryR = mysqli_query($conn, $categoryQ);
            echo "<div class='container my-5'>
        <div class='row'>
            <div class='col-md-8 offset-md-2'>
                <div class='col-xl'>
                    <div class='card mb-4'>
                        <div class='card-header d-flex justify-content-between align-items-center'>
                            <h5 class='mb-0'>Update Post</h5>
                            <small class='text-muted float-end'>{$_SESSION['name']}</small>
                        </div>
                        <div class='card-body'>
                            <form action='../app/app.php' method='POST' enctype='multipart/form-data'>
                                <div class='mb-3'>
                                    <label class='form-label' for='basic-default-fullname'>Post Title <span class='text-danger fs-5'>*</span></label>
                                    <input type='text' class='form-control' id='post_title' name='post_title' value='{$postQuery['title']}' required>
                                    <input type='hidden' name='postId' value='{$postQuery['post_id']}'>
                                    <input type='hidden' name='authorId' value='{$_SESSION['author_id']}'/>
                                </div>
                                <div class='mb-3'>
                                    <label class='form-label' for='basic-default-company'>Post Short Description <span class='text-danger fs-5'>*</span></label>
                                    <input type='text' class='form-control' id='postShortDesc' name='postShortDesc' value='{$postQuery['sort_details']}' required>
                                </div>
                                <div class='mb-3'>
                                    <label for='formFile' class='form-label'>Upload Post Image <span class='text-danger fs-5'>*</span></label>
                                    <input class='form-control mb-4' id='postImg' type='file' name='newImage'>
                                    <div class='card mb-4'>
                                        <img class='card-img' id='postImgShow' src='../../assets/postImage/{$postQuery['post_img']}' alt='Card image cap'>
                                    </div>
                                    <input class='form-control' type='hidden' name='oldImage' value='{$postQuery['post_img']}'>
                                    <input type='hidden' name='oldCategory' value='{$postQuery['category']}'>
                                </div>";
            echo "<div class='mb-3'>
                                         <label for='exampleFormControlSelect1' class='form-label'>Category <span class='text-danger fs-5'>*</span></label>
                                          <select class='form-select' id='newCategory' name='newCategory' aria-label='Default select example' required>";
            if (mysqli_num_rows($categoryR) > 0) {
                while ($categoryD = mysqli_fetch_assoc($categoryR)) {
                    if ($categoryD['category_id'] == $postQuery['category']) {
                        $select = 'selected';
                    } else {
                        $select = '';
                    }
                    echo "<option $select value='{$categoryD['category_id']}'>{$categoryD['category_name']}</option>";
                }
            } else {
                echo '<option selected>Add Category</option>';
            }

            echo "</select>
                                </div>

                                <div class='mb-3'>
                                    <label class='form-label' for='basic-default-message'>Post Description <span class='text-danger fs-5'>*</span></label>
                                    <textarea id='basic-default-message' name='description' class='form-control summernote' value='' required>{$postQuery['description']}</textarea>
                                </div>
                                <button type='submit' name='updatePost' class='btn btn-primary'>Update Post</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>";
        }
    } else {
        echo "<div class='container my-5'>
        <div class='col-md-6 offset-md-3'>
            <div class='card text-center'>
                <div class='card-body'>
                    <div class='btn-group'>
                      <button type='button' class='btn btn-primary dropdown-toggle' data-bs-toggle='dropdown' data-bs-display='static' aria-haspopup='true' aria-expanded='false'>
                       <i class='bx bx-menu'></i>
                        Select All Posts
                      </button>
                      <ul class='dropdown-menu dropdown-menu-end dropdown-menu-lg-start'>";
        $list = mysqli_query($conn, "SELECT * FROM post");
        while ($postList = mysqli_fetch_assoc($list)) {
            $title = substr($postList['title'], 0, 50);
            $pId = base64_encode($postList['post_id']);
            echo "<li><a class='dropdown-item' href='?postid={$pId}'>{$title}</a></li>";
        }
        echo "</ul>
                    </div>
                </div>
            </div>
        </div>
    </div>";
    }


    ?>

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
    //$("#postImgShow").hide();
    $("#postImg").change(function() {
        readURL(this);
        $("#postImgShow").show();
    });
</script>

<!-- / Content -->
<?php include_once '_footer.php'; ?>