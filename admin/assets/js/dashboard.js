// assets/js/dashboard.js

function loadDashboard() {
    fetch('./api/get_dashboard.php')
        .then(r => r.json())
        .then(payload => {
            if (!payload.success) throw new Error(payload.error || 'API error');
            const data = payload.data;
            const kpis = data.kpis || {};
            const recent = data.recent_bookings || [];

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

document.addEventListener('DOMContentLoaded', () => {
    loadDashboard();
});