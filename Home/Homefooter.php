<footer>
    <!-- Footer Start-->
    <div class="footer-area footer-bg footer-padding">
        <div class="container">
            <div class="row d-flex justify-content-between">
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                    <div class="single-footer-caption mb-50">
                        <div class="single-footer-caption mb-30">
                            <div class="footer-tittle">
                                <h4>About Us</h4>
                                <div class="footer-pera">
                                    <p>Jobir Jobs is a simple, modern platform that connects schools with qualified teachers. Post vacancies, discover talent, and manage hiring in one place.</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                    <div class="single-footer-caption mb-50">
                        <div class="footer-tittle">
                            <h4>Contact Info</h4>
                            <ul>
                                <li>
                                    <p>Address :Your address goes
                                        here, your demo address.</p>
                                </li>
                                <li><a href="#">Phone : +8880 44338899</a></li>
                                <li><a href="#">Email : info@colorlib.com</a></li>
                            </ul>
                        </div>

                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                    <div class="single-footer-caption mb-50">
                        <div class="footer-tittle">
                            <h4>Important Link</h4>
                            <ul>
                                <li><a href="#">Home</a></li>
                                <li><a href="#">About</a></li>
                                <li><a href="#">School vacancy</a></li>
                                <li><a href="#">Find teachers</a></li>
                                <li><a href="#">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-5">
                    <div class="single-footer-caption mb-50">
                        <div class="footer-tittle">
                            <h4>Social Media</h4>
                            <div class="footer-social-icons">
                                <ul class="social-list">
                                    <li>
                                        <a href="#" class="social-link" aria-label="Facebook" target="_blank" rel="noopener">
                                            <span class="social-icon"><i class="fab fa-facebook-f"></i></span>
                                            <span class="social-label">Facebook</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="social-link" aria-label="Twitter" target="_blank" rel="noopener">
                                            <span class="social-icon"><i class="fab fa-twitter"></i></span>
                                            <span class="social-label">Twitter</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="social-link" aria-label="LinkedIn" target="_blank" rel="noopener">
                                            <span class="social-icon"><i class="fab fa-linkedin-in"></i></span>
                                            <span class="social-label">LinkedIn</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#" class="social-link" aria-label="Telegram" target="_blank" rel="noopener">
                                            <span class="social-icon"><i class="fab fa-telegram-plane"></i></span>
                                            <span class="social-label">Telegram</span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- footer-bottom area -->
    <div class="footer-bottom-area footer-bg">
        <div class="container">
            <div class="footer-border">
                <div class="row d-flex justify-content-between align-items-center">
                    <div class="col-xl-10 col-lg-10 ">
                        <div class="footer-copy-right">
                            <p>
                                &copy; <script>
                                    document.write(new Date().getFullYear());
                                </script> All rights reserved · Jobir Jobs · Designed by <a href="https://ebenezerseyoum.me" target="_blank" rel="noopener">Ebenezer Seyoum</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer End-->
    <!-- Modal for Send Request -->
    <div class="modal fade" id="sendRequestModal" tabindex="-1" role="dialog" aria-labelledby="sendRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendRequestModalLabel">Send Request</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="sendRequestForm" method="post" action="school-vacancy.php">
                    <div class="modal-body">
                        <input type="hidden" name="send_request" value="1" />
                        <input type="hidden" name="vacancy_id" id="req_vacancy_id" value="" />
                        <div class="form-group">
                            <label for="sender_name">Your Name</label>
                            <input type="text" class="form-control" name="sender_name" id="sender_name" placeholder="Full name" required />
                        </div>
                        <div class="form-group">
                            <label for="sender_contact">Your Contact (email or phone)</label>
                            <input type="text" class="form-control" name="sender_contact" id="sender_contact" placeholder="you@example.com or +2519xxxxxxx" required />
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" name="message" id="message" rows="3">I would like to get contact details and discuss commission.</textarea>
                        </div>
                        <small id="contactInfoHint" class="form-text text-muted"></small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JS for handling Send Request modal (Bootstrap 4 compatible) -->
    <script>
        window.handleSendRequest = function(vacancyId, email, phone) {
            var form = document.getElementById('sendRequestForm');
            if (!form) return;
            // Reset to vacancy mode
            form.action = 'school-vacancy.php';
            var sr = form.querySelector('input[name="send_request"]');
            if (!sr) {
                sr = document.createElement('input');
                sr.type = 'hidden';
                sr.name = 'send_request';
                sr.value = '1';
                form.appendChild(sr);
            }
            var tr = form.querySelector('input[name="teacher_send_request"]');
            if (tr) tr.parentNode.removeChild(tr);

            document.getElementById('req_vacancy_id').value = vacancyId;
            var hint = document.getElementById('contactInfoHint');
            var hasContact = (email && email.trim().length > 0) || (phone && phone.trim().length > 0);
            if (hasContact) {
                hint.innerText = 'Contact info available: ' + [email || null, phone || null].filter(Boolean).join(' | ');
            } else {
                hint.innerText = 'No contact information provided by the school. Send a request to the admin to get in touch and discuss commission.';
            }
            // Show modal (Bootstrap 4 style)
            if (typeof $ !== 'undefined' && typeof $.fn.modal === 'function') {
                $('#sendRequestModal').modal('show');
            } else {
                // Fallback: simple display toggle
                document.getElementById('sendRequestModal').style.display = 'block';
            }
        };
    </script>
