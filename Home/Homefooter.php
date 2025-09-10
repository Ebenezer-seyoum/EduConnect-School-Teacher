<footer>
    <!-- Script for hiding messages -->
    <script>
        setTimeout(function() {
            var successMsg = document.getElementById('successMessage');
            if (successMsg) successMsg.style.display = 'none';
            var errorMsg = document.getElementById('errorMessage');
            if (errorMsg) errorMsg.style.display = 'none';
        }, 2000);
    </script>

    <!-- Password checklist scripts -->
    <script>
        function checkADDPassword() {
            const password = document.getElementById("password").value;
            const checklist = document.getElementById("password-checklist");
            checklist.style.display = password.length > 0 ? "block" : "none";
            document.getElementById("lower").style.color = /[a-z]/.test(password) ? "green" : "red";
            document.getElementById("lower").innerText = /[a-z]/.test(password) ? "✅ One lowercase letter" : "❌ One lowercase letter";
            document.getElementById("upper").style.color = /[A-Z]/.test(password) ? "green" : "red";
            document.getElementById("upper").innerText = /[A-Z]/.test(password) ? "✅ One uppercase letter" : "❌ One uppercase letter";
            document.getElementById("special").style.color = /[@#$%^&+=!]/.test(password) ? "green" : "red";
            document.getElementById("special").innerText = /[@#$%^&+=!]/.test(password) ? "✅ One special character (@#$%^&+=!)" : "❌ One special character (@#$%^&+=!)";
            document.getElementById("length").style.color = password.length >= 8 ? "green" : "red";
            document.getElementById("length").innerText = password.length >= 8 ? "✅ At least 8 characters" : "❌ At least 8 characters";
        }

        function checkPassword() {
            const password = document.getElementById("new_password").value;
            const checklist = document.getElementById("password-checklist");
            checklist.style.display = password.length > 0 ? "block" : "none";
            const hasLower = /[a-z]/.test(password);
            document.getElementById("lower").style.color = hasLower ? "green" : "red";
            document.getElementById("lower").innerText = hasLower ? "✅ One lowercase letter" : "❌ One lowercase letter";
            const hasUpper = /[A-Z]/.test(password);
            document.getElementById("upper").style.color = hasUpper ? "green" : "red";
            document.getElementById("upper").innerText = hasUpper ? "✅ One uppercase letter" : "❌ One uppercase letter";
            const hasSpecial = /[@#$%^&+=!]/.test(password);
            document.getElementById("special").style.color = hasSpecial ? "green" : "red";
            document.getElementById("special").innerText = hasSpecial ? "✅ One special character (@#$%^&+=!)" : "❌ One special character (@#$%^&+=!)";
            const hasLength = password.length >= 8;
            document.getElementById("length").style.color = hasLength ? "green" : "red";
            document.getElementById("length").innerText = hasLength ? "✅ At least 8 characters" : "❌ At least 8 characters";
        }
    </script>
    <!-- Footer Start-->
    <div class="footer-area footer-bg footer-padding" style="background-color:#0a2540; color:#ffffff; width:100%;">
        <div class="container" style="padding:50px 15px;">
            <div class="row g-4 align-items-start">
                <!-- About Us -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="single-footer-caption mb-50">
                        <div class="footer-tittle">
                            <h4 style="color:#ffffff; font-size:22px; font-weight:bold;">About Us</h4>
                            <div class="footer-pera" style="font-size:16px; line-height:1.7;">
                                <p>Jobir Jobs is a simple, modern platform that connects schools with qualified teachers. Post vacancies, discover talent, and manage hiring in one place.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Contact Info -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="single-footer-caption mb-50">
                        <div class="footer-tittle">
                            <h4 style="color:#ffffff; font-size:22px; font-weight:bold;">Contact Info</h4>
                            <ul style="font-size:16px; line-height:1.7; list-style:none; padding:0;">
                                <li>
                                    <p>Address: Your address goes here, your demo address.</p>
                                </li>
                                <li><a href="#">Phone : +8880 44338899</a></li>
                                <li><a href="#">Email : info@colorlib.com</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Important Link -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="single-footer-caption mb-50">
                        <div class="footer-tittle">
                            <h4 style="color:#ffffff; font-size:22px; font-weight:bold;">Important Links</h4>
                            <ul style="font-size:16px; line-height:1.7; list-style:none; padding:0;">
                                <li><a href="#">Home</a></li>
                                <li><a href="#">About</a></li>
                                <li><a href="#">School vacancy</a></li>
                                <li><a href="#">Find teachers</a></li>
                                <li><a href="#">Contact</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Social Media -->
                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
                    <div class="single-footer-caption mb-50">
                        <div class="footer-tittle">
                            <h4 style="color:#ffffff; font-size:22px; font-weight:bold;">Social Media</h4>
                            <ul class="social-list" style="list-style:none; padding:0; font-size:16px;">
                                <li style="margin-bottom:10px;">
                                    <a href="#" class="social-link"><i class="fab fa-facebook-f"></i> Facebook</a>
                                </li>
                                <li style="margin-bottom:10px;">
                                    <a href="#" class="social-link"><i class="fab fa-twitter"></i> Twitter</a>
                                </li>
                                <li style="margin-bottom:10px;">
                                    <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i> LinkedIn</a>
                                </li>
                                <li style="margin-bottom:10px;">
                                    <a href="#" class="social-link"><i class="fab fa-telegram-plane"></i> Telegram</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- footer-bottom area -->
    <div class="footer-bottom-area footer-bg" style="background-color:#081d33; color:#ffffff; width:100%;">
        <div class="container" style="padding:15px 15px;">
            <div class="footer-border text-center">
                <p style="margin:0; font-size:15px;">
                    &copy; <script>
                        document.write(new Date().getFullYear());
                    </script> All rights reserved · Jobir Jobs · Designed by
                    <a href="https://ebenezerseyoum.me" target="_blank" rel="noopener">Ebenezer Seyoum</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Custom Hover Styles -->
<style>
    .footer-area a,
    .footer-bottom-area a {
        color: #ffffff;
        transition: color 0.3s ease;
        text-decoration: none;
        font-size: 16px;
    }

    .footer-area a:hover,
    .footer-bottom-area a:hover {
        color: #1da1f2;
    }

    .social-list i {
        margin-right: 8px;
        font-size: 18px;
    }

    .social-list li {
        margin-bottom: 10px;
    }
</style>
<style>
    /* Back to Top Button */
    .back-to-top {
        position: fixed;
        right: 20px;
        bottom: 20px;
        background: #0a2540;
        border: 1px solid #0a2540;
        color: #ffffff;
        display: none;
        /* hidden until scroll */
        align-items: center;
        justify-content: center;
        width: 50px;
        height: 50px;
        transition: background 0.3s ease, opacity 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        z-index: 999;
        border-radius: 0;
        text-decoration: none;
    }

    .back-to-top i {
        font-size: 20px;
    }

    .back-to-top:hover {
        background: #12365c;
        border-color: #12365c;
        color: #ffffff;
        text-decoration: none;
    }

    .back-to-top.show {
        display: inline-flex;
    }
</style>
<a href="#" class="btn btn-lg btn-lg-square back-to-top" aria-label="Back to top"><i class="fas fa-arrow-up"></i></a>
<script>
    (function() {
        const btn = document.querySelector('.back-to-top');
        if (!btn) return;

        function toggleBtn() {
            if (window.pageYOffset > 250) {
                btn.classList.add('show');
            } else {
                btn.classList.remove('show');
            }
        }
        window.addEventListener('scroll', toggleBtn);
        toggleBtn();
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    })();
</script>