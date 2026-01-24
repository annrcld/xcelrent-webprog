<?php
// public/pages/home.php
$page_title = "Home";

// Initialize variables
$cars = [];
$pickup_date = $_GET['pickup'] ?? '';
$return_date = $_GET['return'] ?? '';
$driver_type = $_GET['driver_type'] ?? '';
$area = $_GET['area'] ?? '';
$show_results = !empty($pickup_date) && !empty($return_date);

// Fetch cars from database only if dates are provided
if ($show_results && isset($conn)) {
    // Build base query
    $sql = "SELECT c.*,
                   CONCAT(c.brand, ' ', c.model) AS name,
                   c.seating AS seats,
                   c.fuel_type AS fuel,
                   c.tier4_daily AS price,
                   (SELECT file_path FROM car_photos WHERE car_id = c.id ORDER BY is_primary DESC LIMIT 1) AS image
            FROM cars c
            WHERE c.status = 'live'";

    $params = [];
    $types = "";

    // Add date availability condition
    if (!empty($pickup_date) && !empty($return_date)) {
        $sql .= " AND c.id NOT IN (
                      SELECT DISTINCT b.car_id
                      FROM bookings b
                      WHERE b.status != 'cancelled'
                        AND (STR_TO_DATE(?, '%Y-%m-%d %H:%i') <= b.end_date AND STR_TO_DATE(?, '%Y-%m-%d %H:%i') >= b.start_date)
                  )";
        $params[] = $pickup_date;
        $params[] = $return_date;
        $types .= "ss";
    }

    // Add driver type filter
    if (!empty($driver_type)) {
        $driver_type_value = $driver_type === 'self' ? 'self_drive' : 'with_driver';
        $sql .= " AND c.driver_type = ?";
        $params[] = $driver_type_value;
        $types .= "s";
    }

    // Add location/area filter
    if (!empty($area)) {
        // Map area values to location values in the database
        switch ($area) {
            case 'metro':
                // For Metro Manila, include common Metro Manila locations
                $sql .= " AND (c.location LIKE '%Quezon City%' OR c.location LIKE '%Manila%' OR c.location LIKE '%Makati%' OR c.location LIKE '%Pasig%' OR c.location LIKE '%Mandaluyong%' OR c.location LIKE '%San Juan%' OR c.location LIKE '%Pasay%' OR c.location LIKE '%Taguig%' OR c.location LIKE '%Parañaque%' OR c.location LIKE '%Muntinlupa%' OR c.location LIKE '%Marikina%' OR c.location LIKE '%Pasay%' OR c.location LIKE '%Valenzuela%' OR c.location LIKE '%Las Piñas%' OR c.location LIKE '%Malabon%' OR c.location LIKE '%Navotas%' OR c.location LIKE '%Caloocan%' OR c.location LIKE '%Bulacan%' OR c.location LIKE '%Pampanga%')";
                break;
            case 'outside':
                // For outside Metro, exclude common Metro Manila locations
                $sql .= " AND c.location NOT LIKE '%Quezon City%' AND c.location NOT LIKE '%Manila%' AND c.location NOT LIKE '%Makati%' AND c.location NOT LIKE '%Pasig%' AND c.location NOT LIKE '%Mandaluyong%' AND c.location NOT LIKE '%San Juan%' AND c.location NOT LIKE '%Pasay%' AND c.location NOT LIKE '%Taguig%' AND c.location NOT LIKE '%Parañaque%' AND c.location NOT LIKE '%Muntinlupa%' AND c.location NOT LIKE '%Marikina%' AND c.location NOT LIKE '%Pasay%' AND c.location NOT LIKE '%Valenzuela%' AND c.location NOT LIKE '%Las Piñas%' AND c.location NOT LIKE '%Malabon%' AND c.location NOT LIKE '%Navotas%' AND c.location NOT LIKE '%Caloocan%'";
                break;
            case 'luzon':
                // For Luzon, no specific location filter needed, show all
                break;
            default:
                // If it's a specific location, use it directly
                $sql .= " AND c.location LIKE ?";
                $params[] = '%' . $area . '%';
                $types .= "s";
                break;
        }
    }

    $sql .= " ORDER BY c.id DESC";

    try {
        if (!empty($params)) {
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result === false) {
                throw new Exception("Execute failed: " . $stmt->error);
            }
        } else {
            $result = $conn->query($sql);
            if ($result === false) {
                throw new Exception("Query failed: " . $conn->error);
            }
        }

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $cars[] = $row;
            }
        }
    } catch (Exception $e) {
        $db_error = "Query failed: " . $e->getMessage();
    }
}
?>

    <header class="hero" id="about">
        <div class="hero-content">
            <h1>Simply Excellent.<br><span class="text-highlight">Truly Affordable.</span></h1>
            <p>Unlock your next adventure. Reliable rides ready for wherever the road takes you.</p>
        </div>
    </header>

