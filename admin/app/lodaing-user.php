<?php
session_start();
if (isset($_POST['searchUser'])) {
    include_once('_DBconnect.php');
    $userid = mysqli_real_escape_string($conn, $_POST['username']);
    $userMobile = mysqli_real_escape_string($conn, $_POST['usermobile']);
    $checkUser = "SELECT * FROM user WHERE username ='{$userid}' AND phone = '{$userMobile}'";
    $queryResult = mysqli_query($conn, $checkUser);
   
    if ($queryResult && mysqli_num_rows($queryResult) > 0) {
        $checkQuery = mysqli_fetch_assoc($queryResult);
        $name = htmlspecialchars(($checkQuery['first_name'] ?? '') . ' ' . ($checkQuery['last_name'] ?? ''));
        $phone = htmlspecialchars($checkQuery['phone'] ?? '');
        $username = htmlspecialchars($checkQuery['username'] ?? '');

        echo "<form action='app/app.php' method='post'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='modalToggleLabel'>User Details</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                        <div class='row mb-3'>
                            <div class='col'>
                                <label for='nameWithTitle' class='form-label'>Name</label>
                                <input type='text' id='nameWithTitle' class='form-control' value='{$name}' readonly>
                                <input type='hidden' name='mobile' value='{$phone}'>
                                <input type='hidden' name='username' value='{$username}'>
                            </div>
                        </div>
                        <div class='row g-2'>
                            <div class='col mb-0'>
                                <label for='userPassword' class='form-label'>New Password</label>
                                <input type='password' id='userPassword' name='userPassword' class='form-control' placeholder='Enter new password' required>
                            </div>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-outline-secondary' data-bs-dismiss='modal'>Close</button>
                        <button type='submit' name='changeUserPassword' class='btn btn-primary'>Change Password</button>
                    </div>
                </form>";
    } else {
        echo "<form action='app/app.php' method='post'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='modalToggleLabel'>User Details</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body text-center py-4'>
                        <i class='bx bx-user-x fs-1 text-muted mb-2'></i>
                        <h5 class='text-muted'>User Details not found.</h5>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-outline-secondary' data-bs-dismiss='modal'>Close</button>
                        <button type='submit' name='changeUserPassword' class='btn btn-primary' disabled>Change Password</button>
                    </div>
                </form>";
    }
} else {
    echo "<div class='p-3 text-muted text-center'>Invalid request.</div>";
}
?>
