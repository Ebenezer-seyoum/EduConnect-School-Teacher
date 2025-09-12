<?php
include 'adminHeader.php'
?>

<!--  Main wrapper -->
<div class="body-wrapper">
  <!--  Header Start -->
  <header class="app-header">
    <nav class="navbar navbar-expand-lg navbar-light">
      <ul class="navbar-nav">
        <li class="nav-item d-block d-xl-none">
          <a class="nav-link sidebartoggler " id="headerCollapse" href="javascript:void(0)">
            <i class="ti ti-menu-2"></i>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link " href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ti ti-bell"></i>
            <div class="notification bg-primary rounded-circle"></div>
          </a>
          <div class="dropdown-menu dropdown-menu-animate-up" aria-labelledby="drop1">
            <div class="message-body">
              <a href="javascript:void(0)" class="dropdown-item">
                Item 1
              </a>
              <a href="javascript:void(0)" class="dropdown-item">
                Item 2
              </a>
            </div>
          </div>
        </li>
      </ul>
      <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">

          <li class="nav-item dropdown">
            <a class="nav-link " href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
              aria-expanded="false">
              <img src="./assets/images/profile/user-1.jpg" alt="" width="35" height="35" class="rounded-circle">
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
              <div class="message-body">
                <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                  <i class="ti ti-user fs-6"></i>
                  <p class="mb-0 fs-3">My Profile</p>
                </a>
                <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                  <i class="ti ti-mail fs-6"></i>
                  <p class="mb-0 fs-3">My Account</p>
                </a>
                <a href="javascript:void(0)" class="d-flex align-items-center gap-2 dropdown-item">
                  <i class="ti ti-list-check fs-6"></i>
                  <p class="mb-0 fs-3">My Task</p>
                </a>
                <a href="./authentication-login.html" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </nav>
  </header>
  <!--  Header End -->
  <div class="body-wrapper-inner">
    <div class="container-fluid">
      <!--  Row 1 -->
      <div class="row">
        <div class="col-12">
          <!-- KPI Cards -->
          <div class="row">
            <div class="col-md-3 col-sm-6 mb-3">
              <div class="card">
                <div class="card-body">
                  <p class="text-muted mb-1">Total Users</p>
                  <h3 id="kpi-total-users" class="mb-0">--</h3>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
              <div class="card">
                <div class="card-body">
                  <p class="text-muted mb-1">Active Users</p>
                  <h3 id="kpi-active-users" class="mb-0">--</h3>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
              <div class="card">
                <div class="card-body">
                  <p class="text-muted mb-1">Inactive Users</p>
                  <h3 id="kpi-inactive-users" class="mb-0">--</h3>
                </div>
              </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
              <div class="card">
                <div class="card-body">
                  <p class="text-muted mb-1">Teachers</p>
                  <h3 id="kpi-teachers" class="mb-0">--</h3>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-lg-8">
          <div class="card w-100">
            <div class="card-body">
              <div class="d-md-flex align-items-center">
                <div>
                  <h4 class="card-title">Users by Type</h4>
                  <p class="card-subtitle">Distribution of user roles</p>
                </div>
                <div class="ms-auto">
                  <ul class="list-unstyled mb-0">
                    <li class="list-inline-item text-primary">Live</li>
                  </ul>
                </div>
              </div>
              <div id="sales-overview" class="mt-4 mx-n6"></div>
            </div>
          </div>
        </div>
        <div class="col-lg-4">
          <div class="card overflow-hidden">
            <div class="card-body pb-0">
              <div class="d-flex align-items-start">
                <div>
                  <h4 class="card-title">Recent Users</h4>
                  <p class="card-subtitle">Latest signups or updates</p>
                </div>
                <div class="ms-auto">
                  <div class="dropdown">
                    <a href="javascript:void(0)" class="text-muted" id="year1-dropdown" data-bs-toggle="dropdown"
                      aria-expanded="false">
                      <i class="ti ti-dots fs-7"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="year1-dropdown">
                      <li>
                        <a class="dropdown-item" href="javascript:void(0)" id="refresh-dashboard">Refresh</a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="javascript:void(0)" id="auto-refresh-toggle">Auto Refresh</a>
                      </li>
                      <li>
                        <a class="dropdown-item disabled" href="javascript:void(0)">v1</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              <div id="recent-users" class="mt-3">
                <div class="text-muted">Loading...</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php
include 'adminFooter.php'
?>