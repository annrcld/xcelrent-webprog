<?php
// public/pages/reset_password.php
$page_title = "Reset Password";

require_once __DIR__ . '/../includes/config.php';

// Get token from URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    header('Location: ?page=home');
    exit;
}

// Check if token is valid
$stmt = $conn->prepare("SELECT prt.*, u.email FROM password_reset_tokens prt JOIN users u ON prt.user_id = u.id WHERE prt.token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();
$token_data = $result->fetch_assoc();

if (!$token_data || strtotime($token_data['expires_at']) < time()) {
    // Token is invalid or expired
    $error_message = "Invalid or expired reset token. Please request a new password reset.";
} else {
    $user_email = $token_data['email'];
}
?>

<style>
    .reset-password-container {
        max-width: 500px;
        margin: 4rem auto;
        padding: 0 2rem;
    }

    .reset-password-card {
        background: white;
        padding: 2.5rem;
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        text-align: center;
    }

    .form-group {
        margin-bottom: 1.5rem;
        text-align: left;
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

    .alert {
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1rem;
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
</style>

<div class="reset-password-container">
    <div class="reset-password-card">
        <h2>Reset Your Password</h2>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
            <a href="?page=home" class="btn btn-primary">Back to Home</a>
        <?php else: ?>
            <p>Enter your new password for <?php echo htmlspecialchars($user_email); ?></p>
            
            <div id="messageDiv"></div>
            
            <div class="form-group">
                <label>New Password</label>
                <input type="password" class="form-control" id="newPassword" placeholder="Enter new password" required>
            </div>
            
            <div class="form-group">
                <label>Confirm New Password</label>
                <input type="password" class="form-control" id="confirmPassword" placeholder="Confirm new password" required>
            </div>
            
            <button class="btn btn-primary full-width" onclick="resetPassword('<?php echo addslashes($token); ?>')">Reset Password</button>
        <?php endif; ?>
    </div>
</div>

<script>
function showAlert(message, type) {
    const messageDiv = document.getElementById('messageDiv');
    messageDiv.innerHTML = `<div class="alert alert-${type}">${message}</div>`;
}

function resetPassword(token) {
    const newPassword = document.getElementById('newPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    
    if (!newPassword || !confirmPassword) {
        showAlert('Please fill in both password fields', 'error');
        return;
    }
    
    if (newPassword !== confirmPassword) {
        showAlert('Passwords do not match', 'error');
        return;
    }
    
    if (newPassword.length < 8) {
        showAlert('Password must be at least 8 characters long', 'error');
        return;
    }
    
    fetch('/project_xcelrent/public/api/reset_password.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            token: token,
            new_password: newPassword
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            // Redirect after a delay
            setTimeout(() => {
                window.location.href = '?page=home';
            }, 2000);
        } else {
            showAlert(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while resetting password', 'error');
    });
}
</script>