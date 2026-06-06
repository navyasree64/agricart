<?php
// Start session if not already started
session_start();

// Include database connection
include('db.php');

// Configuration
$siteName = "AgriCart";
$baseUrl = BASE_URL;

// Active page for navigation
$currentPage = 'about';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - <?php echo htmlspecialchars($siteName); ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/styles.css">
    <link rel="icon" href="<?php echo $baseUrl; ?>favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* About Us Page Styles */
        .about-header {
            background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), 
                        url('<?php echo $baseUrl; ?>assets/images/about-banner.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 80px 20px;
            text-align: center;
            margin-bottom: 40px;
        }

        .about-header h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
        }

        .about-header p {
            font-size: 1.1rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .about-section {
            padding: 40px 0;
        }

        .about-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .mission-vision {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 60px;
        }

        .mission-box, .vision-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .mission-box i, .vision-box i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        .team-section {
            margin-top: 60px;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .team-member {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .team-member:hover {
            transform: translateY(-5px);
        }

        .team-member img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }

        .team-info {
            padding: 20px;
            text-align: center;
        }

        .team-info h3 {
            margin-bottom: 5px;
            color: var(--text-dark);
        }

        .team-info .position {
            color: var(--primary-color);
            font-weight: 500;
            margin-bottom: 10px;
        }

        .team-info .bio {
            color: var(--text-medium);
            font-size: 0.9rem;
            margin-bottom: 15px;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-links a {
            color: var(--text-medium);
            transition: color 0.3s ease;
        }

        .social-links a:hover {
            color: var(--primary-color);
        }

        .values-section {
            margin-top: 60px;
            background: #f8f9fa;
            padding: 60px 0;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .value-card {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .value-card i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .about-header h1 {
                font-size: 2rem;
            }

            .mission-vision {
                grid-template-columns: 1fr;
            }

            .team-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include 'header.php'; ?>

    <!-- About Header -->
    <div class="about-header">
        <h1>About AgriCart</h1>
        <p>Empowering farmers with quality agricultural products and sustainable solutions</p>
    </div>

    <!-- Main Content -->
    <div class="about-content">
        <!-- Mission & Vision -->
        <div class="mission-vision">
            <div class="mission-box">
                <i class="fas fa-bullseye"></i>
                <h2>Our Mission</h2>
                <p>To provide farmers with high-quality agricultural products and innovative solutions that enhance productivity while promoting sustainable farming practices.</p>
            </div>
            <div class="vision-box">
                <i class="fas fa-eye"></i>
                <h2>Our Vision</h2>
                <p>To become the leading agricultural marketplace that connects farmers with the best products and knowledge, contributing to a sustainable and prosperous farming community.</p>
            </div>
        </div>



        <!-- Values Section -->
        <div class="values-section">
            <h2 class="section-title">Our Core Values</h2>
            <div class="values-grid">
                <div class="value-card">
                    <i class="fas fa-leaf"></i>
                    <h3>Sustainability</h3>
                    <p>We are committed to promoting sustainable farming practices that protect our environment.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-handshake"></i>
                    <h3>Integrity</h3>
                    <p>We maintain the highest standards of honesty and transparency in all our operations.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-users"></i>
                    <h3>Community</h3>
                    <p>We believe in building strong relationships with farmers and supporting their growth.</p>
                </div>
                <div class="value-card">
                    <i class="fas fa-lightbulb"></i>
                    <h3>Innovation</h3>
                    <p>We continuously seek new ways to improve farming practices and product quality.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>
</body>
</html> 