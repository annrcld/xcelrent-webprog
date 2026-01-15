// ============================================
// TAB & FORM UTILITIES
// ============================================

// Tab switching
function showTab(tabId, btn) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.nav-btn').forEach(b => b.classList.remove('active'));

    const tabEl = document.getElementById(tabId);
    if (!tabEl) {
        console.warn('showTab: no element with id', tabId);
        return;
    }
    tabEl.classList.add('active');
    if (btn && btn.classList) btn.classList.add('active');

    // Load data when tab is shown
    if (tabId === 'dashboard') loadDashboard();
    if (tabId === 'manage-cars') loadCars();
    if (tabId === 'bookings') loadBookings();
    if (tabId === 'renters') loadRenters();
    if (tabId === 'operators') loadOperators();
}

// Coding Day Logic
function updateCoding() {
    const input = document.getElementById('plateInput');
    const display = document.getElementById('codingDisplay');
    
    // 1. Force Uppercase
    let val = input.value.toUpperCase();
    
    // 2. Automatically add the dash after 3 letters
    if (val.length === 3 && !val.includes('-')) {
        val = val + '-';
    }
    
    // 3. Limit the total length and update input value
    input.value = val.substring(0, 8); 

    // 4. Coding Day Logic
    const lastDigit = input.value.trim().slice(-1);
    const codingMap = {
        '1': 'Monday', '2': 'Monday',
        '3': 'Tuesday', '4': 'Tuesday',
        '5': 'Wednesday', '6': 'Wednesday',
        '7': 'Thursday', '8': 'Thursday',
        '9': 'Friday', '0': 'Friday'
    };

    // Only display coding if the format is complete (e.g., AAA-1111)
    if (input.value.length === 8) {
        display.value = codingMap[lastDigit] || 'Invalid Digit';
    } else {
        display.value = ''; // Keep empty until full plate is entered
    }
}

// Seating Logic based on vehicle type
function handleTypeChange() {
    const type = document.getElementById('vType').value;
    const seating = document.getElementById('seating');

    if (type === 'Sedan') seating.value = 4;
    else if (type === 'SUV') seating.value = 7;
    else if (type === 'Van') seating.value = 10;
}

function adjustSeating(val) {
    const type = document.getElementById('vType').value;
    const input = document.getElementById('seating');
    let current = parseInt(input.value || '0', 10);
    let next = current + val;

    if (type === 'Sedan' && next >= 4 && next <= 5) input.value = next;
    if (type === 'SUV' && next >= 7 && next <= 8) input.value = next;
    if (type === 'Van' && next >= 10 && next <= 15) input.value = next;
}

// ============================================
// DASHBOARD
// ============================================

function loadDashboard() {
    fetch('./api/get_dashboard.php')
        .then(r => r.json())
        .then(payload => {
            if (!payload.success) throw new Error(payload.error || 'API error');
            const data = payload.data;
            const kpis = data.kpis || {};
            const recent = data.recent_bookings || [];

            // Render KPIs
            const kpiRow = document.getElementById('kpiRow');
            if (kpiRow) {
                kpiRow.innerHTML = `
                    <div class="kpi-card red-border"><h3>Total Cars</h3><p>${kpis.total_cars || 0}</p></div>
                    <div class="kpi-card"><h3>Live Cars</h3><p>${kpis.live_cars || 0}</p></div>
                    <div class="kpi-card"><h3>Active Bookings</h3><p>${kpis.active_bookings || 0}</p></div>
                    <div class="kpi-card"><h3>Revenue (30d)</h3><p>₱${Number(kpis.revenue_30d || 0).toLocaleString('en-PH', {minimumFractionDigits: 2})}</p></div>
                    <div class="kpi-card"><h3>New Renters (30d)</h3><p>${kpis.new_renters_30d || 0}</p></div>
                `;
            }

            // Render Recent Activity
            const recentPanel = document.getElementById('recentActivityPanel');
            if (recentPanel) {
                recentPanel.innerHTML = recent.map(b => `
                    <div class="kpi-card" style="padding: 15px; margin-bottom: 10px;">
                        <div style="font-weight: bold;">${b.brand} ${b.model} (${b.plate_number})</div>
                        <div style="color: #666; font-size: 14px;">${b.first_name} ${b.last_name}</div>
                        <div style="color: #666; font-size: 13px; margin: 5px 0;">${new Date(b.start_date).toLocaleString()} → ${new Date(b.end_date).toLocaleString()}</div>
                        <div><span style="background: #e63946; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">${b.status.toUpperCase()}</span> ₱${Number(b.total_amount).toLocaleString('en-PH', {minimumFractionDigits: 2})}</div>
                    </div>
                `).join('');
            }
        })
        .catch(err => {
            console.error('Dashboard load error:', err);
            const kpiRow = document.getElementById('kpiRow');
            if (kpiRow) kpiRow.textContent = 'Error loading dashboard';
        });
}

