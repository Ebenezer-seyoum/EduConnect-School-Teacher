<?php
header('Content-Type: application/json');
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/function.php';

$email = isset($_GET['email']) ? trim($_GET['email']) : '';
if ($email === '') {
    echo json_encode(['ok' => false, 'message' => 'Email parameter missing']);
    exit;
}
// Basic format validation early
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['ok' => true, 'exists' => false, 'message' => 'Invalid email format']);
    exit;
}
$exists = EmailExist($email); // Expect returns 1 if exists (based on earlier usage)
$found = ($exists != 0);
$message = $found ? 'Email found. You can continue.' : 'No account found with this email.';

echo json_encode([
    'ok' => true,
    'exists' => $found,
    'message' => $message
]);
exit;
