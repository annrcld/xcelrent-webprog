// assets/js/manage_cars.js

function loadCars() {
    const category = document.getElementById('categoryFilter')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';

    fetch(`api/get_cars.php?category=${encodeURIComponent(category)}&status=${encodeURIComponent(status)}`)
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(response => {
            if (response.success && Array.isArray(response.data)) {
                renderCars(response.data);
            } else {
                console.error('API error:', response.message || 'Unknown error');
                showEmptyMessage('Failed to load cars.');
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            showEmptyMessage('Error loading data. Please try again later.');
        });
}

function showEmptyMessage(message) {
    const tbody = document.getElementById('carsTableBody');
    if (tbody) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center">${message}</td></tr>`;
    }
}

function renderCars(cars) {
    const tbody = document.getElementById('carsTableBody');
    if (!tbody) return;

    if (cars.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">No cars found.</td></tr>';
        return;
    }

    tbody.innerHTML = '';

    cars.forEach(car => {
        let statusClass = '';
        switch (car.status) {
            case 'live': statusClass = 'badge-green'; break;
            case 'maintenance': statusClass = 'badge-orange'; break;
            case 'hidden': statusClass = 'badge-gray'; break;
            default: statusClass = 'badge-gray';
        }

        const imageUrl = car.car_image ? car.car_image : 'assets/no-car.png';

        const row = `
            <tr>
                <td><img src="${imageUrl}" alt="Car" class="table-img"></td>
                <td><strong>${car.brand} ${car.model}</strong></td>
                <td>${car.plate_number}<br><small>${car.category}</small></td>
                <td>${car.owner || 'Admin'}</td>
                <td><span class="badge ${statusClass}">${car.status.charAt(0).toUpperCase() + car.status.slice(1)}</span></td>
                <td><small>${(car.verified_docs || 0)}/5 Verified</small></td>
                <td class="actions">
                    <button onclick="editCar(${car.id})" class="icon-btn edit" title="Edit">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button onclick="deleteCar(${car.id})" class="icon-btn delete" title="Delete">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

function editCar(id) {
    if (!id) {
        alert('Invalid car ID.');
        return;
    }
    window.location.href = `?page=add_car&edit_id=${id}`;
}

function deleteCar(id) {
    if (!id) {
        alert('Invalid car ID.');
        return;
    }

    if (!confirm("Are you sure you want to delete this vehicle? This cannot be undone.")) {
        return;
    }

    fetch('api/delete_car.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${encodeURIComponent(id)}`
    })
    .then(res => res.json())
    .then(data => {
        alert(data.msg || (data.success ? 'Deleted successfully!' : 'An error occurred.'));
        if (data.success) {
            loadCars();
        }
    })
    .catch(err => {
        console.error('Delete error:', err);
        alert('Network error. Please try again.');
    });
}

document.addEventListener('DOMContentLoaded', () => {
    const categoryFilter = document.getElementById('categoryFilter');
    const statusFilter = document.getElementById('statusFilter');

    if (categoryFilter) categoryFilter.addEventListener('change', loadCars);
    if (statusFilter) statusFilter.addEventListener('change', loadCars);

    loadCars();
});