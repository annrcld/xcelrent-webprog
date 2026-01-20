<?php
// public/pages/payment.php
$page_title = "Payment";
include '../includes/header.php';
?>

<style>
    .payment-container {
        max-width: 800px;
        margin: 4rem auto;
        padding: 0 2rem;
    }
    
    .payment-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    
    .payment-methods {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    
    .payment-option {
        border: 2px solid var(--border-color);
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .payment-option:hover {
        border-color: var(--accent-red);
    }
    
    .payment-option.selected {
        border-color: var(--accent-red);
        background: var(--accent-red-light);
    }
    
    .payment-form {
        background: white;
        padding: 2.5rem;
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 2rem;
    }
    
    .form-group {
        margin-bottom: 1.5rem;
    }
    
    .form-group label {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
        letter-spacing: 0.5px;
    }
    
    .form-control {
        width: 100%;
        padding: 0.8rem;
        border: 1px solid var(--border-color);
        border-radius: 6px;
        font-size: 1rem;
    }
    
    .card-details {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }
    
    .order-summary {
        background: var(--bg-secondary);
        padding: 2rem;
        border-radius: 16px;
    }
    
    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--border-color);
    }
    
    .total-row {
        font-weight: bold;
        font-size: 1.2rem;
        color: var(--accent-red);
        border-top: 2px solid var(--border-color);
        padding-top: 1rem;
        margin-top: 1rem;
    }
</style>

<div class="payment-container">
    <div class="payment-header">
        <h1>Payment Method</h1>
        <p>Choose your preferred payment method</p>
    </div>
    
    <div class="payment-methods">
        <div class="payment-option" onclick="selectPaymentMethod('credit_card')">
            <i class="fab fa-cc-visa fa-2x" style="color: var(--accent-red); margin-bottom: 0.5rem;"></i>
            <h3>Credit Card</h3>
            <p>Visa, Mastercard, Amex</p>
        </div>
        
        <div class="payment-option" onclick="selectPaymentMethod('debit_card')">
            <i class="fas fa-credit-card fa-2x" style="color: var(--accent-red); margin-bottom: 0.5rem;"></i>
            <h3>Debit Card</h3>
            <p>Pay directly from bank</p>
        </div>
        
        <div class="payment-option" onclick="selectPaymentMethod('bank_transfer')">
            <i class="fas fa-university fa-2x" style="color: var(--accent-red); margin-bottom: 0.5rem;"></i>
            <h3>Bank Transfer</h3>
            <p>Direct bank deposit</p>
        </div>
        
        <div class="payment-option" onclick="selectPaymentMethod('gcash')">
            <i class="fas fa-mobile-alt fa-2x" style="color: var(--accent-red); margin-bottom: 0.5rem;"></i>
            <h3>GCash</h3>
            <p>Mobile wallet payment</p>
        </div>
    </div>
    
    <div class="payment-form" id="paymentForm" style="display: none;">
        <h2 id="paymentTitle">Credit Card Information</h2>
        
        <div class="form-group">
            <label>Name on Card</label>
            <input type="text" class="form-control" id="cardName" placeholder="Enter name as it appears on card">
        </div>
        
        <div class="form-group">
            <label>Card Number</label>
            <input type="text" class="form-control" id="cardNumber" placeholder="0000 0000 0000 0000" maxlength="19">
        </div>
        
        <div class="card-details">
            <div class="form-group">
                <label>Expiration Date</label>
                <input type="text" class="form-control" id="expiryDate" placeholder="MM/YY" maxlength="5">
            </div>
            
            <div class="form-group">
                <label>CVV</label>
                <input type="text" class="form-control" id="cvv" placeholder="123" maxlength="3">
            </div>
        </div>
        
        <button class="btn btn-primary full-width" onclick="processPayment()" style="padding: 1rem; font-size: 1.1rem; font-weight: 700;">Complete Payment</button>
    </div>
    
    <div class="order-summary">
        <h3>Order Summary</h3>
        
        <div class="summary-row">
            <span>Selected Vehicle</span>
            <span id="selectedVehicle">-</span>
        </div>
        
        <div class="summary-row">
            <span>Rental Period</span>
            <span id="rentalPeriod">-</span>
        </div>
        
        <div class="summary-row">
            <span>Daily Rate</span>
            <span id="dailyRate">-</span>
        </div>
        
        <div class="summary-row">
            <span>Subtotal</span>
            <span id="subtotal">-</span>
        </div>
        
        <div class="summary-row">
            <span>Taxes & Fees</span>
            <span id="taxesFees">-</span>
        </div>
        
        <div class="summary-row total-row">
            <span>Total Amount</span>
            <span id="totalAmount">-</span>
        </div>
    </div>
