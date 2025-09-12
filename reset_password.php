<?php
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$token = isset($_GET['token']) ? trim($_GET['token']) : '';
$tokenValid = false;

include 'login/loginHeader.php';

if ($email && $token) {
    $emailEsc = mysqli_real_escape_string($conn, $email);
    $tokenEsc = mysqli_real_escape_string($conn, $token);
    $q = mysqli_query($conn, "SELECT expires_at FROM password_resets WHERE email='$emailEsc' AND token='$tokenEsc' LIMIT 1");
    if ($q && mysqli_num_rows($q) === 1) {
        $row = mysqli_fetch_assoc($q);
        if (strtotime($row['expires_at']) > time()) {
            $tokenValid = true;
        }
    }
}
?>
<main class="container auth-card" style="max-width:600px;">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <?php if ($tokenValid) { ?>
                <h2 class="mb-2 text-center" style="color:#0d47a1; font-weight:700;">Set New Password</h2>
                <p class="text-bold mb-4">Create a strong password. The link is valid for a limited time.</p>
                <form action="reset_process.php" method="post" class="row g-3" id="resetForm" novalidate>
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                    <div class="col-12">
                        <label class="form-label">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="password" id="password" class="form-control" placeholder="Enter new password" required minlength="8" autocomplete="new-password" oninput="validatePassword();">
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="password"><i class="fa-solid fa-eye"></i></button>
                        </div>
                        <div id="password-status" class="mt-1 small"></div>
                        <div id="password-checklist" class="bg-light border rounded p-2 mt-2 small" style="display:block; line-height:1.3;">
                            <div id="lower">❌ One lowercase letter</div>
                            <div id="upper">❌ One uppercase letter</div>
                            <div id="special">❌ One special character (@#$%^&+=!)</div>
                            <div id="length">❌ At least 8 characters</div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Re-type password" required minlength="8" autocomplete="new-password" oninput="validatePassword();">
                            <button type="button" class="btn btn-outline-secondary toggle-password" data-target="confirm_password"><i class="fa-solid fa-eye"></i></button>
                        </div>
                        <div id="confirm-status" class="mt-1 small"></div>
                    </div>

                    <div class="col-12 text-center pt-2">
                        <button type="submit" class="btn btn-outline-primary btn-lg px-5" id="submitBtn" disabled>Update Password</button>
                    </div>
                </form>

                <script>
                    function validatePassword() {
                        const pw = document.getElementById('password').value;
                        const cpw = document.getElementById('confirm_password').value;

                        const lower = /[a-z]/.test(pw);
                        const upper = /[A-Z]/.test(pw);
                        const special = /[@#$%^&+=!]/.test(pw);
                        const length = pw.length >= 8;

                        document.getElementById('lower').textContent = (lower ? '✅' : '❌') + ' One lowercase letter';
                        document.getElementById('upper').textContent = (upper ? '✅' : '❌') + ' One uppercase letter';
                        document.getElementById('special').textContent = (special ? '✅' : '❌') + ' One special character (@#$%^&+=!)';
                        document.getElementById('length').textContent = (length ? '✅' : '❌') + ' At least 8 characters';

                        const checklistPassed = lower && upper && special && length;

                        const match = pw && cpw && pw === cpw;
                        const confirmStatus = document.getElementById('confirm-status');
                        confirmStatus.textContent = cpw ? (match ? 'Passwords match' : 'Passwords do not match') : '';
                        confirmStatus.className = 'mt-1 small ' + (cpw ? (match ? 'text-success' : 'text-danger') : '');

                        document.getElementById('submitBtn').disabled = !(checklistPassed && match);
                    }
                </script>
            <?php } else { ?>
                <style>
                    body { background: #fafbfc; }
                    header, footer, .site-header { display:none !important; }
                </style>
                <div class="text-center py-5" style="padding-top:40px;">
                    <div style="font-size:70px; line-height:1; color:#d32f2f; margin-bottom:10px;" aria-hidden="true">
                        <i class="fa-solid fa-circle-exclamation" style="filter:drop-shadow(0 2px 4px rgba(211,47,47,.35));"></i>
                    </div>
                    <h2 class="mt-2 mb-3" style="color:#d32f2f; font-weight:700;">Reset Link Expired</h2>
                    <p class="mb-2" style="font-weight:500;">This password reset link is no longer valid.</p>
                    <p class="small text-muted mb-4" style="max-width:420px;margin:0 auto;">Request a new secure link or use the verification code option again.</p>
                    <a class="btn btn-primary btn-lg px-4" href="forget.php" style="font-weight:600;">Request New Reset Link</a>
                </div>
            <?php } ?>
        </div>
    </div>
</main>

<?php if ($tokenValid) include 'login/loginFooter.php'; ?>
