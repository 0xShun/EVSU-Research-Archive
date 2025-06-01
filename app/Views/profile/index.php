<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container py-5">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="<?= base_url($user['profile_picture'] ?? 'assets/img/default-profile.png') ?>" 
                         class="rounded-circle mb-3" 
                         style="width: 150px; height: 150px; object-fit: cover;"
                         alt="Profile Picture">
                    
                    <h4><?= esc($user['full_name']) ?></h4>
                    <p class="text-muted"><?= esc($user['title'] ?? 'No title set') ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-4">Edit Profile</h5>
                    
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?= session()->getFlashdata('error') ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('profile/update') ?>" method="post" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="profile_picture" class="form-label">Profile Picture</label>
                            <input type="file" class="form-control" id="profile_picture" name="profile_picture">
                        </div>

                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" 
                                   value="<?= esc($user['full_name']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" 
                                   value="<?= esc($user['title'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label for="research_interests" class="form-label">Research Interests</label>
                            <textarea class="form-control" id="research_interests" name="research_interests" 
                                      rows="3"><?= esc($user['research_interests'] ?? '') ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>
            </div>

            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title mb-4">My Publications</h5>
                    
                    <?php if (empty($publications)): ?>
                        <p class="text-muted">No publications uploaded yet.</p>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($publications as $publication): ?>
                                <div class="list-group-item">
                                    <h6 class="mb-1"><?= esc($publication['title']) ?></h6>
                                    <p class="mb-1 text-muted"><?= esc($publication['abstract']) ?></p>
                                    <small>Uploaded on: <?= date('F j, Y', strtotime($publication['created_at'])) ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?> 