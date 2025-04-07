<?php
include 'includes/header.php'; // Include the header to match the website theme
require_once 'config/database.php'; // Use require_once for better error handling

$id = isset($_GET['id']) ? $conn->real_escape_string($_GET['id']) : null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $conn->real_escape_string($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $authors = $conn->real_escape_string(implode(', ', array_map('trim', explode(',', $_POST['authors']))));
    $abstract = $conn->real_escape_string($_POST['abstract']);
    $keywords = $conn->real_escape_string($_POST['keywords']);
    $college_id = $_POST['college_id'];
    $department_id = $_POST['department_id'];
    $program_id = $_POST['program_id'];
    $publication_date = $_POST['publication_date'];

    $query = "UPDATE publications SET title=?, authors=?, abstract=?, keywords=?, college_id=?, department_id=?, program_id=?, publication_date=? WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssiiisi", $title, $authors, $abstract, $keywords, $college_id, $department_id, $program_id, $publication_date, $id);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Publication updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
    $stmt->close();
} else {
    // Fetch existing data to populate the form
    $query = "SELECT * FROM publications WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $stmt->close();
}

?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title">Update Publication</h2>
                    <form action="update_publication.php" method="post" class="needs-validation" novalidate>
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="title" name="title" value="<?php echo htmlspecialchars($data['title']); ?>" required>
                            <div class="invalid-feedback">
                                Please provide a title.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="authors" class="form-label">Authors</label>
                            <input type="text" class="form-control" id="authors" name="authors" value="<?php echo htmlspecialchars($data['authors']); ?>" placeholder="Separate authors with commas" required>
                            <div class="invalid-feedback">
                                Please provide authors.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="abstract" class="form-label">Abstract</label>
                            <textarea class="form-control" id="abstract" name="abstract" rows="3" required><?php echo htmlspecialchars($data['abstract']); ?></textarea>
                            <div class="invalid-feedback">
                                Please provide an abstract.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="keywords" class="form-label">Keywords</label>
                            <input type="text" class="form-control" id="keywords" name="keywords" value="<?php echo htmlspecialchars($data['keywords']); ?>" required>
                            <div class="invalid-feedback">
                                Please provide keywords.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="college_id" class="form-label">College</label>
                            <select class="form-control" id="college_id" name="college_id" required>
                                <?php
                                $college_query = "SELECT id, name FROM colleges ORDER BY name";
                                $college_result = $conn->query($college_query);
                                while ($row = $college_result->fetch_assoc()) {
                                    $selected = $row['id'] == $data['college_id'] ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="department_id" the form-label">Department</label>
                            <select class="form-control" id="department_id" name="department_id" required>
                                <?php
                                $department_query = "SELECT id, name FROM departments ORDER BY name";
                                $department_result = $conn->query($department_query);
                                while ($row = $department_result->fetch_assoc()) {
                                    $selected = $row['id'] == $data['department_id'] ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="program_id" class="form-label">Program</label>
                            <select class="form-control" id="program_id" name="program_id" required>
                                <?php
                                $program_query = "SELECT id, name FROM programs ORDER BY name";
                                $program_result = $conn->query($program_query);
                                while ($row = $program_result->fetch_assoc()) {
                                    $selected = $row['id'] == $data['program_id'] ? 'selected' : '';
                                    echo "<option value='" . $row['id'] . "' $selected>" . htmlspecialchars($row['name']) . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="publication_date" class="form-label">Publication Date</label>
                            <input type="date" class="form-control" id="publication_date" name="publication_date" value="<?php echo htmlspecialchars($data['publication_date']); ?>" required>
                            <div class="invalid-feedback">
                                Please provide a publication date.
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
