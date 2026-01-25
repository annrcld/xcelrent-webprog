i love my wife
Xcelrent Car Rental System - Setup Guide
Prerequisites
XAMPP (Apache, MySQL, PHP)
Web browser
Database management tool (phpMyAdmin included with XAMPP)
Installation Steps

1. Install XAMPP
Download XAMPP from https://www.apachefriends.org/
Install with default settings
Important: MySQL will use port 3306 by default
2. Configure MySQL Port (if needed)
If port 3306 is already in use or you prefer a different port:

Open XAMPP Control Panel
Click “Config” next to MySQL
Select “my.ini” or “my.cnf”
Find the line port=3306 and change to port=3307 (or preferred port)
Save and restart MySQL service
3. Deploy Project Files
Copy the entire project_xcelrent folder
Paste it into your XAMPP htdocs directory:
C:\xampp\htdocs\project_xcelrent\
(or wherever your XAMPP is installed)
4. Import Database
Option A: Using phpMyAdmin (Recommended)
Start Apache and MySQL services in XAMPP Control Panel
Open your browser and navigate to http://localhost/phpmyadmin
Click “Databases” tab
In the “Create database” section:
Enter database name: xcelrent_car_rental
Click “Create”
Click on the newly created database
Click “Import” tab
Click “Choose File” and select your exported SQL file
Click “Go” to import the database
Option B: Using Command Line
Open Command Prompt/Terminal
Navigate to MySQL bin directory:
cd C:\xampp\mysql\bin
Import the database:
mysql -u root -p xcelrent_car_rental < "path_to_your_sql_file.sql"
5. Configure Database Connection
For Admin Panel:
Edit the file: project_xcelrent\admin\includes\config.php

<?php
// admin/includes/config.php

// 1. Start the session first thing
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Database Configuration
$servername = "localhost";
$username = "root";
$password = "";  // Leave empty if no password set for root
$dbname = "xcelrent_car_rental";
$port = 3306;    // Change to 3307 if you configured a different port

// 3. Create connection using specific port variable
$conn = new mysqli($servername, $username, $password, $dbname, $port);

// 4. Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 5. Set charset
mysqli_set_charset($conn, "utf8");

// 6. Define base URLs
define('BASE_URL', '/project_xcelrent/admin/');
define('ASSETS_URL', BASE_URL . 'assets/');
define('API_URL', BASE_URL . 'api/');
?>
For Public Site:
Edit the file: project_xcelrent\includes\config.php

<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');      // Leave empty if no password set for root
define('DB_NAME', 'xcelrent_car_rental');

// Connection - adjust port if needed
try {
    $pdo = new PDO("mysql:host=localhost;dbname=xcelrent_car_rental", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $db_error = $e->getMessage();
    $pdo = null;
}

// Site Configuration
define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/project_xcelrent/public/');
define('SITE_NAME', 'Xcelrent Car Rental');

// Other configurations
define('DEFAULT_CURRENCY', 'PHP');
define('TIMEZONE', 'Asia/Manila');
date_default_timezone_set(TIMEZONE);
?>
6. Start Services
Open XAMPP Control Panel
Start Apache and MySQL services
Make sure both services show green status indicators
7. Access the System
Public Website:
Open browser and go to: http://localhost/project_xcelrent/public/
Admin Panel:
Open browser and go to: http://localhost/project_xcelrent/admin/
8. Default Admin Login
Username: admin (or check your imported database for existing admin accounts)
Password: password (or check your imported database for existing admin accounts)
9. Troubleshooting
Common Issues:
“Connection failed” error:

Verify MySQL service is running
Check port number in config files matches your MySQL port
Verify database name is correct
“Page not found” error:

Ensure project folder is in correct htdocs location
Check that Apache service is running
Database import fails:

Ensure database name matches exactly: xcelrent_car_rental
Check SQL file is not corrupted
Permission errors:

Ensure XAMPP has proper write permissions
Check that upload directories exist and are writable
10. Security Recommendations
Change default admin credentials immediately
Set a password for MySQL root user
Configure proper file permissions
Regular database backups
11. Port Configuration Notes
If using port 3306 (default), no changes needed in config files
If using port 3307 (as mentioned in original config), ensure $port = 3307 in admin config
Update port numbers in config files to match your MySQL configuration
Support
If you encounter issues during setup:

Verify all services are running in XAMPP Control Panel
Check error logs in XAMPP
Ensure file paths are correct
Confirm database import completed successfully
The system is now ready for use on your new computer!