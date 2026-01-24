<?php
// public/pages/booking.php
$page_title = "Book a Car";

// Get car ID from URL parameter
$car_id = $_GET['car_id'] ?? null;
$pickup_date = $_GET['pickup'] ?? '';
$return_date = $_GET['return'] ?? '';

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

    .vehicle-details-section {
        background: white;
        padding: 2rem;
        border-radius: 16px;
        box-shadow: var(--shadow-soft);
        margin-bottom: 2rem;
    }

    .vehicle-card {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 2rem;
        align-items: start;
    }

    .vehicle-specs {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1rem;
    }

    .vehicle-specs .spec {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.9rem;
        color: var(--text-muted);
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
        margin-bottom: 2rem;
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
        border-bottom: none;
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
        max-width: 300px;
        max-height: 300px;
        margin: 1rem auto;
        display: block;
    }
    
    .proof-upload {
        margin: 2rem 0;
        padding: 2rem;
        border: 2px dashed #ccc;
        border-radius: 12px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
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

    @media (max-width: 768px) {
        .vehicle-card {
            grid-template-columns: 1fr;
        }
        
        .form-row {
            grid-template-columns: 1fr;
        }
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
                <img id="vehicleImage" src="/project_xcelrent/public/assets/img/default_car.jpg" alt="Vehicle Image" style="width: 100%; height: 200px; object-fit: cover; border-radius: 8px;">
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
            <span id="selectedVehicle">Loading...</span>
        </div>

        <div class="summary-row">
            <span>Pick-up Date</span>
            <span id="pickupDate"><?php echo $pickup_date ? date('M j, Y g:i A', strtotime($pickup_date)) : 'Not specified'; ?></span>
        </div>

        <div class="summary-row">
            <span>Return Date</span>
            <span id="returnDate"><?php echo $return_date ? date('M j, Y g:i A', strtotime($return_date)) : 'Not specified'; ?></span>
        </div>

        <div class="summary-row">
            <span>Rental Period</span>
            <span id="rentalDays">Calculating...</span>
        </div>

        <div class="summary-row">
            <span>Daily Rate <i>(Long-term (>7 days) rate—may vary by location)</i></span>
            <span id="dailyRate">₱0.00</span>
        </div>

        <div class="summary-row">
            <span>Subtotal</span>
            <span id="subtotal">₱0.00</span>
        </div>

        <div class="summary-row">
            <span>Reservation Fee (Deductible)</span>
            <span id="reservationFee">-₱500.00</span>
        </div>

        <div class="summary-row total-row">
            <span>Total Amount</span>
            <span id="totalAmount">₱0.00</span>
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
                <input type="tel" class="form-control" id="phone" placeholder="09XXXXXXXXX" required>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label>Pick-up Location</label>
                <select class="form-control" id="pickupLocation" required>
                    <option value="">Select pick-up location</option>
                    <option value="Quezon City">Quezon City Branch</option>
                    <option value="Manila">Manila Branch</option>
                    <option value="Makati">Makati Branch</option>
                    <option value="Pasig">Pasig Branch</option>
                    <option value="Bulacan">Bulacan Branch</option>
                </select>
            </div>
            <div class="form-group">
                <label>Return Location</label>
                <select class="form-control" id="returnLocation" required>
                    <option value="">Select return location</option>
                    <option value="Quezon City">Quezon City Branch</option>
                    <option value="Manila">Manila Branch</option>
                    <option value="Makati">Makati Branch</option>
                    <option value="Pasig">Pasig Branch</option>
                    <option value="Bulacan">Bulacan Branch</option>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label>Special Requests (Optional)</label>
            <textarea class="form-control" id="specialRequests" rows="3" placeholder="Any special requests or notes..."></textarea>
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
            <h4 id="qrTitle">Scan QR Code for Payment</h4>
            <p id="qrInstruction" style="color: var(--text-muted); margin-bottom: 1rem;">Please scan this QR code with your payment app</p>
            <img id="qrCodeImage" class="qr-image" src="" alt="QR Code">
            <p style="margin-top: 1rem; font-weight: 600;">Pay  <span style="color: var(--accent-red); font-size: 1.2rem;">₱500</span> now as reservation fee.<br>The remaining balance of <span id="qrAmount">₱0.00</span> will be settled upon vehicle turnover.</p>
        </div>

        <!-- Proof of Payment Upload -->
        <div class="proof-upload" id="proofUpload" onclick="document.getElementById('proofOfFile').click()" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)">
            <h4>Upload Proof of Payment</h4>
            <p>Drag and drop your payment screenshot here or click to browse</p>
            <input type="file" id="proofOfFile" accept="image/*" style="display: none;" onchange="handleFileSelect(event)">
            <button type="button" class="btn btn-outline" style="margin-top: 1rem;">Select File</button>
            <div class="proof-preview" id="proofPreview"></div>
        </div>

        <button class="btn btn-primary full-width" onclick="submitBooking()" style="padding: 1rem; font-size: 1.1rem; font-weight: 700;">Submit Booking</button>
    </div>
</div>

<script>
// Store dates and car ID globally
let globalCarData = null;
const urlParams = new URLSearchParams(window.location.search);
const carId = urlParams.get('car_id');
const pickupDate = '<?php echo $pickup_date; ?>';
const returnDate = '<?php echo $return_date; ?>';

// Calculate rental days
function calculateRentalDays() {
    if (!pickupDate || !returnDate) return 1;
    
    const startDate = new Date(pickupDate);
    const endDate = new Date(returnDate);
    const timeDiff = endDate.getTime() - startDate.getTime();
    const days = Math.ceil(timeDiff / (1000 * 3600 * 24));
    return Math.max(1, days);
}

// Calculate and display total amount
function calculateTotalAmount(dailyRate) {
    const rentalDays = calculateRentalDays();
    const subtotal = dailyRate * rentalDays;
    const reservationFee = 500;
    const totalAmount = subtotal - reservationFee; // Reservation fee is deducted from total

    document.getElementById('rentalDays').textContent = `${rentalDays} day${rentalDays !== 1 ? 's' : ''}`;
    document.getElementById('dailyRate').textContent = `₱${dailyRate.toFixed(2)}`;
    document.getElementById('subtotal').textContent = `₱${subtotal.toFixed(2)}`;
    document.getElementById('reservationFee').textContent = `-₱${reservationFee.toFixed(2)}`;
    document.getElementById('totalAmount').textContent = `₱${totalAmount.toFixed(2)}`;
    document.getElementById('qrAmount').textContent = `₱${totalAmount.toFixed(2)}`;
}

// Load car details
async function loadCarDetails(carId) {
    try {
        const response = await fetch(`/project_xcelrent/public/api/get_car_details.php?id=${carId}`);
        const data = await response.json();
        
        if (data.error) {
            alert(data.error);
            return;
        }
        
        globalCarData = data;
        
        // Update vehicle name and image
        const vehicleName = `${data.brand || ''} ${data.model || ''}`.trim() || 'Unknown Vehicle';
        document.getElementById('vehicleName').textContent = vehicleName;
        document.getElementById('selectedVehicle').textContent = vehicleName;
        
        // Update vehicle image
        if (data.image) {
            document.getElementById('vehicleImage').src = `/project_xcelrent/public/${data.image}`;
        }
        
        // Update vehicle specs
        document.getElementById('vehicleCategory').textContent = data.category || 'N/A';
        document.getElementById('vehicleSeating').textContent = data.seating || '4';
        document.getElementById('vehicleFuel').textContent = data.fuel_type || 'N/A';
        document.getElementById('vehicleTransmission').textContent = data.transmission || 'Automatic';
        document.getElementById('vehicleLocation').textContent = data.location || 'N/A';
        document.getElementById('vehiclePlate').textContent = data.plate_number || 'N/A';
        
        // Calculate total amount
        const dailyRate = parseFloat(data.tier4_daily) || 0;
        calculateTotalAmount(dailyRate);
        
    } catch (error) {
        console.error('Error loading car details:', error);
        alert('Failed to load car details. Please try again.');
    }
}

// Select payment method
function selectPaymentMethod(method) {
    // Remove selected class from all options
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('selected');
    });
    
    // Add selected class to clicked option
    event.currentTarget.classList.add('selected');
    
    // Show QR code
    const qrContainer = document.getElementById('qrContainer');
    const qrImage = document.getElementById('qrCodeImage');
    const qrTitle = document.getElementById('qrTitle');
    const qrInstruction = document.getElementById('qrInstruction');
    
    // Map payment methods to QR code paths (you need to create these images)
    const qrCodes = {
        'gcash': '/project_xcelrent/public/assets/img/qr/gcash_qr.png',
        'bdo': '/project_xcelrent/public/assets/img/qr/bdo_qr.png',
        'gotyme': '/project_xcelrent/public/assets/img/qr/gotyme_qr.png',
        'maya': '/project_xcelrent/public/assets/img/qr/maya_qr.png',
        'maribank': '/project_xcelrent/public/assets/img/qr/maribank_qr.png'
    };
    
    const methodNames = {
        'gcash': 'GCash',
        'bdo': 'BDO',
        'gotyme': 'GoTyme',
        'maya': 'Maya',
        'maribank': 'MariBank'
    };
    
    if (qrCodes[method]) {
        qrImage.src = qrCodes[method];
        qrTitle.textContent = `${methodNames[method]} Payment`;
        qrInstruction.textContent = `Scan this QR code using your ${methodNames[method]} app to complete the payment`;
        qrContainer.style.display = 'block';
        
        // Store selected payment method
        sessionStorage.setItem('selectedPaymentMethod', method);
    }
}

