<?php
// Returns JSON with dashboard stats & recent users
header('Content-Type: application/json');

// Require login
session_start();
if (!isset($_SESSION['uid'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Include DB connection and helpers
require_once __DIR__ . '/../connection/connection.php';

// Helper to run a scalar COUNT(*) query
function count_query($conn, $sql)
{
    $res = mysqli_query($conn, $sql);
    if (!$res) return 0;
    $row = mysqli_fetch_row($res);
    return (int)($row[0] ?? 0);
}

// Basic counts
$totalUsers    = count_query($conn, "SELECT COUNT(*) FROM users");
$activeUsers   = count_query($conn, "SELECT COUNT(*) FROM users WHERE user_status = 1");
$inactiveUsers = max(0, $totalUsers - $activeUsers);

// Teachers count (assuming user_type='teacher')
$teachers      = count_query($conn, "SELECT COUNT(*) FROM users WHERE user_type = 'teacher'");

// Users by type
$byType = [];
$byTypeRes = mysqli_query($conn, "SELECT user_type, COUNT(*) AS cnt FROM users GROUP BY user_type ORDER BY cnt DESC");
if ($byTypeRes) {
    while ($r = mysqli_fetch_assoc($byTypeRes)) {
        $byType[] = [
            'user_type' => $r['user_type'] ?? 'unknown',
            'count' => (int)$r['cnt']
        ];
    }
}

// Recent users (fallback to uid desc if no created_at column)
$recentUsers = [];
$recentRes = mysqli_query($conn, "SELECT uid, full_name, user_type, user_status FROM users ORDER BY uid DESC LIMIT 8");
if ($recentRes) {
    while ($r = mysqli_fetch_assoc($recentRes)) {
        $recentUsers[] = [
            'uid' => (int)($r['uid'] ?? 0),
            'full_name' => $r['full_name'] ?? 'User',
            'user_type' => $r['user_type'] ?? 'unknown',
            'user_status' => isset($r['user_status']) ? (int)$r['user_status'] : 0,
        ];
    }
}

// Build response
$resp = [
    'totalUsers' => $totalUsers,
    'activeUsers' => $activeUsers,
    'inactiveUsers' => $inactiveUsers,
    'teachers' => $teachers,
    'byType' => $byType,
    'recentUsers' => $recentUsers,
];

echo json_encode($resp);
exit;
