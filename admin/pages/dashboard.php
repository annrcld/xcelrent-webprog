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

<?php
// Re-open PHP tag here if you need more PHP logic *after* this HTML block
// to generate content, handle forms, etc.
?>