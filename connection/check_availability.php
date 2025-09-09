<?php
header('Content-Type: application/json');
// Allow only same-origin simple requests; adjust if CORS needed

require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/function.php';

$type = isset($_GET['type']) ? strtolower(trim($_GET['type'])) : '';
$value = isset($_GET['value']) ? trim($_GET['value']) : '';

if ($type === '' || $value === '') {
    echo json_encode(['ok' => false, 'message' => 'Missing parameters']);
    exit;
}

$available = false;
$message = '';

if ($type === 'email') {
    $exists = EmailExist($value);
    $available = ($exists == 0);
    $message = $available ? 'Email is available' : 'Email is already in use';
} elseif ($type === 'username') {
    $exists = UserNameExist($value);
    $available = ($exists == 0);
    $message = $available ? 'Username is available' : 'Username is already taken';
} else {
    echo json_encode(['ok' => false, 'message' => 'Invalid type']);
    exit;
}

echo json_encode(['ok' => true, 'available' => $available, 'message' => $message]);
exit;
