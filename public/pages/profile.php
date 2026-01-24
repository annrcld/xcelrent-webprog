<?php
// public/pages/profile.php
$page_title = "Profile Settings";

// Check if user is logged in
// Check if session is already active to avoid warnings
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    // If not logged in, we can't redirect because headers are already sent by the router
    // Instead, we'll show an access denied message
    return; // Exit early, the HTML below will show the access denied message
}

require_once __DIR__ . '/../includes/config.php';

// Get user details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT id, first_name, last_name, email, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    // User not found, show error message
?>
<div class="profile-container">
    <div class="profile-card">
        <h2>Error</h2>
        <p>User not found. Please contact support.</p>
        <p>Redirecting to home page...</p>
        <script>
            // Redirect to home page after a short delay
            setTimeout(function() {
                window.location.href = '?page=home';
            }, 2000); // Redirect after 2 seconds
        </script>
    </div>
</div>
<?php
    return; // Exit early to prevent showing the rest of the profile page
}

$stmt->close();
?>

<?php
// Show access denied message if user is not logged in
if (!isset($_SESSION['user_id'])) {
?>
<div class="profile-container">
    <div class="profile-card">
        <h2>Access Denied</h2>
        <p>You must be logged in to view your profile.</p>
        <p>Redirecting to home page...</p>
        <script>
            // Redirect to home page after a short delay
            setTimeout(function() {
                window.location.href = '?page=home';
            }, 2000); // Redirect after 2 seconds
        </script>
    </div>
</div>
<?php
    return; // Exit early to prevent showing the rest of the profile page
}
?>

<style>
    .profile-container {
        max-width: 800px;
        margin: 4rem auto;
        padding: 0 2rem;
    }

    .profile-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .profile-card {
        background: white;
        padding: 2.5rem;
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 2rem;
    }

    .form-section {
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 1px solid var(--border-color);
    }

    .form-section:last-child {
        border-bottom: none;
    }

    .form-section h3 {
        margin-bottom: 1.5rem;
        color: var(--text-dark);
        font-size: 1.2rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        letter-spacing: 0.5px;
    }

    .form-control {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 1rem;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--accent-red);
        box-shadow: 0 0 0 2px rgba(230, 56, 70, 0.1);
    }

    .verification-section {
        background: var(--bg-secondary);
        padding: 1.5rem;
        border-radius: 12px;
        margin: 1rem 0;
        display: none;
    }

    .verification-inputs {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 0.5rem;
        margin: 1rem 0;
    }

    .verification-input {
        text-align: center;
        font-size: 1.5rem;
        padding: 0.5rem;
        border: 1px solid var(--border-color);
        border-radius: 6px;
    }

    .btn-verification {
        display: flex;
        gap: 1rem;
        margin-top: 1rem;
    }

    .alert {
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1rem;
        display: none;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .alert-warning {
        background: #fff3cd;
        color: #856404;
        border: 1px solid #ffeaa7;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
        }
        
        .verification-inputs {
            grid-template-columns: repeat(3, 1fr);
        }
    }
</style>

<div class="profile-container">
    <div class="profile-header">
        <h1>Profile Settings</h1>
        <p>Manage your account information and preferences</p>
    </div>

    <div class="profile-card">
        <!-- Personal Information Section -->
        <div class="form-section">
            <h3>Personal Information</h3>
            
            <div class="alert" id="personalAlert"></div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" class="form-control" id="firstName" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" class="form-control" id="lastName" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                </div>
            </div>

            <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                <button type="button" class="btn btn-outline" style="margin-top: 0.5rem;" onclick="initiateVerification('email')">Verify Email Change</button>
            </div>

            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" class="form-control" id="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                <button type="button" class="btn btn-outline" style="margin-top: 0.5rem;" onclick="initiateVerification('phone')">Verify Phone Change</button>
            </div>

            <button class="btn btn-primary" onclick="updatePersonalInfo()">Save Changes</button>
        </div>

        <!-- Password Change Section -->
        <div class="form-section">
            <h3>Change Password</h3>
            
            <div class="alert" id="passwordAlert"></div>
            
            <div class="form-group">
                <label>Current Password</label>
                <input type="password" class="form-control" id="currentPassword" required>
            </div>

            <div class="form-group">
                <label>New Password</label>
                <input type="password" class="form-control" id="newPassword" required>
            </div>

            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" class="form-control" id="confirmNewPassword" required>
            </div>

            <button class="btn btn-primary" onclick="changePassword()">Change Password</button>
        </div>
    </div>
</div>

