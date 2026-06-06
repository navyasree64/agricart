<?php
// Start session
session_start();

// Include database connection
include('db.php');

// Configuration
$siteName = "AgriCart";
$baseUrl = BASE_URL;

// Active page for navigation
$currentPage = 'contact';

// Process form submission
$successMsg = '';
$errorMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $message = $_POST['message'] ?? '';
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $errorMsg = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errorMsg = 'Please enter a valid email address.';
    } else {
        // Normally, save to database or send email here.
        // For now, simulate success
        $successMsg = 'Thank you for contacting us! We will get back to you shortly.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - <?php echo htmlspecialchars($siteName); ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/styles.css">
    <link rel="icon" href="<?php echo $baseUrl; ?>favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --light-color: #f8f9fa;
            --border-color: #ddd;
        }
        
        .contact-header {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('<?php echo $baseUrl; ?>assets/images/farm-banner.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 20px;
            text-align: center;
            margin-bottom: 40px;
        }

        .contact-header h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .contact-header p {
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .contact-container {
            max-width: 1200px;
            margin: 0 auto 60px;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 1fr 1.5fr;
            gap: 40px;
        }

        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
        }

        .contact-info {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .contact-info h2 {
            color: var(--primary-color);
            margin-bottom: 25px;
            font-size: 1.8rem;
        }

        .info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 25px;
        }

        .info-icon {
            background: #eaf6ec;
            color: var(--primary-color);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            margin-right: 20px;
            flex-shrink: 0;
        }

        .info-content h3 {
            font-size: 1.1rem;
            margin-bottom: 5px;
            color: #333;
        }

        .info-content p {
            color: #666;
            font-size: 0.95rem;
            line-height: 1.5;
        }

        .contact-form-card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .contact-form-card h2 {
            color: #333;
            margin-bottom: 25px;
            font-size: 1.8rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #444;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        textarea.form-control {
            resize: vertical;
            min-height: 150px;
        }

        .btn-submit {
            background: var(--primary-color);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
            width: 100%;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 25px;
            font-weight: 500;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>

<div class="contact-header">
    <h1>Contact Us</h1>
    <p>Have questions about our products, orders, or services? We're here to help! Reach out to us through any of the channels below.</p>
</div>

<div class="contact-container">
    <!-- Contact Information -->
    <div class="contact-info">
        <h2>Get in Touch</h2>
        
        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-map-marker-alt"></i>
            </div>
            <div class="info-content">
                <h3>Our Address</h3>
                <p>AgriCart Headquarters, 123 Green Field Road, Agricultural Hub, Bengaluru, Karnataka, 560001</p>
            </div>
        </div>

        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-phone-alt"></i>
            </div>
            <div class="info-content">
                <h3>Call Us</h3>
                <p>+91 98765 43210<br>+91 80 1234 5678</p>
            </div>
        </div>

        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="info-content">
                <h3>Email Us</h3>
                <p>support@agricart.com<br>info@agricart.com</p>
            </div>
        </div>

        <div class="info-item">
            <div class="info-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="info-content">
                <h3>Business Hours</h3>
                <p>Monday - Saturday: 9:00 AM - 6:00 PM<br>Sunday: Closed</p>
            </div>
        </div>
    </div>

    <!-- Contact Form -->
    <div class="contact-form-card">
        <h2>Send Us a Message</h2>
        
        <?php if (!empty($successMsg)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($successMsg); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errorMsg)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-group">
                <label for="name">Your Name</label>
                <input type="text" id="name" name="name" class="form-control" placeholder="Enter your full name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email address" required>
            </div>

            <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" id="subject" name="subject" class="form-control" placeholder="Enter message subject" required>
            </div>

            <div class="form-group">
                <label for="message">Your Message</label>
                <textarea id="message" name="message" class="form-control" placeholder="Type your message here..." required></textarea>
            </div>

            <button type="submit" class="btn-submit">Send Message</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

</body>
</html>