</div>

<script>
let selectedPaymentMethod = null;

function selectPaymentMethod(method) {
    // Remove selected class from all options
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    event.currentTarget.classList.add('selected');
    
    selectedPaymentMethod = method;
    
    // Update form title based on selected method
    const titles = {
        'credit_card': 'Credit Card Information',
        'debit_card': 'Debit Card Information',
        'bank_transfer': 'Bank Transfer Details',
        'gcash': 'GCash Payment Details'
    };
    
    document.getElementById('paymentTitle').textContent = titles[method] || 'Payment Information';
    
    // Show payment form
    document.getElementById('paymentForm').style.display = 'block';
    
    // Scroll to payment form
    document.getElementById('paymentForm').scrollIntoView({ behavior: 'smooth' });
}

// Format card number as user types
document.getElementById('cardNumber')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 16) value = value.substring(0, 16);
    
    // Add spaces every 4 digits
    value = value.replace(/(\d{4})/g, '$1 ').trim();
    e.target.value = value;
});

// Format expiry date as user types
document.getElementById('expiryDate')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 4) value = value.substring(0, 4);
    
    // Add slash after month
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2);
    }
    e.target.value = value;
});

function processPayment() {
    if (!selectedPaymentMethod) {
        alert('Please select a payment method');
        return;
    }
    
    // Validate required fields based on payment method
    if (selectedPaymentMethod === 'credit_card' || selectedPaymentMethod === 'debit_card') {
        const cardName = document.getElementById('cardName').value;
        const cardNumber = document.getElementById('cardNumber').value;
        const expiryDate = document.getElementById('expiryDate').value;
        const cvv = document.getElementById('cvv').value;
        
        if (!cardName || !cardNumber || !expiryDate || !cvv) {
            alert('Please fill in all card details');
            return;
        }
        
        // Validate card number format (16 digits)
        if (cardNumber.replace(/\s/g, '').length !== 16) {
            alert('Card number must be 16 digits');
            return;
        }
        
        // Validate expiry date format (MM/YY)
        if (!/^\d{2}\/\d{2}$/.test(expiryDate)) {
            alert('Expiry date must be in MM/YY format');
            return;
        }
        
        // Validate CVV (3 digits)
        if (!/^\d{3}$/.test(cvv)) {
            alert('CVV must be 3 digits');
            return;
        }
    }
    
    // Process payment (in a real app, this would send to payment gateway)
    completeBooking();
}

function completeBooking() {
    // Show loading state
    const button = document.querySelector('[onclick="processPayment()"]');
    button.disabled = true;
    button.textContent = 'Processing...';
    
    // Simulate payment processing
    setTimeout(() => {
        // Clear session storage
        sessionStorage.removeItem('bookingDetails');
        sessionStorage.removeItem('selectedCar');
        
        // Show success message and redirect
        alert('Payment successful! Your booking is confirmed.');
        window.location.href = '?page=confirmation';
    }, 2000);
}

// Load booking summary from session storage
document.addEventListener('DOMContentLoaded', function() {
    const bookingDetails = sessionStorage.getItem('bookingDetails');
    if (bookingDetails) {
        const details = JSON.parse(bookingDetails);
        if (details.selectedCar) {
            document.getElementById('selectedVehicle').textContent = details.selectedCar.name;
            document.getElementById('dailyRate').textContent = '₱' + details.selectedCar.price.toLocaleString();
            document.getElementById('subtotal').textContent = '₱' + details.selectedCar.price.toLocaleString();
            document.getElementById('taxesFees').textContent = '₱' + (details.selectedCar.price * 0.12).toLocaleString();
            document.getElementById('totalAmount').textContent = '₱' + (details.selectedCar.price * 1.12).toLocaleString();
        }
    }
    
    // Set rental period (mock data)
    document.getElementById('rentalPeriod').textContent = '3 days';
});
</script>

<?php
include '../includes/footer.php';
?>