// ============================================
// MANAGE CARS
// ============================================

function loadCars() {
    const category = document.getElementById('categoryFilter')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';
    
    let url = 'api/get_cars.php';
    const params = new URLSearchParams();
    if (category) params.append('category', category);
    if (status) params.append('status', status);
    if (params.toString()) url += '?' + params.toString();

    fetch(url)
        .then(r => r.json())
        .then(payload => {
            if (!payload.success) throw new Error(payload.error);
            const cars = payload.data || [];

            const tbody = document.getElementById('carsTableBody');
            if (tbody) {
                tbody.innerHTML = cars.map(car => `
                    <tr>
                        <td><span style="background: #ddd; width: 40px; height: 40px; border-radius: 4px; display: inline-block;"></span></td>
                        <td>${car.brand} ${car.model}</td>
                        <td>${car.category}</td>
                        <td>${car.owner || 'N/A'}</td>
                        <td>
                            <label style="display:flex; align-items:center; gap:5px; cursor:pointer;">
                                <input type="checkbox" ${car.status === 'live' ? 'checked' : ''} onchange="toggleCarStatus(this, 'live', ${car.id})">
                                <span>${car.status === 'live' ? 'Live' : 'Hidden'}</span>
                            </label>
                        </td>
                        <td>
                            <label style="display:flex; align-items:center; gap:5px; cursor:pointer;">
                                <input type="checkbox" ${car.status === 'maintenance' ? 'checked' : ''} onchange="toggleCarStatus(this, 'maint', ${car.id})">
                                <span>${car.status === 'maintenance' ? 'Maintenance' : 'Off'}</span>
                            </label>
                        </td>
                        <td>
                            <button class="btn btn-black" onclick="editCar(${car.id})" style="padding: 5px 10px; font-size: 12px;">Edit</button>
                            <button class="btn btn-red" onclick="deleteCar(${car.id})" style="padding: 5px 10px; font-size: 12px;">Delete</button>
                        </td>
                    </tr>
                `).join('');
            }
        })
        .catch(err => {
            console.error('Cars load error:', err);
            const tbody = document.getElementById('carsTableBody');
            if (tbody) tbody.innerHTML = `<tr><td colspan="7">Error loading cars</td></tr>`;
        });
}

function filterCarsTable() {
    const search = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const rows = document.querySelectorAll('#carsTableBody tr');

    rows.forEach(row => {
        const brand = row.cells[1]?.textContent.toLowerCase() || '';
        const owner = row.cells[3]?.textContent.toLowerCase() || '';
        const matchSearch = brand.includes(search) || owner.includes(search);
        row.style.display = matchSearch ? '' : 'none';
    });

    // Also reload with API filters
    loadCars();
}

function toggleCarStatus(checkbox, type, carId) {
    const newStatus = checkbox.checked ? (type === 'live' ? 'live' : 'maintenance') : 'hidden';
    // TODO: Call API to update car status
    console.log(`Toggle car ${carId} status to ${newStatus}`);
}

function editCar(carId) {
    alert('Edit Car #' + carId + ' - Feature coming soon');
}

function deleteCar(carId) {
    if (confirm('Are you sure you want to delete this car?')) {
        // TODO: Call API to delete car
        console.log('Delete car ' + carId);
        loadCars();
    }
}

// ============================================
// MANAGE BOOKINGS
// ============================================

