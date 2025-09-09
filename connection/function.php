<?php
// --- Encryption configuration ---
define('ENCRYPT_METHOD', 'AES-256-CBC');
define('SECRET_KEY', 'your-strong-secret-key');
define('SECRET_IV', 'your-strong-secret-iv');
function encryptPassword($password)
{
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    return openssl_encrypt($password, ENCRYPT_METHOD, $key, 0, $iv);
}
function decryptPassword($encryptedPassword)
{
    $key = hash('sha256', SECRET_KEY);
    $iv = substr(hash('sha256', SECRET_IV), 0, 16);
    return openssl_decrypt($encryptedPassword, ENCRYPT_METHOD, $key, 0, $iv);
}
// --- End of Encryption configuration ---

function basics($data)
{
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
function validateIdNumber($data)
{
    $data = basics($data);
    if (preg_match("/^[a-zA-Z0-9 \/ `|]*$/", $data))
        return 1;
    else
        return 0;
}
//validate date 
function checkDateOfBirth($dob)
{
    // Check if the date is in YYYY-MM-DD format
    $d = DateTime::createFromFormat('Y-m-d', $dob);
    if ($d && $d->format('Y-m-d') === $dob) {
        return true;
    } else {
        return false;
    }
}
//validate number
function validateNumber($data)
{
    $data = basics($data);
    if (preg_match("/^[0-9 \/]*$/", $data))
        return 1;
    else
        return 0;
}
//validatePhoneNumber
function validatePhoneNumber($data)
{
    $data = basics($data);
    if (preg_match("/^(\+?\d{10,15})$/", $data))
        return 1;
    else
        return 0;
}
//validateProfilePicture
function validateProfilePicture($data)
{
    $maxFileSize = 5 * 1024 * 1024;
    $allowedMimeTypes = ['image/jpg', 'image/png', 'image/jpeg', 'image/gif'];
    if ($data['error'] !== UPLOAD_ERR_OK) {
        return "An error occurred while uploading the file. Error code: " . $data['error'];
    }
    if ($data['size'] > $maxFileSize) {
        return "File size exceeds the maximum limit of 5MB.";
    }
    $mimeType = mime_content_type($data['tmp_name']);
    if (!in_array($mimeType, $allowedMimeTypes)) {
        return "Invalid file type. Only JPEG, PNG, and GIF are allowed.";
    }
    if (!getimagesize($data['tmp_name'])) {
        return "The file is not a valid image.";
    }
    return true;
}
//validateUploadedFile
function validateUploadedFile($file, $allowedMimeTypes = ['application/pdf', 'image/jpeg', 'image/png'], $maxFileSize = 10 * 1024 * 1024)
{
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "An error occurred during file upload. Error code: " . $file['error'];
    }
    if ($file['size'] > $maxFileSize) {
        return "File size exceeds the maximum limit of " . ($maxFileSize / (1024 * 1024)) . "MB.";
    }
    $mimeType = mime_content_type($file['tmp_name']);
    if (!in_array($mimeType, $allowedMimeTypes)) {
        return "Invalid file type. Allowed types are: " . implode(", ", $allowedMimeTypes) . ".";
    }
    return true;
}
//validateFileName
function validateName($data)
{
    $data = basics($data);
    if (preg_match("/^[a-zA-Z]*$/", $data))
        return 1;
    else
        return 0;
}
//validate email
function validateEmail($email)
{
    $email = trim($email);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    $parts = explode('@', $email);
    if (count($parts) != 2) {
        return false;
    }
    return true;
}

//validate gender
function validateGender($data)
{
    $data = basics($data);
    if ($data == "M" or $data == "F")
        return 1;
    else
        return 0;
}

//validate password
function validatePassword($data)
{
    $data = basics($data);
    if (preg_match("/^[a-zA-Z0-9 @#$]*$/", $data))
        return 1;
    else
        return 0;
}
//password strength
function isStrongPassword($password)
{
    $errors = [];
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "one lowercase letter";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "one uppercase letter";
    }
    if (!preg_match('/[@#$%^&+=!]/', $password)) {
        $errors[] = "one special character (@#$%^&+=!)";
    }
    if (strlen($password) < 8) {
        $errors[] = "at least 8 characters";
    }

    if (!empty($errors)) {
        return "Password must contain " . implode(", ", $errors) . ".";
    }
    return true;
}

