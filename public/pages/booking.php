<?php
// public/pages/booking.php
$page_title = "Book a Car";

// Get car ID from URL parameter
$car_id = $_GET['car_id'] ?? null;
$pickup_date = $_GET['pickup'] ?? null;
$return_date = $_GET['return'] ?? null;

// Store dates in session for later use
if ($pickup_date) {
    $_SESSION['pickup_date'] = $pickup_date;
}
if ($return_date) {
    $_SESSION['return_date'] = $return_date;
}
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
    
    .payment-methods {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin: 2rem 0;
    }
    
    .payment-option {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        border: 2px solid #eee;
    }
    
    .payment-option:hover {
        border-color: var(--accent-red);
        transform: translateY(-2px);
    }
    
    .payment-option.selected {
        border-color: var(--accent-red);
        background: #fff5f5;
    }
    
    .qr-container {
        display: none;
        text-align: center;
        margin: 2rem 0;
        padding: 2rem;
        background: white;
        border-radius: 12px;
        box-shadow: var(--shadow-soft);
    }
    
    .qr-image {
        max-width: 200px;
        max-height: 200px;
        margin: 0 auto;
    }
    
    .proof-upload {
        margin: 2rem 0;
        padding: 2rem;
        border: 2px dashed #ccc;
        border-radius: 12px;
        text-align: center;
    }
    
    .proof-upload.dragover {
        border-color: var(--accent-red);
        background: #fff5f5;
    }
    
    .proof-preview {
        margin-top: 1rem;
    }
    
    .proof-preview img {
        max-width: 200px;
        max-height: 200px;
        border-radius: 8px;
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

    <!-- Vehicle Details Section -->
    <div class="vehicle-details-section" id="vehicleDetailsSection">
        <h3>Vehicle Details</h3>
        <div class="vehicle-card">
            <div class="vehicle-image-container">
                <img id="vehicleImage" src="assets/img/default_car.jpg" alt="Vehicle Image" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
            </div>
            <div class="vehicle-info">
                <h4 id="vehicleName">Loading vehicle details...</h4>
                <div class="vehicle-specs">
                    <span class="spec"><i class="fa-solid fa-car"></i> <span id="vehicleCategory">N/A</span></span>
                    <span class="spec"><i class="fa-solid fa-user"></i> <span id="vehicleSeating">4</span> Seater</span>
                    <span class="spec"><i class="fa-solid fa-gas-pump"></i> <span id="vehicleFuel">N/A</span></span>
                    <span class="spec"><i class="fa-solid fa-road"></i> <span id="vehicleTransmission">N/A</span></span>
                    <span class="spec"><i class="fa-solid fa-location-dot"></i> <span id="vehicleLocation">N/A</span></span>
                    <span class="spec"><i class="fa-solid fa-id-card"></i> <span id="vehiclePlate">N/A</span></span>
                </div>
            </div>
        </div>
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
            <span>Reservation Fee</span>
            <span id="reservationFee">₱500.00</span>
        </div>

        <div class="summary-row total-row">
            <span>Total Amount</span>
            <span id="totalAmount">-</span>
        </div>
    </div>

    <div class="booking-form">
        <h3>Renter Information</h3>

        <div class="form-row">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" id="firstName" placeholder="Enter your first name" required>
            </div>
            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" id="lastName" placeholder="Enter your last name" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" class="form-control" id="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="tel" class="form-control" id="phone" placeholder="Enter your phone number" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Pick-up Location</label>
                <select class="form-control" id="pickupLocation">
                    <option value="">Select pick-up location</option>
                    <option value="quezon_city">Quezon City Branch</option>
                    <option value="makati">Makati Branch</option>
                    <option value="pasig">Pasig Branch</option>
                </select>
            </div>
            <div class="form-group">
                <label>Return Location</label>
                <select class="form-control" id="returnLocation">
                    <option value="">Select return location</option>
                    <option value="quezon_city">Quezon City Branch</option>
                    <option value="makati">Makati Branch</option>
                    <option value="pasig">Pasig Branch</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Special Requests</label>
            <textarea class="form-control" id="specialRequests" placeholder="Any special requests or notes..."></textarea>
        </div>

        <!-- Payment Method Selection -->
        <div class="payment-section">
            <h3>Select Payment Method</h3>
            <div class="payment-methods">
                <div class="payment-option" onclick="selectPaymentMethod('gcash')">
                    <i class="fab fa-google-pay fa-2x" style="color: var(--accent-red); margin-bottom: 0.5rem;"></i>
                    <h4>GCash</h4>
                </div>
                <div class="payment-option" onclick="selectPaymentMethod('bdo')">
                    <i class="fas fa-university fa-2x" style="color: var(--accent-red); margin-bottom: 0.5rem;"></i>
                    <h4>BDO</h4>
                </div>
                <div class="payment-option" onclick="selectPaymentMethod('gotyme')">
                    <i class="fas fa-mobile-alt fa-2x" style="color: var(--accent-red); margin-bottom: 0.5rem;"></i>
                    <h4>GoTyme</h4>
                </div>
                <div class="payment-option" onclick="selectPaymentMethod('maya')">
                    <i class="fas fa-wallet fa-2x" style="color: var(--accent-red); margin-bottom: 0.5rem;"></i>
                    <h4>Maya</h4>
                </div>
                <div class="payment-option" onclick="selectPaymentMethod('maribank')">
                    <i class="fas fa-building fa-2x" style="color: var(--accent-red); margin-bottom: 0.5rem;"></i>
                    <h4>MariBank</h4>
                </div>
            </div>
        </div>

        <!-- QR Code Container -->
        <div class="qr-container" id="qrContainer">
            <h4>Scan QR Code for Payment</h4>
            <img id="qrCodeImage" class="qr-image" src="" alt="QR Code">
            <p id="qrInstruction">Please scan this QR code with your selected payment app</p>
        </div>

        <!-- Proof of Payment Upload -->
        <div class="proof-upload" id="proofUpload" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
            <h4>Upload Proof of Payment</h4>
            <p>Drag and drop your payment proof here or click to browse</p>
            <input type="file" id="proofOfFile" accept="image/*,application/pdf" style="display: none;" onchange="handleFileSelect(event)">
            <button class="btn btn-outline" onclick="document.getElementById('proofOfFile').click()">Select File</button>
            <div class="proof-preview" id="proofPreview"></div>
        </div>

        <button class="btn btn-primary full-width" onclick="submitBooking()" style="padding: 1rem; font-size: 1.1rem; font-weight: 700;">Submit Booking</button>
    </div>
</div>

<script>
// Get URL parameters
const urlParams = new URLSearchParams(window.location.search);
const carId = urlParams.get('car_id');
const pickupDate = urlParams.get('pickup') || sessionStorage.getItem('pickup_date');
const returnDate = urlParams.get('return') || sessionStorage.getItem('return_date');

if (pickupDate && returnDate) {
    document.getElementById('pickupDate').textContent = pickupDate;
    document.getElementById('returnDate').textContent = returnDate;

    // Store dates in session storage
    sessionStorage.setItem('pickup_date', pickupDate);
    sessionStorage.setItem('return_date', returnDate);

    // Calculate rental days
    const startDate = new Date(pickupDate);
    const endDate = new Date(returnDate);
    const timeDiff = endDate.getTime() - startDate.getTime();
    const rentalDays = Math.max(1, Math.ceil(timeDiff / (1000 * 3600 * 24)));
    document.getElementById('rentalDays').textContent = rentalDays;

    // Calculate total amount if car details are available
    if (carId) {
        // We'll calculate the total amount after loading car details
    }
} else {
    // If no dates are available, show a message
    document.getElementById('pickupDate').textContent = 'Not specified';
    document.getElementById('returnDate').textContent = 'Not specified';
    document.getElementById('rentalDays').textContent = '0';
}

// Load car details if car_id is provided
if (carId) {
    loadCarDetails(carId);
} else {
    // If no car_id, clear the form fields
    clearFormFields();
}

function clearFormFields() {
    document.getElementById('firstName')?.value = '';
    document.getElementById('lastName')?.value = '';
    document.getElementById('email')?.value = '';
    document.getElementById('phone')?.value = '';
    document.getElementById('pickupLocation')?.value = '';
    document.getElementById('returnLocation')?.value = '';
    document.getElementById('specialRequests')?.value = '';

    // Clear payment method selection
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('selected');
    });

    // Hide QR container
    document.getElementById('qrContainer')?.style.display = 'none';

    // Clear proof of payment
    document.getElementById('proofPreview')?.innerHTML = '';
}

