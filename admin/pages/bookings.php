<?php
// Any PHP logic needed *before* the HTML starts goes here.
// For example, maybe fetching initial booking statuses for filters?
// e.g., $booking_statuses = get_booking_statuses();
?>
<!-- Manage Bookings -->
<section id="bookings" class="tab-content active">
    <h1 class="page-title">Manage Bookings</h1>
    <p style="color: #666; margin-bottom: 20px;">The transaction hub where you verify the ₱500 GCash deposits.</p>
    
    <div class="panel">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Car Name</th>
                    <th>Renter Name</th>
                    <th>Rental Dates</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="bookingsTableBody">
                <!-- Populated by API -->
            </tbody>
        </table>
    </div>
</section>

<!-- At the bottom -->
<script src="assets/js/core.js"></script>
<script src="assets/js/manage_bookings.js"></script>