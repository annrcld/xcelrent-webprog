// assets/js/manage_renters.js

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
    document.getElementById('licenseModal')?.classList.add('active');
}

function viewRentalHistory(renterId) {
    document.getElementById('rentalHistoryModal')?.classList.add('active');
}

// Optional: Live search
document.getElementById('userSearch')?.addEventListener('input', loadRenters);

document.addEventListener('DOMContentLoaded', () => {
    loadRenters();
});