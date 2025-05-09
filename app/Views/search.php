<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container py-4">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Search Publications</h1>
            
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form id="searchForm" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" class="form-control form-control-lg" id="searchQuery" name="q" placeholder="Search by title, author, keywords..." value="<?= esc($query ?? '') ?>">
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="college_id" name="college_id">
                                <option value="">All Colleges</option>
                                <?php foreach ($colleges as $college): ?>
                                    <option value="<?= $college['id'] ?>" <?= ($college_id == $college['id']) ? 'selected' : '' ?>>
                                        <?= esc($college['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="department_id" name="department_id">
                                <option value="">All Departments</option>
                                <?php foreach ($departments as $department): ?>
                                    <option value="<?= $department['id'] ?>" <?= ($department_id == $department['id']) ? 'selected' : '' ?>>
                                        <?= esc($department['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="year" name="year">
                                <option value="">All Years</option>
                                <?php for($i = date('Y'); $i >= 2000; $i--): ?>
                                    <option value="<?= $i ?>" <?= ($year == $i) ? 'selected' : '' ?>><?= $i ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-select" id="category" name="category">
                                <option value="">All Categories</option>
                                <option value="thesis">Thesis</option>
                                <option value="capstone">Capstone</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div id="searchResults">
                <!-- Results will be loaded here -->
            </div>

            <div id="pagination" class="d-flex justify-content-center mt-4">
                <!-- Pagination will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let totalPages = 1;

function loadResults(page = 1) {
    const query = document.getElementById('searchQuery').value;
    if (!query) return;

    currentPage = page;
    
    // Show loading state
    document.getElementById('searchResults').innerHTML = `
        <div class="text-center py-5">
            <div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;

    // Fetch results
    fetch(`<?= base_url('api/publications/search') ?>?q=${encodeURIComponent(query)}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            totalPages = data.pages;
            
            if (data.publications.length === 0) {
                document.getElementById('searchResults').innerHTML = `
                    <div class="alert alert-info">
                        No publications found matching "${query}"
                    </div>
                `;
                document.getElementById('pagination').innerHTML = '';
                return;
            }

            // Render results
            document.getElementById('searchResults').innerHTML = `
                <div class="row">
                    ${data.publications.map(pub => `
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title">
                                        <a href="<?= base_url('publications/view/') ?>${pub.id}" class="text-decoration-none">
                                            ${escapeHtml(pub.title)}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted">
                                        <small>${escapeHtml(pub.authors)}</small>
                                    </p>
                                    <p class="card-text">
                                        ${escapeHtml(pub.abstract.substring(0, 150))}...
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-maroon">
                                            ${escapeHtml(pub.department_name || '')}
                                        </span>
                                        <small class="text-muted">
                                            ${new Date(pub.publication_date).toLocaleDateString('en-US', {
                                                year: 'numeric',
                                                month: 'long'
                                            })}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;

            // Render pagination
            if (totalPages > 1) {
                let pagination = '<ul class="pagination">';
                
                // Previous button
                pagination += `
                    <li class="page-item ${page === 1 ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="loadResults(${page - 1}); return false;">
                            Previous
                        </a>
                    </li>
                `;

                // Page numbers
                for (let i = 1; i <= totalPages; i++) {
                    pagination += `
                        <li class="page-item ${i === page ? 'active' : ''}">
                            <a class="page-link" href="#" onclick="loadResults(${i}); return false;">
                                ${i}
                            </a>
                        </li>
                    `;
                }

                // Next button
                pagination += `
                    <li class="page-item ${page === totalPages ? 'disabled' : ''}">
                        <a class="page-link" href="#" onclick="loadResults(${page + 1}); return false;">
                            Next
                        </a>
                    </li>
                `;

                pagination += '</ul>';
                document.getElementById('pagination').innerHTML = pagination;
            } else {
                document.getElementById('pagination').innerHTML = '';
            }
        })
        .catch(error => {
            document.getElementById('searchResults').innerHTML = `
                <div class="alert alert-danger">
                    An error occurred while searching. Please try again.
                </div>
            `;
            console.error('Search error:', error);
        });
}

function escapeHtml(unsafe) {
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

document.getElementById('searchForm').addEventListener('submit', function(e) {
    e.preventDefault();
    loadResults(1);
});

// Load results on page load if query exists
if (document.getElementById('searchQuery').value) {
    loadResults(1);
}
</script>
<?= $this->endSection() ?>
