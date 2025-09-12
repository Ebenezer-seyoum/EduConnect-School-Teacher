<?php
// Landing page after successful password reset
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Jobir Jobs</title>
    <link rel="stylesheet" href="login/assets/lib/wow/animate.css" />
    <link rel="stylesheet" href="login/assets/css/style.css" />
    <link rel="icon" type="image/png" href="Home/assets/img/icon.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAI6G+Ytzyq6Y9ZTl+DYdUjKV+8L8XKni5rN4zj+YxN9gA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: Segoe UI, Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 38px 18px 60px;
        }

        .card {
            background: #fff;
            border-radius: 14px;
            max-width: 600px;
            width: 100%;
            margin: 0 auto;
            box-shadow: 0 4px 18px rgba(0, 0, 0, .07);
            padding: 34px 40px 42px;
            position: relative;
            overflow: hidden;
        }

        .card:before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, #0d47a1 0%, #1976d2 100%);
            opacity: .05;
        }

        .icon-wrap {
            width: 92px;
            height: 92px;
            margin: 0 auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0d47a1, #1565c0);
            color: #fff;
            border-radius: 24px;
            font-size: 46px;
            box-shadow: 0 4px 14px rgba(13, 71, 161, .35);
        }

        h1 {
            font-size: 1.9rem;
            margin: 8px 0 12px;
            color: #0d47a1;
            font-weight: 700;
        }

        p.lead {
            font-size: 1rem;
            line-height: 1.5;
            margin: 0 0 18px;
            color: #333;
            font-weight: 500;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 20px;
        }

        .actions a {
            flex: 1 1 auto;
            text-align: center;
            text-decoration: none;
            font-weight: 600;
            padding: 14px 20px;
            border-radius: 10px;
            transition: .25s;
            background: #0d47a1;
            color: #fff;
            box-shadow: 0 3px 10px rgba(13, 71, 161, .3);
        }

        .actions a:hover {
            background: #09326d;
            transform: translateY(-2px);
        }

        .secondary {
            background: #eceff1;
            color: #0d47a1;
            box-shadow: none;
        }

        .secondary:hover {
            background: #d9e2e7;
            color: #0d47a1;
        }

        .note {
            font-size: .8rem;
            color: #607d8b;
            margin-top: 28px;
        }

        @media (max-width:600px) {
            .card {
                padding: 40px 26px;
            }

            h1 {
                font-size: 1.55rem;
            }

            .icon-wrap {
                width: 76px;
                height: 76px;
                font-size: 38px;
                border-radius: 20px;
            }
        }
    </style>
</head>

<body>
    <div class="card" role="alert" aria-live="polite">
        <div style="text-align:left;margin:0 0 18px;">
            <i class="fa-solid fa-circle-check" style="font-size:78px;color:#0d47a1;filter:drop-shadow(0 3px 8px rgba(0,0,0,.12));" aria-label="Password updated"></i>
        </div>
        <h1 style="margin-top:0;">Password Updated</h1>
        <p class="lead" style="margin-bottom:12px;">Your password was changed successfully and is now active.</p>
        <p style="font-size:.95rem;color:#444;line-height:1.55;">For security, we recommend logging in again to verify everything looks correct. You may proceed to <strong><a href="login.php" style="color:#0d47a1;text-decoration:underline;">log in to your account</a></strong> using your new password.</p>
        <p style="font-size:.95rem;color:#444;line-height:1.55;">If you'd rather continue browsing first, you can return to the <strong><a href="index.php" style="color:#0d47a1;text-decoration:underline;">home page</a></strong>.</p>
        <p style="font-size:.9rem;color:#5a6b77;margin-top:22px;">Didn't perform this action? Please <a href="contact.php" style="color:#0d47a1;text-decoration:underline;font-weight:600;">contact support</a> immediately so we can help secure your account.</p>
        <div class="note" style="margin-top:26px;">If any issue occurred while signing in, reach out to support for assistance.</div>
    </div>
</body>

</html>