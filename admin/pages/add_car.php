<section id="add-car" class="tab-content active">
    <h1 class="page-title">Add New Car</h1>
    
    <form class="entry-form" id="carEntryForm">
        
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
                    <input type="file" name="or_file" accept=".pdf,.jpg,.jpeg,.png" required>
                </div>
                <div class="file-group">
                    <label>Certificate of Registration (CR)</label>
                    <input type="file" name="cr_file" accept=".pdf,.jpg,.jpeg,.png" required>
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

        <button type="submit" class="btn btn-red submit-btn">Save Vehicle to Inventory</button>
    </form>
</section>

<script>
document.getElementById('carEntryForm').onsubmit = function(e) {
    e.preventDefault(); // This stops the browser from opening the JSON page

    const formData = new FormData(this);

    // Show a "Processing..." state on the button if you like
    const submitBtn = this.querySelector('.submit-btn');
    const originalText = submitBtn.innerText;
    submitBtn.innerText = "Saving...";
    submitBtn.disabled = true;

    fetch('api/add_car.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.msg); // Show the success prompt
            // REDIRECT: Change 'index.php' to your actual dashboard URL if different
            window.location.href = 'index.php?page=manage_cars'; 
        } else {
            alert("Error: " + data.msg);
            submitBtn.innerText = originalText;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert("An error occurred while saving.");
        submitBtn.innerText = originalText;
        submitBtn.disabled = false;
    });
};
</script>