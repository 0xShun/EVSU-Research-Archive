<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
    <!-- AdminLTE content goes here -->
    <div class="row">
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box">
                <span class="info-box-icon bg-info elevation-1"><i class="fas fa-book"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Total Publications</span>
                    <span class="info-box-number"><?= $totalPublications ?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
        <div class="col-12 col-sm-6 col-md-3">
            <div class="info-box mb-3">
                <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-users"></i></span>

                <div class="info-box-content">
                    <span class="info-box-text">Total Users</span>
                    <span class="info-box-number"><?= $totalUsers ?></span>
                </div>
                <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Welcome</h3>
                </div>
                <div class="card-body">
                    <p>Welcome to the Admin Dashboard! Use the sidebar to navigate.</p>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?> 