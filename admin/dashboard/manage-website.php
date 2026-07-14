<?php include_once "_header.php";
include_once "_subHeader.php"; ?>
<!-- Content -->
<div class="container-fluid flex-grow-1 container-p-y">

    <!-- Layout Demo -->
    <div class="container my-5">
        <div class="row">
            <div class="col-sm-4">
                <div class="card">
                    <img class="card-img p-4" id="webLogoShow" src="../../assets/images/<?php echo $settingd['logo']; ?>" alt="<?php echo $settingd['websitename']; ?>">
                    <div class="card-body">
                        <h5 class="card-title">Website Logo</h5>
                        <form action="../app/app.php" method="POST" enctype="multipart/form-data">
                            <div class="input-group">
                                <input type="file" class="form-control" id="webLogo" name="webLogo" required>
                                <button class="btn btn-outline-primary" type="submit" id="webLogobtn" name="webLogobtn">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <img class="card-img p-4" id="webfaviconShow" src="../../assets/images/<?php echo $settingd['favicon']; ?>" alt="<?php echo $settingd['websitename']; ?>">
                    <div class="card-body">
                        <h5 class="card-title">Favicon Icon</h5>
                        <form action="../app/app.php" method="POST" enctype="multipart/form-data">
                            <div class="input-group">
                                <input type="file" class="form-control" id="webfavicon" name="webfavicon" required>
                                <button class="btn btn-outline-primary" type="submit" name="webfaviconbtn">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="card">
                    <img class="card-img p-4" id="webwatterMarkShow" src="../../assets/images/<?php echo $settingd['watterMark']; ?>" alt="<?php echo $settingd['websitename']; ?>">
                    <div class="card-body">
                        <h5 class="card-title">Wattermark Logo</h5>
                        <form action="../app/app.php" method="POST" enctype="multipart/form-data">
                            <div class="input-group">
                                <input type="file" class="form-control" id="webwatterMark" name="webwatterMark" required>
                                <button class="btn btn-outline-primary" type="submit" name="webwattermarkbtn">Upload</button>
                            </div>
                        </form>
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
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Manage Website</h5>
                            <small class="text-muted float-end">Website Setting</small>
                        </div>
                        <div class="card-body">
                            <form action="../app/app.php" method="POST">
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-fullname">Website Name</label>
                                    <input type="text" class="form-control" id="webName" name="webName" value="<?php echo $settingd['websitename']; ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-company">Website Footer</label>
                                    <input type="text" class="form-control" id="webFooter" name="webFooter" value='<?php echo $settingd['footerdesc']; ?>'>

                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-email">Website Email</label>
                                    <div class="input-group input-group-merge">
                                        <input type="text" id="webEmail" name="webEmail" class="form-control" value="<?php echo $settingd['workEmail']; ?>">
                                        <!-- <span class="input-group-text" id="basic-default-email2">@example.com</span> -->
                                    </div>
                                    <div class="form-text">You can use letters, numbers &amp; periods</div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label" for="basic-default-message">Website Keyword</label>
                                    <textarea id="webKeyword" name="webKeyword" class="form-control" placeholder="Enter Website Keyword."><?php echo $settingd['keywords']; ?></textarea>
                                </div>

                                <button type="submit" name="settingUpdate" class="btn btn-primary">Update</button>
                            </form>
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

        <!--/ Layout Demo -->
    </div>
</div>
<script>
    $(document).ready(function() {
        // Website Logo Showing Code =====================================================
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#webLogoShow').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
        // $("#webLogoShow").hide();
        $("#webLogo").change(function() {
            readURL(this);
            $("#webLogoShow").show();
        });
        // Website Fevicon Showing Code ==================================================
        function readURL2(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#webfaviconShow').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
        // $("#voterImgShow").hide();
        $("#webfavicon").change(function() {
            readURL2(this);
            $("#webfaviconShow").show();
        });

        // Website Fevicon Showing Code ==================================================
        function readURL3(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#webwatterMarkShow').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
        // $("#voterImgShow").hide();
        $("#webwatterMark").change(function() {
            readURL3(this);
            $("#webwatterMarkShow").show();
        });
    });
</script>

<!-- / Content -->
<?php include_once "_footer.php"; ?>