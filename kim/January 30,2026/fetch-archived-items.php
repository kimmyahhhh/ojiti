<?php
include('../../../database/connection.php');
$db = new Database();
$conn = $db->conn;

// Fetch all items from tbl_purchasereturned that are archived
$sql = "SELECT * FROM tbl_purchasereturned WHERE Status = 'Archived' ORDER BY DateAdded DESC";
$result = $conn->query($sql);

$data = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

echo json_encode($data);
?>
