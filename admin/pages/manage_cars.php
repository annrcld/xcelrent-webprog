<!-- pages/manage_cars.php -->
<section id="manage-cars" class="tab-content active">
    <h1 class="page-title">Manage Cars</h1>
    <p style="color: #666; margin-bottom: 20px;">Control the visibility and health of your fleet.</p>
    
    <div class="panel">
        <div class="search-filter-bar">
            <input type="text" id="searchInput" placeholder="Search by Plate or Brand..." disabled title="Search not implemented">
            
            <select id="categoryFilter">
                <option value="">All Categories</option>
                <option value="Sedan">Sedan</option>
                <option value="SUV">SUV</option>
                <option value="Van">Van</option>
            </select>
            
            <select id="statusFilter">
                <option value="">All Status</option>
                <option value="live">Live</option>
                <option value="hidden">Hidden</option>
                <option value="maintenance">Maintenance</option>
            </select>
        </div>

        <table class="data-table">
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Unit (Brand / Model)</th>
                    <th>Plate & Category</th>
                    <th>Owner</th>
                    <th>Status</th>
                    <th>Docs Verified</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="carsTableBody">
                <!-- Cars loaded via JS -->
            </tbody>
        </table>
    </div>
</section>

<!-- ONLY THIS SCRIPT IS LOADED -->
<script src="assets/js/manage_cars.js"></script>