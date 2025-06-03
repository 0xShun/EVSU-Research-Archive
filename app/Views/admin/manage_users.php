<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h2>Manage Users</h2>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">User List</h3>
            <div class="card-tools">
                <a href="<?= base_url('admin/users/create') ?>" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add New User
                </a>
            </div>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= esc($user['name']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td><?= esc($user['role']) ?></td>
                            <td>
                                <?php if ($user['is_active']): ?>
                                    <span class="badge badge-success">Active</span>
                                <?php else: ?>
                                    <span class="badge badge-danger">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('admin/users/edit/' . $user['id']) ?>" class="btn btn-info btn-sm"><i class="fas fa-pencil-alt"></i> Edit</a>
                                <a href="<?= base_url('admin/users/delete/' . $user['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 