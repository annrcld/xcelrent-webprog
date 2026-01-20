<?php
// public/pages/contact.php
$page_title = "Contact Us";
include __DIR__ . '/../includes/header.php';
?>

<nav>
    <div class="nav-container">
        <div class="logo" onclick="window.location.href='home.php'">Xcelrent<span class="dot">.</span></div>
        <div class="nav-links">
            <a href="home.php">Home</a>
            <a href="cars.php">Book</a>
            <a href="about.php">About</a>
            <a href="contact.php" class="active">Contact</a>
             <div class="divider-vertical"></div>
            <div id="authButtons" class="nav-auth-group">
                <button class="btn btn-text" onclick="openModal('operatorModal')">Be an Operator</button>
                <button class="btn btn-primary" onclick="openModal('loginModal')">Sign In</button>
            </div>
        </div>
        <div class="mobile-menu-btn"><i class="fa-solid fa-bars"></i></div>
    </div>
</nav>

<style>
    .contact-container {
        max-width: 800px;
        margin: 4rem auto;
        padding: 0 2rem;
    }
    
    .contact-content {
        background: white;
        padding: 3rem;
        border-radius: 12px;
        box-shadow: var(--shadow-soft);
        margin: 2rem 0;
    }
    
    .contact-info {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin: 2rem 0;
    }
    
    .info-card {
        background: var(--bg-secondary);
        padding: 1.5rem;
        border-radius: 8px;
        text-align: center;
    }
    
    .info-card i {
        font-size: 2rem;
        color: var(--accent-red);
        margin-bottom: 1rem;
    }
    
    .contact-form {
        margin-top: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-control {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 1rem;
    }
    
    textarea.form-control {
        min-height: 150px;
        resize: vertical;
    }
</style>

<div class="contact-container">
    <div class="contact-content">
        <h1>Contact Xcelrent</h1>
        <p>Have questions or need assistance? Reach out to us and our team will get back to you as soon as possible.</p>
        
        <div class="contact-info">
            <div class="info-card">
                <i class="fas fa-map-marker-alt"></i>
                <h3>Location</h3>
                <p>Batasan Hills, Quezon City<br>Metro Manila, Philippines</p>
            </div>
            
            <div class="info-card">
                <i class="fas fa-phone"></i>
                <h3>Phone</h3>
                <p>+63 912 345 6789</p>
            </div>
            
            <div class="info-card">
                <i class="fas fa-envelope"></i>
                <h3>Email</h3>
                <p>support@xcelrent.com</p>
            </div>
        </div>
        
        <div class="contact-form">
            <h2>Send us a Message</h2>
            <form id="contactForm">
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Your Name" required>
                </div>
                
                <div class="form-group">
                    <input type="email" class="form-control" placeholder="Your Email" required>
                </div>
                
                <div class="form-group">
                    <input type="text" class="form-control" placeholder="Subject">
                </div>
                
                <div class="form-group">
                    <textarea class="form-control" placeholder="Your Message" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('contactForm').addEventListener('submit', function(e) {
    e.preventDefault();
    alert('Thank you for your message! We will get back to you soon.');
    this.reset();
});
</script>

<?php
include __DIR__ . '/../includes/footer.php';
?>