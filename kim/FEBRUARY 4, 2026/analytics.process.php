<?php
include(__DIR__ . "/../../database/connection.php");

header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : 'individual'; // individual, branch, department
$metric = isset($_GET['metric']) ? $_GET['metric'] : 'count'; // count, amount
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 5;

// Drill-down filters
$filterType = isset($_GET['filter_type']) ? $_GET['filter_type'] : ''; // e.g., 'branch', 'department'
$filterValue = isset($_GET['filter_value']) ? $_GET['filter_value'] : ''; // e.g., 'CALASIAO'

$db = new Database();
$conn = $db->conn;

if ($action == 'get_filters') {
    $filterData = [];
    $type = isset($_GET['type']) ? $_GET['type'] : 'branch';
    
    if ($type == 'branch') {
        // Get branches from tbl_books to match what is shown in the table
        $sql = "SELECT DISTINCT Branch as name FROM tbl_books 
                WHERE Branch IS NOT NULL 
                AND Branch != '' 
                AND Branch NOT IN ('HEADOFFICE', 'HEAD OFFICE', 'ISYNERGIES, INC.', '37568 : ISYNERGIES, INC.', 'HO') 
                ORDER BY Branch ASC";
        $result = $conn->query($sql);
    } elseif ($type == 'category') {
        $filterData = ['HEADOFFICE', 'EXTERNAL CLIENT', 'MFI BRANCHES', 'STAFF', 'BUSINESS UNIT', 'OTHERS', 'INDIVIDUAL'];
        echo json_encode($filterData);
        exit;
    } elseif ($type == 'department') {
        $sql = "SELECT department_name as name FROM tbl_master_departments ORDER BY department_name ASC";
        $result = $conn->query($sql);
    } elseif ($type == 'individual') {
        // Just return top individuals to avoid loading thousands
         $sql = "SELECT full_name as name FROM tbl_master_identities ORDER BY full_name ASC LIMIT 100";
         $result = $conn->query($sql);
    }

    if (isset($result) && $result) {
        while ($row = $result->fetch_assoc()) {
            $filterData[] = $row['name'];
        }
    }
    echo json_encode($filterData);
    exit;
}

