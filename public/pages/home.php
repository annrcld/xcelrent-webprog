<?php
// public/pages/home.php
$page_title = "Home";
include __DIR__ . '/../includes/header.php'; // Uses absolute path to avoid include errors

// Fetch cars from database
$cars = [];
if (isset($conn)) {
    // Fetch cars with their primary photo
    $sql = "SELECT c.*, 
                   CONCAT(c.brand, ' ', c.model) AS name, 
                   c.seating AS seats, 
                   c.fuel_type AS fuel, 
                   c.tier4_daily AS price,
                   (SELECT file_path FROM car_photos WHERE car_id = c.id ORDER BY is_primary DESC LIMIT 1) AS image
            FROM cars c 
            WHERE c.status = 'live' 
            ORDER BY c.id DESC";
    $result = $conn->query($sql);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $cars[] = $row;
        }
    }
}
?>

    <nav>
        <div class="nav-container">
            <div class="logo" onclick="window.location.reload()">Xcelrent<span class="dot">.</span></div>

            <div class="nav-links">
                <a href="cars.php">Book</a>
                <a href="?page=about">About</a>
                <a onclick="scrollToSection('testimonials')">Reviews</a>
                <a href="?page=contact">Contact</a>

                <div class="divider-vertical"></div>

                <div id="authButtons" class="nav-auth-group">
                    <button class="btn btn-text" onclick="openModal('operatorModal')">Be an Operator</button>
                    <button class="btn btn-primary" onclick="openModal('loginModal')">Sign In</button>
                </div>

                <div class="user-menu" id="userMenu" style="display: none;">
                    <div class="user-avatar" onclick="toggleDropdown()">GB</div>
                    <div class="dropdown-menu" id="dropdownMenu">
                        <a onclick="logout()">Log out</a>
                    </div>
                </div>
            </div>

            <div class="mobile-menu-btn"><i class="fa-solid fa-bars"></i></div>
        </div>
    </nav>

    <div class="guest-notice" id="guestNotice">
        <div class="notice-content">
            <span>Browsing as a guest? <a onclick="openModal('signupModal')">Create an account</a> for exclusive deals.</span>
        </div>
        <button onclick="document.getElementById('guestNotice').style.display='none'">&times;</button>
    </div>

    <header class="hero" id="about">
        <div class="hero-content">
            <h1>Simply Excellent.<br><span class="text-highlight">Truly Affordable.</span></h1>
            <p>Unlock your next adventure. Reliable rides ready for wherever the road takes you.</p>
        </div>
    </header>

<div class="search-box">
    <form class="search-form" onsubmit="searchCars(event)">

        <div class="form-group">
            <label>Type</label>
            <div class="custom-select" id="typeSelect">
                <input type="hidden" id="driverOption" value="self">
                <div class="select-trigger">
                    <span>Self-Drive</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="custom-options">
                    <div class="option selected" data-value="self">Self-Drive</div>
                    <div class="option" data-value="driver">With Driver</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Where to?</label>
            <div class="custom-select" id="destinationSelect">
                <input type="hidden" id="destinationValue" value="metro">
                <div class="select-trigger">
                    <span>Metro Manila</span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="custom-options">
                    <div class="option selected" data-value="metro">Metro Manila</div>
                    <div class="option" data-value="outside">Outside Metro</div>
                    <div class="option" data-value="luzon">Any point of Luzon</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Pickup</label>
            <div class="date-trigger" onclick="openDateModal('pickup')">
                <span id="pickupDisplay">Date & Time</span>
                <i class="fa-regular fa-calendar"></i>
            </div>
            <input type="hidden" id="pickupDateValue">
        </div>

        <div class="form-group">
            <label>Return</label>
            <div class="date-trigger" onclick="openDateModal('return')">
                <span id="returnDisplay">Date & Time</span>
                <i class="fa-regular fa-calendar"></i>
            </div>
            <input type="hidden" id="returnDateValue">
        </div>

        <div class="form-group submit-group">
            <button type="submit" class="btn-search">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

