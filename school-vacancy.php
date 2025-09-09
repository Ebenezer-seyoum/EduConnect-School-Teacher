<?php
include 'Home/Homeheader.php';

// Handle send request (POST)
$req_success = $req_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request']) && (int)$_POST['send_request'] === 1) {
    $vid = (int)($_POST['vacancy_id'] ?? 0);
    $v = $vid ? getVacancyById($vid) : null;
    if ($v) {
        $sender_name = trim($_POST['sender_name'] ?? 'Anonymous');
        $sender_contact = trim($_POST['sender_contact'] ?? '');
        $message = trim($_POST['message'] ?? 'Request for contact/commission');
        $adminUid = (int)($v['created_by'] ?? 0);
        if ($adminUid > 0 && addNotification($adminUid, $vid, $sender_name, $sender_contact, $message)) {
            $req_success = 'Request sent to the admin.';
        } else {
            $req_error = 'Failed to send request.';
        }
    } else {
        $req_error = 'Vacancy not found.';
    }
}

$vacancies = getVacancies(50, null);

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

$salaryRanges = ['0-5000', '5001-10000', '10001-20000', '20001+']; // Example ranges
$experienceLevels = ['0-1', '1-3', '3-5', '5+'];
?>
<!-- Header Start: School Vacancy Page -->
<div class="container-fluid position-relative d-flex align-items-center justify-content-center" 
     style="min-height: 150px; overflow: hidden; background-color: #061343ff; border-radius: 0 50px 0 0;">
    
    <svg class="position-absolute top-0 end-0" width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" style="z-index:1;">
        <path d="M0,0 C100,50 150,150 200,200 L200,0 Z" fill="#ffffff10"/>
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



    <!-- Job Listing with Filter Sidebar -->
    <div class="job-listing-area pt-5 pb-5">
        <div class="container">
            <?php if (!empty($req_success)) { ?>
                <div class="alert alert-success wow animate__animated animate__fadeIn"><?php echo htmlspecialchars($req_success); ?></div>
            <?php } elseif (!empty($req_error)) { ?>
                <div class="alert alert-danger wow animate__animated animate__fadeIn"><?php echo htmlspecialchars($req_error); ?></div>
            <?php } ?>

            <div class="row">
                <!-- Filter Sidebar -->
                <div class="col-lg-3 mb-4">
                    <div class="card shadow-sm rounded p-3">
                        <h5 class="mb-3">Filter Vacancies</h5>

                        <!-- Location Filter -->
                        <div class="mb-3">
                            <label class="form-label d-block">Location</label>
                            <div class="filter-group scroll-area" id="filter-locations">
                                <?php foreach ($locations as $loc) {
                                    $id = 'loc_' . md5($loc); ?>
                                    <div class="form-check form-check-inline me-0 mb-2">
                                        <input class="form-check-input" type="checkbox" id="<?php echo $id; ?>" name="loc[]" value="<?php echo htmlspecialchars($loc); ?>">
                                        <label class="form-check-label badge rounded-pill bg-light text-dark border ms-1" for="<?php echo $id; ?>"><?php echo htmlspecialchars($loc); ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Salary Filter -->
                        <div class="mb-3">
                            <label class="form-label d-block">Salary (ETB)</label>
                            <div class="filter-group" id="filter-salaries">
                                <?php foreach ($salaryRanges as $range) {
                                    $id = 'sal_' . preg_replace('/[^0-9+]+/', '_', $range); ?>
                                    <div class="form-check form-check-inline me-0 mb-2">
                                        <input class="form-check-input" type="checkbox" id="<?php echo $id; ?>" name="salary[]" value="<?php echo $range; ?>">
                                        <label class="form-check-label badge rounded-pill bg-light text-dark border ms-1" for="<?php echo $id; ?>"><?php echo $range; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <!-- Experience Filter -->
                        <div class="mb-3">
                            <label class="form-label d-block">Experience (years)</label>
                            <div class="filter-group" id="filter-experiences">
                                <?php foreach ($experienceLevels as $exp) {
                                    $id = 'exp_' . preg_replace('/[^0-9+]+/', '_', $exp); ?>
                                    <div class="form-check form-check-inline me-0 mb-2">
                                        <input class="form-check-input" type="checkbox" id="<?php echo $id; ?>" name="exp[]" value="<?php echo $exp; ?>">
                                        <label class="form-check-label badge rounded-pill bg-light text-dark border ms-1" for="<?php echo $id; ?>"><?php echo $exp; ?></label>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary flex-fill" id="apply-filter">Apply</button>
                            <button class="btn btn-outline-secondary flex-fill" id="clear-filter">Clear</button>
                        </div>
                    </div>
                </div>

                <!-- Vacancy List -->
                <div class="col-lg-9">
                    <div class="row g-4" id="vacancy-list">
                        <?php if (empty($vacancies)) { ?>
                            <div class="col-12">
                                <div class="alert alert-info text-center">No vacancies available.</div>
                            </div>
                            <?php } else {
                            foreach ($vacancies as $v) { ?>
                                <div class="col-12 vacancy-item" data-location="<?php echo htmlspecialchars($v['location']); ?>" data-salary="<?php echo (int)($v['salary']); ?>" data-experience="<?php echo (int)($v['experience'] ?? 0); ?>">
                                    <div class="card shadow-sm rounded border-0 hover-raise wow animate__animated animate__fadeInUp">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="card-title mb-1"><?php echo htmlspecialchars($v['title']); ?></h5>
                                                    <div class="small text-muted mb-2"><i class="ti-location-pin"></i> <?php echo htmlspecialchars($v['location']); ?> • <strong>ETB</strong> <?php echo (int)$v['salary']; ?> • <?php echo (int)($v['experience'] ?? 0); ?> yrs</div>
                                                </div>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($v['employment_type']); ?></span>
                                            </div>
                                            <div class="mt-3 d-flex gap-2 flex-wrap">
                                                <button class="btn btn-outline-primary" type="button" data-bs-toggle="modal" data-bs-target="#vacancyDetailModal" onclick='showVacancyDetail(<?php echo json_encode([
                                                                                                                                                                                                    "title" => $v["title"],
                                                                                                                                                                                                    "location" => $v["location"],
                                                                                                                                                                                                    "salary" => (int)$v["salary"],
                                                                                                                                                                                                    "experience" => (int)($v["experience"] ?? 0),
                                                                                                                                                                                                    "employment_type" => $v["employment_type"],
                                                                                                                                                                                                    "description" => $v["description"],
                                                                                                                                                                                                ]); ?>)'>Details</button>
                                                <button class="btn btn-primary" onclick="handleSendRequest(<?php echo (int)$v['sid']; ?>)">Send Request</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php }
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Custom Styles -->
<style>
    .hover-raise {
        transition: transform .25s ease, box-shadow .25s ease
    }

    .hover-raise:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, .12) !important
    }

    .card-body h5 {
        font-weight: 600;
    }

    .card .badge {
        font-size: 0.9rem;
        padding: 0.5em 0.8em;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: #fff;
    }

    .btn-primary:hover {
        background-color: #004085;
        border-color: #004085;
    }

    @media (max-width: 992px) {
        .col-lg-3 {
            margin-bottom: 20px;
        }

        .d-flex.flex-wrap {
            justify-content: center;
        }
    }

    .filter-group {
        max-height: 180px;
        overflow: auto
    }

    .filter-group .form-check-input {
        display: none
    }

    .filter-group .form-check-label {
        cursor: pointer;
        padding: .35rem .7rem;
        border: 1px solid #dee2e6;
    }

    .filter-group .form-check-input:checked+.form-check-label {
        background: linear-gradient(90deg, #6a11cb, #2575fc);
        color: #fff;
        border-color: transparent
    }
</style>

<!-- Vacancy Detail Modal -->
<div class="modal fade" id="vacancyDetailModal" tabindex="-1" aria-labelledby="vacancyDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="vacancyDetailModalLabel">Vacancy Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h4 id="vd-title" class="mb-1"></h4>
                <div class="text-muted small mb-3"><i class="ti-location-pin"></i> <span id="vd-location"></span> • <strong>ETB</strong> <span id="vd-salary"></span> • <span id="vd-exp"></span> yrs • <span class="badge bg-info" id="vd-type"></span></div>
                <p id="vd-desc" class="mb-0"></p>
                <div class="alert alert-warning mt-3 mb-0">Contact information is hidden. Click "Send Request" to notify the admin and get in touch.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Filters Script -->
<script>
    function handleSendRequest(vacancyId) {
        if (confirm('Send request to the admin for this vacancy?')) {
            let form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `<input type="hidden" name="send_request" value="1">
                          <input type="hidden" name="vacancy_id" value="${vacancyId}">`;
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Modal populate
    function showVacancyDetail(data) {
        document.getElementById('vd-title').innerText = data.title || '';
        document.getElementById('vd-location').innerText = data.location || '';
        document.getElementById('vd-salary').innerText = data.salary || 0;
        document.getElementById('vd-exp').innerText = data.experience || 0;
        document.getElementById('vd-type').innerText = data.employment_type || '';
        document.getElementById('vd-desc').innerText = data.description || '';
    }

    function getCheckedValues(containerId) {
        return Array.from(document.querySelectorAll('#' + containerId + ' input[type="checkbox"]:checked')).map(el => el.value);
    }

    function inSalaryRanges(value, ranges) {
        if (!ranges.length) return true;
        let v = parseInt(value || 0, 10);
        return ranges.some(r => {
            let parts = r.split('-');
            if (parts.length === 2) {
                return v >= parseInt(parts[0], 10) && v <= parseInt(parts[1], 10);
            }
            return v >= parseInt(parts[0], 10);
        });
    }

    function inExperienceRanges(value, ranges) {
        if (!ranges.length) return true;
        let v = parseInt(value || 0, 10);
        return ranges.some(r => {
            if (r.includes('+')) {
                return v >= parseInt(r, 10);
            }
            let parts = r.split('-');
            if (parts.length === 2) {
                return v >= parseInt(parts[0], 10) && v <= parseInt(parts[1], 10);
            }
            return v === parseInt(parts[0], 10);
        });
    }

    function applyFilters() {
        const selLocs = getCheckedValues('filter-locations');
        const selSal = getCheckedValues('filter-salaries');
        const selExp = getCheckedValues('filter-experiences');
        document.querySelectorAll('.vacancy-item').forEach(item => {
            const loc = item.getAttribute('data-location');
            const sal = parseInt(item.getAttribute('data-salary') || 0, 10);
            const exp = parseInt(item.getAttribute('data-experience') || 0, 10);
            const locOk = selLocs.length ? selLocs.includes(loc) : true;
            const salOk = inSalaryRanges(sal, selSal);
            const expOk = inExperienceRanges(exp, selExp);
            item.style.display = (locOk && salOk && expOk) ? 'block' : 'none';
        });
    }

    document.getElementById('apply-filter').addEventListener('click', applyFilters);
    document.getElementById('clear-filter').addEventListener('click', function() {
        document.querySelectorAll('.filter-group input[type="checkbox"]').forEach(cb => {
            cb.checked = false;
        });
        applyFilters();
    });
</script>

<?php
include 'Home/Homefooter.php';
?>