// assets/js/manage_operators.js

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
        // TODO: Call API
        console.log('Approved operator ' + opId);
        loadOperators();
    }
}

function rejectOperator(opId) {
    if (confirm('Reject this operator?')) {
        // TODO: Call API
        console.log('Rejected operator ' + opId);
        loadOperators();
    }
}

function viewOperatorDetails(opId) {
    alert('View details for operator ' + opId);
}

document.addEventListener('DOMContentLoaded', () => {
    loadOperators();
});