<?php
$__isModal = isset($_GET['modal']) && (int)$_GET['modal'] === 1;
if (!function_exists('getVacancyById')) {
    require_once __DIR__ . '/connection/connection.php';
    require_once __DIR__ . '/connection/function.php';
}
// Handle request submit
$t_req_success = $t_req_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teacher_send_request']) && (int)$_POST['teacher_send_request'] === 1) {
    $adminUids = function_exists('getAdminUids') ? getAdminUids() : [1];
    $teacherName = trim($_POST['teacher_name'] ?? '');
    $teacherId = (int)($_POST['teacher_id'] ?? 0);
    $sender_name = trim($_POST['sender_name'] ?? 'Anonymous');
    $sender_contact = trim($_POST['sender_contact'] ?? '');
    $sender_email = trim($_POST['sender_email'] ?? '');
    $message = trim($_POST['message'] ?? ('Request to contact teacher' . ($teacherName ? ': ' . $teacherName : '')));
    $anyOk = false;
    foreach ($adminUids as $auid) {
        $contactCombined = $sender_contact . ($sender_email !== '' ? (' | ' . $sender_email) : '');
        if (addNotification((int)$auid, 0, $sender_name, $contactCombined, $message, $teacherId > 0 ? $teacherId : null)) {
            $anyOk = true;
        }
    }
    $t_req_success = $anyOk ? 'Request sent to admin(s).' : 'Failed to send request.';
}

$tid = (int)($_GET['tid'] ?? 0);
$uid = (int)($_GET['uid'] ?? 0);
$profile = null;
if ($tid > 0 && isset($conn)) {
    $res = mysqli_query($conn, "SELECT tid, user_id, full_name, profile_picture, bio, years_experience, expected_salary, address, subject, cv FROM teacher_profiles WHERE tid = $tid LIMIT 1");
    if ($res && mysqli_num_rows($res) === 1) {
        $profile = mysqli_fetch_assoc($res);
    }
}
if (!$profile && $uid > 0) {
    if (function_exists('getTeacherProfileByUserId')) {
        $profile = getTeacherProfileByUserId($uid);
    }
    if (!$profile && isset($conn)) {
        $res = mysqli_query($conn, "SELECT tid, user_id, full_name, profile_picture, bio, years_experience, expected_salary, address, subject, cv FROM teacher_profiles WHERE user_id = $uid LIMIT 1");
        if ($res && mysqli_num_rows($res) === 1) {
            $profile = mysqli_fetch_assoc($res);
        }
    }
}

