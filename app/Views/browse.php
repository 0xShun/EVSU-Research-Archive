
<?php
// Initialize session
session_start();

// Include database connection
require_once 'config/database.php';

// Get college filter
$college_filter = isset($_GET['college']) ? (int)$_GET['college'] : 0;

// Include header
include_once 'includes/header.php';
?>

<main class="container py-4">
    <h1 class="mb-4">Browse Research Publications</h1>
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="mb-0">Filter by College</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="browse.php" class="btn <?php echo ($college_filter == 0) ? 'btn-maroon' : 'btn-outline-maroon'; ?>">All</a>
                        
                        <?php
                        $college_query = "SELECT * FROM colleges ORDER BY name";
                        $college_result = mysqli_query($conn, $college_query);
                        
                        while ($college = mysqli_fetch_assoc($college_result)) {
                            $active_class = ($college_filter == $college['id']) ? 'btn-maroon' : 'btn-outline-maroon';
                            echo "<a href='browse.php?college={$college['id']}' class='btn {$active_class}'>{$college['short_name']}</a>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <?php
            if ($college_filter > 0) {
                // Get college name
                $college_name_query = "SELECT name FROM colleges WHERE id = $college_filter";
                $college_name_result = mysqli_query($conn, $college_name_query);
                $college_name = mysqli_fetch_assoc($college_name_result)['name'];
                
                echo "<h2 class='mb-4'>{$college_name}</h2>";
                
                // Get departments for this college
                $dept_query = "SELECT * FROM departments WHERE college_id = $college_filter ORDER BY name";
                $dept_result = mysqli_query($conn, $dept_query);
                
                while ($department = mysqli_fetch_assoc($dept_result)) {
                    echo "<div class='college-section mb-5'>";
                    echo "<h3>{$department['name']}</h3>";
                    
                    // Get programs for this department
                    $prog_query = "SELECT * FROM programs WHERE department_id = {$department['id']} ORDER BY name";
                    $prog_result = mysqli_query($conn, $prog_query);
                    
                    echo "<div class='accordion mb-3' id='dept{$department['id']}'>";
                    while ($program = mysqli_fetch_assoc($prog_result)) {
                        echo "<div class='accordion-item'>";
                        echo "<h4 class='accordion-header'>";
                        echo "<button class='accordion-button collapsed' type='button' data-bs-toggle='collapse' data-bs-target='#prog{$program['id']}'>";
                        echo "{$program['name']} ({$program['short_name']})";
                        echo "</button>";
                        echo "</h4>";
                        
                        echo "<div id='prog{$program['id']}' class='accordion-collapse collapse' data-bs-parent='#dept{$department['id']}'>";
                        echo "<div class='accordion-body'>";
                        
                        // Get publications for this program
                        $pub_query = "SELECT * FROM publications WHERE program_id = {$program['id']} ORDER BY publication_date DESC";
                        $pub_result = mysqli_query($conn, $pub_query);
                        
                        if (mysqli_num_rows($pub_result) > 0) {
                            echo "<div class='row'>";
                            while ($publication = mysqli_fetch_assoc($pub_result)) {
                                echo "<div class='col-md-6 mb-4'>";
                                echo "<div class='card h-100 shadow-sm publication-card'>";
                                echo "<div class='card-body'>";
                                echo "<h5 class='card-title'>" . htmlspecialchars($publication['title']) . "</h5>";
                                echo "<p class='card-text text-muted'>" . htmlspecialchars($publication['authors']) . "</p>";
                                echo "<p class='card-text small'>" . substr(htmlspecialchars($publication['abstract']), 0, 100) . "...</p>";
                                echo "</div>";
                                echo "<div class='card-footer bg-white'>";
                                echo "<a href='publication.php?id={$publication['id']}' class='btn btn-sm btn-outline-maroon'>View Details</a>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                            echo "</div>";
                        } else {
                            echo "<p>No publications found for this program.</p>";
                        }
                        
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                // Display all colleges
                $colleges_query = "SELECT * FROM colleges ORDER BY name";
                $colleges_result = mysqli_query($conn, $colleges_query);
                
                while ($college = mysqli_fetch_assoc($colleges_result)) {
                    echo "<div class='college-section mb-5'>";
                    echo "<h2>{$college['name']} ({$college['short_name']})</h2>";
                    
                    // Get recent publications for this college
                    $pub_query = "SELECT * FROM publications WHERE college_id = {$college['id']} ORDER BY publication_date DESC LIMIT 6";
                    $pub_result = mysqli_query($conn, $pub_query);
                    
                    if (mysqli_num_rows($pub_result) > 0) {
                        echo "<div class='row'>";
                        while ($publication = mysqli_fetch_assoc($pub_result)) {
                            echo "<div class='col-md-4 mb-4'>";
                            echo "<div class='card h-100 shadow-sm publication-card'>";
                            echo "<div class='card-body'>";
                            echo "<h5 class='card-title'>" . htmlspecialchars($publication['title']) . "</h5>";
                            echo "<p class='card-text text-muted'>" . htmlspecialchars($publication['authors']) . "</p>";
                            echo "<p class='card-text small'>" . substr(htmlspecialchars($publication['abstract']), 0, 100) . "...</p>";
                            echo "</div>";
                            echo "<div class='card-footer bg-white'>";
                            echo "<a href='publication.php?id={$publication['id']}' class='btn btn-sm btn-outline-maroon'>View Details</a>";
                            echo "</div>";
                            echo "</div>";
                            echo "</div>";
                        }
                        echo "</div>";
                        
                        echo "<div class='text-end'>";
                        echo "<a href='browse.php?college={$college['id']}' class='btn btn-sm btn-maroon'>View All from {$college['short_name']}</a>";
                        echo "</div>";
                    } else {
                        echo "<p>No publications found for this college.</p>";
                    }
                    
                    echo "</div>";
                }
            }
            ?>
        </div>
    </div>
</main>

<?php
// Include footer
include_once 'includes/footer.php';
?>
