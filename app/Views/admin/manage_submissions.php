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
                    <td><a href="<?= base_url('publications/view/' . $submission['id']) ?>"><?= esc($submission['title']) ?></a></td>
                    <td><?= esc($submission['authors']) ?></td>
                    <td><?= esc($submission['department_name']) ?></td>
                    <td>
                        <?php if ($submission['status'] === 'pending'): ?>
                            <a href="<?= base_url('admin/submissions/approve/' . $submission['id']) ?>" class="btn btn-sm btn-success">Approve</a>
                            <a href="<?= base_url('admin/submissions/reject/' . $submission['id']) ?>" class="btn btn-sm btn-danger">Reject</a>
                        <?php elseif ($submission['status'] === 'approved'): ?>
                            <span class="badge badge-success" style="color:rgb(0, 255, 51);">Approved</span>
                        <?php else:  ?>
                            <span class="badge badge-danger" style="color:rgb(255, 0, 0);">Rejected</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?= $this->endSection() ?> 