</footer>

<style>
    /* Tighter footer spacing */
    .footer-area.footer-padding {
        padding-top: 40px;
        padding-bottom: 30px;
    }

    .footer-bottom-area .footer-border {
        padding-top: 12px;
        padding-bottom: 12px;
    }

    /* Professional social icons */
    .footer-social-icons .social-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        /* vertical stack */
        gap: 12px;
    }

    .footer-social-icons .social-list li {
        display: flex;
    }

    .footer-social-icons .social-list a.social-link {
        display: flex;
        align-items: center;
        gap: 10px;
        width: 100%;
        padding: 8px 10px;
        border-radius: 8px;
        text-decoration: none;
        transition: color .2s ease, background-color .2s ease;
        color: #222;
        /* default for light footers */
    }

    /* Color adjustments for dark footer backgrounds */
    .footer-bg .footer-social-icons .social-list a.social-link {
        color: #fff;
    }

    .footer-social-icons .social-list a.social-link:hover,
    .footer-social-icons .social-list a.social-link:focus {
        color: #00A0A8;
    }

    /* Background hover for entire row (light vs dark) */
    .footer-social-icons .social-list a.social-link:hover {
        background: rgba(0, 0, 0, 0.06);
    }

    .footer-bg .footer-social-icons .social-list a.social-link:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    .footer-social-icons .social-list .social-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(0, 0, 0, 0.15);
        /* default (light) */
        background: transparent;
        transition: all .2s ease;
    }

    .footer-bg .footer-social-icons .social-list .social-icon {
        border-color: rgba(255, 255, 255, 0.25);
    }

    .footer-social-icons .social-list .social-icon i {
        color: inherit;
    }

    .footer-social-icons .social-list a.social-link:hover .social-icon,
    .footer-social-icons .social-list a.social-link:focus .social-icon,
    .footer-social-icons .social-list a.social-link:active .social-icon {
        background: #00A0A8;
        border-color: #00A0A8;
    }

    .footer-social-icons .social-list a.social-link:hover .social-icon i,
    .footer-social-icons .social-list a.social-link:focus .social-icon i,
    .footer-social-icons .social-list a.social-link:active .social-icon i {
        color: #fff;
    }

    .footer-social-icons .social-list i {
        font-size: 16px;
        line-height: 1;
    }

    .footer-social-icons .social-list .social-label {
        font-size: 14px;
    }

    /* Center the copyright line */
    .footer-copy-right,
    .footer-copy-right p {
        text-align: center;
    }
</style>

<!-- JS here -->

<!-- All JS Custom Plugins Link Here here -->
<script src="Home/assets/js/vendor/modernizr-3.5.0.min.js"></script>
<!-- Jquery, Popper, Bootstrap -->
<script src="Home/assets/js/vendor/jquery-1.12.4.min.js"></script>
<script src="Home/assets/js/popper.min.js"></script>
<script src="Home/assets/js/bootstrap.min.js"></script>
<!-- Jquery Mobile Menu -->
<script src="Home/assets/js/jquery.slicknav.min.js"></script>

