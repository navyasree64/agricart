<?php
// Start session if not already started
session_start();

// Include database connection
include('db.php');

// Configuration
$siteName = "AgriCart";
$baseUrl = "http://localhost/webfinal/"; // Update this to your actual base URL

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

        <!-- Team Section -->
        <div class="team-section">
            <h2 class="section-title">Meet Our Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="<?php echo $baseUrl; ?>assets/images/team/madhav.jpg" alt="Madhav">
                    <div class="team-info">
                        <h3>Madhav</h3>
                        <div class="position">Team Lead</div>
                        <p class="bio">Leading our team with expertise in project management and agricultural technology solutions.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <img src="<?php echo $baseUrl; ?>assets/images/team/koushik.jpg" alt="Koushik">
                    <div class="team-info">
                        <h3>Koushik</h3>
                        <div class="position">Technical Lead</div>
                        <p class="bio">Expert in web development and system architecture, ensuring our platform runs smoothly.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <img src="<?php echo $baseUrl; ?>assets/images/team/nandan.jpg" alt="Nandan">
                    <div class="team-info">
                        <h3>Nandan</h3>
                        <div class="position">UI/UX Designer</div>
                        <p class="bio">Creating beautiful and intuitive user interfaces that enhance the user experience.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <img src="<?php echo $baseUrl; ?>assets/images/team/navya.jpg" alt="Navya">
                    <div class="team-info">
                        <h3>Navya</h3>
                        <div class="position">Content Manager</div>
                        <p class="bio">Managing and creating engaging content that helps farmers make informed decisions.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-member">
                    <img src="<?php echo $baseUrl; ?>assets/images/team/chetan.jpg" alt="Chetan">
                    <div class="team-info">
                        <h3>Chetan</h3>
                        <div class="position">Backend Developer</div>
                        <p class="bio">Building robust backend systems and ensuring data security for our platform.</p>
                        <div class="social-links">
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                            <a href="#"><i class="fab fa-twitter"></i></a>
                            <a href="#"><i class="fas fa-envelope"></i></a>
                        </div>
                    </div>
                </div>
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