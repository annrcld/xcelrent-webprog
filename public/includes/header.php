<?php
// public/includes/header.php
require_once __DIR__ . '/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Xcelrent Car Rental</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/project_xcelrent/public/assets/css/style.css">
</head>
<body>

<!-- Common Navigation Bar -->
<nav>
    <div class="nav-container">
        <div class="logo" onclick="window.location.href='?page=home'">Xcelrent<span class="dot">.</span></div>
        <div class="nav-links">
            <a href="?page=home"<?php echo (isset($_GET['page']) && $_GET['page'] === 'home') || !isset($_GET['page']) ? ' class="active"' : ''; ?>>Home</a>
            <a href="?page=about"<?php echo isset($_GET['page']) && $_GET['page'] === 'about' ? ' class="active"' : ''; ?>>About</a>
            <a href="?page=home#testimonials" onclick="if(window.location.search.indexOf('page=home') !== -1) { scrollToSection('testimonials'); return false; }">Reviews</a>
            <a href="?page=contact"<?php echo isset($_GET['page']) && $_GET['page'] === 'contact' ? ' class="active"' : ''; ?>>Contact</a>
             <div class="divider-vertical"></div>
            <div class="nav-auth-group">
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

<!-- Common Modals -->
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