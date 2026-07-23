<?php
session_start();
if (isset($_POST['setUser'])) {
    include_once('_DBconnect.php');
    $userid = mysqli_real_escape_string($conn, $_POST['userid']);
    $checkUser = "SELECT * FROM user WHERE user_id ='{$userid}'";
    $checkQuery = mysqli_query($conn, $checkUser);
    if ($checkQuery && mysqli_num_rows($checkQuery) > 0) {
        $fetchUser = mysqli_fetch_assoc($checkQuery);
        $userI = $fetchUser['userStatus'];
        $fullName = htmlspecialchars(($fetchUser['first_name'] ?? '') . ' ' . ($fetchUser['last_name'] ?? ''));
        echo "<div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='modalCenterTitle'>User Details</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class='row mb-3'>
                    <div class='col'>
                        <div class='card accordion-item'>
                            <h2 class='accordion-header' id='headingOne'>
                                <button type='button' class='accordion-button collapsed' data-bs-toggle='collapse'
                                    data-bs-target='#accordionOne' aria-expanded='false' aria-controls='accordionOne'>
                                    {$fullName}
                                </button>
                            </h2>

                            <div id='accordionOne' class='accordion-collapse collapse' data-bs-parent='#accordionExample'>
                                <div class='accordion-body'>
                                  <div class='table-responsive text-nowrap'>
                                    <table class='table table-borderless mb-0'>                                      
                                        <tbody>
                                        <tr>
                                            <th>Username</th>
                                            <td>: " . htmlspecialchars($fetchUser['username'] ?? '') . "</td>
                                        </tr>
                                        <tr>
                                            <th>Mobile No</th>
                                            <td>: " . htmlspecialchars($fetchUser['phone'] ?? 'N/A') . "</td>
                                        </tr>
                                        <tr>
                                            <th>Email Id</th>
                                            <td>: " . htmlspecialchars($fetchUser['email'] ?? 'N/A') . "</td>
                                        </tr>
                                        <tr>
                                            <th>User Status</th>
                                            <td>: ";
                                            if ($fetchUser['userStatus'] == 'Y') {
                                                echo "<span class='badge bg-label-success'>Active</span>";
                                            } elseif ($fetchUser['userStatus'] == 'N') {
                                                echo "<span class='badge bg-label-danger'>Inactive</span>";                                                
                                            } elseif ($fetchUser['userStatus'] == 'W') {
                                                echo "<span class='badge bg-label-warning'>Saved Draft</span>";
                                            } else {
                                                echo "<span class='badge bg-label-secondary'>Unknown</span>";
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
                        <label class='form-label'>User Registration Date</label>
                        <div class='alert alert-primary mb-0' role='alert'>" . htmlspecialchars($fetchUser['rDate'] ?? 'N/A') . "</div>
                    </div>
                </div>
            </div>
            <div class='modal-footer d-flex justify-content-end gap-2'>";
        if ($_SESSION['role'] == 1) {
            if ($userI == 'N' || $userI == 'W') {
                if ($userI == 'N') {
                    echo "<button type='button' class='btn btn-danger' disabled>Rejected</button>";
                } else {
                    echo "<form action='app/app.php' method='POST' class='d-inline'><input type='hidden' name='userR' value='{$fetchUser['user_id']}'><button type='submit' name='userRajected' class='btn btn-danger'>Reject</button></form>";
                }

                echo "<form action='app/app.php' method='POST' class='d-inline'><input type='hidden' name='userA' value='{$fetchUser['user_id']}'><button type='submit' name='userApproved' class='btn btn-success'>Approve</button></form>";

            } elseif ($userI == 'Y') {
                echo "<form action='app/app.php' method='POST' class='d-inline'><input type='hidden' name='userR' value='{$fetchUser['user_id']}'><button type='submit' name='userRajected' class='btn btn-danger'>Reject</button></form>";
                echo "<button type='button' class='btn btn-success' disabled>Approved</button>";
            }
        }
        echo "</div></div>";
    } else {
        echo "<div class='p-3 text-muted text-center'>No Record Found.</div>";
    }
}
?>