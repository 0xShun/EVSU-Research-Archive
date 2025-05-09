<?php
// Initialize session
session_start();

// Include database connection
require_once 'config/database.php';

// Get publication ID
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Query to get publication details
$query = "SELECT p.*, c.name AS college_name, d.name AS department_name, pr.name AS program_name, pr.short_name AS program_short_name
          FROM publications p
          LEFT JOIN colleges c ON p.college_id = c.id
          LEFT JOIN departments d ON p.department_id = d.id
          LEFT JOIN programs pr ON p.program_id = pr.id
          WHERE p.id = $id";
$result = mysqli_query($conn, $query);

// Check if publication exists
if (mysqli_num_rows($result) == 0) {
    header("Location: index.php");
    exit();
}

$publication = mysqli_fetch_assoc($result);

// Include header with default navbar
include_once 'layouts/default.php';
?>

<?= $this->section('content') ?>
<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="browse.php">Browse</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($publication['title']); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h1 class="card-title h3 mb-3"><?php echo htmlspecialchars($publication['title']); ?></h1>
                    
                    <div class="d-flex flex-wrap mb-3">
                        <span class="badge bg-maroon me-2 mb-2"><?php echo htmlspecialchars($publication['college_name']); ?></span>
                        <?php if (!empty($publication['department_name'])): ?>
                            <span class="badge bg-light text-dark me-2 mb-2"><?php echo htmlspecialchars($publication['department_name']); ?></span>
                        <?php endif; ?>
                        <?php if (!empty($publication['program_name'])): ?>
                            <span class="badge bg-light text-dark me-2 mb-2"><?php echo htmlspecialchars($publication['program_short_name']); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <p class="text-muted">
                        <strong>Authors:</strong> <?php echo htmlspecialchars($publication['authors']); ?><br>
                        <strong>Published:</strong> <?php echo date('F j, Y', strtotime($publication['publication_date'])); ?>
                    </p>
                    
                    <h5 class="mt-4">Abstract</h5>
                    <p><?php echo nl2br(htmlspecialchars($publication['abstract'])); ?></p>
                    
                    <?php if (!empty($publication['keywords'])): ?>
                        <h5 class="mt-4">Keywords</h5>
                        <p>
                            <?php
                            $keywords = explode(',', $publication['keywords']);
                            foreach ($keywords as $keyword) {
                                echo '<span class="badge bg-light text-dark me-2">' . trim(htmlspecialchars($keyword)) . '</span>';
                            }
                            ?>
                        </p>
                    <?php endif; ?>
                    
                    <?php if (!empty($publication['file_path'])): ?>
                        <div class="mt-4 d-flex justify-content-between align-items-center">
                            <a href="<?php echo htmlspecialchars($publication['file_path']); ?>" class="btn btn-maroon" target="_blank">
                                <i class="fas fa-file-pdf me-2"></i> View PDF
                            </a>
                            <a href="download.php?file=<?php echo urlencode($publication['file_path']); ?>" class="btn btn-dark" download>
                                <i class="fas fa-download me-2"></i> Download PDF
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-maroon text-white">
                    <h5 class="card-title mb-0">Research Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">College</div>
                                <?php echo htmlspecialchars($publication['college_name']); ?>
                            </div>
                        </li>
                        <?php if (!empty($publication['department_name'])): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div the="fw-bold">Department</div>
                                    <?php echo htmlspecialchars($publication['department_name']); ?>
                                </div>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($publication['program_name'])): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">Program</div>
                                    <?php echo htmlspecialchars($publication['program_name']); ?>
                                </div>
                            </li>
                        <?php endif; ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold">Publication Date</div>
                                <?php echo date('F j, Y', strtotime($publication['publication_date'])); ?>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Related publications section -->
            <?php
            // Get related publications based on college or keywords
            $related_query = "SELECT id, title, authors FROM publications 
                           WHERE id != $id AND college_id = {$publication['college_id']} 
                           LIMIT 5";
            $related_result = mysqli_query($conn, $related_query);
            
            if (mysqli_num_rows($related_result) > 0):
            ?>
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Related Publications</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php while ($related = mysqli_fetch_assoc($related_result)): ?>
                            <li class="list-group-item px-0">
                                <a href="publication.php?id=<?php echo $related['id']; ?>" class="text-decoration-none">
                                    <h6 class="mb-1"><?php echo htmlspecialchars($related['title']); ?></h6>
                                </a>
                                <small class="text-muted"><?php echo htmlspecialchars($related['authors']); ?></small>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