function loadCarDetails(carId) {
    // Show loading state
    document.getElementById('vehicleName').innerHTML = '<div class="loading-spinner">Loading vehicle details...</div>';
    document.getElementById('selectedVehicle').innerHTML = '<div class="loading-spinner">Loading...</div>';

    // Fetch real car details from the API
    fetch(`api/get_car_details.php?id=${carId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.car) {
                const car = data.car;

                // Update the booking summary with car details
                document.getElementById('selectedVehicle').textContent = `${car.brand} ${car.model}`;

                // Update the vehicle details section
                document.getElementById('vehicleName').innerHTML = `<strong>${car.brand} ${car.model}</strong>`;
                document.getElementById('vehicleCategory').textContent = car.category || 'N/A';
                document.getElementById('vehicleSeating').textContent = car.seating || '4';
                document.getElementById('vehicleFuel').textContent = car.fuel_type || 'N/A';
                document.getElementById('vehicleTransmission').textContent = car.transmission || 'Automatic';
                document.getElementById('vehicleLocation').textContent = car.location || 'N/A';
                document.getElementById('vehiclePlate').textContent = car.plate_number || 'N/A';

                // Update vehicle image if available
                if (car.car_image) {
                    document.getElementById('vehicleImage').src = `../${car.car_image}`;
                }

                // Calculate total amount based on daily rate
                const rentalDays = parseInt(document.getElementById('rentalDays').textContent) || 1;
                calculateTotalAmount(rentalDays, car.tier4_daily || 0);

                // Store car details in sessionStorage for later use
                sessionStorage.setItem('selectedCar', JSON.stringify(car));
            } else {
                document.getElementById('selectedVehicle').textContent = 'Car not found';
                document.getElementById('vehicleName').textContent = 'Car not found';
            }
        })
        .catch(error => {
            console.error('Error loading car details:', error);
            document.getElementById('selectedVehicle').textContent = 'Error loading car details';
            document.getElementById('vehicleName').textContent = 'Error loading details';
        });
}

function calculateTotalAmount(days, dailyRate = null) {
    // Get daily rate from sessionStorage if not provided
    if (!dailyRate) {
        const selectedCar = JSON.parse(sessionStorage.getItem('selectedCar') || '{}');
        dailyRate = selectedCar.tier4_daily || 0;
    }

    // Calculate rental days based on pickup and return dates
    const pickupDateStr = sessionStorage.getItem('pickup_date');
    const returnDateStr = sessionStorage.getItem('return_date');

    if (pickupDateStr && returnDateStr) {
        const pickupDate = new Date(pickupDateStr);
        const returnDate = new Date(returnDateStr);
        const timeDiff = returnDate.getTime() - pickupDate.getTime();
        days = Math.max(1, Math.ceil(timeDiff / (1000 * 3600 * 24)));

        // Update the rental days display
        document.getElementById('rentalDays').textContent = days;
    }

    const subtotal = days * dailyRate;
    const reservationFee = 500; // Static reservation fee
    const totalAmount = subtotal + reservationFee;

    document.getElementById('dailyRate').textContent = `₱${dailyRate.toFixed(2)}`;
    document.getElementById('subtotal').textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('totalAmount').textContent = `₱${totalAmount.toFixed(2)}`;
}

function selectPaymentMethod(method) {
    // Remove selected class from all options
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('selected');
    });

    // Add selected class to clicked option
    event.currentTarget.classList.add('selected');

    // Show QR code for selected payment method
    const qrContainer = document.getElementById('qrContainer');
    const qrImage = document.getElementById('qrCodeImage');
    const qrInstruction = document.getElementById('qrInstruction');
    
    // Map payment methods to QR code paths
    const qrCodes = {
        'gcash': 'assets/images/qr_codes/gcash_qr.png',
        'bdo': 'assets/images/qr_codes/bdo_qr.png',
        'gotyme': 'assets/images/qr_codes/gotyme_qr.png',
        'maya': 'assets/images/qr_codes/maya_qr.png',
        'maribank': 'assets/images/qr_codes/maribank_qr.png'
    };
    
    if (qrCodes[method]) {
        qrImage.src = qrCodes[method];
        qrInstruction.textContent = `Please scan this QR code with ${method.charAt(0).toUpperCase() + method.slice(1)} app`;
        qrContainer.style.display = 'block';
    } else {
        qrContainer.style.display = 'none';
    }
    
    // Store selected payment method
    sessionStorage.setItem('selectedPaymentMethod', method);
}

function handleDragOver(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('proofUpload').classList.add('dragover');
}

function handleDragLeave(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('proofUpload').classList.remove('dragover');
}

function handleDrop(e) {
    e.preventDefault();
    e.stopPropagation();
    document.getElementById('proofUpload').classList.remove('dragover');
    
    const files = e.dataTransfer.files;
    if (files.length) {
        handleFile(files[0]);
    }
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        handleFile(file);
    }
}

function handleFile(file) {
    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'application/pdf'];
    if (!validTypes.includes(file.type)) {
        alert('Please upload a valid image (JPG, PNG) or PDF file');
        return;
    }
    
    // Validate file size (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('File size must be less than 5MB');
        return;
    }
    
    // Show preview
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('proofPreview');
        if (file.type.startsWith('image/')) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Proof Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px;">`;
        } else {
            preview.innerHTML = `<p>PDF File: ${file.name}</p>`;
        }
        
        // Store file in session storage
        const fileData = {
            name: file.name,
            type: file.type,
            size: file.size,
            data: e.target.result
        };
        sessionStorage.setItem('proofOfPayment', JSON.stringify(fileData));
    };
    reader.readAsDataURL(file);
}

