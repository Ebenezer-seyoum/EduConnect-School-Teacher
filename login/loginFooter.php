   <!-- footer-bottom area -->

   <!-- JavaScript Libraries -->
   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
   <script src="login/assets/lib/wow/wow.min.js"></script>
   <script src="login/assets/lib/easing/easing.min.js"></script>
   <script src="login/assets/lib/waypoints/waypoints.min.js"></script>
   <script src="login/assets/lib/owlcarousel/owl.carousel.min.js"></script>
   <!-- Template Javascript -->
   <script src="js/main.js"></script>
   <!-- Toggle Password Script -->
   <script>
       document.querySelectorAll('.toggle-password').forEach(button => {
           button.addEventListener('click', function() {
               const target = document.getElementById(this.getAttribute('data-target'));
               if (!target) return;
               if (target.type === "password") {
                   target.type = "text";
                   this.innerHTML = '<i class="fa-solid fa-eye-slash"></i>';
               } else {
                   target.type = "password";
                   this.innerHTML = '<i class="fa-solid fa-eye"></i>';
               }
           });
       });
   </script>
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
       }, 3000);
   </script>
   <!-- script for Management automatically generated -->
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

   </html>