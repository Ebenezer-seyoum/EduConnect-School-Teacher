<?php
include 'adminHeader.php';

$items = getFeedbacks(200);
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
            </ul>
            <div class="navbar-collapse justify-content-end px-0" id="navbarNav"></div>
        </nav>
    </header>
    <!--  Header End -->
    <div class="body-wrapper-inner">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Feedback</h4>
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width:80px;">#</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Subject</th>
                                            <th style="width:120px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($items)) { ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">No feedback yet.</td>
                                            </tr>
                                            <?php } else {
                                            foreach ($items as $f) { ?>
                                                <tr>
                                                    <td><?php echo (int)$f['id']; ?></td>
                                                    <td><?php echo htmlspecialchars($f['name']); ?></td>
                                                    <td><?php echo htmlspecialchars($f['email']); ?></td>
                                                    <td><?php echo htmlspecialchars($f['subject']); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#fbModal" data-id="<?php echo (int)$f['id']; ?>">Details</button>
                                                    </td>
                                                </tr>
                                        <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Details Modal -->
                            <div class="modal fade" id="fbModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Feedback Detail</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div id="fb-loading" class="text-muted">Loading...</div>
                                            <div id="fb-content" style="display:none;">
                                                <div class="mb-2"><strong>Name:</strong> <span id="fb-name"></span></div>
                                                <div class="mb-2"><strong>Email:</strong> <span id="fb-email"></span></div>
                                                <div class="mb-2"><strong>Subject:</strong> <span id="fb-subject"></span></div>
                                                <div class="mb-2"><strong>Message:</strong><br>
                                                    <div class="form-control" id="fb-message" style="min-height:120px; white-space:pre-wrap"></div>
                                                </div>
                                                <div class="text-muted small" id="fb-created"></div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <script>
                                const modalEl = document.getElementById('fbModal');
                                modalEl.addEventListener('show.bs.modal', async (ev) => {
                                    const btn = ev.relatedTarget;
                                    const id = btn?.getAttribute('data-id');
                                    document.getElementById('fb-loading').style.display = 'block';
                                    document.getElementById('fb-content').style.display = 'none';
                                    try {
                                        const resp = await fetch('feedback_detail.php?id=' + encodeURIComponent(id));
                                        const data = await resp.json();
                                        document.getElementById('fb-name').textContent = data.name || '';
                                        document.getElementById('fb-email').textContent = data.email || '';
                                        document.getElementById('fb-subject').textContent = data.subject || '';
                                        document.getElementById('fb-message').textContent = data.message || '';
                                        document.getElementById('fb-created').textContent = data.created_at ? ('Submitted at ' + data.created_at) : '';
                                        document.getElementById('fb-loading').style.display = 'none';
                                        document.getElementById('fb-content').style.display = 'block';
                                    } catch (e) {
                                        document.getElementById('fb-loading').textContent = 'Failed to load.';
                                    }
                                });
                            </script>

                        </div>
                    </div>
                </div>
            </div>
            <?php include 'adminFooter.php'; ?>