<!-- Verification Modal -->
<div id="verificationModal" class="modal" style="display: none;">
    <div class="modal-content" style="width: 90%; max-width: 500px; padding: 2rem;">
        <span class="close" onclick="closeVerificationModal()">&times;</span>
        <h3 id="verificationTitle">Verify Your Identity</h3>
        <p id="verificationMessage">Enter the code sent to your email/phone to verify the change.</p>
        
        <div class="verification-inputs" id="verificationInputs">
            <input type="text" class="verification-input" maxlength="1" oninput="moveToNext(this, 0)">
            <input type="text" class="verification-input" maxlength="1" oninput="moveToNext(this, 1)">
            <input type="text" class="verification-input" maxlength="1" oninput="moveToNext(this, 2)">
            <input type="text" class="verification-input" maxlength="1" oninput="moveToNext(this, 3)">
            <input type="text" class="verification-input" maxlength="1" oninput="moveToNext(this, 4)">
            <input type="text" class="verification-input" maxlength="1" oninput="moveToNext(this, 5)">
        </div>
        
        <div class="btn-verification">
            <button class="btn btn-outline" onclick="resendVerificationCode()">Resend Code</button>
            <button class="btn btn-primary" onclick="verifyCode()">Verify Code</button>
        </div>
        
        <p style="margin-top: 1rem; font-size: 0.9rem; color: var(--text-muted);">Code expires in <span id="countdown">5:00</span></p>
    </div>
</div>

<script>
let verificationType = null;
let verificationTarget = null;
let countdownInterval = null;

function showAlert(elementId, message, type) {
    const alertElement = document.getElementById(elementId);
    alertElement.textContent = message;
    alertElement.className = `alert alert-${type}`;
    alertElement.style.display = 'block';
    
    // Auto hide after 5 seconds
    setTimeout(() => {
        alertElement.style.display = 'none';
    }, 5000);
}

function initiateVerification(type) {
    verificationType = type;
    const currentValue = type === 'email' ? document.getElementById('email').value : document.getElementById('phone').value;
    
    // Check if value has changed
    if (currentValue === '<?php echo addslashes($user['email']); ?>' && type === 'email') {
        showAlert('personalAlert', 'Email has not changed', 'warning');
        return;
    }
    
    if (currentValue === '<?php echo addslashes($user['phone']); ?>' && type === 'phone') {
        showAlert('personalAlert', 'Phone number has not changed', 'warning');
        return;
    }
    
    // Send verification request
    fetch('/project_xcelrent/public/api/send_verification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            type: type,
            value: currentValue
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            verificationTarget = currentValue;
            document.getElementById('verificationTitle').textContent = `Verify ${type === 'email' ? 'Email' : 'Phone'} Change`;
            document.getElementById('verificationMessage').textContent = `A verification code has been sent to your ${type}. Enter the code below to verify.`;
            document.getElementById('verificationModal').style.display = 'block';
            
            startCountdown();
        } else {
            showAlert('personalAlert', data.message || 'Failed to send verification code', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('personalAlert', 'An error occurred while sending verification code', 'error');
    });
}

function startCountdown() {
    let totalSeconds = 300; // 5 minutes
    
    clearInterval(countdownInterval);
    countdownInterval = setInterval(() => {
        const minutes = Math.floor(totalSeconds / 60);
        const seconds = totalSeconds % 60;
        document.getElementById('countdown').textContent = `${minutes}:${seconds.toString().padStart(2, '0')}`;
        
        if (totalSeconds <= 0) {
            clearInterval(countdownInterval);
            document.getElementById('countdown').textContent = 'Expired';
        }
        
        totalSeconds--;
    }, 1000);
}

function moveToNext(current, index) {
    if (current.value.length === 1 && index < 5) {
        document.querySelectorAll('.verification-input')[index + 1].focus();
    }
}

function verifyCode() {
    const inputs = document.querySelectorAll('.verification-input');
    const code = Array.from(inputs).map(input => input.value).join('');
    
    if (code.length !== 6) {
        showAlert('personalAlert', 'Please enter the complete 6-digit code', 'error');
        return;
    }
    
    fetch('/project_xcelrent/public/api/verify_code.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            type: verificationType,
            code: code,
            target: verificationTarget
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeVerificationModal();
            showAlert('personalAlert', 'Verification successful! You can now save your changes.', 'success');
        } else {
            showAlert('personalAlert', data.message || 'Invalid verification code', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('personalAlert', 'An error occurred during verification', 'error');
    });
}

function resendVerificationCode() {
    fetch('/project_xcelrent/public/api/send_verification.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            type: verificationType,
            value: verificationTarget
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('personalAlert', 'Verification code resent successfully', 'success');
            startCountdown(); // Restart the countdown
        } else {
            showAlert('personalAlert', data.message || 'Failed to resend verification code', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('personalAlert', 'An error occurred while resending verification code', 'error');
    });
}

