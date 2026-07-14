<?php
session_start();
if (isset($_POST['setUser'])) {
    include_once('_DBconnect.php');
    $userid = mysqli_real_escape_string($conn, $_POST['userid']);
    // $userid = 30;
    $checkUser = "SELECT * FROM user
    WHERE user_id ='".$userid."'";
    $checkQuery = mysqli_query($conn, $checkUser);
    $fetchUser = mysqli_fetch_assoc($checkQuery);
    $userI = $fetchUser['userStatus'];
    if (mysqli_num_rows($checkQuery) > 0) {
        echo "<div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='modalCenterTitle'>User Details</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class='row'>
                    <div class='col mb-3'>
                        <div class='card accordion-item'>
                            <h2 class='accordion-header' id='headingOne'>
                                <button type='button' class='accordion-button collapsed' data-bs-toggle='collapse'
                                    data-bs-target='#accordionOne' aria-expanded='false' aria-controls='accordionOne'>
                                    {$fetchUser['first_name']} {$fetchUser['last_name']}
                                </button>
                            </h2>

                            <div id='accordionOne' class='accordion-collapse collapse'
                                data-bs-parent='#accordionExample'>
                                <div class='accordion-body'>
                                  <div class='table-responsive text-nowrap'>
                                    <table class='table'>                                      
                                        <tbody class='table-border-bottom-0'>
                                        <tr>
                                            <th>UserName</th>
                                            <td>: {$fetchUser['username']}</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile No</th>
                                            <td>: {$fetchUser['phone']}</td>
                                        </tr>
                                        <tr>
                                            <th>Email Id</th>
                                            <td>: {$fetchUser['email']}</td>
                                        </tr>
                                        <tr>
                                            <th>User Status</th>
                                            <td>:";
                                            if($fetchUser['userStatus'] == 'Y'){
                                                echo "<span class='badge bg-success'>Active</span>";
                                            }
                                            elseif($fetchUser['userStatus'] == 'N'){
                                                echo "<span class='badge bg-danger'>Dactive</span>";                                                
                                            }
                                            elseif($fetchUser['userStatus'] == 'W'){
                                                echo "<span class='badge bg-warning'>Save User draft</span>";
                                            }else{
                                                echo "";
                                            }
                                            echo "</td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row g-2'>
                    
                    <div class='col mb-0'>
                        <label for='dobWithTitle' class='form-label'>User Registration Date</label>
                        <div class='alert alert-primary' role='alert'>{$fetchUser['rDate']}</div>
                    </div>
                </div>
            </div>
            <div class='modal-footer'>";
            if($_SESSION['role'] == 1){
            if ($userI == 'N' || $userI == 'W') {

                if ($userI == 'N') {
                    echo "<button type='submit'class='btn btn-danger' disabled>Rajected</button></form>";
                } else {
                    echo "<form action='../app/app.php' method='POST'><input type='hidden' name='userR' value='{$fetchUser['user_id']}'><button type='submit' name='userRajected' class='btn btn-danger'>Raject</button></form>";
                }

                echo "<form action='../app/app.php' method='POST'><input type='hidden' name='userA' value='{$fetchUser['user_id']}'><button type='submit' name='userApproved' class='btn btn-success'>Approve</button></form>";

            } elseif ($userI == 'Y') {
                echo "<form action='../app/app.php' method='POST'><input type='hidden' name='userR' value='{$fetchUser['user_id']}'><button type='submit' name='userRajected' class='btn btn-danger'> Raject</button></form>";
                echo "<button type='submit'class='btn btn-success' disabled>Approved</button></form>";
            }
        }
        echo "</div></div>";
    } else {
        echo 'No Recourd Found.';
    }
}