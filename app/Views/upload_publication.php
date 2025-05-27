<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Upload Publication</h2>
                </div>
                <div class="card-body p-4">
                    <?php if (session()->has('errors')): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('publications/create') ?>" method="post" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="mb-3">
                            <label for="title" class="form-label fw-bold">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?= old('title') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="authors" class="form-label fw-bold">Authors</label>
                            <input type="text" class="form-control" id="authors" name="authors" value="<?= old('authors') ?>" required>
                            <small class="text-muted">Enter authors' names separated by commas</small>
                        </div>

                        <div class="mb-3">
                            <label for="abstract" class="form-label fw-bold">Abstract</label>
                            <textarea class="form-control" id="abstract" name="abstract" rows="5" required><?= old('abstract') ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="keywords" class="form-label fw-bold">Keywords</label>
                            <input type="text" class="form-control" id="keywords" name="keywords" value="<?= old('keywords') ?>" required>
                            <small class="text-muted">Enter keywords separated by commas</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="college_id" class="form-label fw-bold">College</label>
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
                                <label for="department_id" class="form-label fw-bold">Department</label>
                                <select class="form-select" id="department_id" name="department_id" required>
                                    <option value="">Select Department</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="program_id" class="form-label fw-bold">Program</label>
                                <select class="form-select" id="program_id" name="program_id" required>
                                    <option value="">Select Program</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="publication_date" class="form-label fw-bold">Publication Date</label>
                            <input type="date" class="form-control" id="publication_date" name="publication_date" value="<?= old('publication_date') ?>" required>
                        </div>

                        <div class="mb-4">
                            <label for="file" class="form-label fw-bold">Publication File</label>
                            <input type="file" class="form-control" id="file" name="file" accept=".pdf,.doc,.docx" required>
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX. Maximum size: 10MB</small>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary px-4">Upload Publication</button>
                            <a href="<?= base_url('publications') ?>" class="btn btn-secondary px-4">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Function to load departments
function loadDepartments(collegeId) {
    const departmentSelect = document.getElementById('department_id');
    departmentSelect.innerHTML = '<option value="">Select Department</option>';
    
    if (collegeId) {
        fetch(`<?= base_url('api/colleges') ?>/${collegeId}/departments`)
            .then(response => response.json())
            .then(departments => {
                departments.forEach(dept => {
                    const option = new Option(dept.name, dept.id);
                    departmentSelect.add(option);
                });
                // If there's a previously selected department, try to select it
                const oldDepartmentId = '<?= old('department_id') ?>';
                if (oldDepartmentId) {
                    departmentSelect.value = oldDepartmentId;
                    // Trigger program loading for the selected department
                    loadPrograms(oldDepartmentId);
                }
            })
            .catch(error => {
                console.error('Error loading departments:', error);
            });
    }
}

// Function to load programs
function loadPrograms(departmentId) {
    const programSelect = document.getElementById('program_id');
    programSelect.innerHTML = '<option value="">Select Program</option>';
    
    if (departmentId) {
        fetch(`<?= base_url('api/departments') ?>/${departmentId}/programs`)
            .then(response => response.json())
            .then(programs => {
                programs.forEach(prog => {
                    const option = new Option(prog.name, prog.id);
                    programSelect.add(option);
                });
                // If there's a previously selected program, try to select it
                const oldProgramId = '<?= old('program_id') ?>';
                if (oldProgramId) {
                    programSelect.value = oldProgramId;
                }
            })
            .catch(error => {
                console.error('Error loading programs:', error);
            });
    }
}

// Add event listeners when the document is loaded
document.addEventListener('DOMContentLoaded', function() {
    const collegeSelect = document.getElementById('college_id');
    const departmentSelect = document.getElementById('department_id');
    
    // Load initial departments if a college is selected
    if (collegeSelect.value) {
        loadDepartments(collegeSelect.value);
    }
    
    // College change event
    collegeSelect.addEventListener('change', function() {
        loadDepartments(this.value);
        // Clear program selection when college changes
        document.getElementById('program_id').innerHTML = '<option value="">Select Program</option>';
    });
    
    // Department change event
    departmentSelect.addEventListener('change', function() {
        loadPrograms(this.value);
    });
});
</script>
<?= $this->endSection() ?>
