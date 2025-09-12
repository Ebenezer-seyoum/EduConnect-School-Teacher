<?php
require_once __DIR__ . '/connection/connection.php';
require_once __DIR__ . '/connection/function.php';

header('Content-Type: application/json');

require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
require_once __DIR__ . '/PHPMailer/Exception.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$email = trim($_POST['email'] ?? '');
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email.']);
    exit;
}

$emailEsc = mysqli_real_escape_string($conn, $email);
$res = mysqli_query($conn, "SELECT attempts, code_expires_at FROM password_resets WHERE email='$emailEsc' LIMIT 1");
if (!$res || mysqli_num_rows($res) !== 1) {
    echo json_encode(['success' => false, 'message' => 'No active reset request found.']);
    exit;
}
$row = mysqli_fetch_assoc($res);

// Basic server-side cooldown: disallow resending more than once per 60 seconds
$now = time();
$lastExpiry = isset($row['code_expires_at']) ? strtotime($row['code_expires_at']) : 0;
$lastIssued = $lastExpiry - (15 * 60); // original issue time if 15-min window
if ($lastIssued && ($now - $lastIssued) < 60) {
    echo json_encode(['success' => false, 'message' => 'Please wait a minute before requesting another code.']);
    exit;
}

$shortCode = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$newExpiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

$upd = mysqli_query($conn, "UPDATE password_resets SET short_code='$shortCode', code_expires_at='$newExpiry', attempts=0 WHERE email='$emailEsc'");
if (!$upd) {
    echo json_encode(['success' => false, 'message' => 'Could not update code.']);
    exit;
}

// Send email with the new code only (keep it simple for resend)

$mail = new \PHPMailer\PHPMailer\PHPMailer(true);
try {
    if (getenv('SMTP_DEBUG') === '1') {
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = function ($str, $level) { error_log('SMTP DEBUG ['.$level.']: '.trim($str)); };
    }
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'jobirjobs.noreply@gmail.com';
    $mail->Password = 'ygccqcugwvlgelmz';
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = 465;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom('jobirjobs.noreply@gmail.com', 'Jobirs jobs Support Team');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Your Verification Code';
    $mail->Body = "<div style='font-family:Segoe UI, Arial, sans-serif; padding:24px; background:#f4f6f9;'>
        <h2 style='color:#0d47a1; margin:0 0 16px;'>New Verification Code</h2>
        <p style='font-size:14px; color:#444; margin:0 0 12px;'>Use the code below to continue resetting your password. It expires in 15 minutes.</p>
        <div style='font-size:38px; letter-spacing:8px; font-weight:700; color:#0d47a1; margin:12px 0;'>$shortCode</div>
        <p style='font-size:12px; color:#777; margin:0;'>If you didn't request this, you can ignore this email.</p>
    </div>";
    $mail->AltBody = "Your verification code: $shortCode (valid 15 minutes).";

    if ($mail->send()) {
        echo json_encode(['success' => true, 'message' => 'Code resent.', 'expires_at' => $newExpiry]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Could not send email. Try again.']);
    }
} catch (Exception $e) {
    error_log('Resend code mail error: ' . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'Issue sending the code. Try again later.']);
}