<div class="search-box">
    <form class="search-form" onsubmit="searchCars(event)">

        <div class="form-group">
            <label>Type</label>
            <div class="custom-select" id="typeSelect">
                <input type="hidden" id="driverOption" value="<?php echo htmlspecialchars($_GET['driver_type'] ?? 'self'); ?>">
                <div class="select-trigger">
                    <span><?php
                        $driverType = $_GET['driver_type'] ?? 'self';
                        echo $driverType === 'self' ? 'Self-Drive' : 'With Driver';
                    ?></span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="custom-options">
                    <div class="option <?php echo ($driverType === 'self') ? 'selected' : ''; ?>" data-value="self">Self-Drive</div>
                    <div class="option <?php echo ($driverType === 'driver') ? 'selected' : ''; ?>" data-value="driver">With Driver</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Area</label>
            <div class="custom-select" id="destinationSelect">
                <input type="hidden" id="destinationValue" value="<?php echo htmlspecialchars($_GET['area'] ?? 'metro'); ?>">
                <div class="select-trigger">
                    <span><?php
                        $area = $_GET['area'] ?? 'metro';
                        switch ($area) {
                            case 'metro': echo 'Metro Manila'; break;
                            case 'outside': echo 'Outside Metro'; break;
                            case 'luzon': echo 'Any point of Luzon'; break;
                            default: echo ucfirst(str_replace('_', ' ', $area)); break;
                        }
                    ?></span>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="custom-options">
                    <div class="option <?php echo ($area === 'metro') ? 'selected' : ''; ?>" data-value="metro">Metro Manila</div>
                    <div class="option <?php echo ($area === 'outside') ? 'selected' : ''; ?>" data-value="outside">Outside Metro</div>
                    <div class="option <?php echo ($area === 'luzon') ? 'selected' : ''; ?>" data-value="luzon">Any point of Luzon</div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label>Pickup</label>
            <div class="date-trigger" onclick="openDateModal('pickup')">
                <span id="pickupDisplay"><?php echo !empty($pickup_date) ? htmlspecialchars(date('M j, Y g:i A', strtotime($pickup_date))) : 'Date & Time'; ?></span>
                <i class="fa-regular fa-calendar"></i>
            </div>
            <input type="hidden" id="pickupDateValue" value="<?php echo htmlspecialchars($pickup_date); ?>">
        </div>

        <div class="form-group">
            <label>Return</label>
            <div class="date-trigger" onclick="openDateModal('return')">
                <span id="returnDisplay"><?php echo !empty($return_date) ? htmlspecialchars(date('M j, Y g:i A', strtotime($return_date))) : 'Date & Time'; ?></span>
                <i class="fa-regular fa-calendar"></i>
            </div>
            <input type="hidden" id="returnDateValue" value="<?php echo htmlspecialchars($return_date); ?>">
        </div>

        <div class="form-group submit-group">
            <button type="submit" class="btn-search">
                <i class="fa-solid fa-arrow-right"></i>
            </button>
        </div>
    </form>
</div>

