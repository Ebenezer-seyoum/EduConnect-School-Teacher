<?php
include 'Home/Homeheader.php';

// Handle teacher contact request (reuse notifications table; we don't have a teacher->admin owner, so notify all admins or a fixed admin)
$t_req_success = $t_req_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teacher_send_request']) && (int)$_POST['teacher_send_request'] === 1) {
    // notify all admins
    $adminUids = function_exists('getAdminUids') ? getAdminUids() : [1];
    $sender_name = trim($_POST['sender_name'] ?? '');
    $sender_contact = trim($_POST['sender_contact'] ?? '');
    $message = trim($_POST['message'] ?? 'I would like to contact this teacher.');
    $vacancyId = 0; // no vacancy context for teacher requests
    if ($sender_name === '' || $sender_contact === '') {
        $t_req_error = 'Please provide your name and contact.';
    } else {
        $anyOk = false;
        foreach ($adminUids as $auid) {
            if (addNotification((int)$auid, $vacancyId, $sender_name, $sender_contact, $message)) {
                $anyOk = true;
            }
        }
        $t_req_success = $anyOk ? 'Request sent to admin(s).' : 'Failed to send request.';
    }
}

// Fetch teacher profiles
$teachers = [];
if (function_exists('getTeacherProfiles')) {
    $teachers = getTeacherProfiles();
} else {
    // Fallback: query manually
    if (isset($conn)) {
        $res = mysqli_query($conn, "SELECT user_id, full_name, profile_picture, bio, years_experience, expected_salary, address, cv FROM teacher_profiles ORDER BY user_id DESC LIMIT 100");
        if ($res) {
            while ($r = mysqli_fetch_assoc($res)) {
                $teachers[] = $r;
            }
        }
    }
}
function esc($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
?>
<style>
    /* Teacher card layout */
    .teacher-card .teacher-photo {
        height: 220px;
        object-fit: cover;
    }

    .teacher-card .card-title {
        font-weight: 700;
        color: #28395a;
    }

    .teacher-card .card-subtitle {
        color: #6c757d;
    }

    .teacher-card .meta {
        color: #5b6279;
    }

    .line-clamp-3 {
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .hover-raise {
        transition: transform .25s ease, box-shadow .25s ease
    }

    .hover-raise:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px rgba(0, 0, 0, .12) !important
    }

    @media (min-width: 992px) {
        .teacher-card .teacher-photo {
            height: 240px;
        }
    }
</style>
<!-- Header Start: Find Teachers Page -->
<div class="container-fluid position-relative d-flex align-items-center justify-content-center" 
     style="min-height: 150px; overflow: hidden; background-color: #061343ff; border-radius: 0 50px 0 0;">
    
    <svg class="position-absolute top-0 end-0" width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" style="z-index:1;">
        <path d="M0,0 C100,50 150,150 200,200 L200,0 Z" fill="#ffffff10"/>
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
    <div class="slider-area ">
        <div class="single-slider section-overly slider-height2 d-flex align-items-center" style="background-image: url('Home/assets/img/hero/about.jpg');">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12">
                        <div class="hero-cap text-center">
                            <h2>Find Teachers</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="job-listing-area pt-120 pb-120">
        <div class="container">
            <div class="row">
                <div class="col-xl-12">
                    <!-- Teacher List Start -->
                    <section class="featured-job-area">
                        <div class="container">
                            <div class="row">
                                <?php if (!empty($t_req_success)) { ?>
                                    <div class="alert alert-success"><?php echo esc($t_req_success); ?></div>
                                <?php } elseif (!empty($t_req_error)) { ?>
                                    <div class="alert alert-danger"><?php echo esc($t_req_error); ?></div>
                                <?php } ?>

                                <?php if (empty($teachers)) { ?>
                                    <div class="col-lg-12">
                                        <div class="alert alert-info">No teachers available.</div>
                                    </div>
                                    <?php } else {
                                    foreach ($teachers as $t) {
                                        $pp = !empty($t['profile_picture']) ? esc($t['profile_picture']) : 'admin/assets/images/no.png';
                                        $name = esc($t['full_name'] ?? 'Unnamed');
                                        $addr = esc($t['address'] ?? '');
                                        $expY = (int)($t['years_experience'] ?? 0);
                                        $salary = esc($t['expected_salary'] ?? '');
                                        $bio = esc($t['bio'] ?? '');
                                    ?>
                                        <div class="col-md-6 col-lg-4 mb-4">
                                            <div class="teacher-card card h-100 border-0 shadow-sm hover-raise">
                                                <img src="<?php echo $pp; ?>" class="card-img-top teacher-photo" alt="<?php echo $name; ?>">
                                                <div class="card-body d-flex flex-column">
                                                    <h5 class="card-title text-center mb-3"><?php echo $name; ?></h5>
                                                    <button type="button" class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#teacherDetailModal" onclick='showTeacherDetail(<?php echo json_encode([
                                                                                                                                                                                                    'name' => $t['full_name'] ?? '',
                                                                                                                                                                                                    'bio' => $t['bio'] ?? '',
                                                                                                                                                                                                    'exp' => (int)($t['years_experience'] ?? 0),
                                                                                                                                                                                                    'salary' => $t['expected_salary'] ?? '',
                                                                                                                                                                                                    'address' => $t['address'] ?? '',
                                                                                                                                                                                                    'pic' => $pp,
                                                                                                                                                                                                ]); ?>)'>Details</button>
                                                </div>
                                            </div>
                                        </div>
                                <?php }
                                } ?>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</main>
