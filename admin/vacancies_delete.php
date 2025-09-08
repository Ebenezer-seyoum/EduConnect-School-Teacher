<?php
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$v = $id ? getVacancyById($id) : null;
if (!$v || (int)$v['created_by'] !== (int)$_SESSION['uid']) {
    echo '<div class="alert alert-warning">Vacancy not found.</div>';
    return;
}

$deleted = false;
if (isset($_POST['confirm_delete']) && (int)$_POST['confirm_delete'] === 1) {
    if (deleteVacancy($id, $_SESSION['uid'])) {
        $deleted = true;
    }
}
?>

<?php if ($deleted) { ?>
    <div class="alert alert-success">Vacancy deleted. <a href="index.php?page=vacancies&tab=view">Back to list</a></div>
<?php return;
} ?>

<div class="alert alert-danger">Are you sure you want to delete vacancy #<?php echo (int)$v['id']; ?> - <?php echo htmlspecialchars($v['title']); ?>?</div>
<form method="post">
    <input type="hidden" name="confirm_delete" value="1" />
    <button type="submit" class="btn btn-danger">Yes, delete</button>
    <a href="index.php?page=vacancies&tab=view" class="btn btn-secondary">Cancel</a>
</form>