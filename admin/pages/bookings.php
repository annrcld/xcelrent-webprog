
<?php
// admin/pages/bookings.php
?>
<!-- Manage Bookings -->
<section id="bookings" class="tab-content active">
    <h1 class="page-title">Manage Bookings</h1>
    <p style="color: #666; margin-bottom: 20px;">Review and approve booking requests with payment verification.</p>
    
    <div class="panel">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Car</th>
                    <th>Renter</th>
                    <th>Rental Dates</th>
                    <th>Rental Period</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="bookingsTableBody">
                <tr><td colspan="7" style="text-align: center; padding: 20px;">Loading bookings...</td></tr>
            </tbody>
        </table>
    </div>
</section>

<!-- Payment Proof Modal -->
<div id="paymentProofModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header">
            <h2>Payment Proof</h2>
            <button class="close-btn" onclick="closePaymentModal()">&times;</button>
        </div>
        <div class="modal-body" style="text-align: center; padding: 20px;">
            <img id="proofImage" src="" alt="Payment Proof" style="max-width: 100%; max-height: 500px; border-radius: 8px;">
            <div id="proofDetails" style="margin-top: 20px; text-align: left;"></div>
        </div>
    </div>
</div>

<!-- Renter Profile Modal -->
<div id="renterProfileModal" class="modal-overlay">
    <div class="modal-content" style="max-width: 700px;">
        <div class="modal-header">
            <h2>Renter Profile</h2>
            <button class="close-btn" onclick="closeRenterModal()">&times;</button>
        </div>
        <div class="modal-body" id="renterProfileContent" style="padding: 20px;">
            <div style="text-align: center; padding: 40px;">Loading renter details...</div>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    z-index: 1000;
    align-items: center;
    justify-content: center;
}

.modal-overlay.active {
    display: flex;
}

.modal-content {
    background: white;
    border-radius: 12px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #999;
}

.close-btn:hover {
    color: #333;
}

.renter-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
    margin-bottom: 20px;
}

.info-item {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
}

.info-label {
    font-size: 12px;
    color: #666;
    text-transform: uppercase;
    margin-bottom: 5px;
}

.info-value {
    font-size: 14px;
    font-weight: 600;
    color: #333;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.btn {
    padding: 6px 12px;
    font-size: 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.2s;
}

.btn-edit {
    background: #0066cc;
    color: white;
}

.btn-edit:hover {
    background: #0052a3;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0, 102, 204, 0.3);
}

.btn-delete {
    background: #dc2626;
    color: white;
}

.btn-delete:hover {
    background: #b91c1c;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 38, 38, 0.3);
}

.btn-view {
    background: #666;
    color: white;
}

.btn-view:hover {
    background: #555;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(102, 102, 102, 0.3);
}

.btn-approve {
    background: #28a745;
    color: white;
}

.btn-approve:hover {
    background: #218838;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
}

.btn-reject {
    background: #dc3545;
    color: white;
}

.btn-reject:hover {
    background: #c82333;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
}

.status-badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-confirmed {
    background: #dbeafe;
    color: #1e40af;
}

.status-ongoing {
    background: #e0e7ff;
    color: #4338ca;
}

.status-completed {
    background: #d1fae5;
    color: #065f46;
}

.status-cancelled {
    background: #fee2e2;
    color: #991b1b;
}

.renter-name {
    color: #3b82f6;
    cursor: pointer;
    text-decoration: none;
}

.renter-name:hover {
    text-decoration: underline;
}
</style>

<script src="assets/js/manage_bookings.js"></script>