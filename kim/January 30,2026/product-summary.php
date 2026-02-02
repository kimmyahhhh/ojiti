<?php
// Disable error reporting to output for JSON responses
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

include('../../../database/connection.php');
$db = new Database();
$conn = $db->conn;

if (isset($_POST['SIno'])) {
    $SIno = $_POST['SIno'];
    $Serialno = isset($_POST['Serialno']) ? $_POST['Serialno'] : '';
    $Product = isset($_POST['Product']) ? $_POST['Product'] : '';

    try {
        // Construct query
        $sql = "SELECT * FROM tbl_invlist WHERE SIno = ?";
        $params = [$SIno];
        $types = "s";

        if (!empty($Product)) {
            $sql .= " AND Product = ?";
            $params[] = $Product;
            $types .= "s";
        }
        
        if (!empty($Serialno)) {
            $sql .= " AND Serialno = ?";
            $params[] = $Serialno;
            $types .= "s";
        }

        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            
            $response = [
                'SIno' => $row['SIno'],
                'Serialno' => $row['Serialno'],
                'product' => $row['Product'], // JS expects lowercase 'product'
                'Supplier' => $row['Supplier'],
                'Category' => $row['Category'],
                'Type' => $row['Type'],
                'Branch' => $row['Branch'],
                'SRP' => $row['SRP'],
                'Quantity' => $row['Quantity'],
                'DealerPrice' => $row['DealerPrice'],
                'TotalPrice' => $row['TotalPrice']
            ];
            
            echo json_encode($response);
        } else {
            echo json_encode(['error' => 'Product not found with SI: ' . $SIno]);
        }
        $stmt->close();
    } catch (Exception $e) {
        error_log("Error in product-summary.php: " . $e->getMessage());
        echo json_encode(['error' => 'Database error occurred']);
    }
} else {
    echo json_encode(['error' => 'Missing required parameters']);
}
?>
