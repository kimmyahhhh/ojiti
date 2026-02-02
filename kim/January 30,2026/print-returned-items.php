<?php
    session_start();
    if (isset($_SESSION['EMPNO']) && isset($_SESSION['USERNAME']) && isset($_SESSION["AUTHENTICATED"]) && $_SESSION["AUTHENTICATED"] === true) {
        include('../../database/connection.php');
        $db = new Database();
        $conn = $db->conn;

        $categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';
        $mode = isset($_GET['mode']) ? $_GET['mode'] : '';

        // Check for session-based printing (Print & Archive workflow)
        if ($mode === 'session' && isset($_SESSION['print_ids']) && !empty($_SESSION['print_ids'])) {
            $ids = $_SESSION['print_ids'];
            $count = count($ids);
            $types = str_repeat('s', $count);
            $placeholders = implode(',', array_fill(0, $count, '?'));
            
            // Use Batchno consistently
            $sql = "SELECT * FROM tbl_purchasereturned WHERE Batchno IN ($placeholders) ORDER BY Category ASC, Product ASC";
            $stmt = $conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param($types, ...$ids);
                $stmt->execute();
                $result = $stmt->get_result();
            } else {
                // Fallback or empty result if prepare fails
                $result = false;
            }
            
            // Optional: Clear session ids to prevent re-printing same batch on refresh (optional, but good practice)
            // unset($_SESSION['print_ids']); 
        } 
        // Fallback to original behavior (Filtered view)
        elseif ($categoryFilter && $categoryFilter !== 'All') {
            $sql = "SELECT * FROM tbl_purchasereturned WHERE Category = ? AND (Status IS NULL OR Status != 'Archived') ORDER BY Category ASC, Product ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $categoryFilter);
            $stmt->execute();
            $result = $stmt->get_result();
        } else {
            $sql = "SELECT * FROM tbl_purchasereturned WHERE (Status IS NULL OR Status != 'Archived') ORDER BY Category ASC, Product ASC";
            $result = $conn->query($sql);
        }
        
        $items = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $items[$row['Category']][] = $row;
            }
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchased Return Items - Print</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .category-header {
            background-color: #e9ecef;
            font-weight: bold;
        }
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">Print Report</button>
        <button onclick="window.close()">Close</button>
    </div>

    <div class="header">
        <h2>iSynergies Inc.</h2>
        <h3>Purchased Return Items Report</h3>
        <p>Date: <?php echo date('F d, Y'); ?></p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>SI No.</th>
                <th>Serial No.</th>
                <th>Quantity</th>
                <th>Type</th>
                <th>Reason</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($items)): ?>
                <?php foreach ($items as $category => $categoryItems): ?>
                    <tr class="category-header">
                        <td colspan="6">Category: <?php echo htmlspecialchars($category); ?></td>
                    </tr>
                    <?php foreach ($categoryItems as $item): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['Product']); ?></td>
                            <td><?php echo htmlspecialchars($item['SIno']); ?></td>
                            <td><?php echo htmlspecialchars($item['Serialno']); ?></td>
                            <td><?php echo htmlspecialchars($item['Quantity']); ?></td>
                            <td><?php echo htmlspecialchars($item['Type']); ?></td>
                            <td><?php echo htmlspecialchars($item['Reason']); ?></td>
                            <td>Returned</td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center;">No returned items found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
<?php
    } else {
        echo '<script> window.location.href = "../../login.php"; </script>';
    }
?>