<div id="resultsContainer" style="<?php echo empty($cars) ? 'display: none;' : ''; ?> padding: 4rem 2rem;">
    <div id="resultsLoader" class="cars-grid" style="<?php echo !empty($cars) ? 'display: none;' : ''; ?>">
        <div class="skeleton-card">
            <div class="skeleton-image shim"></div>
            <div class="skeleton-details">
                <div class="skeleton-line shim" style="width: 60%"></div>
                <div class="skeleton-line shim" style="width: 40%"></div>
                <div class="skeleton-button shim"></div>
            </div>
        </div>
        <div class="skeleton-card">
            <div class="skeleton-image shim"></div>
            <div class="skeleton-details">
                <div class="skeleton-line shim" style="width: 70%"></div>
                <div class="skeleton-line shim" style="width: 30%"></div>
                <div class="skeleton-button shim"></div>
            </div>
        </div>
        <div class="skeleton-card">
            <div class="skeleton-image shim"></div>
            <div class="skeleton-details">
                <div class="skeleton-line shim" style="width: 50%"></div>
                <div class="skeleton-line shim" style="width: 50%"></div>
                <div class="skeleton-button shim"></div>
            </div>
        </div>
    </div>

    <div id="resultsContent" style="<?php echo !empty($cars) ? 'display: block;' : 'display: none;'; ?>">
    <div class="results-header" style="text-align: center; margin-bottom: 3rem;">
        <h2 class="section-heading">Available Vehicles</h2>
        <p class="section-subheading">Hand-picked premium rides for your journey</p>
    </div>

    <div class="results-meta" style="display: flex; justify-content: space-between; max-width: 1200px; margin: 0 auto 2rem; padding: 0 20px;">
        <span class="results-count"><?php echo count($cars); ?> vehicles found</span>
        <div class="sort-wrapper">
             <span>Sort by: Price <i class="fa-solid fa-chevron-down"></i></span>
        </div>
    </div>

    <div class="cars-grid" id="carsGrid">
        <?php foreach ($cars as $car): ?>
            <div class="car-card" style="border: 1px solid #eee; border-radius: 12px; overflow: hidden; transition: transform 0.2s; background: #fff;">
                <div class="car-image-wrapper" style="height: 200px; overflow: hidden;">
                    <!-- Image path from DB is likely 'uploads/cars/...'. Adjusting base path to public/ -->
                    <img src="/project_xcelrent/public/<?php echo htmlspecialchars($car['image'] ?? 'assets/img/default_car.jpg'); ?>" 
                         alt="<?php echo htmlspecialchars($car['name'] ?? 'Car'); ?>" 
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <div class="car-details" style="padding: 1.5rem;">
                    <h3 style="margin: 0 0 0.5rem; font-size: 1.25rem;"><?php echo htmlspecialchars($car['name'] ?? 'Unknown Model'); ?></h3>
                    <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;">
                        <i class="fa-solid fa-user" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($car['seats'] ?? '4'); ?> Seats &nbsp;|&nbsp; 
                        <i class="fa-solid fa-gas-pump" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($car['fuel'] ?? 'Gas'); ?>
                    </p>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 700; font-size: 1.2rem; color: #22c55e;">₱<?php echo number_format($car['price'] ?? 0); ?>/day</span>
                        <!-- You may need to adjust the onclick function based on your JS implementation -->
                        <button class="btn btn-primary" onclick="viewCar(<?php echo $car['id']; ?>)">View</button>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        </div>

    <div class="pagination-container" style="display: flex; justify-content: center; margin-top: 4rem;">
        </div>
</div>
    </div>
