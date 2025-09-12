<?php
require_once __DIR__ . '/connection/connection.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';
require 'PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $isAjax = isset($_POST['ajax']) && $_POST['ajax'] === '1';
    if ($isAjax) {
        header('Content-Type: application/json');
    }
    $email = trim($_POST['email'] ?? '');
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        if ($isAjax) {
            echo json_encode(['success' => false, 'code' => 'invalid_email', 'message' => 'Please enter a valid registered email address.']);
        } else {
            echo sweetAlertPage('Invalid Email', 'Please enter a valid registered email address.', 'error', 'forgot.php');
        }
        exit;
    }

    $emailEsc = mysqli_real_escape_string($conn, $email);
    $result = mysqli_query($conn, "SELECT uid, full_name FROM users WHERE email='$emailEsc' LIMIT 1");
    if ($result && mysqli_num_rows($result) === 1) {
        $u = mysqli_fetch_assoc($result);
        $username = $u['full_name'] ?? '';
        // Attempt to extend password_resets table with extra columns for verification code (silent failure ok)
        $existingCols = [];
        if ($colRes = @mysqli_query($conn, "SHOW COLUMNS FROM password_resets")) {
            while ($c = mysqli_fetch_assoc($colRes)) {
                $existingCols[] = strtolower($c['Field']);
            }
        }
        if (!in_array('short_code', $existingCols)) @mysqli_query($conn, "ALTER TABLE password_resets ADD COLUMN short_code VARCHAR(20) NULL");
        if (!in_array('code_expires_at', $existingCols)) @mysqli_query($conn, "ALTER TABLE password_resets ADD COLUMN code_expires_at DATETIME NULL");
        if (!in_array('attempts', $existingCols)) @mysqli_query($conn, "ALTER TABLE password_resets ADD COLUMN attempts INT NOT NULL DEFAULT 0");

        $token = bin2hex(random_bytes(32));
        $shortCode = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT); // 6-digit numeric code
        $expires = date("Y-m-d H:i:s", strtotime("+15 minutes"));
        mysqli_query($conn, "DELETE FROM password_resets WHERE email='$emailEsc'");
        mysqli_query($conn, "INSERT INTO password_resets (email, token, expires_at, short_code, code_expires_at, attempts) VALUES ('$emailEsc', '$token', '$expires', '$shortCode', '$expires', 0)");

        // Dynamic base URL
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $base = rtrim($scheme . $host, '/');
        $resetLink = $base . '/Edu-Connect/reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email);

        $mail = new PHPMailer(true);
        try {
            // Optional debug flag: set DEFINE_SMTP_DEBUG true somewhere secure (not committed) to enable
            if (getenv('SMTP_DEBUG') === '1') {
                $mail->SMTPDebug = 2; // verbose debug
                $mail->Debugoutput = function ($str, $level) {
                    error_log('SMTP DEBUG [' . $level . ']: ' . trim($str));
                };
            }
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'jobirjobs.noreply@gmail.com';
            $mail->Password = 'ygccqcugwvlgelmz'; // Consider moving to env var
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // implicit TLS
            $mail->Port = 465;
            $mail->CharSet = 'UTF-8';
            $mail->setFrom('jobirjobs.noreply@gmail.com', 'Jobir jobs Support Team');
            $mail->addReplyTo('jobirjobs.noreply@gmail.com', 'No Reply');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
           $mail->Body = "
<div style='font-family:Segoe UI, Arial, sans-serif; padding:24px; background:#2c2c2c; color:#fff;'>
  <!-- Greeting -->
  <p style='font-size:15px;'>Hi " . htmlspecialchars($username) . ",</p>
  <h2 style='margin:0 0 16px;'>Request to Reset Your Password</h2>

  <p style='font-size:15px; line-height:1.55; margin:0 0 16px;'>
    You requested a password reset for your <strong>Jobir Jobs</strong> account.  
    For security, please choose <strong>only one</strong> method below to continue.
  </p>

  <!-- Option 1: Reset Link -->
  <div style='background:#fff; color:#333; border-radius:12px; padding:20px; margin:0 0 22px;'>
    <h3 style='color:#0d47a1; font-size:16px; margin:0 0 12px;'>Option 1: Secure Reset Link (Recommended)</h3>
    <p style='font-size:14px; margin:0 0 16px;'>
      Click the button below to reset your password. This link expires in 15 minutes.
    </p>
    <div style='text-align:center; margin:18px 0 0;'>
      <a href='$resetLink' style='display:inline-block; padding:14px 28px; background:#0d47a1; color:#fff; text-decoration:none; border-radius:6px; font-weight:600; font-size:15px; transition:.3s;'>
        Reset Password
      </a>
    </div>
  </div>

  <!-- Option 2: Verification Code -->
  <div style='background:#fff; color:#333; border-radius:12px; padding:20px; text-align:center; margin:0 0 22px; border:1px dashed #0d47a1;'>
    <h3 style='color:#0d47a1; font-size:16px; margin:0 0 12px;'>Option 2: Verification Code</h3>
    <p style='font-size:14px; margin:0 0 12px;'>
       If the reset link is unavailable, you can use the verification code below to reset your password.
    </p>
    <div style='font-size:38px; letter-spacing:8px; font-weight:700; color:#0d47a1; margin:12px 0;'>$shortCode</div>
    <p style='font-size:13px; margin:0;'>
      Enter this code on the <a href='$base/Edu-Connect/verify_code.php?email=" . urlencode($email) . "' style='color:#0d47a1; font-weight:600; text-decoration:underline;'>verification page</a>.
    </p>
  </div>

  <!-- Expiry & Disclaimer -->
  <p style='font-size:13px; margin:0 0 6px;'>
    Both methods expire at <strong>$expires</strong>. After that, request a new reset.
  </p>
  <p style='font-size:12px; margin:0 0 24px;'>
    If you did not request this, you can safely ignore this message. Your password remains unchanged.
  </p>

  <hr style='border:none; border-top:1px solid #fffffffa; margin:28px 0;'>

<!-- Social Media (icons tinted to match site color) -->
<div style='text-align:center; margin-top:20px;'>
    <a href='https://twitter.com/YourProfile' style='margin:0 6px; display:inline-block;'>
        <img src='https://cdn.simpleicons.org/twitter/0d47a1' width='24' height='24' alt='Twitter'>
    </a>
    <a href='https://linkedin.com/in/YourProfile' style='margin:0 6px; display:inline-block;'>
        <img src='https://cdn.simpleicons.org/linkedin/0d47a1' width='24' height='24' alt='LinkedIn'>
    </a>
    <a href='https://wa.me/YourNumber' style='margin:0 6px; display:inline-block;'>
        <img src='https://cdn.simpleicons.org/whatsapp/0d47a1' width='24' height='24' alt='WhatsApp'>
    </a>
    <a href='mailto:support@jobirsjobs.com' style='margin:0 6px; display:inline-block;'>
        <img src='https://cdn.simpleicons.org/maildotru/0d47a1' width='24' height='24' alt='Email'>
    </a>
</div>

<p style='font-size:13px;  color:#ffffff99; margin-top:16px; text-align:center;'><strong>Jobirs Jobs Support Team</strong></p>
</div>
";

$mail->AltBody = "Hi $username,\n\nPassword reset (15m)\n\nOption 1 (link): $resetLink\nOption 2 (code): $shortCode\nUse either link OR code. If not you, ignore this email.\n\nJobirs Jobs Support Team";

            if ($mail->send()) {
                if ($isAjax) {
                    echo json_encode(['success' => true, 'message' => 'Reset link & 6-digit code sent.']);
        } else {
          echo sweetAlertPage('Check your email', 'You will receive an email with instructions to reset your password.', 'success', 'forget.php', 0);
        }
            } else {
                if ($isAjax) {
                    echo json_encode(['success' => false, 'code' => 'send_failed', 'message' => 'We could not send the reset email. Try again later.']);
                } else {
                    echo sweetAlertPage('Send Failed', 'We could not send the reset email. Please try again later.', 'error', 'forget.php');
                }
            }
        } catch (Exception $e) {
            error_log('Password reset mail error: ' . $mail->ErrorInfo);
            if ($isAjax) {
                echo json_encode(['success' => false, 'code' => 'exception', 'message' => 'Issue sending the email. Please try again soon.']);
            } else {
                echo sweetAlertPage('Email Error', 'We encountered an issue sending the email. Please try again soon.', 'error', 'forget.php');
            }
        }
    } else {
        if ($isAjax) {
            echo json_encode(['success' => false, 'code' => 'not_found', 'message' => 'No account is registered with that email address.']);
        } else {
            echo sweetAlertPage('Not Found', 'No account is registered with that email address.', 'warning', 'forget.php', 4000);
        }
    }
}

function sweetAlertPage($title, $text, $icon, $redirect, $timer = 0)
{
    $rdJs = $redirect ? "window.location='{$redirect}';" : '';
    $timerJs = $timer > 0 ? "timer: $timer, timerProgressBar: true," : '';
    return "<!DOCTYPE html><html><head><meta charset='utf-8'><title>$title</title>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <link rel='preconnect' href='https://cdn.jsdelivr.net' />
    <style>body{font-family:Segoe UI,Arial,sans-serif; background:#f5f7fa;}</style>
    </head><body>
    <script>Swal.fire({title: '" . addslashes($title) . "', text: '" . addslashes($text) . "', icon: '$icon', $timerJs confirmButtonText: 'OK'}).then(()=>{$rdJs});</script>
    </body></html>";
}