<!-- Jquery Slick , Owl-Carousel Plugins -->
<script src="Home/assets/js/owl.carousel.min.js"></script>
<script src="Home/assets/js/slick.min.js"></script>
<script src="Home/assets/js/price_rangs.js"></script>

<!-- One Page, Animated-HeadLin -->
<script src="Home/assets/js/wow.min.js"></script>
<script src="Home/assets/js/animated.headline.js"></script>
<script src="Home/assets/js/jquery.magnific-popup.js"></script>

<!-- Scrollup, nice-select, sticky -->
<script src="Home/assets/js/jquery.scrollUp.min.js"></script>
<script src="Home/assets/js/jquery.nice-select.min.js"></script>
<script src="Home/assets/js/jquery.sticky.js"></script>

<!-- contact js -->
<script src="Home/assets/js/contact.js"></script>
<script src="Home/assets/js/jquery.form.js"></script>
<script src="Home/assets/js/jquery.validate.min.js"></script>
<script src="Home/assets/js/mail-script.js"></script>
<script src="Home/assets/js/jquery.ajaxchimp.min.js"></script>

<!-- Jquery Plugins, main Jquery -->
<script src="Home/assets/js/plugins.js"></script>
<script src="Home/assets/js/main.js"></script>
<!-- script for blocking and unblocking users -->
<script>
    setTimeout(function() {
        var successMsg = document.getElementById('successMessage');
        if (successMsg) {
            successMsg.style.display = 'none';
        }
        var errorMsg = document.getElementById('errorMessage');
        if (errorMsg) {
            errorMsg.style.display = 'none';
        }
    }, 2000);
</script>
<!-- script for Management automatically generated -->
<script>
    function checkADDPassword() {
        const password = document.getElementById("password").value;
        // Show checklist only when password has content
        const checklist = document.getElementById("password-checklist");
        if (password.length > 0) {
            checklist.style.display = "block";
        } else {
            checklist.style.display = "none";
        }
        // Validate each condition
        document.getElementById("lower").style.color = /[a-z]/.test(password) ? "green" : "red";
        document.getElementById("lower").innerText = /[a-z]/.test(password) ? "✅ One lowercase letter" : "❌ One lowercase letter";
        document.getElementById("upper").style.color = /[A-Z]/.test(password) ? "green" : "red";
        document.getElementById("upper").innerText = /[A-Z]/.test(password) ? "✅ One uppercase letter" : "❌ One uppercase letter";
        document.getElementById("special").style.color = /[@#$%^&+=!]/.test(password) ? "green" : "red";
        document.getElementById("special").innerText = /[@#$%^&+=!]/.test(password) ? "✅ One special character (@#$%^&+=!)" : "❌ One special character (@#$%^&+=!)";
        document.getElementById("length").style.color = password.length >= 8 ? "green" : "red";
        document.getElementById("length").innerText = password.length >= 8 ? "✅ At least 8 characters" : "❌ At least 8 characters";
    }
</script>
<script>
    function checkPassword() {
        const password = document.getElementById("new_password").value;
        const checklist = document.getElementById("password-checklist");
        // Show checklist if user types something, hide otherwise
        checklist.style.display = password.length > 0 ? "block" : "none";
        // Check lowercase
        const hasLower = /[a-z]/.test(password);
        document.getElementById("lower").style.color = hasLower ? "green" : "red";
        document.getElementById("lower").innerText = hasLower ? "✅ One lowercase letter" : "❌ One lowercase letter";
        // Check uppercase
        const hasUpper = /[A-Z]/.test(password);
        document.getElementById("upper").style.color = hasUpper ? "green" : "red";
        document.getElementById("upper").innerText = hasUpper ? "✅ One uppercase letter" : "❌ One uppercase letter";
        // Check special character
        const hasSpecial = /[@#$%^&+=!]/.test(password);
        document.getElementById("special").style.color = hasSpecial ? "green" : "red";
        document.getElementById("special").innerText = hasSpecial ? "✅ One special character (@#$%^&+=!)" : "❌ One special character (@#$%^&+=!)";
        // Check length
        const hasLength = password.length >= 8;
        document.getElementById("length").style.color = hasLength ? "green" : "red";
        document.getElementById("length").innerText = hasLength ? "✅ At least 8 characters" : "❌ At least 8 characters";
    }
</script>
</body>

</html>