<div id="resultsContainer" style="<?php echo $show_results ? '' : 'display: none;'; ?> padding: 4rem 2rem;">
    <div id="resultsLoader" class="cars-grid" style="<?php echo $show_results && empty($cars) ? 'display: block;' : 'display: none;'; ?>">
        <div class="skeleton-card">
            <div class="skeleton-image shim"></div>
            <div class="skeleton-details">
                <div class="skeleton-line shim" style="width: 60%"></div>
                <div class="skeleton-line shim" style="width: 40%"></div>
                <div class="skeleton-button shim"></div>
            </div>
        </div>
        <div class="skeleton-card">
            <div class="skeleton-image shim"></div>
            <div class="skeleton-details">
                <div class="skeleton-line shim" style="width: 70%"></div>
                <div class="skeleton-line shim" style="width: 30%"></div>
                <div class="skeleton-button shim"></div>
            </div>
        </div>
        <div class="skeleton-card">
            <div class="skeleton-image shim"></div>
            <div class="skeleton-details">
                <div class="skeleton-line shim" style="width: 50%"></div>
                <div class="skeleton-line shim" style="width: 50%"></div>
                <div class="skeleton-button shim"></div>
            </div>
        </div>
    </div>

    <div id="resultsContent" style="<?php echo $show_results && !empty($cars) ? 'display: block;' : 'display: none;'; ?>">
    <div class="results-header" style="text-align: center; margin-bottom: 3rem;">
        <h2 class="section-heading">Available Vehicles</h2>
        <p class="section-subheading">Hand-picked premium rides for your journey</p>
    </div>

    <div class="results-meta" style="display: flex; justify-content: space-between; max-width: 1200px; margin: 0 auto 2rem; padding: 0 20px;">
        <span class="results-count"><?php echo count($cars); ?> vehicles found</span>
        <div class="sort-wrapper">
             <span>Sort by: Price <i class="fa-solid fa-chevron-down"></i></span>
        </div>
    </div>

    <div class="cars-grid" id="carsGrid">
        <?php if ($show_results && !empty($cars)): ?>
            <?php foreach ($cars as $car): ?>
                <div class="car-card" style="border: 1px solid #eee; border-radius: 12px; overflow: hidden; transition: transform 0.2s; background: #fff;">
                    <div class="car-image-wrapper" style="height: 200px; overflow: hidden;">
                        <!-- Image path from DB is likely 'uploads/cars/...'. Adjusting base path to public/ -->
                        <img src="/project_xcelrent/public/<?php echo htmlspecialchars($car['image'] ?? 'assets/img/default_car.jpg'); ?>"
                             alt="<?php echo htmlspecialchars($car['name'] ?? 'Car'); ?>"
                             style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div class="car-details" style="padding: 1.5rem;">
                        <h3 style="margin: 0 0 0.5rem; font-size: 1.25rem;"><?php echo htmlspecialchars($car['name'] ?? 'Unknown Model'); ?></h3>
                        <p style="color: #666; font-size: 0.9rem; margin-bottom: 1rem;">
                            <i class="fa-solid fa-user" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($car['seats'] ?? '4'); ?> Seats &nbsp;|&nbsp;
                            <i class="fa-solid fa-gas-pump" style="margin-right: 5px;"></i> <?php echo htmlspecialchars($car['fuel'] ?? 'Gas'); ?>
                        </p>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-weight: 700; font-size: 1.2rem; color: #22c55e;">₱<?php echo number_format($car['price'] ?? 0); ?>/day</span>
                            <!-- You may need to adjust the onclick function based on your JS implementation -->
                            <button class="btn btn-primary" onclick="viewCar(<?php echo $car['id']; ?>)">View</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php elseif ($show_results && empty($cars)): ?>
            <div class="no-results-message" style="text-align: center; padding: 3rem; grid-column: 1 / -1;">
                <div style="font-size: 3rem; margin-bottom: 1rem; color: #ccc;">
                    <i class="fa-solid fa-car-side"></i>
                </div>
                <h3>No cars available for the selected dates</h3>
                <p style="color: #666; margin-top: 0.5rem;">Try selecting different pickup and return dates</p>
            </div>
        <?php endif; ?>
        </div>

    <div class="pagination-container" style="display: flex; justify-content: center; margin-top: 4rem;">
        </div>
</div>
    </div>