function tesc($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
// If requested as modal content, output only the modal markup and exit
if ($__isModal) {
    if (!$profile) {
        echo '<div class="modal fade" id="teacherDetailModal" tabindex="-1" aria-hidden="true">'
            . '<div class="modal-dialog modal-dialog-centered">'
            . '<div class="modal-content">'
            . '<div class="modal-body text-center py-5">'
            . '<div class="mb-3" style="font-size:3rem;color:#dc3545;"><i class="fas fa-circle-exclamation" aria-hidden="true"></i></div>'
            . '<div class="fw-bold" style="font-size:1.5rem;">Teacher Not Found</div>'
            . '<p class="text-muted mt-2 mb-0">The teacher profile you’re looking for might have been removed or is unavailable.</p>'
            . '</div>'
            . '<div class="modal-footer justify-content-center">'
            . '<button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>'
            . '</div>'
            . '</div>'
            . '</div>'
            . '</div>';
        exit;
    }
    $nm = tesc($profile['full_name'] ?? '');
    $sub = tesc($profile['subject'] ?? '');
    $addr = tesc($profile['address'] ?? '');
    $exp = (int)($profile['years_experience'] ?? 0);
    $sal = number_format((float)($profile['expected_salary'] ?? 0), 2);
    $pic = tesc(!empty($profile['profile_picture']) ? $profile['profile_picture'] : 'admin/assets/images/no.png');
    $tidOut = (int)($profile['tid'] ?? 0);

    echo '<div class="modal fade" id="teacherDetailModal" tabindex="-1" aria-hidden="true">'
        . '<div class="modal-dialog modal-lg modal-dialog-centered">'
        . '<div class="modal-content">'
        // Header (match vacancy_detail style)
        . '<div class="modal-header" style="background-color:#0d47a1;">'
        . '<h5 class="modal-title fw-bold text-white">'
        . '<i class="fas fa-user-graduate text-warning me-2"></i>' . $nm
        . '</h5>'
        . '<button type="button" class="btn-close" data-bs-dismiss="modal"></button>'
        . '</div>'

        // Body
        . '<div class="modal-body">'
        . '<div class="d-flex gap-3 mb-4">'
        // Photo block (like logo box)
        . '<div style="flex:0 0 96px;">'
        . '<div style="width:96px;height:96px;border-radius:8px;border:1px solid #e2e6ea;display:flex;align-items:center;justify-content:center;background:#fff;overflow:hidden;">'
        . '<img src="' . $pic . '" alt="profile" style="max-width:100%; max-height:100%; object-fit:cover;">'
        . '</div>'
        . '</div>'
        // Info block
        . '<div class="flex-grow-1">'
        . '<h4 class="fw-bold mb-3 text-primary">' . ($sub !== '' ? htmlspecialchars(ucfirst($sub)) . ' Teacher' : 'Teacher') . '</h4>'
        . '<div class="row g-2" style="line-height:1.6; color:#1a2b3c;">'
        . '<div class="col-sm-6"><strong>Name:</strong> ' . $nm . '</div>'
        . '<div class="col-sm-6"><strong>Location:</strong> ' . $addr . '</div>'
        . '<div class="col-sm-6"><strong>Experience:</strong> ' . $exp . ' years</div>'
        . '<div class="col-sm-6"><strong>Expected Salary:</strong> ETB ' . $sal . '</div>'
        . '<div class="col-sm-6"><strong>Subject:</strong> ' . $sub . '</div>'
        . '</div>'
        . '</div>'
        . '</div>'

        . '<hr>'

        // Description
        . '<div class="mb-2 fw-bold text-dark">About ' . $nm . '</div>'
        . '<div style="line-height:1.7; color:#243447;">' . nl2br(tesc($profile['bio'] ?? '')) . '</div>'

        . '<div class="alert alert-warning mt-4 mb-0">Contact details are hidden. Use <strong>Send Request</strong> to notify the admin.</div>'
        . '</div>'

        // Footer
        . '<div class="modal-footer">'
        . '<button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>'
        . '<button type="button" class="btn btn-primary" onclick="(function(n,id){try{if(typeof showTeacherRequest===\'function\'){showTeacherRequest(n,id);return;}var tn=document.getElementById(\'tr-teacher-name\');if(tn)tn.value=n;var tv=document.getElementById(\'tr-teacher-view\');if(tv)tv.value=n;var ti=document.getElementById(\'tr-teacher-id\');if(ti)ti.value=id;var mEl=document.getElementById(\'teacherRequestModal\');if(mEl&&window.bootstrap){new bootstrap.Modal(mEl).show();}else{alert(\'Request form not available on this page.\');}}catch(e){alert(\'Request form not available.\');}})(' . json_encode($nm) . ', ' . $tidOut . ')">Send Request</button>'
        . '</div>'

        . '</div>'
        . '</div>'
        . '</div>';
    exit;
}
?>

<div class="container mt-4">
    <?php if (!empty($t_req_success) || !empty($t_req_error)) { ?>
        <script>
            (function() {
                function showAlert() {
                    <?php if (!empty($t_req_success)) { ?>
                        Swal.fire({
                                icon: 'success',
                                title: 'Request sent',
                                text: 'Thanks! Your request was sent successfully. An administrator will contact you soon.',
                                confirmButtonText: 'OK'
                            })
                            .then(function() {
                                window.location.href = window.location.pathname + window.location.search;
                            });
                    <?php } else { ?>
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong',
                            text: <?php echo json_encode($t_req_error); ?>,
                            confirmButtonText: 'OK'
                        });
                    <?php } ?>
                }
                if (typeof Swal === 'undefined') {
                    var s = document.createElement('script');
                    s.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                    s.onload = showAlert;
                    document.head.appendChild(s);
                } else {
                    showAlert();
                }
            })();
        </script>
    <?php } ?>

    <?php if (!$profile) { ?>
        <div class="text-center py-5">
            <div class="mb-3" style="font-size:3rem;color:#dc3545;"><i class="fas fa-circle-exclamation" aria-hidden="true"></i></div>
            <div class="fw-bold" style="font-size:1.5rem;">Teacher Not Found</div>
            <p class="text-muted mb-4">The teacher profile you’re looking for might have been removed or is unavailable.</p>
            <a href="find-teachers.php" class="btn btn-primary">Back to Find Teachers</a>
        </div>
    <?php } else { ?>
        <div class="row g-4">
            <!-- Left: Profile card -->
            <div class="col-lg-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                        <img src="<?php echo tesc(!empty($profile['profile_picture']) ? $profile['profile_picture'] : 'admin/assets/images/no.png'); ?>" alt="Profile" class="img-fluid rounded mb-3" style="max-height:260px;object-fit:cover;">
                        <h4 class="fw-bold mb-1"><?php echo tesc($profile['full_name'] ?? ''); ?></h4>
                        <div class="text-muted small"><?php echo tesc($profile['subject'] ?? ''); ?></div>
                    </div>
                </div>
            </div>
            <!-- Right: Details card -->
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Profile Details</h5>
                        <!-- About/Bio displayed like a description section -->
                        <div class="mb-2 fw-bold text-dark">About Us</div>
                        <div style="white-space: pre-wrap; line-height:1.7; color:#243447;">
                            <?php echo nl2br(tesc($profile['bio'] ?? '')); ?>
                        </div>
                        <!-- Key facts -->
                        <div class="row g-3">
                            <div class="col-md-6"><strong>Experience:</strong> <?php echo (int)($profile['years_experience'] ?? 0); ?> years</div>
                            <div class="col-md-6"><strong>Expected Salary:</strong> ETB <?php echo number_format((float)($profile['expected_salary'] ?? 0), 2); ?></div>
                            <div class="col-md-6"><strong>Location:</strong> <?php echo tesc($profile['address'] ?? ''); ?></div>
                            <div class="col-md-6"><strong>Subject:</strong> <?php echo tesc($profile['subject'] ?? ''); ?></div>
                            <?php if (!empty($profile['cv'])) { ?>
                                <div class="col-12"><strong>CV:</strong> <a href="<?php echo tesc($profile['cv']); ?>" target="_blank" rel="noopener">View CV</a></div>
                            <?php } ?>
                        </div>
                        <!-- About this Teacher section -->
                        <div class="mt-4">
                            <h6 class="fw-bold mb-2">About this Teacher</h6>
                            <p class="mb-0 text-secondary">
                                <?php
                                $nm = tesc($profile['full_name'] ?? '');
                                $sub = tesc($profile['subject'] ?? '');
                                $exp = (int)($profile['years_experience'] ?? 0);
                                $loc = tesc($profile['address'] ?? '');
                                $salVal = (float)($profile['expected_salary'] ?? 0);
                                $salText = $salVal > 0 ? (' with an expected salary of ETB ' . number_format($salVal, 2)) : '';
                                $parts = [];
                                if ($sub !== '') {
                                    $parts[] = $sub . ' teacher';
                                }
                                if ($loc !== '') {
                                    $parts[] = 'based in ' . $loc;
                                }
                                $desc = !empty($parts) ? implode(' • ', $parts) : 'educator';
                                echo ($nm !== '' ? $nm : 'This teacher') . ' is a ' . $exp . '-year experienced ' . $desc . $salText . '.';
                                ?>
                            </p>
                        </div>
                        <div class="alert alert-danger mt-4 mb-0">To get contact details, please contact admin using <strong>Send Request</strong>.</div>
                    </div>
                </div>
            </div>
        </div>
</div>
<?php } ?>

