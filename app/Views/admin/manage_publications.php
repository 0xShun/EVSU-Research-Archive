<?= $this->extend('layouts/admin') ?>

<?= $this->section('content') ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Manage Publications</h5>
                </div>
                <div class="card-body">
                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger">
                            <?= session('error') ?>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success">
                            <?= session('success') ?>
                        </div>
                    <?php endif; ?>

                    <!-- Publication List -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Authors</th>
                                    <th>College</th>
                                    <th>Department</th>
                                    <th>Program</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($publications as $publication): ?>
                                    <tr>
                                        <td><?= esc($publication['title']) ?></td>
                                        <td><?= esc($publication['authors']) ?></td>
                                        <td><?= esc($publication['college_name']) ?></td>
                                        <td><?= esc($publication['department_name']) ?></td>
                                        <td><?= esc($publication['program_name']) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $publication['status'] === 'approved' ? 'success' : ($publication['status'] === 'pending' ? 'warning' : 'danger') ?>">
                                                <?= ucfirst($publication['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($publication['publication_date'])) ?></td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editModal<?= $publication['id'] ?>">
                                                Edit
                                            </button>
                                            <?php if ($publication['status'] === 'pending'): ?>
                                                <a href="<?= base_url('admin/publications/approve/' . $publication['id']) ?>" 
                                                   class="btn btn-sm btn-success"
                                                   onclick="return confirm('Are you sure you want to approve this publication?')">
                                                    Approve
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editModal<?= $publication['id'] ?>" tabindex="-1">
                                        <div class="modal-dialog modal-lg">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Edit Publication</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="<?= base_url('admin/publications/update/' . $publication['id']) ?>" method="post">
                                                    <?= csrf_field() ?>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="title<?= $publication['id'] ?>" class="form-label">Title</label>
                                                            <input type="text" class="form-control" id="title<?= $publication['id'] ?>" 
                                                                   name="title" value="<?= esc($publication['title']) ?>" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="authors<?= $publication['id'] ?>" class="form-label">Authors</label>
                                                            <input type="text" class="form-control" id="authors<?= $publication['id'] ?>" 
                                                                   name="authors" value="<?= esc($publication['authors']) ?>" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="abstract<?= $publication['id'] ?>" class="form-label">Abstract</label>
                                                            <textarea class="form-control" id="abstract<?= $publication['id'] ?>" 
                                                                      name="abstract" rows="3" required><?= esc($publication['abstract']) ?></textarea>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="keywords<?= $publication['id'] ?>" class="form-label">Keywords</label>
                                                            <input type="text" class="form-control" id="keywords<?= $publication['id'] ?>" 
                                                                   name="keywords" value="<?= esc($publication['keywords']) ?>" required>
                                                        </div>

                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <label for="college_id<?= $publication['id'] ?>" class="form-label">College</label>
                                                                    <select class="form-select" id="college_id<?= $publication['id'] ?>" 
                                                                            name="college_id" required>
                                                                        <?php foreach ($colleges as $college): ?>
                                                                            <option value="<?= $college['id'] ?>" 
                                                                                    <?= ($publication['college_id'] == $college['id']) ? 'selected' : '' ?>>
                                                                                <?= esc($college['name']) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <label for="department_id<?= $publication['id'] ?>" class="form-label">Department</label>
                                                                    <select class="form-select" id="department_id<?= $publication['id'] ?>" 
                                                                            name="department_id" required>
                                                                        <?php foreach ($departments as $department): ?>
                                                                            <option value="<?= $department['id'] ?>" 
                                                                                    <?= ($publication['department_id'] == $department['id']) ? 'selected' : '' ?>>
                                                                                <?= esc($department['name']) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="mb-3">
                                                                    <label for="program_id<?= $publication['id'] ?>" class="form-label">Program</label>
                                                                    <select class="form-select" id="program_id<?= $publication['id'] ?>" 
                                                                            name="program_id" required>
                                                                        <?php foreach ($programs as $program): ?>
                                                                            <option value="<?= $program['id'] ?>" 
                                                                                    <?= ($publication['program_id'] == $program['id']) ? 'selected' : '' ?>>
                                                                                <?= esc($program['name']) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="publication_date<?= $publication['id'] ?>" class="form-label">Publication Date</label>
                                                            <input type="date" class="form-control" id="publication_date<?= $publication['id'] ?>" 
                                                                   name="publication_date" value="<?= date('Y-m-d', strtotime($publication['publication_date'])) ?>" required>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label for="status<?= $publication['id'] ?>" class="form-label">Status</label>
                                                            <select class="form-select" id="status<?= $publication['id'] ?>" name="status" required>
                                                                <option value="pending" <?= $publication['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                                <option value="approved" <?= $publication['status'] === 'approved' ? 'selected' : '' ?>>Approved</option>
                                                                <option value="rejected" <?= $publication['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                                                            </select>
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

                    <!-- Pagination -->
                    <?php if (isset($pager)): ?>
                        <div class="mt-4">
                            <?= $pager->links() ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Dynamic department and program loading based on college selection
document.querySelectorAll('[id^="college_id"]').forEach(select => {
    select.addEventListener('change', function() {
        const collegeId = this.value;
        const publicationId = this.id.replace('college_id', '');
        const departmentSelect = document.getElementById('department_id' + publicationId);
        const programSelect = document.getElementById('program_id' + publicationId);
        
        // Clear existing options
        departmentSelect.innerHTML = '<option value="">Select Department</option>';
        programSelect.innerHTML = '<option value="">Select Program</option>';
        
        if (collegeId) {
            // Fetch departments for selected college
            fetch(`<?= base_url('api/get_departments/') ?>${collegeId}`)
                .then(response => response.json())
                .then(departments => {
                    departments.forEach(dept => {
                        const option = document.createElement('option');
                        option.value = dept.id;
                        option.textContent = dept.name;
                        departmentSelect.appendChild(option);
                    });
                });
        }
    });
});

document.querySelectorAll('[id^="department_id"]').forEach(select => {
    select.addEventListener('change', function() {
        const departmentId = this.value;
        const publicationId = this.id.replace('department_id', '');
        const programSelect = document.getElementById('program_id' + publicationId);
        
        // Clear existing options
        programSelect.innerHTML = '<option value="">Select Program</option>';
        
        if (departmentId) {
            // Fetch programs for selected department
            fetch(`<?= base_url('api/get_programs/') ?>${departmentId}`)
                .then(response => response.json())
                .then(programs => {
                    programs.forEach(prog => {
                        const option = document.createElement('option');
                        option.value = prog.id;
                        option.textContent = prog.name;
                        programSelect.appendChild(option);
                    });
                });
        }
    });
});
</script>
<?= $this->endSection() ?> 