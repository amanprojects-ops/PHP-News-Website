<?php include_once "_header.php";
include_once "_subHeader.php"; ?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="card mb-4">
                    <div class="card-header border-bottom d-flex justify-content-between align-items-center py-3">
                        <h5 class="mb-0">Add New User</h5>
                        <small class="text-muted float-end">Creator: <?php echo htmlspecialchars($_SESSION['name'] ?? ''); ?></small>
                    </div>
                    <div class="card-body py-4">
                        <div class="alert" id="message" role="alert" style="display:none;"></div>
                        <form action="app/app.php" id="addUser_form" method="post">
                            <div class="mb-3">
                                <label class="form-label" for="username">Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" class="form-control" name="username" id="username"
                                        placeholder="Enter Username" aria-label="Enter Username">
                                    <button class="btn btn-outline-primary" type="button" id="verifyUsername">Verify Username</button>
                                </div>
                                <div class="form-text fw-semibold" id="usernameStatus" style="display:none;"></div>
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <input type="hidden" name="user_id" value='<?php echo htmlspecialchars($_SESSION['author_id'] ?? ''); ?>'>
                                        <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                                            <input type="text" class="form-control" id="first_name"
                                                name="first_name" placeholder="Enter First Name" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="last_name">Last Name <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bx-user"></i></span>
                                            <input type="text" class="form-control" id="last_name" name="last_name"
                                                placeholder="Enter Last Name" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="row">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label class="form-label" for="userEmail">Email <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                            <input type="email" id="userEmail" name="userEmail" class="form-control"
                                                placeholder="email@example.com" readonly>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label" for="userMobile">Phone No <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                            <input type="text" id="userMobile" name="userMobile" maxlength="10"
                                                class="form-control phone-mask" placeholder="99999 99999" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="userPassword">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="userPassword"
                                        placeholder="············" name="password" value="1234" readonly>
                                    <span id="basic-default-password2" class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label" for="role">User Role <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bx bx-shield-quarter"></i></span>
                                    <select class="form-select" id="role" name="role" disabled>
                                        <?php if ($_SESSION['role'] == 1) { ?>
                                        <option value="1">Admin</option>
                                        <option value="2">Sub Admin</option>
                                        <option value="3">News Anchor</option>
                                        <option selected value="0">Operator</option>
                                        <?php } elseif ($_SESSION['role'] == 2) { ?>
                                            <option selected value="3">News Anchor</option>
                                            <option value="0">Operator</option>
                                        <?php } elseif ($_SESSION['role'] == 3) { ?>
                                            <option selected value="0">Operator</option>
                                        <?php } ?>
                                    </select>
                                    <input type="hidden" name="addUser" value="">
                                </div>
                            </div>
                            <button type="button" id="addUser" class="btn btn-primary">
                                <i class="bx bx-user-plus me-1"></i> Add User
                            </button>
                        </form>
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
                $('#message').removeClass('alert-success').addClass('alert-danger').html("Username cannot be empty.").fadeIn(500, function () {
                    setTimeout(function() { $('#message').fadeOut(500); }, 3000);
                });
            } else {
                $.ajax({
                    url: 'app/verifyUsername.php',
                    type: 'POST',
                    data: { username: username, checkUsername: username },
                    success: function (usernameStatus) {
                        if (usernameStatus == 1) {
                            $('#message').removeClass('alert-danger').addClass('alert-success').html("Username available.").fadeIn(500, function () {
                                setTimeout(function() { $('#message').fadeOut(500); }, 3000);
                            });
                            $('#usernameStatus').removeClass('text-danger').addClass('text-success').fadeIn(500).html('Username available');
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
                                    $('#message').removeClass('alert-success').addClass('alert-danger').html("All fields are required.").fadeIn(500, function () {
                                        setTimeout(function() { $('#message').fadeOut(500); }, 3000);
                                    });
                                } else {
                                    $('#addUser_form').submit();
                                }
                            });

                        } else {
                            $('#message').removeClass('alert-success').addClass('alert-danger').html("Username already taken by another user.").fadeIn(500, function () {
                                setTimeout(function() { $('#message').fadeOut(500); }, 3000);
                            });
                            $('#usernameStatus').removeClass('text-success').addClass('text-danger').fadeIn(500).html('Username already taken');
                        }
                    }
                })
            }
        });
    });
</script>
<!-- / Content -->
<?php include_once "_footer.php"; ?>