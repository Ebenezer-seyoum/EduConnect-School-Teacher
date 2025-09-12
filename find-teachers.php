<?php
include 'Home/Homeheader.php';

// Handle teacher contact request (reuse notifications table; we don't have a teacher->admin owner, so notify all admins or a fixed admin)
$t_req_success = $t_req_error = '';
// Teacher quick request (mirrors vacancy request style: minimal info, optional anonymous)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teacher_send_request']) && (int)$_POST['teacher_send_request'] === 1) {
    $adminUids = function_exists('getAdminUids') ? getAdminUids() : [1];
    $teacherName = trim($_POST['teacher_name'] ?? '');
    $teacherId = (int)($_POST['teacher_id'] ?? 0);
    $sender_name = trim($_POST['sender_name'] ?? 'Anonymous');
    $sender_contact = trim($_POST['sender_contact'] ?? '');
    $sender_email = trim($_POST['sender_email'] ?? '');
    $baseMsg = 'Request to contact teacher';
    $message = trim($_POST['message'] ?? ($baseMsg . ($teacherName ? ': ' . $teacherName : '')));
    $anyOk = false;
    foreach ($adminUids as $auid) {
        $contactCombined = $sender_contact . ($sender_email !== '' ? (' | ' . $sender_email) : '');
        if (addNotification((int)$auid, 0, $sender_name, $contactCombined, $message, $teacherId > 0 ? $teacherId : null)) {
            $anyOk = true;
        }
    }
    $t_req_success = $anyOk ? 'Request sent to admin(s).' : 'Failed to send request.';
}

