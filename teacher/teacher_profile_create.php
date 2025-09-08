<?php
// Assumes session + connection included by teacher.php
$uid = (int)$_SESSION['uid'];
$existing = getTeacherProfileByUserId($uid);
if ($existing) {
    echo '<div class="alert alert-info">Profile already exists. You can <a href="teacher.php?page=edit_profile">edit it here</a>.</div>';
    return;
}

// Match register.php style: use per-field error strings and a $test flag
$success = '';
$allErr = '';
$test = true;
// Preset values and errors for sticky form and messages
$full_name = $bio = $expected_salary = $address = $contact_email = $contact_phone = '';
$years_experience = 0;
$full_name_err = $bio_err = $years_experience_err = $expected_salary_err = $address_err = $contact_email_err = $contact_phone_err = $profile_picture_err = $cv_err = '';
function tpv($v)
{
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_profile']) && (int)$_POST['create_profile'] === 1) {
    // Gather inputs (trim where applicable)
    $full_name = trim($_POST['full_name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $years_experience_raw = $_POST['years_experience'] ?? '';
    $years_experience = ($years_experience_raw === '' ? 0 : (int)$years_experience_raw);
    $expected_salary = trim($_POST['expected_salary'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $contact_email = trim($_POST['contact_email'] ?? '');
    $contact_phone = trim($_POST['contact_phone'] ?? '');

    // Validate like register.php style (now all fields mandatory)
    if ($full_name === '') {
        $full_name_err = 'Please enter your full name';
        $test = false;
    } elseif (function_exists('validateIdNumber') && validateIdNumber($full_name) == 0) {
        $full_name_err = 'Please enter a valid full name';
        $test = false;
    }

    if ($bio === '') {
        $bio_err = 'Please enter your bio';
        $test = false;
    }

    if ($contact_email === '') {
        $contact_email_err = 'Please enter your email';
        $test = false;
    } elseif (!function_exists('validateEmail') || !validateEmail($contact_email)) {
        $contact_email_err = 'Please enter a valid email address (example: user@domain.com)';
        $test = false;
    }

    if ($contact_phone === '') {
        $contact_phone_err = 'Please enter your phone number';
        $test = false;
    } elseif (!function_exists('validatePhoneNumber') || validatePhoneNumber($contact_phone) == 0) {
        $contact_phone_err = 'Please enter valid phone number';
        $test = false;
    }

    if ($expected_salary === '') {
        $expected_salary_err = 'Please enter your expected salary';
        $test = false;
    } elseif (!preg_match('/^\d+(\.\d{1,2})?$/', $expected_salary)) {
        $expected_salary_err = 'Salary must be a number (max 2 decimals)';
        $test = false;
    }

    if ($years_experience_raw === '') {
        $years_experience_err = 'Please enter your years of experience';
        $test = false;
    } elseif ($years_experience < 0 || $years_experience > 60) {
        $years_experience_err = 'Years of experience must be between 0 and 60';
        $test = false;
    }

    if ($address === '') {
        $address_err = 'Please enter your address';
        $test = false;
    }

    // uploads
    $profile_picture_path = '';
    $cv_path = '';
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $valid = function_exists('validateProfilePicture') ? validateProfilePicture($_FILES['profile_picture']) : true;
            if ($valid === true) {
                $ext = pathinfo($_FILES['profile_picture']['name'], PATHINFO_EXTENSION);
                $fileName = 'pp_' . $uid . '_' . time() . '.' . $ext;
                $destDir = __DIR__ . '/assets/images/profile';
                $dest = $destDir . '/' . $fileName;
                if (!is_dir($destDir)) {
                    @mkdir($destDir, 0777, true);
                }
                if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $dest)) {
                    $profile_picture_path = 'teacher/assets/images/profile/' . $fileName;
                } else {
                    $profile_picture_err = 'Upload failed';
                    $test = false;
                }
            } else {
                $profile_picture_err = is_string($valid) ? $valid : 'Invalid image file';
                $test = false;
            }
        } else {
            $profile_picture_err = 'Error uploading image';
            $test = false;
        }
    } else {
        $profile_picture_err = 'Please upload a profile picture';
        $test = false;
    }

    if (isset($_FILES['cv']) && $_FILES['cv']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['cv']['error'] === UPLOAD_ERR_OK) {
            $valid = function_exists('validateUploadedFile') ? validateUploadedFile($_FILES['cv'], ['application/pdf'], 5 * 1024 * 1024) : true;
            if ($valid === true) {
                $ext = pathinfo($_FILES['cv']['name'], PATHINFO_EXTENSION);
                $fileName = 'cv_' . $uid . '_' . time() . '.' . $ext;
                $destDir = __DIR__ . '/assets/images/profile';
                $dest = $destDir . '/' . $fileName;
                if (!is_dir($destDir)) {
                    @mkdir($destDir, 0777, true);
                }
                if (move_uploaded_file($_FILES['cv']['tmp_name'], $dest)) {
                    $cv_path = 'teacher/assets/images/profile/' . $fileName;
                } else {
                    $cv_err = 'CV upload failed';
                    $test = false;
                }
            } else {
                $cv_err = is_string($valid) ? $valid : 'Invalid CV file';
                $test = false;
            }
        } else {
            $cv_err = 'Error uploading CV';
            $test = false;
        }
    } else {
        $cv_err = 'Please upload your CV (PDF)';
        $test = false;
    }

    if ($test === true) {
        $created = createTeacherProfile($uid, [
            'full_name' => $full_name,
            'bio' => $bio,
            'years_experience' => $years_experience,
            'expected_salary' => $expected_salary,
            'address' => $address,
            'contact_email' => $contact_email,
            'contact_phone' => $contact_phone,
            'profile_picture' => $profile_picture_path,
            'cv' => $cv_path,
        ]);
        if ($created) {
            $success = 'Inserted successfully';
            // refresh existing
            $existing = getTeacherProfileByUserId($uid);
        } else {
            $allErr = 'There was error while saving profile';
        }
    } else {
        // optional general error message like register.php
        // $allErr can remain empty to rely on per-field spans
    }
}
?>

