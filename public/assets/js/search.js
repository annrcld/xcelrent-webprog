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

    // Get the currently selected values from the custom selects
    const driverTypeOption = document.querySelector('#typeSelect .option.selected');
    const areaOption = document.querySelector('#destinationSelect .option.selected');

    const driverType = driverTypeOption ? driverTypeOption.getAttribute('data-value') : 'self';
    const area = areaOption ? areaOption.getAttribute('data-value') : 'metro';

    // Redirect to the home page with search parameters to display filtered results
    const pickupParam = encodeURIComponent(pickupDate);
    const returnParam = encodeURIComponent(returnDate);
    const driverParam = encodeURIComponent(driverType);
    const areaParam = encodeURIComponent(area);
    window.location.href = `?page=home&pickup=${pickupParam}&return=${returnParam}&driver_type=${driverParam}&area=${areaParam}`;
}