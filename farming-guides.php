<?php
// Start session if not already started
session_start();

// Include database connection
include('db.php');

// Configuration
$siteName = "AgriCart";
$baseUrl = "http://localhost/webfinal/";

// Get current season
$currentMonth = date('M');
$currentSeason = '';
$seasonDescription = '';
$recommendedCrops = [];
$waterManagementTips = [];
$soilHealthTips = [];

// Determine current season and set related information
if ($currentMonth == 'Dec' || $currentMonth == 'Jan' || $currentMonth == 'Feb') {
    $currentSeason = 'Winter';
    $seasonDescription = 'Winter is the perfect time for cool-season crops and soil preparation.';
    $recommendedCrops = [
        'Wheat', 'Barley', 'Mustard', 'Potatoes', 'Carrots', 'Spinach', 'Cauliflower', 'Cabbage'
    ];
    $waterManagementTips = [
        'Reduce irrigation frequency as evaporation rates are lower',
        'Water in the morning to prevent freezing at night',
        'Use mulch to retain soil moisture',
        'Monitor soil moisture levels regularly'
    ];
    $soilHealthTips = [
        'Add organic matter to improve soil structure',
        'Test soil pH and adjust if necessary',
        'Apply compost or well-rotted manure',
        'Consider cover cropping to prevent soil erosion'
    ];
} elseif ($currentMonth == 'Mar' || $currentMonth == 'Apr' || $currentMonth == 'May') {
    $currentSeason = 'Spring';
    $seasonDescription = 'Spring is ideal for planting most crops as temperatures rise and days get longer.';
    $recommendedCrops = [
        'Rice', 'Corn', 'Tomatoes', 'Cucumbers', 'Peppers', 'Beans', 'Squash', 'Melons'
    ];
    $waterManagementTips = [
        'Increase irrigation as temperatures rise',
        'Implement drip irrigation for water efficiency',
        'Water early in the morning',
        'Monitor rainfall and adjust irrigation accordingly'
    ];
    $soilHealthTips = [
        'Prepare soil with proper tilling',
        'Add balanced fertilizer before planting',
        'Test soil nutrients and adjust as needed',
        'Maintain proper soil moisture for seed germination'
    ];
} elseif ($currentMonth == 'Jun' || $currentMonth == 'Jul' || $currentMonth == 'Aug') {
    $currentSeason = 'Summer';
    $seasonDescription = 'Summer requires careful water management and heat-tolerant crop selection.';
    $recommendedCrops = [
        'Okra', 'Eggplant', 'Sweet Potatoes', 'Peanuts', 'Millet', 'Sorghum', 'Pumpkins', 'Watermelons'
    ];
    $waterManagementTips = [
        'Water deeply but less frequently',
        'Use mulch to reduce evaporation',
        'Implement shade structures for sensitive crops',
        'Consider rainwater harvesting'
    ];
    $soilHealthTips = [
        'Maintain soil moisture with organic mulch',
        'Monitor soil temperature',
        'Add compost to improve water retention',
        'Consider intercropping to maximize space'
    ];
} else {
    $currentSeason = 'Fall';
    $seasonDescription = 'Fall is perfect for harvesting and preparing for the next growing season.';
    $recommendedCrops = [
        'Lettuce', 'Radishes', 'Turnips', 'Broccoli', 'Kale', 'Garlic', 'Onions', 'Peas'
    ];
    $waterManagementTips = [
        'Reduce irrigation as temperatures cool',
        'Water in the morning to prevent fungal diseases',
        'Prepare irrigation systems for winter',
        'Monitor soil moisture for new plantings'
    ];
    $soilHealthTips = [
        'Add compost and organic matter',
        'Plant cover crops to protect soil',
        'Test soil and plan amendments',
        'Prepare beds for spring planting'
    ];
}

