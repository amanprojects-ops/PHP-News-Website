<?php

if (isset($_SESSION['success'])) {
    echo "<script>
    Swal.fire({
      position: 'center',
      icon: 'success',
      title: '{$_SESSION['success']}',
      showConfirmButton: false,
      timer: 2000
    });
    </script>";
    unset($_SESSION['success']);
}
elseif (isset($_SESSION['error'])) {
    echo "<script>
    Swal.fire({
      position: 'center',
      icon: 'error',
      title: '{$_SESSION['error']}',
      showConfirmButton: false,
      timer: 2000
    })
    </script>";
    unset($_SESSION['error']);
}
elseif (isset($_SESSION['warning'])) {
    echo "<script>
    Swal.fire({
      position: 'center',
      icon: 'warning',
      title: '{$_SESSION['warning']}',
      showConfirmButton: false,
      timer:4000
    })
    </script>";
    unset($_SESSION['warning']);
}


?>