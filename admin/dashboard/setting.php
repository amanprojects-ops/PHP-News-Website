<?php include_once "_header.php";
include_once "_subHeader.php";

$userid = trim($_SESSION['author_id']);

$selectQ = "SELECT * FROM user WHERE user_id = '{$userid}'";

$selectR = mysqli_query($conn, $selectQ);

if (mysqli_num_rows($selectR) > 0) {

    $selectD = mysqli_fetch_assoc($selectR);
?>
    <!-- Content -->
    <div class="container-fluid flex-grow-1 container-p-y">

        <!-- Layout Demo -->
        <div class="container my-5">

            <div class="card mb-4">
                <h5 class="card-header">Profile Details</h5>
                <!-- Account -->
                <div class="card-body">
                    <div class="d-flex align-items-start align-items-sm-center gap-4">
                        <img src="../assets/img/avatars/1.png" id="userImgShow" alt="user-avatar" class="d-block rounded" height="100" width="100" id="uploadedAvatar">
                        <div class="button-wrapper">
                            <form action="../app/app.php" method="post" enctype="multipart/form-data">
                                <label for="upload" class="btn btn-primary me-2 mb-4" tabindex="0">
                                    <span class="d-none d-sm-block">New photo</span>
                                    <i class="bx bx-upload d-block d-sm-none"></i>
                                    <input type="file" id="upload" class="account-file-input" hidden="" accept="image/png, image/jpeg">
                                </label>
                                <button class="btn btn-primary me-2 mb-4" name="userImg" type="button" id="inputGroupFileAddon04">Upload</button>
                            </form>
                            <p class="text-muted mb-0">Allowed JPG, GIF or PNG. Max size of 800K</p>
                        </div>
                    </div>
                </div>
                <hr class="my-0">
                <form action="../app/app.php" method="post">
                    <div class="card-body">
                        <div class="row">
                            <?php if ($_SESSION['author_id'] == 1) { ?>
                                <div class="mb-3 col-md-6">
                                    <label for="firstName" class="form-label">Username</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-user"></i></span>
                                        <input class="form-control" type="text" id="username" name="username" value="<?php echo $selectD['username']; ?>" placeholder="<?php echo $selectD['username']; ?>">
                                    </div>
                                </div>
                            <?php } else { ?>
                                <div class="mb-3 col-md-6">
                                    <label for="firstName" class="form-label">Username</label>
                                    <div class="input-group input-group-merge">
                                        <span class="input-group-text"><i class="bx bx-user"></i></span>
                                        <input class="form-control" type="text" id="username" name="username" value="<?php echo $selectD['username']; ?>" placeholder="<?php echo $selectD['username']; ?>" readonly>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="mb-3 col-md-6">
                                <label for="firstName" class="form-label">Full Name</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-user"></i></span>
                                    <input class="form-control" type="text" id="userFull" name="userFull" value="<?php echo $selectD['first_name'] . ' ' . $selectD['last_name'] ?>" placeholder="">
                                </div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="lastName" class="form-label">Email</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                    <input class="form-control" type="email" name="userEmail" id="userEmail" value="<?php echo $selectD['email'] ?>" placeholder="">
                                </div>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label for="email" class="form-label">Phone</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                    <input class="form-control" type="text" id="userMobile" name="userMobile" maxlength="10" value="<?php echo $selectD['phone'] ?>" placeholder="<?php echo $selectD['phone'] ?>">
                                    <input type="hidden" class="form-control" name="userid" value='<?php echo $selectD['user_id'] ?>'>
                                </div>
                            </div>

                            <div class="mb-3 col-md-6">
                                <label class="form-label" for="phoneNumber">Role</label>
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text"><i class="tf-icons bx bx-check-shield"></i></span>
                                    <?php if ($selectD['role'] == 1) { ?>
                                        <input type="text" id="role" name="role" class="form-control" value="Admin" readonly>
                                    <?php } elseif ($selectD['role'] == 2) { ?>
                                        <input type="text" id="role" name="role" class="form-control" value="Sub-Admin" readonly>
                                    <?php } elseif ($selectD['role'] == 3) { ?>
                                        <input type="text" id="role" name="role" class="form-control" value="News Anchor" readonly>
                                    <?php } elseif ($selectD['role'] == 0) { ?>
                                        <input type="text" id="role" name="role" class="form-control" value="Oprator" readonly>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <button type="submit" name="userProfile" class="btn btn-primary me-2">Save changes</button>
                        <!-- <button type="reset" class="btn btn-outline-secondary">Cancel</button> -->
                    </div>
                </form>
            </div>
            <!-- /Account -->
        </div>
    </div>
<?php } else {
    echo "<script>window.location.href='./404.php';</script>";
} ?>
</div>
<script>
    $(document).ready(function() {
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function(e) {
                    $('#userImgShow').attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        }
        // $("#voterImgShow").hide();
        $("#upload").change(function() {
            readURL(this);
            $("#userImgShow").show();
        });
    });
</script>

<!-- / Content -->
<?php include_once "_footer.php"; ?>