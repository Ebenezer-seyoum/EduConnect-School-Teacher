<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../connection/connection.php';
require_once __DIR__ . '/../connection/function.php';

if (!isset($_SESSION['uid'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid id']);
    exit;
}

$fb = getFeedbackById($id);
if (!$fb) {
    http_response_code(404);
    echo json_encode(['error' => 'Not found']);
    exit;
}

// escape output minimally for JSON
$fb['name'] = htmlspecialchars($fb['name'], ENT_QUOTES, 'UTF-8');
$fb['email'] = htmlspecialchars($fb['email'], ENT_QUOTES, 'UTF-8');
$fb['subject'] = htmlspecialchars($fb['subject'], ENT_QUOTES, 'UTF-8');
$fb['message'] = htmlspecialchars($fb['message'], ENT_QUOTES, 'UTF-8');

echo json_encode($fb);
