<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title mb-4">Edit Publication</h2>
                    
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

                    <form action="<?= base_url('publications/edit/' . $publication['id']) ?>" method="post">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= esc($publication['title']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="authors" class="form-label">Authors</label>
                            <input type="text" class="form-control" id="authors" name="authors" value="<?= esc($publication['authors']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="abstract" class="form-label">Abstract</label>
                            <textarea class="form-control" id="abstract" name="abstract" rows="5" required><?= esc($publication['abstract']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="keywords" class="form-label">Keywords</label>
                            <input type="text" class="form-control" id="keywords" name="keywords" value="<?= esc($publication['keywords']) ?>" required>
                            <div class="form-text">Separate keywords with commas</div>
                        </div>

                        <div class="mb-3">
                            <label for="college_id" class="form-label">College</label>
                            <select class="form-select" id="college_id" name="college_id" required>
                                <option value="">Select College</option>
                                <?php foreach ($colleges as $college): ?>
                                    <option value="<?= $college['id'] ?>" <?= ($publication['college_id'] == $college['id']) ? 'selected' : '' ?>>
                                        <?= esc($college['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select class="form-select" id="department_id" name="department_id" required>
                                <option value="">Select Department</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?= $department['id'] ?>" <?= ($publication['department_id'] == $department['id']) ? 'selected' : '' ?>>
                                        <?= esc($department['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="program_id" class="form-label">Program</label>
                            <select class="form-select" id="program_id" name="program_id" required>
                                <option value="">Select Program</option>
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?= $program['id'] ?>" <?= ($publication['program_id'] == $program['id']) ? 'selected' : '' ?>>
                                        <?= esc($program['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="publication_date" class="form-label">Publication Date</label>
                            <input type="date" class="form-control" id="publication_date" name="publication_date" value="<?= date('Y-m-d', strtotime($publication['publication_date'])) ?>" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('profile') ?>" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Update Publication</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('college_id').addEventListener('change', function() {
    const collegeId = this.value;
    const departmentSelect = document.getElementById('department_id');
    
    // Clear existing options
    departmentSelect.innerHTML = '<option value="">Select Department</option>';
    
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

document.getElementById('department_id').addEventListener('change', function() {
    const departmentId = this.value;
    const programSelect = document.getElementById('program_id');
    
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
</script>
<?= $this->endSection() ?> 