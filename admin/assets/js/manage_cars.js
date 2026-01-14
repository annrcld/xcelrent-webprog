function renderCars(cars) {
    const tbody = document.getElementById('carTableBody');
    tbody.innerHTML = '';

    cars.forEach(car => {
        // Status Badge Logic
        let statusClass = car.status === 'live' ? 'badge-green' : (car.status === 'maintenance' ? 'badge-orange' : 'badge-gray');
        
        tbody.innerHTML += `
            <tr>
                <td><img src="${car.car_image || 'assets/no-car.png'}" class="table-img"></td>
                <td>
                    <strong>${car.brand} ${car.model}</strong><br>
                    <small>${car.plate_number} | ${car.category}</small>
                </td>
                <td>${car.owner || 'N/A'}</td>
                <td><span class="badge ${statusClass}">${car.status.toUpperCase()}</span></td>
                <td><small>${car.verified_docs}/5 Verified</small></td>
                <td class="actions">
                    <button onclick="openEditModal(${car.id})" class="icon-btn edit"><i class="fas fa-edit"></i></button>
                    <button onclick="toggleStatus(${car.id}, '${car.status}')" class="icon-btn status"><i class="fas fa-eye-slash"></i></button>
                    <button onclick="deleteCar(${car.id})" class="icon-btn delete"><i class="fas fa-trash"></i></button>
                </td>
            </tr>
        `;
    });
}