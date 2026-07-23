<?php
session_start();
if (isset($_POST['set'])) {
    include_once('_DBconnect.php');
    $categoryid = mysqli_real_escape_string($conn, $_POST['categoryid']);
    $checkCategory = "SELECT * FROM category 
    LEFT JOIN user ON category.author = user.user_id
    WHERE category_id ='{$categoryid}'";
    $checkQuery = mysqli_query($conn, $checkCategory);
    if ($checkQuery && mysqli_num_rows($checkQuery) > 0) {
        $fetchCategory = mysqli_fetch_assoc($checkQuery);
        $CategoryI = $fetchCategory['categoryStatus'];
        $categoryName = htmlspecialchars($fetchCategory['category_name'] ?? '');
        $categoryTitle = htmlspecialchars($fetchCategory['categoryTitle'] ?? '');
        echo "<div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='modalCenterTitle'>Category Details</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class='row mb-3'>
                    <div class='col'>
                        <div class='card accordion-item'>
                            <h2 class='accordion-header' id='headingOne'>
                                <button type='button' class='accordion-button collapsed' data-bs-toggle='collapse'
                                    data-bs-target='#accordionOne' aria-expanded='false' aria-controls='accordionOne'>
                                    {$categoryName}
                                </button>
                            </h2>

                            <div id='accordionOne' class='accordion-collapse collapse' data-bs-parent='#accordionExample'>
                                <div class='accordion-body'>
                                    {$categoryTitle}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row g-2'>
                    <div class='col mb-0'>
                        <label class='form-label'>Category Date</label>
                        <div class='alert alert-primary mb-0' role='alert'>" . htmlspecialchars($fetchCategory['categoryDate'] ?? '') . "</div>
                    </div>
                </div>
            </div>
            <div class='modal-footer d-flex justify-content-end gap-2'>";
        if ($_SESSION['role'] == 1) {
            if ($CategoryI == 'N' || $CategoryI == 'W') {
                if ($CategoryI == 'N') {
                    echo "<button type='button' class='btn btn-danger' disabled>Rejected</button>";
                } else {
                    echo "<form action='../app/app.php' method='POST' class='d-inline'><input type='hidden' name='categoryR' value='{$fetchCategory['category_id']}'><button type='submit' name='categoryRajected' class='btn btn-danger'>Reject</button></form>";
                }

                echo "<form action='../app/app.php' method='POST' class='d-inline'><input type='hidden' name='categoryA' value='{$fetchCategory['category_id']}'><button type='submit' name='categoryApproved' class='btn btn-success'>Approve</button></form>";

            } elseif ($CategoryI == 'Y') {
                echo "<form action='../app/app.php' method='POST' class='d-inline'><input type='hidden' name='categoryR' value='{$fetchCategory['category_id']}'><button type='submit' name='categoryRajected' class='btn btn-danger'>Reject</button></form>";
                echo "<button type='button' class='btn btn-success' disabled>Approved</button>";
            }
        }
        echo "</div></div>";
    } else {
        echo "<div class='p-3 text-muted text-center'>No Record Found.</div>";
    }
}
?>