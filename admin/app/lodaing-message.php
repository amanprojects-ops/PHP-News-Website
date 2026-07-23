<?php

if (isset($_POST['setmsg'])) {
    include_once('_DBconnect.php');
    $msgid = mysqli_real_escape_string($conn, $_POST['msgid']);
    $checkmsg = "SELECT * FROM contact_us WHERE id ='{$msgid}'";
    $checkQuery = mysqli_query($conn, $checkmsg);
    if ($checkQuery && mysqli_num_rows($checkQuery) > 0) {
        $fetchmsg = mysqli_fetch_assoc($checkQuery);
        $name = htmlspecialchars($fetchmsg['name'] ?? '');
        $email = htmlspecialchars($fetchmsg['email'] ?? '');
        $message = htmlspecialchars($fetchmsg['message'] ?? '');
        $created = $fetchmsg['created_at'] ?? '';
        $sentDate = $created ? date('d-m-Y H:i', strtotime($created)) : 'N/A';

        echo "<div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='modalCenterTitle'>Contact Message Details</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class='row mb-3'>
                    <div class='col'>
                        <div class='card accordion-item'>
                            <h2 class='accordion-header' id='headingOne'>
                                <button type='button' class='accordion-button collapsed' data-bs-toggle='collapse'
                                    data-bs-target='#accordionOne' aria-expanded='false' aria-controls='accordionOne'>
                                    {$name}
                                </button>
                            </h2>

                            <div id='accordionOne' class='accordion-collapse collapse' data-bs-parent='#accordionExample'>
                                <div class='accordion-body py-3'>
                                    <ul class='list-group list-group-flush'>
                                        <li class='list-group-item d-flex align-items-center px-0'>
                                            <i class='bx bx-user me-2 text-primary'></i>
                                            <strong>Name:</strong>&nbsp; {$name}
                                        </li>
                                        <li class='list-group-item d-flex align-items-center px-0'>
                                            <i class='bx bx-envelope me-2 text-primary'></i>
                                            <strong>Email:</strong>&nbsp; <a href='mailto:{$email}'>{$email}</a>
                                        </li>
                                        <li class='list-group-item px-0'>
                                            <div class='d-flex align-items-start'>
                                                <i class='bx bx-comment-detail me-2 mt-1 text-primary'></i>
                                                <div><strong>Message:</strong><br><p class='mt-1 mb-0 text-break'>{$message}</p></div>
                                            </div>
                                        </li>                                    
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row g-2'>
                    <div class='col mb-0'>
                        <label class='form-label'>Sent Date</label>
                        <div class='alert alert-primary mb-0' role='alert'>{$sentDate}</div>
                    </div>
                </div>
            </div>
            <div class='modal-footer'>
                <button type='button' class='btn btn-outline-secondary' data-bs-dismiss='modal'>Close</button>
            </div>
        </div>";
    } else {
        echo "<div class='p-3 text-muted text-center'>No Record Found.</div>";
    }
}

?>