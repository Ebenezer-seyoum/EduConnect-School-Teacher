<?php
// This endpoint returns only modal markup to be injected via fetch().
// Core functions come from Homeheader on the parent page; add minimal fallbacks.
if (!function_exists('getVacancyById')) {
    require_once __DIR__ . '/connection/connection.php';
    require_once __DIR__ . '/connection/function.php';
}

$sid = (int)($_GET['sid'] ?? 0);
$v = $sid ? getVacancyById($sid) : null;

if (!$v) {
    echo '<div class="modal fade" id="vacancyNotFoundModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-body text-center py-5">
                        <div class="mb-3" style="font-size:3rem;color:#dc3545;">
                            <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                        </div>
                        <div class="fw-bold" style="font-size:1.5rem;">Vacancy Not Found</div>
                        <p class="text-muted mt-2 mb-0">The vacancy you’re looking for might have been removed or is currently unavailable.</p>
                    </div>
                    <div class="modal-footer justify-content-center">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>';
    exit;
}

$schoolTitle = !empty($v['title']) ? $v['title'] : 'School';
$logo = !empty($v['logo']) ? $v['logo'] : 'Home/assets/img/icon.png';
$positionText = !empty($v['position']) ? $v['position'] : 'Teacher';
$numPositions = (int)($v['number_of_position'] ?? 1);
?>

<div class="modal fade" id="vacancyDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <!-- Header -->
            <div class="modal-header" style="background-color:#0d47a1;">
                <h5 class="modal-title fw-bold text-white">
                    <i class="fas fa-school text-primary me-2"></i><?php echo htmlspecialchars($schoolTitle); ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">
                <div class="d-flex gap-3 mb-4">
                    <!-- Logo -->
                    <div style="flex:0 0 96px;">
                        <div style="width:96px;height:96px;border-radius:8px;border:1px solid #e2e6ea;
                                    display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden;">
                            <img src="<?php echo htmlspecialchars($logo); ?>" alt="logo"
                                style="max-width:100%; max-height:100%; object-fit:contain;">
                        </div>
                    </div>

                    <!-- Info -->
                    <div class="flex-grow-1">
                        <h4 class="fw-bold mb-3 text-primary">
                            <?php echo htmlspecialchars(ucfirst($positionText)); ?> Teacher
                        </h4>
                        <div class="row g-2" style="line-height:1.6; color:#1a2b3c;">
                            <div class="col-sm-6">
                                <strong>School:</strong> <?php echo htmlspecialchars($schoolTitle); ?>
                            </div>
                            <div class="col-sm-6">
                                <strong>Location:</strong> <?php echo htmlspecialchars($v['location'] ?? ''); ?>
                            </div>
                            <div class="col-sm-6">
                                <strong>Positions:</strong> <?php echo $numPositions; ?>
                            </div>
                            <div class="col-sm-6">
                                <strong>Salary:</strong> ETB <?php echo number_format((float)($v['salary'] ?? 0), 2); ?>
                            </div>
                            <div class="col-sm-6">
                                <strong>Employment Type:</strong> <?php echo htmlspecialchars($v['employment_type'] ?? ''); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Description -->
                <div class="mb-2 fw-bold text-dark">Description</div>
                <div style="line-height:1.7; color:#243447;">
                    <?php
                    $desc = trim((string)($v['description'] ?? ''));
                    if ($desc === '') {
                        echo '<ul class="mb-3 ps-3">'
                            . '<li>Deliver engaging lessons aligned with the curriculum.</li>'
                            . '<li>Assess and support student learning and growth.</li>'
                            . '<li>Collaborate with staff and participate in school activities.</li>'
                            . '</ul>';
                    } else {
                        echo nl2br(htmlspecialchars($desc));
                    }
                    ?>
                </div>

                <div class="alert alert-warning mt-4 mb-0">
                    Contact details are hidden. Use <strong>Request</strong> to send a secure message to the school admin.
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-primary" onclick="loadVacancyRequest(<?php echo (int)$v['sid']; ?>)">
                    Request This Position
                </button>
            </div>
        </div>
    </div>
</div>