  <?php
  session_start();
  //error_reporting(0);
  include '../Edu-Connect/connection/connection.php';
  include '../Edu-Connect/connection/function.php';
  ?>
  <!doctype html>
  <html lang="en">

  <head>
    <meta charset="utf-8">
    <title>Jobir jobs</title>
    <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
    <link rel="icon" href="login/assets/img/icon.ico" type="image/x-icon" />
    <meta content="" name="keywords">
    <meta content="" name="description">
    <link rel="stylesheet" href="Home/assets/lib/animate/animate.min.css" />
    <link href="login/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="login/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
      body.auth-bg {
        /* Monochrome curved wave background (single brand color) */
        background: linear-gradient(135deg, #f8fafc 0%, #eef2f7 100%) fixed,
          url('login/assets/img/bg-auth-curve-mono.svg') center/cover fixed no-repeat;
        padding-top: 76px;
      }

      .auth-card,
      .auth-card * {
        color: #212529;
      }

      /* Typography: Use Times New Roman across auth pages */
      body.auth-bg,
      .auth-header,
      .auth-card {
        font-family: 'Times New Roman', Times, serif !important;
      }

      /* Preserve Font Awesome fonts so icons remain visible with custom fonts */
      .auth-card .fa,
      .auth-card .fa-solid,
      .auth-card .fa-regular,
      .auth-card .fa-brands,
      .auth-header .fa,
      .auth-header .fa-solid,
      .auth-header .fa-regular,
      .auth-header .fa-brands {
        font-family: 'Font Awesome 6 Free' !important;
      }

      .auth-card .fa-brands,
      .auth-header .fa-brands {
        font-family: 'Font Awesome 6 Brands' !important;
      }

      .auth-card .fa-solid,
      .auth-header .fa-solid {
        font-weight: 900 !important;
      }

      .auth-card .fa-regular,
      .auth-header .fa-regular {
        font-weight: 400 !important;
      }


      /* Button hover: darken bg, subtle top-to-bottom shadow, slight size decrease */
      .btn-primary {
        background-color: #0d47a1;
        /* dark blue */
        border-color: #0d47a1;
        color: #fff;
        transition: background-color .15s ease-in-out, box-shadow .15s ease-in-out, transform .15s ease-in-out, filter .15s ease-in-out;
      }

      .btn-primary:hover,
      .btn-primary:focus {
        background-color: #1976d2;
        /* lighter on hover */
        border-color: #1976d2;
        box-shadow: 0 -.5rem 1rem rgba(0, 0, 0, .06) inset, 0 .25rem .75rem rgba(0, 0, 0, .08);
        transform: scale(0.99);
      }

      .btn-outline-primary:hover {
        box-shadow: 0 -.5rem 1rem rgba(0, 0, 0, .05) inset, 0 .25rem .75rem rgba(0, 0, 0, .08);
        transform: scale(0.99);
      }

      /* Alert emphasis */
      .alert.alert-success,
      .alert.alert-danger {
        font-weight: 800;
      }

      .alert.alert-danger {
        color: #020000ff !important;
        /* stronger red text */
        background-color: #fd0835ff !important;
        /* soft red background */
        border-color: #f5c2c7 !important;
      }

      .alert.alert-success {
        color: #ffffff !important;
        background-color: #1b5e20 !important;
        /* dark green */
        border-color: #145a18 !important;
      }

      /* Login page signup callout */
      .auth-callout {
        background: #f8fafc;
        border: 1px solid #e5e7eb;
        border-radius: .5rem;
        padding: .75rem 1rem;
      }

      .auth-header.navbar {
        height: 60px;
      }

      .auth-card {
        max-width: 480px;
        margin: 32px auto 48px;
      }

      .brand-logo {
        height: 36px;
        width: auto;
      }

      /* Auth inputs - bold black text */
      .auth-card input.form-control,
      .auth-card .form-select {
        color: #000 !important;
        font-weight: 600;
      }

      .toggle-password {
        min-width: 46px;
      }

      .btn i {
        margin-right: .5rem;
      }

      /* White CTA box below primary actions */
      .auth-cta-box {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: .5rem;
        padding: .75rem 1rem;
        display: flex;
        justify-content: center;
        gap: .35rem;
        transition: box-shadow .15s ease-in-out, transform .15s ease-in-out;
      }

      .auth-cta-box:hover {
        box-shadow: 0 .25rem .75rem rgba(0, 0, 0, .08);
        transform: translateY(-1px);
      }

      .auth-cta-box a {
        text-decoration: none;
        font-weight: 700;
      }

      .auth-cta-box a:hover {
        text-decoration: underline;
      }

      /* Unified CTA link style (Forgot password, Sign Up, Login in CTA) */
      .auth-cta-link {
        color: #0d47a1;
        font-weight: 700;
        text-decoration: underline;
        transition: color .15s ease-in-out;
      }

      .auth-cta-link:hover {
        color: #1976d2;
      }

      /* Signup button inside CTA with light gray hover */
      .auth-cta-box .auth-signup-btn {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        color: #0d47a1;
        font-weight: 700;
        padding: .4rem .75rem;
        border-radius: .375rem;
        text-decoration: none;
        transition: background-color .15s ease-in-out, box-shadow .15s ease-in-out, transform .15s ease-in-out;
      }

      .auth-cta-box .auth-signup-btn:hover {
        background: #f3f4f6;
        /* light gray */
        color: #0b3a85;
        /* slightly darker text */
        text-decoration: none;
        box-shadow: 0 .1rem .25rem rgba(0, 0, 0, .05);
      }

      /* Make invalid feedback clearly red and readable */
      .auth-card .invalid-feedback {
        color: #b00020 !important;
        font-weight: 600;
      }

      /* Ensure feedback shows for input-group invalid cases (adjacent or later siblings) */
      .auth-card .input-group.is-invalid+.invalid-feedback,
      .auth-card .input-group.is-invalid~.invalid-feedback {
        display: block;
      }

      /* Forgot password link styling */
      a.forgot-link {
        color: #0d47a1;
        font-weight: 600;
        text-decoration: underline;
        transition: color .15s ease-in-out;
      }

      a.forgot-link:hover {
        color: #1976d2;
      }
    </style>
  </head>

  <body class="auth-bg">

    <!-- Top brand/header -->
    <header class="auth-header navbar navbar-light bg-white shadow-sm fixed-top">
      <div class="container d-flex align-items-center justify-content-between">
        <a class="navbar-brand d-flex align-items-center" href="index.php" style="text-decoration:none;">
          <img src="Home/assets/img/logo.png" alt="Jobir Jobs" class="brand-logo me-2" />
        </a>
        <div class="d-flex align-items-center gap-2">
          <a href="index.php" class="btn btn-outline-primary btn-sm">Back to Home</a>
        </div>
      </div>
    </header>