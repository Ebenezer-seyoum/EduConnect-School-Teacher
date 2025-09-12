<?php
include 'Home/Homeheader.php';

// Fallback include in case helper functions are not loaded via Homeheader
if (!function_exists('getAdminUids')) {
    require_once __DIR__ . '/connection/connection.php';
    require_once __DIR__ . '/connection/function.php';
}

// Handle send request (POST)
$req_success = $req_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request']) && (int)$_POST['send_request'] === 1) {
    $vid = (int)($_POST['vacancy_id'] ?? 0);
    $v = $vid ? getVacancyById($vid) : null;
    if ($v) {
        $sender_name = trim($_POST['sender_name'] ?? '');
        $sender_contact = trim($_POST['sender_contact'] ?? '');
        $sender_email = trim($_POST['sender_email'] ?? '');
        $message = trim($_POST['message'] ?? '');

        // Basic server-side validation
        if ($sender_name === '' || $sender_contact === '') {
            $req_error = 'Please provide your name and phone number.';
        } else {
            $adminUids = getAdminUids();
            if (empty($adminUids) && !empty($v['created_by'])) {
                $adminUids = [(int)$v['created_by']];
            }
            $sent = 0;
            foreach ($adminUids as $adminUid) {
                // If no custom message, compose a clear summary including phone/email
                $msg = $message !== '' ? $message : (function () use ($v, $sender_name, $sender_contact, $sender_email) {
                    $pos = isset($v['position']) && $v['position'] !== '' ? ucfirst($v['position']) . ' Teacher' : 'Teacher';
                    $school = isset($v['title']) ? $v['title'] : 'School';
                    $parts = ["Request for $pos at $school by $sender_name"];
                    if ($sender_contact !== '') $parts[] = "Phone: $sender_contact";
                    if ($sender_email !== '') $parts[] = "Email: $sender_email";
                    return implode(' | ', $parts);
                })();
                $contactCombined = $sender_contact . ($sender_email !== '' ? (' | ' . $sender_email) : '');
                if (addNotification((int)$adminUid, $vid, $sender_name, $contactCombined, $msg)) {
                    $sent++;
                }
            }
            if ($sent > 0) {
                $req_success = 'Successfully sent request' . ($sent > 1 ? 's' : '') . '.';
            } else {
                $req_error = 'Failed to send request.';
            }
        }
    } else {
        $req_error = 'Vacancy not found.';
    }
}

$vacancies = getVacancies(50, null);

// Helper: human readable time ago
if (!function_exists('timeAgo')) {
    function timeAgo($datetime)
    {
        if (!$datetime) return '';
        $ts = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
        if (!$ts) return '';
        $diff = time() - $ts;
        if ($diff < 0) $diff = 0; // future safe
        if ($diff < 60) return 'Just now';
        $m = floor($diff / 60);
        if ($m < 60) return $m . ' min' . ($m == 1 ? '' : 's') . ' ago';
        $h = floor($diff / 3600);
        if ($h < 24) return $h . ' hour' . ($h == 1 ? '' : 's') . ' ago';
        $d = floor($diff / 86400);
        if ($d < 7) return $d . ' day' . ($d == 1 ? '' : 's') . ' ago';
        return date('Y-m-d', $ts);
    }
}

// Build unique locations safely
$locations = [];
if (!empty($vacancies)) {
    foreach ($vacancies as $vv) {
        $loc = trim((string)($vv['location'] ?? ''));
        if ($loc !== '' && !in_array($loc, $locations, true)) {
            $locations[] = $loc;
        }
    }
}

// Build subjects list from position column
$positions = [];
if (!empty($vacancies)) {
    foreach ($vacancies as $vv) {
        $pos = trim((string)($vv['position'] ?? 'Teacher'));
        if ($pos !== '' && !in_array($pos, $positions, true)) {
            $positions[] = $pos;
        }
    }
}

$salaryRanges = ['0-5000', '5001-10000', '10001-20000', '20001+']; // Example ranges
$experienceLevels = ['0-1', '1-3', '3-5', '5+'];
?>
<!-- Header Start: School Vacancy Page -->
<div class="container-fluid position-relative d-flex align-items-center justify-content-center"
    style="min-height: 150px; overflow: hidden; background-color: #061343ff; border-radius: 0 50px 0 0;">

    <svg class="position-absolute top-0 end-0" width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" style="z-index:1;">
        <path d="M0,0 C100,50 150,150 200,200 L200,0 Z" fill="#ffffff10" />
    </svg>

    <div class="position-relative text-center" style="z-index: 2; max-width: 500px;">
        <h1 class="text-white fw-bold mb-2 animate__animated animate__fadeInDown"
            style="font-size:2rem; text-shadow: 0 2px 6px rgba(0,0,0,0.3);">
            <i class="fas fa-school me-2 text-warning"></i> School Vacancies
        </h1>
        <p class="text-white-50 mb-0 animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 1rem;">
            Explore current job openings in schools near you
        </p>
    </div>
</div>
<!-- Search bar below header, aligned right -->
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

