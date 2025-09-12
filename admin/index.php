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
          <?php $unreadCount = getUnreadNotificationCount($_SESSION['uid']);
          $notifs = getNotifications($_SESSION['uid'], 8);
          $schema = function_exists('notifications_read_schema') ? notifications_read_schema() : ['col' => 'read_at', 'mode' => 'timestamp'];
          $readCol = $schema['col'];
          $readMode = $schema['mode'];
          ?>
          <a class="nav-link position-relative" href="javascript:void(0)" id="drop1" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="ti ti-bell"></i>
            <?php if ($unreadCount > 0) { ?>
              <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="notif-count"><?php echo $unreadCount; ?></span>
            <?php } ?>
          </a>
          <div class="dropdown-menu dropdown-menu-animate-up dropdown-menu-end" aria-labelledby="drop1" style="min-width: 320px;">
            <div class="message-body">
              <?php if (empty($notifs)) { ?>
                <div class="dropdown-item text-muted small">No notifications</div>
                <?php } else {
                foreach ($notifs as $n) {
                  $isUnread = true;
                  if ($readMode === 'timestamp') {
                    $isUnread = empty($n[$readCol]);
                  } else {
                    $isUnread = isset($n[$readCol]) ? ((int)$n[$readCol] === 0) : true;
                  }
                ?>
                  <a href="index.php?page=vacancies&tab=notif_detail&nid=<?php echo (int)$n['id']; ?>" class="dropdown-item d-flex justify-content-between align-items-start <?php echo $isUnread ? 'fw-semibold' : ''; ?>">
                    <span>
                      <?php echo htmlspecialchars($n['sender_name'] ?: 'Request', ENT_QUOTES, 'UTF-8'); ?>
                      <small class="d-block text-muted">#<?php echo (int)$n['id']; ?> • <?php echo htmlspecialchars(substr($n['message'] ?? '', 0, 50)); ?></small>
                    </span>
                    <?php if ($isUnread) { ?><span class="badge bg-primary">New</span><?php } ?>
                  </a>
              <?php }
              } ?>
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
              <span class="ms-2 d-none d-md-inline-block fw-semibold">
                <?php echo isset($currentUser['full_name']) ? htmlspecialchars($currentUser['full_name']) : 'User'; ?>
              </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up" aria-labelledby="drop2">
              <div class="message-body">
                <a href="index.php?page=profile" class="d-flex align-items-center gap-2 dropdown-item">
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
                <a href="../connection/logout.php" class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
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
      <?php
      $page = isset($_GET['page']) ? $_GET['page'] : 'vacancies';
      $tab = isset($_GET['tab']) ? $_GET['tab'] : 'view';
      ?>
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <?php if ($page === 'vacancies') { ?>
                <h4 class="card-title mb-4">School Vacancies</h4>
                <!-- Toolbar removed; use sidebar links for navigation -->
                <?php if ($tab === 'post') { ?>
                  <?php include __DIR__ . '/post_vacancy.php'; ?>
                <?php } elseif ($tab === 'view') { ?>
                  <?php include __DIR__ . '/vacancies_view.php'; ?>
                <?php } elseif ($tab === 'edit') { ?>
                  <?php include __DIR__ . '/vacancies_edit.php'; ?>
                <?php } elseif ($tab === 'delete') { ?>
                  <?php include __DIR__ . '/vacancies_delete.php'; ?>
                <?php } elseif ($tab === 'notif_detail') { ?>
                  <?php
                  $nid = isset($_GET['nid']) ? (int)$_GET['nid'] : 0;
                  if ($nid > 0) {
                    $nd = getNotificationById($nid, $_SESSION['uid']);
                    if ($nd) {
                      markNotificationRead($nid, $_SESSION['uid']);
                      echo '<h5>Request Detail</h5>';
                      echo '<div class="row g-2">';
                      echo '<div class="col-12"><div class="form-control">From: ' . htmlspecialchars($nd['sender_name'] ?? '') . '</div></div>';
                      echo '<div class="col-12"><div class="form-control">Contact: ' . htmlspecialchars($nd['sender_contact'] ?? '') . '</div></div>';
                      echo '<div class="col-12"><div class="form-control">Message: ' . htmlspecialchars($nd['message'] ?? '') . '</div></div>';
                      echo '</div>';
                    } else {
                      echo '<div class="alert alert-warning">Notification not found.</div>';
                    }
                  }
                  ?>
                <?php } ?>
              <?php } elseif ($page === 'teachers') { ?>
                <h4 class="card-title mb-4">Teachers</h4>
                <div class="d-flex gap-2 mb-3">
                  <a href="index.php?page=teachers&tab=view" class="btn btn-outline-secondary btn-sm">View All</a>
                  <a href="index.php?page=teachers&tab=edit" class="btn btn-outline-secondary btn-sm">Edit</a>
                  <a href="index.php?page=teachers&tab=block" class="btn btn-outline-warning btn-sm">Block / Unblock</a>
                </div>
                <?php if ($tab === 'view') { ?>
                  <p class="text-muted">List all teachers (DB wiring TBD).</p>
                <?php } elseif ($tab === 'edit') { ?>
                  <p class="text-muted">Edit teacher details.</p>
                <?php } elseif ($tab === 'block') { ?>
                  <p class="text-muted">Block or Unblock a teacher account.</p>
                <?php } ?>
              <?php } elseif ($page === 'profile') { ?>
                <h4 class="card-title mb-4">My Profile</h4>
                <div class="row g-3">
                  <div class="col-md-6">
                    <div class="form-control">Full Name: <?php echo htmlspecialchars($currentUser['full_name'] ?? ''); ?></div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-control">Email: <?php echo htmlspecialchars($currentUser['email'] ?? ''); ?></div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-control">Phone: <?php echo htmlspecialchars($currentUser['phone'] ?? ''); ?></div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-control">Username: <?php echo htmlspecialchars($currentUser['user_name'] ?? ''); ?></div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-control">User Type: <?php echo htmlspecialchars($currentUser['user_type'] ?? ''); ?></div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-control">Status: <?php echo (isset($currentUser['user_status']) && (int)$currentUser['user_status'] === 1) ? 'Active' : 'Inactive'; ?></div>
                  </div>
                </div>
              <?php } ?>
            </div>
          </div>
        </div>
      </div>
      <?php
      include 'adminFooter.php'
      ?>