
<?php
// Include database connection
require_once 'config/database.php';

// Get department ID
$department_id = isset($_GET['department_id']) ? (int)$_GET['department_id'] : 0;

// Validate
if ($department_id <= 0) {
    echo json_encode([]);
    exit();
}

// Query programs
$query = "SELECT id, name FROM programs WHERE department_id = $department_id ORDER BY name";
$result = mysqli_query($conn, $query);

// Build JSON response
$programs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $programs[] = [
        'id' => $row['id'],
        'name' => $row['name']
    ];
}

// Return JSON
header('Content-Type: application/json');
echo json_encode($programs);
?>