<div class="container my-4">
    <?php if (!empty($req_success) || !empty($req_error)) { ?>
        <script>
            (function() {
                function showAlert() {
                    <?php if (!empty($req_success)) { ?>
                        Swal.fire({
                            icon: 'success',
                            title: 'Request sent',
                            text: 'Thanks! Your request was sent successfully. An administrator will contact you soon.',
                            confirmButtonText: 'OK'
                        }).then(function() {
                            // Navigate to the same page via GET to avoid form re-submission
                            window.location.href = window.location.pathname;
                        });
                    <?php } else { ?>
                        Swal.fire({
                            icon: 'error',
                            title: 'Something went wrong',
                            text: <?php echo json_encode($req_error); ?>,
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
    <div class="row">
        <!-- Filter panel -->
        <aside class="col-lg-3 mb-4" style="border-right:4px solid #07326d;">
            <div class="p-3 shadow-sm" style="position: sticky; top: 1rem;">
                <h5 class="fw-bold mb-3">Filters</h5>

                <!-- Removed sidebar top search as requested -->

                <?php
                $filterItems = [
                    'Subject' => ['id' => 'subject', 'data' => $positions],
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
                            <!-- Hidden select to keep compatibility with filtering code -->
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
        </aside>

        <!-- Vacancies list -->
        <section class="col-lg-9">
            <div class="row g-3" id="vacancy-list">
                <div id="no-results" class="col-12" style="display:none;">
                    <div class="text-center py-5">
                        <div class="mb-2" style="font-size:6rem;color:#dc3545;">
                            <i class="fas fa-circle-exclamation" aria-hidden="true"></i>
                        </div>
                        <div class="fw-bold" style="font-size:2.5rem;color:#dc3545;">No results match your filters</div>
                        <div class="text-bold mt-1">Adjust or clear your filters to see available vacancies.</div>
                    </div>
                </div>
                <?php if (empty($vacancies)) { ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <div class="mb-2" style="font-size:3rem;color:#0d6efd;">
                                <i class="fas fa-circle-info" aria-hidden="true"></i>
                            </div>
                            <div class="fw-bold" style="font-size:1.5rem;">No vacancies posted yet</div>
                            <div class="text-muted mt-1">Please check back later for new opportunities.</div>
                        </div>
                    </div>
                    <?php } else {
                    foreach ($vacancies as $v) {
                        $logo = !empty($v['logo']) ? $v['logo'] : 'Home/assets/img/no.png';
                        $schoolName = !empty($v['title']) ? $v['title'] : 'School'; // title holds school name
                        $address = trim((string)($v['contact_address'] ?? ''));
                        $postedAgo = timeAgo($v['created_at'] ?? null);
                        $positionText = trim((string)($v['position'] ?? 'Teacher'));
                        $salaryAmt = (float)($v['salary'] ?? 0);
                        $numPositions = (int)($v['number_of_position'] ?? 1);
                        $data = htmlspecialchars(json_encode([
                            'sid' => $v['sid'],
                            'position' => $positionText,
                            'title' => $schoolName,
                            'school' => $schoolName,
                            'logo' => $logo,
                            'location' => $v['location'] ?? '',
                            'address' => $address,
                            'salary' => $salaryAmt,
                            'positions' => $numPositions,
                            'employment_type' => $v['employment_type'] ?? '',
                            'description' => $v['description'] ?? ''
                        ]), ENT_QUOTES, 'UTF-8');
                    ?>
                        <div class="col-12 col-md-6 vacancy-item" data-details="<?php echo $data; ?>">
                            <div class="card shadow-sm p-3 vacancy-card position-relative h-100" style="border:1px solid #07326d; transition:all .2s;">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h3 class="fw-bold mb-0" style="font-size:1.25rem;">
                                        <span style="font-size:1.9rem;">
                                            <i class="fas fa-chalkboard-teacher text-primary me-2"></i><?php echo htmlspecialchars(ucfirst($positionText)); ?> Teacher
                                        </span>
                                    </h3>
                                    <span class="text-danger small"><i class="far fa-clock me-1"></i><?php echo $postedAgo; ?></span>
                                </div>
                                <div class="mb-2" style="line-height:1.6;">
                                    <div class="mb-1"><i class="fas fa-school text-secondary me-2"></i><?php echo htmlspecialchars($schoolName); ?></div>
                                    <div class="mb-1"><i class="fas fa-map-marker-alt text-secondary me-2"></i><?php echo htmlspecialchars($v['location'] ?? ''); ?></div>
                                    <div class="mb-1"><i class="fas fa-briefcase text-secondary me-2"></i><?php echo htmlspecialchars($v['experience'] ?? 'Not specified'); ?> <strong>yrs</strong></div>
                                    <div class="mb-1"><i class="fas fa-users text-secondary me-2"></i><?php echo $numPositions; ?><strong>Positions</strong> </div>
                                    <div class="mb-1"><i class="fas fa-coins text-secondary me-2"></i><strong>Salary:</strong> ETB <?php echo number_format($salaryAmt, 2); ?></div>
                                    <?php $etype = trim((string)($v['employment_type'] ?? ''));
                                    if ($etype !== '') { ?>
                                        <div class="mb-1 text-center">
                                            <span class="d-inline-block px-2 py-1 rounded bg-success text-white" style="white-space: nowrap;">
                                                <i class="fas fa-id-badge me-1"></i><?php echo htmlspecialchars(ucfirst($etype)); ?>
                                            </span>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="d-flex gap-2 mt-auto">
                                    <button class="btn btn-outline-primary btn-sm flex-fill read-more-btn" onclick="loadVacancyDetail(<?php echo (int)$v['sid']; ?>)">Read More</button>
                                    <button class="btn btn-gradient btn-sm flex-fill request-btn" onclick="loadVacancyRequest(<?php echo (int)$v['sid']; ?>)">Request</button>
                                </div>
                            </div>
                        </div>
                <?php }
                } ?>
            </div>
        </section>
    </div>
</div>

<style>
    .btn-gradient {
        background: linear-gradient(90deg, #2575fc, #6a11cb);
        color: #fff;
        border: none;
    }

    .vacancy-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 28px rgba(7, 50, 109, 0.15);
    }

    /* Custom select with searchable dropdown */
    .custom-select {
        position: relative;
    }

    .custom-select-menu {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1050;
        background: #fff;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        max-height: 260px;
        overflow: auto;
        margin-top: 2px;
    }

    .custom-select-options .list-group-item {
        cursor: pointer;
    }

    .custom-select-options .list-group-item.active,
    .custom-select-options .list-group-item:hover {
        background: #f1f5ff;
    }

    /* Hide native arrow on the faux select and animate our chevron */
    .custom-select .custom-select-toggle {
        background-image: none !important;
        -webkit-appearance: none;
        appearance: none;
        padding-right: 2.25rem;
    }

    .custom-select .custom-select-toggle i {
        transition: transform .2s ease;
    }

    .custom-select.open .custom-select-toggle i {
        transform: rotate(90deg);
    }
</style>

<script>
    // AJAX fetch modals from other page
    function loadVacancyDetail(id) {
        fetch('vacancy_detail.php?sid=' + id)
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

    function loadVacancyRequest(id) {
        fetch('vacancy_request.php?sid=' + id)
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
            .catch(() => alert('Failed to load request form.'));
    }

    // Simple filter with live search
    function applyFilters() {
        const subj = (document.getElementById('filter-subject').value || '').toLowerCase();
        const loc = (document.getElementById('filter-location').value || '').toLowerCase();
        const exp = document.getElementById('filter-exp').value; // kept for future use
        const sal = document.getElementById('filter-salary').value;
        const q = ((document.getElementById('global-search').value || '')).trim().toLowerCase();
        const items = document.querySelectorAll('.vacancy-item');
        let shown = 0;
        items.forEach(item => {
            const d = JSON.parse(item.dataset.details || '{}');
            let ok = true;
            const hay = ((d.title || '') + ' ' + (d.position || '') + ' ' + (d.location || '')).toLowerCase();
            if (q && !hay.includes(q)) ok = false;
            if (subj && !(d.position || '').toLowerCase().includes(subj)) ok = false;
            if (loc && !(d.location || '').toLowerCase().includes(loc)) ok = false;
            if (sal) {
                if (sal.includes('+') && d.salary < parseInt(sal)) ok = false;
                else {
                    const parts = sal.split('-');
                    if (parts.length === 2 && (d.salary < parseInt(parts[0]) || d.salary > parseInt(parts[1]))) ok = false;
                }
            }
            item.style.display = ok ? 'block' : 'none';
            if (ok) shown++;
        });
        document.getElementById('visible-count').innerText = shown + ' / ' + items.length + ' shown';
        var noRes = document.getElementById('no-results');
        if (noRes) noRes.style.display = (shown === 0 && items.length > 0) ? 'block' : 'none';
    }

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
    // Auto-search on input/change
    ['global-search', 'filter-subject', 'filter-location', 'filter-exp', 'filter-salary'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        const evt = (el.tagName === 'SELECT') ? 'change' : 'input';
        el.addEventListener(evt, () => {
            // debounce minimal
            clearTimeout(window.__filterTimer);
            // No auto-apply on input/change; only Apply/Search buttons trigger filtering
        });
    });
    // Custom searchable select dropdowns
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
            const lower = (labelEl.textContent || '').toLowerCase();
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
            // Store base label
            const labelEl = cs.querySelector('.custom-select-label');
            if (labelEl && !labelEl.dataset.base) {
                labelEl.dataset.base = labelEl.textContent;
            }
            // Toggle open/close
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
            // Option click
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
            // Search filter
            function filterList(q) {
                const query = (q || '').toLowerCase();
                options.forEach(li => {
                    const txt = (li.textContent || '').toLowerCase();
                    li.style.display = txt.includes(query) ? '' : 'none';
                });
            }
            if (search) {
                search.addEventListener('input', function(e) {
                    filterList(this.value);
                });
            }
        });
        document.addEventListener('click', function() {
            closeAllCustomSelects();
        });
    })();
    applyFilters();
</script>

<?php
include 'Home/Homefooter.php';
?>