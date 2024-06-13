<?php include_once "_header.php";
include_once "_subHeader.php";

if (isset($_GET['check-user'])) {
    include_once("../app/_DBconnect.php");
    $userid = mysqli_real_escape_string($conn, base64_decode($_GET['userid']));

    $loadQ = "SELECT * FROM user WHERE user_id = '{$userid}'";

    $loadR = mysqli_query($conn, $loadQ);
    if (mysqli_num_rows($loadR) > 0) {
        $loadData = mysqli_fetch_assoc($loadR);

    }

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
                                <h5 class="mb-0">Update User</h5>
                                <small class="text-muted float-end text-uppercase">
                                    <?php echo $loadData['username'] ?>
                                </small>
                            </div>
                            <div class="card-body">
                                <form action="../app/app.php" method="POST">
                                    <div class="mb-3">
                                        <label class="form-label" for="basic-icon-default-fullname">First Name</label>
                                        <div class="input-group input-group-merge">
                                            <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                    class="bx bx-user"></i></span>
                                            <input type="hidden" name="user_id" value="<?php echo $loadData['user_id'] ?>">
                                            <input type="text" class="form-control" id="first_name" name="first_name"
                                                value="<?php echo $loadData['first_name'] ?>" aria-label="Enter last name">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="basic-icon-default-fullname">First Name</label>
                                        <div class="input-group input-group-merge">
                                            <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                    class="bx bx-user"></i></span>
                                            <input type="text" class="form-control" id="last_name" name="last_name"
                                                value="<?php echo $loadData['last_name'] ?>" aria-label="Enter last name">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="basic-icon-default-email">Email</label>
                                        <div class="input-group input-group-merge">
                                            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                            <input type="text" id="userEmail" name="userEmail" class="form-control"
                                                value="<?php echo $loadData['email'] ?>"
                                                aria-label="<?php echo $loadData['email'] ?>"
                                                aria-describedby="basic-icon-default-email2">
                                            <!-- <span id="basic-icon-default-email2" class="input-group-text">@gmail.com</span> -->
                                        </div>
                                        <div class="form-text">You can use letters, numbers &amp; periods</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="basic-icon-default-phone">Phone No</label>
                                        <div class="input-group input-group-merge">
                                            <span id="basic-icon-default-phone2" class="input-group-text"><i
                                                    class="bx bx-phone"></i></span>
                                            <input type="text" id="userMobile" name="userMobile" maxlength="10"
                                                class="form-control phone-mask" value="<?php echo $loadData['phone'] ?>"
                                                aria-label="<?php echo $loadData['phone'] ?>">
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label" for="basic-icon-default-phone">User Role</label>
                                        <div class="input-group">
                                            <label class="input-group-text" for="inputGroupSelect01"><i
                                                    class="menu-icon tf-icons bx bx-check-shield"></i></label>
                                            <select class="form-select" name="role" id="role">
                                                <?php
                                                if ($loadData['role'] == 1) {
                                                    echo "<option selected value='1'>Admin</option>
                                                    <option value='2'>Sub-Admin</option>
                                                    <option value='3'>News Anchor</option>
                                                    <option value='0'>Oprator</option>";
                                                } elseif ($loadData['role'] == 2) {
                                                    echo "
                                                    <option value='1'>Admin</option>
                                                    <option selected value='2'>Sub Admin</option>
                                                    <option value='3'>News Anchor</option>
                                                    <option value='0'>Oprator</option>";
                                                } elseif ($loadData['role'] == 3) {
                                                    echo "
                                                    <option value='1'>Admin</option>
                                                    <option value='2'>Sub Admin</option>
                                                    <option selected value='3'>News Anchor</option>
                                                    <option value='0'>Oprator</option>";
                                                } elseif ($loadData['role'] == 0) {
                                                    echo "
                                                    <option value='1'>Admin</option>
                                                    <option value='2'>Sub Admin</option>
                                                    <option value='3'>News Anchor</option>
                                                    <option selected value='0'>Oprator</option>";
                                                }

                                                ?>

                                            </select>
                                        </div>
                                    </div>
                                    <button type="submit" name="userUpdate" class="btn btn-primary">Update User</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- / Content -->
    <?php
} else {
    $_SESSION['warning'] = "User Data Not Found. Try again.";
    echo "<script>window.location.href='../dashboard/view-user.php';</script>";
}

include_once "_footer.php"; ?>