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
    
    // Navigate to booking page with car ID and dates
    const params = new URLSearchParams({
        page: 'booking',
        car_id: carId,
        pickup: pickupDate,
        return: returnDate
    });
    
    window.location.href = `?${params.toString()}`;
}

function goBackToResults() {
    // Get the previous search parameters from session storage or URL
    const pickupDate = sessionStorage.getItem('pickup_date') || '';
    const returnDate = sessionStorage.getItem('return_date') || '';
    
    if (pickupDate && returnDate) {
        window.location.href = `?page=cars&pickup=${encodeURIComponent(pickupDate)}&return=${encodeURIComponent(returnDate)}`;
    } else {
        window.location.href = '?page=cars';
    }
}