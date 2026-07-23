<?php 
include_once "header.php"; 

$site_name = htmlspecialchars($settings['websitename'] ?? 'Our News Portal');
$site_email = htmlspecialchars($settings['workEmail'] ?? 'contact@domain.com');
$site_url = htmlspecialchars($settings['websiteUrl'] ?? '#');
?>

<main class="py-5 bg-light">
  <div class="container">

    <!-- Header Section -->
    <div class="text-center mb-5">
      <span class="badge bg-warning-soft text-warning px-3 py-2 rounded-pill fw-semibold mb-2" style="background-color: rgba(255, 193, 7, 0.15);">
        <i class="fa fa-exclamation-triangle me-1"></i> Legal &amp; Compliance
      </span>
      <h1 class="display-6 fw-bold text-dark mb-2">Disclaimer Notice</h1>
      <p class="text-muted mx-auto" style="max-width: 600px;">
        Terms of content usage, accuracy guarantees, third-party link policies, and liability terms for <strong><?php echo $site_name; ?></strong>.
      </p>
    </div>

    <div class="row g-4">
      <!-- Main Content Column -->
      <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 bg-white">
          
          <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 1.25rem;">
              <i class="fa fa-file-text"></i>
            </div>
            <div>
              <h3 class="h5 fw-bold text-dark mb-1">Official Disclaimer Statement</h3>
              <small class="text-muted">Effective Date: <?php echo date('F d, Y'); ?> | Domain: <?php echo parse_url($site_url, PHP_URL_HOST) ?: $site_name; ?></small>
            </div>
          </div>

          <div class="disclaimer-items">
            
            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-circle">1</span> Welcome &amp; Scope
              </h4>
              <p class="text-muted small ps-4 mb-0">
                Welcome to <strong><?php echo $site_name; ?></strong>. We provide breaking news, journalistic coverage, and general updates. By accessing or using this website, you accept and agree to be bound by the terms of this disclaimer.
              </p>
            </div>

            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-circle">2</span> Accuracy of Information
              </h4>
              <p class="text-muted small ps-4 mb-0">
                While <strong><?php echo $site_name; ?></strong> strives to keep information reliable, timely, and correct, we make no representations or warranties of any kind—express or implied—about the completeness, accuracy, or suitability of news items, graphics, or services contained on the site.
              </p>
            </div>

            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-circle">3</span> Third-Party Links &amp; Content
              </h4>
              <p class="text-muted small ps-4 mb-0">
                Our articles may contain links to external third-party websites or services. <strong><?php echo $site_name; ?></strong> has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third-party sites.
              </p>
            </div>

            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-circle">4</span> Editorial Opinions &amp; Columnists
              </h4>
              <p class="text-muted small ps-4 mb-0">
                Opinions expressed in columns, editorials, user comments, or guest contributions are solely those of the individual authors and do not necessarily reflect the official stance or policy of <strong><?php echo $site_name; ?></strong>.
              </p>
            </div>

            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-circle">5</span> User-Generated Comments
              </h4>
              <p class="text-muted small ps-4 mb-0">
                Users may post comments or feedback. <strong><?php echo $site_name; ?></strong> reserves the right to moderate, edit, or remove any content deemed objectionable, defamatory, or unlawful, but bears no liability for user postings.
              </p>
            </div>

            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-circle">6</span> Limitation of Liability
              </h4>
              <p class="text-muted small ps-4 mb-0">
                Under no circumstances shall <strong><?php echo $site_name; ?></strong> or its owners be liable for any direct, indirect, incidental, or consequential loss or damage arising out of your use or inability to use this platform.
              </p>
            </div>

            <div class="mb-4">
              <h4 class="h6 fw-bold text-dark d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-circle">7</span> Policy Modifications
              </h4>
              <p class="text-muted small ps-4 mb-0">
                We reserve the right to amend this disclaimer at any time without prior notice. Continued usage of the site signifies your agreement with updated terms.
              </p>
            </div>

            <div class="p-3 bg-light rounded-3 border">
              <h4 class="h6 fw-bold text-dark mb-1"><i class="fa fa-envelope text-primary me-2"></i>Questions or Copyright Inquiries?</h4>
              <p class="text-muted small mb-2">
                If you have questions regarding content removal, copyright notices, or press inquiries, please email us directly:
              </p>
              <a href="mailto:<?php echo $site_email; ?>" class="fw-bold text-primary text-decoration-none">
                <i class="fa fa-paper-plane me-1"></i> <?php echo $site_email; ?>
              </a>
            </div>

          </div>

        </div>
      </div>

      <!-- Sticky Sidebar Column -->
      <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white sticky-top" style="top: 20px;">
          <h5 class="fw-bold text-dark mb-3 border-bottom pb-2"><i class="fa fa-compass text-primary me-2"></i>Legal Navigation</h5>
          <div class="list-group list-group-flush">
            <a href="privacy-policy.php" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 border-0 rounded-3 mb-1">
              <span><i class="fa fa-lock me-2 text-primary"></i> Privacy Policy</span>
              <i class="fa fa-chevron-right small text-muted"></i>
            </a>
            <a href="aboutus.php" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 border-0 rounded-3 mb-1">
              <span><i class="fa fa-info-circle me-2 text-success"></i> About Us</span>
              <i class="fa fa-chevron-right small text-muted"></i>
            </a>
            <a href="contactus.php" class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 border-0 rounded-3">
              <span><i class="fa fa-envelope me-2 text-warning"></i> Contact Us</span>
              <i class="fa fa-chevron-right small text-muted"></i>
            </a>
          </div>
        </div>
      </div>

    </div>

  </div>
</main>

<?php include_once "footer.php"; ?>