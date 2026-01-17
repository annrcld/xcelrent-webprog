<section id="manage-cars" class="tab-content active">
    <h1 class="page-title">Manage Cars</h1>
    <p style="color: #666; margin-bottom: 20px;">Control the visibility and health of your fleet.</p>
    
    <div class="panel">
        <div class="search-filter-bar">
            <input type="text" id="searchInput" placeholder="Search by Unit Name or Owner..." onkeyup="loadCars()">
            <select id="categoryFilter" onchange="loadCars()">
                <option value="">All Categories</option>
                <option value="Sedan">Sedan</option>
                <option value="SUV">SUV</option>
                <option value="Van">Van</option>
            </select>
            <select id="statusFilter" onchange="loadCars()">
                <option value="">All Status</option>
                <option value="live">Live</option>
                <option value="hidden">Hidden</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Car Image</th>
                    <th>Unit Name</th>
                    <th>Category</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="carsTableBody">
                </tbody>
        </table>
    </div>
</section>

<script>
// 1. Function to Fetch and Display Cars
function loadCars() {
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    fetch(`api/get_cars.php?category=${category}&status=${status}`)
        .then(res => res.json())
        .then(response => {
            if (!response.success) return;
            
            const tbody = document.getElementById('carsTableBody');
            tbody.innerHTML = '';
            
            response.data.forEach(car => {
                tbody.innerHTML += `
                    <tr>
                        <td><img src="../${car.car_image || 'assets/placeholder.png'}" width="60" style="border-radius:5px;"></td>
                        <td><strong>${car.brand} ${car.model}</strong><br><small>${car.plate_number}</small></td>
                        <td>${car.category}</td>
                        <td>${car.owner || 'Admin'}</td>
                        <td><span class="status-tag ${car.status}">${car.status}</span></td>
                        <td>
                            <button onclick="editCar(${car.id})" class="btn-action edit">Edit</button>
                            <button onclick="deleteCar(${car.id})" class="btn-action delete" style="color:red;">Delete</button>
                        </td>
                    </tr>
                `;
            });
        });
}

// 2. Redirect to add_car page with the ID for editing
function editCar(id) {
    // This will open your existing add_car page and pass the ID
    // We will update that page next to handle the "Edit" mode
    window.location.href = `?page=add_car&edit_id=${id}`; 
}

// 3. Delete Function
function deleteCar(id) {
    if(confirm("Are you sure you want to delete this vehicle? This cannot be undone.")) {
        fetch(`api/delete_car.php?id=${id}`) // Note: You'll need to create this simple API file
        .then(res => res.json())
        .then(res => {
            alert(res.msg);
            loadCars();
        });
    }
}

// Initial Load
document.addEventListener('DOMContentLoaded', loadCars);
</script>