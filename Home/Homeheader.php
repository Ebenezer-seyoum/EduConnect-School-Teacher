<?php
session_start();
//error_reporting(0);
include '../Edu-Connect/connection/connection.php';
include '../Edu-Connect/connection/function.php';
?>
<!doctype html>
<html class="no-js" lang="zxx">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Jobir Jobs</title>
    <meta name="description" content="Jobir Jobs - Find teaching jobs and school vacancies easily">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="manifest" href="site.webmanifest">
    <link rel="shortcut icon" type="image/x-icon" href="Home/assets/img/icon.ico">

    <!-- CSS here -->
    <link rel="stylesheet" href="Home/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="Home/assets/css/style.css">

    <style>
        /* === Apply Times New Roman to entire website === */
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        a,
        li,
        span,
        div,
        button {
            font-family: 'Times New Roman', Times, serif;
        }

        /* Hero improvements */
        .hero-landing {
            position: relative;
            background-position: center;
            background-size: cover;
        }

        /* Darken the background image slightly for better text contrast */
        .hero-landing.hero-overly::before {
            background-color: rgba(0, 0, 0, 0.35);
        }

        /* Spacing and layout */
        .slider-area .hero__caption {
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .slider-area .hero__caption h1 {
            color: #ffffff;
            font-size: 64px;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 20px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.35);
        }

        @media (max-width: 1199.98px) {
            .slider-area .hero__caption h1 {
                font-size: 52px;
            }
        }

        @media (max-width: 991.98px) {
            .slider-area .hero__caption h1 {
                font-size: 42px;
            }
        }

        @media (max-width: 767.98px) {
            .slider-area .hero__caption h1 {
                font-size: 30px;
            }
        }

        /* Search box card styling over hero */
        .slider-area form.search-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: saturate(150%) blur(2px);
            border-radius: 10px;
            padding: 12px 12px 12px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
        }

        .slider-area form.search-box .input-form input {
            height: 56px;
            font-size: 16px;
        }

        .slider-area form.search-box .select-form .nice-select {
            height: 56px;
            line-height: 40px;
        }

        .slider-area form.search-box .search-form a {
            height: 56px;
            line-height: 28px;
            font-size: 16px;
            border-radius: 8px;
        }

        /* Reduce overall hero min-height for a tighter, modern look */
        .slider-height {
            min-height: 680px;
        }

        @media (max-width: 991.98px) {
            .slider-height {
                min-height: 560px;
            }
        }

        @media (max-width: 767.98px) {
            .slider-height {
                min-height: 480px;
            }
        }

        /* Logo */
        .logo img {
            max-height: 80px;
            width: auto;
            transition: transform 0.4s ease;
        }

        .logo img:hover {
            transform: scale(1.1);
        }

        /* Navigation */
        #navigation {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            gap: 40px;
        }

        #navigation li a {
            font-size: 18px;
            font-weight: 700;
            color: #333;
            text-decoration: none;
            padding: 8px 0;
            transition: color 0.3s ease, transform 0.3s ease, border-bottom 0.3s ease;
        }

        #navigation li a:hover {
            color: #007bff;
            border-bottom: 3px solid #007bff;
            transform: scale(1.1);
        }

        /* Buttons (Rectangle with hover color change) */
        .header-btn .btn {
            padding: 12px 28px;
            border-radius: 4px;
            /* rectangle */
            font-weight: 700;
            font-size: 16px;
            transition: all 0.4s ease;
        }

        .head-btn1 {
            background: #007bff;
            color: #fff;
            border: 1px solid #007bff;
        }

        .head-btn1:hover {
            background: #0056b3;
            color: #fff;
        }

        .head-btn2 {
            background: #fff;
            border: 2px solid #007bff;
            color: #007bff;
        }

        .head-btn2:hover {
            background: #007bff;
            color: #fff;
        }

        /* Mobile menu */
        .mobile_menu {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            transition: background 0.3s ease, transform 0.3s ease;
            cursor: pointer;
        }

        .mobile_menu:hover {
            background: #e0e0e0;
            transform: scale(1.05);
        }

        /* Keep all images responsive within their containers */
        img {
            max-width: 100%;
            height: auto;
            display: block;
        }

        .hero-img {
            width: 100% !important;
            max-width: 100% !important;
            height: auto;
            display: block;
            object-fit: contain;
        }

        /* Right-align hero text at large screens only, keep center on small */
        @media (min-width: 992px) {
            .slider-area .hero__caption {
                text-align: right;
            }
        }

        @media (max-width: 991.98px) {
            .slider-area .hero__caption {
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <header>
        <!-- Header Start -->
        <div class="header-area header-transparrent">
            <div class="headder-top header-sticky">
                <div class="container">
                    <div class="row align-items-center justify-content-between">

                        <!-- Logo -->
                        <div class="col-lg-3 col-md-3 col-6">
                            <div class="logo">
                                <a href="index.php">
                                    <img src="Home/assets/img/logo.png" alt="Jobir Jobs">
                                </a>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="col-lg-6 col-md-6 d-none d-lg-block">
                            <nav class="main-menu text-center">
                                <ul id="navigation">
                                    <li><a href="index.php">Home</a></li>
                                    <li><a href="about.php">About</a></li>
                                    <li><a href="school-vacancy.php">School Vacancy</a></li>
                                    <li><a href="find-teachers.php">Find Teachers</a></li>
                                    <li><a href="contact.php">Contact</a></li>
                                </ul>
                            </nav>
                        </div>

                        <!-- Auth Buttons -->
                        <div class="col-lg-3 col-md-3 col-6 text-end">
                            <div class="header-btn">
                                <a href="register.php" class="btn head-btn1">Register</a>
                                <a href="login.php" class="btn head-btn2 ms-2">Login</a>
                            </div>
                        </div>

                        <!-- Mobile Menu -->
                        <div class="col-12 d-lg-none">
                            <div class="mobile_menu">
                                <span>☰ Menu</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- Header End -->
    </header>