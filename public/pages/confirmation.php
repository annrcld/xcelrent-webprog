<?php
// public/pages/confirmation.php
$page_title = "Booking Confirmed";
include '../includes/header.php';
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
                <span>#XCR-<?php echo date('Ymd') . rand(1000, 9999); ?></span>
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
// Load booking details from session or mock data
document.addEventListener('DOMContentLoaded', function() {
    // In a real implementation, this would fetch from the database
    // For now, we'll use mock data
    document.getElementById('customerName').textContent = 'John Doe';
    document.getElementById('vehicleName').textContent = 'Toyota Vios 2026';
    document.getElementById('pickupDate').textContent = 'Jan 25, 2026 at 10:00 AM';
    document.getElementById('returnDate').textContent = 'Jan 28, 2026 at 6:00 PM';
    document.getElementById('totalAmount').textContent = '₱5,340.00';
});
</script>

<?php
include '../includes/footer.php';
?>