function loadBookings() {
    fetch('api/get_bookings.php')
        .then(r => r.json())
        .then(payload => {
            if (!payload.success) throw new Error(payload.error);
            const bookings = payload.data || [];

            const tbody = document.getElementById('bookingsTableBody');
            if (tbody) {
                tbody.innerHTML = bookings.map(b => `
                    <tr>
                        <td>${b.brand} ${b.model}</td>
                        <td>${b.first_name} ${b.last_name}</td>
                        <td>${new Date(b.start_date).toLocaleString()} → ${new Date(b.end_date).toLocaleString()}</td>
                        <td>
                            <select onchange="updateBookingStatus(${b.id}, this.value)" style="padding: 5px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="pending" ${b.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="confirmed" ${b.status === 'confirmed' ? 'selected' : ''}>Confirmed</option>
                                <option value="ongoing" ${b.status === 'ongoing' ? 'selected' : ''}>Ongoing</option>
                                <option value="completed" ${b.status === 'completed' ? 'selected' : ''}>Completed</option>
                                <option value="cancelled" ${b.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                        </td>
                        <td>
                            <button class="btn btn-black" onclick="viewPaymentProof(${b.id})" style="padding: 5px 10px; font-size: 12px;">Payment</button>
                        </td>
                    </tr>
                `).join('');
            }
        })
        .catch(err => {
            console.error('Bookings load error:', err);
            const tbody = document.getElementById('bookingsTableBody');
            if (tbody) tbody.innerHTML = `<tr><td colspan="5">Error loading bookings</td></tr>`;
        });
}

function updateBookingStatus(bookingId, status) {
    const formData = new FormData();
    formData.append('booking_id', bookingId);
    formData.append('status', status);

    fetch('api/update_booking.php', {method: 'POST', body: formData})
        .then(r => r.json())
        .then(payload => {
            if (payload.success) {
                console.log('Booking updated');
                loadBookings();
            } else {
                alert('Error: ' + payload.msg);
            }
        })
        .catch(err => console.error('Update error:', err));
}

function viewPaymentProof(bookingId) {
    document.getElementById('paymentModal').classList.add('active');
    // TODO: Load actual payment proof image/document
}

// ============================================
// RENTERS
// ============================================

function loadRenters() {
    const search = document.getElementById('userSearch')?.value || '';
    
    let url = 'api/get_renters.php';
    if (search) url += '?search=' + encodeURIComponent(search);

    fetch(url)
        .then(r => r.json())
        .then(payload => {
            if (!payload.success) throw new Error(payload.error);
            const renters = payload.data || [];

            const container = document.getElementById('usersContainer');
            if (container) {
                container.innerHTML = renters.map(renter => `
                    <div class="kpi-card" style="padding: 15px; margin-bottom: 10px;">
                        <div style="font-weight: bold;">${renter.first_name} ${renter.last_name}</div>
                        <div style="color: #666; font-size: 14px;">${renter.email}</div>
                        <div style="color: #666; font-size: 14px;">${renter.phone || 'N/A'}</div>
                        <div style="margin-top: 10px;">
                            <span style="background: #e8e8e8; padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px;">Rentals: ${renter.total_rentals}</span>
                            <span style="background: #e8e8e8; padding: 4px 8px; border-radius: 4px; font-size: 12px;">${renter.status}</span>
                        </div>
                        <div style="margin-top: 10px;">
                            <button class="btn btn-black" onclick="viewLicense(${renter.id})" style="padding: 5px 10px; font-size: 12px;">License</button>
                            <button class="btn btn-black" onclick="viewRentalHistory(${renter.id})" style="padding: 5px 10px; font-size: 12px;">History</button>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(err => {
            console.error('Renters load error:', err);
            const container = document.getElementById('usersContainer');
            if (container) container.textContent = 'Error loading renters';
        });
}

function viewLicense(renterId) {
    document.getElementById('licenseModal').classList.add('active');
    // TODO: Load renter's license document
}

function viewRentalHistory(renterId) {
    document.getElementById('rentalHistoryModal').classList.add('active');
    // TODO: Load renter's rental history
}

// ============================================
// OPERATORS
// ============================================

function loadOperators() {
    fetch('api/get_operators.php')
        .then(r => r.json())
        .then(payload => {
            if (!payload.success) throw new Error(payload.error);
            const operators = payload.data || [];

            const container = document.getElementById('operatorApplicationsContainer');
            if (container) {
                container.innerHTML = operators.map(op => `
                    <div class="kpi-card" style="padding: 15px; margin-bottom: 15px; border: 2px solid ${op.verified ? '#4caf50' : '#ff9800'};">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: bold; font-size: 16px;">${op.company_name || 'N/A'}</div>
                                <div style="color: #666;">${op.contact_name}</div>
                                <div style="color: #666; font-size: 14px;">${op.email}</div>
                                <div style="color: #666; font-size: 14px;">${op.phone}</div>
                                <div style="margin-top: 8px;">
                                    <span style="background: #e8e8e8; padding: 4px 8px; border-radius: 4px; font-size: 12px; margin-right: 5px;">Cars: ${op.total_cars}</span>
                                    <span style="background: ${op.verified ? '#4caf50' : '#ff9800'}; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">${op.verified ? 'Verified' : 'Pending'}</span>
                                </div>
                            </div>
                            <div>
                                ${!op.verified ? `
                                    <button class="btn btn-red" onclick="approveOperator(${op.id})" style="padding: 8px 15px; margin-bottom: 5px; width: 100%;">Approve</button>
                                    <button class="btn btn-black" onclick="rejectOperator(${op.id})" style="padding: 8px 15px; width: 100%;">Reject</button>
                                ` : `
                                    <button class="btn btn-black" onclick="viewOperatorDetails(${op.id})" style="padding: 8px 15px;">View</button>
                                `}
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        })
        .catch(err => {
            console.error('Operators load error:', err);
            const container = document.getElementById('operatorApplicationsContainer');
            if (container) container.textContent = 'Error loading operators';
        });
}

function approveOperator(opId) {
    if (confirm('Approve this operator?')) {
        // TODO: Call API to verify operator
        console.log('Approved operator ' + opId);
        loadOperators();
    }
}

function rejectOperator(opId) {
    if (confirm('Reject this operator?')) {
        // TODO: Call API to reject operator
        console.log('Rejected operator ' + opId);
        loadOperators();
    }
}

function viewOperatorDetails(opId) {
    alert('View details for operator ' + opId);
}

// ============================================
// MODAL FUNCTIONS
// ============================================

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal-overlay');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.classList.remove('active');
        }
    });
}