</div>
    <section class="advantages-section" id="advantages">
        <div class="advantages-grid">
            <div class="advantage-card">
                <i class="fa-solid fa-tag advantage-icon"></i>
                <h3>Best Rates</h3>
                <p>Guaranteed lowest market prices.</p>
            </div>
            <div class="advantage-card">
                <i class="fa-solid fa-car advantage-icon"></i>
                <h3>Quality Fleet</h3>
                <p>Maintained for safety and comfort.</p>
            </div>
            <div class="advantage-card">
                <i class="fa-solid fa-shield-halved advantage-icon"></i>
                <h3>Secure</h3>
                <p>Verified hosts and transparent contracts.</p>
            </div>
        </div>
        <section class="testimonials-section" id="testimonials">
    <div class="section-header">
        <h2 class="section-heading">What Our Renters Say</h2>
        <p class="section-subheading">Don't just take our word for it—hear from our community.</p>
    </div>

    <div class="testimonials-grid">
        <div class="testimonial-card">
            <div class="stars">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <p class="feedback-text">"The booking process was incredibly smooth. The car was spotless and ready exactly when I needed it. Best rental experience in QC!"</p>
            <div class="user-info">
                <div class="user-details">
                    <p class="user-name">James Rodriguez</p>
                    <p class="user-status">Verified Renter</p>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <div class="stars">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <p class="feedback-text">"Super affordable rates compared to others. I used the 'With Driver' service for a family trip to Tagaytay and it was very professional."</p>
            <div class="user-info">
                <div class="user-details">
                    <p class="user-name">Maria Santos</p>
                    <p class="user-status">Verified Renter</p>
                </div>
            </div>
        </div>

        <div class="testimonial-card">
            <div class="stars">
                <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i>
            </div>
            <p class="feedback-text">"I love the transparency. No hidden fees, no complicated contracts. Xcelrent is now my go-to for my monthly business trips."</p>
            <div class="user-info">
                <div class="user-details">
                    <p class="user-name">Kevin Lee</p>
                    <p class="user-status">Verified Renter</p>
                </div>
            </div>
        </div>
    </div>
    </section>

    <footer id="contact">
        <div class="footer-container">
            <div class="footer-left">
                <div class="brand-location-group">
                    <h2 class="logo">Xcelrent<span class="dot">.</span></h2>
                    <span class="divider-slash">/</span>
                    <p class="location-text">Batasan Hills, Quezon City, Metro Manila</p>
                </div>
                <p class="copyright">&copy; 2026 Xcelrent Car Rental.</p>
            </div>

            <div class="footer-right">
                <div class="social-links">
                    <a href="https://www.facebook.com/xcelrentcarrental"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="https://www.tiktok.com/@xcelrent"><i class="fa-brands fa-tiktok"></i></a>
                </div>
            </div>
        </div>
    </footer>

