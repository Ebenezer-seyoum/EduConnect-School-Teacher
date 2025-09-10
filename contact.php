<?php
include 'Home/Homeheader.php';

// Initialize state (like register page style)
$fb_success = $fb_error = '';
$fb_old = [
    'name' => '',
    'email' => '',
    'phone' => '',
    'subject' => '',
    'message' => ''
];
$fb_field_errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    // Capture old values (sticky form)
    foreach ($fb_old as $k => $v) {
        $fb_old[$k] = trim($_POST[$k] ?? '');
    }

    // Validate
    if ($fb_old['name'] === '') {
        $fb_field_errors['name'] = 'Full name is required.';
    } elseif (mb_strlen($fb_old['name']) > 150) {
        $fb_field_errors['name'] = 'Full name max 150 characters.';
    }
    if ($fb_old['email'] === '') {
        $fb_field_errors['email'] = 'Email is required.';
    } elseif (!validateEmail($fb_old['email'])) {
        $fb_field_errors['email'] = 'Invalid email format.';
    } elseif (mb_strlen($fb_old['email']) > 50) {
        $fb_field_errors['email'] = 'Email max 50 characters.';
    }
    if ($fb_old['phone'] === '') {
        $fb_field_errors['phone'] = 'Phone is required.';
    } elseif (!validatePhoneNumber($fb_old['phone'])) {
        $fb_field_errors['phone'] = 'Invalid phone number.';
    } elseif (mb_strlen($fb_old['phone']) > 50) {
        $fb_field_errors['phone'] = 'Phone max 50 characters.';
    }
    if ($fb_old['subject'] === '') {
        $fb_field_errors['subject'] = 'Subject is required.';
    } elseif (mb_strlen($fb_old['subject']) > 190) {
        $fb_field_errors['subject'] = 'Subject max 190 characters.';
    }
    if ($fb_old['message'] === '') {
        $fb_field_errors['message'] = 'Message is required.';
    } elseif (mb_strlen($fb_old['message']) > 1000) {
        $fb_field_errors['message'] = 'Message must be 1000 characters or less.';
    }

    if (empty($fb_field_errors)) {
        [$ok, $m] = addFeedbackContact($fb_old['name'], $fb_old['email'], $fb_old['phone'], $fb_old['subject'], $fb_old['message']);
        if ($ok) {
            $fb_success = 'Thank you! Your feedback has been sent.';
            $fb_old = ['name' => '', 'email' => '', 'phone' => '', 'subject' => '', 'message' => ''];
        } else {
            $fb_error = $m ?: 'Failed to send feedback.';
        }
    } else {
        $fb_error = 'Please correct the highlighted fields.';
    }

    // AJAX response (prevent full page reload scroll jump)
    if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
        header('Content-Type: application/json');
        echo json_encode([
            'ok' => !empty($fb_success) && empty($fb_field_errors),
            'success' => $fb_success,
            'error' => $fb_error,
            'field_errors' => $fb_field_errors,
            'old' => $fb_old
        ]);
        exit;
    }
}
?>
<!-- Header Start -->
<div class="container-fluid position-relative d-flex align-items-center justify-content-center"
    style="min-height: 150px; overflow: hidden; background-color: #061343ff; border-radius: 0 50px 0 0;">

    <!-- Decorative Top-Right Curve -->
    <svg class="position-absolute top-0 end-0" width="200" height="200" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" style="z-index:1;">
        <path d="M0,0 C100,50 150,150 200,200 L200,0 Z" fill="#ffffff10" />
    </svg>

    <!-- Header Content -->
    <div class="position-relative text-center" style="z-index: 2; max-width: 500px;">
        <h1 class="text-white fw-bold mb-2 animate__animated animate__fadeInDown"
            style="font-size:2rem; text-shadow: 0 2px 6px rgba(0,0,0,0.3);">
            <i class="fas fa-briefcase me-2 text-warning"></i> Contact Us
        </h1>
        <p class="text-white-50 mb-0 animate__animated animate__fadeInUp animate__delay-1s" style="font-size: 1rem;">
            Connect with the right opportunities and professionals in a simple way
        </p>
    </div>
