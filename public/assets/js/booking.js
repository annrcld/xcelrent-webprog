/**
 * Fetches car details from the server and displays the booking page.
 * @param {number} carId The ID of the car to view.
 */
async function viewCar(carId) {
    if (!carId) {
        console.error("viewCar called with no carId.");
        return;
    }

    try {
        const response = await fetch(`/project_xcelrent/public/api/get_car_details.php?id=${carId}`);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const car = await response.json();

        if (car.error) {
            alert(car.error);
            return;
        }

        // --- Populate Booking Page ---
        document.getElementById('detailCarImg').src = `/project_xcelrent/public/${car.image || 'assets/img/default_car.jpg'}`;
        document.getElementById('detailCarNameHero').textContent = car.name;
        document.getElementById('detailCarName').textContent = car.name;
        
        // Specs (assuming 'transmission' and 'category' columns exist in your 'cars' table)
        document.getElementById('detailSeats').textContent = car.seats || 'N/A';
        document.getElementById('detailFuel').textContent = car.fuel || 'N/A';
        document.getElementById('detailTrans').textContent = car.transmission || 'Automatic';

        const categoryElement = document.querySelector('.spec-item:nth-child(2)');
        if (categoryElement) {
            categoryElement.innerHTML = `<i class="fa-solid fa-car" style="color:var(--accent-red); width: 25px;"></i> ${car.category || 'N/A'}`;
        }

        // --- Price Calculation ---
        const pickupDateStr = document.getElementById('pickupDateValue').value;
        const returnDateStr = document.getElementById('returnDateValue').value;
        let rentalDays = 1;

        if (pickupDateStr && returnDateStr) {
            const pickupDate = new Date(pickupDateStr);
            const returnDate = new Date(returnDateStr);
            const timeDiff = returnDate.getTime() - pickupDate.getTime();
            const calculatedDays = Math.ceil(timeDiff / (1000 * 3600 * 24));
            rentalDays = calculatedDays > 0 ? calculatedDays : 1;
        }

        const pricePerDay = parseFloat(car.price) || 0;
        const totalBasePrice = pricePerDay * rentalDays;
        const reservationFee = totalBasePrice * 0.10; // Example: 10% reservation fee

        document.getElementById('priceBase').textContent = totalBasePrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('priceFee').textContent = reservationFee.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        document.getElementById('priceTotal').textContent = totalBasePrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

        // --- Date & Duration Display ---
        document.getElementById('finalPickup').textContent = document.getElementById('pickupDisplay').textContent;
        document.getElementById('finalReturn').textContent = document.getElementById('returnDisplay').textContent;
        document.getElementById('finalDuration').textContent = `${rentalDays} day${rentalDays !== 1 ? 's' : ''}`;

        // --- Switch Views ---
        const mainContentSelectors = ['.hero', '.search-box', '#resultsContainer', '.advantages-section', '.testimonials-section', 'footer', '.page-container'];
        mainContentSelectors.forEach(selector => {
            const element = document.querySelector(selector);
            if (element) element.style.display = 'none';
        });

        document.getElementById('bookingDetailsPage').style.display = 'block';
        window.scrollTo(0, 0);

    } catch (error) {
        console.error('Failed to fetch and display car details:', error);
        alert('An error occurred while loading car details. Please try again.');
    }
}

function goBackToResults() {
    window.location.reload();
}