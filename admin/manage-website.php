<?php
include_once "_header.php";
include_once "_subHeader.php";

// Authorization check: Only Super Admin (role 1)
if (!isset($_SESSION['role']) || (int) $_SESSION['role'] !== 1) {
    echo "<script>window.location.href='dashboard.php';</script>";
    exit();
}

// Ensure CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Re-fetch latest settings
$sQ = mysqli_query($conn, "SELECT * FROM `settings` LIMIT 1");
$s = $sQ ? mysqli_fetch_assoc($sQ) : [];

// Determine active tab from URL query param
$activeTab = htmlspecialchars($_GET['tab'] ?? 'general');
$validTabs = ['general', 'branding', 'seo', 'smtp', 'custom'];
if (!in_array($activeTab, $validTabs, true)) {
    $activeTab = 'general';
}
?>

<!-- Content wrapper -->
<div class="content-wrapper">
    <!-- Content -->
    <div class="container-xxl flex-grow-1 container-p-y">

        <!-- Page Header -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h4 class="fw-bold py-1 mb-1"><span class="text-muted fw-light">System /</span> Settings Manager</h4>
                <p class="text-muted mb-0">Dynamically configure website identity, branding, SEO, email SMTP, and maintenance mode.</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <?php if (!empty($s['maintenanceMode'])): ?>
                    <span class="badge bg-danger p-2"><i class="bx bx-error-circle me-1"></i> Maintenance Active</span>
                <?php else: ?>
                    <span class="badge bg-success p-2"><i class="bx bx-check-circle me-1"></i> System Online</span>
                <?php endif; ?>
                <a href="<?php echo htmlspecialchars($s['websiteUrl'] ?? '../'); ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="bx bx-link-external me-1"></i> View Website
                </a>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <ul class="nav nav-pills flex-column flex-md-row mb-4" id="systemSettingsTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link <?php echo ($activeTab === 'general') ? 'active' : ''; ?>" data-bs-toggle="pill" data-bs-target="#tab-general" type="button" role="tab">
                    <i class="bx bx-slider-alt me-1"></i> General &amp; Contact
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link <?php echo ($activeTab === 'branding') ? 'active' : ''; ?>" data-bs-toggle="pill" data-bs-target="#tab-branding" type="button" role="tab">
                    <i class="bx bx-image me-1"></i> Branding &amp; Media
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link <?php echo ($activeTab === 'seo') ? 'active' : ''; ?>" data-bs-toggle="pill" data-bs-target="#tab-seo" type="button" role="tab">
                    <i class="bx bx-search-alt me-1"></i> SEO &amp; Social
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link <?php echo ($activeTab === 'smtp') ? 'active' : ''; ?>" data-bs-toggle="pill" data-bs-target="#tab-smtp" type="button" role="tab">
                    <i class="bx bx-envelope me-1"></i> Email &amp; SMTP
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link <?php echo ($activeTab === 'custom') ? 'active' : ''; ?>" data-bs-toggle="pill" data-bs-target="#tab-custom" type="button" role="tab">
                    <i class="bx bx-code-block me-1"></i> Analytics &amp; Scripts
                </button>
            </li>
        </ul>

        <!-- Tab Contents -->
        <div class="tab-content p-0" id="systemSettingsContent">

            <!-- TAB 1: GENERAL & CONTACT SETTINGS -->
            <div class="tab-pane fade <?php echo ($activeTab === 'general') ? 'show active' : ''; ?>" id="tab-general" role="tabpanel">
                <div class="card mb-4">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bx bx-globe me-2 text-primary"></i>Website Identity &amp; Contact Information</h5>
                        <span class="badge bg-label-primary">Core Configuration</span>
                    </div>
                    <div class="card-body py-4">
                        <form action="app/app.php" method="POST">
                            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="webName">Website Name <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-bookmark"></i></span>
                                        <input type="text" class="form-control" id="webName" name="webName" value="<?php echo htmlspecialchars($s['websitename'] ?? ''); ?>" required placeholder="e.g. Daily Chronicle News">
                                    </div>
                                    <div class="form-text">Display brand name used throughout the website.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="webTitle">Site Tagline / Default Title</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-heading"></i></span>
                                        <input type="text" class="form-control" id="webTitle" name="webTitle" value="<?php echo htmlspecialchars($s['websiteTitle'] ?? ''); ?>" placeholder="e.g. Latest Breaking News, Articles &amp; Analysis">
                                    </div>
                                    <div class="form-text">Tagline appended to page titles and home header.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="webUrl">Website Base URL <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-link"></i></span>
                                        <input type="url" class="form-control" id="webUrl" name="webUrl" value="<?php echo htmlspecialchars($s['websiteUrl'] ?? ''); ?>" required placeholder="http://127.0.0.1/PHP-News-Website">
                                    </div>
                                    <div class="form-text">Root URL without trailing slash (used for assets and canonical links).</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="webEmail">Official Contact Email <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                        <input type="email" class="form-control" id="webEmail" name="webEmail" value="<?php echo htmlspecialchars($s['workEmail'] ?? ''); ?>" required placeholder="contact@yourwebsite.com">
                                    </div>
                                    <div class="form-text">Official recipient email for reader inquiries and alerts.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="contactPhone">Editorial Phone / WhatsApp</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-phone"></i></span>
                                        <input type="text" class="form-control" id="contactPhone" name="contactPhone" value="<?php echo htmlspecialchars($s['contactPhone'] ?? ''); ?>" placeholder="+91 9876543210">
                                    </div>
                                    <div class="form-text">Public phone number shown on the Contact Us page.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold" for="contactAddress">Office / Newsroom Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bx bx-map-pin"></i></span>
                                        <input type="text" class="form-control" id="contactAddress" name="contactAddress" value="<?php echo htmlspecialchars($s['contactAddress'] ?? ''); ?>" placeholder="e.g. 402 Press Enclave, New Delhi, India">
                                    </div>
                                    <div class="form-text">Displayed on the Contact Us info card.</div>
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-semibold" for="webFooter">Footer Notice &amp; Copyright</label>
                                    <textarea class="form-control" id="webFooter" name="webFooter" rows="2" placeholder="e.g. All Rights Reserved. ConnectBihar.in"><?php echo htmlspecialchars($s['footerdesc'] ?? ''); ?></textarea>
                                    <div class="form-text">Displayed in the bottom footer row on every public page.</div>
                                </div>
                            </div>

                            <!-- Maintenance Mode Section -->
                            <div class="border rounded-3 p-3 mt-4 bg-light">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark"><i class="bx bx-traffic-cone me-2 text-warning"></i>Maintenance Mode</h6>
                                        <small class="text-muted">Temporarily display a maintenance notice to public visitors while allowing administrators full access.</small>
                                    </div>
                                    <div class="form-check form-switch form-switch-lg">
                                        <input class="form-check-input" type="checkbox" id="maintenanceMode" name="maintenanceMode" value="1" <?php echo (!empty($s['maintenanceMode'])) ? 'checked' : ''; ?>>
                                    </div>
                                </div>

                                <div class="mb-2">
                                    <label class="form-label fw-semibold" for="maintenanceMsg">Custom Maintenance Message</label>
                                    <textarea class="form-control" id="maintenanceMsg" name="maintenanceMsg" rows="2" placeholder="We are currently upgrading our systems. Please check back shortly."><?php echo htmlspecialchars($s['maintenanceMsg'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                <button type="submit" name="updateGeneralSettings" class="btn btn-primary px-4">
                                    <i class="bx bx-save me-1"></i> Save General Settings
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- TAB 2: BRANDING & MEDIA ASSETS -->
            <div class="tab-pane fade <?php echo ($activeTab === 'branding') ? 'show active' : ''; ?>" id="tab-branding" role="tabpanel">
                <div class="row g-4">
                    <!-- Website Logo Card -->
                    <div class="col-lg-4">
                        <div class="card h-100 shadow-sm border">
                            <div class="card-header border-bottom py-3">
                                <h6 class="mb-0 fw-bold"><i class="bx bx-image-alt me-2 text-primary"></i>Website Main Logo</h6>
                            </div>
                            <div class="card-body text-center d-flex flex-column justify-content-between py-4">
                                <div>
                                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-center mb-3" style="min-height: 140px; border: 2px dashed #cbd5e1;">
                                        <img id="logoPreview" class="img-fluid" src="../assets/images/<?php echo htmlspecialchars($s['logo'] ?? ''); ?>" alt="Logo Preview" style="max-height: 90px; max-width: 100%; object-fit: contain;">
                                    </div>
                                    <span class="badge bg-label-info mb-3">Recommended: 250×60px (PNG, WEBP, SVG)</span>
                                    <p class="text-muted small">Displayed on public header, admin navbar, and branding links.</p>
                                </div>
                                <form action="app/app.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <div class="mb-3">
                                        <input type="file" class="form-control form-control-sm image-file-input" name="webLogo" id="inputLogo" accept=".png, .jpg, .jpeg, .webp, .svg" data-preview="#logoPreview" required>
                                    </div>
                                    <button type="submit" name="webLogobtn" class="btn btn-primary w-100 btn-sm">
                                        <i class="bx bx-upload me-1"></i> Upload New Logo
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Website Favicon Card -->
                    <div class="col-lg-4">
                        <div class="card h-100 shadow-sm border">
                            <div class="card-header border-bottom py-3">
                                <h6 class="mb-0 fw-bold"><i class="bx bx-bookmark-alt me-2 text-primary"></i>Browser Favicon Icon</h6>
                            </div>
                            <div class="card-body text-center d-flex flex-column justify-content-between py-4">
                                <div>
                                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-center mb-3" style="min-height: 140px; border: 2px dashed #cbd5e1;">
                                        <img id="faviconPreview" class="img-fluid" src="../assets/images/<?php echo htmlspecialchars($s['favicon'] ?? ''); ?>" alt="Favicon Preview" style="max-height: 64px; max-width: 64px; object-fit: contain;">
                                    </div>
                                    <span class="badge bg-label-info mb-3">Recommended: 32×32px or 64×64px (ICO, PNG)</span>
                                    <p class="text-muted small">Icon displayed in browser tabs, bookmarks, and mobile shortcuts.</p>
                                </div>
                                <form action="app/app.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <div class="mb-3">
                                        <input type="file" class="form-control form-control-sm image-file-input" name="webfavicon" id="inputFavicon" accept=".ico, .png, .jpg, .jpeg, .webp" data-preview="#faviconPreview" required>
                                    </div>
                                    <button type="submit" name="webfaviconbtn" class="btn btn-primary w-100 btn-sm">
                                        <i class="bx bx-upload me-1"></i> Upload New Favicon
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Watermark Logo Card -->
                    <div class="col-lg-4">
                        <div class="card h-100 shadow-sm border">
                            <div class="card-header border-bottom py-3">
                                <h6 class="mb-0 fw-bold"><i class="bx bx-shield-quarter me-2 text-primary"></i>Watermark / Stamp</h6>
                            </div>
                            <div class="card-body text-center d-flex flex-column justify-content-between py-4">
                                <div>
                                    <div class="p-3 bg-light rounded-3 d-flex align-items-center justify-content-center mb-3" style="min-height: 140px; border: 2px dashed #cbd5e1;">
                                        <img id="watermarkPreview" class="img-fluid" src="../assets/images/<?php echo htmlspecialchars($s['watterMark'] ?? ''); ?>" alt="Watermark Preview" style="max-height: 90px; max-width: 100%; object-fit: contain;">
                                    </div>
                                    <span class="badge bg-label-info mb-3">Recommended: Transparent PNG (approx. 180×180px)</span>
                                    <p class="text-muted small">Overlay watermark used on media and article post imagery.</p>
                                </div>
                                <form action="app/app.php" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                    <div class="mb-3">
                                        <input type="file" class="form-control form-control-sm image-file-input" name="webwatterMark" id="inputWatermark" accept=".png, .webp" data-preview="#watermarkPreview" required>
                                    </div>
                                    <button type="submit" name="webwattermarkbtn" class="btn btn-primary w-100 btn-sm">
                                        <i class="bx bx-upload me-1"></i> Upload Watermark
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: SEO & SOCIAL MEDIA -->
            <div class="tab-pane fade <?php echo ($activeTab === 'seo') ? 'show active' : ''; ?>" id="tab-seo" role="tabpanel">
                <form action="app/app.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <div class="row g-4">
                        <!-- Search Engine Optimization Card -->
                        <div class="col-lg-6">
                            <div class="card h-100 border">
                                <div class="card-header border-bottom py-3">
                                    <h5 class="mb-0"><i class="bx bx-search-alt me-2 text-primary"></i>Metadata &amp; Search Crawlers</h5>
                                </div>
                                <div class="card-body py-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="metaDescription">Default Meta Description</label>
                                        <textarea class="form-control" id="metaDescription" name="metaDescription" rows="3" maxlength="320" placeholder="A concise 150-160 character description of your news portal..."><?php echo htmlspecialchars($s['metaDescription'] ?? ''); ?></textarea>
                                        <div class="d-flex justify-content-between form-text">
                                            <span>Shown in Google search engine snippets.</span>
                                            <span id="metaDescCount">0/320</span>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="webKeyword">Meta Keywords</label>
                                        <textarea class="form-control" id="webKeyword" name="webKeyword" rows="3" placeholder="News, Breaking News, Politics, Sports, Jobs, Tech, Updates..."><?php echo htmlspecialchars($s['keywords'] ?? ''); ?></textarea>
                                        <div class="form-text">Comma-separated keywords for search engine indexing.</div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold" for="metaAuthor">Default Author / Publisher</label>
                                            <input type="text" class="form-control" id="metaAuthor" name="metaAuthor" value="<?php echo htmlspecialchars($s['metaAuthor'] ?? 'Editorial Team'); ?>" placeholder="e.g. Editorial Board">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold" for="robotsIndex">Robots Crawling Directive</label>
                                            <select class="form-select" id="robotsIndex" name="robotsIndex">
                                                <?php
                                                $robots = $s['robotsIndex'] ?? 'index, follow';
                                                $robotOptions = [
                                                    'index, follow'     => 'Index, Follow (Recommended)',
                                                    'noindex, follow'   => 'No-Index, Follow',
                                                    'index, nofollow'   => 'Index, No-Follow',
                                                    'noindex, nofollow' => 'No-Index, No-Follow (Staging/Private)'
                                                ];
                                                foreach ($robotOptions as $val => $label) {
                                                    $sel = ($robots === $val) ? 'selected' : '';
                                                    echo "<option value=\"{$val}\" {$sel}>{$label}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media Profiles Card -->
                        <div class="col-lg-6">
                            <div class="card h-100 border">
                                <div class="card-header border-bottom py-3">
                                    <h5 class="mb-0"><i class="bx bxl-facebook-circle me-2 text-primary"></i>Social Media Channel Links</h5>
                                </div>
                                <div class="card-body py-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="socialFacebook">Facebook Page URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bxl-facebook"></i></span>
                                            <input type="url" class="form-control" id="socialFacebook" name="socialFacebook" value="<?php echo htmlspecialchars($s['socialFacebook'] ?? ''); ?>" placeholder="https://facebook.com/yourbrand">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="socialTwitter">Twitter / X URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bxl-twitter"></i></span>
                                            <input type="url" class="form-control" id="socialTwitter" name="socialTwitter" value="<?php echo htmlspecialchars($s['socialTwitter'] ?? ''); ?>" placeholder="https://x.com/yourhandle">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="socialInstagram">Instagram Profile URL</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bxl-instagram"></i></span>
                                            <input type="url" class="form-control" id="socialInstagram" name="socialInstagram" value="<?php echo htmlspecialchars($s['socialInstagram'] ?? ''); ?>" placeholder="https://instagram.com/yourchannel">
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="socialLinkedin">LinkedIn Company / Page</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bxl-linkedin"></i></span>
                                            <input type="url" class="form-control" id="socialLinkedin" name="socialLinkedin" value="<?php echo htmlspecialchars($s['socialLinkedin'] ?? ''); ?>" placeholder="https://linkedin.com/company/yourportal">
                                        </div>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold" for="socialYoutube">YouTube Channel</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bxl-youtube"></i></span>
                                                <input type="url" class="form-control" id="socialYoutube" name="socialYoutube" value="<?php echo htmlspecialchars($s['socialYoutube'] ?? ''); ?>" placeholder="https://youtube.com/@channel">
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label fw-semibold" for="socialWhatsapp">WhatsApp Channel / Group</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bxl-whatsapp"></i></span>
                                                <input type="url" class="form-control" id="socialWhatsapp" name="socialWhatsapp" value="<?php echo htmlspecialchars($s['socialWhatsapp'] ?? ''); ?>" placeholder="https://chat.whatsapp.com/...">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <button type="submit" name="updateSeoSettings" class="btn btn-primary px-4">
                                <i class="bx bx-save me-1"></i> Save SEO &amp; Social Links
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- TAB 4: EMAIL & SMTP CONFIGURATION -->
            <div class="tab-pane fade <?php echo ($activeTab === 'smtp') ? 'show active' : ''; ?>" id="tab-smtp" role="tabpanel">
                <div class="row g-4">
                    <!-- SMTP Server Settings -->
                    <div class="col-lg-8">
                        <div class="card border h-100">
                            <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bx bx-server me-2 text-primary"></i>SMTP Server Configuration</h5>
                                <span class="badge bg-label-info">TLS / SSL Secure</span>
                            </div>
                            <div class="card-body py-4">
                                <form action="app/app.php" method="POST" id="smtpSettingsForm">
                                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" for="mailDriver">Mail Delivery Driver</label>
                                            <select class="form-select" id="mailDriver" name="mailDriver">
                                                <option value="smtp" <?php echo (($s['mailDriver'] ?? 'mail') === 'smtp') ? 'selected' : ''; ?>>SMTP Server (Recommended)</option>
                                                <option value="mail" <?php echo (($s['mailDriver'] ?? 'mail') === 'mail') ? 'selected' : ''; ?>>PHP Native mail()</option>
                                            </select>
                                            <div class="form-text">Choose SMTP for reliable delivery without spam flagging.</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" for="smtpEncryption">Encryption Type</label>
                                            <select class="form-select" id="smtpEncryption" name="smtpEncryption">
                                                <option value="tls" <?php echo (($s['smtpEncryption'] ?? 'tls') === 'tls') ? 'selected' : ''; ?>>TLS (STARTTLS - Port 587)</option>
                                                <option value="ssl" <?php echo (($s['smtpEncryption'] ?? 'tls') === 'ssl') ? 'selected' : ''; ?>>SSL (Direct SMTPS - Port 465)</option>
                                                <option value="none" <?php echo (($s['smtpEncryption'] ?? 'tls') === 'none') ? 'selected' : ''; ?>>None (Port 25 - Not Secure)</option>
                                            </select>
                                        </div>

                                        <div class="col-md-8">
                                            <label class="form-label fw-semibold" for="smtpHost">SMTP Host Server</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-hdd"></i></span>
                                                <input type="text" class="form-control" id="smtpHost" name="smtpHost" value="<?php echo htmlspecialchars($s['smtpHost'] ?? ''); ?>" placeholder="e.g. smtp.gmail.com or smtp.mailgun.org">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label fw-semibold" for="smtpPort">SMTP Port</label>
                                            <input type="number" class="form-control" id="smtpPort" name="smtpPort" value="<?php echo htmlspecialchars($s['smtpPort'] ?? '587'); ?>" placeholder="587">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" for="smtpUser">SMTP Username / Email</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-user"></i></span>
                                                <input type="text" class="form-control" id="smtpUser" name="smtpUser" value="<?php echo htmlspecialchars($s['smtpUser'] ?? ''); ?>" placeholder="your-email@gmail.com">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" for="smtpPass">SMTP Password / App Password</label>
                                            <div class="input-group input-group-merge">
                                                <input type="password" class="form-control" id="smtpPass" name="smtpPass" placeholder="<?php echo !empty($s['smtpPass']) ? '•••••••• (leave blank to keep current)' : 'Enter SMTP password'; ?>">
                                                <span class="input-group-text cursor-pointer" id="togglePasswordBtn"><i class="bx bx-hide" id="toggleIcon"></i></span>
                                            </div>
                                            <div class="form-text">For Gmail, use a 16-character App Password.</div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" for="smtpFromEmail">Sender / "From" Email</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-envelope"></i></span>
                                                <input type="email" class="form-control" id="smtpFromEmail" name="smtpFromEmail" value="<?php echo htmlspecialchars($s['smtpFromEmail'] ?? ($s['workEmail'] ?? '')); ?>" placeholder="noreply@yourdomain.com">
                                            </div>
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label fw-semibold" for="smtpFromName">Sender / "From" Name</label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bx bx-id-card"></i></span>
                                                <input type="text" class="form-control" id="smtpFromName" name="smtpFromName" value="<?php echo htmlspecialchars($s['smtpFromName'] ?? ($s['websitename'] ?? 'News Portal')); ?>" placeholder="News Desk">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-4 text-end">
                                        <button type="submit" name="updateSmtpSettings" class="btn btn-primary px-4">
                                            <i class="bx bx-save me-1"></i> Save SMTP Configuration
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Live SMTP Test Card -->
                    <div class="col-lg-4">
                        <div class="card border h-100 shadow-sm">
                            <div class="card-header border-bottom py-3 bg-light">
                                <h6 class="mb-0 fw-bold"><i class="bx bx-send me-2 text-success"></i>Live SMTP Test Tool</h6>
                            </div>
                            <div class="card-body py-4 d-flex flex-column justify-content-between">
                                <div>
                                    <p class="small text-muted mb-3">
                                        Send a live test message to verify socket handshake, authentication, and inbox delivery.
                                    </p>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="testEmailRecipient">Recipient Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bx-at"></i></span>
                                            <input type="email" class="form-control" id="testEmailRecipient" placeholder="your-email@example.com" value="<?php echo htmlspecialchars($_SESSION['email'] ?? ($s['workEmail'] ?? '')); ?>">
                                        </div>
                                    </div>

                                    <button type="button" class="btn btn-success w-100 mb-3" id="btnRunEmailTest">
                                        <i class="bx bx-paper-plane me-1"></i> Send Test Email
                                    </button>

                                    <div id="testResultContainer" style="display: none;">
                                        <div class="alert p-3 mb-2" id="testResultAlert"></div>
                                        <div class="accordion" id="accordionLogs">
                                            <div class="accordion-item border">
                                                <h2 class="accordion-header">
                                                    <button class="accordion-button collapsed py-2 px-3 small" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLogs">
                                                        <i class="bx bx-terminal me-2"></i> Connection Logs
                                                    </button>
                                                </h2>
                                                <div id="collapseLogs" class="accordion-collapse collapse">
                                                    <div class="accordion-body p-2 bg-dark text-light rounded-bottom" style="font-family: monospace; font-size: 11px; max-height: 220px; overflow-y: auto;" id="testLogsContainer">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-info py-2 px-3 small mb-0 mt-3">
                                    <i class="bx bx-bulb me-1"></i> <strong>Tip:</strong> If using Gmail, make sure to enable 2FA and generate a 16-digit App Password under your Google Account security settings.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: ANALYTICS & CUSTOM SCRIPTS -->
            <div class="tab-pane fade <?php echo ($activeTab === 'custom') ? 'show active' : ''; ?>" id="tab-custom" role="tabpanel">
                <form action="app/app.php" method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                    <div class="row g-4">
                        <!-- Integrations Card -->
                        <div class="col-lg-6">
                            <div class="card h-100 border">
                                <div class="card-header border-bottom py-3">
                                    <h5 class="mb-0"><i class="bx bx-line-chart me-2 text-primary"></i>Google Services &amp; Monetization</h5>
                                </div>
                                <div class="card-body py-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="googleAnalytics">Google Analytics Measurement ID / GA4 Tag</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bx-bar-chart"></i></span>
                                            <input type="text" class="form-control" id="googleAnalytics" name="googleAnalytics" value="<?php echo htmlspecialchars($s['googleAnalytics'] ?? ''); ?>" placeholder="e.g. G-XXXXXXXXXX or UA-XXXXXXXXX-X">
                                        </div>
                                        <div class="form-text">Automatically generates and injects the global gtag.js tracker on all public pages.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="googleAdsense">Google AdSense Publisher ID</label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="bx bx-dollar-circle"></i></span>
                                            <input type="text" class="form-control" id="googleAdsense" name="googleAdsense" value="<?php echo htmlspecialchars($s['googleAdsense'] ?? ''); ?>" placeholder="e.g. ca-pub-8896362105504152">
                                        </div>
                                        <div class="form-text">Injects the AdSense crawler verification meta tag and script into &lt;head&gt;.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Custom Header & Footer Code -->
                        <div class="col-lg-6">
                            <div class="card h-100 border">
                                <div class="card-header border-bottom py-3">
                                    <h5 class="mb-0"><i class="bx bx-code-curly me-2 text-primary"></i>Custom Code Injection</h5>
                                </div>
                                <div class="card-body py-4">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="customHeadCode">Custom &lt;head&gt; Code</label>
                                        <textarea class="form-control font-monospace" id="customHeadCode" name="customHeadCode" rows="4" style="font-size: 12px;" placeholder="<!-- Custom meta tags, external CSS links, or verification tags -->"><?php echo htmlspecialchars($s['customHeadCode'] ?? ''); ?></textarea>
                                        <div class="form-text">Rendered inside &lt;head&gt; before stylesheets.</div>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label fw-semibold" for="customFooterCode">Custom Footer / &lt;body&gt; Code</label>
                                        <textarea class="form-control font-monospace" id="customFooterCode" name="customFooterCode" rows="4" style="font-size: 12px;" placeholder="<!-- Live chat scripts, pixel trackers, or custom JavaScript -->"><?php echo htmlspecialchars($s['customFooterCode'] ?? ''); ?></textarea>
                                        <div class="form-text">Rendered right before the closing &lt;/body&gt; tag.</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 text-end">
                            <button type="submit" name="updateCustomCode" class="btn btn-primary px-4">
                                <i class="bx bx-save me-1"></i> Save Custom Code &amp; Analytics
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
</div>

<!-- Dynamic Client Scripts -->
<script>
    $(document).ready(function() {
        // Image preview on file selection
        $('.image-file-input').on('change', function() {
            var input = this;
            var targetPreview = $(this).data('preview');
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $(targetPreview).attr('src', e.target.result);
                };
                reader.readAsDataURL(input.files[0]);
            }
        });

        // Meta description character counter
        function updateMetaCounter() {
            var len = $('#metaDescription').val().length;
            $('#metaDescCount').text(len + '/320');
        }
        $('#metaDescription').on('input', updateMetaCounter);
        updateMetaCounter();

        // Password Show/Hide Toggle
        $('#togglePasswordBtn').on('click', function() {
            var passInput = $('#smtpPass');
            var icon = $('#toggleIcon');
            if (passInput.attr('type') === 'password') {
                passInput.attr('type', 'text');
                icon.removeClass('bx-hide').addClass('bx-show');
            } else {
                passInput.attr('type', 'password');
                icon.removeClass('bx-show').addClass('bx-hide');
            }
        });

        // Live Test Email AJAX execution
        $('#btnRunEmailTest').on('click', function(e) {
            e.preventDefault();
            var btn = $(this);
            var recipient = $('#testEmailRecipient').val().trim();

            if (!recipient) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Recipient Required',
                    text: 'Please enter a valid recipient email address.'
                });
                return;
            }

            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Connecting &amp; Sending...');
            $('#testResultContainer').slideUp();

            // Collect current form values for real-time testing
            var payload = {
                csrf_token: '<?php echo $csrf_token; ?>',
                test_email: recipient,
                mailDriver: $('#mailDriver').val(),
                smtpHost: $('#smtpHost').val(),
                smtpPort: $('#smtpPort').val(),
                smtpUser: $('#smtpUser').val(),
                smtpPass: $('#smtpPass').val(),
                smtpEncryption: $('#smtpEncryption').val(),
                smtpFromEmail: $('#smtpFromEmail').val(),
                smtpFromName: $('#smtpFromName').val()
            };

            $.ajax({
                url: 'app/test-mail.php',
                type: 'POST',
                data: payload,
                dataType: 'json',
                success: function(resp) {
                    btn.prop('disabled', false).html('<i class="bx bx-paper-plane me-1"></i> Send Test Email');
                    $('#testResultContainer').slideDown();

                    var alertBox = $('#testResultAlert');
                    var logsBox = $('#testLogsContainer');

                    if (resp.success) {
                        alertBox.removeClass('alert-danger').addClass('alert-success')
                                .html('<strong><i class="bx bx-check-circle me-1"></i> Success!</strong> ' + resp.message);
                        Swal.fire({
                            icon: 'success',
                            title: 'Email Sent!',
                            text: resp.message
                        });
                    } else {
                        alertBox.removeClass('alert-success').addClass('alert-danger')
                                .html('<strong><i class="bx bx-error-circle me-1"></i> Delivery Failed:</strong> ' + resp.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'SMTP Error',
                            text: resp.message
                        });
                    }

                    // Render logs
                    if (resp.logs && resp.logs.length > 0) {
                        logsBox.html(resp.logs.map(function(line) {
                            return '<div>' + $('<div>').text(line).html() + '</div>';
                        }).join(''));
                    } else {
                        logsBox.html('<div>No log entries recorded.</div>');
                    }
                },
                error: function(xhr, status, error) {
                    btn.prop('disabled', false).html('<i class="bx bx-paper-plane me-1"></i> Send Test Email');
                    $('#testResultContainer').slideDown();
                    $('#testResultAlert').removeClass('alert-success').addClass('alert-danger')
                        .html('<strong><i class="bx bx-x-circle me-1"></i> AJAX Request Error:</strong> ' + error);
                    $('#testLogsContainer').text(xhr.responseText || 'Server returned an invalid response.');
                }
            });
        });

        // Sync tab clicks with URL query param so refresh stays on active tab
        $('button[data-bs-toggle="pill"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr('data-bs-target').replace('#tab-', '');
            if (history.pushState) {
                var newurl = window.location.protocol + "//" + window.location.host + window.location.pathname + '?tab=' + target;
                window.history.pushState({path: newurl}, '', newurl);
            }
        });
    });
</script>

<?php include_once "_footer.php"; ?>