<?php
// Authentication functions for Xcelrent Car Rental

session_start();

/**
 * Login function
 */
function login($email, $password) {
    global $pdo;
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user && verify_password($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            return true;
        }
        return false;
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

/**
 * Logout function
 */
function logout() {
    session_destroy();
    unset($_SESSION['user_id']);
    unset($_SESSION['user_email']);
    unset($_SESSION['user_name']);
    return true;
}

/**
 * Register function
 */
function register($first_name, $last_name, $email, $phone, $password) {
    global $pdo;
    
    try {
        // Check if user already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            return ['success' => false, 'message' => 'Email already exists'];
        }
        
        // Hash password
        $hashed_password = hash_password($password);
        
        // Insert new user
        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password) VALUES (?, ?, ?, ?, ?)");
        $result = $stmt->execute([$first_name, $last_name, $email, $phone, $hashed_password]);
        
        if ($result) {
            return ['success' => true, 'message' => 'Registration successful'];
        } else {
            return ['success' => false, 'message' => 'Registration failed'];
        }
    } catch (PDOException $e) {
        error_log("Registration error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Registration failed due to server error'];
    }
}

/**
 * Check if user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Get current user info
 */
function get_current_user() {
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, phone FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get user error: " . $e->getMessage());
            return null;
        }
    }
    return null;
}

/**
 * Require authentication
 */
function require_login() {
    if (!is_logged_in()) {
        header('Location: ../login.php');
        exit();
    }
}

/**
 * Generate CSRF token
 */
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token
 */
function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>