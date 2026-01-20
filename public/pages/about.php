<?php
// public/pages/about.php
$page_title = "About Us";
include __DIR__ . '/../includes/header.php';
?>

<nav>
    <div class="nav-container">
        <div class="logo" onclick="window.location.href='home.php'">Xcelrent<span class="dot">.</span></div>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="about.php" class="active">About</a>
            <a href="home.php#about" onclick="scrollToSection('testimonials')">Reviews</a>
            <a href="contact.php">Contact</a>
             <div class="divider-vertical"></div>
            <div class="nav-auth-group">
                <button class="btn btn-text" onclick="openModal('operatorModal')">Be an Operator</button>
                <button class="btn btn-primary" onclick="openModal('loginModal')">Sign In</button>
            </div>
        </div>
        <div class="mobile-menu-btn"><i class="fa-solid fa-bars"></i></div>
    </div>
</nav>

<style>
    .about-container {
        max-width: 800px;
        margin: 4rem auto;
        padding: 0 2rem;
    }
    
    .about-content {
        background: white;
        padding: 3rem;
        border-radius: 12px;
        box-shadow: var(--shadow-soft);
        margin: 2rem 0;
    }
    
    .about-content h1 {
        color: var(--text-main);
        margin-bottom: 1.5rem;
    }
    
    .about-content p {
        line-height: 1.8;
        color: var(--text-muted);
        margin-bottom: 1.5rem;
    }
    
    .mission-section {
        background: var(--bg-secondary);
        padding: 2rem;
        border-radius: 12px;
        margin: 2rem 0;
    }
</style>

<div class="about-container">
    <div class="about-content">
        <h1>About Xcelrent Car Rental</h1>
        <p>At Xcelrent, we believe that great journeys begin with reliable transportation. Founded in 2026, we've been committed to providing top-quality vehicles at competitive prices, ensuring every customer has access to safe, comfortable, and dependable transportation solutions.</p>
        
        <div class="mission-section">
            <h2>Our Mission</h2>
            <p>We strive to revolutionize the car rental experience by combining cutting-edge technology with exceptional customer service, making vehicle rental seamless, transparent, and affordable for everyone.</p>
        </div>
        
        <h2>Why Choose Xcelrent?</h2>
        <ul style="padding-left: 1.5rem; margin: 1.5rem 0;">
            <li style="margin-bottom: 0.5rem;">Extensive fleet of well-maintained vehicles</li>
            <li style="margin-bottom: 0.5rem;">Competitive pricing with no hidden fees</li>
            <li style="margin-bottom: 0.5rem;">24/7 customer support</li>
            <li style="margin-bottom: 0.5rem;">Flexible rental terms</li>
            <li>Transparent policies with easy booking</li>
        </ul>
    </div>
</div>

<?php
include __DIR__ . '/../includes/footer.php';
?>