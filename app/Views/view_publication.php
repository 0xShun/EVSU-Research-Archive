<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?= base_url() ?>" class="text-danger">Home</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('publications') ?>" class="text-danger">Publications</a></li>
                <li class="breadcrumb-item active"><?= esc($publication['title']) ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h1 class="h2 text-danger mb-4"><?= esc($publication['title']) ?></h1>
                
                <div class="mb-4">
                    <h2 class="h5 text-danger">Authors</h2>
                    <p class="mb-0"><?= esc($publication['authors']) ?></p>
                </div>

                <div class="mb-4">
                    <h2 class="h5 text-danger">Abstract</h2>
                    <p class="mb-0"><?= nl2br(esc($publication['abstract'])) ?></p>
                </div>

                <div class="mb-4">
                    <h2 class="h5 text-danger">Keywords</h2>
                    <p class="mb-0">
                        <?php
                        $keywords = explode(',', $publication['keywords']);
                        foreach ($keywords as $keyword) {
                            echo '<span class="badge bg-light text-danger me-2">' . esc(trim($keyword)) . '</span>';
                        }
                        ?>
                    </p>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h2 class="h5 text-danger">Publication Details</h2>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Department:</strong> <?= esc($publication['department_name']) ?>
                            </li>
                            <li class="mb-2">
                                <strong>Program:</strong> <?= esc($publication['program_name']) ?>
                            </li>
                        </ul>
                    </div>
                </div>

                <?php if (!empty($publication['file_path'])): ?>
                    <div class="mb-4">
                        <a href="<?= base_url('publications/download/' . $publication['id']) ?>" class="btn btn-primary">
                            <i class="fas fa-download me-2"></i>Download Publication
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h2 class="h5 text-danger mb-3">Share This Publication</h2>
                <div class="d-flex gap-2">
                    <a href="https://twitter.com/intent/tweet?url=<?= urlencode(current_url()) ?>&text=<?= urlencode($publication['title']) ?>" 
                       class="btn btn-outline-primary" target="_blank">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" 
                       class="btn btn-outline-primary" target="_blank">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= urlencode(current_url()) ?>&title=<?= urlencode($publication['title']) ?>" 
                       class="btn btn-outline-primary" target="_blank">
                        <i class="fab fa-linkedin"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h5 text-danger mb-3">Related Publications</h2>
                <?php if (isset($related_publications) && !empty($related_publications)): ?>
                    <?php foreach ($related_publications as $related): ?>
                        <div class="mb-3">
                            <h3 class="h6">
                                <a href="<?= base_url('publication/view/' . $related['id']) ?>" class="text-danger text-decoration-none">
                                    <?= esc($related['title']) ?>
                                </a>
                            </h3>
                            <p class="small text-muted mb-0">
                                <?= esc($related['authors']) ?><br>
                                <?= date('Y', strtotime($related['publication_date'])) ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted mb-0">No related publications found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
