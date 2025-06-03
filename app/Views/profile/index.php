<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h2>Profile Management</h2>

    <?php if (session()->has('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                <?php foreach (session('errors') as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-4">
        <div class="card-header">
            <h4>Edit Profile</h4>
        </div>
        <div class="card-body">
            <form action="<?= base_url('profile/update') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="mb-3 text-center">
                    <img src="<?= base_url(session()->get('profile_picture') ?? 'assets/user-default.png') ?>" 
                         class="img-thumbnail rounded-circle" 
                         alt="Profile Picture" 
                         style="width: 150px; height: 150px; object-fit: cover;">
                    <p class="mt-2">Role: <?= esc($user['role'] ?? 'N/A') ?></p>
                </div>
                <div class="mb-3">
                    <label for="profile_picture" class="form-label">Upload Profile Picture</label>
                    <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/*">
                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?= esc($user['name'] ?? '') ?>" required>
                </div>

                <div class="mb-3" id="research-interests-display">
                    <label class="form-label">Research Interests:</label>
                    <p><?= esc($user['research_interests'] ?? 'N/A') ?></p>
                    <button type="button" class="btn btn-secondary btn-sm" id="edit-research-interests">Edit Research Interests</button>
                </div>

                <div class="mb-3" id="research-interests-edit" style="display: none;">
                    <label for="research_interests" class="form-label">Research Interests (comma-separated)</label>
                    <textarea class="form-control" id="research_interests" name="research_interests" rows="3"><?= esc($user['research_interests'] ?? '') ?></textarea>
                    <button type="button" class="btn btn-secondary btn-sm mt-2" id="cancel-edit-research-interests">Cancel</button>
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h4>My Publications</h4>
        </div>
        <div class="card-body">
            <?php if (empty($publications)): ?>
                <p>You have not uploaded any publications yet.</p>
            <?php else: ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Publication Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($publications as $pub): ?>
                            <tr>
                                <td><?= esc($pub['title']) ?></td>
                                <td><?= esc($pub['publication_date']) ?></td>
                                <td>
                                    <a href="<?= base_url('publications/view/' . $pub['id']) ?>" class="btn btn-sm btn-info">View</a>
                                    <a href="<?= base_url('publications/edit/' . $pub['id']) ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="<?= base_url('publications/delete/' . $pub['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this publication?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <div class="mt-4">
        <a href="<?= base_url('auth/logout') ?>" class="btn btn-danger">Logout</a>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const researchInterestsDisplay = document.getElementById('research-interests-display');
        const researchInterestsEdit = document.getElementById('research-interests-edit');
        const editButton = document.getElementById('edit-research-interests');
        const cancelButton = document.getElementById('cancel-edit-research-interests');

        editButton.addEventListener('click', () => {
            researchInterestsDisplay.style.display = 'none';
            researchInterestsEdit.style.display = 'block';
        });

        cancelButton.addEventListener('click', () => {
            researchInterestsDisplay.style.display = 'block';
            researchInterestsEdit.style.display = 'none';
        });

        // Initially hide the edit section if there are no research interests
        if (researchInterestsDisplay.querySelector('p').innerText.trim() === 'N/A') {
             researchInterestsEdit.style.display = 'block';
             researchInterestsDisplay.style.display = 'none';
        }
    });
</script>
<?= $this->endSection() ?> 