if ($action == 'get_top_performers') {
    $data = [];
    
    // Categorization Logic
    $categoryCase = "CASE 
        WHEN b.Branch IN ('HEADOFFICE', 'HO', 'ISYNERGIES, INC.', '37568 : ISYNERGIES, INC.') THEN 'HEADOFFICE'
        WHEN b.AcctNo LIKE '%@isynergies.com%' OR b.AcctNo LIKE '%@mfi.org%' THEN 'STAFF'
        WHEN (b.AcctNo LIKE '%@gmail.com%' OR b.AcctNo LIKE '%@yahoo.com%' OR b.AcctNo LIKE '%@hotmail.com%' OR b.AcctNo LIKE '%@outlook.com%') 
             AND (b.Branch IS NULL OR b.Branch = '' OR b.Branch = 'Unknown' OR LOWER(b.Branch) NOT LIKE CONCAT('%', LOWER(SUBSTRING_INDEX(b.AcctNo, '@', 1)), '%')) 
             THEN 'INDIVIDUAL'
        WHEN b.Branch LIKE '%BUSINESS%' OR b.Branch LIKE '%UNIT%' OR b.Branch LIKE '%DEPT%' THEN 'BUSINESS UNIT'
        WHEN b.Branch IS NOT NULL AND b.Branch != '' AND b.Branch != 'Unknown' THEN 'MFI BRANCHES'
        WHEN b.AcctNo LIKE '%@gmail.com%' OR b.AcctNo LIKE '%@yahoo.com%' OR b.AcctNo LIKE '%@hotmail.com%' OR b.AcctNo LIKE '%@outlook.com%' THEN 'EXTERNAL CLIENT'
        WHEN (b.Payee IS NOT NULL AND b.Payee != '' AND b.Payee != '-') OR (b.AcctTitle IS NOT NULL AND b.AcctTitle != '') THEN 'INDIVIDUAL'
        ELSE 'OTHERS'
    END";
    
    // Source Logic
    $source = isset($_GET['source']) ? $_GET['source'] : 'all';
    $sourceWhere = "";
    if ($source == 'acash') {
        $sourceWhere = " AND b.SLName = 'ACASH'";
    } elseif ($source == 'ecpay') {
        $sourceWhere = " AND (b.Payee LIKE '%EC%PAY%' OR b.Explanation LIKE '%EC%PAY%' OR b.Explanation LIKE '%ECPAY%')";
    }

    // If a filter is applied, we are always looking for Individuals within that context
    if (!empty($filterType) && !empty($filterValue)) {
        $whereClause = "";
        $joinClause = "";
        $havingClause = "";
        
        if ($filterType == 'branch') {
            // Filter by tbl_books.Branch as requested by user ("choices i can see on the table")
            $joinClause = "LEFT JOIN tbl_master_identities i ON i.full_name = COALESCE(NULLIF(b.Payee, ''), NULLIF(b.AcctTitle, ''))";
            $whereClause = "WHERE b.Branch = '" . $conn->real_escape_string($filterValue) . "'";
        } elseif ($filterType == 'department') {
            $joinClause = "JOIN tbl_master_identities i ON i.full_name = COALESCE(NULLIF(b.Payee, ''), NULLIF(b.AcctTitle, ''))
                           JOIN tbl_master_departments d ON i.department_id = d.id";
            $whereClause = "WHERE d.department_name = '" . $conn->real_escape_string($filterValue) . "'";
        } elseif ($filterType == 'category') {
            // For category, we filter using HAVING on the computed column.
            $joinClause = "LEFT JOIN tbl_master_identities i ON i.full_name = COALESCE(NULLIF(b.Payee, ''), NULLIF(b.AcctTitle, ''))";
            $havingClause = "HAVING Category = '" . $conn->real_escape_string($filterValue) . "'";
            
            // Initialize WHERE if not set by others
            $whereClause = "WHERE 1=1";
        }
        
        // Append Source Filter
        if (empty($whereClause)) $whereClause = "WHERE 1=1";
        $whereClause .= $sourceWhere;

        $nameExpr = "COALESCE(i.full_name, NULLIF(b.Payee, ''), NULLIF(b.AcctTitle, ''), 'Unknown')";

        $sql = "SELECT $nameExpr as Name, 
                       COUNT(*) as TransactionCount, 
                       SUM(b.DrOther) as TotalAmount,
                       $categoryCase as Category
                FROM tbl_books b
                $joinClause
                $whereClause
                GROUP BY Name, Category
                $havingClause
                ORDER BY " . (($metric == 'amount' || $metric == 'all') ? "TotalAmount" : "TransactionCount") . " DESC 
                LIMIT $limit";
        
        $result = $conn->query($sql);
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
            echo json_encode($data);
            exit;
        }
// Removed duplicate code block
    
    // Default Logic (No drill-down)
    $groupBy = "";
    $selectName = "";
    
    if ($type == 'individual') {
        $selectName = "COALESCE(NULLIF(b.Payee, ''), NULLIF(b.AcctTitle, ''), 'Unknown') as Name";
        $groupBy = "Name";
    } elseif ($type == 'branch') {
        $selectName = "b.Branch as Name";
        $groupBy = "b.Branch";
    } elseif ($type == 'category') {
        $selectName = "$categoryCase as Name";
        $groupBy = "Name";
    } elseif ($type == 'department') {
        $selectName = "d.department_name as Name";
        $groupBy = "d.department_name";
        
        $sql = "SELECT $selectName, 
                       COUNT(*) as TransactionCount, 
                       SUM(b.DrOther) as TotalAmount 
                FROM tbl_books b
                LEFT JOIN tbl_master_identities i ON i.full_name = COALESCE(NULLIF(b.Payee, ''), NULLIF(b.AcctTitle, ''))
                LEFT JOIN tbl_master_departments d ON i.department_id = d.id
                WHERE d.department_name IS NOT NULL
                $sourceWhere
                GROUP BY $groupBy 
                ORDER BY " . ($metric == 'amount' ? "TotalAmount" : "TransactionCount") . " DESC 
                LIMIT $limit";
                
        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        echo json_encode($data);
        exit;
    }

    if ($type != 'department') {
        $sql = "SELECT $selectName, 
                       COUNT(*) as TransactionCount, 
                       SUM(b.DrOther) as TotalAmount 
                FROM tbl_books b
                WHERE 1=1 $sourceWhere
                GROUP BY $groupBy 
                ORDER BY " . (($metric == 'amount' || $metric == 'all') ? "TotalAmount" : "TransactionCount") . " DESC 
                LIMIT $limit";

        $result = $conn->query($sql);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        echo json_encode($data);
    }
} elseif ($action == 'get_filters') {
    // Return lists for dropdowns
    $filters = [
        'branches' => [],
        'departments' => []
    ];
    
    $res = $conn->query("SELECT branch_name FROM tbl_master_branches ORDER BY branch_name");
    while ($row = $res->fetch_assoc()) $filters['branches'][] = $row['branch_name'];
    
    $res = $conn->query("SELECT department_name FROM tbl_master_departments ORDER BY department_name");
    while ($row = $res->fetch_assoc()) $filters['departments'][] = $row['department_name'];
    
    echo json_encode($filters);
} else {
    echo json_encode(['error' => 'Invalid action']);
}
?>