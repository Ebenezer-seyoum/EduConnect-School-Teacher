<?php
// Load vacancy
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$v = $id ? getVacancyById($id) : null;
if (!$v || (int)$v['created_by'] !== (int)$_SESSION['uid']) {
    echo '<div class="alert alert-warning">Vacancy not found.</div>';
    return;
}

$success = $error = '';
// on submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_vacancy']) && (int)$_POST['edit_vacancy'] === 1) {
    $data = [
        'title' => $_POST['title'] ?? $v['title'],
        'description' => $_POST['description'] ?? $v['description'],
        'salary' => $_POST['salary'] ?? $v['salary'],
        'location' => $_POST['location'] ?? $v['location'],
        'employment_type' => $_POST['employment_type'] ?? $v['employment_type'],
        'contact_email' => $_POST['contact_email'] ?? $v['contact_email'],
        'contact_phone' => $_POST['contact_phone'] ?? $v['contact_phone'],
        'contact_address' => $_POST['contact_address'] ?? $v['contact_address'],
    ];
    if (updateVacancy($id, $data)) {
        $success = 'Updated successfully';
        $v = getVacancyById($id);
    } else {
        $error = 'Update failed';
    }
}

$employmentTypes = ['Full-time', 'Part-time', 'Contract', 'Temporary', 'Internship', 'Other'];
function f($s)
{
    return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<?php if ($success) { ?><div class="alert alert-success"><?php echo $success; ?></div><?php } ?>
<?php if ($error) { ?><div class="alert alert-danger"><?php echo $error; ?></div><?php } ?>

<form method="post" class="row g-3">
    <input type="hidden" name="edit_vacancy" value="1" />
    <div class="col-md-8">
        <label class="form-label">School Name</label>
        <input type="text" name="title" class="form-control" value="<?php echo f($v['title']); ?>" />
    </div>
    <div class="col-md-4">
        <label class="form-label">Employment Type</label>
        <select name="employment_type" class="form-select">
            <?php foreach ($employmentTypes as $opt) { ?><option value="<?php echo f($opt); ?>" <?php echo ($v['employment_type'] === $opt) ? 'selected' : ''; ?>><?php echo f($opt); ?></option><?php } ?>
        </select>
    </div>
    <div class="col-12">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="6"><?php echo f($v['description']); ?></textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label">Salary</label>
        <input type="text" name="salary" class="form-control" value="<?php echo f($v['salary']); ?>" />
    </div>
    <div class="col-md-4">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control" value="<?php echo f($v['location']); ?>" />
    </div>
    <div class="col-md-4">
        <label class="form-label">Contact Email</label>
        <input type="email" name="contact_email" class="form-control" value="<?php echo f($v['contact_email']); ?>" />
    </div>
    <div class="col-md-6">
        <label class="form-label">Contact Phone</label>
        <input type="text" name="contact_phone" class="form-control" value="<?php echo f($v['contact_phone']); ?>" />
    </div>
    <div class="col-md-6">
        <label class="form-label">Contact Address</label>
        <input type="text" name="contact_address" class="form-control" value="<?php echo f($v['contact_address']); ?>" />
    </div>
    <div class="col-12">
        <button class="btn btn-primary" type="submit">Save Changes</button>
    </div>
</form>