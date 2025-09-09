<?php
include 'login/loginHeader.php';
// declare defaults to avoid undefined notices and track validation state
$fullName = $gender = $email = $phone = $username = $password = $confirmPassword = "";
$fullName_err = $gender_err = $email_err = $phone_err = $username_err = $password_err = $confirmPassword_err = $allErr = $success = "";
$test = true;

if (isset($_POST["register"]) && ($_SERVER["REQUEST_METHOD"] == "POST")) {
    //validate full name
    if (empty($_POST["full_name"])) {
        $fullName_err = "Please enter your full name";
        $test = false;
    } else if (validateIdNumber($_POST["full_name"]) == 0) {
        $fullName_err = "Please enter a valid full name";
        $test = false;
    } else {
        $fullName = $_POST["full_name"];
    }
    //validate gender
    if (empty($_POST["gender"])) {
        $gender_err = "Please select your gender";
        $test = false;
    } else {
        $g = $_POST["gender"];
        if ($g === 'Male') {
            $g = 'M';
        }
        if ($g === 'Female') {
            $g = 'F';
        }
        if (validateGender($g) == 0) {
            $gender_err = "Invalid input";
            $test = false;
        } else {
            $gender = $g;
        }
    }

    // Validate email
    if (empty($_POST["email"])) {
        $email_err = "Please enter your email";
        $test = false;
    } else {
        $email = ($_POST["email"]);
        if (!validateEmail($email)) {
            $email_err = "Please enter a valid email address (example: user@domain.com)";
            $test = false;
        } else {
            $email = $_POST["email"];
        }
    }
    //validate username
    if (empty($_POST["username"])) {
        $username_err = "Please enter your username";
        $test = false;
    } else if (validateIdNumber($_POST["username"]) == 0) {
        $username_err = "Please enter valid username";
        $test = false;
    } else {
        $username = $_POST["username"];
    }
    //validate password
    if (empty($_POST["password"])) {
        $password_err = "Please enter your new password";
        $test = false;
    } else if (validatePassword($_POST["password"]) == 0) {
        $password_err = "Please enter a valid password (no invalid symbols)";
        $test = false;
    } else {
        $password = $_POST["password"];
        $strongPassword = isStrongPassword($password);
        if ($strongPassword !== true) {
            $password_err = $strongPassword;
            $test = false;
        }
    }

    //validate password confirmation
    if (empty($_POST["confirm_password"])) {
        $confirmPassword_err = "Please enter your new password";
        $test = false;
    } else if (validatePassword($_POST["confirm_password"]) == 0) {
        $confirmPassword_err = "Please enter valid password";
        $test = false;
    } else if (comparePasswords($_POST["password"], $_POST["confirm_password"]) == 0) {
        $confirmPassword_err = "Password did not match";
    } else {
        $confirmPassword = $_POST["confirm_password"];
    }
    //validate  Phone 
    if (empty($_POST["phone"])) {
        $phone_err = "Please enter your phone number";
        $test = false;
    } else if (validatePhoneNumber($_POST["phone"]) == 0) {
        $phone_err = "Please enter valid phone number";
        $test = false;
    } else {
        $phone = $_POST["phone"];
    }
    if ($test == true) {
        $user_type = 'teacher';

        if (EmailExist($email) == 0 && UserNameExist($username) == 0) {
            if (addUser($fullName, $gender, $email, $phone, $username, $password, $user_type) == 1) {
                $success = "User Successfully registered";
            } else {
                $allErr = "There was error while registration";
            }
        } else {
            $allErr = "User with this email or username already exists";
        }
    }
}
?>

