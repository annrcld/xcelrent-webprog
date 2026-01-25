// admin/assets/js/manage_bookings.js

function loadBookings() {
    fetch('api/get_bookings.php')
        .then(r => r.json())
        .then(payload => {
            if (!payload.success) throw new Error(payload.error);
            const bookings = payload.data || [];

            const tbody = document.getElementById('bookingsTableBody');
            if (!tbody) return;

            if (bookings.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #666;">No bookings found</td></tr>';
                return;
            }

            tbody.innerHTML = bookings.map(b => {
                // Calculate rental period
                const startDate = new Date(b.start_date);
                const endDate = new Date(b.end_date);
                const days = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
                const rentalPeriod = `${days} day${days !== 1 ? 's' : ''}`;

                // Format dates
                const startFormatted = startDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const endFormatted = endDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                // Status badge
                const statusClass = `status-${b.status}`;
                
                // Show approve/reject only for pending bookings
                const actionButtons = b.status === 'pending' ? `
                    <button class="btn btn-green" onclick="approveBooking(${b.id})" title="Approve Booking">
                        <i data-lucide="check"></i> Approve
                    </button>
                    <button class="btn btn-red" onclick="rejectBooking(${b.id})" title="Reject Booking">
                        <i data-lucide="x"></i> Reject
                    </button>
                ` : '';

                return `
                    <tr>
                        <td>
                            <strong>${b.brand} ${b.model}</strong><br>
                            <small style="color: #666;">${b.plate_number}</small>
                        </td>
                        <td>
                            <a href="javascript:void(0)" class="renter-name" onclick="viewRenterProfile(${b.user_id})">
                                ${b.first_name} ${b.last_name}
                            </a>
                        </td>
                        <td>
                            <small>${startFormatted} → ${endFormatted}</small>
                        </td>
                        <td>${rentalPeriod}</td>
                        <td><strong>₱${parseFloat(b.total_amount).toFixed(2)}</strong></td>
                        <td><span class="status-badge ${statusClass}">${b.status}</span></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-black" onclick="viewPaymentProof(${b.id}, '${b.proof_of_payment || ''}')" title="View Payment Proof">
                                    <i data-lucide="receipt"></i> Payment
                                </button>
                                ${actionButtons}
                            </div>
                        </td>
                    </tr>
                `;
            }).join('');

            // Reinitialize Lucide icons
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        })
        .catch(err => {
            console.error('Bookings load error:', err);
            const tbody = document.getElementById('bookingsTableBody');
            if (tbody) tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 20px; color: #ef4444;">Error loading bookings</td></tr>`;
        });
}

async function approveBooking(bookingId) {
    if (!confirm('Approve this booking? The car will be marked as unavailable for these dates.')) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append('booking_id', bookingId);

        const response = await fetch('api/approve_booking.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('Booking approved successfully!');
            loadBookings();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error approving booking:', error);
        alert('Error approving booking: ' + error.message);
    }
}

async function rejectBooking(bookingId) {
    const reason = prompt('Enter reason for rejection (optional):');
    
    if (reason === null) return; // User cancelled

    try {
        const formData = new FormData();
        formData.append('booking_id', bookingId);
        formData.append('reason', reason);

        const response = await fetch('api/reject_booking.php', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();

        if (data.success) {
            alert('Booking rejected successfully!');
            loadBookings();
        } else {
            alert('Error: ' + data.message);
        }
    } catch (error) {
        console.error('Error rejecting booking:', error);
        alert('Error rejecting booking: ' + error.message);
    }
}

function viewPaymentProof(bookingId, proofPath) {
    if (!proofPath || proofPath === 'null') {
        alert('No payment proof uploaded for this booking');
        return;
    }

    const modal = document.getElementById('paymentProofModal');
    const img = document.getElementById('proofImage');
    
    // Set image source with proper path
    img.src = `/project_xcelrent/public/${proofPath}`;
    
    modal.classList.add('active');
}

function closePaymentModal() {
    document.getElementById('paymentProofModal').classList.remove('active');
}

async function viewRenterProfile(userId) {
    const modal = document.getElementById('renterProfileModal');
    const content = document.getElementById('renterProfileContent');
    
    content.innerHTML = '<div style="text-align: center; padding: 40px;">Loading renter details...</div>';
    modal.classList.add('active');

    try {
        const response = await fetch(`api/get_renter_details.php?user_id=${userId}`);
        const data = await response.json();

        if (!data.success) {
            content.innerHTML = `<div style="color: #ef4444; text-align: center; padding: 40px;">${data.message}</div>`;
            return;
        }

        const user = data.user;
        const bookings = data.bookings || [];

        content.innerHTML = `
            <div class="renter-info-grid">
                <div class="info-item">
                    <div class="info-label">Full Name</div>
                    <div class="info-value">${user.first_name} ${user.last_name}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value">${user.email}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Phone</div>
                    <div class="info-value">${user.phone || 'Not provided'}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Member Since</div>
                    <div class="info-value">${new Date(user.created_at).toLocaleDateString()}</div>
                </div>
            </div>

            <h3 style="margin: 20px 0 10px;">Booking History (${bookings.length})</h3>
            ${bookings.length > 0 ? `
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background: #f8f9fa;">
                            <th style="padding: 8px; text-align: left; font-size: 12px;">Car</th>
                            <th style="padding: 8px; text-align: left; font-size: 12px;">Dates</th>
                            <th style="padding: 8px; text-align: left; font-size: 12px;">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${bookings.map(b => `
                            <tr style="border-bottom: 1px solid #eee;">
                                <td style="padding: 8px; font-size: 13px;">${b.brand} ${b.model}</td>
                                <td style="padding: 8px; font-size: 13px;">${new Date(b.start_date).toLocaleDateString()}</td>
                                <td style="padding: 8px;"><span class="status-badge status-${b.status}">${b.status}</span></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            ` : '<p style="color: #666; text-align: center; padding: 20px;">No booking history</p>'}
        `;

    } catch (error) {
        console.error('Error loading renter details:', error);
        content.innerHTML = `<div style="color: #ef4444; text-align: center; padding: 40px;">Error loading renter details</div>`;
    }
}

function closeRenterModal() {
    document.getElementById('renterProfileModal').classList.remove('active');
}

// Close modals on outside click
window.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('active');
    }
});

document.addEventListener('DOMContentLoaded', () => {
    loadBookings();
});