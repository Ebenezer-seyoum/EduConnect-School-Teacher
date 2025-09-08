<?php
// expects connection/function.php loaded by adminHeader.php
$myVacancies = getVacancies(200, $_SESSION['uid']);
?>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>School</th>
                <th>Type</th>
                <th>Location</th>
                <th>Salary</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($myVacancies)) { ?>
                <tr>
                    <td colspan="7" class="text-muted">No vacancies yet.</td>
                </tr>
                <?php } else {
                foreach ($myVacancies as $v) { ?>
                    <tr>
                        <td>#<?php echo (int)$v['id']; ?></td>
                        <td><?php echo htmlspecialchars($v['title']); ?></td>
                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($v['employment_type']); ?></span></td>
                        <td><?php echo htmlspecialchars($v['location']); ?></td>
                        <td><?php echo htmlspecialchars($v['salary']); ?></td>
                        <td><?php echo htmlspecialchars($v['created_at'] ?? ''); ?></td>
                        <td>
                            <a href="index.php?page=vacancies&tab=edit&id=<?php echo (int)$v['id']; ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                            <a href="index.php?page=vacancies&tab=delete&id=<?php echo (int)$v['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this vacancy?');">Delete</a>
                        </td>
                    </tr>
            <?php }
            } ?>
        </tbody>
    </table>
</div>