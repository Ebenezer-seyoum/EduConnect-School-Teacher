<?php
$uid = (int)$_SESSION['uid'];
$profile = getTeacherProfileByUserId($uid);
if (!$profile) {
    echo '<div class="alert alert-info">No profile found. <a href="teacher.php?page=create_profile">Create one now</a>.</div>';
    return;
}

$err = [];
$ok = '';
function tpv($v)
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_profile']) && (int)$_POST['edit_profile'] === 1) {
    $full_name = trim($_POST['full_name'] ?? $profile['full_name']);
    $bio = trim($_POST['bio'] ?? $profile['bio']);
    $years_experience = (int)($_POST['years_experience'] ?? $profile['years_experience']);
    $expected_salary = trim($_POST['expected_salary'] ?? (string)$profile['expected_salary']);
    $address = trim($_POST['address'] ?? $profile['address']);
    $contact_email = trim($_POST['contact_email'] ?? $profile['contact_email']);
    $contact_phone = trim($_POST['contact_phone'] ?? $profile['contact_phone']);

    if ($full_name === '') $err['full_name'] = 'Full name is required';
    if ($contact_email !== '' && function_exists('validateEmail') && !validateEmail($contact_email)) $err['contact_email'] = 'Invalid email';
    if ($contact_phone !== '' && function_exists('validatePhoneNumber') && validatePhoneNumber($contact_phone) == 0) $err['contact_phone'] = 'Invalid phone';
    if ($expected_salary !== '' && !preg_match('/^\d+(\.\d{1,2})?$/', $expected_salary)) $err['expected_salary'] = 'Salary must be a number (max 2 decimals)';
    if ($years_experience < 0 || $years_experience > 60) $err['years_experience'] = 'Years of experience must be between 0 and 60';

    $updates = [
        'full_name' => $full_name,
        'bio' => $bio,
        'years_experience' => $years_experience,
        'expected_salary' => $expected_salary,
        'address' => $address,
        'contact_email' => $contact_email,
        'contact_phone' => $contact_phone,
    ];

    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
        $valid = validateProfilePicture($_FILES['profile_picture']);
        if ($valid === true) {
            $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
            $fileName = 'pp_' . $uid . '_' . time() . '.' . $ext;
            $dest = __DIR__ . '/assets/images/profile/' . $fileName;
            if (!is_dir(__DIR__ . '/assets/images/profile')) {
                @mkdir(__DIR__ . '/assets/images/profile', 0777, true);
            }
            if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $dest)) {
                $updates['profile_picture'] = 'teacher/assets/images/profile/' . $fileName;
            } else {
                $err['profile_picture'] = 'Upload failed';
            }
        } else {
            $err['profile_picture'] = $valid;
        }
    }
    if (isset($_FILES['cv']) && $_FILES['cv']['error'] === UPLOAD_ERR_OK) {
        $valid = validateUploadedFile($_FILES['cv'], ['application/pdf'], 5 * 1024 * 1024);
        if ($valid === true) {
            $ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
            $fileName = 'cv_' . $uid . '_' . time() . '.' . $ext;
            $dest = __DIR__ . '/assets/images/profile/' . $fileName;
            if (!is_dir(__DIR__ . '/assets/images/profile')) {
                @mkdir(__DIR__ . '/assets/images/profile', 0777, true);
            }
            if (move_uploaded_file($_FILES['cv']['tmp_name'], $dest)) {
                $updates['cv'] = 'teacher/assets/images/profile/' . $fileName;
            } else {
                $err['cv'] = 'CV upload failed';
            }
        } else {
            $err['cv'] = $valid;
        }
    }

    if (empty($err)) {
        if (updateTeacherProfile($uid, $updates)) {
            $ok = 'Profile updated successfully';
            $profile = getTeacherProfileByUserId($uid);
        } else {
            $ok = 'No changes or update failed';
        }
    }
}
?>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Edit Teacher Profile</h4>
        <?php if (!empty($ok)) { ?><div class="alert alert-success"><?php echo tpv($ok); ?></div><?php } ?>
        <form method="post" enctype="multipart/form-data" class="row g-3">
            <input type="hidden" name="edit_profile" value="1" />
            <div class="col-md-6">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="full_name" class="form-control" value="<?php echo tpv($profile['full_name']); ?>" required />
                <span class="text-danger"><?php echo tpv($err['full_name'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
                <label class="form-label">Years of Experience</label>
                <input type="number" name="years_experience" class="form-control" min="0" max="60" value="<?php echo (int)$profile['years_experience']; ?>" />
                <span class="text-danger"><?php echo tpv($err['years_experience'] ?? ''); ?></span>
            </div>
            <div class="col-12">
                <label class="form-label">Bio</label>
                <textarea name="bio" class="form-control" rows="4"><?php echo tpv($profile['bio']); ?></textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label">Expected Salary (ETB)</label>
                <input type="text" name="expected_salary" class="form-control" value="<?php echo tpv($profile['expected_salary']); ?>" />
                <span class="text-danger"><?php echo tpv($err['expected_salary'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" value="<?php echo tpv($profile['address']); ?>" />
            </div>
            <div class="col-md-6">
                <label class="form-label">Contact Email</label>
                <input type="email" name="contact_email" class="form-control" value="<?php echo tpv($profile['contact_email']); ?>" />
                <span class="text-danger"><?php echo tpv($err['contact_email'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
                <label class="form-label">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-control" value="<?php echo tpv($profile['contact_phone']); ?>" />
                <span class="text-danger"><?php echo tpv($err['contact_phone'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
                <label class="form-label">Profile Picture (jpg/png/jpeg/gif)</label>
                <input type="file" name="profile_picture" class="form-control" accept="image/*" />
                <?php if (!empty($profile['profile_picture'])) { ?><small class="text-muted d-block mt-1">Current: <a href="../<?php echo tpv($profile['profile_picture']); ?>" target="_blank">View</a></small><?php } ?>
                <span class="text-danger"><?php echo tpv($err['profile_picture'] ?? ''); ?></span>
            </div>
            <div class="col-md-6">
                <label class="form-label">CV (PDF up to 5MB)</label>
                <input type="file" name="cv" class="form-control" accept="application/pdf" />
                <?php if (!empty($profile['cv'])) { ?><small class="text-muted d-block mt-1">Current: <a href="../<?php echo tpv($profile['cv']); ?>" target="_blank">View</a></small><?php } ?>
                <span class="text-danger"><?php echo tpv($err['cv'] ?? ''); ?></span>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>