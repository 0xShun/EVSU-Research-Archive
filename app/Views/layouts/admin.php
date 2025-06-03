<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>EVSU Research Archive</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <!-- Theme style -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
  <!-- Custom styles -->
  <link rel="stylesheet" href="<?= base_url('assets/css/custom.css') ?>">
</head>
<body class="hold-transition sidebar-mini">
<div class="wrapper" style="min-height: 100vh; display: flex; flex-direction: column;">

  <!-- Navbar -->
  <nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <?php if (session()->get('isLoggedIn')): ?>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <img src="<?= base_url(session()->get('profile_picture') ?? 'assets/user-default.png') ?>" 
                 class="img-circle elevation-2" 
                 alt="User Image" 
                 style="width: 30px; height: 30px; object-fit: cover;">
            <span class="ml-2"><?= session()->get('username') ?></span>
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="<?= base_url('profile') ?>">
              <i class="fas fa-user mr-2"></i> Profile
            </a>
            <?php if (session()->get('role') === 'admin'): ?>
              <a class="dropdown-item" href="<?= base_url('admin') ?>">
                <i class="fas fa-cog mr-2"></i> Admin Panel
              </a>
            <?php endif; ?>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="<?= base_url('auth/logout') ?>">
              <i class="fas fa-sign-out-alt mr-2"></i> Logout
            </a>
          </div>
        </li>
      <?php else: ?>
        <?php if (uri_string() === ''): // Check if it's the home page ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('user/login') ?>">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('user/register') ?>">Register</a>
          </li>
        <?php endif; ?>
      <?php endif; ?>
      <li class="nav-item">
        <a class="nav-link" data-widget="fullscreen" href="#" role="button">
          <i class="fas fa-expand-arrows-alt"></i>
        </a>
      </li>
    </ul>
  </nav>
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
  <aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <!-- <a href="<?= base_url() ?>" class="brand-link">
      <img src="https://i.imgur.com/4GYD4hu.png" alt="EVSU Logo" class="brand-image img-circle elevation-3" style="height: 40px; width: auto;">
    </a> -->

    <!-- Sidebar -->
    <div class="sidebar">
      <!-- Sidebar user panel (optional) -->
      <?php if (session()->get('isLoggedIn')): ?>
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
          <div class="image">
            <img src="<?= base_url(session()->get('profile_picture') ?? 'assets/user-default.png') ?>" 
                 class="img-circle elevation-2" 
                 alt="User Image"
                 style="width: 40px; height: 40px; object-fit: cover;">
          </div>
          <div class="info">
            <a href="<?= base_url('profile') ?>" class="d-block"><?= session()->get('username') ?></a>
          </div>
        </div>
      <?php endif; ?>

      <!-- Sidebar Menu -->
      <nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
          <li class="nav-item">
            <a href="<?= base_url('admin') ?>" class="nav-link <?= current_url() == base_url('admin') ? 'active' : '' ?>">
              <i class="nav-icon fas fa-tachometer-alt"></i>
              <p>Dashboard</p>
            </a>
          </li>
          <li class="nav-item">
            <a href="<?= base_url('admin/manage-submissions') ?>" class="nav-link <?= strpos(current_url(), 'admin/manage-submissions') !== false ? 'active' : '' ?>">
              <i class="nav-icon fas fa-file-alt"></i>
              <p>Manage Submissions</p>
            </a>
          </li>
           <li class="nav-item has-treeview <?= strpos(current_url(), 'admin/users') !== false ? 'menu-open' : '' ?>">
            <a href="#" class="nav-link <?= strpos(current_url(), 'admin/users') !== false ? 'active' : '' ?>">
              <i class="nav-icon fas fa-users"></i>
              <p>
                Manage Users
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>
            <ul class="nav nav-treeview">
              <li class="nav-item">
                <a href="<?= base_url('admin/manage-users') ?>" class="nav-link <?= current_url() == base_url('admin/manage-users') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>View All Users</p>
                </a>
              </li>
              <li class="nav-item">
                <a href="<?= base_url('admin/users/create') ?>" class="nav-link <?= current_url() == base_url('admin/users/create') ? 'active' : '' ?>">
                  <i class="far fa-circle nav-icon"></i>
                  <p>Add New User</p>
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= current_url() == base_url('admin/view-analytics') ? 'active' : '' ?>" href="<?= base_url('admin/view-analytics') ?>">
              <i class="nav-icon fas fa-chart-bar"></i>
              <p>View Analytics</p>
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link <?= current_url() == base_url('admin/contact-messages') ? 'active' : '' ?>" href="<?= base_url('admin/contact-messages') ?>">
                <i class="fas fa-envelope me-2"></i>
                Contact Messages
            </a>
          </li>
        </ul>
      </nav>
      <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
  </aside>

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0"><?= $title ?? 'Dashboard' ?></h1>
          </div><!-- /.col -->
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
              <?php if (isset($breadcrumbs)): ?>
                <?php foreach ($breadcrumbs as $label => $url): ?>
                  <li class="breadcrumb-item <?= $url ? '' : 'active' ?>">
                    <?php if ($url): ?>
                      <a href="<?= $url ?>"><?= $label ?></a>
                    <?php else: ?>
                      <?= $label ?>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              <?php endif; ?>
            </ol>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">
        <?= $this->renderSection('content') ?>
      </div><!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <footer class="main-footer mt-auto text-center">
    <div class="d-none d-sm-inline">
      v1.0.0
    </div>
    <strong>Copyright &copy; <?= date('Y') ?> <a href="https://www.evsu.edu.ph">Eastern Visayas State University</a>.</strong> All rights reserved.
  </footer>
</div>
<!-- ./wrapper -->

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
<!-- Custom scripts -->
<script src="<?= base_url('assets/js/custom.js') ?>"></script>
</body>
</html> 