async function submitBooking() {
    // Validate form
    const firstName = document.getElementById('firstName').value;
    const lastName = document.getElementById('lastName').value;
    const email = document.getElementById('email').value;
    const phone = document.getElementById('phone').value;
    const pickupLoc = document.getElementById('pickupLocation').value;
    const returnLoc = document.getElementById('returnLocation').value;
    const selectedPaymentMethod = sessionStorage.getItem('selectedPaymentMethod');
    const proofOfFile = document.getElementById('proofOfFile');

    if (!firstName || !lastName || !email || !phone || !pickupLoc || !returnLoc || !selectedPaymentMethod || !proofOfFile.files[0]) {
        alert('Please fill in all required fields and upload proof of payment');
        return;
    }

    // Get dates from session storage
    const pickupDate = sessionStorage.getItem('pickup_date');
    const returnDate = sessionStorage.getItem('return_date');

    // Create form data for file upload
    const formData = new FormData();
    formData.append('car_id', carId);
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('pickup_location', pickupLoc);
    formData.append('return_location', returnLoc);
    formData.append('special_requests', document.getElementById('specialRequests').value);
    formData.append('payment_method', selectedPaymentMethod);
    formData.append('pickup_date', pickupDate);
    formData.append('return_date', returnDate);
    formData.append('proof_of_payment', proofOfFile.files[0]); // Add the file to form data

    try {
        const response = await fetch('api/submit_booking.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('Booking submitted successfully! Your reservation is pending approval.');

            // Clear session storage
            sessionStorage.removeItem('selectedCar');
            sessionStorage.removeItem('selectedPaymentMethod');
            sessionStorage.removeItem('pickup_date');
            sessionStorage.removeItem('return_date');

            // Redirect to confirmation page
            window.location.href = '?page=confirmation';
        } else {
            alert('Error submitting booking: ' + data.message);
        }
    } catch (error) {
        console.error('Error submitting booking:', error);
        alert('Error submitting booking: ' + error.message);
    }
}

// Load saved data if returning from another page
document.addEventListener('DOMContentLoaded', function() {
    const savedBooking = sessionStorage.getItem('bookingDetails');
    if (savedBooking) {
        const details = JSON.parse(savedBooking);
        if (details.firstName) document.getElementById('firstName').value = details.firstName;
        if (details.lastName) document.getElementById('lastName').value = details.lastName;
        if (details.email) document.getElementById('email').value = details.email;
        if (details.phone) document.getElementById('phone').value = details.phone;
        if (details.pickupLocation) document.getElementById('pickupLocation').value = details.pickupLocation;
        if (details.returnLocation) document.getElementById('returnLocation').value = details.returnLocation;
        if (details.specialRequests) document.getElementById('specialRequests').value = details.specialRequests;
    }
});
</script>

<?php
include __DIR__ . '/../includes/footer.php';
?>