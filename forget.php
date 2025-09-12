<?php include 'login/loginHeader.php'; ?>
<main class="container auth-card">
    <div class="card shadow-sm border-0">
        <div class="card-body p-4 p-md-5">
            <h2 class="mb-3" style="color:#0d47a1; font-weight:700;">Forgot Password</h2>
            <p class="text-muted">Enter your email address and we’ll send you a password reset link.</p>
            <form action="forgot_process.php" method="post" class="row g-3" id="forgot-form" novalidate>
                <div class="col-12">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="fp-email" class="form-control" placeholder="Enter your email" required autocomplete="email">
                    <div id="email-feedback" class="small mt-1"></div>
                </div>
                <div class="col-12 text-center d-flex flex-column align-items-center gap-2">
                    <button type="submit" class="btn btn-outline-primary btn-lg px-5" id="reset-btn" disabled>Reset Password</button>
                </div>
            </form>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script>
                (function() {
                    const emailInput = document.getElementById('fp-email');
                    const feedback = document.getElementById('email-feedback');
                    const submitBtn = document.getElementById('reset-btn');
                    let lastQuery = '';
                    let timer;
                    let lastStatus = false;
                    const OK_CLASS = 'text-success';
                    const ERR_CLASS = 'text-danger';
                    const WARN_CLASS = 'text-warning';

                    function setFeedback(msg, status) {
                        feedback.className = 'small mt-1';
                        if (status === 'ok') feedback.classList.add(OK_CLASS);
                        else if (status === 'err') feedback.classList.add(ERR_CLASS);
                        else feedback.classList.add(WARN_CLASS);
                        feedback.textContent = msg;
                    }

                    function validateFormat(val) {
                        return /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(val);
                    }

                    function checkAvailability(val) {
                        if (val === '') {
                            setFeedback('Please enter your registered email.', 'err');
                            submitBtn.disabled = true;
                            return;
                        }
                        if (!validateFormat(val)) {
                            setFeedback('Please enter a valid email format.', 'err');
                            submitBtn.disabled = true;
                            return;
                        }
                        setFeedback('Checking email...', 'warn');
                        fetch('connection/check_email_exists.php?email=' + encodeURIComponent(val))
                            .then(r => r.json())
                            .then(js => {
                                if (!js.ok) {
                                    setFeedback('Unable to check email right now.', 'err');
                                    submitBtn.disabled = true;
                                    return;
                                }
                                if (js.exists) {
                                    setFeedback(js.message || 'Email found. You can continue.', 'ok');
                                    submitBtn.disabled = false;
                                    lastStatus = true;
                                } else {
                                    setFeedback(js.message || 'No account found with this email.', 'err');
                                    submitBtn.disabled = true;
                                    lastStatus = false;
                                }
                            })
                            .catch(() => {
                                setFeedback('Network error. Try again.', 'err');
                                submitBtn.disabled = true;
                            });
                    }
                    emailInput.addEventListener('input', function() {
                        const val = emailInput.value.trim();
                        submitBtn.disabled = true;
                        clearTimeout(timer);
                        timer = setTimeout(() => {
                            if (val !== lastQuery) {
                                lastQuery = val;
                                checkAvailability(val);
                            }
                        }, 400);
                    });
                    const form = document.getElementById('forgot-form');
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        const val = emailInput.value.trim();
                        if (val === '') {
                            setFeedback('Please enter your registered email.', 'err');
                            submitBtn.disabled = true;
                            return;
                        }
                        if (!lastStatus) {
                            setFeedback('We can\'t find that email. Check and try again.', 'err');
                            return;
                        }
                        // Prepare AJAX submission
                        submitBtn.disabled = true;
                        const origHtml = submitBtn.innerHTML;
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';
                        const fd = new FormData(form);
                        fd.append('ajax', '1');
                        fetch('forgot_process.php', {
                                method: 'POST',
                                body: fd
                            })
                            .then(r => {
                                const ct = r.headers.get('Content-Type') || '';
                                if (ct.includes('application/json')) return r.json();
                                return r.text().then(t => ({
                                    success: false,
                                    code: 'html_response',
                                    message: 'Unexpected response from server.',
                                    raw: t
                                }));
                            })
                            .then(data => {
                                if (data.success) {
                                    // Show confirmation instead of navigating to verify_code page
                                    Swal.fire({
                                        title: 'Check your email',
                                        text: 'You will receive an email with instructions to reset your password.',
                                        icon: 'success',
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                    return;
                                } else {
                                    let msg = data.message || 'Unable to send reset email. Please try again later.';
                                    if (data.code === 'send_failed' || data.code === 'exception') {
                                        msg += ' (If this persists, verify SMTP credentials / app password.)';
                                    }
                                    Swal.fire({
                                        title: 'Error',
                                        text: msg,
                                        icon: 'error'
                                    });
                                }
                            })
                            .catch(err => {
                                Swal.fire({
                                    title: 'Network Error',
                                    text: 'Connection issue. Please retry.',
                                    icon: 'error'
                                });
                            })
                            .finally(() => {
                                submitBtn.innerHTML = origHtml;
                                submitBtn.disabled = false;
                            });
                    });
                })();
            </script>
        </div>
    </div>
    <div class="mt-4">
        <div class="card shadow-sm border-0">
            <div class="card-body py-3 text-center">
                <a href="login.php" class="btn btn-light btn-sm px-4">
                    <style>
                        .btn.btn-light.btn-sm.px-4 {
                            font-weight: 700;
                            text-decoration: underline;
                            font-size: 1.15rem;
                        }

                        .btn.btn-light.btn-sm.px-4:hover {
                            background-color: #0d47a1 !important;
                            color: #fff !important;
                            border-color: #0d47a1 !important;
                        }
                    </style>
                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </div>
    </div>
</main>
<?php include 'login/loginFooter.php'; ?>