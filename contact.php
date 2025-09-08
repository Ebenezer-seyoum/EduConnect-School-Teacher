<?php
include 'Home/Homeheader.php';
?>
<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 mb-4">
            <h2 class="mb-4">Contact Us</h2>
            <?php if (!empty($_SESSION['flash'])) {
                $f = $_SESSION['flash'];
                unset($_SESSION['flash']); ?>
                <div class="alert alert-<?php echo htmlspecialchars($f['type']); ?>"><?php echo htmlspecialchars($f['msg']); ?></div>
            <?php } ?>
            <form action="contact_process.php" method="POST" class="p-4 shadow rounded bg-white">
                <div class="form-group mb-3">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group mb-3">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group mb-3">
                    <label for="subject">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="form-group mb-3">
                    <label for="message">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="4" required></textarea>
                </div>
                <button type="submit" class="btn btn-info w-100">Send Message</button>
            </form>
        </div>
        <div class="col-lg-5">
            <div class="p-4 shadow rounded bg-white h-100">
                <h5 class="mb-3">Contact Information</h5>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><strong>Address:</strong> Your address goes here, your demo address.</li>
                    <li class="mb-2"><strong>Phone:</strong> +8880 44338899</li>
                    <li class="mb-2"><strong>Email:</strong> info@colorlib.com</li>
                    <li class="mb-0"><strong>Hours:</strong> Mon–Fri, 9:00–17:00</li>
                </ul>
            </div>
        </div>
    </div>

</main>
<?php
include 'Home/Homefooter.php';
?>