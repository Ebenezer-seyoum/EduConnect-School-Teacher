<?php
require_once __DIR__ . '/connection/connection.php';
require_once __DIR__ . '/connection/function.php';

$email = trim($_GET['email'] ?? '');
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $code = trim($_POST['code'] ?? '');
    
    if ($email === '' || $code === '') {
        $message = 'Please enter the code.';
    } elseif (strlen($code) < 6) {
        $message = 'Code must be 6 digits.';
    } else {
        $emailEsc = mysqli_real_escape_string($conn, $email);
        $codeEsc = mysqli_real_escape_string($conn, $code);
        $res = mysqli_query($conn, "SELECT token, short_code, code_expires_at, attempts, expires_at FROM password_resets WHERE email='$emailEsc' LIMIT 1");

        if ($res && mysqli_num_rows($res) === 1) {
            $row = mysqli_fetch_assoc($res);
            if ((int)$row['attempts'] >= 5) {
                $message = 'Too many attempts. Request a new reset link.';
            } elseif (strtotime($row['code_expires_at']) < time()) {
                $message = 'Code expired. Request a new reset link.';
            } elseif (!hash_equals($row['short_code'], $codeEsc)) {
                mysqli_query($conn, "UPDATE password_resets SET attempts=attempts+1 WHERE email='$emailEsc'");
                $message = 'Invalid code. Please try again.';
            } else {
                header('Location: reset_password.php?token=' . urlencode($row['token']) . '&email=' . urlencode($email));
                exit;
            }
        } else {
            $message = 'No active reset request for this email.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title>Jobir Jobs</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="stylesheet" href="login/assets/css/style.css" />
    <link rel="icon" href="login/assets/img/icon.ico" type="image/x-icon" />
    <style>
        body {
            font-family: Segoe UI, Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 46px 18px 60px;
        }

        .card {
            background: #fff;
            max-width: 480px;
            margin: 0 auto;
            padding: 42px 38px 46px;
            border-radius: 16px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .08);
        }

        h1 {
            margin: 0 0 10px;
            font-size: 1.7rem;
            color: #0d47a1;
            font-weight: 700;
        }

        label {
            font-weight: 600;
            margin-bottom: 6px;
            display: block;
            font-size: .9rem;
        }

        input[type=text] {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid #b6c2d1;
            border-radius: 10px;
            font-size: 1.1rem;
            letter-spacing: 6px;
            text-align: center;
            font-weight: 600;
            outline: none;
            transition: .25s;
            background: #f9fbfd;
        }

        input[type=text]:focus {
            border-color: #0d47a1;
            box-shadow: 0 0 0 3px rgba(13, 71, 161, .15);
            background: #fff;
        }

        button {
            width: 100%;
            margin-top: 20px;
            background: #0d47a1;
            color: #fff;
            border: none;
            padding: 14px 18px;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: .25s;
        }

        button:hover {
            background: #09326d;
        }

        .message {
            margin-top: 16px;
            font-size: 1rem;
            font-weight: 700;
            color: #c62828;
            background: #fdecea;
            padding: 10px 14px;
            border-radius: 8px;
        }

        .helper {
            font-size: .75rem;
            color: #607d8b;
            margin-top: 10px;
        }

        .resend a {
            color: #0d47a1;
            text-decoration: underline;
            font-weight: 600;
        }

        .validation-error {
            color: #c62828;
            font-size: 0.85rem;
            margin-top: 6px;
        }
    </style>
</head>

<body>
    <div class="card">
        <?php if ($message) {
            echo '<div class="message">' . htmlspecialchars($message) . '</div>';
        } ?>
        <h1>Enter Verification Code</h1>
        <p style="margin:0 0 18px;font-size:.95rem;color:#444;line-height:1.5;">
            Enter the 6-digit code sent to <strong><?php echo htmlspecialchars($email); ?></strong> to reset your password.
        </p>
        <form method="post" autocomplete="off" id="verifyForm">
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>" />
        
            <input type="text" name="code" id="code" maxlength="6" pattern="[0-9]{6}" placeholder="Enter 6-digit code" inputmode="numeric"  />
            <div class="validation-error" id="codeError"></div>
            <button type="submit">Verify Code</button>
        </form>
        <div class="helper resend" style="margin-top:18px;">Didn't get it? <a href="forget.php">Request a new code</a>.</div>
    </div>

    <script>
        const form = document.getElementById('verifyForm');
        const codeInput = document.getElementById('code');
        const codeError = document.getElementById('codeError');

        form.addEventListener('submit', function (e) {
            codeError.textContent = '';
            if (codeInput.value.trim() === '') {
                e.preventDefault();
                codeError.textContent = 'Code cannot be empty.';
            } else if (codeInput.value.trim().length < 6) {
                e.preventDefault();
                codeError.textContent = 'Code must be 6 digits.';
            }
        });
    </script>
</body>

</html>