<section id="bookingDetailsPage" class="page-section" style="display: none; padding: 4rem 0; background: #fff;">
    <div style="max-width: 1000px; margin: 0 auto; padding: 0 20px;">

        <div style="position: relative; border-radius: 20px; overflow: hidden; margin-bottom: 2.5rem; height: 400px; box-shadow: var(--shadow-soft);">
            <img id="detailCarImg" src="" style="width: 100%; height: 100%; object-fit: cover;">
            <div style="position: absolute; bottom: 0; left: 0; right: 0; padding: 40px; background: linear-gradient(transparent, rgba(0,0,0,0.8)); color: white;">
                <h1 id="detailCarNameHero" style="font-size: 2.5rem; margin: 0; font-weight: 800;"></h1>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1.4fr 1fr; gap: 40px; align-items: start;">
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap;">
                     <div style="display: flex; align-items: center; gap: 1rem;">
                        <button onclick="goBackToResults()" title="Go Back" style="background: transparent; border: 1px solid var(--border-color); border-radius: 50%; width: 40px; height: 40px; cursor: pointer; font-size: 1rem; line-height: 40px; text-align: center;"><i class="fa-solid fa-arrow-left"></i></button>
                        <h2 id="detailCarName" style="font-size: 2rem; font-weight: 700; margin:0;"></h2>
                    </div>
                    <button class="btn btn-text" style="border: 1px solid var(--border-color); font-size: 0.75rem; font-weight: 700;">
                        <i class="fa-solid fa-circle-info"></i> SHOW GUIDELINES
                    </button>
                </div>

                <p style="color: var(--text-muted); margin-bottom: 2rem;">
                    <i class="fa-solid fa-location-dot" style="color: var(--accent-red);"></i> Batasan Hills, Quezon City, Metro Manila
                </p>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 3rem;">
                    <div class="spec-item"><i class="fa-solid fa-user" style="color:var(--accent-red); width: 25px;"></i> <span id="detailSeats"></span> seater</div>
                    <div class="spec-item"><i class="fa-solid fa-car" style="color:var(--accent-red); width: 25px;"></i> MPV</div>
                    <div class="spec-item"><i class="fa-solid fa-gears" style="color:var(--accent-red); width: 25px;"></i> <span id="detailTrans"></span></div>
                    <div class="spec-item"><i class="fa-solid fa-gas-pump" style="color:var(--accent-red); width: 25px;"></i> <span id="detailFuel"></span></div>
                    <div class="spec-item" style="color: var(--accent-red); font-weight: 700;">
                        <i class="fa-solid fa-calendar-xmark" style="width: 25px;"></i> Coding on Monday
                    </div>
                </div>

                <div style="border: 1px solid var(--border-color); border-radius: 16px; overflow: hidden;">
                    <div onclick="openDateModal('pickup')" style="padding: 20px; border-bottom: 1px solid var(--border-color); cursor: pointer; display: flex; justify-content: space-between;">
                        <div>
                            <span id="finalPickup" style="display: block; font-weight: 700; font-size: 1.1rem;">-</span>
                            <small style="color: var(--text-muted);">Pick Up Date</small>
                        </div>
                        <i class="fa-solid fa-pen" style="color: var(--accent-red);"></i>
                    </div>
                    <div onclick="openDateModal('return')" style="padding: 20px; border-bottom: 1px solid var(--border-color); cursor: pointer; display: flex; justify-content: space-between;">
                        <div>
                            <span id="finalReturn" style="display: block; font-weight: 700; font-size: 1.1rem;">-</span>
                            <small style="color: var(--text-muted);">Return Date</small>
                        </div>
                        <i class="fa-solid fa-pen" style="color: var(--accent-red);"></i>
                    </div>
                    <div style="padding: 20px; background: #f9fafb;">
                        <span id="finalDuration" style="display: block; font-weight: 700;">1 day</span>
                        <small style="color: var(--text-muted);">Rental Length</small>
                    </div>
                </div>
            </div>

            <div style="text-align: right;">
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: #22c55e; font-size: 2rem; margin: 0;">PHP <span id="priceBase">0.00</span></h3>
                    <small style="color: var(--text-muted);">Full Payment Upon Pick Up</small>
                </div>
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: #22c55e; font-size: 2rem; margin: 0;">PHP <span id="priceFee">0.00</span></h3>
                    <small style="color: var(--text-muted);">Reservation Fee</small>
                </div>
                <div style="border-top: 2px solid var(--border-color); padding-top: 1.5rem; margin-bottom: 2.5rem;">
                    <h3 style="color: #22c55e; font-size: 2.5rem; margin: 0; font-weight: 800;">PHP <span id="priceTotal">0.00</span></h3>
                    <small style="color: var(--text-muted);">Total Rental Fee (Inclusive of VAT and fees)</small>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 15px; margin-bottom: 2rem; opacity: 0.6;">
                    <i class="fa-brands fa-cc-visa fa-2x"></i>
                    <i class="fa-brands fa-cc-mastercard fa-2x"></i>
                    <i class="fa-solid fa-qrcode fa-2x"></i>
                </div>

                <button class="btn btn-primary full-width" style="padding: 1.2rem; font-size: 1.1rem; border-radius: 12px; font-weight: 700;" onclick="finalizeBooking()">CONFIRM BOOKING</button>
            </div>
        </div>
    </div>
