// Start of Selection
<?php
require_once 'config/database.php'; // Use require_once to ensure the database connection file is included

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $id = $conn->real_escape_string($_POST['id']);

    $query = "DELETE FROM publications WHERE id=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Publication deleted successfully!</div>";
        header("Location: view_publication.php"); // Redirect to browse page after deletion
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
    $stmt->close();
} else {
    header("Location: view_publication.php"); // Redirect to home if the method is not POST or id is not set
    exit();
}
?>
