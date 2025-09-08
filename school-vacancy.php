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

$salaryRanges = ['0-5000', '5001-10000', '10001-20000', '20001+']; // Example ranges
$experienceLevels = ['0-1 year', '1-3 years', '3-5 years', '5+ years'];
?>

<main>
    <!-- Hero Banner -->
    <div class="slider-area">
        <div class="single-slider section-overly slider-height2 d-flex align-items-center" style="background-image: url('assets/img/hero/about.jpg');">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="hero-cap text-center">
                            <h2>School Vacancies</h2>
                        </div>
                    </div>
                </div>
            </div>
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
                            <label class="form-label">Location</label>
                            <select class="form-select" id="filter-location">
                                <option value="">All</option>
                                <?php foreach ($locations as $loc) { ?>
                                    <option value="<?php echo htmlspecialchars($loc); ?>"><?php echo htmlspecialchars($loc); ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Salary Filter -->
                        <div class="mb-3">
                            <label class="form-label">Salary Range (ETB)</label>
                            <select class="form-select" id="filter-salary">
                                <option value="">All</option>
                                <?php foreach ($salaryRanges as $range) { ?>
                                    <option value="<?php echo $range; ?>"><?php echo $range; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <!-- Experience Filter -->
                        <div class="mb-3">
                            <label class="form-label">Experience</label>
                            <select class="form-select" id="filter-experience">
                                <option value="">All</option>
                                <?php foreach ($experienceLevels as $exp) { ?>
                                    <option value="<?php echo $exp; ?>"><?php echo $exp; ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <button class="btn btn-primary w-100" id="apply-filter">Apply Filters</button>
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
                                <div class="col-12 vacancy-item" data-location="<?php echo htmlspecialchars($v['location']); ?>" data-salary="<?php echo htmlspecialchars($v['salary']); ?>" data-experience="<?php echo htmlspecialchars($v['experience'] ?? '0'); ?>">
                                    <div class="card shadow-sm rounded wow animate__animated animate__fadeInUp">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <h5 class="card-title"><?php echo htmlspecialchars($v['title']); ?></h5>
                                                    <p class="mb-1"><strong>Location:</strong> <?php echo htmlspecialchars($v['location']); ?></p>
                                                    <p class="mb-0"><strong>Salary:</strong> <?php echo htmlspecialchars($v['salary']); ?> ETB</p>
                                                    <p class="mb-0"><strong>Experience:</strong> <?php echo htmlspecialchars($v['experience'] ?? '0'); ?> years</p>
                                                </div>
                                                <span class="badge bg-info"><?php echo htmlspecialchars($v['employment_type']); ?></span>
                                            </div>

                                            <div class="mt-3 d-flex gap-2 flex-wrap">
                                                <button class="btn btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#detail-<?php echo (int)$v['sid']; ?>" aria-expanded="false" aria-controls="detail-<?php echo (int)$v['sid']; ?>">Details</button>
                                                <button class="btn btn-primary" onclick="handleSendRequest(<?php echo (int)$v['sid']; ?>)">Send Request</button>
                                            </div>

                                            <div class="collapse mt-3" id="detail-<?php echo (int)$v['sid']; ?>">
                                                <div class="card card-body bg-light">
                                                    <p><?php echo nl2br(htmlspecialchars($v['description'])); ?></p>
                                                    <div class="alert alert-warning mb-0">
                                                        Contact information is hidden. Click "Send Request" to notify the admin and get in touch.
                                                    </div>
                                                </div>
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
</style>

<!-- Filters Script -->
<script>
function handleSendRequest(vacancyId) {
    if(confirm('Send request to the admin for this vacancy?')) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `<input type="hidden" name="send_request" value="1">
                          <input type="hidden" name="vacancy_id" value="${vacancyId}">`;
        document.body.appendChild(form);
        form.submit();
    }
}

// Filter vacancies
document.getElementById('apply-filter').addEventListener('click', function(){
    let location = document.getElementById('filter-location').value;
    let salary = document.getElementById('filter-salary').value;
    let experience = document.getElementById('filter-experience').value;

    document.querySelectorAll('.vacancy-item').forEach(item => {
        let itemLoc = item.getAttribute('data-location');
        let itemSal = item.getAttribute('data-salary');
        let itemExp = item.getAttribute('data-experience');

        let salaryMatch = true;
        if(salary){
            let range = salary.split('-');
            if(range[1]){
                salaryMatch = parseInt(itemSal) >= parseInt(range[0]) && parseInt(itemSal) <= parseInt(range[1]);
            } else {
                salaryMatch = parseInt(itemSal) >= parseInt(range[0]);
            }
        }

        let locationMatch = !location || itemLoc === location;
        let experienceMatch = !experience || itemExp === experience.split(' ')[0];

        if(locationMatch && salaryMatch && experienceMatch){
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
});
</script>

<?php
include 'Home/Homefooter.php';
?>
