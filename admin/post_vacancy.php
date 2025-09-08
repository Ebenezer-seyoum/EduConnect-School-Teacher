<?php
// Ensure session exists (adminHeader.php normally starts it)
if (!isset($_SESSION)) {
    session_start();
}

// Initialize values & error messages like register.php
$title = $description = $salary = $location = $employment_type = $contact_email = $contact_phone = $contact_address = '';
$title_err = $description_err = $salary_err = $location_err = $employment_type_err = $contact_email_err = $contact_phone_err = $contact_address_err = '';
$allErr = $success = '';

// Allowed employment types for the select box
$employmentTypes = [
    'Full-time',
    'Part-time',
    'Contract',
    'Temporary',
    'Internship',
    'Other',
];

function pv_val($v)
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

if (isset($_POST['vacancy_form']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $test = true;

    // Title (School Name) - required
    if (empty($_POST['title'])) {
        $title_err = 'Please enter school name';
        $test = false;
    } else {
        $title = trim($_POST['title']);
    }

    // Description (required)
    if (empty($_POST['description'])) {
        $description_err = 'Please enter a description';
        $test = false;
    } else {
        $description = trim($_POST['description']);
    }

    // Salary (required) - use validateIdNumber as requested
    if (empty($_POST['salary'])) {
        $salary_err = 'Please enter salary';
        $test = false;
    } else {
        // allow users to type with commas; normalize before validation
        $salary = str_replace(',', '', trim($_POST['salary']));
        if (function_exists('validateIdNumber') && validateIdNumber($salary) == 0) {
            $salary_err = 'Please enter valid salary';
            $test = false;
        }
    }

    // Location (required)
    if (empty($_POST['location'])) {
        $location_err = 'Please enter location';
        $test = false;
    } else {
        $location = trim($_POST['location']);
    }

    // Employment type (required) - must be one of the allowed options
    if (empty($_POST['employment_type'])) {
        $employment_type_err = 'Please select employment type';
        $test = false;
    } else {
        $employment_type = trim($_POST['employment_type']);
        if (!in_array($employment_type, $employmentTypes, true)) {
            $employment_type_err = 'Please select a valid employment type';
            $test = false;
        }
    }

    // Contact email (required)
    if (empty($_POST['contact_email'])) {
        $contact_email_err = 'Please enter contact email';
        $test = false;
    } else {
        $contact_email = trim($_POST['contact_email']);
        if (function_exists('validateEmail') && !validateEmail($contact_email)) {
            $contact_email_err = 'Please enter a valid email address (example: user@domain.com)';
            $test = false;
        }
    }

    // Contact phone (required) - normalize to Ethiopian E.164 (+2519XXXXXXXX)
    if (empty($_POST['contact_phone'])) {
        $contact_phone_err = 'Please enter contact phone';
        $test = false;
    } else {
        $raw_phone = preg_replace('/\D+/', '', $_POST['contact_phone']);
        // Determine local 9-digit part
        $local9 = '';
        if (strpos($raw_phone, '251') === 0) {
            // number may be 2519XXXXXXXX or longer; take last 9
            $local9 = substr($raw_phone, -9);
        } elseif (strlen($raw_phone) === 10 && $raw_phone[0] === '0') {
            $local9 = substr($raw_phone, 1);
        } else {
            // assume already 9-digit local
            $local9 = $raw_phone;
        }
        $contact_phone = '+251' . $local9;
        if (function_exists('validatePhoneNumber') && validatePhoneNumber($contact_phone) == 0) {
            $contact_phone_err = 'Please enter valid phone number';
            $test = false;
        }
    }

    // Contact address (required)
    if (empty($_POST['contact_address'])) {
        $contact_address_err = 'Please enter contact address';
        $test = false;
    } else {
        $contact_address = trim($_POST['contact_address']);
    }

    // Must be logged in
    $created_by = isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : 0;
    if ($created_by <= 0) {
        $allErr = 'You must be logged in to post a vacancy';
        $test = false;
    }

    if ($test === true) {
        // Use helper function similar to register.php style
        if (function_exists('addVacancy') && addVacancy($title, $description, $salary, $location, $employment_type, $contact_email, $contact_phone, $contact_address, $created_by) == 1) {
            $success = 'Vacancy posted successfully';
            // Reset fields
            $title = $description = $salary = $location = $employment_type = $contact_email = $contact_phone = $contact_address = '';
        } else {
            $allErr = 'There was an error while saving the vacancy';
        }
    }
}
?>

<div class="mb-3">
    <h5 class="mb-0">Post Vacancy</h5>
    <small class="text-muted">Fill out the details to create a new school vacancy</small>
    <?php if (!empty($success)) { ?>
        <div class=" form-control bg-success"><?php echo $success; ?></div>
    <?php  } ?>
    <?php if (!empty($allErr)) { ?>
        <div class=" form-control bg-danger"><?php echo $allErr; ?></div>
    <?php  } ?>
</div>

<form method="post" action="index.php?page=vacancies&tab=post" class="row g-3">
    <input type="hidden" name="vacancy_form" value="1" />

    <div class="col-md-8">
        <label class="form-label">School Name <span class="text-danger">*</span></label>
        <input type="text" name="title" class="form-control" maxlength="190" placeholder="e.g., Abebe School" value="<?php echo pv_val($title); ?>" />
        <span class="text-danger"><?php echo $title_err; ?></span>
    </div>

    <div class="col-md-4">
        <label class="form-label">Employment Type <span class="text-danger">*</span></label>
        <select name="employment_type" class="form-select">
            <option value="">Select employment type</option>
            <?php foreach ($employmentTypes as $opt) { ?>
                <option value="<?php echo htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($employment_type === $opt) ? 'selected' : ''; ?>><?php echo htmlspecialchars($opt, ENT_QUOTES, 'UTF-8'); ?></option>
            <?php } ?>
        </select>
        <span class="text-danger"><?php echo $employment_type_err; ?></span>
    </div>

    <div class="col-12">
        <label class="form-label">Description <span class="text-danger">*</span></label>
        <textarea name="description" class="form-control" rows="6" placeholder="Write a clear description of the vacancy, requirements, and responsibilities."><?php echo pv_val($description); ?></textarea>
        <span class="text-danger"><?php echo $description_err; ?></span>
    </div>

    <div class="col-md-4">
        <label class="form-label">Salary (ETB / month) <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text">ETB</span>
            <input type="text" name="salary" class="form-control" placeholder="e.g., 15,000" value="<?php echo pv_val($salary); ?>" />
            <span class="input-group-text">/month</span>
        </div>
        <span class="text-danger"><?php echo $salary_err; ?></span>
    </div>

    <div class="col-md-4">
        <label class="form-label">Location <span class="text-danger">*</span></label>
        <input type="text" name="location" class="form-control" maxlength="190" placeholder="e.g., Addis Ababa, Bole" value="<?php echo pv_val($location); ?>" />
        <span class="text-danger"><?php echo $location_err; ?></span>
    </div>

    <div class="col-md-4">
        <label class="form-label">Contact Email <span class="text-danger">*</span></label>
        <input type="email" name="contact_email" class="form-control" maxlength="190" placeholder="e.g., hr@school.edu.et" value="<?php echo pv_val($contact_email); ?>" />
        <span class="text-danger"><?php echo $contact_email_err; ?></span>
    </div>

    <div class="col-md-6">
        <label class="form-label">Contact Phone <span class="text-danger">*</span></label>
        <div class="input-group">
            <span class="input-group-text" style="min-width: 86px;">
                <img src="https://flagcdn.com/w20/et.png" alt="ET" width="20" height="15" class="me-2" /> +251
            </span>
            <input type="text" name="contact_phone" class="form-control" maxlength="50" placeholder="e.g., 911234567" value="<?php echo pv_val($contact_phone); ?>" />
        </div>
        <span class="text-danger"><?php echo $contact_phone_err; ?></span>
    </div>

    <div class="col-md-6">
        <label class="form-label">Contact Address <span class="text-danger">*</span></label>
        <input type="text" name="contact_address" class="form-control" maxlength="255" placeholder="e.g., Bole, Woreda 03, House No. 123" value="<?php echo pv_val($contact_address); ?>" />
        <span class="text-danger"><?php echo $contact_address_err; ?></span>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary">Post Vacancy</button>
    </div>
    <!-- Removed edit, delete, and view buttons. Use sidebar for navigation. -->
</form>