function checkUserCredentials($username, $inputPassword)
{
    global $conn;

    // Prevent SQL injection
    $username = mysqli_real_escape_string($conn, $username);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE user_name = '$username'");
    $row = mysqli_fetch_assoc($query);

    if ($row) {
        // Decrypt stored password
        $decryptedPassword = decryptPassword($row['password']);

        // Compare with input password
        if ($inputPassword === $decryptedPassword) {
            return $row; // Login successful
        }
    }

    return false; // Login failed
}

//user password get
function getUserPassword($conn, $user_id)
{
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $query = "SELECT password FROM users WHERE uid = '$user_id'";
    $result = mysqli_query($conn, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return $row['password'];
    } else {
        return false;
    }
}
//change password
function changeUserPassword($conn, $user_id, $old_password, $new_password)
{
    $stored_password = getUserPassword($conn, $user_id);
    if ($stored_password === false) {
        return ['status' => false, 'message' => 'User not found.'];
    }
    if (encryptPassword($old_password) !== $stored_password) {
        return ['status' => false, 'message' => 'Old password is incorrect.'];
    }
    $new_encrypted = encryptPassword($new_password);
    $update_query = "UPDATE users SET password = '$new_encrypted' WHERE uid = '$user_id'";
    if (mysqli_query($conn, $update_query)) {
        return ['status' => true, 'message' => 'Password changed successfully.'];
    } else {
        return ['status' => false, 'message' => 'Error updating password.'];
    }
}

//validate check password
function comparePasswords($data1, $data2)
{
    $data1 = basics($data1);
    $data2 = basics($data2);
    if ($data1 == $data2)
        return 1;
    else
        return 0;
}
//validate user type
function validateUserType($data)
{
    $data = basics($data);
    if (preg_match("/^[a-zA-Z0-9 _]*$/", $data))
        return 1;
    else
        return 0;
}
//validate class type
function validateClassType($data)
{
    $data = basics($data);
    if (preg_match("/^[a-zA-Z0-9 _]*$/", $data))
        return 1;
    else
        return 0;
}

function checkUserByUsername($data)
{
    global $conn;
    $query = mysqli_query($conn, "select uid from users where user_name ='$data'");
    $result = mysqli_num_rows($query);
    return $result;
}

function getRoleByUsername($data)
{
    global $conn;

    if (mysqli_num_rows(mysqli_query($conn, "select * from users")) > 0) {
        $query = mysqli_query($conn, "select uid, password, user_type from users where user_name='$data'");
        $result = mysqli_fetch_array($query);
        return $result;
    } else {
        echo "no user found";
    }
}


function getRoleByPassword($data)
{
    global $conn;
    if (mysqli_num_rows(mysqli_query($conn, "select * from users")) > 0) {
        $query = mysqli_query($conn, "select uid, user_type, user_status from users where password='$data'");
        $result = mysqli_fetch_array($query);
        return $result;
    } else {
        echo "no user found";
    }
}

function updateUserStatus($status, $uid)
{
    global $conn;
    $status = (int)$status;
    $uid = (int)$uid;
    $query = "UPDATE users SET user_status = $status WHERE uid = $uid";
    if (mysqli_query($conn, $query)) {
        return 1;
    } else {
        return 0;
    }
}
function addUser($fullname, $gender, $email, $phone, $username, $password, $user_type)
{
    global $conn;

    // sanitize inputs
    $fullname = mysqli_real_escape_string($conn, $fullname);
    $gender = mysqli_real_escape_string($conn, $gender);
    $email = mysqli_real_escape_string($conn, $email);
    $phone = mysqli_real_escape_string($conn, $phone);
    $username = mysqli_real_escape_string($conn, $username);
    $user_type = mysqli_real_escape_string($conn, $user_type);

    // encrypt password
    $encrypted_password = encryptPassword($password);
    $encrypted_password = mysqli_real_escape_string($conn, $encrypted_password);

    // default status (1 = active)
    $user_status = 1;

    // Try to match existing schema: some queries use user_name (not username)
    $query = "INSERT INTO users (full_name, gender, email, phone, user_name, password, user_type, user_status)
              VALUES ('$fullname', '$gender', '$email', '$phone', '$username', '$encrypted_password', '$user_type', $user_status)";

    if (mysqli_query($conn, $query)) {
        return 1;
    } else {
        return 0;
    }
}

