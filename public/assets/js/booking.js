// public/assets/js/booking.js

/**
 * Navigates to the booking page with car details
 * @param {number} carId The ID of the car to view.
 */
function viewCar(carId) {
    if (!carId) {
        console.error("viewCar called with no carId.");
        return;
    }

    // Get the current search parameters
    const pickupDate = document.getElementById('pickupDateValue')?.value || '';
    const returnDate = document.getElementById('returnDateValue')?.value || '';
    
    // Use ABSOLUTE path to index.php
    window.location.href = `/project_xcelrent/public/index.php?page=booking&car_id=${carId}&pickup=${encodeURIComponent(pickupDate)}&return=${encodeURIComponent(returnDate)}`;
}

function goBackToResults() {
    // Get the previous search parameters from session storage or URL
    const pickupDate = sessionStorage.getItem('pickup_date') || '';
    const returnDate = sessionStorage.getItem('return_date') || '';
    
    if (pickupDate && returnDate) {
        window.location.href = `/project_xcelrent/public/index.php?page=cars&pickup=${encodeURIComponent(pickupDate)}&return=${encodeURIComponent(returnDate)}`;
    } else {
        window.location.href = '/project_xcelrent/public/index.php?page=cars';
    }
}