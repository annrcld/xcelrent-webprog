// public/assets/js/search.js

/* --- SEARCH FUNCTIONALITY --- */
function searchCars(e) {
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

    // Redirect to the home page with search parameters to display filtered results
    const pickupParam = encodeURIComponent(pickupDate);
    const returnParam = encodeURIComponent(returnDate);
    window.location.href = `?page=home&pickup=${pickupParam}&return=${returnParam}`;
}