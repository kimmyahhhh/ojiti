<?php
include_once("../../database/connection.php");

class Process extends Database
{
    public function GetMainChartData($data){
        $year = $data['year'];
        
        // Initialize arrays with 0 for 12 months
        $netSales = array_fill(0, 12, 0);
        $grossSales = array_fill(0, 12, 0);
        
        // Handle both m/d/Y and Y-m-d formats
        $dateExpr = "DateSold";
        $dtParsed = "COALESCE(STR_TO_DATE(".$dateExpr.", '%m/%d/%Y'), STR_TO_DATE(".$dateExpr.", '%Y-%m-%d'))";

        $sql = "SELECT 
                    MONTH($dtParsed) as Month, 
                    SUM(NetSales) as Net, 
                    SUM(GrossSales) as Gross 
                FROM tbl_salesjournal 
                WHERE YEAR($dtParsed) = ? 
                GROUP BY Month";
                
        $stmt = $this->conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param('s', $year);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while($row = $result->fetch_assoc()){
                // Month is 1-12, array index is 0-11
                $idx = intval($row['Month']) - 1;
                if($idx >= 0 && $idx < 12){
                    $netSales[$idx] = floatval($row['Net']);
                    $grossSales[$idx] = floatval($row['Gross']);
                }
            }
            $stmt->close();
        }
        
