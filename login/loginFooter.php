<div class="row pt-5 mt-5 text-center">
         <div class="py-6 px-6 text-center">
    <p class="mb-0 fs-4">Design by <a href="https://ebenezerseyoum.me" class="pe-1 text-primary text-decoration-underline" target="_blank">Ebenezer Seyoum</a></p>
</div>         
        </div>
      </div>
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
        button.addEventListener('click', function () {
            const target = document.getElementById(this.getAttribute('data-target'));
            if (target.type === "password") {
                target.type = "text";
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                target.type = "password";
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
</script>
<!-- script for blocking and unblocking users -->
<script>
    setTimeout(function () {
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
</html>

