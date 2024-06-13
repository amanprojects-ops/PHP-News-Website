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
                            <h5 class="mb-0">Add New User</h5>
                            <small class="text-muted float-end">
                                <?php echo $_SESSION['name'] ?>
                            </small>
                        </div>
                        <div class="card-body">
                            <div class="alert" id="message" role="alert" style="display:none;"></div>
                            <form action="../app/app.php" id="addUser_form" method="post">
                                <div class="mb-3">
                                    <label class="form-label" for="basic-icon-default-fullname">Username</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="username" id="username"
                                            placeholder="Enter Username" aria-label="Enter Username"
                                            aria-describedby="button-addon2">
                                        <button class="btn btn-outline-primary" type="button" id="verifyUsername">Verify
                                            Username</button>
                                    </div>
                                    <div class="form-text text-bold" id="usernameStatus" style="display:none;"></div>
                                </div>
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <input type="hidden" name="user_id"
                                                value='<?php echo $_SESSION['author_id']; ?>'>
                                            <label class="form-label" for="basic-icon-default-fullname">First
                                                Name</label>
                                            <div class="input-group input-group-merge">
                                                <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                        class="bx bx-user"></i></span>
                                                <input type="text" class="form-control" id="first_name"
                                                    name="first_name" placeholder="Enter First Name"
                                                    aria-label="Enter First Name" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="basic-icon-default-fullname">Last
                                                Name</label>
                                            <div class="input-group input-group-merge">
                                                <span id="basic-icon-default-fullname2" class="input-group-text"><i
                                                        class="bx bx-user"></i></span>
                                                <input type="text" class="form-control" id="last_name" name="last_name"
                                                    placeholder="Enter Last Name" aria-label="Enter Last Name" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="form-label" for="basic-icon-default-email">Email</label>
                                            <div class="input-group input-group-merge">
                                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                                <input type="text" id="userEmail" name="userEmail" class="form-control"
                                                    placeholder="email@example.com" aria-label="email@example.com"
                                                    aria-describedby="basic-icon-default-email2" readonly>
                                                <!-- <span id="basic-icon-default-email2" class="input-group-text">@gmail.com</span> -->
                                            </div>
                                            <div class="form-text">You can use letters, numbers &amp; periods</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label" for="basic-icon-default-phone">Phone No</label>
                                            <div class="input-group input-group-merge">
                                                <span id="basic-icon-default-phone2" class="input-group-text"><i
                                                        class="bx bx-phone"></i></span>
                                                <input type="text" id="userMobile" name="userMobile" maxlength="10"
                                                    class="form-control phone-mask" placeholder="99999 99999"
                                                    aria-label="99999 99999" readonly>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-password-toggle">
                                    <label class="form-label" for="basic-default-password12">Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="userPassword"
                                            placeholder="············" name="password" value="1234" readonly>
                                        <span id="basic-default-password2" class="input-group-text cursor-pointer"><i
                                                class="bx bx-hide"></i></span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="basic-icon-default-phone">User Role</label>
                                    <div class="input-group">
                                        <label class="input-group-text" for="inputGroupSelect01"><i
                                                class="menu-icon tf-icons bx bx-check-shield"></i></label>
                                        <select class="form-select" id="role" name="role" disabled>
                                            <?php if ($_SESSION['role'] == 1 ) { ?>
                                            <option value="1">Admin</option>
                                            <option value="2">Sub Admin</option>
                                            <option value="3">News Anchor</option>
                                            <option selected value="0">Oprator</option>
                                            <?php }elseif($_SESSION['role'] == 2){?>
                                                <option selected value="3">News Anchor</option>
                                                <option value="0">Oprator</option>
                                            <?php } elseif($_SESSION['role'] == 3){?>
                                                <option selected value="0">Oprator</option>
                                            <?php } ?>
                                        </select>
                                        <input type="hidden" name="addUser" value="">
                                    </div>
                                </div>
                                <button type="button" id="addUser" class="btn btn-primary">Add User</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        $("#verifyUsername").click(function () {
            var username = $('#username').val();
            if (username == '') {
                $('#message').removeClass('alert-success').addClass('alert-danger').html("Username Cannot empty.").slideDown(3000, function () {
                    $(this).slideUp(3000);
                });
            } else {
                $.ajax({
                    url: '../app/verifyUsername.php',
                    type: 'POST',
                    data: { username: username, checkUsername: username },
                    success: function (usernameStatus) {
                        if (usernameStatus == 1) {
                            $('#message').removeClass('alert-danger').addClass('alert-success').html("Username available.").slideDown(3000, function () {
                                $(this).slideUp(3000);
                            });
                            $('#usernameStatus').removeClass('text-danger').addClass('text-success').fadeIn(1500).html('Username available');
                            $('#username').attr('readonly', true);
                            $('#verifyUsername').attr('disabled', true);
                            $('#role').removeAttr("disabled");
                            $('#first_name').removeAttr("readonly");
                            $('#last_name').removeAttr("readonly");
                            $('#userEmail').removeAttr("readonly");
                            $('#userMobile').removeAttr("readonly");
                            $('#userPassword').removeAttr("readonly");

                            $('#addUser').click(function(){
                                var username = $('#username').val();
                                var first_name = $('#first_name').val();
                                var last_name = $('#last_name').val();
                                var userEmail = $('#userEmail').val();
                                var userMobile = $('#userMobile').val();
                                var userPassword = $('#userPassword').val();

                                if(username == '' || first_name == '' || last_name == '' || userEmail == '' || userMobile == '' || userPassword == ''){
                                    $('#message').removeClass('alert-success').addClass('alert-danger').html("All fileds are required.").slideDown(3000, function () {
                                        $(this).slideUp(3000);
                                    });
                                }else{
                                    $('#addUser_form').submit();
                                }
                            });

                        } else {
                            $('#message').removeClass('alert-success').addClass('alert-danger').html("Username already taken another user.").slideDown(3000, function () {
                                $(this).slideUp(3000);
                            });
                            $('#usernameStatus').removeClass('text-success').addClass('text-danger').fadeIn(1500).html('Username already taken another user');
                        }
                    }
                })
            }
        });
    });
</script>
<!-- / Content -->
<?php include_once "_footer.php"; ?>