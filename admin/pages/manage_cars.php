<!-- pages/manage_cars.php -->
<section id="manage-cars" class="tab-content active">
    <h1 class="page-title">Manage Cars</h1>
    <p style="color: #666; margin-bottom: 20px;">Control the visibility and health of your fleet.</p>
    
    <div class="panel">
            <div class="filter-grid">
                <div class="filter-row">
                    <div class="filter-col">
                        <input type="text" id="searchInput" placeholder="Search by Plate or Brand..." onkeyup="loadCars()">
                    </div>
                    <div class="filter-col">
                        <select id="categoryFilter" onchange="loadCars()">
                            <option value="">All Categories</option>
                            <option value="Sedan">Sedan</option>
                            <option value="SUV">SUV</option>
                            <option value="Van">Van</option>
                        </select>
                    </div>
                </div>

                <div class="filter-row">
                    <div class="filter-col">
                        <select id="locationFilter" onchange="loadCars()">
                            <option value="">All Locations</option>
                            <option value="Quezon City">Quezon City</option>
                            <option value="Manila">Manila</option>
                            <option value="Pasig">Pasig</option>
                            <option value="Bulacan">Bulacan</option>
                        </select>
                    </div>
                    <div class="filter-col">
                        <select id="driverTypeFilter" onchange="loadCars()">
                            <option value="">All Driver Types</option>
                            <option value="self_drive">Self Drive</option>
                            <option value="with_driver">With Driver</option>
                        </select>
                    </div>
                </div>

                <div class="filter-row">
                    <div class="filter-col">
                        <select id="statusFilter" onchange="loadCars()">
                            <option value="">All Status</option>
                            <option value="live">Live</option>
                            <option value="hidden">Hidden</option>
                            <option value="maintenance">Maintenance</option>
                        </select>
                    </div>
                    <div class="filter-col">
                        <!-- Empty or add "Reset Filters" button later -->
                    </div>
                </div>
            </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Unit (Brand / Model)</th>
                    <th>Category</th>
                    <th>Plate Number</th>
                    <th>Location</th>
                    <th>Driver Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="carsTableBody">
                <!-- Cars loaded via JS -->
            </tbody>
        </table>
    </div>
</section>

<script src="assets/js/manage_cars.js"></script>