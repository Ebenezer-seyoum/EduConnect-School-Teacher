<?php
include 'login/loginHeader.php';
?>
<?php
$username = $password = "";
$username_err = $password_err = $all_err = "";
$test = true;

if (isset($_POST["login"]) and ($_SERVER["REQUEST_METHOD"] == "POST")) {
    // Validate username
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
        $password_err = "Please enter a valid password";
        $test = false;
    } else {
        $password = $_POST["password"];
    }

    if ($test == true) {
        $user_data = checkUserCredentials($username, $password);

        if ($user_data) {
            // User found in users table
            if ($user_data['user_status'] == 2) {
                $all_err = "This account is deactivated. Please contact the admin.";
            } else {
                $_SESSION["uid"] = $user_data['uid'];
                $roleName = $user_data['user_type'];

                if ($roleName === "admin") {
                    header('location: admin/admin.php');
                    exit;
                } else if ($roleName === "teacher") {
                    header('location: teacher/teacher.php');
                    exit;
                }
            }
        } else {
            $all_err = "Incorrect username or password.";
        }
    }
}
?>
<main class="container" style="margin-top: 60px; margin-bottom: 60px; max-width: 500px;">
    <h2 class="text-center mb-4">Login</h2>
    <?php if (!empty($all_err)) { ?>
        <div class="form-control bg-danger mb-3 text-white"><?php echo $all_err; ?></div>
    <?php } ?>
    <form action="" method="POST" class="p-4 shadow rounded bg-white">
        <div class="form-group mb-3">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username">
            <?php if (!empty($username_err)) { ?><span class="text-danger"><?php echo $username_err; ?></span><?php } ?>
        </div>
        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password">
            <?php if (!empty($password_err)) { ?><span class="text-danger"><?php echo $password_err; ?></span><?php } ?>
        </div>
        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
        <div class="mt-3 text-center">
            <a href="register.php">Don't have an account? Register</a>
        </div>
    </form>
</main>

<?php
include 'login/loginFooter.php';
?>