</div>
<!-- Header End -->




<style>
    /* Hover effect for contact info boxes */
    .contact-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
        transition: all 0.3s ease-in-out;
    }

    /* Form input text and placeholder color for dark background */
    .form-control-dark {
        background-color: #0d3b66;
        color: #fff;
        border: 1px solid #fff;
    }

    .form-control-dark::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .form-control-dark:focus {
        color: #fff;
        background-color: #0d3b66;
        border-color: #fff;
        box-shadow: none;
    }

    /* Social media buttons */
    .social-buttons a {
        background-color: #0d3b66;
        color: #fff;
        padding: 10px 15px;
        border-radius: 5px;
        text-decoration: none;
    }

    .social-buttons a:hover {
        background-color: #1a5d9c;
        color: #fff;
    }

    /* Dark info cards similar to form */
    .info-card-dark {
        background-color: #0d3b66;
        color: #fff;
        border-radius: 12px;
        padding: 22px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .15);
        transition: background 0.2s, color 0.2s, border-color 0.2s;
    }



    .info-card-dark h5 {
        color: #fff;
        margin-bottom: 6px;
    }

    .info-card-dark a {
        color: #fff;
        text-decoration: none;
        font-weight: 600;
    }

    .info-card-dark a:hover {
        text-decoration: underline;
    }

    .info-card-dark .icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, .12);
        margin-right: 12px;
    }

    .social-vertical {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .social-vertical a {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .2);
        border-radius: 10px;
        padding: 10px 14px;
        color: #fff;
    }

    .social-vertical a:hover {
        background: rgba(255, 255, 255, .16);
    }

    .social-vertical i {
        width: 18px;
        text-align: center;
    }

    /* Vertical social layout */
    .social-vertical {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .social-vertical a {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, .08);
        border: 1px solid rgba(255, 255, 255, .2);
        border-radius: 10px;
        padding: 12px 18px;
        color: #fff;
        text-decoration: none;
        font-size: 1.1rem;
        font-weight: 500;
        transition: background 0.2s, color 0.2s;
    }


    .social-vertical i {
        width: 22px;
        text-align: center;
        font-size: 1.3em;
    }

    .contact-row {
        display: flex;
        gap: 18px;
        margin-bottom: 18px;
    }

    .contact-row .info-card-dark {
        flex: 1;
        min-width: 0;
    }

    .social-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
        margin-top: 10px;
    }

    .social-grid-col {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .social-grid a {
        display: flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        padding: 12px 18px;
        color: #fff;
        text-decoration: none;
        font-size: 1.1rem;
        font-weight: 500;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s, transform 0.2s;
    }

    .social-grid a:hover {
        background: #8B0000;
        color: #fff;
        border-color: #8B0000;
        box-shadow: 0 6px 18px rgba(139, 0, 0, 0.15);
        transform: translateY(-3px);
    }

    .social-grid i {
        width: 22px;
        text-align: center;
        font-size: 1.3em;
    }

    /* Phone/Email hover dark red */
    .contact-row .info-card-dark:hover {
        background: #8B0000;
        color: #fff;
        border-color: #8B0000;
        box-shadow: 0 6px 18px rgba(139, 0, 0, 0.15);
        transform: translateY(-3px);
    }

    .btn-primary.w-100.py-3 {
        background: #fff;
        color: #8B0000;
        border: 2px solid #8B0000;
        font-weight: 600;
        transition: background 0.2s, color 0.2s, box-shadow 0.2s, transform 0.2s;
    }

    .btn-primary.w-100.py-3:hover {
        background: #8B0000;
        color: #fff;
        border-color: #8B0000;
        box-shadow: 0 6px 18px rgba(139, 0, 0, 0.15);
        transform: translateY(-3px);
    }
</style>

<!-- Contact + Form Section -->
<div class="container-fluid contact overflow-hidden py-5">
    <div class="container py-5">
        <div class="row g-5">

            <!-- Contact Information (three separate dark containers) -->
            <div class="col-lg-6 wow fadeInLeft" data-wow-delay="0.1s">
                <div class="sub-style mb-4">
                    <h5 class="sub-title text-primary pe-3">Quick Contact</h5>
                </div>
                <h1 class="display-5 mb-4">Have Questions? Don't Hesitate to Contact Us</h1>
                <p class="mb-4">Reach us via phone, email, or follow us on social media. We're here to help!</p>

                <div class="row g-3 align-items-stretch">
                    <div class="col-12">
                        <div class="contact-row">
                            <div class="info-card-dark d-flex align-items-center">
                                <div class="icon me-2"><i class="fas fa-phone-alt"></i></div>
                                <div>
                                    <h5 class="mb-1">Phone</h5>
                                    <a href="tel:+01234567890">+012 3456 7890</a>
                                </div>
                            </div>
                            <div class="info-card-dark d-flex align-items-center">
                                <div class="icon me-2"><i class="fas fa-envelope"></i></div>
                                <div>
                                    <h5 class="mb-1">Email</h5>
                                    <a href="mailto:info@jobirjobs.com">info@jobirjobs.com</a>
                                </div>
                            </div>
                        </div>
                        <div class="info-card-dark">
                            <h5 class="mb-3">Social Media</h5>
                            <div class="social-grid">
                                <div class="social-grid-col">
                                    <a href="#"><i class="fab fa-linkedin-in"></i><span>LinkedIn</span></a>
                                    <a href="#"><i class="fab fa-instagram"></i><span>Instagram</span></a>
                                    <a href="#"><i class="fab fa-facebook-f"></i><span>Facebook</span></a>
                                </div>
                                <div class="social-grid-col">
                                    <a href="#"><i class="fab fa-twitter"></i><span>Twitter</span></a>
                                    <a href="#"><i class="fab fa-telegram-plane"></i><span>Telegram</span></a>
                                    <a href="#"><i class="fab fa-whatsapp"></i><span>WhatsApp</span></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feedback Form -->
            <div class="col-lg-6 wow fadeInRight" data-wow-delay="0.3s">
                <?php if (!empty($fb_success)) { ?><div id="fb-success" class="alert alert-success fw-bold" style="background:#0f5132;color:#fff;border:none;"><?php echo $fb_success; ?></div><?php } ?>
                <?php if (!empty($fb_error)) { ?><div id="fb-error" class="alert alert-danger fw-bold"><?php echo $fb_error; ?></div><?php } ?>
                <form id="contact-form" class="p-4 rounded-3 dark-form" style="background-color: #0d3b66;" method="post" action="">
                    <input type="hidden" name="contact_form" value="1">
                    <div class="sub-style mb-4">
                        <h5 class="sub-title text-light pe-3">Let’s Connect</h5>
                    </div>
                    <h1 class="display-5 text-white mb-3">Send Your Message</h1>
                    <p class="mb-4 text-light">Fill out the form below and we will get back to you as soon as possible.</p>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control form-control-dark <?php echo !empty($fb_field_errors['name']) ? 'is-invalid' : ''; ?>" id="name" name="name" placeholder="Your Full Name" maxlength="150" value="<?php echo htmlspecialchars($fb_old['name']); ?>">
                                <label for="name" class="text-light">Your Full Name</label>
                                <?php if (!empty($fb_field_errors['name'])) { ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($fb_field_errors['name']); ?></div><?php } ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" class="form-control form-control-dark <?php echo !empty($fb_field_errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" placeholder="Your Email" maxlength="50" value="<?php echo htmlspecialchars($fb_old['email']); ?>">
                                <label for="email" class="text-light">Your Email</label>
                                <?php if (!empty($fb_field_errors['email'])) { ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($fb_field_errors['email']); ?></div><?php } ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control form-control-dark <?php echo !empty($fb_field_errors['phone']) ? 'is-invalid' : ''; ?>" id="phone" name="phone" placeholder="Phone (e.g. +251911234567)" maxlength="50" pattern="^\+?\d{10,15}$" value="<?php echo htmlspecialchars($fb_old['phone']); ?>">
                                <label for="phone" class="text-light">Your Phone</label>
                                <?php if (!empty($fb_field_errors['phone'])) { ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($fb_field_errors['phone']); ?></div><?php } ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control form-control-dark <?php echo !empty($fb_field_errors['subject']) ? 'is-invalid' : ''; ?>" id="subject" name="subject" placeholder="Subject" maxlength="190" value="<?php echo htmlspecialchars($fb_old['subject']); ?>">
                                <label for="subject" class="text-light">Subject</label>
                                <?php if (!empty($fb_field_errors['subject'])) { ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($fb_field_errors['subject']); ?></div><?php } ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <textarea class="form-control form-control-dark <?php echo !empty($fb_field_errors['message']) ? 'is-invalid' : ''; ?>" placeholder="Write your message (max 1000 characters)" id="message" name="message" style="height: 160px" maxlength="1000"><?php echo htmlspecialchars($fb_old['message']); ?></textarea>
                                <label for="message" class="text-light">Message</label>
                                <div class="d-flex justify-content-between small mt-1">
                                    <div class="text-danger" id="message-warning" style="display:none;">Approaching limit</div>
                                    <div id="message-count" class="text-light ms-auto">0 / 1000</div>
                                </div>
                                <?php if (!empty($fb_field_errors['message'])) { ?><div class="invalid-feedback d-block"><?php echo htmlspecialchars($fb_field_errors['message']); ?></div><?php } ?>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn w-100 py-3" style="background:#fff;color:#8B0000;border:2px solid #8B0000;font-weight:600;transition:background 0.2s,color 0.2s;" onmouseover="this.style.background='#8B0000';this.style.color='#fff';" onmouseout="this.style.background='#fff';this.style.color='#8B0000';">Send Message</button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<?php
include 'Home/Homefooter.php';
?>
<style>
    .dark-form .invalid-feedback {
        color: #dc3545 !important;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .dark-form .form-control.is-invalid,
    .dark-form .form-control-dark.is-invalid {
        border-color: #dc3545 !important;
        box-shadow: none;
    }

    .dark-form .form-floating>.invalid-feedback {
        position: static;
        margin-top: 4px;
    }

    #message-count.text-danger {
        font-weight: 700;
    }
</style>
<script>
    (function() {
        const msg = document.getElementById('message');
        const count = document.getElementById('message-count');
        const warn = document.getElementById('message-warning');
        if (!msg) return;

        function update() {
            const len = msg.value.length;
            count.textContent = len + ' / 1000';
            if (len > 1000) {
                count.classList.remove('text-light');
                count.classList.add('text-danger');
                warn.style.display = 'block';
                warn.textContent = 'Over limit';
            } else if (len >= 950) {
                count.classList.remove('text-light');
                count.classList.add('text-danger');
                warn.style.display = 'block';
                warn.textContent = 'Approaching limit';
            } else {
                count.classList.remove('text-danger');
                count.classList.add('text-light');
                warn.style.display = 'none';
            }
        }
        msg.addEventListener('input', update);
        update();
        // Auto hide success/error after 3s
        setTimeout(() => {
            const s = document.getElementById('fb-success');
            if (s) s.style.display = 'none';
            const e = document.getElementById('fb-error');
            if (e) e.style.display = 'none';
        }, 3000);
    })();
</script>