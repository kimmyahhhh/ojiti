<?php
include_once("../../database/connection.php");

class Process extends Database {
    public function SearchPaidUnpaid($data){
        $from = isset($data['fromDate']) ? trim($data['fromDate']) : '';
        $to = isset($data['toDate']) ? trim($data['toDate']) : '';
        $isConsign = isset($data['isConsign']) ? $data['isConsign'] : 'No';
        $typeVal = isset($data['typeVal']) ? $data['typeVal'] : '1';
        $withSI = isset($data['withSI']) ? $data['withSI'] : 'No';
        
        // Map filters
        $status = ($typeVal == '1') ? 'PAID' : 'UNPAID';
        $consignFlag = ($isConsign === 'Yes') ? 'CONSIGNMENT' : 'NO';
        
        // Dates come as yyyy-mm-dd; convert to mm/dd/YYYY strings to match stored format
        $fromStr = $from ? date('m/d/Y', strtotime($from)) : '';
        $toStr = $to ? date('m/d/Y', strtotime($to)) : '';
        $noFilters = ($fromStr === '' && $toStr === '');
        
        // Build dynamic WHERE and params
        $filters = [];
        $types = '';
        $params = [];
        if ($fromStr !== '' && $toStr !== '') {
            $filters[] = "STR_TO_DATE(DateAdded,'%m/%d/%Y') BETWEEN STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(?, '%m/%d/%Y')";
            $types .= 'ss';
            $params[] = $fromStr;
            $params[] = $toStr;
        }
        if (!$noFilters) {
            $filters[] = "Status = ?";
            $types .= 's';
            $params[] = $status;
            $filters[] = "itemConsign = ?";
            $types .= 's';
            $params[] = $consignFlag;
            if ($withSI === 'Yes') {
                $filters[] = "SI IS NOT NULL AND SI <> '-'";
            }
        }
        $where = count($filters) ? implode(' AND ', $filters) : '1=1';
        
        // Items
        $items = [];
        $sqlItems = "SELECT SI, DateAdded, Status, Branch, Product, DealerPrice, TotalPrice, VatSales, TotalSRP, Type FROM tbl_inventoryout WHERE ".$where." ORDER BY STR_TO_DATE(DateAdded,'%m/%d/%Y') DESC, SI";
        $stmt = $this->conn->prepare($sqlItems);
        if (count($params) > 0) {
            $refs = [];
            $refs[] = &$types;
            foreach ($params as $k => $v) { $refs[] = &$params[$k]; }
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()){ $items[] = $row; }
        $stmt->close();
        
        // Totals
        $totals = ["TotalPrice"=>0,"TotalSRP"=>0,"TotalMarkup"=>0,"TotalQty"=>0];
        $sqlTotals = "SELECT SUM(CAST(TotalPrice AS DECIMAL(15,2))) AS TotalPrice, SUM(CAST(TotalSRP AS DECIMAL(15,2))) AS TotalSRP, SUM(CAST(TotalMarkup AS DECIMAL(15,2))) AS TotalMarkup, SUM(CAST(Quantity AS DECIMAL(15,2))) AS TotalQty FROM tbl_inventoryout WHERE ".$where;
        $stmt = $this->conn->prepare($sqlTotals);
        if (count($params) > 0) {
            $refs = [];
            $refs[] = &$types;
            foreach ($params as $k => $v) { $refs[] = &$params[$k]; }
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()){
            $totals["TotalPrice"] = floatval($row["TotalPrice"] ?? 0);
            $totals["TotalSRP"] = floatval($row["TotalSRP"] ?? 0);
            $totals["TotalMarkup"] = floatval($row["TotalMarkup"] ?? 0);
            $totals["TotalQty"] = floatval($row["TotalQty"] ?? 0);
        }
        $stmt->close();
        
        // Clients aggregation
        $clients = [];
        $sqlClients = "SELECT COALESCE(Soldto,'-') AS Customer, SUM(CAST(AmountDue AS DECIMAL(15,2))) AS TotalPayables FROM tbl_inventoryout WHERE ".$where." GROUP BY Soldto ORDER BY TotalPayables DESC";
        $stmt = $this->conn->prepare($sqlClients);
        if (count($params) > 0) {
            $refs = [];
            $refs[] = &$types;
            foreach ($params as $k => $v) { $refs[] = &$params[$k]; }
            call_user_func_array([$stmt, 'bind_param'], $refs);
        }
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()){ $clients[] = $row; }
        $stmt->close();
        
