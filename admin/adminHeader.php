<?php
session_start();
include '../connection/connection.php';
include '../connection/function.php';

// require login
if (!isset($_SESSION['uid'])) {
  header('Location: ../login.php');
  exit;
}

// fetch current user once for all admin pages using this header
$currentUser = getUserByID($_SESSION['uid']);
$activePage = isset($_GET['page']) ? $_GET['page'] : 'vacancies';
?>
<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Jobir jobs Dashboard</title>
  <link rel="shortcut icon" type="image/png" href="./assets/images/logos/favicon.png" />
  <link rel="stylesheet" href="./assets/css/styles.min.css" />
</head>

<body>
  <!--  Body Wrapper -->
  <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">

    <!--  App Topstrip -->
    <div class="app-topstrip bg-dark py-6 px-3 w-100 d-lg-flex align-items-center justify-content-between">
      <div class="d-flex align-items-center justify-content-center gap-3 mb-2 mb-lg-0">
        <a class="d-flex justify-content-center" href="index.php">
          <img src="assets/images/logos/logo-wrappixel.svg" alt="" width="150">
        </a>
      </div>
      <h3 class="text-white mb-0 fs-5 text-center">Jobirs jobs dashboard</h3>
    </div>
    <!-- Sidebar Start -->
    <aside class="left-sidebar">
      <!-- Sidebar scroll-->
      <div>
        <div class="brand-logo d-flex align-items-center justify-content-between">
          <a href="./index.php" class="text-nowrap logo-img">
            <img src="assets/images/logos/logo.svg" alt="" />
          </a>
          <div class="close-btn d-xl-none d-block sidebartoggler cursor-pointer" id="sidebarCollapse">
            <i class="ti ti-x fs-6"></i>
          </div>
        </div>
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
          <ul id="sidebarnav">
            <?php $activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'view'; ?>
            <li class="nav-small-cap">
              <iconify-icon icon="solar:menu-dots-linear" class="nav-small-cap-icon fs-4"></iconify-icon>
              <span class="hide-menu">School Vacancies</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link d-flex align-items-center gap-3" href="./post_vacancy.php">
                <i class="ti ti-square-plus"></i>
                <span class="hide-menu">Post Vacancy</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link d-flex align-items-center gap-3" href="./view_vacancies.php">
                <i class="ti ti-list-details"></i>
                <span class="hide-menu">View Vacancies</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link d-flex align-items-center gap-3" href="./edit_vacancies.php">
                <i class="ti ti-edit"></i>
                <span class="hide-menu">Edit Vacancies</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link d-flex align-items-center gap-3" href="./delete_vacancies.php">
                <i class="ti ti-trash"></i>
                <span class="hide-menu">Delete Vacancies</span>
              </a>
            </li>

            <li class="nav-small-cap mt-3">
              <iconify-icon icon="solar:chat-round-dots-outline" class="nav-small-cap-icon fs-4"></iconify-icon>
              <span class="hide-menu">Feedback</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link d-flex align-items-center gap-3" href="./feedback.php">
                <i class="ti ti-message-circle"></i>
                <span class="hide-menu">View Feedback</span>
              </a>
            </li>

            <li class="nav-small-cap mt-3">
              <iconify-icon icon="solar:users-group-rounded-outline" class="nav-small-cap-icon fs-4"></iconify-icon>
              <span class="hide-menu">Teachers</span>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link d-flex align-items-center gap-3 <?php echo ($activePage === 'teachers' && $activeTab === 'view') ? 'active' : ''; ?>" href="./index.php?page=teachers&tab=view">
                <i class="ti ti-users"></i>
                <span class="hide-menu">View Teachers</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link d-flex align-items-center gap-3 <?php echo ($activePage === 'teachers' && $activeTab === 'edit') ? 'active' : ''; ?>" href="./index.php?page=teachers&tab=edit">
                <i class="ti ti-user-edit"></i>
                <span class="hide-menu">Edit Teacher</span>
              </a>
            </li>
            <li class="sidebar-item">
              <a class="sidebar-link d-flex align-items-center gap-3 <?php echo ($activePage === 'teachers' && $activeTab === 'block') ? 'active' : ''; ?>" href="./index.php?page=teachers&tab=block">
                <i class="ti ti-user-cancel"></i>
                <span class="hide-menu">Block / Unblock</span>
              </a>
            </li>
          </ul>
        </nav>
        <!-- End Sidebar navigation -->
      </div>
      <!-- End Sidebar scroll-->
    </aside>
    <!--  Sidebar End -->