<?php
session_start();
if (isset($_POST['searchUser'])) {
    include_once('_DBconnect.php');
    $userid = mysqli_real_escape_string($conn, $_POST['username']);
    $userMobile = mysqli_real_escape_string($conn, $_POST['usermobile']);
    // $Userid = 30;
    $checkUser = "SELECT * FROM user WHERE username ='".$userid."' && phone = '".$userMobile."'";
    $checkQuery = mysqli_fetch_assoc(mysqli_query($conn, $checkUser));
   
    if(mysqli_num_rows(mysqli_query($conn, $checkUser))>0){
        echo " <form action='./app/app.php' method='post'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='modalToggleLabel'>User Details</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                    <div class='row'>
                        <div class='col mb-3'>
                                <label for='nameWithTitle' class='form-label'>Name</label>
                                <input type='text' id='nameWithTitle' class='form-control' value='{$checkQuery['first_name']} {$checkQuery['last_name']}' readonly>
                                <input type='hidden' name='mobile' value='{$checkQuery['phone']}'>
                                <input type='hidden' name='username' value='{$checkQuery['username']}'>
                            </div>
                        </div>
                        <div class='row g-2'>
                            <div class='col mb-0'>
                                <label for='emailWithTitle' class='form-label'>Password</label>
                                    <input type='password' name='userPassword' class='form-control' placeholder='******************' required>
                            </div>
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-outline-secondary' data-bs-dismiss='modal'> Close </button>
                        <button type='submit' name='changeUserPassword' class='btn btn-primary'>Change Password</button>
                    </div>
                </form>";
    }else{
        echo "
        <form action='./app/app.php' method='post'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='modalToggleLabel'>User Details</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>
                    <div class='row'>
                         <hr><h2 class='text-center'>User Details not found.</h2>
                    </div>
                    <div class='modal-footer'>
                        <button type='button' class='btn btn-outline-secondary' data-bs-dismiss='modal'> Close </button>
                        <button type='submit' name='changeUserPassword' class='btn btn-primary' disabled>Change Password</button>
                    </div>
                </form> ";
    }
}else{
    echo "Sorry";
}
