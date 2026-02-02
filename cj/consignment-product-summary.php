<?php
include('../../../database/connection.php');
$db = new Database();
$conn = $db->conn;

if (isset($_POST['SIno'])) {
    $SIno = trim($_POST['SIno']);
    $Serialno = trim($_POST['Serialno'] ?? '');
    $Product = trim($_POST['Product'] ?? '');

    if (!empty($Product)) {
        $sql = "SELECT * FROM tbl_invlist WHERE TRIM(SIno) = ? AND TRIM(Product) = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $SIno, $Product);
    } else {
        $sql = "SELECT * FROM tbl_invlist WHERE TRIM(SIno) = ? AND TRIM(Serialno) = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $SIno, $Serialno);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode([
            'Serialno' => $row['Serialno'],
            'SIno' => $row['SIno'],
            'product' => $row['Product'],
            'Supplier' => $row['Supplier'],
            'SRP' => $row['SRP'],
            'Quantity' => $row['Quantity'],
            'DealerPrice' => $row['DealerPrice'],
            'TotalPrice' => $row['TotalPrice']
        ]);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
}
?>
