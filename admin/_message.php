<?php
$sessions = ['success' => 'Login SuccessFully', 'error' => 'Login Failed', 'warning' => 'Login Warning'];
foreach ($sessions as $session => $message) {
  if (isset($_SESSION[$session])) {
    echo "<script>
    Swal.fire({
      position: 'center',
      icon: '$session',
      title: '$message',
      showConfirmButton: false,
      timer: 2000
    });
    </script>";
    unset($_SESSION[$session]);
    break;
  }
}
?>