function EmailExist($data)
{
    global $conn;
    $query = mysqli_query($conn, "select email from users where email ='$data'");
    $result = mysqli_num_rows($query);
    return $result;
}
function UserNameExist($data)
{
    global $conn;
    $query = mysqli_query($conn, "select user_name from users where user_name ='$data'");
    $result = mysqli_num_rows($query);
    return $result;
}
function getUserByID($data)
{
    global $conn;
    $query = mysqli_query($conn, "SELECT * FROM users WHERE uid = '$data'");
    $result = mysqli_fetch_array($query);
    if ($result && isset($result["password"])) {
        $result["password"] = decryptPassword($result["password"]);
    }
    return $result;
}

// Add Vacancy (direct query, escaped). Returns 1 on success, 0 on failure
function addVacancy($title, $description, $salary, $location, $employment_type, $contact_email, $contact_phone, $contact_address, $created_by)
{
    global $conn;

    // sanitize inputs
    $title = mysqli_real_escape_string($conn, $title);
    $description = mysqli_real_escape_string($conn, $description);
    $salary = mysqli_real_escape_string($conn, $salary);
    $location = mysqli_real_escape_string($conn, $location);
    $employment_type = mysqli_real_escape_string($conn, $employment_type);
    $contact_email = mysqli_real_escape_string($conn, $contact_email);
    $contact_phone = mysqli_real_escape_string($conn, $contact_phone);
    $contact_address = mysqli_real_escape_string($conn, $contact_address);
    $created_by = (int)$created_by;

    $sql = "INSERT INTO school_vacancies (title, description, salary, location, employment_type, contact_email, contact_phone, contact_address, created_by)
            VALUES ('$title', '$description', '$salary', '$location', '$employment_type', '$contact_email', '$contact_phone', '$contact_address', $created_by)";

    if (mysqli_query($conn, $sql)) {
        return 1;
    }
    return 0;
}

// ---- Vacancy helpers ----
function getVacancies($limit = 50, $createdBy = null)
{
    global $conn;
    $limit = (int)$limit;
    $where = '';
    if ($createdBy !== null) {
        $createdBy = (int)$createdBy;
        $where = "WHERE created_by = $createdBy";
    }
    $sql = "SELECT * FROM school_vacancies $where ORDER BY sid DESC LIMIT $limit";
    $res = mysqli_query($conn, $sql);
    $rows = [];
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = $r;
        }
    }
    return $rows;
}

function getVacancyById($id)
{
    global $conn;
    $id = (int)$id;
    $res = mysqli_query($conn, "SELECT * FROM school_vacancies WHERE sid = $id LIMIT 1");
    if ($res && mysqli_num_rows($res) === 1) {
        return mysqli_fetch_assoc($res);
    }
    return null;
}

function updateVacancy($id, $data)
{
    global $conn;
    $id = (int)$id;
    $fields = [];
    foreach (['title', 'description', 'salary', 'location', 'employment_type', 'contact_email', 'contact_phone', 'contact_address'] as $k) {
        if (isset($data[$k])) {
            $v = mysqli_real_escape_string($conn, $data[$k]);
            $fields[] = "$k='$v'";
        }
    }
    if (empty($fields)) return false;
    $sql = "UPDATE school_vacancies SET " . implode(',', $fields) . " WHERE id = $id";
    return mysqli_query($conn, $sql) ? true : false;
}

function deleteVacancy($id, $createdBy = null)
{
    global $conn;
    $id = (int)$id;
    $where = "id = $id";
    if ($createdBy !== null) {
        $createdBy = (int)$createdBy;
        $where .= " AND created_by = $createdBy";
    }
    $sql = "DELETE FROM school_vacancies WHERE $where";
    return mysqli_query($conn, $sql) ? true : false;
}

// ---- Notifications helpers ----
function ensureNotificationsTable()
{ /* no-op: tables created manually */
}