<!-- Teacher Detail Modal (polished) -->
<div class="modal fade" id="teacherDetailModal" tabindex="-1" aria-labelledby="teacherDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="teacherDetailModalLabel">Teacher Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4 text-center">
                        <img id="td-pic" src="" alt="Profile" class="img-fluid rounded" style="max-height: 220px; object-fit: cover;" />
                    </div>
                    <div class="col-md-8">
                        <h4 id="td-name" class="mb-2"></h4>
                        <div class="mb-2 text-muted"><i class="ti-location-pin"></i> <span id="td-address"></span></div>
                        <p id="td-bio" class="mb-2"></p>
                        <div class="d-flex flex-wrap gap-3">
                            <div><strong>Experience:</strong> <span id="td-exp"></span> years</div>
                            <div><strong>Expected Salary:</strong> <span id="td-salary"></span></div>
                        </div>
                        <div class="alert alert-warning mt-3 mb-0">Contact information is hidden. Use "Send Request" to notify the admin.</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
    function showTeacherDetail(data) {
        document.getElementById('td-name').innerText = data.name || '';
        document.getElementById('td-bio').innerText = data.bio || '';
        document.getElementById('td-exp').innerText = data.exp || 0;
        document.getElementById('td-salary').innerText = data.salary || '';
        document.getElementById('td-address').innerText = data.address || '';
        document.getElementById('td-pic').src = data.pic || '';
    }

    function openTeacherRequest(teacher) {
        // Reuse footer modal but post back to this page
        var form = document.getElementById('sendRequestForm');
        if (form) {
            form.action = 'find-teachers.php';
            // switch hidden flags
            var sr = form.querySelector('input[name="send_request"]');
            var tr = form.querySelector('input[name="teacher_send_request"]');
            if (!tr) {
                tr = document.createElement('input');
                tr.type = 'hidden';
                tr.name = 'teacher_send_request';
                tr.value = '1';
                form.appendChild(tr);
            }
            if (sr) {
                sr.parentNode.removeChild(sr);
            }
            // Pre-fill a message with teacher context
            var msg = document.getElementById('message');
            if (msg) {
                var tname = teacher && teacher.name ? teacher.name : 'this teacher';
                var taddr = teacher && teacher.address ? (' at ' + teacher.address) : '';
                msg.value = 'I would like to get in touch with ' + tname + taddr + ' via the admin.';
            }
            // open the modal
            if (typeof $ !== 'undefined' && typeof $.fn.modal === 'function') {
                $('#sendRequestModal').modal('show');
            }
        }
    }
</script>

<?php
include 'Home/Homefooter.php';
?>