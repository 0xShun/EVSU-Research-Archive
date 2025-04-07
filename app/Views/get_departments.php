
<?php
// Include database connection
require_once 'config/database.php';

// Get college ID
$college_id = isset($_GET['college_id']) ? (int)$_GET['college_id'] : 0;

// Validate
if ($college_id <= 0) {
    echo json_encode([]);
    exit();
}

// Query departments
$query = "SELECT id, name FROM departments WHERE college_id = $college_id ORDER BY name";
$result = mysqli_query($conn, $query);

// Build JSON response
$departments = [];
while ($row = mysqli_fetch_assoc($result)) {
    $departments[] = [
        'id' => $row['id'],
        'name' => $row['name']
    ];
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($departments);
?>
