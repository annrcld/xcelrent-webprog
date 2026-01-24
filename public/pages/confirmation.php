<?php
// public/pages/confirmation.php
$page_title = "Booking Confirmed";
?>

<style>
    .confirmation-container {
        max-width: 800px;
        margin: 4rem auto;
        padding: 0 2rem;
        text-align: center;
    }
    
    .confirmation-content {
        background: white;
        padding: 3rem;
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        margin: 2rem 0;
    }
    
    .confirmation-icon {
        font-size: 4rem;
        color: #22c55e;
        margin-bottom: 1.5rem;
    }
    
    .booking-details {
        background: var(--bg-secondary);
        padding: 2rem;
        border-radius: 12px;
        margin: 2rem 0;
        text-align: left;
    }
    
    .detail-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    
    .actions {
        display: flex;
        gap: 1rem;
        justify-content: center;
        margin-top: 2rem;
    }
    
    .btn-outline {
        background: transparent;
        border: 1px solid var(--accent-red);
        color: var(--accent-red);
    }
    
    .btn-outline:hover {
        background: var(--accent-red);
        color: white;
    }
</style>

<div class="confirmation-container">
    <div class="confirmation-content">
        <div class="confirmation-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        
        <h1>Booking Confirmed!</h1>
        <p style="color: var(--text-muted); font-size: 1.1rem; margin: 1rem 0;">Your reservation has been successfully processed.</p>
        
        <div class="booking-details">
            <h3 style="margin-bottom: 1.5rem;">Booking Details</h3>
            
            <div class="detail-row">
                <span>Booking Reference</span>
                <span id="bookingReference">#XCR-XXXXXXXX</span>
            </div>
            
            <div class="detail-row">
                <span>Customer Name</span>
                <span id="customerName">-</span>
            </div>
            
            <div class="detail-row">
                <span>Vehicle</span>
                <span id="vehicleName">-</span>
            </div>
            
            <div class="detail-row">
                <span>Pick-up Date</span>
                <span id="pickupDate">-</span>
            </div>
            
            <div class="detail-row">
                <span>Return Date</span>
                <span id="returnDate">-</span>
            </div>
            
            <div class="detail-row">
                <span>Total Amount Paid</span>
                <span id="totalAmount">-</span>
            </div>
        </div>
        
        <p style="margin: 1.5rem 0; color: var(--text-muted);">A confirmation email has been sent to your email address with all the booking details.</p>
        
        <div class="actions">
            <button class="btn btn-primary" onclick="window.print()">Print Receipt</button>
            <button class="btn btn-outline" onclick="window.location.href = '?page=home'">Back to Home</button>
        </div>
    </div>
</div>

<script>
// Load booking details from the API
document.addEventListener('DOMContentLoaded', function() {
    // Get booking ID from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const bookingId = urlParams.get('booking_id');

    if (bookingId) {
        fetch(`/project_xcelrent/public/api/get_booking_details.php?id=${bookingId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const booking = data.data;

                    // Update booking details
                    document.getElementById('customerName').textContent = booking.customer_name;
                    document.getElementById('vehicleName').textContent = booking.vehicle;
                    document.getElementById('pickupDate').textContent = booking.pickup_date;
                    document.getElementById('returnDate').textContent = booking.return_date;
                    document.getElementById('totalAmount').textContent = booking.total_amount;

                    // Update booking reference
                    document.getElementById('bookingReference').textContent = booking.reference;
                } else {
                    console.error('Error loading booking details:', data.message);
                    alert('Error loading booking details: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error fetching booking details:', error);
                console.error('Error name:', error.name);
                console.error('Error message:', error.message);

                // Check if it's a network error
                if (error instanceof TypeError && error.message.includes('fetch')) {
                    alert('Network error: Could not connect to the server. Please check your connection and try again.');
                } else {
                    alert('Error loading booking details: ' + error.message + '. Please try again.');
                }
            });
    } else {
        console.warn('No booking ID found in URL');
        alert('No booking ID found. Please go back and complete a booking first.');
    }
});
</script>