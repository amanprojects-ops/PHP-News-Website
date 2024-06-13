<?php include_once "header.php";

  if (isset($_POST['name']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message'])) {
    include_once "config.php";

    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $sql = "INSERT INTO contact_us (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";

    if (mysqli_query($conn, $sql)) {
      echo "<script type='text/javascript'>alert('Your message has been successfully submitted!');</script>";
    } else {
      echo "Error";
    }

    mysqli_close($conn);
  }


?>
<div class="my-5">
<div class="container">
      <h2 class="text-center">Contact Us</h2>
      <form class='rounded' action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
        <div class="form-group">
          <label for="name">Name:</label>
          <input type="text" class="form-control" id="name" placeholder="Enter Name" name="name">
        </div>
        <div class="form-group">
          <label for="email">Email:</label>
          <input type="email" class="form-control" id="email" placeholder="Enter Email" name="email">
        </div>
        <div class="form-group">
          <label for="subject">Subject:</label>
          <input type="text" class="form-control" id="subject" placeholder="Enter Subject" name="subject">
        </div>
        <div class="form-group">
          <label for="message">Message:</label>
          <textarea class="form-control" rows="5" id="message" name="message"></textarea>
        </div>
        <button type="submit" class="btn btn-primary my-5">Submit</button>
      </form>
    </div>
    </div>
<?php include_once "footer.php"; ?>


<!-- CREATE TABLE contact_us (
  id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(30) NOT NULL,
  email VARCHAR(50) NOT NULL,
  subject VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
 -->