<main class="container auth-card" style="max-width: 720px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <div class="mb-3 text-center">
                <h2 class="mb-1" style="color:#0d47a1; font-weight:1000;">Create your account</h2>
                <p class="text-muted mb-0">Join Jobir Jobs to find teaching opportunities.</p>
            </div>
            <?php if (!empty($success)) { ?><div id="successMessage" class="alert alert-success"><?php echo $success; ?></div><?php } ?>
            <?php if (!empty($allErr)) { ?><div id="errorMessage" class="alert alert-danger"><?php echo $allErr; ?></div><?php } ?>

            <form method="post" action="register.php" class="row g-3">
                <div class="col-12">
                    <label class="form-label">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="full_name" class="form-control <?php echo !empty($fullName_err) ? 'is-invalid' : ''; ?>" maxlength="150" placeholder="e.g., Abebe Kebede" value="<?php echo htmlspecialchars($fullName); ?>" />
                    <?php if (!empty($fullName_err)) { ?><div class="invalid-feedback"><?php echo $fullName_err; ?></div><?php } ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Gender <span class="text-danger">*</span></label>
                    <select name="gender" class="form-select <?php echo !empty($gender_err) ? 'is-invalid' : ''; ?>">
                        <option value="">Select gender</option>
                        <option value="M" <?php echo ($gender === 'M' ? 'selected' : ''); ?>>Male</option>
                        <option value="F" <?php echo ($gender === 'F' ? 'selected' : ''); ?>>Female</option>
                    </select>
                    <?php if (!empty($gender_err)) { ?><div class="invalid-feedback"><?php echo $gender_err; ?></div><?php } ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone <span class="text-danger">*</span></label>
                    <input id="phone" type="tel" name="phone" class="form-control <?php echo !empty($phone_err) ? 'is-invalid' : ''; ?>" maxlength="50" placeholder="e.g., +251 911 234 567" value="<?php echo htmlspecialchars($phone); ?>" />
                    <input type="hidden" id="phone_e164" name="phone_e164" value="" />
                    <?php if (!empty($phone_err)) { ?><div class="invalid-feedback"><?php echo $phone_err; ?></div><?php } ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control <?php echo !empty($email_err) ? 'is-invalid' : ''; ?>" maxlength="190" placeholder="e.g., user@example.com" value="<?php echo htmlspecialchars($email); ?>" />
                    <?php if (!empty($email_err)) { ?><div class="invalid-feedback"><?php echo $email_err; ?></div><?php } ?>
                    <div id="email-availability" class="form-text"></div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control <?php echo !empty($username_err) ? 'is-invalid' : ''; ?>" maxlength="190" placeholder="Choose a username" value="<?php echo htmlspecialchars($username); ?>" />
                    <?php if (!empty($username_err)) { ?><div class="invalid-feedback"><?php echo $username_err; ?></div><?php } ?>
                    <div id="username-availability" class="form-text"></div>
                </div>


                <div class="col-12 col-sm-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group <?php echo !empty($password_err) ? 'is-invalid' : ''; ?>">
                        <input type="password" name="password" id="new_password" class="form-control <?php echo !empty($password_err) ? 'is-invalid' : ''; ?>" maxlength="255" placeholder="Create a strong password" onkeyup="checkPassword()" />
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="new_password"><i class="fa-solid fa-eye"></i></button>
                    </div>
                    <div id="password-checklist" style="display:none; font-size: 0.9rem;" class="bg-light rounded p-2 mt-2">
                        <div id="lower">❌ One lowercase letter</div>
                        <div id="upper">❌ One uppercase letter</div>
                        <div id="special">❌ One special character (@#$%^&+=!)</div>
                        <div id="length">❌ At least 8 characters</div>
                    </div>
                    <?php if (!empty($password_err)) { ?><div class="invalid-feedback"><?php echo $password_err; ?></div><?php } ?>
                </div>
                <div class="col-12 col-sm-6">
                    <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group <?php echo !empty($confirmPassword_err) ? 'is-invalid' : ''; ?>">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control <?php echo !empty($confirmPassword_err) ? 'is-invalid' : ''; ?>" maxlength="255" placeholder="Re-type your password" />
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirm_password"><i class="fa-solid fa-eye"></i></button>
                    </div>
                    <?php if (!empty($confirmPassword_err)) { ?><div class="invalid-feedback"><?php echo $confirmPassword_err; ?></div><?php } ?>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" name="register" value="1" class="btn btn-outline-primary btn-lg px-5">
                        <i class="fa-solid fa-user-plus"></i> Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="auth-cta-box ">
            <span>Already have an account?</span>
            <a href="login.php" class="auth-cta-link">Login</a>
        </div>
    </div>
</main>
<!-- intl-tel-input styles -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.6/build/css/intlTelInput.css" />
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.6/build/js/intlTelInput.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.5.6/build/js/utils.js"></script>
<script>
    // Live availability check with debounce
    (function() {
        const emailInput = document.querySelector('input[name="email"]');
        const userInput = document.querySelector('input[name="username"]');
        const emailMsg = document.getElementById('email-availability');
        const userMsg = document.getElementById('username-availability');
        const setMsg = (el, ok, text) => {
            if (!el) return;
            el.textContent = text || '';
            el.classList.remove('text-success', 'text-danger');
            if (text) el.classList.add(ok ? 'text-success' : 'text-danger');
        };
        let t1, t2;
        const check = (type, value, cb) => {
            if (!value) {
                cb(null);
                return;
            }
            fetch('connection/check_availability.php?type=' + encodeURIComponent(type) + '&value=' + encodeURIComponent(value))
                .then(r => r.json())
                .then(d => cb(d))
                .catch(() => cb(null));
        };
        if (emailInput) {
            const handler = () => {
                clearTimeout(t1);
                t1 = setTimeout(() => {
                    const v = emailInput.value.trim();
                    check('email', v, (d) => {
                        if (!d) {
                            setMsg(emailMsg, false, '');
                            return;
                        }
                        setMsg(emailMsg, !!d.available, d.message);
                    });
                }, 400);
            };
            emailInput.addEventListener('input', handler);
            emailInput.addEventListener('blur', handler);
        }
        if (userInput) {
            const handler = () => {
                clearTimeout(t2);
                t2 = setTimeout(() => {
                    const v = userInput.value.trim();
                    check('username', v, (d) => {
                        if (!d) {
                            setMsg(userMsg, false, '');
                            return;
                        }
                        setMsg(userMsg, !!d.available, d.message);
                    });
                }, 400);
            };
            userInput.addEventListener('input', handler);
            userInput.addEventListener('blur', handler);
        }
    })();
</script>
<?php include 'login/loginFooter.php'; ?>