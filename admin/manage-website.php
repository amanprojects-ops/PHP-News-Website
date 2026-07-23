<?php include_once "_header.php";
include_once "_subHeader.php"; ?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Logo, Favicon, Watermark Row -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Website Logo</h5>
                        <div class="my-3 p-3 bg-light rounded d-flex align-items-center justify-content-center" style="min-height: 120px;">
                            <img class="img-fluid" id="webLogoShow" src="../assets/images/<?php echo htmlspecialchars($settingd['logo']); ?>" alt="<?php echo htmlspecialchars($settingd['websitename']); ?>" style="max-height: 100px;">
                        </div>
                        <form action="../app/app.php" method="POST" enctype="multipart/form-data">
                            <div class="input-group">
                                <input type="file" class="form-control" id="webLogo" name="webLogo" required>
                                <button class="btn btn-primary" type="submit" id="webLogobtn" name="webLogobtn">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Favicon Icon</h5>
                        <div class="my-3 p-3 bg-light rounded d-flex align-items-center justify-content-center" style="min-height: 120px;">
                            <img class="img-fluid" id="webfaviconShow" src="../assets/images/<?php echo htmlspecialchars($settingd['favicon']); ?>" alt="<?php echo htmlspecialchars($settingd['websitename']); ?>" style="max-height: 80px;">
                        </div>
                        <form action="../app/app.php" method="POST" enctype="multipart/form-data">
                            <div class="input-group">
                                <input type="file" class="form-control" id="webfavicon" name="webfavicon" required>
                                <button class="btn btn-primary" type="submit" name="webfaviconbtn">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body text-center">
                        <h5 class="card-title">Watermark Logo</h5>
                        <div class="my-3 p-3 bg-light rounded d-flex align-items-center justify-content-center" style="min-height: 120px;">
                            <img class="img-fluid" id="webwatterMarkShow" src="../assets/images/<?php echo htmlspecialchars($settingd['watterMark']); ?>" alt="<?php echo htmlspecialchars($settingd['websitename']); ?>" style="max-height: 100px;">
                        </div>
                        <form action="../app/app.php" method="POST" enctype="multipart/form-data">
                            <div class="input-group">
                                <input type="file" class="form-control" id="webwatterMark" name="webwatterMark" required>
                                <button class="btn btn-primary" type="submit" name="webwattermarkbtn">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- General Website Settings Card -->
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card mb-4">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">Manage Website Settings</h5>
                        <span class="badge bg-label-primary">General Configuration</span>
                    </div>
                    <div class="card-body py-4">
                        <form action="../app/app.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label" for="webName">Website Name</label>
                                <input type="text" class="form-control" id="webName" name="webName" value="<?php echo htmlspecialchars($settingd['websitename']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="webFooter">Website Footer Text</label>
                                <input type="text" class="form-control" id="webFooter" name="webFooter" value="<?php echo htmlspecialchars($settingd['footerdesc']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="webEmail">Website Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input type="email" id="webEmail" name="webEmail" class="form-control" value="<?php echo htmlspecialchars($settingd['workEmail']); ?>" required>
                                </div>
                                <div class="form-text">System contact &amp; notification email address</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="webKeyword">Website Keywords</label>
                                <textarea id="webKeyword" name="webKeyword" class="form-control" rows="3" placeholder="Enter Website Keywords for SEO."><?php echo htmlspecialchars($settingd['keywords']); ?></textarea>
                            </div>

                            <button type="submit" name="settingUpdate" class="btn btn-primary">Save Settings</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    $(document).ready(function() {
        function readURL(input, targetShow) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(targetShow).attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#webLogo").change(function() {
            readURL(this, '#webLogoShow');
        });
        $("#webfavicon").change(function() {
            readURL(this, '#webfaviconShow');
        });
        $("#webwatterMark").change(function() {
            readURL(this, '#webwatterMarkShow');
        });
    });
</script>

<!-- / Content -->
<?php include_once "_footer.php"; ?>