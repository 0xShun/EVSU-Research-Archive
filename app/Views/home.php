<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container">
    <!-- Hero Section -->
    <div class="row align-items-center py-5">
        <div class="col-md-6">
            <h1 class="display-4 fw-bold mb-4">EVSU Research Archive</h1>
            <p class="lead mb-4">Discover academic excellence through our comprehensive collection of research publications from Eastern Visayas State University.</p>
            <div class="d-flex gap-3">
                <a href="<?= base_url('publications') ?>" class="btn btn-primary btn-lg">Browse Publications</a>
                <a href="<?= base_url('publications/search') ?>" class="btn btn-outline-secondary btn-lg">Search</a>
            </div>
        </div>
        <div class="col-md-6">
            <img src="https://i.imgur.com/4GYD4hu.png" alt="Research Archive" class="img-fluid rounded shadow-lg">
        </div>
    </div>

    <!-- Statistics Section -->
    <div class="row py-5">
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-book-open fa-3x text-danger mb-3"></i>
                    <h3 class="card-title"><?= number_format($stats['total']) ?></h3>
                    <p class="card-text">Total Publications</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-calendar-alt fa-3x text-danger mb-3"></i>
                    <h3 class="card-title"><?= number_format($stats['this_year']) ?></h3>
                    <p class="card-text">Publications This Year</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-university fa-3x text-danger mb-3"></i>
                    <h3 class="card-title"><?= number_format($stats['colleges']) ?></h3>
                    <p class="card-text">Colleges</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-center h-100">
                <div class="card-body">
                    <i class="fas fa-building fa-3x text-danger mb-3"></i>
                    <h3 class="card-title"><?= number_format($stats['departments']) ?></h3>
                    <p class="card-text">Departments</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest Publications Section -->
    <div class="py-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Latest Publications</h2>
            <a href="<?= base_url('publications') ?>" class="btn btn-outline-primary">View All</a>
        </div>
        <div class="row">
            <?php foreach ($latest_publications as $publication): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="card-title">
                                <a href="<?= base_url('publications/view/' . $publication['id']) ?>" class="text-decoration-none">
                                    <?= esc($publication['title']) ?>
                                </a>
                            </h5>
                            <p class="card-text text-muted">
                                <small><?= esc($publication['authors']) ?></small>
                            </p>
                            <p class="card-text">
                                <?= esc(substr($publication['abstract'], 0, 150)) ?>...
                            </p>
                        </div>
                        <div class="card-footer bg-transparent">
                            <small class="text-muted">
                                Published <?= date('F Y', strtotime($publication['publication_date'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 