<?php
// Any PHP logic needed *before* the HTML starts goes here.
// For example, maybe fetching initial user data or search filters?
// e.g., $initial_users = get_initial_users();
?>
<!-- Renter Data -->
<section id="renters" class="tab-content active">
    <h1 class="page-title">Renter Data</h1>
    <p style="color: #666; margin-bottom: 20px;">Your customer database and security archives.</p>
    
    <div class="panel">
        <div style="margin-bottom: 20px;">
            <input type="text" id="userSearch" placeholder="Search by name or email..." style="width: 100%; padding: 10px; border: 2px solid #ddd; border-radius: 6px;">
        </div>
        <div id="usersContainer">
            <!-- Populated by API -->
        </div>
    </div>
</section>

<!-- At the bottom -->
<script src="assets/js/core.js"></script>
<script src="assets/js/manage_renters.js"></script>