<div class="card">
    <div class="card-body">
        <h4 class="card-title">Create Teacher Profile</h4>
        <?php if (!empty($success)) { ?><div class="alert alert-success"><?php echo tpv($success); ?></div><?php } ?>
        <?php if (!empty($allErr)) { ?><div class="alert alert-danger"><?php echo tpv($allErr); ?></div><?php } ?>
        <form method="post" enctype="multipart/form-data" class="row g-3 align-items-start">
            <input type="hidden" name="create_profile" value="1" />
            <!-- Left: Avatar upload -->
            <div class="col-md-4">
                <?php $defaultAvatar = './assets/images/no.png'; ?>
                <div class="avatar-upload text-center">
                    <div class="avatar-preview mb-2">
                        <img id="avatarPreview" class="rounded-circle border" src="<?php echo $defaultAvatar; ?>" alt="Profile Picture" width="140" height="140" />
                    </div>
                    <div class="change-btn">
                        <input type="file" class="form-control d-none" id="imageUpload" name="profile_picture" accept="image/*">
                        <label for="imageUpload" class="mb-0 btn btn-primary btn-sm">Choose Image</label>
                        <button type="button" id="removeImage" class="btn btn-outline-danger ms-2 btn-sm">Remove</button>
                    </div>
                    <div class="small text-danger mt-2"><?php echo tpv($profile_picture_err); ?></div>
                </div>
            </div>
            <!-- Right: Form fields -->
            <div class="col-md-8">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo tpv($full_name); ?>" />
                        <span class="text-danger"><?php echo tpv($full_name_err); ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Years of Experience</label>
                        <input type="number" name="years_experience" class="form-control" min="0" max="60" value="<?php echo (int)$years_experience; ?>" />
                        <span class="text-danger"><?php echo tpv($years_experience_err); ?></span>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea name="bio" class="form-control" rows="4" placeholder="Tell schools about yourself"><?php echo tpv($bio); ?></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Expected Salary (ETB)</label>
                        <input type="text" name="expected_salary" class="form-control" placeholder="e.g., 15000.00" value="<?php echo tpv($expected_salary); ?>" />
                        <span class="text-danger"><?php echo tpv($expected_salary_err); ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="<?php echo tpv($address); ?>" />
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Email</label>
                        <input type="email" name="contact_email" class="form-control" value="<?php echo tpv($contact_email); ?>" />
                        <span class="text-danger"><?php echo tpv($contact_email_err); ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Phone</label>
                        <input type="text" name="contact_phone" class="form-control" placeholder="+2519xxxxxxx" value="<?php echo tpv($contact_phone); ?>" />
                        <span class="text-danger"><?php echo tpv($contact_phone_err); ?></span>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">CV (PDF up to 5MB)</label>
                        <input type="file" name="cv" class="form-control" accept="application/pdf" />
                        <span class="text-danger"><?php echo tpv($cv_err); ?></span>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Create Profile</button>
                    </div>
                </div>
            </div>
        </form>
        <script>
            (function() {
                var input = document.getElementById('imageUpload');
                var preview = document.getElementById('avatarPreview');
                var removeBtn = document.getElementById('removeImage');
                var placeholder = '<?php echo $defaultAvatar; ?>';
                if (input) {
                    input.addEventListener('change', function(e) {
                        if (this.files && this.files[0]) {
                            var url = URL.createObjectURL(this.files[0]);
                            preview.src = url;
                        }
                    });
                }
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        if (input) {
                            input.value = '';
                        }
                        preview.src = placeholder;
                    });
                }
            })();
        </script>
    </div>
</div>