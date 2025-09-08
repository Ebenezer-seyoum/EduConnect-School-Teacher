<?php
include 'Home/Homeheader.php';

// declare defaults to avoid undefined notices and track validation state
$fullName = $gender = $email = $phone = $username = $password = $confirmPassword = "";
$fullName_err = $gender_err = $email_err = $phone_err = $username_err = $password_err = $confirmPassword_err = $allErr = $success = "";
$test = true;

if (isset($_POST["register"]) && ($_SERVER["REQUEST_METHOD"] == "POST")) {
    //validate full name
    if (empty($_POST["full_name"])) {
        $fullName_err = "Please enter your full name";
        $test = false;
    } else if (validateIdNumber($_POST["full_name"]) == 0) {
        $fullName_err = "Please enter a valid full name";
        $test = false;
    } else {
        $fullName = $_POST["full_name"];
    }
    //validate gender
    if (empty($_POST["gender"])) {
        $gender_err = "Please select your gender";
        $test = false;
    } else if (validateGender($_POST["gender"]) == 0) {
        $gender_err = "Invalid input";
        $test = false;
    } else {
        $gender = $_POST["gender"];
    }

    // Validate email
    if (empty($_POST["email"])) {
        $email_err = "Please enter your email";
        $test = false;
    } else {
        $email = trim($_POST["email"]);
        if (!validateEmail($email)) {
            $email_err = "Please enter a valid email address (example: user@domain.com)";
            $test = false;
        } else {
            $email = $_POST["email"];
        }
    }
    //validate username
    if (empty($_POST["username"])) {
        $username_err = "Please enter your username";
        $test = false;
    } else if (validateName($_POST["username"]) == 0) {
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
        $password_err = "Please enter a valid password (no invalid symbols)";
        $test = false;
    } else {
        $password = $_POST["password"];
        $strongPassword = isStrongPassword($password);
        if ($strongPassword !== true) {
            $password_err = $strongPassword;
            $test = false;
        }
    }

    //validate password confirmation
    if (empty($_POST["confirm_password"])) {
        $confirmPassword_err = "Please enter your new password";
        $test = false;
    } else if (validatePassword($_POST["confirm_password"]) == 0) {
        $confirmPassword_err = "Please enter valid password";
        $test = false;
    } else if (comparePasswords($_POST["password"], $_POST["confirm_password"]) == 0) {
        $confirmPassword_err = "Password did not match";
    } else {
        $confirmPassword = $_POST["confirm_password"];
    }
    //validate  Phone 
    if (empty($_POST["phone"])) {
        $phone_err = "Please enter your phone number";
        $test = false;
    } else if (validatePhoneNumber($_POST["phone"]) == 0) {
        $phone_err = "Please enter valid phone number";
        $test = false;
    } else {
        $phone = $_POST["phone"];
    }
    if ($test == true) {
        $user_type = 'teacher';
        if (EmailExist($email) == 0 && UserNameExist($username) == 0) {
            if (addUser($fullName, $gender, $email, $phone, $username, $password, $user_type) == 1) {
                $success = "User Successfully registered";
                header('refresh:2');
            } else {
                $allErr = "There was error while registration";
            }
        } else {
            $allErr = "User with this email or username already exists";
        }
    }
}
?>

<main class="container" style="margin-top: 60px; margin-bottom: 60px; max-width: 500px;">
    <h2 class="text-center mb-4">Register</h2>
    <?php if (!empty($success)) { ?>
        <div class=" form-control bg-success"><?php echo $success; ?></div>
    <?php  } ?>
    <?php if (!empty($allErr)) { ?>
        <div class=" form-control bg-danger"><?php echo $allErr; ?></div>
    <?php  } ?>

    <form action="" method="POST" class="p-4 shadow rounded bg-white">
        <div class="form-group mb-3">
            <label for="full_name">Full Name</label>
            <input type="text" class="form-control" id="full_name" name="full_name">
            <span class="text-danger"><?php echo $fullName_err; ?></span>
        </div>
        <div class="form-group mb-3">
            <label for="gender">Gender</label>
            <select class="form-control" id="gender" name="gender">
                <option value="">Select Gender</option>
                <option value="M">Male</option>
                <option value="F">Female</option>
            </select>
            <span class="text-danger"><?php echo $gender_err; ?></span>
        </div>
        <div class="form-group mb-3">
            <label for="phone">Phone</label>
            <input type="text" class="form-control" id="phone" name="phone">
            <span class="text-danger"><?php echo $phone_err; ?></span>
        </div>
        <div class="form-group mb-3">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email">
            <span class="text-danger"><?php echo $email_err; ?></span>
        </div>
        <div class="form-group mb-3">
            <label for="username">Username</label>
            <input type="text" class="form-control" id="username" name="username">
            <span class="text-danger"><?php echo $username_err; ?></span>
        </div>
        <div class="form-group mb-3">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" onkeyup="checkADDPassword()" />
  <ul id="password-checklist" style="list-style: none; padding: 0; display: none;">
    <li id="lower" style="color: red;">❌ One lowercase letter</li>
    <li id="upper" style="color: red;">❌ One uppercase letter</li>
    <li id="special" style="color: red;">❌ One special character (@#$%^&+=!)</li>
    <li id="length" style="color: red;">❌ At least 8 characters</li>
</ul>
            <span class="text-danger"><?php echo $password_err; ?></span>
        </div>
        <div class="form-group mb-3">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
            <span class="text-danger"><?php echo $confirmPassword_err; ?></span>
        </div>
        <button type="submit" name="register" class="btn btn-success w-100">Register</button>
        <div class="mt-3 text-center">
            <a href="login.php">Already have an account? Login</a>
        </div>
    </form>
</main>

<?php
include 'Home/Homefooter.php';
?>