</div>
    <section class="advantages-section" id="advantages">
        <div class="advantages-grid">
            <div class="advantage-card">
                <i class="fa-solid fa-tag advantage-icon"></i>
                <h3>Best Rates</h3>
                <p>Guaranteed lowest market prices.</p>
            </div>
            <div class="advantage-card">
                <i class="fa-solid fa-car advantage-icon"></i>
                <h3>Quality Fleet</h3>
                <p>Maintained for safety and comfort.</p>
            </div>
            <div class="advantage-card">
                <i class="fa-solid fa-shield-halved advantage-icon"></i>
                <h3>Secure</h3>
                <p>Verified hosts and transparent contracts.</p>
            </div>
        </div>
        <section class="testimonials-section" id="testimonials">
    <div class="section-header">
        <h2 class="section-heading">What Our Renters Say</h2>
        <p class="section-subheading">Don't just take our word for it—hear from our community.</p>
    </div>

    <div class="testimonials-grid">
        <div class="testimonial-card">
            <div class="stars">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <p class="feedback-text">"The booking process was incredibly smooth. The car was spotless and ready exactly when I needed it. Best rental experience in QC!"</p>
            <div class="user-info">
                <div class="user-details">
                    <p class="user-name">James Rodriguez</p>
                    <p class="user-status">Verified Renter</p>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <div class="stars">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <p class="feedback-text">"Super affordable rates compared to others. I used the 'With Driver' service for a family trip to Tagaytay and it was very professional."</p>
            <div class="user-info">
                <div class="user-details">
                    <p class="user-name">Maria Santos</p>
                    <p class="user-status">Verified Renter</p>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <div class="stars">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <p class="feedback-text">"I love the transparency. No hidden fees, no complicated contracts. Xcelrent is now my go-to for my monthly business trips."</p>
            <div class="user-info">
                <div class="user-details">
                    <p class="user-name">Kevin Lee</p>
                    <p class="user-status">Verified Renter</p>
                </div>
            </div>
        </div>
    </div>
    </section>

    <footer id="contact">
        <div class="footer-container">
            <div class="footer-left">
                <div class="brand-location-group">
                    <h2 class="logo">Xcelrent<span class="dot">.</span></h2>
                    <span class="divider-slash">/</span>
                    <p class="location-text">Batasan Hills, Quezon City, Metro Manila</p>
                </div>
                <p class="copyright">&copy; 2026 Xcelrent Car Rental.</p>
            </div>

            <div class="footer-right">
                <div class="social-links">
                    <a href="https://www.facebook.com/xcelrentcarrental"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.tiktok.com/@xcelrent"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <div id="loginModal" class="modal center-flex">
        <div class="modal-content minimal-modal">
            <span class="close" onclick="closeModal('loginModal')">&times;</span>
            <h2>Welcome back</h2>
            <form onsubmit="handleLogin(event)">
                <input type="email" placeholder="Email" required class="minimal-input">
                <input type="password" placeholder="Password" required class="minimal-input">
                <button type="submit" class="btn btn-primary full-width">Sign In</button>
            </form>
            <p class="modal-footer-text">New here? <a onclick="closeModal('loginModal'); openModal('signupModal')">Create account</a></p>
        </div>
    </div>

    <div id="dateModal" class="modal center-flex">
        <div class="modal-content date-modal-content">
            <div class="date-header">
                <span id="dateModalTitle">Select Date</span>
                <h2 id="headerDate">--</h2>
            </div>
            <div class="calendar-wrapper">
                <input type="text" id="inlineCalendar" style="display: none;">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary full-width" onclick="confirmDate()">Confirm Schedule</button>
            </div>
        </div>
    </div>

        <div id="signupModal" class="modal center-flex">
        <div class="modal-content wide-modal">
            <span class="close" onclick="closeModal('signupModal')">&times;</span>
            <h2>Create Account</h2>
            <p class="section-subheading">Join Xcelrent to start your journey.</p>

            <form onsubmit="handleSignUp(event)" id="signupForm">
                <div class="form-row">
                    <div class="form-group-signup">
                        <label>First Name</label>
                        <input type="text" id="regFirst" placeholder="Jan" class="minimal-input" required>
                    </div>
                    <div class="form-group-signup">
                        <label>Last Name</label>
                        <input type="text" id="regLast" placeholder="Santos" class="minimal-input" required>
                    </div>
                </div>

                <div class="form-group-signup">
                    <label>Contact Number</label>
                    <input type="tel" id="regPhone" placeholder="09XXXXXXXXX" class="minimal-input" required>
                    <span class="error-text" id="phoneError">Phone number must be 11 digits.</span>
                </div>

                <div class="form-group-signup">
                    <label>Email Address</label>
                    <input type="email" id="regEmail" placeholder="your@email.com" class="minimal-input" required>
                </div>

                <div class="form-group-signup">
                    <label>Password</label>
                    <input type="password" id="regPass" placeholder="••••••••" class="minimal-input" required>
                    <span class="error-text" id="passError">Minimum 8 characters.</span>
                </div>

                <button type="submit" class="btn btn-primary full-width">Create Account</button>
            </form>
            <p class="modal-footer-text">Already have an account? <a onclick="closeModal('signupModal'); openModal('loginModal')">Sign In</a></p>
        </div>
    </div>

    <div id="otpModal" class="modal center-flex">
        <div class="modal-content wide-modal text-center">
            <span class="close" onclick="closeModal('otpModal')">&times;</span>
            <div style="font-size: 3rem; color: var(--accent-red); margin-bottom: 15px;">
                <i class="fa-solid fa-envelope-circle-check"></i>
            </div>
            <h2>Verify Email</h2>
            <p class="section-subheading">A 6-digit code was sent to <br><strong id="displayEmail">user@email.com</strong></p>

            <div class="otp-container" style="margin: 25px 0;">
                <input type="text" maxlength="6" id="otpInput" placeholder="000000"
                    style="letter-spacing: 8px; text-align: center; font-size: 1.5rem; font-weight: 800; border-bottom: 2px solid var(--accent-red); width: 80%; border-top:none; border-left:none; border-right:none; outline:none;">
            </div>

            <button class="btn btn-primary full-width" onclick="verifyAndFinish()">Verify & Join Xcelrent</button>
            <p class="modal-footer-text">Didn't get the code? <a href="#" style="color: var(--accent-red); font-weight: 600;">Resend</a></p>
        </div>
    </div>

        <div id="operatorModal" class="modal">
            <div class="modal-content wide-modal">
                <span class="close" onclick="closeModal('operatorModal')">&times;</span>

                <div class="step-content active" id="opStep1">
                    <h2>Become an Operator</h2>
                    <p class="promo-text">Earn <span class="text-highlight">₱20,000 - ₱60,000</span> monthly.</p>

                    <div class="requirements-container">
                    <p class="req-title">What you'll need:</p>
                            <div class="req-grid">
                                <div class="req-item">
                                    <i class="fa-solid fa-id-card"></i>
                                    <span>Prof. License</span>
                                </div>
                                <div class="req-item">
                                    <i class="fa-solid fa-file-invoice"></i>
                                    <span>Vehicle OR/CR</span>
                                </div>
                                <div class="req-item">
                                    <i class="fa-solid fa-shield-halved"></i>
                                    <span>NBI Clearance</span>
                                </div>
                                <div class="req-item">
                                    <i class="fa-solid fa-file-contract"></i>
                                    <span>Deed of Sale</span>
                                </div>
                            </div>
                        </div>

                <button class="btn btn-primary full-width" onclick="moveStep('next')">Get Started</button>
            </div>

            <div class="step-content" id="opStep2">
                <h3>Privacy Policy</h3>
                <div class="privacy-scroll-box">
                    <p>Xcelrent values your privacy and is committed to protecting your personal information. We collect only the necessary personal identification and vehicle details to verify users and ensure safe, secure rental transactions.</p>

                    <p>Your information is used solely for account verification, rental processing, and listing vehicles on our platform. We do not sell, trade, or share your personal data with unauthorized third parties. All data is handled with strict security measures in place to maintain confidentiality and trust.</p>

                    <p>By using our services, you agree to our Terms of Service and this Privacy Policy.</p>
                </div>
                <div class="agreement-row">
                    <input type="checkbox" id="privacyAgree">
                    <label for="privacyAgree">I have read and agree to the Privacy Policy and Terms of Service.</label>
                </div>
            </div>

 <div class="step-content" id="opStep3">
            <h3>Vehicle Details</h3>

            <div class="form-row-compact">
                <div class="form-group-inner">
                    <label>Vehicle Name</label>
                    <input type="text" placeholder="e.g. Toyota Vios 2026" class="minimal-input" id="vName">
                </div>
                <div class="form-group-inner">
                    <label>Plate Number</label>
                    <input type="text" placeholder="ABC 1234" class="minimal-input" id="vPlate">
                </div>
            </div>

            <div class="form-row-compact triple">
                <div class="form-group-inner">
                    <label>Category</label>
                    <select class="minimal-input" id="vCategory" onchange="updateSeaters()">
                        <option value="" disabled selected>Select...</option>
                        <option value="Sedan">Sedan</option>
                        <option value="SUV">SUV</option>
                        <option value="Van">Van</option>
                    </select>
                </div>
                <div class="form-group-inner">
                    <label>Seaters</label>
                    <input type="text" id="vSeaters" class="minimal-input" readonly placeholder="---" style="background:#f3f4f6; font-weight:600; color:var(--accent-red);">
                </div>
                <div class="form-group-inner">
                    <label>Fuel</label>
                    <select class="minimal-input" id="vFuel">
                        <option value="" disabled selected>Select...</option>
                        <option value="Gasoline">Gasoline</option>
                        <option value="Diesel">Diesel</option>
                    </select>
                </div>
            </div>

            <div class="form-row-compact">
                <div class="form-group-inner">
                    <label>Driver Type</label>
                    <select class="minimal-input" id="vDriverType">
                        <option value="Self-Drive">Self-Drive</option>
                        <option value="With Driver">With Driver</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="step-content" id="opStep4">
            <h3>Upload Requirements</h3>
            <p class="compact-note">Upload clear photos of the vehicle and legal documents below.</p>

            <div class="upload-grid-compact">
                <div class="upload-item">
                    <label>Vehicle Photos</label>
                    <input type="file" id="up-photos" multiple>
                </div>
                <div class="upload-item">
                    <label>OR / CR</label>
                    <input type="file" id="up-or">
                </div>
                <div class="upload-item">
                    <label>Deed of Sale / Auth.</label>
                    <input type="file" id="up-deed">
                </div>
                <div class="upload-item">
                    <label>NBI Clearance</label>
                    <input type="file" id="up-nbi">
                </div>
                <div class="upload-item">
                    <label>Driver's License</label>
                    <input type="file" id="up-license">
                </div>
            </div>
        </div>

        <div class="op-footer" id="opFooter" style="display:none;">
            <button class="btn btn-text" id="btnBack" onclick="moveStep('back')">Back</button>
            <button class="btn btn-primary" id="btnNext" onclick="moveStep('next')">Next</button>
        </div>
    </div>
