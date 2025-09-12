<?php
require_once __DIR__ . '/connection/connection.php';
require_once __DIR__ . '/connection/function.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? '');
    $token = trim($_POST['token'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    function swal($title, $text, $icon, $redirect = '', $timer = 0)
    {
        $rd = $redirect ? "window.location='$redirect';" : '';
        $t = $timer > 0 ? "timer:$timer,timerProgressBar:true," : '';
        return "<!DOCTYPE html><html><head><meta charset='utf-8'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            <title>$title</title></head><body>
            <script>
            Swal.fire({title:'" . addslashes($title) . "',text:'" . addslashes($text) . "',icon:'$icon',$t confirmButtonText:'OK'}).then(()=>{ $rd });
            </script></body></html>";
    }

    if ($password === '' || $confirm === '' || $email === '' || $token === '') {
        echo swal('Missing Data', 'Please complete all required fields.', 'error', 'forget.php', 3500);
        exit;
    }

    $backUrl = 'reset_password.php?token=' . urlencode($token) . '&email=' . urlencode($email);

    if ($password !== $confirm) {
        echo swal('Mismatch', 'Passwords do not match.', 'error', $backUrl, 3500);
        exit;
    }

    // Strong password check
    if (strlen($password) < 8 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[@#$%^&+=!]/', $password)) {
        echo swal('Weak Password', 'Password must be 8+ chars with lowercase, uppercase, and one special (@#$%^&+=!).', 'warning', $backUrl, 4000);
        exit;
    }

    $emailEsc = mysqli_real_escape_string($conn, $email);
    $tokenEsc = mysqli_real_escape_string($conn, $token);

    // Check token validity
    $check = mysqli_query($conn, "SELECT expires_at FROM password_resets WHERE email='$emailEsc' AND token='$tokenEsc' LIMIT 1");
    if (!$check || mysqli_num_rows($check) !== 1) {
        echo swal('Invalid Link', 'The reset link is invalid or has expired. Please request a new one.', 'error', 'forget.php', 4000);
        exit;
    }

    $rowTok = mysqli_fetch_assoc($check);
    if (strtotime($rowTok['expires_at']) <= time()) {
        echo swal('Expired', 'Reset link expired. Please request a new one.', 'error', 'forget.php', 4000);
        exit;
    }

    // Check previous password
    $userRes = mysqli_query($conn, "SELECT password FROM users WHERE email='$emailEsc' LIMIT 1");
    if ($userRes && mysqli_num_rows($userRes) === 1) {
        $rowU = mysqli_fetch_assoc($userRes);
        $currentStored = $rowU['password']; // encrypted
        $decryptedOld = decryptPassword($currentStored);

        if ($decryptedOld !== false && hash_equals($decryptedOld, $password)) {
            echo swal('Not Allowed', 'New password must be different from the previous one.', 'warning', $backUrl);
            exit;
        }
    }

    // Encrypt and update password
    $finalStore = encryptPassword($password);
    if (!$finalStore) {
        echo swal('Error', 'Password encryption failed. Try again.', 'error', $backUrl, 4000);
        exit;
    }

    $upd = mysqli_query($conn, "UPDATE users SET password='" . mysqli_real_escape_string($conn, $finalStore) . "' WHERE email='$emailEsc' LIMIT 1");

    if ($upd && mysqli_affected_rows($conn) === 1) {
        // Remove used token
        mysqli_query($conn, "DELETE FROM password_resets WHERE email='$emailEsc'");
        echo swal('Password Updated', 'Your password has been changed.', 'success', 'password_reset_success.php');
    } else {
        echo swal('Update Failed', 'Could not update password. Please try again.', 'error', $backUrl, 4000);
    }
}
