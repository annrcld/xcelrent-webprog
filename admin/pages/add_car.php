<section id="add-car" class="tab-content active">
    <h1 class="page-title"><span id="formTitle">Add New Car</span></h1>
    
    <form class="entry-form" id="carForm" method="POST" enctype="multipart/form-data">
        
        <!-- Hidden field for edit mode -->
        <input type="hidden" name="car_id" id="carId" value="">
        
        <div class="form-section">
            <h3>Vehicle Identity</h3>
            <div class="form-row">
                <input type="text" placeholder="Brand" name="brand" required>
                <input type="text" placeholder="Model" name="model" required>
                <select id="vType" name="category" onchange="handleTypeChange()" required>
                    <option value="">-- Select Category --</option>
                    <option value="Sedan">Sedan</option>
                    <option value="SUV">SUV</option>
                    <option value="Van">Van</option>
                </select>
                <select name="fuel_type" required>
                    <option value="">-- Fuel Type --</option>
                    <option value="Gasoline">Gasoline</option>
                    <option value="Diesel">Diesel</option>
                </select>
                <select name="driver_type" required>
                    <option value="">-- Driver Type --</option>
                    <option value="self_drive">Self Drive</option>
                    <option value="with_driver">With Driver</option>
                </select>
            </div>
        </div>

        <div class="form-section">
            <h3>Specifications & Compliance</h3>
            <div class="form-row">
                <div class="counter-input">
                    <label>Seating</label>
                    <div class="controls">
                        <button type="button" onclick="adjustSeating(-1)">-</button>
                        <input type="number" id="seating" name="seating" value="4" readonly>
                        <button type="button" onclick="adjustSeating(1)">+</button>
                    </div>
                </div>
               <input type="text" 
                    id="plateInput" 
                    placeholder="AAA-1111" 
                    name="plate_number" 
                    onkeyup="updateCoding()" 
                    style="text-transform: uppercase;" 
                    maxlength="8" 
                    pattern="[A-Z]{3}-[0-9]{4}" 
                    required>
                <input type="text" id="codingDisplay" placeholder="Coding Day" name="coding_day" readonly class="bg-gray">
            </div>
        </div>

        <div class="form-section">
            <h3>Documentation Requirements</h3>
            <p style="font-size: 13px; color: #666; margin-bottom: 15px;">Please upload clear copies of the following documents (PDF, JPG, PNG).</p>
            
            <div class="form-row">
                <div class="file-group">
                    <label>Official Receipt (OR)</label>
                    <input type="file" name="or_file" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="file-group">
                    <label>Certificate of Registration (CR)</label>
                    <input type="file" name="cr_file" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="file-group">
                    <label>NBI Clearance</label>
                    <input type="file" name="nbi_clearance" accept=".pdf,.jpg,.jpeg,.png">
                </div>
            </div>

            <div class="form-row mt-2">
                <div class="file-group">
                    <label>Deed of Sale</label>
                    <input type="file" name="deed_of_sale" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="file-group">
                    <label>Professional Driver's License</label>
                    <input type="file" name="pro_license" accept=".pdf,.jpg,.jpeg,.png">
                </div>
                <div class="file-group" style="visibility: hidden;">
                    <label>Spacer</label>
                    <input type="file">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Pricing Tiers</h3>
            
            <div class="pricing-tier-section">
                <h4>Metro Manila (NCR)</h4>
                <div class="form-row">
                    <div class="price-input">
                        <label>12 Hours (₱)</label>
                        <input type="number" name="tier1_12hrs" placeholder="1399" step="0.01" min="1" required>
                    </div>
                    <div class="price-input">
                        <label>24 Hours (₱)</label>
                        <input type="number" name="tier1_24hrs" placeholder="1799" step="0.01" min="1" required>
                    </div>
                </div>
            </div>

            <div class="pricing-tier-section mt-2">
                <h4>Nearby Provinces (Bulacan, Cavite, etc.)</h4>
                <div class="form-row">
                    <div class="price-input">
                        <label>12 Hours (₱)</label>
                        <input type="number" name="tier2_12hrs" placeholder="1899" step="0.01" min="1" required>
                    </div>
                    <div class="price-input">
                        <label>24 Hours (₱)</label>
                        <input type="number" name="tier2_24hrs" placeholder="2299" step="0.01" min="1" required>
                    </div>
                </div>
            </div>

            <div class="pricing-tier-section mt-2">
                <h4>Long Distance (Baguio, Bicol, etc.)</h4>
                <div class="form-row">
                    <div class="price-input">
                        <label>Any Point Luzon 24h (₱)</label>
                        <input type="number" name="tier3_24hrs" placeholder="2799" step="0.01" min="1" required>
                    </div>
                    <div class="price-input">
                        <label>Long Term Daily Rate (₱)</label>
                        <input type="number" name="tier4_daily" placeholder="2000" step="0.01" min="1" required>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Location</h3>
            <div class="form-row">
                <div class="price-input">
                    <label>Area</label>
                    <select name="location" required>
                        <option value="">-- Select Location --</option>
                        <option value="Quezon City">Quezon City</option>
                        <option value="Manila">Manila</option>
                        <option value="Pasig">Pasig</option>
                        <option value="Bulacan">Bulacan</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3>Car Image</h3>
            <div class="form-row">
                <div class="file-group">
                    <label>Upload Car Image</label>
                    <input type="file" name="car_image" accept=".jpg,.jpeg,.png" id="carImageInput">
                </div>
            </div>
            <div id="imagePreview" class="mt-2" style="display: none;">
                <img id="previewImg" src="" alt="Image Preview" style="max-width: 200px; max-height: 150px; border-radius: 8px;">
            </div>
        </div>

        <button type="submit" id="submitBtn" class="btn btn-red submit-btn">Save Vehicle to Inventory</button>
    </form>
