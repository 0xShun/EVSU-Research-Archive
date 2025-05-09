<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h2>Admin Dashboard</h2>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Manage Users</h5>
                    <p class="card-text">Add, edit, or remove users and assign roles.</p>
                    <a href="<?= base_url('admin/manage-users') ?>" class="btn btn-primary">Manage Users</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Manage Submissions</h5>
                    <p class="card-text">Review and approve or reject submissions.</p>
                    <a href="<?= base_url('admin/manage-submissions') ?>" class="btn btn-primary">Manage Submissions</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">View Analytics</h5>
                    <p class="card-text">View system usage and performance analytics.</p>
                    <a href="<?= base_url('admin/view-analytics') ?>" class="btn btn-primary">View Analytics</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 