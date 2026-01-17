// assets/js/manage_cars.js

function loadCars() {
    const category = document.getElementById('categoryFilter')?.value || '';
    const location = document.getElementById('locationFilter')?.value || '';
    const driverType = document.getElementById('driverTypeFilter')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';

    fetch(`api/get_cars.php?category=${encodeURIComponent(category)}&status=${encodeURIComponent(status)}&driver_type=${encodeURIComponent(driverType)}&location=${encodeURIComponent(location)}`)
        .then(res => res.json())
        .then(response => {
            if (response.success) {
                renderCars(response.data);
            } else {
                console.error('Failed to load cars:', response);
            }
        })
        .catch(err => {
            console.error('Network error:', err);
            document.getElementById('carsTableBody').innerHTML = '<tr><td colspan="8">Error loading data</td></tr>';
        });
}

function renderCars(cars) {
    const tbody = document.getElementById('carsTableBody');
    if (!tbody) return;

    tbody.innerHTML = '';

    cars.forEach(car => {
        // Status badge class
        let statusClass = car.status === 'live' ? 'badge-green' : 
                         (car.status === 'maintenance' ? 'badge-orange' : 'badge-gray');

        // Clean driver type label
        let driverTypeLabel = 'N/A';
        if (car.driver_type === 'self_drive') driverTypeLabel = 'Self Drive';
        else if (car.driver_type === 'with_driver') driverTypeLabel = 'With Driver';

        const imageUrl = car.car_image || 'assets/no-car.png';

        tbody.innerHTML += `
            <tr>
                <td><img src="${imageUrl}" class="table-img"></td>
                <td>
                    <strong>${car.brand} ${car.model}</strong><br>
                    <small>${car.plate_number}</small>
                </td>
                <td>${car.category}</td>
                <td>${car.plate_number}</td>
                <td>${car.location || 'N/A'}</td>
                <td>${driverTypeLabel}</td>
                <td class="status-cell">
                    <span class="badge ${statusClass}">${car.status.toUpperCase()}</span>
                </td>
                <td class="actions-cell">
                    <button onclick="editCar(${car.id})" class="action-btn edit" title="Edit Car">
                        <i data-lucide="pencil"></i>
                    </button>
                    <button onclick="deleteCar(${car.id})" class="action-btn delete" title="Delete Car">
                        <i data-lucide="trash-2"></i>
                    </button>
                </td>
            </tr>
        `;
    });

    // Re-render Lucide icons after DOM update
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function editCar(id) {
    window.location.href = `?page=add_car&edit_id=${id}`;
}

function deleteCar(id) {
    if(confirm("Are you sure you want to delete this vehicle? This cannot be undone.")) {
        fetch(`api/delete_car.php?id=${id}`)
        .then(res => res.json())
        .then(res => {
            alert(res.msg);
            loadCars();
        })
        .catch(err => {
            alert('Error deleting vehicle. Please try again.');
            console.error(err);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');
    const driverTypeFilter = document.getElementById('driverTypeFilter');
    const locationFilter = document.getElementById('locationFilter');

    if (categoryFilter) categoryFilter.addEventListener('change', loadCars);
    if (statusFilter) statusFilter.addEventListener('change', loadCars);
    if (driverTypeFilter) driverTypeFilter.addEventListener('change', loadCars);
    if (locationFilter) locationFilter.addEventListener('change', loadCars);

    loadCars();
});