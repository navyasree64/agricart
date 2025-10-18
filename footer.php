<?php
// Configuration
$siteName = "AGRICART";
$currentYear = date("Y");
$baseUrl = isset($baseUrl) ? $baseUrl : "/"; // Use existing baseUrl if defined elsewhere
?>

<footer class="site-footer">
  <div class="footer-container">
    <!-- About -->
    <div class="footer-column about-column">
      <h3>About <?php echo htmlspecialchars($siteName); ?></h3>
      <p>
        <?php echo htmlspecialchars($siteName); ?> is your trusted online destination for high-quality agricultural products, tools, and resources. 
        Empowering farmers with modern solutions for a better tomorrow.
      </p>
    </div>

    <!-- Quick Links -->
    <div class="footer-column">
      <h3>Quick Links</h3>
      <ul>
        <li><a href="<?php echo $baseUrl; ?>index.php">Home</a></li>
        <li><a href="<?php echo $baseUrl; ?>about.php">About Us</a></li>
        <li><a href="<?php echo $baseUrl; ?>cart.php">Cart</a></li>
        <li><a href="<?php echo $baseUrl; ?>profile.php">My Account</a></li>
        <li><a href="<?php echo $baseUrl; ?>contact.php">Contact Us</a></li>
      </ul>
    </div>

    <!-- Contact Info -->
    <div class="footer-column">
      <h3>Contact Us</h3>
      <address>
        <p><i class="icon-email"></i> Email: <a href="mailto:support@agricart.com">support@agricart.com</a></p>
        <p><i class="icon-phone"></i> Phone: +91-9876543210</p>
        <p><i class="icon-location"></i> Location: Hyderabad, Telangana</p>
      </address>
    </div>

    <!-- Social Media -->
    <div class="footer-column">
      <h3>Follow Us</h3>
      <div class="social-icons">
        <a href="#" aria-label="Facebook"><i class="icon-facebook"></i> Facebook</a>
        <a href="#" aria-label="Instagram"><i class="icon-instagram"></i> Instagram</a>
        <a href="#" aria-label="YouTube"><i class="icon-youtube"></i> YouTube</a>
        <a href="#" aria-label="Twitter"><i class="icon-twitter"></i> Twitter</a>
      </div>
    </div>
  </div>

  <!-- Newsletter Subscription (Optional) -->
  <div class="newsletter-section">
    <h3>Subscribe to our Newsletter</h3>
    <form action="<?php echo $baseUrl; ?>newsletter-subscribe.php" method="POST" class="newsletter-form">
      <input type="email" name="email" placeholder="Enter your email" required>
      <button type="submit">Subscribe</button>
    </form>
    <p class="newsletter-note">Stay updated with our latest products and farming tips!</p>
  </div>

  <div class="copyright">
    <hr>
    <p>&copy; <?php echo $currentYear; ?> <strong><?php echo htmlspecialchars($siteName); ?></strong>. All rights reserved.</p>
    <div class="footer-links">
      <a href="<?php echo $baseUrl; ?>pages/privacy-policy.php">Privacy Policy</a> | 
      <a href="<?php echo $baseUrl; ?>pages/terms.php">Terms of Service</a> | 
      <a href="<?php echo $baseUrl; ?>pages/faq.php">FAQ</a>
    </div>
  </div>
</footer>

<!-- Add this CSS to your styles.css file -->
<style>
  .site-footer {
    background: #2e7d32;
    color: white;
    padding: 40px 20px 20px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  }

  .footer-container {
    max-width: 1200px;
    margin: auto;
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    gap: 30px;
  }

  .footer-column {
    flex: 1 1 250px;
  }

  .footer-column h3 {
    margin-bottom: 15px;
    position: relative;
    padding-bottom: 10px;
  }

  .footer-column h3::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 2px;
    background: rgba(255, 255, 255, 0.5);
  }

  .footer-column ul {
    list-style: none;
    padding: 0;
    margin: 0;
  }

  .footer-column ul li {
    padding: 5px 0;
  }

  .footer-column a {
    color: white;
    text-decoration: none;
    transition: opacity 0.3s;
  }

  .footer-column a:hover {
    opacity: 0.8;
    text-decoration: underline;
  }

  .social-icons {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
  }

  .social-icons a {
    display: inline-flex;
    align-items: center;
    margin-bottom: 10px;
  }

  .newsletter-section {
    max-width: 1200px;
    margin: 30px auto;
    padding: 20px;
    background: rgba(0, 0, 0, 0.1);
    border-radius: 5px;
    text-align: center;
  }

  .newsletter-form {
    display: flex;
    max-width: 500px;
    margin: 15px auto;
  }

  .newsletter-form input {
    flex: 1;
    padding: 10px;
    border: none;
    border-radius: 4px 0 0 4px;
  }

  .newsletter-form button {
    padding: 10px 20px;
    background: #ff6f00;
    color: white;
    border: none;
    border-radius: 0 4px 4px 0;
    cursor: pointer;
    transition: background 0.3s;
  }

  .newsletter-form button:hover {
    background: #e65100;
  }

  .newsletter-note {
    font-size: 13px;
    opacity: 0.8;
  }

  .copyright {
    text-align: center;
    margin-top: 30px;
    font-size: 14px;
  }

  .copyright hr {
    border-color: rgba(255, 255, 255, 0.3);
    margin-bottom: 20px;
  }

  .footer-links {
    margin-top: 10px;
  }

  .footer-links a {
    color: white;
    text-decoration: none;
  }

  @media (max-width: 768px) {
    .footer-column {
      flex: 1 1 100%;
    }
    
    .newsletter-form {
      flex-direction: column;
    }
    
    .newsletter-form input,
    .newsletter-form button {
      width: 100%;
      border-radius: 4px;
      margin-bottom: 10px;
    }
  }
</style>
