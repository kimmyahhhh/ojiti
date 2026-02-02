<?php
// Disable error reporting to output for JSON responses
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

include('../../../database/connection.php');
$db = new Database();
$conn = $db->conn;

if (isset($_POST['branch']) && isset($_POST['type']) && isset($_POST['category'])) {
    $branch = $_POST['branch'];
    $type = $_POST['type'];
    $category = $_POST['category'];

    try {
        // Adjust column names if they differ in your database
        $stmt = $conn->prepare("SELECT Product, SIno, Serialno, Quantity FROM tbl_invlist WHERE Branch = ? AND Type = ? AND Category = ? ORDER BY Product ASC");
        
        if (!$stmt) {
             throw new Exception("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sss", $branch, $type, $category);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                'Product' => $row['Product'],
                'SIno' => $row['SIno'],
                'Serialno' => $row['Serialno'],
                'Quantity' => $row['Quantity']
            ];
        }
        
        echo json_encode($data);
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error in product-search.php: " . $e->getMessage());
        echo json_encode([]);
    }
} else {
    echo json_encode([]);
}
?>
