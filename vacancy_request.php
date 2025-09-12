<?php
// Return only modal markup for fetch() usage.
if (!function_exists('getVacancyById')) {
    require_once __DIR__ . '/connection/connection.php';
    require_once __DIR__ . '/connection/function.php';
}

$sid = (int)($_GET['sid'] ?? 0);
$v = $sid ? getVacancyById($sid) : null;

if (!$v) {
    echo '<div class="modal"><div class="modal-dialog"><div class="modal-content"><div class="modal-body">Vacancy not found.</div></div></div></div>';
    exit;
}

$position = htmlspecialchars(!empty($v['position']) ? $v['position'] : 'Teacher', ENT_QUOTES, 'UTF-8');
$schoolTitle = htmlspecialchars(!empty($v['title']) ? $v['title'] : 'School', ENT_QUOTES, 'UTF-8');
?>

<div class="modal fade" id="requestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" onsubmit="
                var ok=true;
                var n=this.sender_name; var p=this.sender_contact;
                if(n.value.trim()===''){ n.classList.add('is-invalid'); ok=false; }
                if(p.value.trim()===''){ p.classList.add('is-invalid'); ok=false; }
                return ok;">
                <div class="modal-header text-white" style="background-color:#0d47a1;">
                    <h5 class="modal-title text-white fw-bold">Send Request</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="send_request" value="1">
                    <input type="hidden" name="vacancy_id" value="<?php echo $sid; ?>">

                    <div class="mb-3">
                        <label class="form-label">School</label>
                        <input type="text" class="form-control" value="<?php echo $schoolTitle; ?>" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" class="form-control" value="<?php echo $position; ?> Teacher" readonly>
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
                        <div class="invalid-feedback">Please enter a valid email address.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Message (optional)</label>
                        <textarea name="message" class="form-control" rows="3" placeholder="Short message (optional)"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-gradient">Send Request</button>
                </div>
            </form>
        </div>
    </div>
</div>