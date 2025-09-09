<?php
include 'login/loginHeader.php';
$username = $password = '';
$username_err = $password_err = '';
$all_err = '';

if (isset($_POST['login']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $ok = true;
    if (empty($_POST['username'])) {
        $username_err = 'Please enter your username';
        $ok = false;
    } else if (!validateIdNumber(trim($_POST['username']))) {
        $username_err = 'please enter valid a username';
        $ok = false;
    } else {
        $username = trim($_POST['username']);
    }
    if (empty($_POST['password'])) {
        $password_err = 'Please enter your password';
        $ok = false;
    } else if (!validatePassword(trim($_POST['password']))) {
        $password_err = 'please enter valid a password';
        $ok = false;
    } else {
        $password = $_POST['password'];
    }

    if ($ok) {
        if (checkUserByUsername($username)) {
            $user_data = checkUserCredentials($username, $password);
            if ($user_data) {
                if ((int)$user_data['user_status'] === 2) {
                    $all_err = 'This account is deactivated. Please contact the admin.';
                } elseif ((int)$user_data['user_status'] === 0 || (int)$user_data['user_status'] === 1) {

                    $_SESSION['uid'] = $user_data['uid'];
                    updateUserStatus(1, $_SESSION['uid']);
                    if ($user_data['user_type'] === 'admin') {
                        header('Location: admin/admin.php');
                        exit;
                    } elseif ($user_data['user_type'] === 'teacher') {
                        header('Location: teacher/teacher.php');
                        exit;
                    } else {
                        header('Location: index.php');
                        exit;
                    }
                } else {
                    $all_err = 'There is no user associated with the given information';
                }
            } else {
                $all_err = 'There is no user associated with the given information';
            }
        } else {
            $all_err = 'There is no user associated with the given information';
        }
    }
}
?>

<main class="container auth-card">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <div class="mb-3 text-center">
                <h2 class="mb-1" style="color:#0d47a1; font-weight:1000;">Welcome back</h2>
                <p class="text-muted mb-0">Login into Jobir Jobs.</p>
            </div>
            <?php if (!empty($all_err)) { ?>
                <div id="errorMessage" class="alert alert-danger"><?php echo $all_err; ?></div>
            <?php } ?>
            <form id="loginForm" method="post" action="login.php" class="row g-3" novalidate>
                <div class="col-12">
                    <label class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" name="username" class="form-control <?php echo !empty($username_err) ? 'is-invalid' : ''; ?>" value="<?php echo htmlspecialchars($username); ?>" placeholder="Enter your username">
                    <div class="invalid-feedback"><?php echo !empty($username_err) ? htmlspecialchars($username_err) : 'Please enter your username'; ?></div>
                </div>
                <div class="col-12">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group <?php echo !empty($password_err) ? 'is-invalid' : ''; ?>">
                        <input type="password" name="password" id="login_password" class="form-control <?php echo !empty($password_err) ? 'is-invalid' : ''; ?>" placeholder="Enter your password">
                        <button type="button" class="btn btn-outline-secondary toggle-password" data-target="login_password"><i class="fa-solid fa-eye"></i></button>
                    </div>
                    <div class="invalid-feedback"><?php echo !empty($password_err) ? htmlspecialchars($password_err) : 'Please enter your password'; ?></div>
                </div>
                <div class="col-12 d-flex justify-content-end">
                    <a href="#" class="auth-cta-link" onclick="alert('Password reset coming soon.'); return false;">Forgot password?</a>
                </div>
                <div class="col-12 text-center">
                    <button type="submit" name="login" value="1" class="btn btn-outline-primary btn-lg px-5">
                        <i class="fa-solid fa-right-to-bracket"></i> Login
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="col-12 mt-3">
        <div class="auth-cta-box">
            <span class="me-2">Don't have an account?</span>
            <a href="register.php" class="auth-cta-link">Sign Up</a>
        </div>
    </div>
</main>
<?php include 'login/loginFooter.php'; ?>