// Photo Gallery
let currentPhotoApp = 1;
let currentPhotoIndex = 1;

function viewPhotoGallery(appId, photoNum) {
    currentPhotoApp = appId;
    currentPhotoIndex = photoNum || 1;
    updatePhotoCounter();
    document.getElementById('photoGalleryModal').classList.add('active');
}

function previousPhoto() {
    if (currentPhotoIndex > 1) {
        currentPhotoIndex--;
        updatePhotoCounter();
    }
}

function nextPhoto() {
    if (currentPhotoIndex < 10) {
        currentPhotoIndex++;
        updatePhotoCounter();
    }
}

function updatePhotoCounter() {
    document.getElementById('photoCounter').textContent = currentPhotoIndex + '/10';
}

// ============================================
// PAGE INIT
// ============================================

console.log("SUCCESS: script.js has been detected and loaded!");

document.addEventListener('DOMContentLoaded', () => {
    // 1. Initialize Icons
    if (typeof lucide !== 'undefined') lucide.createIcons();

    // 2. Detect Page
    const urlParams = new URLSearchParams(window.location.search);
    const currentPage = urlParams.get('page') || 'dashboard';

    console.log("Routing logic triggered for:", currentPage);

    // 3. Page Routing Logic
    switch(currentPage) {
        case 'dashboard':
            console.log("Loading Dashboard...");
            if (typeof loadDashboard === 'function') loadDashboard();
            break;
            
        case 'manage_cars':
            console.log("Loading Manage Cars...");
            if (typeof loadCars === 'function') loadCars();
            break;

        case 'bookings':
            console.log("Loading Bookings...");
            if (typeof loadBookings === 'function') loadBookings();
            break;

        case 'renters':
            console.log("Loading Renters...");
            if (typeof loadRenters === 'function') loadRenters();
            break;

        case 'operators':
            console.log("Loading Operators...");
            if (typeof loadOperators === 'function') loadOperators();
            break;
            
        case 'add_car':
            console.log("Add Car page ready.");
            // Usually, add_car is just a static form, no API load needed
            break;

        default:
            console.warn("No specific loader for this page.");
    }
});