// ---- Teacher profile helpers ----
function ensureTeacherProfilesTable()
{ /* no-op: tables created manually */
}

function getTeacherProfileByUserId($uid)
{
    global $conn;
    ensureTeacherProfilesTable();
    $uid = (int)$uid;
    $res = mysqli_query($conn, "SELECT * FROM teacher_profiles WHERE user_id = $uid LIMIT 1");
    if ($res && mysqli_num_rows($res) === 1) return mysqli_fetch_assoc($res);
    return null;
}

function createTeacherProfile($uid, $data)
{
    global $conn;
    ensureTeacherProfilesTable();
    $uid = (int)$uid;
    $full_name = mysqli_real_escape_string($conn, $data['full_name']);
    $bio = mysqli_real_escape_string($conn, $data['bio'] ?? '');
    $years_experience = (int)($data['years_experience'] ?? 0);
    $expected_salary = $data['expected_salary'] !== '' ? (float)$data['expected_salary'] : 'NULL';
    $address = mysqli_real_escape_string($conn, $data['address'] ?? '');
    $contact_email = mysqli_real_escape_string($conn, $data['contact_email'] ?? '');
    $contact_phone = mysqli_real_escape_string($conn, $data['contact_phone'] ?? '');
    $profile_picture = mysqli_real_escape_string($conn, $data['profile_picture'] ?? '');
    $cv = mysqli_real_escape_string($conn, $data['cv'] ?? '');

    $expected_salary_sql = ($expected_salary === 'NULL') ? 'NULL' : sprintf("'%.2f'", $expected_salary);

    $sql = "INSERT INTO teacher_profiles (user_id, profile_picture, full_name, bio, years_experience, expected_salary, cv, address, contact_email, contact_phone)
            VALUES ($uid, '$profile_picture', '$full_name', '$bio', $years_experience, $expected_salary_sql, '$cv', '$address', '$contact_email', '$contact_phone')";
    return mysqli_query($conn, $sql) ? true : false;
}

function updateTeacherProfile($uid, $data)
{
    global $conn;
    ensureTeacherProfilesTable();
    $uid = (int)$uid;
    $fields = [];
    foreach (['full_name', 'bio', 'address', 'contact_email', 'contact_phone'] as $k) {
        if (isset($data[$k])) {
            $fields[] = $k . "='" . mysqli_real_escape_string($conn, $data[$k]) . "'";
        }
    }
    if (isset($data['years_experience'])) {
        $fields[] = 'years_experience=' . (int)$data['years_experience'];
    }
    if (array_key_exists('expected_salary', $data)) {
        if ($data['expected_salary'] === '' || $data['expected_salary'] === null) {
            $fields[] = 'expected_salary=NULL';
        } else {
            $fields[] = 'expected_salary=' . sprintf("'%.2f'", (float)$data['expected_salary']);
        }
    }
    if (!empty($data['profile_picture'])) {
        $fields[] = "profile_picture='" . mysqli_real_escape_string($conn, $data['profile_picture']) . "'";
    }
    if (!empty($data['cv'])) {
        $fields[] = "cv='" . mysqli_real_escape_string($conn, $data['cv']) . "'";
    }
    if (empty($fields)) return false;
    $sql = "UPDATE teacher_profiles SET " . implode(',', $fields) . " WHERE user_id = $uid";
    return mysqli_query($conn, $sql) ? true : false;
}

function getTeacherProfiles($limit = 100)
{
    global $conn;
    ensureTeacherProfilesTable();
    $limit = (int)$limit;
    $rows = [];
    $sql = "SELECT user_id, full_name, profile_picture, bio, years_experience, expected_salary, address, cv FROM teacher_profiles ORDER BY user_id DESC LIMIT $limit";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = $r;
        }
    }
    return $rows;
}

function getAdminUids()
{
    global $conn;
    $uids = [];
    // Only select active admins if user_status exists
    $sql = "SELECT uid FROM users WHERE user_type='admin'";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $uids[] = (int)$r['uid'];
        }
    }
    return $uids;
}

