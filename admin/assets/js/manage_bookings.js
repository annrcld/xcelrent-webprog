// assets/js/manage_bookings.js

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
    document.getElementById('paymentModal')?.classList.add('active');
}

document.addEventListener('DOMContentLoaded', () => {
    loadBookings();
});