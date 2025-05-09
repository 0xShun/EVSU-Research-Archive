<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h2>Manage Submissions</h2>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Authors</th>
                <th>Department</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $submission): ?>
                <tr>
                    <td><?= $submission['id'] ?></td>
                    <td><?= esc($submission['title']) ?></td>
                    <td><?= esc($submission['authors']) ?></td>
                    <td><?= esc($submission['department_id']) ?></td>
                    <td>
                        <a href="<?= base_url('admin/approve-submission/' . $submission['id']) ?>" class="btn btn-sm btn-success">Approve</a>
                        <a href="<?= base_url('admin/reject-submission/' . $submission['id']) ?>" class="btn btn-sm btn-danger">Reject</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?> 