</div>

<section id="bookingDetailsPage" class="page-section" style="display: none; padding: 4rem 0; background: #fff;">
    <div style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">

        <div style="position: relative; border-radius: 20px; overflow: hidden; margin-bottom: 2.5rem; height: 400px; box-shadow: var(--shadow-soft);">
            <img id="detailCarImg" src="" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 40px; background: linear-gradient(transparent, rgba(0,0,0,0.8)); color: white;">
                <h1 id="detailCarNameHero" style="font-size: 2.5rem; margin: 0; font-weight: 800;"></h1>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 40px; align-items: start;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap;">
                     <div style="display: flex; align-items: center; gap: 1rem;">
                        <button onclick="goBackToResults()" title="Go Back" style="background: transparent; border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; font-size: 1rem; line-height: 40px; text-align: center;"><i class="fa-solid fa-arrow-left"></i></button>
                        <h2 id="detailCarName" style="font-size: 2rem; font-weight: 700; margin:0;"></h2>
                    </div>
                    <button class="btn btn-text" style="border: 1px solid var(--border-color); font-size: 0.75rem; font-weight: 700;">
                        <i class="fa-solid fa-circle-info"></i> SHOW GUIDELINES
                    </button>
                </div>

                <p style="color: var(--text-muted); margin-bottom: 2rem;">
                    <i class="fa-solid fa-location-dot" style="color: var(--accent-red);"></i> Batasan Hills, Quezon City, Metro Manila
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 3rem;">
                    <div class="spec-item"><i class="fa-solid fa-user" style="color:var(--accent-red); width: 25px;"></i> <span id="detailSeats"></span> seater</div>
                    <div class="spec-item"><i class="fa-solid fa-car" style="color:var(--accent-red); width: 25px;"></i> MPV</div>
                    <div class="spec-item"><i class="fa-solid fa-gears" style="color:var(--accent-red); width: 25px;"></i> <span id="detailTrans"></span></div>
                    <div class="spec-item"><i class="fa-solid fa-gas-pump" style="color:var(--accent-red); width: 25px;"></i> <span id="detailFuel"></span></div>
                    <div class="spec-item" style="color: var(--accent-red); font-weight: 700;">
                        <i class="fa-solid fa-calendar-xmark" style="width: 25px;"></i> Coding on Monday
                    </div>
                </div>

                <div style="border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden;">
                    <div onclick="openDateModal('pickup')" style="padding: 20px; border-bottom: 1px solid var(--border-color); cursor: pointer; display: flex; justify-content: space-between;">
                        <div>
                            <span id="finalPickup" style="display: block; font-weight: 700; font-size: 1.1rem;">-</span>
                            <small style="color: var(--text-muted);">Pick Up Date</small>
                        </div>
                        <i class="fa-solid fa-pen" style="color: var(--accent-red);"></i>
                    </div>
                    <div onclick="openDateModal('return')" style="padding: 20px; border-bottom: 1px solid var(--border-color); cursor: pointer; display: flex; justify-content: space-between;">
                        <div>
                            <span id="finalReturn" style="display: block; font-weight: 700; font-size: 1.1rem;">-</span>
                            <small style="color: var(--text-muted);">Return Date</small>
                        </div>
                        <i class="fa-solid fa-pen" style="color: var(--accent-red);"></i>
                    </div>
                    <div style="padding: 20px; background: #f9fafb;">
                        <span id="finalDuration" style="display: block; font-weight: 700;">1 day</span>
                        <small style="color: var(--text-muted);">Rental Length</small>
                    </div>
                </div>
            </div>

            <div style="text-align: right;">
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: #22c55e; font-size: 2rem; margin: 0;">PHP <span id="priceBase">0.00</span></h3>
                    <small style="color: var(--text-muted);">Full Payment Upon Pick Up</small>
                </div>
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: #22c55e; font-size: 2rem; margin: 0;">PHP <span id="priceFee">0.00</span></h3>
                    <small style="color: var(--text-muted);">Reservation Fee</small>
                </div>
                <div style="border-top: 2px solid var(--border-color); padding-top: 1.5rem; margin-bottom: 2.5rem;">
                    <h3 style="color: #22c55e; font-size: 2.5rem; margin: 0; font-weight: 800;">PHP <span id="priceTotal">0.00</span></h3>
                    <small style="color: var(--text-muted);">Total Rental Fee (Inclusive of VAT and fees)</small>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 15px; margin-bottom: 2rem; opacity: 0.6;">
                    <i class="fa-brands fa-cc-visa fa-2x"></i>
                    <i class="fa-brands fa-cc-mastercard fa-2x"></i>
                    <i class="fa-solid fa-qrcode fa-2x"></i>
                </div>

                <button class="btn btn-primary full-width" style="padding: 1.2rem; font-size: 1.1rem; border-radius: 12px; font-weight: 700;" onclick="finalizeBooking()">CONFIRM BOOKING</button>
            </div>
        </div>
    </div>
</section>

<?php
include __DIR__ . '/../includes/footer.php';
?>