function closeVerificationModal() {
    document.getElementById('verificationModal').style.display = 'none';
    // Clear inputs
    document.querySelectorAll('.verification-input').forEach(input => input.value = '');
    clearInterval(countdownInterval);
    document.getElementById('countdown').textContent = '5:00';
}

function updatePersonalInfo() {
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();

    // Basic validation
    if (!firstName || !lastName || !email || !phone) {
        showAlert('personalAlert', 'Please fill in all fields', 'error');
        return;
    }

    if (!isValidEmail(email)) {
        showAlert('personalAlert', 'Please enter a valid email address', 'error');
        return;
    }

    if (!isValidPhone(phone)) {
        showAlert('personalAlert', 'Please enter a valid phone number (09XXXXXXXXX)', 'error');
        return;
    }

    // Check if any changes were made
    if (firstName === '<?php echo addslashes($user['first_name']); ?>' &&
        lastName === '<?php echo addslashes($user['last_name']); ?>' &&
        email === '<?php echo addslashes($user['email']); ?>' &&
        phone === '<?php echo addslashes($user['phone']); ?>') {
        showAlert('personalAlert', 'No changes made', 'warning');
        return;
    }

    // Check if email or phone has been verified if they've changed
    const originalEmail = '<?php echo addslashes($user['email']); ?>';
    const originalPhone = '<?php echo addslashes($user['phone']); ?>';
    let needsVerification = false;

    if (email !== originalEmail) {
        // Check if email has been verified
        fetch('/project_xcelrent/public/api/check_verification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                type: 'email',
                value: email
            })
        })
        .then(response => response.json())
        .then(data => {
            if (!data.verified) {
                showAlert('personalAlert', 'Please verify your email address before saving changes', 'error');
                return;
            }

            // If phone also changed, check its verification
            if (phone !== originalPhone) {
                return fetch('/project_xcelrent/public/api/check_verification.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        type: 'phone',
                        value: phone
                    })
                }).then(response => response.json());
            }

            return Promise.resolve({verified: true});
        })
        .then(data => {
            if (data && data.verified) {
                // Both email and phone are verified, proceed with update
                performUpdate(firstName, lastName, email, phone);
            } else if (data && !data.verified) {
                showAlert('personalAlert', 'Please verify your phone number before saving changes', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('personalAlert', 'An error occurred while checking verification', 'error');
        });
    } else if (phone !== originalPhone) {
        // Only phone changed, check its verification
        fetch('/project_xcelrent/public/api/check_verification.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                type: 'phone',
                value: phone
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.verified) {
                // Phone is verified, proceed with update
                performUpdate(firstName, lastName, email, phone);
            } else {
                showAlert('personalAlert', 'Please verify your phone number before saving changes', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('personalAlert', 'An error occurred while checking verification', 'error');
        });
    } else {
        // No email or phone changes, proceed with update
        performUpdate(firstName, lastName, email, phone);
    }
}

function performUpdate(firstName, lastName, email, phone) {
    fetch('/project_xcelrent/public/api/update_profile.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            first_name: firstName,
            last_name: lastName,
            email: email,
            phone: phone
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('personalAlert', 'Profile updated successfully!', 'success');
            // Optionally reload the page to show updated values
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showAlert('personalAlert', data.message || 'Failed to update profile', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('personalAlert', 'An error occurred while updating profile', 'error');
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('verificationModal');
    if (event.target == modal) {
        closeVerificationModal();
    }
}

function changePassword() {
    const currentPassword = document.getElementById('currentPassword').value;
    const newPassword = document.getElementById('newPassword').value;
    const confirmNewPassword = document.getElementById('confirmNewPassword').value;
    
    if (!currentPassword || !newPassword || !confirmNewPassword) {
        showAlert('passwordAlert', 'Please fill in all password fields', 'error');
        return;
    }
    
    if (newPassword !== confirmNewPassword) {
        showAlert('passwordAlert', 'New passwords do not match', 'error');
        return;
    }
    
    if (newPassword.length < 8) {
        showAlert('passwordAlert', 'New password must be at least 8 characters long', 'error');
        return;
    }
    
    // Send password change request
    fetch('/project_xcelrent/public/api/change_password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            current_password: currentPassword,
            new_password: newPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('passwordAlert', 'Password changed successfully!', 'success');
            // Clear password fields
            document.getElementById('currentPassword').value = '';
            document.getElementById('newPassword').value = '';
            document.getElementById('confirmNewPassword').value = '';
        } else {
            showAlert('passwordAlert', data.message || 'Failed to change password', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('passwordAlert', 'An error occurred while changing password', 'error');
    });
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function isValidPhone(phone) {
    const re = /^09\d{9}$/;
    return re.test(phone);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('verificationModal');
    if (event.target == modal) {
        closeVerificationModal();
    }
}
</script>