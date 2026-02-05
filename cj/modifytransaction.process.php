<?php
include_once("../../database/connection.php");

class Process extends Database
{
    private $maxRows = 5000;
    private function GetLastClosingDateYmd(){
        $stmt = @$this->conn->prepare("SELECT Value FROM tbl_configuration WHERE ConfigName = 'LASTCLOSINGDATE' LIMIT 1");
        if (!$stmt) return '';
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if (!$result) return '';
        $row = $result->fetch_assoc();
        $val = trim($row['Value'] ?? '');
        if ($val === '') return '';
        $ts = strtotime($val);
        if ($ts === false) return '';
        return date("Y-m-d", $ts);
    }
    private function IsDateClosed($selectedDate){
        $last = $this->GetLastClosingDateYmd();
        if ($last === '') return false;
        $selTs = strtotime($selectedDate);
        if ($selTs === false) return false;
        $sel = date("Y-m-d", $selTs);
        return $sel <= $last;
    }
    public function LoadORTypes($data){
        $selectedDate = $data["SelectedDate"] ?? '';
        if ($this->IsDateClosed($selectedDate)){
            echo json_encode(["BLOCKED"=>1,"MESSAGE"=>"Date is already closed.","ORTYPES"=>[]]);
            return;
        }
        $selYmd = date("Y-m-d", strtotime($selectedDate));
        $rows = [];
        $sql = "SELECT ORTYPE FROM (
            SELECT ORTYPE FROM TBL_BOOKS WHERE BOOKTYPE='CRB' AND CDATE = ?
            UNION ALL
            SELECT ORTYPE FROM TBL_BOOKS WHERE BOOKTYPE='CRB' AND CDATE LIKE '%/%/%' AND STR_TO_DATE(CDATE,'%m/%d/%Y') = ?
            UNION ALL
            SELECT ORTYPE FROM TBL_BOOKS WHERE BOOKTYPE='CRB' AND CDATE LIKE '%-%-%' AND STR_TO_DATE(CDATE,'%m-%d-%Y') = ?
        ) t GROUP BY ORTYPE ORDER BY ORTYPE";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("sss", $selYmd, $selYmd, $selYmd);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
        echo json_encode(array("ORTYPES" => $rows));
    }

    public function LoadTransactions($data){
        $ortype = $data["type"];
        $selectedDate = $data["SelectedDate"] ?? '';
        if ($this->IsDateClosed($selectedDate)){
            echo json_encode(["BLOCKED"=>1,"MESSAGE"=>"Date is already closed.","ORLIST"=>[]]);
            return;
        }
        $selYmd = date("Y-m-d", strtotime($selectedDate));
        $data = [];

        $limitPlusOne = $this->maxRows + 1;
        $sql = "SELECT ORNO, PAYEE, CLIENTNO, LOANID, NATURE, FUND, CDATE FROM (
            SELECT ORNO, PAYEE, CLIENTNO, LOANID, NATURE, FUND, CDATE
              FROM TBL_BOOKS
             WHERE BOOKTYPE='CRB' AND ORTYPE = ? AND CDATE = ?
            UNION ALL
            SELECT ORNO, PAYEE, CLIENTNO, LOANID, NATURE, FUND, CDATE
              FROM TBL_BOOKS
             WHERE BOOKTYPE='CRB' AND ORTYPE = ? AND CDATE LIKE '%/%/%' AND STR_TO_DATE(CDATE,'%m/%d/%Y') = ?
            UNION ALL
            SELECT ORNO, PAYEE, CLIENTNO, LOANID, NATURE, FUND, CDATE
              FROM TBL_BOOKS
             WHERE BOOKTYPE='CRB' AND ORTYPE = ? AND CDATE LIKE '%-%-%' AND STR_TO_DATE(CDATE,'%m-%d-%Y') = ?
        ) t
        GROUP BY ORNO, PAYEE, CLIENTNO, LOANID, NATURE, FUND, CDATE
        ORDER BY PAYEE
        LIMIT ".$limitPlusOne;
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssss", $ortype, $selYmd, $ortype, $selYmd, $ortype, $selYmd);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        $truncated = 0;
        if (count($data) > $this->maxRows){
            $data = array_slice($data, 0, $this->maxRows);
            $truncated = 1;
        }
            
        echo json_encode(array(
            "ORLIST" => $data,
            "TRUNCATED" => $truncated,
            "MAXROWS" => $this->maxRows,
        ));
    }

    public function GetORData($data){
        $orno = $data["orno"];
        $cdate = $data["cdate"];
        if ($this->IsDateClosed($cdate)){
            echo json_encode(["BLOCKED"=>1,"MESSAGE"=>"Date is already closed."]);
            return;
        }
        $data = [];
        $cdateYmd = date("Y-m-d", strtotime($cdate));

        $fund = "-";
        $po = "-";
        $nature = "-";

        $principal = 0;
        $interest = 0;
        $cbu = 0;
        $penalty = 0;
        $mba = 0;
        $total = 0;

        $qry = "SELECT * FROM TBL_BOOKS WHERE ORNo = '".$orno."' AND BOOKTYPE = 'CRB' AND CDATE = '".$cdateYmd."'";

        $dateExpr = "COALESCE(STR_TO_DATE(CDATE,'%Y-%m-%d'), STR_TO_DATE(CDATE,'%m/%d/%Y'), STR_TO_DATE(CDATE,'%m-%d-%Y'))";
        $stmt = $this->conn->prepare("SELECT * FROM TBL_BOOKS WHERE ORNo = ? AND BOOKTYPE = 'CRB' AND ".$dateExpr." = ?");
        $stmt->bind_param("ss", $orno, $cdateYmd);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;

            $fund = $row["Fund"];
            $po = $row["PO"];
            $nature = $row["Nature"];

            $gl = strval($row["GLNo"]);
            $slAmt = floatval($row["SLDrCr1"]);
            $drOther = floatval($row["DrOther"]);
            if ($gl === '11920') { $principal += $slAmt; }
            if ($gl === '21370') { $interest += $slAmt; }
            if ($gl === '11120') { $total += $drOther; }
        }

        $stmtLP = $this->conn->prepare("SELECT Fund, Principal, Interest, Penalty, CBU, MBA, Total FROM tbl_loanspayment WHERE ORNo = ? AND DATE(TransactDate) = ? LIMIT 1");
        if ($stmtLP){
            $stmtLP->bind_param("ss", $orno, $cdateYmd);
            $stmtLP->execute();
            $resLP = $stmtLP->get_result();
            if ($lp = $resLP->fetch_assoc()){
                $fund = $lp["Fund"] ?? $fund;
                $principal = floatval($lp["Principal"] ?? 0);
                $interest = floatval($lp["Interest"] ?? 0);
                $penalty = floatval($lp["Penalty"] ?? 0);
                $cbu = floatval($lp["CBU"] ?? 0);
                $mba = floatval($lp["MBA"] ?? 0);
                $total = floatval($lp["Total"] ?? $total);
            }
            $stmtLP->close();
        }
            
        echo json_encode(array(
            "ORDATA" => $data,
            "QRY" => $qry,
            "FUND" => $fund,
            "PO" => $po,
            "NATURE" => $nature,
            "PRINCIPAL" => $principal,
            "INTEREST" => $interest,
            "CBU" => $cbu,
            "PENALTY" => $penalty,
            "MBA" => $mba,
            "TOTAL" => $total
        ));
    }

    public function CancelTransaction($data){
        $orno = $data["orno"];
        $fund = $data["fund"];
        $po = $data["po"];
        $nature = $data["nature"];
        $clientno = $data["clientno"];
        $loanid = $data["loanid"];
        $cdate = $data["cdate"];
        if ($this->IsDateClosed($cdate)){
            echo json_encode(["STATUS"=>"ERROR","MESSAGE"=>"Date is already closed."]);
            return;
        }
        $cdateYmd = date("Y-m-d", strtotime($cdate));
        $dateExpr = "COALESCE(STR_TO_DATE(CDATE,'%Y-%m-%d'), STR_TO_DATE(CDATE,'%m/%d/%Y'), STR_TO_DATE(CDATE,'%m-%d-%Y'))";
        
        if ($nature == "LOAN AMORTIZATION") {
            $stmt = $this->conn->prepare("UPDATE TBL_BOOKS SET PAYEE = 'CANCELLED', DROTHER = '0', CROTHER = '0', SLDRCR = '0', SLDRCR1 = '0',CLIENTNO='-', LOANID='-', EXPLANATION = CONCAT(EXPLANATION, ' (CANCELLED)') WHERE ORNO = ? AND ".$dateExpr." = ? AND CLIENTNO = ? AND LOANID = ?");
            $stmt->bind_param("ssss", $orno, $cdateYmd, $clientno, $loanid);
        } else {
            $stmt = $this->conn->prepare("UPDATE TBL_BOOKS SET PAYEE = 'CANCELLED', DROTHER = '0', CROTHER = '0', SLDRCR = '0', SLDRCR1 = '0',CLIENTNO='-', LOANID='-', EXPLANATION = CONCAT(EXPLANATION, ' (CANCELLED)') WHERE ORNO = ? AND ".$dateExpr." = ? AND FUND = ? AND PO = ?");
            $stmt->bind_param("ssss", $orno, $cdateYmd, $fund, $po);
        }

        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        
        $status = "";
        if($result > 0){
            $status = "SUCCESS";
        } else {
            $status = "ERROR";
        }

        echo json_encode(array(
            "STATUS" => $status,
            // "VALUES" => $values,
            // "QRY" => $qry,
        ));
    }

    public function DeleteTransaction($data){
        $orno = $data["orno"];
        $cdate = $data["cdate"];
        if ($this->IsDateClosed($cdate)){
            echo json_encode(["STATUS"=>"ERROR","MESSAGE"=>"Date is already closed."]);
            return;
        }
        $cdateYmd = date("Y-m-d", strtotime($cdate));
        $dateExpr = "COALESCE(STR_TO_DATE(CDATE,'%Y-%m-%d'), STR_TO_DATE(CDATE,'%m/%d/%Y'), STR_TO_DATE(CDATE,'%m-%d-%Y'))";
        
        $stmt = $this->conn->prepare("DELETE FROM TBL_BOOKS WHERE ORNO = ? AND BOOKTYPE = 'CRB' AND ".$dateExpr." = ?");
        $stmt->bind_param("ss", $orno, $cdateYmd);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        
        $status = "";
        if($result > 0){
            $status = "SUCCESS";
        } else {
            $status = "ERROR";
        }

        echo json_encode(array(
            "STATUS" => $status,
            // "VALUES" => $values,
            // "QRY" => $qry,
        ));
    }
    
    public function SelectQuery($string){
        $data = [];
        $stmt = $this->conn->prepare($string);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}
