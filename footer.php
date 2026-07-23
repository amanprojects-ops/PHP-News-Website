<?php
// Fallback if settings or conn is not set
if (!isset($settings) || !isset($conn)) {
    include_once ('config.php');
}
$footer_desc = $settings['footerdesc'] ?? '';
?>

<style>
  /* Premium Footer Redesign */
  #footer {
    background: linear-gradient(135deg, #111827 0%, #1f2937 100%);
    color: #9ca3af;
    padding: 40px 0 25px 0;
    border-top: 3px solid #1e90ff;
    font-family: 'Outfit', 'Inter', sans-serif;
    font-size: 14px;
    position: relative;
    overflow: hidden;
  }

  /* Subtle background design element */
  #footer::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(30, 144, 255, 0.05) 0%, transparent 70%);
    pointer-events: none;
  }

  #footer .footer-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    position: relative;
    z-index: 1;
  }

  #footer .footer-logo {
    font-size: 22px;
    font-weight: 700;
    color: #ffffff;
    margin-bottom: 12px;
    letter-spacing: 0.5px;
  }

  #footer .footer-logo span {
    color: #1e90ff;
  }

  #footer .footer-links {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
    margin-bottom: 24px;
    margin-top: 10px;
  }

  #footer .footer-links a {
    color: #d1d5db;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    padding: 8px 18px;
    border-radius: 20px;
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    font-size: 13px;
  }

  #footer .footer-links a:hover {
    color: #ffffff;
    background: rgba(30, 144, 255, 0.12);
    border-color: rgba(30, 144, 255, 0.3);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(30, 144, 255, 0.08);
  }

  #footer .footer-divider {
    width: 100%;
    max-width: 800px;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.08), transparent);
    margin: 10px 0 20px 0;
  }

  #footer .copyright {
    font-size: 12px;
    color: #6b7280;
    letter-spacing: 0.3px;
    line-height: 1.5;
  }

  #footer .copyright a {
    color: #1e90ff;
    text-decoration: none;
    transition: color 0.2s ease;
    font-weight: 500;
  }

  #footer .copyright a:hover {
    color: #60a5fa;
    text-decoration: underline;
  }
</style>

<div id="footer">
  <div class="container">
    <div class="footer-content">
      <!-- Footer Logo / Website Title -->
      <div class="footer-logo">
        <?php echo htmlspecialchars($settings['websitename'] ?? 'News Website'); ?>
      </div>
      
      <!-- Footer Navigation Links -->
      <div class="footer-links">
        <a href="./contactus.php">Contact Us</a>
        <a href="./aboutus.php">About Us</a>
        <a href="./disclaimer.php">Disclaimer</a>
        <a href="./privacy-policy.php">Privacy Policy</a>
      </div>

      <!-- Divider -->
      <div class="footer-divider"></div>

      <!-- Copyright Details -->
      <div class="copyright">
        &copy; 2019 - <?php echo date('Y'); ?> 
        <a href="/"><?php echo htmlspecialchars($settings['websitename'] ?? 'News Portal'); ?></a>. 
        <?php echo htmlspecialchars($footer_desc); ?> Build With 💕 <a href="https://amanprojects.com/" title="Aman Projects" target="_blank">Aman Projects</a>
      </div>
    </div>
  </div>
</div>
</body>
</html>
