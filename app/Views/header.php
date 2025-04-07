<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EVSU Research Archive</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --evsu-red: #dc3545;
            --evsu-dark: #212529;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 2px 4px rgba(0,0,0,.1);
        }
        
        .navbar-brand {
            font-weight: bold;
            color: var(--evsu-red) !important;
        }
        
        .nav-link {
            color: var(--evsu-dark) !important;
            font-weight: 500;
        }
        
        .nav-link:hover {
            color: var(--evsu-red) !important;
        }
        
        .btn-primary {
            background-color: var(--evsu-red);
            border-color: var(--evsu-red);
        }
        
        .btn-primary:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        
        .text-danger {
            color: var(--evsu-red) !important;
        }
        
        .card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,.075);
        }
        
        .card-title {
            color: var(--evsu-dark);
        }
        
        .badge.bg-light {
            background-color: #f8f9fa !important;
            color: var(--evsu-dark) !important;
            border: 1px solid #dee2e6;
        }
        
        .breadcrumb-item a {
            color: var(--evsu-red);
            text-decoration: none;
        }
        
        .breadcrumb-item.active {
            color: var(--evsu-dark);
        }
        
        .pagination .page-link {
            color: var(--evsu-red);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--evsu-red);
            border-color: var(--evsu-red);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">EVSU Research Archive</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url() ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('publications') ?>">Publications</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('publications/search') ?>">Search</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('about') ?>">About</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <?php if (session()->has('message')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= session('message') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>

    <footer class="bg-white mt-5 py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="text-danger">EVSU Research Archive</h5>
                    <p class="text-muted">Eastern Visayas State University</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted mb-0">&copy; <?= date('Y') ?> EVSU Research Archive. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
