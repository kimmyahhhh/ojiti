<?php
// Disable error reporting to output for JSON responses
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

include_once(__DIR__ . '/../../../database/connection.php');
$db = new Database();
$conn = $db->conn;

if (!isset($_POST['item_name'])) {
    echo json_encode(['error' => 'Item name is required']);
    exit;
}

$itemName = $_POST['item_name'];
$parentId = 0;

// Map item names to their specific Maintenance Module IDs
// Type -> 171 (Shared with Product Maintenance)
// Category -> 172 (Shared with Product Maintenance)
// Return Type -> 1612 (Specific to Purchased Return)

switch ($itemName) {
    case 'Type':
        $parentId = 1613; // Purchased Return -> Type
        break;
    case 'Category':
        $parentId = 1614; // Purchased Return -> Category
        break;
    case 'Return Type':
        $parentId = 1612;
        break;
    default:
        // Fallback for unknown items (or handle error)
        echo json_encode(['error' => 'Unknown item name requested']);
        exit;
}

try {
    // Query directly by parent ID (module_no)
    $sql = "SELECT module as choice_value 
            FROM tbl_maintenance_module 
            WHERE module_no = ? 
            AND module_type = 3 
            AND status = 1 
            ORDER BY module ASC";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    $stmt->bind_param("i", $parentId);
    if (!$stmt->execute()) {
        throw new Exception('Database execute error: ' . $stmt->error);
    }
    
    $result = $stmt->get_result();

    $choices = [];
    while ($row = $result->fetch_assoc()) {
        $choices[] = $row['choice_value'];
    }

    echo json_encode($choices);

} catch (Exception $e) {
    error_log("Error in fetch-maintenance-options.php: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}
?>