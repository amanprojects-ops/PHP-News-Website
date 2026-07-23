<?php
$sessionKeys = ['success', 'error', 'warning'];
foreach ($sessionKeys as $key) {
  if (isset($_SESSION[$key])) {
    // Use the actual message from the session
    $message = addslashes($_SESSION[$key]);
    echo "<script>
        Swal.fire({
            toast: true,
            position: 'center',
            icon: '$key',
            title: '$message',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
        </script>";
    unset($_SESSION[$key]);
    break;
  }
}
?>