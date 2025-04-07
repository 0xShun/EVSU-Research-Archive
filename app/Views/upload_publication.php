<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <h2>Upload Publication</h2>

    <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <ul>
                <?php foreach (session('errors') as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('publications/create') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
        </div>

        <div class="mb-3">
            <label for="authors" class="form-label">Authors</label>
            <input type="text" class="form-control" id="authors" name="authors" value="<?= old('authors') ?>" required>
            <small class="text-muted">Enter authors' names separated by commas</small>
        </div>

        <div class="mb-3">
            <label for="abstract" class="form-label">Abstract</label>
            <textarea class="form-control" id="abstract" name="abstract" rows="5" required><?= old('abstract') ?></textarea>
        </div>

        <div class="mb-3">
            <label for="keywords" class="form-label">Keywords</label>
            <input type="text" class="form-control" id="keywords" name="keywords" value="<?= old('keywords') ?>" required>
            <small class="text-muted">Enter keywords separated by commas</small>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="college_id" class="form-label">College</label>
                <select class="form-select" id="college_id" name="college_id" required>
                    <option value="">Select College</option>
                    <?php foreach ($colleges as $college): ?>
                        <option value="<?= $college['id'] ?>" <?= old('college_id') == $college['id'] ? 'selected' : '' ?>>
                            <?= $college['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="department_id" class="form-label">Department</label>
                <select class="form-select" id="department_id" name="department_id" required>
                    <option value="">Select Department</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?= $department['id'] ?>" <?= old('department_id') == $department['id'] ? 'selected' : '' ?>>
                            <?= $department['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label for="program_id" class="form-label">Program</label>
                <select class="form-select" id="program_id" name="program_id" required>
                    <option value="">Select Program</option>
                </select>
            </div>
        </div>

        <div class="mb-3">
            <label for="publication_date" class="form-label">Publication Date</label>
            <input type="date" class="form-control" id="publication_date" name="publication_date" value="<?= old('publication_date') ?>" required>
        </div>

        <div class="mb-3">
            <label for="file" class="form-label">Publication File</label>
            <input type="file" class="form-control" id="file" name="file" accept=".pdf,.doc,.docx" required>
            <small class="text-muted">Accepted formats: PDF, DOC, DOCX. Maximum size: 10MB</small>
        </div>

        <div class="mb-4">
            <button type="submit" class="btn btn-primary">Upload Publication</button>
            <a href="<?= base_url('publications') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
// Add dynamic loading of departments based on college selection
document.getElementById('college_id').addEventListener('change', function() {
    const collegeId = this.value;
    const departmentSelect = document.getElementById('department_id');
    
    // Clear current departments
    departmentSelect.innerHTML = '<option value="">Select Department</option>';
    
    if (collegeId) {
        fetch(`<?= base_url('api/colleges') ?>/${collegeId}/departments`)
            .then(response => response.json())
            .then(departments => {
                departments.forEach(dept => {
                    const option = new Option(dept.name, dept.id);
                    departmentSelect.add(option);
                });
            });
    }
});

// Add dynamic loading of programs based on department selection
document.getElementById('department_id').addEventListener('change', function() {
    const departmentId = this.value;
    const programSelect = document.getElementById('program_id');
    
    // Clear current programs
    programSelect.innerHTML = '<option value="">Select Program</option>';
    
    if (departmentId) {
        fetch(`<?= base_url('api/departments') ?>/${departmentId}/programs`)
            .then(response => response.json())
            .then(programs => {
                programs.forEach(prog => {
                    const option = new Option(prog.name, prog.id);
                    programSelect.add(option);
                });
            });
    }
});
</script>
<?= $this->endSection() ?>
