<?php
// public/pages/booking.php
$page_title = "Book a Car";

// Get car ID from URL parameter
$car_id = $_GET['car_id'] ?? null;
?>

<style>
    .booking-container {
        max-width: 1000px;
        margin: 4rem auto;
        padding: 0 2rem;
    }

    .booking-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }

    .booking-steps {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
    }

    .booking-steps::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--border-color);
        z-index: 1;
    }

    .step {
        text-align: center;
        position: relative;
        z-index: 2;
    }

    .step-number {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--input-bg);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-weight: bold;
        color: var(--text-muted);
    }

    .step.active .step-number {
        background: var(--accent-red);
        color: white;
    }

    .step.active .step-label {
        color: var(--accent-red);
        font-weight: 600;
    }

    .booking-form {
        background: white;
        padding: 2.5rem;
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 2rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 1.5rem;
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

    .summary-section {
        background: var(--bg-secondary);
        padding: 2rem;
        border-radius: 16px;
        margin-top: 2rem;
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

<div class="booking-container">
    <div class="booking-header">
        <h1>Complete Your Booking</h1>
        <p>Fill in the details below to reserve your vehicle</p>
    </div>

    <div class="booking-steps">
        <div class="step active">
            <div class="step-number">1</div>
            <div class="step-label">Vehicle</div>
        </div>
        <div class="step">
            <div class="step-number">2</div>
            <div class="step-label">Details</div>
        </div>
        <div class="step">
            <div class="step-number">3</div>
            <div class="step-label">Payment</div>
        </div>
        <div class="step">
            <div class="step-number">4</div>
            <div class="step-label">Confirmation</div>
        </div>
    </div>

    <div class="booking-form">
        <div class="form-row">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" placeholder="Enter your first name" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" placeholder="Enter your last name" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" class="form-control" placeholder="Enter your phone number" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Pick-up Location</label>
                <select class="form-control">
                    <option value="">Select pick-up location</option>
                    <option value="quezon_city">Quezon City Branch</option>
                    <option value="makati">Makati Branch</option>
                    <option value="pasig">Pasig Branch</option>
                </select>
            </div>
            <div class="form-group">
                <label>Return Location</label>
                <select class="form-control">
                    <option value="">Select return location</option>
                    <option value="quezon_city">Quezon City Branch</option>
                    <option value="makati">Makati Branch</option>
                    <option value="pasig">Pasig Branch</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Special Requests</label>
            <textarea class="form-control" placeholder="Any special requests or notes..."></textarea>
        </div>

        <button class="btn btn-primary full-width" onclick="proceedToPayment()" style="padding: 1rem; font-size: 1.1rem; font-weight: 700;">Proceed to Payment</button>
    </div>

    <div class="summary-section">
        <h3>Booking Summary</h3>

        <div class="summary-row">
            <span>Selected Vehicle</span>
            <span id="selectedVehicle">-</span>
        </div>

        <div class="summary-row">
            <span>Pick-up Date</span>
            <span id="pickupDate">-</span>
        </div>

        <div class="summary-row">
            <span>Return Date</span>
            <span id="returnDate">-</span>
        </div>

        <div class="summary-row">
            <span>Rental Days</span>
            <span id="rentalDays">-</span>
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
// Get URL parameters
const urlParams = new URLSearchParams(window.location.search);
const carId = urlParams.get('car_id');

// Load car details if car_id is provided
if (carId) {
    loadCarDetails(carId);
}

function loadCarDetails(carId) {
    // In a real implementation, this would fetch car details from the API
    // For now, we'll use a mock implementation
    const mockCars = [
        {
            id: 1,
            name: "Toyota Vios 2026",
            price: 1500
        },
        {
            id: 2,
            name: "Isuzu Sportivo X 2014",
            price: 1799
        },
        {
            id: 3,
            name: "Toyota Innova 2026",
            price: 3500
        }
    ];

    const car = mockCars.find(c => c.id == carId);
    if (car) {
        document.getElementById('selectedVehicle').textContent = car.name;
        // Store car details in sessionStorage for later use
        sessionStorage.setItem('selectedCar', JSON.stringify(car));
    }
}

function proceedToPayment() {
    // Validate form
    const firstName = document.querySelector('input[placeholder="Enter your first name"]').value;
    const lastName = document.querySelector('input[placeholder="Enter your last name"]').value;
    const email = document.querySelector('input[placeholder="Enter your email"]').value;
    const phone = document.querySelector('input[placeholder="Enter your phone number"]').value;

    if (!firstName || !lastName || !email || !phone) {
        alert('Please fill in all required fields');
        return;
    }

    // Save booking details to sessionStorage
    const bookingDetails = {
        firstName,
        lastName,
        email,
        phone,
        selectedCar: JSON.parse(sessionStorage.getItem('selectedCar') || '{}')
    };

    sessionStorage.setItem('bookingDetails', JSON.stringify(bookingDetails));

    // Redirect to payment page
    window.location.href = '?page=payment';
}

// Load saved data if returning from another page
document.addEventListener('DOMContentLoaded', function() {
    const savedBooking = sessionStorage.getItem('bookingDetails');
    if (savedBooking) {
        const details = JSON.parse(savedBooking);
        if (details.firstName) {
            document.querySelector('input[placeholder="Enter your first name"]').value = details.firstName;
        }
        if (details.lastName) {
            document.querySelector('input[placeholder="Enter your last name"]').value = details.lastName;
        }
        if (details.email) {
            document.querySelector('input[placeholder="Enter your email"]').value = details.email;
        }
        if (details.phone) {
            document.querySelector('input[placeholder="Enter your phone number"]').value = details.phone;
        }
    }
});
</script>

<?php
include '../includes/footer.php';
?>