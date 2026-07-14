<?php

if (isset($_POST['setmsg'])) {
    include_once('_DBconnect.php');
    $msgid = mysqli_real_escape_string($conn, $_POST['msgid']);
    // $msgid = 30;
    $checkmsg = "SELECT * FROM contact_us 
    WHERE id ='{$msgid}'";
    $checkQuery = mysqli_query($conn, $checkmsg);
    $fetchmsg = mysqli_fetch_assoc($checkQuery);
    if (mysqli_num_rows($checkQuery) > 0) {
        echo "<div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='modalCenterTitle'>Contact Details</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class='row'>
                    <div class='col mb-3'>
                        <div class='card accordion-item'>
                            <h2 class='accordion-header' id='headingOne'>
                                <button type='button' class='accordion-button collapsed' data-bs-toggle='collapse'
                                    data-bs-target='#accordionOne' aria-expanded='false' aria-controls='accordionOne'>
                                    {$fetchmsg['name']}
                                </button>
                            </h2>

                            <div id='accordionOne' class='accordion-collapse collapse'
                                data-bs-parent='#accordionExample' style=''>
                                <div class='accordion-body'>
                                <div class='demo-inline-spacing mt-3'>
                                    <ul class='list-group'>
                                    <li class='list-group-item d-flex align-items-center'>
                                        <i class='bx bx-msg me-2'></i>
                                        {$fetchmsg['email']}
                                    </li>
                                    <li class='list-group-item d-flex align-items-center'>
                                       <i class='bx bx-envelope me-2'></i>
                                        {$fetchmsg['email']}
                                    </li>
                                    <li class='list-group-item d-flex align-items-center'>
                                        <i class='bx bx-run me-2'></i>
                                        {$fetchmsg['message']}
                                    </li>                                    
                                    </ul>
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row g-2'>
                    
                    <div class='col mb-0'>
                        <label for='dobWithTitle' class='form-label'>Message Sand Date</label>
                        <div class='alert alert-primary' role='alert'>";
                        $date = explode('-',$fetchmsg['created_at']);
                        echo $date[2]."-".$date['1']."-".$date['0'];
                        echo"</div>
                    </div>
                </div>
            </div>
            <div class='modal-footer'>";
       
        echo "</div></div>";
    } else {
        echo 'No Recourd Found.';
    }
}

?>