</section>

<script>
// Check if we're in edit mode
const urlParams = new URLSearchParams(window.location.search);
const editId = urlParams.get('edit_id');

if (editId) {
    document.getElementById('formTitle').textContent = 'Edit Vehicle';
    document.getElementById('submitBtn').textContent = 'Update Vehicle';
    loadCarData(editId);
}

function loadCarData(id) {
    fetch(`api/get_car.php?id=${id}`)
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert('Failed to load car data.');
                return;
            }

            const car = data.data;

            // Set hidden ID
            document.getElementById('carId').value = car.id;

            // Fill form fields
            document.querySelector('[name="brand"]').value = car.brand || '';
            document.querySelector('[name="model"]').value = car.model || '';
            document.querySelector('[name="category"]').value = car.category || '';
            document.querySelector('[name="fuel_type"]').value = car.fuel_type || '';
            document.querySelector('[name="driver_type"]').value = car.driver_type || '';
            document.querySelector('[name="seating"]').value = car.seating || 4;
            document.querySelector('[name="plate_number"]').value = car.plate_number || '';
            document.querySelector('[name="location"]').value = car.location || '';

            // Pricing
            document.querySelector('[name="tier1_12hrs"]').value = car.tier1_12hrs || '';
            document.querySelector('[name="tier1_24hrs"]').value = car.tier1_24hrs || '';
            document.querySelector('[name="tier2_12hrs"]').value = car.tier2_12hrs || '';
            document.querySelector('[name="tier2_24hrs"]').value = car.tier2_24hrs || '';
            document.querySelector('[name="tier3_24hrs"]').value = car.tier3_24hrs || '';
            document.querySelector('[name="tier4_daily"]').value = car.tier4_daily || '';

            // Optional: Show image preview if available
            if (car.car_image) {
                // You can add an image preview here if needed
            }
        })
        .catch(err => {
            alert('Error loading car data.');
            console.error(err);
        });
}

// Image preview functionality
document.getElementById('carImageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('previewImg').src = event.target.result;
            document.getElementById('imagePreview').style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

// Handle form submission
document.getElementById('carForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);

    const url = editId ? 'api/update_car.php' : 'api/add_car.php';

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.msg);
            window.location.href = '?page=manage_cars'; // Redirect back to manage cars
        } else {
            alert('Error: ' + data.msg);
        }
    })
    .catch(err => {
        alert('Network error. Please try again.');
        console.error(err);
    });
});

// Seating counter
function adjustSeating(delta) {
    const input = document.getElementById('seating');
    let val = parseInt(input.value) + delta;
    if (val < 1) val = 1;
    if (val > 20) val = 20; // max seating
    input.value = val;
}

// Plate coding logic (optional)
function updateCoding() {
    const plate = document.getElementById('plateInput').value.toUpperCase();
    document.getElementById('codingDisplay').value = getCodingDay(plate);
}

function getCodingDay(plate) {
    if (!plate.match(/^[A-Z]{3}-[0-9]{4}$/)) return '';
    const lastDigit = plate.slice(-1);
    switch(lastDigit) {
        case '0': case '1': return 'Monday';
        case '2': case '3': return 'Tuesday';
        case '4': case '5': return 'Wednesday';
        case '6': case '7': return 'Thursday';
        case '8': case '9': return 'Friday';
        default: return '';
    }
}
</script>

<!-- Keep as-is (already has inline JS or can add later) -->
<!-- Or add: -->
<script src="assets/js/core.js"></script>
<!-- (since it uses updateCoding, handleTypeChange) -->