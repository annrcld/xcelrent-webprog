<?php
// Any PHP initialization or logic needed *before* the HTML goes here.
// For example, maybe loading user data, fetching recent activity counts, etc.
// e.g., require_once '../includes/config.php'; // If needed here specifically
// e.g., $user_role = $_SESSION['role'] ?? null;

// After any necessary initial PHP code, close the PHP tag to allow HTML output
?>
<!-- Dashboard -->
<section id="dashboard" class="tab-content active">
    <h1 class="page-title">Command Center</h1>
    <div class="kpi-row" id="kpiRow">
        </div>
    <div class="dashboard-grid" id="recentActivityPanel">
        </div>
</section>

<!-- At the bottom -->
<script src="assets/js/core.js"></script>
<script src="assets/js/dashboard.js"></script>