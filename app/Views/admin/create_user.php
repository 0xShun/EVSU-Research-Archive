<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h2><?= $title ?></h2>

    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">New User Details</h3>
        </div>
        <form action="<?= base_url('admin/users/store') ?>" method="post">
            <?= csrf_field() ?>
            <div class="card-body">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" placeholder="Enter name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" placeholder="Enter email" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select class="form-control" id="role" name="role" required>
                        <?php foreach (config('Auth')->roles as $role): ?>
                            <option value="<?= $role ?>" <?= old('role') === $role ? 'selected' : '' ?>><?= $role ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?> 