<!-- Teacher Request Modal (reused) -->
<div class="modal fade" id="teacherRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" onsubmit="var ok=true; var n=this.sender_name; var p=this.sender_contact; if(n.value.trim()===''){ n.classList.add('is-invalid'); ok=false; } if(p.value.trim()===''){ p.classList.add('is-invalid'); ok=false; } return ok;">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">Send Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="teacher_send_request" value="1">
                    <input type="hidden" name="teacher_name" id="tr-teacher-name" value="">
                    <input type="hidden" name="teacher_id" id="tr-teacher-id" value="0">
                    <div class="mb-3">
                        <label class="form-label">Teacher</label>
                        <input type="text" class="form-control" id="tr-teacher-view" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Your Name <span class="text-danger">*</span></label>
                        <input type="text" name="sender_name" class="form-control" required oninput="this.classList.remove('is-invalid')">
                        <div class="invalid-feedback">Please enter your name.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone <span class="text-danger">*</span></label>
                        <input type="text" name="sender_contact" class="form-control" placeholder="e.g. +251900000000" required oninput="this.classList.remove('is-invalid')">
                        <div class="invalid-feedback">Please enter your phone number.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="sender_email" class="form-control" placeholder="you@example.com">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message (optional)</label>
                        <textarea name="message" class="form-control" rows="3" placeholder="Short message (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-gradient">Send Request</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function showTeacherRequest(name, uid) {
        document.getElementById('tr-teacher-name').value = name || '';
        document.getElementById('tr-teacher-view').value = name || '';
        var idEl = document.getElementById('tr-teacher-id');
        if (idEl) idEl.value = (uid || <?php echo (int)$uid; ?>);
        var modalEl = document.getElementById('teacherRequestModal');
        var m = new bootstrap.Modal(modalEl);
        m.show();
    }
</script>

<?php include 'Home/Homefooter.php'; ?>