// Active page for navigation
$currentPage = 'guides';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farming Guides - <?php echo htmlspecialchars($siteName); ?></title>
    <link rel="stylesheet" href="<?php echo $baseUrl; ?>assets/css/styles.css">
    <link rel="icon" href="<?php echo $baseUrl; ?>favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .guide-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .season-header {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('<?php echo $baseUrl; ?>assets/images/season-<?php echo strtolower($currentSeason); ?>.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .season-title {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .season-description {
            font-size: 1.2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .guide-section {
            background: white;
            border-radius: 10px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section-title {
            color: #4a8f29;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            display: flex;
            align-items: center;
        }

        .section-title i {
            margin-right: 1rem;
        }

        .crop-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1rem;
        }

        .crop-card {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 8px;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .crop-card:hover {
            transform: translateY(-5px);
        }

        .crop-card i {
            font-size: 2rem;
            color: #4a8f29;
            margin-bottom: 1rem;
        }

        .crop-name {
            font-weight: 600;
            color: #333;
        }

        .tips-list {
            list-style: none;
            padding: 0;
        }

        .tips-list li {
            padding: 1rem;
            margin-bottom: 0.5rem;
            background: #f8f9fa;
            border-radius: 8px;
            display: flex;
            align-items: center;
        }

        .tips-list li i {
            color: #4a8f29;
            margin-right: 1rem;
            font-size: 1.2rem;
        }

        .season-navigation {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 2rem 0;
        }

        .season-btn {
            padding: 0.8rem 1.5rem;
            border: 2px solid #4a8f29;
            border-radius: 30px;
            color: #4a8f29;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .season-btn:hover, .season-btn.active {
            background: #4a8f29;
            color: white;
        }

        @media (max-width: 768px) {
            .guide-container {
                padding: 1rem;
            }

            .season-header {
                padding: 2rem 1rem;
            }

            .season-title {
                font-size: 2rem;
            }

            .crop-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }

            .season-navigation {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include 'header.php'; ?>

    <div class="guide-container">
        <!-- Season Header -->
        <div class="season-header">
            <h1 class="season-title"><?php echo $currentSeason; ?> Farming Guide</h1>
            <p class="season-description"><?php echo $seasonDescription; ?></p>
        </div>

        <!-- Season Navigation -->
        <div class="season-navigation">
            <a href="?season=winter" class="season-btn <?php echo $currentSeason == 'Winter' ? 'active' : ''; ?>">Winter</a>
            <a href="?season=spring" class="season-btn <?php echo $currentSeason == 'Spring' ? 'active' : ''; ?>">Spring</a>
            <a href="?season=summer" class="season-btn <?php echo $currentSeason == 'Summer' ? 'active' : ''; ?>">Summer</a>
            <a href="?season=fall" class="season-btn <?php echo $currentSeason == 'Fall' ? 'active' : ''; ?>">Fall</a>
        </div>

        <!-- Recommended Crops Section -->
        <div class="guide-section">
            <h2 class="section-title">
                <i class="fas fa-seedling"></i>
                Recommended Crops for <?php echo $currentSeason; ?>
            </h2>
            <div class="crop-grid">
                <?php foreach ($recommendedCrops as $crop): ?>
                    <div class="crop-card">
                        <i class="fas fa-leaf"></i>
                        <h3 class="crop-name"><?php echo $crop; ?></h3>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Water Management Section -->
        <div class="guide-section">
            <h2 class="section-title">
                <i class="fas fa-tint"></i>
                Water Management Tips
            </h2>
            <ul class="tips-list">
                <?php foreach ($waterManagementTips as $tip): ?>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <?php echo $tip; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Soil Health Section -->
        <div class="guide-section">
            <h2 class="section-title">
                <i class="fas fa-mountain"></i>
                Soil Health Management
            </h2>
            <ul class="tips-list">
                <?php foreach ($soilHealthTips as $tip): ?>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <?php echo $tip; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Include Footer -->
    <?php include 'footer.php'; ?>

    <script>
        // Add any interactive features here
        document.addEventListener('DOMContentLoaded', function() {
            // Add smooth scrolling for section links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function (e) {
                    e.preventDefault();
                    document.querySelector(this.getAttribute('href')).scrollIntoView({
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
</body>
</html> 