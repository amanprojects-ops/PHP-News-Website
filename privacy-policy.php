<?php 
include_once "header.php";

$site_name = htmlspecialchars($settings['websitename'] ?? 'Our News Portal');
$site_email = htmlspecialchars($settings['workEmail'] ?? 'privacy@newswebsite.com');
$site_url = htmlspecialchars($settings['websiteUrl'] ?? '#');
?>

<main class="py-5 bg-light">
  <div class="container">

    <!-- Header Section -->
    <div class="text-center mb-5">
      <span class="badge bg-primary-soft text-primary px-3 py-2 rounded-pill fw-semibold mb-2">
        <i class="fa fa-shield me-1"></i> Data Privacy &amp; Protection
      </span>
      <h1 class="display-6 fw-bold text-dark mb-2">Privacy Policy</h1>
      <p class="text-muted mx-auto" style="max-width: 600px;">
        Learn how <strong><?php echo $site_name; ?></strong> collects, protects, and handles your personal information responsibly.
      </p>
    </div>

    <div class="row g-4">
      
      <!-- Policy Content Column -->
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
          
          <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
            <div>
              <h3 class="h5 fw-bold text-dark mb-1">Privacy Framework</h3>
              <small class="text-muted"><i class="fa fa-clock-o me-1"></i> Last updated: <?php echo date('F 01, Y'); ?></small>
            </div>
            <span class="badge bg-success-soft text-success rounded-pill px-3 py-2 fw-semibold" style="background-color: rgba(40, 167, 69, 0.1);">
              <i class="fa fa-check-circle me-1"></i> Compliant Policy
            </span>
          </div>

          <div class="privacy-sections">
            
            <!-- Section 1 -->
            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fa fa-info-circle text-primary"></i> 1. Introduction
              </h4>
              <p class="text-muted small mb-0 ps-3">
                At <strong><?php echo $site_name; ?></strong>, we are deeply committed to respecting your privacy and protecting the information you share with us. This Privacy Policy details the types of personal data we collect, how it is used, and the security measures we employ to keep your data safe.
              </p>
            </div>

            <!-- Section 2 -->
            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fa fa-database text-primary"></i> 2. Information We Collect
              </h4>
              <p class="text-muted small mb-2 ps-3">
                We may collect personal and non-personal data when you interact with our website:
              </p>
              <ul class="text-muted small ps-4 mb-0">
                <li class="mb-1"><strong>Personal Information:</strong> Name, email address, or contact details provided when submitting forms, newsletter registrations, or comments.</li>
                <li class="mb-1"><strong>Automated Technical Data:</strong> IP address, browser type, operating system version, referring URLs, and device analytics gathered for performance optimization.</li>
              </ul>
            </div>

            <!-- Section 3 -->
            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fa fa-cookie text-primary"></i> 3. Cookies &amp; Tracking Technologies
              </h4>
              <p class="text-muted small mb-0 ps-3">
                <strong><?php echo $site_name; ?></strong> uses cookies and standard web beacons to enhance your browsing experience, remember your preferences, and analyze site traffic patterns. You can manage or disable cookies via your browser settings; however, disabling them may impact specific interactive website features.
              </p>
            </div>

            <!-- Section 4 -->
            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fa fa-lock text-primary"></i> 4. Data Security Standards
              </h4>
              <p class="text-muted small mb-0 ps-3">
                We employ technical, administrative, and physical security procedures to protect against unauthorized access, loss, misuse, or alteration of personal data. While we enforce high security safeguards, please note that no internet transmission is completely immune to security threats.
              </p>
            </div>

            <!-- Section 5 -->
            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fa fa-share-alt text-primary"></i> 5. Third-Party Disclosures
              </h4>
              <p class="text-muted small mb-0 ps-3">
                We do not sell, trade, or rent your personal identification information to third parties. We may share generic aggregated demographic information not linked to any personal identification with trusted partners and advertisers.
              </p>
            </div>

            <!-- Section 6 -->
            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <i class="fa fa-user-shield text-primary"></i> 6. Your Rights &amp; Choices
              </h4>
              <p class="text-muted small mb-0 ps-3">
                You have the right to request access to, correction of, or deletion of your personal data stored on our platform. You may also unsubscribe from optional email notifications at any time.
              </p>
            </div>

            <!-- Section 7 -->
            <div class="p-3 bg-light rounded-3 border">
              <h5 class="h6 fw-bold text-dark mb-1"><i class="fa fa-envelope text-primary me-2"></i>Contact Privacy Officer</h5>
              <p class="text-muted small mb-2">
                If you have questions, feedback, or data privacy requests regarding <strong><?php echo $site_name; ?></strong>, reach out to our team:
              </p>
              <a href="mailto:<?php echo $site_email; ?>" class="fw-bold text-primary text-decoration-none">
                <i class="fa fa-paper-plane me-1"></i> <?php echo $site_email; ?>
              </a>
            </div>

          </div>

        </div>
      </div>

      <!-- Quick Navigation Sidebar Column -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white sticky-top" style="top: 20px;">
          <h5 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="fa fa-compass text-primary me-2"></i>Quick Navigation</h5>
          <div class="list-group list-group-flush">
            <a href="aboutus.php" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 border-0 rounded-3 mb-1">
              <span><i class="fa fa-info-circle me-2 text-success"></i> About Us</span>
              <i class="fa fa-chevron-right small text-muted"></i>
            </a>
            <a href="disclaimer.php" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 border-0 rounded-3 mb-1">
              <span><i class="fa fa-exclamation-triangle me-2 text-warning"></i> Disclaimer</span>
              <i class="fa fa-chevron-right small text-muted"></i>
            </a>
            <a href="contactus.php" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 border-0 rounded-3">
              <span><i class="fa fa-envelope me-2 text-primary"></i> Contact Us</span>
              <i class="fa fa-chevron-right small text-muted"></i>
            </a>
          </div>
        </div>
      </div>

    </div>

  </div>
</main>

<style>
.bg-primary-soft {
  background-color: rgba(30, 144, 255, 0.1);
}
</style>

<?php include_once "footer.php"; ?>