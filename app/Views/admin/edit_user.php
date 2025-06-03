<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h2><?= $title ?></h2>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Validation Errors!</h5>
            <ul>
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="card card-warning">
        <div class="card-header">
            <h3 class="card-title">Edit User Details</h3>
        </div>
        <form action="<?= base_url('admin/users/update/' . $user['id']) ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="_method" value="POST"> <!-- CodeIgniter requires POST for updates -->
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name', $user['name']) ?>" placeholder="Enter name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email', $user['email']) ?>" placeholder="Enter email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password (leave blank to keep current)</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <?php foreach (config('Auth')->roles as $role): ?>
                            <option value="<?= $role ?>" <?= old('role', $user['role']) === $role ? 'selected' : '' ?>><?= $role ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" <?= old('is_active', $user['is_active']) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-warning">Update</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?> 