</section>

<script>
// Function to handle search from the fleet page (needed for the cars page search form)
function searchCarsFromFleet(e) {
    e.preventDefault();

    const pickupDate = document.getElementById('pickupDateValue').value;
    const returnDate = document.getElementById('returnDateValue').value;

    if (!pickupDate || !returnDate) {
        const triggers = document.querySelectorAll('.date-trigger');
        triggers.forEach(trigger => {
            const inputId = trigger.nextElementSibling.id;
            if (!document.getElementById(inputId).value) {
                trigger.classList.add('input-error');
                setTimeout(() => trigger.classList.remove('input-error'), 500);
            }
        });
        return;
    }

    // Redirect to the cars page with search parameters
    const pickupParam = encodeURIComponent(pickupDate);
    const returnParam = encodeURIComponent(returnDate);
    window.location.href = `?page=cars&pickup=${pickupParam}&return=${returnParam}`;
}
</script>

<script>
// Handle section scrolling when page loads
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const section = urlParams.get('section');

    if (section === 'testimonials') {
        // Wait a bit for page to load, then scroll to testimonials
        setTimeout(function() {
            scrollToSection('testimonials');
        }, 100);
    }
});

// Function to view car details and proceed to booking
function viewCar(carId) {
    if (!carId) {
        console.error("viewCar called with no carId.");
        return;
    }

    // Get the selected dates from the form
    const pickupDate = document.getElementById('pickupDateValue')?.value;
    const returnDate = document.getElementById('returnDateValue')?.value;

    // Redirect to the booking page with car ID and dates
    let bookingUrl = `?page=booking&car_id=${carId}`;
    if (pickupDate && returnDate) {
        bookingUrl += `&pickup=${encodeURIComponent(pickupDate)}&return=${encodeURIComponent(returnDate)}`;
    }

    window.location.href = bookingUrl;
}
// Add JavaScript to handle custom select functionality
document.addEventListener('DOMContentLoaded', function() {
    // Handle custom select for driver type
    const typeSelect = document.getElementById('typeSelect');
    const typeTrigger = typeSelect.querySelector('.select-trigger');
    const typeOptions = typeSelect.querySelectorAll('.option');
    const typeHiddenInput = typeSelect.querySelector('input[type="hidden"]');

    typeTrigger.addEventListener('click', function() {
        const optionsContainer = this.nextElementSibling;
        optionsContainer.classList.toggle('active');
    });

    typeOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            typeOptions.forEach(opt => opt.classList.remove('selected'));

            // Add selected class to clicked option
            this.classList.add('selected');

            // Update the trigger text
            typeTrigger.querySelector('span').textContent = this.textContent;

            // Update the hidden input value
            typeHiddenInput.value = this.getAttribute('data-value');

            // Close the options
            this.parentElement.classList.remove('active');
        });
    });

    // Handle custom select for destination
    const destSelect = document.getElementById('destinationSelect');
    const destTrigger = destSelect.querySelector('.select-trigger');
    const destOptions = destSelect.querySelectorAll('.option');
    const destHiddenInput = destSelect.querySelector('input[type="hidden"]');

    destTrigger.addEventListener('click', function() {
        const optionsContainer = this.nextElementSibling;
        optionsContainer.classList.toggle('active');
    });

    destOptions.forEach(option => {
        option.addEventListener('click', function() {
            // Remove selected class from all options
            destOptions.forEach(opt => opt.classList.remove('selected'));

            // Add selected class to clicked option
            this.classList.add('selected');

            // Update the trigger text
            destTrigger.querySelector('span').textContent = this.textContent;

            // Update the hidden input value
            destHiddenInput.value = this.getAttribute('data-value');

            // Close the options
            this.parentElement.classList.remove('active');
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!typeSelect.contains(e.target)) {
            const options = typeSelect.querySelector('.custom-options');
            if (options) options.classList.remove('active');
        }

        if (!destSelect.contains(e.target)) {
            const options = destSelect.querySelector('.custom-options');
            if (options) options.classList.remove('active');
        }
    });
});
</script>
