<?= $this->extend('header') ?>

<?= $this->section('content') ?>
<div class="row mb-5">
    <div class="col-md-12 text-center">
        <h1 class="display-4 text-danger mb-4">EVSU Research Archive</h1>
        <p class="lead">Discover and access research publications from Eastern Visayas State University</p>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= base_url('publication/search') ?>" method="get">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <input type="text" class="form-control" name="keyword" placeholder="Search by title, author, or keywords">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="department">
                                <option value="">All Departments</option>
                                <option value="Computer Studies">Computer Studies</option>
                                <option value="Engineering">Engineering</option>
                                <option value="Education">Education</option>
                                <option value="Arts and Sciences">Arts and Sciences</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="type">
                                <option value="">All Types</option>
                                <option value="journal">Journal</option>
                                <option value="conference">Conference</option>
                                <option value="thesis">Thesis</option>
                                <option value="dissertation">Dissertation</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" name="year">
                                <option value="">All Years</option>
                                <?php for($i = date('Y'); $i >= 2000; $i--): ?>
                                    <option value="<?= $i ?>"><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row mb-5">
    <div class="col-md-12">
        <h2 class="text-danger mb-4">Featured Publications</h2>
        <div class="row">
            <?php if(isset($featured_publications) && !empty($featured_publications)): ?>
                <?php foreach($featured_publications as $publication): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($publication['title']) ?></h5>
                                <p class="card-text text-muted"><?= esc($publication['authors']) ?></p>
                                <p class="card-text">
                                    <small class="text-muted">
                                        <?= esc($publication['department_name'] ?? 'N/A') ?> | <?= esc($publication['year'] ?? date('Y', strtotime($publication['publication_date']))) ?>
                                    </small>
                                </p>
                                <a href="<?= base_url('publication/view/' . $publication['id']) ?>" class="btn btn-outline-danger">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-info">
                        No featured publications available at the moment.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <h2 class="text-danger mb-4">Quick Statistics</h2>
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h3 class="text-danger"><?= isset($stats['total']) ? $stats['total'] : 0 ?></h3>
                        <p class="mb-0">Total Publications</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h3 class="text-danger"><?= isset($stats['journals']) ? $stats['journals'] : 0 ?></h3>
                        <p class="mb-0">Journal Articles</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h3 class="text-danger"><?= isset($stats['theses']) ? $stats['theses'] : 0 ?></h3>
                        <p class="mb-0">Theses</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card bg-light">
                    <div class="card-body">
                        <h3 class="text-danger"><?= isset($stats['conferences']) ? $stats['conferences'] : 0 ?></h3>
                        <p class="mb-0">Conference Papers</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
