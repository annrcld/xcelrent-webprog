<aside class="sidebar">
    <div class="logo-area">
        <i data-lucide="car" class="logo-icon"></i>
        <div class="logo-text">XCEL<span>RENT</span></div>
    </div>

    <nav class="sidebar-nav">
        <a href="index.php?page=dashboard" class="nav-btn <?php echo ($page == 'dashboard') ? 'active' : ''; ?>">
            <i data-lucide="layout-dashboard"></i> Dashboard
        </a>
        <a href="index.php?page=add_car" class="nav-btn <?php echo ($page == 'add_car') ? 'active' : ''; ?>">
            <i data-lucide="plus-square"></i> Add Car
        </a>
        <a href="index.php?page=manage_cars" class="nav-btn <?php echo ($page == 'manage_cars') ? 'active' : ''; ?>">
            <i data-lucide="list"></i> Manage Cars
        </a>
        <a href="index.php?page=bookings" class="nav-btn <?php echo ($page == 'bookings') ? 'active' : ''; ?>">
            <i data-lucide="calendar-check"></i> Manage Bookings
        </a>
        <a href="index.php?page=renters" class="nav-btn <?php echo ($page == 'renters') ? 'active' : ''; ?>">
            <i data-lucide="users"></i> Renter Data
        </a>
        <a href="index.php?page=operators" class="nav-btn <?php echo ($page == 'operators') ? 'active' : ''; ?>">
            <i data-lucide="shield-check"></i> Operators
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php" class="nav-btn logout-link" onclick="return confirm('Are you sure you want to log out?')">
            <i data-lucide="log-out"></i> Logout
        </a>
    </div>
</aside>

<main class="main-content">