        echo json_encode(array(
            "NET" => $netSales,
            "GROSS" => $grossSales
        ));
    }

    public function GetInventoryChartData($data){
        $year = $data['year'];
        
        // Initialize arrays with 0 for 12 months
        $invCost = array_fill(0, 12, 0);
        $invSrp = array_fill(0, 12, 0);
        
        $dateExpr = "DateAdded"; // Using DateAdded for inventory flow over time
        // FIX: Ensure correct parsing logic for m/d/Y or Y-m-d. 
        // If DateAdded is datetime, we should just use it. If varchar, we parse.
        // Assuming varchar given the COALESCE pattern used elsewhere.
        $dtParsed = "COALESCE(STR_TO_DATE(".$dateExpr.", '%m/%d/%Y'), STR_TO_DATE(".$dateExpr.", '%Y-%m-%d'))";

        if ($this->ensureTableExists('tbl_invlist')) {
            $sql = "SELECT 
                        MONTH($dtParsed) as Month, 
                        SUM(DealerPrice * Quantity) as Cost, 
                        SUM(TotalSRP) as SRP 
                    FROM tbl_invlist 
                    WHERE YEAR($dtParsed) = ? 
                    GROUP BY Month";
                    
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('s', $year);
                $stmt->execute();
                $result = $stmt->get_result();
                
                while($row = $result->fetch_assoc()){
                    $idx = intval($row['Month']) - 1;
                    if($idx >= 0 && $idx < 12){
                        $invCost[$idx] = floatval($row['Cost']);
                        $invSrp[$idx] = floatval($row['SRP']);
                    }
                }
                $stmt->close();
            }
        }
        
        echo json_encode(array(
            "COST" => $invCost,
            "SRP" => $invSrp
        ));
    }

    public function GetDashboardStats($data){
        // Fetch current year data for dashboard widgets
        $dateFromInput = isset($data['dateFrom']) ? $data['dateFrom'] : null;
        $dateToInput = isset($data['dateTo']) ? $data['dateTo'] : null;
        
        // Legacy fallback
        if (!$dateFromInput && isset($data['date'])) {
            $dateFromInput = $data['date'];
            $dateToInput = $data['date']; // Single day range
        }
        
        // If still null, default to today
        if (!$dateFromInput) {
            $dateFromInput = date('Y-m-d');
            $dateToInput = date('Y-m-d');
        }
        
        // Format for SQL
        $dateFrom = date('Y-m-d', strtotime($dateFromInput));
        $dateTo = date('Y-m-d', strtotime($dateToInput));
        
        // For year-based context (if needed elsewhere)
        $year = date('Y', strtotime($dateTo));
        
        $stats = [
            'revenue' => 0,
            'expenses' => 0,
            'income' => 0,
            'receivable' => 0,
            'payable' => 0,
            'income_budget' => 0,
            'expenses_budget' => 0,
            'inventory_cost' => 0,
            'inventory_srp' => 0,
            'today_sales' => 0,
            'members' => 0
        ];

        // Helper for date parsing
        $dateSoldParsed = "COALESCE(STR_TO_DATE(DateSold, '%m/%d/%Y'), STR_TO_DATE(DateSold, '%Y-%m-%d'))";
        $datePurchaseParsed = "COALESCE(STR_TO_DATE(DatePurchase, '%m/%d/%Y'), STR_TO_DATE(DatePurchase, '%Y-%m-%d'))";
        $dateAddedParsed = "COALESCE(STR_TO_DATE(DateAdded, '%m/%d/%Y'), STR_TO_DATE(DateAdded, '%Y-%m-%d'))";

        // 1. Revenue (Gross Sales from tbl_salesjournal)
        if ($this->ensureTableExists('tbl_salesjournal')) {
            $sql = "SELECT SUM(GrossSales) as total FROM tbl_salesjournal WHERE $dateSoldParsed BETWEEN ? AND ?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('ss', $dateFrom, $dateTo);
                $stmt->execute();
                $res = $stmt->get_result();
                if($row = $res->fetch_assoc()){
                    $stats['revenue'] = floatval($row['total']);
                }
                $stmt->close();
            }
        }

        // 2. Expenses (NetPurchase from tbl_purchasejournal)
        if ($this->ensureTableExists('tbl_purchasejournal')) {
            $sql = "SELECT SUM(NetPurchase) as total FROM tbl_purchasejournal WHERE $datePurchaseParsed BETWEEN ? AND ?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('ss', $dateFrom, $dateTo);
                $stmt->execute();
                $res = $stmt->get_result();
                if($row = $res->fetch_assoc()){
                    $stats['expenses'] = floatval($row['total']);
                }
                $stmt->close();
            }
        }

        // 3. Income (Revenue - Expenses)
        $stats['income'] = $stats['revenue'] - $stats['expenses'];

        // 4. Accounts Receivable (Unpaid Transactions) - usually cumulative, but let's filter by date range of transaction?
        // Usually AR is "current outstanding", regardless of when it happened. 
        // But if filtering by date range, maybe we want "AR generated in this period"?
        // Standard dashboard practice: AR is a snapshot of NOW.
        // However, user asked for "Date From and To".
        // Let's filter AR by the transaction date within range AND status not paid.
        // Actually, if I filter AR by date, I miss old debts.
        // Let's assume the filter applies to "Activity during this period".
        // BUT for "Balance Sheet" items like Inventory/AR, it's ambiguous.
        // Let's stick to "Transactions within this period that are unpaid".
        
        // Wait, AR is total owed. If I filter by "This Month", I only see debts from this month.
        // That might be what they want if analyzing performance.
        // Let's apply the date filter to creation date.
        
        // Check tbl_transaction columns. Assuming 'Date' or 'DateAdded'.
        // Assuming 'Date' column exists based on context or use DateSold from salesjournal link?
        // tbl_transaction usually has 'Date' or 'DateTransaction'.
        // Let's check table structure if possible or guess.
        // I'll assume 'Date' or 'TransactionDate'.
        // Let's use a safe fallback or just keep it total if unsure.
        // Given the request "add a date from and to", users expect filters to apply.
        // I will attempt to filter if column exists, else filter by nothing (all time).
        // Actually, let's look at previous code.
        // Previous code: "SELECT SUM(TotalPrice) as total FROM tbl_transaction WHERE Status != 'PAID' ..."
        // No date filter was used before.
        // I will leave AR as "Total Outstanding" (Snapshot) because debt doesn't disappear just because you changed the date filter, unless you want "Debt incurred in this period".
        // BUT, for consistency, if I change the date to last year, I shouldn't see today's debt?
        // That's hard to calculate historically.
        // I'll keep AR/Inventory as SNAPSHOTS (Current State) ignoring date filter, OR apply filter to "DateAdded".
        // Let's apply filter to DateAdded/Date if possible to show "Production" in that period.
        // BUT "Inventory Value" is definitely a snapshot of CURRENT stock. Filtering by date added shows "Inventory Added in this period", not "Current Value".
        // I will keep Inventory and AR as SNAPSHOTS (ignoring date) for now, as that's safer for "Assets".
        // Revenue/Expenses/Income are FLOWS, so they MUST use the date filter.
        
        if ($this->ensureTableExists('tbl_transaction')) {
            // Keeping as Snapshot of current receivables
            $sql = "SELECT SUM(TotalPrice) as total FROM tbl_transaction WHERE Status != 'PAID' AND Status != 'CANCELLED'";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->execute();
                $res = $stmt->get_result();
                if($row = $res->fetch_assoc()){
                    $stats['receivable'] = floatval($row['total']);
                }
                $stmt->close();
            }
        }

        // ... Inventory ...
        // 7. Inventory Value (Cost) - Apply Date Filter if possible
        if ($this->ensureTableExists('tbl_invlist')) {
            // Apply filter to DateAdded or DatePurchase to show "Inventory Added" in this period?
            // OR keep as snapshot?
            // User feedback implies they EXPECT it to change.
            // If the user wants to see "Inventory Value" relative to the filtered date,
            // they likely mean "Inventory Added/Purchased during this period".
            // Let's use DateAdded for filtering.
            
            // Note: This changes the meaning from "Total Current Asset" to "Assets Acquired in Period".
            // This is consistent with Revenue/Expenses behavior.
            
            $sql = "SELECT SUM(DealerPrice * Quantity) as total FROM tbl_invlist WHERE $dateAddedParsed BETWEEN ? AND ?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('ss', $dateFrom, $dateTo);
                $stmt->execute();
                $res = $stmt->get_result();
                if($row = $res->fetch_assoc()){
                    $stats['inventory_cost'] = floatval($row['total']);
                }
                $stmt->close();
            }
        }

        // 8. Inventory Value (SRP) - Apply Date Filter
        if ($this->ensureTableExists('tbl_invlist')) {
            $sql = "SELECT SUM(TotalSRP) as total FROM tbl_invlist WHERE $dateAddedParsed BETWEEN ? AND ?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('ss', $dateFrom, $dateTo);
                $stmt->execute();
                $res = $stmt->get_result();
                if($row = $res->fetch_assoc()){
                    $stats['inventory_srp'] = floatval($row['total']);
                }
                $stmt->close();
            }
        }

        // 9. Today's Sales -> Renaming concept to "Sales in Range" or keeping "Today"?
        // The widget says "Today".
        // If the user selects a range, "Today" widget might be confusing if it shows the range total.
        // But "Revenue" widget already shows range total.
        // Let's keep "Today" as strictly TODAY regardless of filter? 
        // Or make it "Daily Average" in range?
        // Let's keep it as "Today's Sales" (Actual Today) to provide a "Live" anchor.
        
        if ($this->ensureTableExists('tbl_salesjournal')) {
            $todayDate = date('Y-m-d'); // Always today
            $sql = "SELECT SUM(GrossSales) as total FROM tbl_salesjournal WHERE DATE($dateSoldParsed) = ?";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('s', $todayDate);
                $stmt->execute();
                $res = $stmt->get_result();
                if($row = $res->fetch_assoc()){
                    $stats['today_sales'] = floatval($row['total']);
                }
                $stmt->close();
            }
        }

        // 10. Total Members - SNAPSHOT
        if ($this->ensureTableExists('tbl_clientlist')) {
            $sql = "SELECT COUNT(*) as total FROM tbl_clientlist";
            $stmt = $this->conn->prepare($sql);
            if ($stmt) {
                $stmt->execute();
                $res = $stmt->get_result();
                if($row = $res->fetch_assoc()){
                    $stats['members'] = intval($row['total']);
                }
                $stmt->close();
            }
        }

        echo json_encode($stats);
    }

    private function ensureTableExists($table){
        // Basic check to avoid crash if table missing
        $check = $this->conn->query("SHOW TABLES LIKE '$table'");
        return $check->num_rows > 0;
    }
}
?>