// File upload handlers
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
        document.getElementById('proofOfFile').files = files;
        handleFileSelect({ target: { files: files } });
    }
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // Validate file type
    if (!file.type.startsWith('image/')) {
        alert('Please upload an image file (JPG, PNG, etc.)');
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
        preview.innerHTML = `
            <p style="color: var(--accent-red); font-weight: 600; margin-top: 1rem;">File uploaded: ${file.name}</p>
            <img src="${e.target.result}" alt="Proof Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; margin-top: 0.5rem;">
        `;
    };
    reader.readAsDataURL(file);
}

// Submit booking
async function submitBooking() {
    // Validate form
    const firstName = document.getElementById('firstName').value.trim();
    const lastName = document.getElementById('lastName').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const pickupLoc = document.getElementById('pickupLocation').value;
    const returnLoc = document.getElementById('returnLocation').value;
    const specialRequests = document.getElementById('specialRequests').value.trim();
    const selectedPaymentMethod = sessionStorage.getItem('selectedPaymentMethod');
    const proofFile = document.getElementById('proofOfFile').files[0];
    
    // Validation
    if (!firstName || !lastName || !email || !phone) {
        alert('Please fill in all required personal information fields');
        return;
    }
    
    if (!pickupLoc || !returnLoc) {
        alert('Please select both pickup and return locations');
        return;
    }
    
    if (!selectedPaymentMethod) {
        alert('Please select a payment method');
        return;
    }
    
    if (!proofFile) {
        alert('Please upload proof of payment');
        return;
    }
    
    // Validate phone number format
    if (!/^09\d{9}$/.test(phone)) {
        alert('Please enter a valid Philippine mobile number (09XXXXXXXXX)');
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('car_id', carId);
    formData.append('first_name', firstName);
    formData.append('last_name', lastName);
    formData.append('email', email);
    formData.append('phone', phone);
    formData.append('pickup_location', pickupLoc);
    formData.append('return_location', returnLoc);
    formData.append('pickup_date', pickupDate);
    formData.append('return_date', returnDate);
    formData.append('special_requests', specialRequests);
    formData.append('payment_method', selectedPaymentMethod);
    formData.append('proof_of_payment', proofFile);
    
// Replace the end of your submitBooking function with this:
    try {
        const response = await fetch('/project_xcelrent/public/api/submit_booking.php', {
            method: 'POST',
            body: formData
        });

        // CHECK IF THE RESPONSE IS OK
        if (!response.ok) {
            const errorText = await response.text();
            console.error('Server Error Response:', errorText);
            throw new Error(`Server returned ${response.status}: ${response.statusText}`);
        }

        const data = await response.json();
        // ... rest of your logic
    } catch (error) {
        console.error('Detailed Error:', error);
        alert('Debug Info: ' + error.message);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    if (carId) {
        loadCarDetails(carId);
    } else {
        alert('No car selected. Redirecting to car listings...');
        window.location.href = '?page=cars';
    }
});
</script>