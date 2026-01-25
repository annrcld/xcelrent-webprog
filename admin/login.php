<?php
// admin/login.php
require_once 'includes/config.php';

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // For initial setup, we use plain text comparison. 
    // Recommended: Use password_hash() and password_verify() in production.
    $stmt = $conn->prepare("SELECT id, password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($pass === $row['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $row['id'];
            header("Location: index.php");
            exit();
        } else {
            $error = "Invalid password.";
        }
    } else {
        $error = "Admin not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Xcelrent | Admin Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('assets/img/login-background.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            border-top: 5px solid #dc2626;
            backdrop-filter: blur(10px);
        }
        .login-card h2 { text-align: center; margin-bottom: 25px; font-weight: 800; letter-spacing: -1px; }
        .login-card h2 span { color: #dc2626; }
        .error-msg { color: #dc2626; background: #ffeef0; padding: 10px; border-radius: 5px; margin-bottom: 15px; font-size: 14px; text-align: center; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2 class="logo-text" style="text-align: center; margin-bottom: 25px;">Xcelrent<span class="dot">.</span></h2>
        <?php if($error): ?> <div class="error-msg"><?php echo $error; ?></div> <?php endif; ?>
        <form method="POST">
            <div style="margin-bottom: 15px;">
                <label>Username</label>
                <input type="text" name="username" required placeholder="Enter username">
            </div>
            <div style="margin-bottom: 20px;">
                <label>Password</label>
                <input type="password" name="password" required placeholder="Enter password">
            </div>
            <button type="submit" class="btn btn-red submit-btn">Access Command Center</button>
        </form>
    </div>
</body>
</html>