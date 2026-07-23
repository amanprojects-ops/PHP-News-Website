<?php 
include_once "header.php";

$alert_msg = '';
$alert_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_contact'])) {
  $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
  $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
  $subject = mysqli_real_escape_string($conn, trim($_POST['subject'] ?? ''));
  $message = mysqli_real_escape_string($conn, trim($_POST['message'] ?? ''));

  if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $sql = "INSERT INTO contact_us (name, email, subject, message) VALUES ('{$name}', '{$email}', '{$subject}', '{$message}')";
      if (mysqli_query($conn, $sql)) {
        $alert_msg = "Thank you! Your message has been sent successfully to our editorial team.";
        $alert_type = "success";
      } else {
        $alert_msg = "Database Error: Could not store your message. Please try again later.";
        $alert_type = "danger";
      }
    } else {
      $alert_msg = "Please provide a valid email address.";
      $alert_type = "warning";
    }
  } else {
    $alert_msg = "Please fill in all required fields.";
    $alert_type = "warning";
  }
}

$site_email = htmlspecialchars($settings['workEmail'] ?? 'editorial@newswebsite.com');
$site_name = htmlspecialchars($settings['websitename'] ?? 'Our News Portal');
?>

<main class="py-5 bg-light">
  <div class="container">

    <!-- Header Banner -->
    <div class="text-center mb-5">
      <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-semibold mb-2">
        <i class="fa fa-paper-plane me-1"></i> Get In Touch
      </span>
      <h1 class="display-6 fw-bold text-dark mb-2">Contact Our Editorial Team</h1>
      <p class="text-muted mx-auto" style="max-width: 580px;">
        Have a news tip, feedback, business query, or copyright concern? Reach out to <strong><?php echo $site_name; ?></strong> below.
      </p>
    </div>

    <!-- Alert Notification -->
    <?php if (!empty($alert_msg)): ?>
      <div class="row justify-content-center mb-4">
        <div class="col-lg-10">
          <div class="alert alert-<?php echo $alert_type; ?> alert-dismissible fade show rounded-4 shadow-sm p-3 border-0 d-flex align-items-center gap-3" role="alert">
            <i class="fa <?php echo ($alert_type === 'success') ? 'fa-check-circle fs-3 text-success' : 'fa-exclamation-circle fs-3 text-danger'; ?>"></i>
            <div>
              <strong><?php echo ($alert_type === 'success') ? 'Success!' : 'Notice:'; ?></strong> <?php echo $alert_msg; ?>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div class="row g-4 justify-content-center">
      
      <!-- Contact Info Cards Column -->
      <div class="col-lg-4">
        
        <!-- Official Email Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white hover-card">
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px; font-size: 1.25rem;">
              <i class="fa fa-envelope"></i>
            </div>
            <div>
              <h6 class="fw-bold text-dark mb-1">Official Email</h6>
              <a href="mailto:<?php echo $site_email; ?>" class="text-primary text-decoration-none small fw-semibold">
                <?php echo $site_email; ?>
              </a>
            </div>
          </div>
        </div>

        <!-- News Desk Office Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white hover-card">
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px; font-size: 1.25rem;">
              <i class="fa fa-map-marker"></i>
            </div>
            <div>
              <h6 class="fw-bold text-dark mb-1">Editorial Headquarters</h6>
              <p class="text-muted small mb-0">
                Media Center &amp; Newsroom,<br><?php echo $site_name; ?> Desk
              </p>
            </div>
          </div>
        </div>

        <!-- Working Hours Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white hover-card">
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-warning text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px; font-size: 1.25rem;">
              <i class="fa fa-clock-o"></i>
            </div>
            <div>
              <h6 class="fw-bold text-dark mb-1">Operating Hours</h6>
              <p class="text-muted small mb-0">
                Monday - Saturday: 8:00 AM - 9:00 PM<br>24/7 Breaking News Desk
              </p>
            </div>
          </div>
        </div>

        <!-- Quick Links Pill Card -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white">
          <h6 class="fw-bold mb-2"><i class="fa fa-shield me-2"></i>Privacy &amp; Terms</h6>
          <p class="small text-white-50 mb-3">
            Your personal information is kept strictly confidential in accordance with our site policy.
          </p>
          <a href="privacy-policy.php" class="btn btn-light btn-sm rounded-pill fw-semibold text-primary">Read Privacy Policy</a>
        </div>

      </div>

      <!-- Contact Form Column -->
      <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
          <h3 class="h4 fw-bold text-dark mb-1">Send Us a Message</h3>
          <p class="text-muted small mb-4">Fill out the form below and our desk will respond within 24 hours.</p>

          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            
            <div class="form-group mb-3">
              <label for="name" class="form-label fw-semibold text-dark small">Your Full Name <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control bg-light border-start-0" id="name" name="name" placeholder="John Doe" required>
              </div>
            </div>

            <div class="form-group mb-3">
              <label for="email" class="form-label fw-semibold text-dark small">Email Address <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-envelope"></i></span>
                <input type="email" class="form-control bg-light border-start-0" id="email" name="email" placeholder="name@example.com" required>
              </div>
            </div>

            <div class="form-group mb-3">
              <label for="subject" class="form-label fw-semibold text-dark small">Subject / Topic <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa fa-tag"></i></span>
                <input type="text" class="form-control bg-light border-start-0" id="subject" name="subject" placeholder="e.g. News Tip / General Query" required>
              </div>
            </div>

            <div class="form-group mb-4">
              <label for="message" class="form-label fw-semibold text-dark small">Detailed Message <span class="text-danger">*</span></label>
              <textarea class="form-control bg-light" id="message" name="message" rows="5" placeholder="Write your message or inquiry in detail here..." required></textarea>
            </div>

            <button type="submit" name="submit_contact" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm hover-lift">
              <i class="fa fa-paper-plane me-2"></i> Submit Message
            </button>

          </form>
        </div>
      </div>

    </div>

  </div>
</main>

<style>
.hover-card {
  transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.hover-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
}
.hover-lift {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-lift:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(30, 144, 255, 0.3) !important;
}
.bg-primary-soft {
  background-color: rgba(30, 144, 255, 0.1);
}
</style>

<?php include_once "footer.php"; ?>