<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/config.php';

try {
    $response = [];

    // KPIs
    $kpis = [];
    
    // Total cars
    $result = $conn->query("SELECT COUNT(*) AS count FROM cars");
    $kpis['total_cars'] = intval($result->fetch_assoc()['count']);
    
    // Live cars
    $result = $conn->query("SELECT COUNT(*) AS count FROM cars WHERE status='live'");
    $kpis['live_cars'] = intval($result->fetch_assoc()['count']);
    
    // Active bookings
    $result = $conn->query("SELECT COUNT(*) AS count FROM bookings WHERE status IN ('confirmed','ongoing')");
    $kpis['active_bookings'] = intval($result->fetch_assoc()['count']);
    
    // Revenue (last 30 days)
    $result = $conn->query("SELECT IFNULL(SUM(amount),0) AS revenue FROM payments WHERE status='completed' AND payment_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $kpis['revenue_30d'] = floatval($result->fetch_assoc()['revenue']);
    
    // New renters (last 30 days)
    $result = $conn->query("SELECT COUNT(*) AS count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $kpis['new_renters_30d'] = intval($result->fetch_assoc()['count']);
    
    $response['kpis'] = $kpis;

    // Recent bookings (last 10)
    $bookingsSql = "
        SELECT b.id, b.start_date, b.end_date, b.total_amount, b.status,
               u.first_name, u.last_name, c.brand, c.model, c.plate_number
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN cars c ON b.car_id = c.id
        ORDER BY b.created_at DESC
        LIMIT 10
    ";
    $result = $conn->query($bookingsSql);
    $recent = [];
    while ($row = $result->fetch_assoc()) {
        $recent[] = $row;
    }
    $response['recent_bookings'] = $recent;

    echo json_encode(['success' => true, 'data' => $response]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
$conn->close();
?>