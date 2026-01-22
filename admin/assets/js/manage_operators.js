// assets/js/manage_operators.js

function loadOperators() {
    fetch('api/get_operators.php')
        .then(r => r.json())
        .then(payload => {
            if (!payload.success) throw new Error(payload.error);
            const operators = payload.data || [];

            const container = document.getElementById('operatorApplicationsContainer');
            if (container) {
                if (operators.length === 0) {
                    container.innerHTML = '<div class="no-data">No pending car applications</div>';
                    return;
                }

                // Create table structure for car applications (one row per car application)
                container.innerHTML = `
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Car</th>
                                <th>Operator</th>
                                <th>Contact</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${operators.map(app => {
                                // Combine brand and model for car name
                                const carName = `${app.brand || 'N/A'} ${app.model || 'N/A'}`;

                                return `
                                    <tr>
                                        <td>
                                            <div class="car-name">
                                                <strong>${carName}</strong><br>
                                                <small>${app.category || 'N/A'} • ${app.plate_number || 'N/A'}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="operator-email">${app.email}</div>
                                        </td>
                                        <td>
                                            <div class="contact-phone">${app.phone}</div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <button class="btn btn-blue" onclick="viewOperatorDetails(${app.operator_id}, ${app.car_id})" title="View Details">View</button>
                                                <button class="btn btn-green" onclick="approveOperator(${app.operator_id}, ${app.car_id})" title="Approve Application">Approve</button>
                                                <button class="btn btn-red" onclick="rejectOperator(${app.operator_id}, ${app.car_id})" title="Reject Application">Reject</button>
                                            </div>
                                        </td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                `;
            }
        })
        .catch(err => {
            console.error('Operators load error:', err);
            const container = document.getElementById('operatorApplicationsContainer');
            if (container) container.innerHTML = '<div class="error">Error loading operator applications</div>';
        });
}

async function approveOperator(operatorId, carId) {
    if (confirm('Approve this car application?')) {
        try {
            const response = await fetch('api/approve_operator.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `operator_id=${operatorId}&car_id=${carId}`
            });

            const data = await response.json();

            if (data.success) {
                alert('Car application approved successfully! Car is now live and visible to customers.');
                loadOperators(); // Refresh the operator applications list
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error approving car application:', error);
            alert('Error approving car application: ' + error.message);
        }
    }
}

async function rejectOperator(operatorId, carId) {
    const reason = prompt('Enter reason for rejection (optional):');

    if (reason !== null) { // User didn't cancel
        try {
            const formData = new FormData();
            formData.append('operator_id', operatorId);
            formData.append('car_id', carId);
            formData.append('reason', reason);

            const response = await fetch('api/reject_operator.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                alert('Car application rejected successfully!' + (data.warning ? '\nWarning: ' + data.warning : ''));
                loadOperators();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error rejecting car application:', error);
            alert('Error rejecting car application: ' + error.message);
        }
    }
}

function viewOperatorDetails(operatorId, carId) {
    // Show loading state
    const container = document.getElementById('operatorApplicationsContainer');
    container.innerHTML = '<div class="loading">Loading car application details...</div>';

    // Fetch car application details
    fetch(`api/get_operator_details.php?operator_id=${operatorId}&car_id=${carId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                container.innerHTML = `<div class="error">Error: ${data.error}</div>`;
                return;
            }

            const op = data.operator;
            const car = data.car; // Single car details
            const docs = data.documents;

            // Build HTML for car application details
            let html = `
                <div class="operator-details-back">
                    <button class="btn btn-black" onclick="loadOperators()">← Back to Applications</button>
                </div>

                <div class="operator-header">
                    <h2>${car.brand} ${car.model}</h2>
                    <div class="operator-meta">
                        <p><strong>Operator:</strong> ${op.company_name}</p>
                        <p><strong>Contact:</strong> ${op.contact_name}</p>
                        <p><strong>Email:</strong> ${op.email}</p>
                        <p><strong>Phone:</strong> ${op.phone}</p>
                        <p><strong>Plate Number:</strong> ${car.plate_number}</p>
                        <p><strong>Category:</strong> ${car.category}</p>
                        <p><strong>Seating:</strong> ${car.seating}</p>
                        <p><strong>Fuel Type:</strong> ${car.fuel_type}</p>
                        <p><strong>Transmission:</strong> ${car.transmission || 'N/A'}</p>
                        <p><strong>Driver Type:</strong> ${car.driver_type === 'self_drive' ? 'Self-Drive' : 'With Driver'}</p>
                        <p><strong>Location:</strong> ${car.location}</p>
                        <p><strong>Application Date:</strong> ${new Date(car.created_at).toLocaleDateString()}</p>
                    </div>
                </div>

                <div class="operator-section">
                    <h3>Car Photos (${car.total_photos})</h3>
                    <div class="photo-grid">
            `;

            if (car.photos && car.photos.length > 0) {
                car.photos.forEach(photo => {
                    html += `
                        <div class="photo-item ${photo.is_primary ? 'primary-photo' : ''}">
                            <img src="../${photo.file_path}" alt="Car photo" style="max-width: 150px; max-height: 150px;">
                            ${photo.is_primary ? '<span class="primary-label">Primary</span>' : ''}
                        </div>
                    `;
                });
            } else {
                html += '<p>No photos uploaded</p>';
            }

            html += `
                    </div>
                </div>

                <div class="operator-section">
                    <h3>Documents (${docs.length})</h3>
                    <div class="document-list">
            `;

            docs.forEach(doc => {
                html += `
                    <div class="document-item">
                        <span class="doc-type">${doc.doc_type}</span>
                        <a href="../${doc.file_path}" target="_blank" class="btn btn-sm">View Document</a>
                        <span class="doc-status">Verified: ${doc.verified ? 'Yes' : 'No'}</span>
                    </div>
                `;
            });

            if (docs.length === 0) {
                html += '<p>No documents uploaded</p>';
            }

            html += `
                    </div>
                </div>

                <div class="operator-actions-section">
                    <h3>Actions</h3>
                    <div class="action-buttons-full">
                        <button class="btn btn-green" onclick="approveOperator(${operatorId}, ${carId})" title="Approve Application">Approve Application</button>
                        <button class="btn btn-red" onclick="rejectOperator(${operatorId}, ${carId})" title="Reject Application">Reject Application</button>
                        <button class="btn btn-black" onclick="loadOperators()">Back to Applications</button>
                    </div>
                </div>
            `;

            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading car application details:', error);
            container.innerHTML = '<div class="error">Error loading car application details</div>';
        });
}

function getStatusClass(status) {
    switch(status) {
        case 'live':
            return 'status-live';
        case 'hidden':
            return 'status-hidden';
        case 'maintenance':
            return 'status-maintenance';
        default:
            return 'status-pending';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    loadOperators();
});