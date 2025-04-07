<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row">
        <div class="col-md-3">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="card-title">Filter Publications</h5>
                    <form method="get" action="<?= base_url('publications') ?>">
                        <div class="mb-3">
                            <label for="college_id" class="form-label">College</label>
                            <select class="form-select" id="college_id" name="college_id">
                                <option value="">All Colleges</option>
                                <?php foreach ($colleges as $college): ?>
                                    <option value="<?= $college['id'] ?>" <?= ($college_id == $college['id']) ? 'selected' : '' ?>>
                                        <?= esc($college['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="department_id" class="form-label">Department</label>
                            <select class="form-select" id="department_id" name="department_id">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?= $department['id'] ?>" <?= ($department_id == $department['id']) ? 'selected' : '' ?>>
                                        <?= esc($department['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="year" class="form-label">Year</label>
                            <select class="form-select" id="year" name="year">
                                <option value="">All Years</option>
                                <?php foreach ($years as $year_option): ?>
                                    <option value="<?= $year_option ?>" <?= ($year == $year_option) ? 'selected' : '' ?>>
                                        <?= $year_option ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sort By</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" <?= ($sort == 'newest') ? 'selected' : '' ?>>Newest First</option>
                                <option value="oldest" <?= ($sort == 'oldest') ? 'selected' : '' ?>>Oldest First</option>
                                <option value="title" <?= ($sort == 'title') ? 'selected' : '' ?>>Title</option>
                                <option value="department" <?= ($sort == 'department') ? 'selected' : '' ?>>Department</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Publications</h2>
                <a href="<?= base_url('publications/upload') ?>" class="btn btn-primary">
                    <i class="fas fa-upload me-2"></i>Upload Publication
                </a>
            </div>

            <?php if (empty($publications)): ?>
                <div class="alert alert-info">
                    No publications found matching your criteria.
                </div>
            <?php else: ?>
                <div class="row">
                    <?php foreach ($publications as $publication): ?>
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="<?= base_url('publications/view/' . $publication['id']) ?>" class="text-decoration-none">
                                            <?= esc($publication['title']) ?>
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <small>
                                            <?= esc($publication['authors']) ?>
                                        </small>
                                    </p>
                                    <p class="card-text">
                                        <?= esc(substr($publication['abstract'], 0, 150)) ?>...
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-maroon">
                                            <?= esc($publication['department_name'] ?? '') ?>
                                        </span>
                                        <small class="text-muted">
                                            <?= date('F Y', strtotime($publication['publication_date'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-center mt-4">
                    <?= $pager->links() ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.getElementById('college_id').addEventListener('change', function() {
    const collegeId = this.value;
    const departmentSelect = document.getElementById('department_id');
    
    // Clear existing options
    departmentSelect.innerHTML = '<option value="">All Departments</option>';
    
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
</script>
<?= $this->endSection() ?> 