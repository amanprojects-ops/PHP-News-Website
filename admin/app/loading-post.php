<?php
session_start();
if (isset($_POST['set'])) {
    include_once('_DBconnect.php');
    $postid = mysqli_real_escape_string($conn, $_POST['postid']);
    $checkPost = "SELECT * FROM post 
    LEFT JOIN user ON post.author = user.user_id
    WHERE post_id ='{$postid}'";
    $checkQuery = mysqli_query($conn, $checkPost);
    $fetchPost = mysqli_fetch_assoc($checkQuery);
    $postI = $fetchPost['postStatus'];
    $postTitle = substr($fetchPost['title'], 0, 35);
    if (mysqli_num_rows($checkQuery) > 0) {
        echo "<div class='modal-content'>
            <div class='modal-header'>
                <h5 class='modal-title' id='modalCenterTitle'>Post Details</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
            </div>
            <div class='modal-body'>
                <div class='row'>
                    <div class='col mb-3'>
                        <div class='card accordion-item'>
                            <h2 class='accordion-header' id='headingOne'>
                                <button type='button' class='accordion-button collapsed' data-bs-toggle='collapse'
                                    data-bs-target='#accordionOne' aria-expanded='false' aria-controls='accordionOne'>
                                    {$postTitle}
                                </button>
                            </h2>

                            <div id='accordionOne' class='accordion-collapse collapse'
                                data-bs-parent='#accordionExample' style=''>
                                <div class='accordion-body overflow-auto'>
                                    {$fetchPost['description']}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class='row g-2'>
                    <div class='col mb-0'>
                        <label for='emailWithTitle' class='form-label'>Post Author</label>
                        <div class='alert alert-primary' role='alert'>{$fetchPost['first_name']} {$fetchPost['last_name']}</div>
                    </div>
                    <div class='col mb-0'>
                        <label for='dobWithTitle' class='form-label'>Post Date</label>
                        <div class='alert alert-primary' role='alert'>{$fetchPost['post_date']}</div>
                    </div>
                </div>
            </div>
            <div class='modal-footer'>";
        if ($_SESSION['role'] == 1) {
            if ($postI == 'N' || $postI == 'W') {
                if ($postI == 'N') {
                    echo "<button type='submit'class='btn btn-danger' disabled>Rajected</button></form>";
                } else {
                    echo "<form action='../app/app.php' method='post'><input type='hidden' name='postR' value='{$fetchPost['post_id']}'><input type='hidden' name='postC' value='{$fetchPost['category']}'><button type='submit' name='postRajected' class='btn btn-danger'>Raject</button></form>";
                }

                echo "<form action='../app/app.php' method='post'><input type='hidden' name='postA' value='{$fetchPost['post_id']}'><input type='hidden' name='postC' value='{$fetchPost['category']}'><button type='submit' name='postApproved' class='btn btn-success'>Approve</button></form>";

            } elseif ($postI == 'Y') {
                echo "<form action='../app/app.php' method='post'><input type='hidden' name='postR' value='{$fetchPost['post_id']}'><input type='hidden' name='postC' value='{$fetchPost['category']}'><button type='submit' name='postRajected' class='btn btn-danger'> Raject</button></form>";
                echo "<button type='submit'class='btn btn-success' disabled>Approved</button></form>";
            }
        } elseif ($_SESSION['role'] == 2 || $_SESSION['role'] == 3 || $_SESSION['role'] == 0) {
            if ($postI == 'N') {
                echo "<button type='submit'class='btn btn-danger' disabled>Rajected</button></form>";
            } else {
                if ($postI == 'Y') {
                    echo "<button type='submit'class='btn btn-success' disabled>Approved</button></form>";
                } else {
                    echo "<form action='../app/app.php' method='post'><input type='hidden' name='postR' value='{$fetchPost['post_id']}'><input type='hidden' name='postC' value='{$fetchPost['category']}'><button type='submit' name='postRajected' class='btn btn-danger'>Raject</button></form>";
                }

            }
        }

        echo "</div></div>";
    } else {
        echo 'No Recourd Found.';
    }
}

?>