function addNotification($adminUid, $vacancyId, $senderName, $senderContact, $message)
{
    global $conn;
    ensureNotificationsTable();
    $adminUid = (int)$adminUid;
    $vacancyId = (int)$vacancyId;
    $senderName = mysqli_real_escape_string($conn, $senderName ?? '');
    $senderContact = mysqli_real_escape_string($conn, $senderContact ?? '');
    $message = mysqli_real_escape_string($conn, $message ?? '');
    $sql = "INSERT INTO notifications (admin_uid, vacancy_id, sender_name, sender_contact, message) VALUES ($adminUid, $vacancyId, '$senderName', '$senderContact', '$message')";
    return mysqli_query($conn, $sql) ? true : false;
}

function getUnreadNotificationCount($adminUid)
{
    global $conn;
    ensureNotificationsTable();
    $adminUid = (int)$adminUid;
    $sql = "SELECT COUNT(*) AS c FROM notifications WHERE uid = $adminUid AND is_read = 0";
    $res = mysqli_query($conn, $sql);
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        return (int)($row['c'] ?? 0);
    }
    return 0;
}

function getNotifications($adminUid, $limit = 10)
{
    global $conn;
    ensureNotificationsTable();
    $adminUid = (int)$adminUid;
    $limit = (int)$limit;
    $sql = "SELECT * FROM notifications WHERE uid = $adminUid ORDER BY is_read ASC, id DESC LIMIT $limit";
    $rows = [];
    $res = mysqli_query($conn, $sql);
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = $r;
        }
    }
    return $rows;
}

function getNotificationById($id, $adminUid)
{
    global $conn;
    ensureNotificationsTable();
    $id = (int)$id;
    $adminUid = (int)$adminUid;
    $res = mysqli_query($conn, "SELECT * FROM notifications WHERE id = $id AND uid = $adminUid LIMIT 1");
    if ($res && mysqli_num_rows($res) === 1) return mysqli_fetch_assoc($res);
    return null;
}

function markNotificationRead($id, $adminUid)
{
    global $conn;
    ensureNotificationsTable();
    $id = (int)$id;
    $adminUid = (int)$adminUid;
    $sql = "UPDATE notifications SET is_read = 1 WHERE id = $id AND uid = $adminUid";
    return mysqli_query($conn, $sql) ? true : false;
}

// ---- Feedback helpers ----
function ensureFeedbackTable()
{
    // Attempt to create feedback table if it doesn't exist
    // Columns: id, name, email, subject, message, created_at
    global $conn;
    $sql = "CREATE TABLE IF NOT EXISTS feedback (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(150) NOT NULL,
                email VARCHAR(200) NOT NULL,
                subject VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    @mysqli_query($conn, $sql);
}

function addFeedback($name, $email, $subject, $message)
{
    global $conn;
    ensureFeedbackTable();

    // basic server-side validation using existing helpers
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        return [false, 'All fields are required.'];
    }
    if (!validateEmail($email)) {
        return [false, 'Invalid email address.'];
    }
    // Sanitize and insert
    $name = mysqli_real_escape_string($conn, basics($name));
    $email = mysqli_real_escape_string($conn, basics($email));
    $subject = mysqli_real_escape_string($conn, trim($subject));
    $message = mysqli_real_escape_string($conn, trim($message));

    $sql = "INSERT INTO feedback (name, email, subject, message) VALUES ('$name', '$email', '$subject', '$message')";
    if (mysqli_query($conn, $sql)) {
        return [true, 'Feedback submitted'];
    }
    return [false, 'Database error while saving feedback.'];
}

function getFeedbacks($limit = 200)
{
    global $conn;
    ensureFeedbackTable();
    $limit = (int)$limit;
    $rows = [];
    $res = mysqli_query($conn, "SELECT id, name, email, subject, message, created_at FROM feedback ORDER BY id DESC LIMIT $limit");
    if ($res) {
        while ($r = mysqli_fetch_assoc($res)) {
            $rows[] = $r;
        }
    }
    return $rows;
}

function getFeedbackById($id)
{
    global $conn;
    ensureFeedbackTable();
    $id = (int)$id;
    $res = mysqli_query($conn, "SELECT id, name, email, subject, message, created_at FROM feedback WHERE id = $id LIMIT 1");
    if ($res && mysqli_num_rows($res) === 1) {
        return mysqli_fetch_assoc($res);
    }
    return null;
}
