<?php
session_start();
include('db.php');
include('header.php');
?>

<div class="about-products-container">
    <div class="hero-section">
        <h1>Our Products & Their Benefits</h1>
        <p>Learn how our products help farmers achieve better results</p>
    </div>

    <div class="content-section">
        <div class="info-card">
            <h2>How We Source Our Products</h2>
            <div class="info-content">
                <div class="info-item">
                    <i class="fas fa-seedling"></i>
                    <h3>Direct from Farmers</h3>
                    <p>We work directly with local farmers to source the highest quality seeds and agricultural products.</p>
                </div>
                <div class="info-item">
                    <i class="fas fa-certificate"></i>
                    <h3>Quality Assurance</h3>
                    <p>All products undergo rigorous quality testing to ensure they meet our high standards.</p>
                </div>
                <div class="info-item">
                    <i class="fas fa-truck"></i>
                    <h3>Efficient Distribution</h3>
                    <p>Our streamlined distribution network ensures products reach farmers quickly and in perfect condition.</p>
                </div>
            </div>
        </div>

        <div class="info-card">
            <h2>Benefits to Farmers</h2>
            <div class="info-content">
                <div class="info-item">
                    <i class="fas fa-chart-line"></i>
                    <h3>Increased Yield</h3>
                    <p>Our products help farmers achieve higher crop yields through improved seed quality and agricultural inputs.</p>
                </div>
                <div class="info-item">
                    <i class="fas fa-dollar-sign"></i>
                    <h3>Cost Efficiency</h3>
                    <p>By providing high-quality products at competitive prices, we help farmers reduce costs and increase profits.</p>
                </div>
                <div class="info-item">
                    <i class="fas fa-leaf"></i>
                    <h3>Sustainable Farming</h3>
                    <p>Our products support sustainable farming practices, helping farmers protect their land for future generations.</p>
                </div>
            </div>
        </div>

        <div class="testimonials">
            <h2>Farmer Success Stories</h2>
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <p>"Using these products has increased my crop yield by 30% in just one season!"</p>
                    <cite>- Rajesh Kumar, Small Farmer</cite>
                </div>
                <div class="testimonial-card">
                    <p>"The quality of seeds and fertilizers has made a significant difference in my farm's productivity."</p>
                    <cite>- Priya Sharma, Organic Farmer</cite>
                </div>
                <div class="testimonial-card">
                    <p>"The technical support and guidance provided along with the products has been invaluable."</p>
                    <cite>- Amit Patel, Commercial Farmer</cite>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.about-products-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.hero-section {
    text-align: center;
    padding: 60px 20px;
    background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('assets/images/farm-banner.jpg');
    background-size: cover;
    background-position: center;
    color: white;
    border-radius: 10px;
    margin-bottom: 40px;
}

.hero-section h1 {
    font-size: 2.5em;
    margin-bottom: 20px;
}

.content-section {
    display: grid;
    gap: 30px;
}

.info-card {
    background: white;
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.info-card h2 {
    color: #4a8f29;
    margin-bottom: 30px;
    text-align: center;
}

.info-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.info-item {
    text-align: center;
    padding: 20px;
}

.info-item i {
    font-size: 2.5em;
    color: #4a8f29;
    margin-bottom: 15px;
}

.info-item h3 {
    color: #333;
    margin-bottom: 10px;
}

.testimonials {
    background: #f8f9fa;
    padding: 40px;
    border-radius: 10px;
}

.testimonials h2 {
    text-align: center;
    color: #4a8f29;
    margin-bottom: 30px;
}

.testimonial-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.testimonial-card {
    background: white;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.testimonial-card p {
    font-style: italic;
    margin-bottom: 15px;
}

.testimonial-card cite {
    color: #666;
    font-size: 0.9em;
}
</style>

<?php include('footer.php'); ?> 