// Fetch teacher profiles (define a shim if helper doesn't exist)
if (!function_exists('getTeacherProfiles')) {
    function getTeacherProfiles()
    {
        global $conn;
        $rows = [];
        if (isset($conn)) {
            $res = mysqli_query($conn, "SELECT tid, user_id, full_name, subject, profile_picture, bio, years_experience, expected_salary, address, cv FROM teacher_profiles ORDER BY user_id DESC LIMIT 100");
            if ($res) {
                while ($r = mysqli_fetch_assoc($res)) {
                    $rows[] = $r;
                }
            }
        }
        return $rows;
    }
}
$teachers = getTeacherProfiles();
function esc($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
// Build filter lists similar to School Vacancies page
$subjects = [];
$locations = [];
foreach ($teachers as $tp) {
    $subj = trim((string)($tp['subject'] ?? ''));
    if ($subj !== '' && !in_array($subj, $subjects, true)) $subjects[] = $subj;
    $loc = trim((string)($tp['address'] ?? ''));
    if ($loc !== '' && !in_array($loc, $locations, true)) $locations[] = $loc;
}
sort($subjects);
sort($locations);
$salaryRanges = ['0-5000', '5001-10000', '10001-20000', '20001+'];
$experienceLevels = ['0-1', '1-3', '3-5', '5+'];
?>
<style>
    /* Modern teacher card layout (aligned with vacancy style) */
    .teacher-card .teacher-photo {
        height: 220px;
        object-fit: cover;
    }

    @media (min-width: 992px) {
        .teacher-card .teacher-photo {
            height: 240px;
        }
    }

    .teacher-card .card-title {
        font-weight: 600;
        color: #28395a;
    }

    .teacher-meta li {
        margin-bottom: .15rem;
    }

    .teacher-meta strong {
        color: #223;
        font-weight: 600;
    }

    .line-clamp-3 {
        display: -webkit-box;
        line-clamp: 3;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .btn-gradient {
        background: linear-gradient(90deg, #2575fc, #6a11cb);
        color: #fff;
        border: none;
    }

    .btn-gradient:hover {
        filter: brightness(.9);
        color: #fff;
    }

    .teacher-card {
        border: 1px solid #07326d;
        transition: all .2s;
    }

    .teacher-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(7, 50, 109, 0.15);
    }

    .posted-time-badge {
        background: #eef3ff;
        color: #294169;
        font-size: .6rem;
        font-weight: 600;
        padding: .35rem .55rem;
        border-radius: 30px;
        border: 1px solid #d6e3ff;
        white-space: nowrap;
    }

    .hover-raise {
        transition: none;
    }

    .hover-raise:hover {
        transform: none;
        box-shadow: inherit !important;
    }

    .btn-gradient {
        background: linear-gradient(90deg, #2575fc, #6a11cb);
        color: #fff;
        border: none;
    }

    /* Custom searchable selects (shared with School Vacancies) */
    .custom-select {
        position: relative;
    }

    .custom-select .custom-select-toggle {
        cursor: pointer;
        background-image: none !important;
        /* remove native arrow from form-select */
        -webkit-appearance: none;
        appearance: none;
        padding-right: 2.25rem;
        /* space for our chevron */
    }

    .custom-select .fa-chevron-down {
        transition: transform .15s ease;
    }

    .custom-select.open .fa-chevron-down {
        transform: rotate(90deg);
    }

    .custom-select-menu {
        position: absolute;
        z-index: 1000;
        background: #fff;
        width: 100%;
        max-height: 240px;
        overflow: auto;
        border: 1px solid #dee2e6;
        border-radius: .25rem;
    }
</style>
<!-- Header Start: Find Teachers Page -->
<div class="container-fluid position-relative d-flex align-items-center justify-content-center"
    style="min-height: 150px; overflow: hidden; background-color: #061343ff; border-radius: 0 50px 0 0;">

    <svg class="position-absolute top-0 end-0" width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" style="z-index:1;">
        <path d="M0,0 C100,50 150,150 200,200 L200,0 Z" fill="#ffffff10" />
    </svg>

    <div class="position-relative text-center" style="z-index: 2; max-width: 500px;">
        <h1 class="text-white fw-bold mb-2 animate__animated animate__fadeInDown"
            style="font-size:2rem; text-shadow: 0 2px 6px rgba(0,0,0,0.3);">
            <i class="fas fa-user-graduate me-2 text-warning"></i> Find Teachers
        </h1>
        <p class="text-white-50 mb-0 animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 1rem;">
            Search for qualified educators and connect with them directly
        </p>
    </div>
</div>

<main>
    <div class="container mt-3">
        <div class="d-flex justify-content-end">
            <div class="input-group" style="min-width:320px; max-width:420px;">
                <input id="global-search" type="text" class="form-control" placeholder="Search subject, school or location...">
                <button class="btn btn-warning" type="button" id="global-search-btn" aria-label="Search">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="job-listing-area pt-120 pb-120">
        <div class="container">
            <div class="row">
                <!-- Sidebar Filters (custom searchable dropdowns like School Vacancies) -->
                <div class="col-lg-3 mb-4" style="border-right:4px solid #07326d;">
                    <div class="p-3 shadow-sm" style="position: sticky; top: 1rem;">
                        <h5 class="fw-bold mb-3">Filters</h5>
                        <?php
                        $filterItems = [
                            'Subject' => ['id' => 'subject', 'data' => $subjects],
                            'Location' => ['id' => 'location', 'data' => $locations],
                            'Experience' => ['id' => 'exp', 'data' => $experienceLevels],
                            'Salary' => ['id' => 'salary', 'data' => $salaryRanges]
                        ];
                        foreach ($filterItems as $label => $info) {
                            $lower = strtolower($label);
                            $selectId = 'filter-' . $info['id'];
                        ?>
                            <div class="mb-3 position-relative">
                                <label class="small fw-semibold"><?php echo $label; ?></label>
                                <div class="custom-select" data-select-id="<?php echo $selectId; ?>">
                                    <button type="button" class="form-select form-select-sm d-flex justify-content-between align-items-center custom-select-toggle">
                                        <span class="custom-select-label">All <?php echo $lower; ?></span>
                                        <i class="fas fa-chevron-down ms-2"></i>
                                    </button>
                                    <div class="custom-select-menu shadow" style="display:none;">
                                        <div class="p-2 border-bottom bg-white sticky-top">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white"><i class="fas fa-search"></i></span>
                                                <input type="text" class="form-control custom-select-search" placeholder="Search <?php echo $lower; ?>">
                                            </div>
                                        </div>
                                        <ul class="list-group list-group-flush custom-select-options">
                                            <li class="list-group-item list-group-item-action" data-value="">All <?php echo $lower; ?></li>
                                            <?php foreach ($info['data'] as $d) {
                                                $val = htmlspecialchars($d); ?>
                                                <li class="list-group-item list-group-item-action" data-value="<?php echo $val; ?>"><?php echo $val; ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <select id="<?php echo $selectId; ?>" class="d-none">
                                        <option value="">All <?php echo $lower; ?></option>
                                        <?php foreach ($info['data'] as $d) { ?>
                                            <option value="<?php echo htmlspecialchars($d); ?>"><?php echo htmlspecialchars($d); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="d-grid gap-2">
                            <button class="btn btn-gradient btn-sm" id="apply-filter"><i class="fas fa-filter me-1"></i>Apply</button>
                            <button class="btn btn-light btn-sm" id="clear-filter">Reset</button>
                        </div>
                        <div class="small text-muted mt-3" id="visible-count"></div>
                    </div>
                </div>
                <!-- Teacher Grid -->
                <div class="col-lg-9">
                    <div class="row g-3" id="teacher-grid">
                        <div class="row g-3" id="vacancy-list">
                            <div id="no-results" class="col-12" style="display:none;">
                                <div class="text-center py-5">
                                    <div class="mb-2" style="font-size:6rem;color:#dc3545;">
                                        <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                                    </div>
                                    <div class="fw-bold" style="font-size:2.5rem;color:#dc3545;">No results match your filters</div>
                                    <div class="text-bold mt-1">Adjust or clear your filters to see available teachers.</div>
                                </div>
                            </div>
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
                                                }).then(function() {
                                                    window.location.href = window.location.pathname;
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
                            <?php if (empty($teachers)) { ?>
                                <div class="col-12 text-center py-5">
                                    <div class="mb-2" style="font-size:3rem;color:#0d6efd;"><i class="fas fa-circle-info" aria-hidden="true"></i></div>
                                    <div class="fw-bold" style="font-size:1.5rem;">No teachers available</div>
                                    <div class="text-muted mt-1">Please check back later.</div>
                                </div>
                                <?php } else {
                                foreach ($teachers as $t) {
                                    $pp = !empty($t['profile_picture']) ? esc($t['profile_picture']) : 'admin/assets/images/no.png';
                                    $name = esc($t['full_name'] ?? 'Unnamed');
                                    $addr = esc($t['address'] ?? '');
                                    $expY = (int)($t['years_experience'] ?? 0);
                                    $salaryRaw = $t['expected_salary'] ?? 0;
                                    $salaryNum = (float)preg_replace('/[^0-9.]/', '', (string)$salaryRaw);
                                    $salary = esc((string)$salaryRaw);
                                    $bio = esc($t['bio'] ?? '');
                                    $subject = esc($t['subject'] ?? '');
                                    $details = htmlspecialchars(json_encode([
                                        'name' => $t['full_name'] ?? '',
                                        'subject' => $t['subject'] ?? '',
                                        'location' => $t['address'] ?? '',
                                        'exp' => (int)($t['years_experience'] ?? 0),
                                        'salary' => (float)$salaryNum
                                    ]), ENT_QUOTES, 'UTF-8');
                                    $uid = (int)($t['tid'] ?? ($t['user_id'] ?? 0));
                                ?>
                                    <div class="col-12 col-md-6 teacher-item" data-details="<?php echo $details; ?>">
                                        <div class="card h-100 shadow-sm teacher-card overflow-hidden"
                                            style="border-radius:12px; transition:all .3s; border:2px solid #07326d;">

                                            <!-- Photo (taller height) -->
                                            <div class="teacher-photo-wrap bg-light">
                                                <img src="<?php echo $pp; ?>" alt="<?php echo $name; ?>"
                                                    class="teacher-photo w-100 h-100 object-fit-cover" />
                                            </div>

                                            <!-- Name + Subject -->
                                            <div class="p-3 text-center bg-primary text-white">
                                                <h3 class="fw-bold mb-2 text-white"
                                                    style="font-size:1.6rem; letter-spacing:0.8px; margin:0;">
                                                    <?php echo $name; ?>
                                                </h3>
                                                <div class="fw-semibold text-white"
                                                    style="font-size:1.1rem; opacity:0.95; letter-spacing:0.5px;">
                                                    <i class="fas fa-book-open me-1"></i>
                                                    <?php echo $subject ?: '—'; ?> Teacher
                                                </div>
                                            </div>

                                            <!-- Buttons -->
                                            <div class="card-body d-flex flex-column p-3">
                                                <div class="mt-auto d-flex gap-2">
                                                    <button class="btn btn-outline-primary btn-sm flex-fill read-more-btn"
                                                        onclick="loadTeacherDetail(<?php echo (int)$t['tid']; ?>)">Read More</button>
                                                    <button type="button"
                                                        class="btn btn-gradient btn-sm flex-fill"
                                                        onclick="showTeacherRequest('<?php echo addslashes($name); ?>', <?php echo (int)$t['tid']; ?>)">
                                                        Request
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <style>
                                        .teacher-card:hover {
                                            transform: translateY(-4px);
                                            box-shadow: 0 8px 28px rgba(7, 50, 109, 0.25);
                                        }

                                        .teacher-photo-wrap {
                                            height: 280px;
                                            /* taller photo */
                                            overflow: hidden;
                                        }

                                        .teacher-photo {
                                            transition: transform .3s ease;
                                        }

                                        .teacher-card:hover .teacher-photo {
                                            transform: scale(1.05);
                                        }

                                        .btn-gradient {
                                            background: linear-gradient(90deg, #2575fc, #6a11cb);
                                            color: #fff;
                                            border: none;
                                        }
                                    </style>


                            <?php }
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</main>

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
    // Teacher filtering (mirrors School Vacancies custom-select approach)
    function applyFilters() {
        const subj = (document.getElementById('filter-subject')?.value || '').toLowerCase();
        const loc = (document.getElementById('filter-location')?.value || '').toLowerCase();
        const exp = (document.getElementById('filter-exp')?.value || '');
        const sal = (document.getElementById('filter-salary')?.value || '');
        const q = ((document.getElementById('global-search')?.value || '')).trim().toLowerCase();
        const items = document.querySelectorAll('#teacher-grid .teacher-item');
        let shown = 0;
        items.forEach(item => {
            const d = JSON.parse(item.dataset.details || '{}');
            let ok = true;
            const hay = ((d.name || '') + ' ' + (d.subject || '') + ' ' + (d.location || '')).toLowerCase();
            if (q && !hay.includes(q)) ok = false;
            if (subj && !(d.subject || '').toLowerCase().includes(subj)) ok = false;
            if (loc && !(d.location || '').toLowerCase().includes(loc)) ok = false;
            if (exp) {
                const years = parseInt(d.exp || 0, 10);
                if (exp.includes('+')) {
                    if (years < parseInt(exp)) ok = false;
                } else {
                    const parts = exp.split('-');
                    if (parts.length === 2) {
                        if (years < parseInt(parts[0]) || years > parseInt(parts[1])) ok = false;
                    }
                }
            }
            if (sal) {
                const sval = parseFloat(d.salary || 0);
                if (sal.includes('+')) {
                    if (sval < parseInt(sal)) ok = false;
                } else {
                    const p = sal.split('-');
                    if (p.length === 2) {
                        if (sval < parseInt(p[0]) || sval > parseInt(p[1])) ok = false;
                    }
                }
            }
            item.style.display = ok ? 'block' : 'none';
            if (ok) shown++;
        });
        const total = items.length;
        const vc = document.getElementById('visible-count');
        if (vc) vc.textContent = shown + ' / ' + total + ' shown';
        var noRes = document.getElementById('no-results');
        if (noRes) noRes.style.display = (shown === 0 && total > 0) ? 'block' : 'none';
    }

    // Custom selects
    function closeAllCustomSelects(except) {
        document.querySelectorAll('.custom-select-menu').forEach(menu => {
            if (except && except === menu) return;
            menu.style.display = 'none';
            var cs = menu.closest('.custom-select');
            if (cs) cs.classList.remove('open');
        });
    }

    function customSelectSyncLabels() {
        document.querySelectorAll('.custom-select').forEach(cs => {
            const selId = cs.getAttribute('data-select-id');
            const sel = document.getElementById(selId);
            const labelEl = cs.querySelector('.custom-select-label');
            if (!sel || !labelEl) return;
            const base = (labelEl.dataset.base || labelEl.textContent || 'All');
            const text = sel.value ? sel.value : base;
            labelEl.textContent = text;
        });
    }
    (function initCustomSelects() {
        document.querySelectorAll('.custom-select').forEach(cs => {
            const selId = cs.getAttribute('data-select-id');
            const sel = document.getElementById(selId);
            const toggle = cs.querySelector('.custom-select-toggle');
            const menu = cs.querySelector('.custom-select-menu');
            const search = cs.querySelector('.custom-select-search');
            const options = cs.querySelectorAll('.custom-select-options .list-group-item');
            const labelEl = cs.querySelector('.custom-select-label');
            if (labelEl && !labelEl.dataset.base) {
                labelEl.dataset.base = labelEl.textContent;
            }
            toggle.addEventListener('click', function(e) {
                e.stopPropagation();
                const currentlyOpen = menu.style.display !== 'none';
                closeAllCustomSelects();
                menu.style.display = currentlyOpen ? 'none' : 'block';
                if (!currentlyOpen) {
                    cs.classList.add('open');
                } else {
                    cs.classList.remove('open');
                }
                if (!currentlyOpen && search) {
                    search.value = '';
                    search.focus();
                    filterList('');
                }
            });
            options.forEach(li => {
                li.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const val = this.getAttribute('data-value');
                    if (sel) {
                        sel.value = val;
                        const evt = new Event('change');
                        sel.dispatchEvent(evt);
                    }
                    if (labelEl) labelEl.textContent = (val === '') ? labelEl.dataset.base : this.textContent;
                    menu.style.display = 'none';
                    cs.classList.remove('open');
                    // Do not auto-apply; user will click Apply
                });
            });

            function filterList(q) {
                const query = (q || '').toLowerCase();
                options.forEach(li => {
                    const txt = (li.textContent || '').toLowerCase();
                    li.style.display = txt.includes(query) ? '' : 'none';
                });
            }
            if (search) {
                search.addEventListener('input', function() {
                    filterList(this.value);
                });
            }
        });
        document.addEventListener('click', function() {
            closeAllCustomSelects();
        });
    })();

    document.getElementById('apply-filter').addEventListener('click', applyFilters);
    document.getElementById('global-search-btn').addEventListener('click', applyFilters);
    document.getElementById('clear-filter').addEventListener('click', () => {
        document.querySelectorAll('select').forEach(s => {
            s.selectedIndex = 0;
            const evt = new Event('change');
            s.dispatchEvent(evt);
        });
        const gs = document.getElementById('global-search');
        if (gs) gs.value = '';
        customSelectSyncLabels();
        applyFilters();
    });
    // No auto-apply on input/change; only Apply/Search buttons trigger filtering

    function loadTeacherDetail(id) {
        fetch('teacher_detail.php?tid=' + encodeURIComponent(id) + '&modal=1')
            .then(r => r.text())
            .then(html => {
                const container = document.createElement('div');
                container.innerHTML = html;
                const modalEl = container.querySelector('.modal');
                if (!modalEl) {
                    throw new Error('Modal markup not found');
                }
                document.body.appendChild(container);
                const m = new bootstrap.Modal(modalEl);
                m.show();
                modalEl.addEventListener('hidden.bs.modal', () => container.remove());
            })
            .catch(() => alert('Failed to load details.'));
    }

    function showTeacherRequest(name, uid) {
        document.getElementById('tr-teacher-name').value = name || '';
        document.getElementById('tr-teacher-view').value = name || '';
        var idEl = document.getElementById('tr-teacher-id');
        if (idEl) idEl.value = (uid || 0);
        var modalEl = document.getElementById('teacherRequestModal');
        var m = new bootstrap.Modal(modalEl);
        m.show();
    }
</script>

<?php
include 'Home/Homefooter.php';
?>