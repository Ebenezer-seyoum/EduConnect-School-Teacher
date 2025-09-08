<?php
session_start();
require_once __DIR__ . '/connection/connection.php';
require_once __DIR__ . '/connection/function.php';

// Simple flash helper
function set_flash($type, $msg)
{
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    set_flash('danger', 'Invalid request.');
    header('Location: contact.php');
    exit;
}

$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$subject = $_POST['subject'] ?? '';
$message = $_POST['message'] ?? '';

list($ok, $msg) = addFeedback($name, $email, $subject, $message);
if ($ok) {
    set_flash('success', 'Thank you! Your message has been sent.');
} else {
    set_flash('danger', $msg ?: 'Unable to send your message.');
}

header('Location: contact.php');
exit;
