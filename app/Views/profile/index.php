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

    <?php if (session()->has('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= session('error') ?>
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
                </div>

                <div class="mb-3">
                    <label for="research_interests" class="form-label">Research Interests (comma-separated)</label>
                    <textarea class="form-control" id="research_interests" name="research_interests" rows="3"><?= esc($user['research_interests'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">My Publications</h4>
            <a href="<?= base_url('publications/upload') ?>" class="btn btn-primary">
                <i class="fas fa-upload me-2"></i>Upload New Publication
            </a>
        </div>
        <div class="card-body">
            <?php if (empty($publications)): ?>
                <p>You have not uploaded any publications yet.</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Publication Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($publications as $pub): ?>
                                <tr>
                                    <td>
                                        <a href="<?= base_url('publications/view/' . $pub['id']) ?>" class="text-decoration-none">
                                            <?= esc($pub['title']) ?>
                                        </a>
                                    </td>
                                    <td><?= date('F Y', strtotime($pub['publication_date'])) ?></td>
                                    <td>
                                        <?php
                                        $statusClass = [
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger'
                                        ];
                                        $statusText = [
                                            'pending' => 'Pending Review',
                                            'approved' => 'Approved',
                                            'rejected' => 'Rejected'
                                        ];
                                        ?>
                                        <span class="badge bg-<?= $statusClass[$pub['status']] ?>">
                                            <?= $statusText[$pub['status']] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="<?= base_url('publications/view/' . $pub['id']) ?>" class="btn btn-sm btn-info me-1">View</a>
                                            <?php if ($pub['status'] === 'pending'): ?>
                                                <!-- Edit button to open modal -->
                                                <button type="button" class="btn btn-sm btn-primary me-1" data-bs-toggle="modal" data-bs-target="#editPublicationModal<?= $pub['id'] ?>">
                                                    Edit
                                                </button>
                                                <!-- Delete button -->
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $pub['id'] ?>)">
                                                    Delete
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Publication Modal -->
                                <div class="modal fade" id="editPublicationModal<?= $pub['id'] ?>" tabindex="-1" aria-labelledby="editPublicationModalLabel<?= $pub['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editPublicationModalLabel<?= $pub['id'] ?>">Edit Publication</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form action="<?= base_url('publications/updateFromModal/' . $pub['id']) ?>" method="post">
                                                <?= csrf_field() ?>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="title<?= $pub['id'] ?>" class="form-label">Title</label>
                                                        <input type="text" class="form-control" id="title<?= $pub['id'] ?>" name="title" value="<?= esc($pub['title']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="authors<?= $pub['id'] ?>" class="form-label">Authors</label>
                                                        <input type="text" class="form-control" id="authors<?= $pub['id'] ?>" name="authors" value="<?= esc($pub['authors']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="abstract<?= $pub['id'] ?>" class="form-label">Abstract</label>
                                                        <textarea class="form-control" id="abstract<?= $pub['id'] ?>" name="abstract" rows="4" required><?= esc($pub['abstract']) ?></textarea>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="keywords<?= $pub['id'] ?>" class="form-label">Keywords</label>
                                                        <input type="text" class="form-control" id="keywords<?= $pub['id'] ?>" name="keywords" value="<?= esc($pub['keywords']) ?>" required>
                                                        <div class="form-text">Separate keywords with commas</div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="college_id<?= $pub['id'] ?>" class="form-label">College</label>
                                                                <select class="form-select" id="college_id<?= $pub['id'] ?>" name="college_id" required>
                                                                    <option value="">Select College</option>
                                                                    <?php foreach ($colleges as $college): ?>
                                                                        <option value="<?= $college['id'] ?>" <?= ($pub['college_id'] == $college['id']) ? 'selected' : '' ?>>
                                                                            <?= esc($college['name']) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="department_id<?= $pub['id'] ?>" class="form-label">Department</label>
                                                                <select class="form-select" id="department_id<?= $pub['id'] ?>" name="department_id" required>
                                                                    <option value="">Select Department</option>
                                                                    <?php // Departments will be loaded via JavaScript ?>
                                                                    <?php // Removed dynamic loading, show all departments ?>
                                                                    <?php foreach ($departments as $department): ?>
                                                                        <option value="<?= $department['id'] ?>" <?= ($pub['department_id'] == $department['id']) ? 'selected' : '' ?>>
                                                                            <?= esc($department['name']) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="mb-3">
                                                                <label for="program_id<?= $pub['id'] ?>" class="form-label">Program</label>
                                                                <select class="form-select" id="program_id<?= $pub['id'] ?>" name="program_id" required>
                                                                    <option value="">Select Program</option>
                                                                    <?php // Programs will be loaded via JavaScript ?>
                                                                     <?php // Removed dynamic loading, show all programs ?>
                                                                        <?php foreach ($programs as $program): ?>
                                                                            <option value="<?= $program['id'] ?>" <?= ($pub['program_id'] == $program['id']) ? 'selected' : '' ?>>
                                                                                <?= esc($program['name']) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="publication_date<?= $pub['id'] ?>" class="form-label">Publication Date</label>
                                                        <input type="date" class="form-control" id="publication_date<?= $pub['id'] ?>" name="publication_date" value="<?= date('Y-m-d', strtotime($pub['publication_date'])) ?>" required>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?= $pager->links() ?>
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
        // Removed JavaScript for toggling research interests display

        // Function to confirm deletion (keep if needed for publications)
        window.confirmDelete = function(id) {
            if (confirm('Are you sure you want to delete this publication?')) {
                window.location.href = '<?= base_url('publications/delete/') ?>' + id;
            }
        };
    });
</script>
<?= $this->endSection() ?> 