        echo json_encode(["items"=>$items, "totals"=>$totals, "clients"=>$clients]);
    }

    public function GetItemDetails($data){
        $si = isset($data['si']) ? trim($data['si']) : '';
        $date = isset($data['date']) ? trim($data['date']) : '';
        $branch = isset($data['branch']) ? trim($data['branch']) : '';
        if ($si === ''){
            echo json_encode(["item"=>null]);
            return;
        }
        // Prefer matching by SI + Date + Branch when available
        $sql = "";
        $types = "";
        $params = [];
        if ($date !== '' && $branch !== ''){
            $sql = "SELECT * FROM tbl_inventoryout WHERE SI = ? AND DateAdded = ? AND Branch = ? LIMIT 1";
            $types = "sss";
            $params = [$si, $date, $branch];
        } else if ($date !== ''){
            $sql = "SELECT * FROM tbl_inventoryout WHERE SI = ? AND DateAdded = ? LIMIT 1";
            $types = "ss";
            $params = [$si, $date];
        } else {
            $sql = "SELECT * FROM tbl_inventoryout WHERE SI = ? ORDER BY STR_TO_DATE(DateAdded,'%m/%d/%Y') DESC LIMIT 1";
            $types = "s";
            $params = [$si];
        }
        $stmt = $this->conn->prepare($sql);
        $refs = [];
        $refs[] = &$types;
        foreach ($params as $k => $v) { $refs[] = &$params[$k]; }
        call_user_func_array([$stmt, 'bind_param'], $refs);
        $stmt->execute();
        $res = $stmt->get_result();
        $item = null;
        if ($row = $res->fetch_assoc()){
            $item = $row;
        }
        $stmt->close();
        echo json_encode(["item"=>$item]);
    }
    
    public function GetClientDetails($data){
        $customer = isset($data['customer']) ? trim($data['customer']) : '';
        $from = isset($data['fromDate']) ? trim($data['fromDate']) : '';
        $to = isset($data['toDate']) ? trim($data['toDate']) : '';
        $isConsign = isset($data['isConsign']) ? $data['isConsign'] : 'No';
        $typeVal = isset($data['typeVal']) ? $data['typeVal'] : '1';
        $withSI = isset($data['withSI']) ? $data['withSI'] : 'No';
        
        if ($customer === ''){
            echo json_encode(["items"=>[], "total"=>0]);
            return;
        }
        $status = ($typeVal == '1') ? 'PAID' : 'UNPAID';
        $consignFlag = ($isConsign === 'Yes') ? 'CONSIGNMENT' : 'NO';
        $fromStr = $from ? date('m/d/Y', strtotime($from)) : '';
        $toStr = $to ? date('m/d/Y', strtotime($to)) : '';
        $noFilters = ($fromStr === '' && $toStr === '');
        
        $filters = ["Soldto = ?"];
        $types = "s";
        $params = [$customer];
        if ($fromStr !== '' && $toStr !== '') {
            $filters[] = "STR_TO_DATE(DateAdded,'%m/%d/%Y') BETWEEN STR_TO_DATE(?, '%m/%d/%Y') AND STR_TO_DATE(?, '%m/%d/%Y')";
            $types .= 'ss';
            $params[] = $fromStr;
            $params[] = $toStr;
        }
        if (!$noFilters) {
            $filters[] = "Status = ?";
            $types .= 's';
            $params[] = $status;
            $filters[] = "itemConsign = ?";
            $types .= 's';
            $params[] = $consignFlag;
            if ($withSI === 'Yes') {
                $filters[] = "SI IS NOT NULL AND SI <> '-'";
            }
        }
        $where = implode(' AND ', $filters);
        
        $items = [];
        $sqlItems = "SELECT SI, DateAdded, Branch, Status, Product, Quantity, AmountDue, TotalPrice, TotalSRP, Type FROM tbl_inventoryout WHERE ".$where." ORDER BY STR_TO_DATE(DateAdded,'%m/%d/%Y') DESC, SI";
        $stmt = $this->conn->prepare($sqlItems);
        $refs = [];
        $refs[] = &$types;
        foreach ($params as $k => $v) { $refs[] = &$params[$k]; }
        call_user_func_array([$stmt, 'bind_param'], $refs);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()){ $items[] = $row; }
        $stmt->close();
        
        $total = 0;
        $sqlTotal = "SELECT SUM(CAST(AmountDue AS DECIMAL(15,2))) AS TotalPayables FROM tbl_inventoryout WHERE ".$where;
        $stmt = $this->conn->prepare($sqlTotal);
        $refs = [];
        $refs[] = &$types;
        foreach ($params as $k => $v) { $refs[] = &$params[$k]; }
        call_user_func_array([$stmt, 'bind_param'], $refs);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()){
            $total = floatval($row["TotalPayables"] ?? 0);
        }
        $stmt->close();
        
        echo json_encode(["items"=>$items, "total"=>$total]);
    }
}
