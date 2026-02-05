<?php
include_once("../../database/connection.php");

class Process extends Database
{
    private function ResolveMaintenanceItemId($moduleName, $submoduleName, $itemName){
        $moduleId = 0;
        $subId = 0;
        $itemId = 0;

        // Module
        $stmt = $this->conn->prepare("SELECT id_module FROM tbl_maintenance_module WHERE module_type = 0 AND module = ? LIMIT 1");
        $stmt->bind_param("s", $moduleName);
        $stmt->execute();
        $res = $stmt->get_result();
        if($row = $res->fetch_assoc()){ $moduleId = intval($row["id_module"]); }
        $stmt->close();
        if($moduleId <= 0){ return 0; }

        // Submodule
        $stmt = $this->conn->prepare("SELECT id_module FROM tbl_maintenance_module WHERE module_type = 1 AND module = ? AND module_no = ? LIMIT 1");
        $stmt->bind_param("si", $submoduleName, $moduleId);
        $stmt->execute();
        $res = $stmt->get_result();
        if($row = $res->fetch_assoc()){ $subId = intval($row["id_module"]); }
        $stmt->close();
        if($subId <= 0){ return 0; }

        // Item
        $stmt = $this->conn->prepare("SELECT id_module FROM tbl_maintenance_module WHERE module_type = 2 AND module = ? AND module_no = ? LIMIT 1");
        $stmt->bind_param("si", $itemName, $subId);
        $stmt->execute();
        $res = $stmt->get_result();
        if($row = $res->fetch_assoc()){ $itemId = intval($row["id_module"]); }
        $stmt->close();

        return $itemId;
    }

    private function GetMaintenanceChoices($moduleName, $submoduleName, $itemName){
        $choices = [];
        $itemId = $this->ResolveMaintenanceItemId($moduleName, $submoduleName, $itemName);
        if($itemId <= 0){ return $choices; }

        $stmt = $this->conn->prepare("SELECT module as choice_value FROM tbl_maintenance_module WHERE module_type = 3 AND status = 1 AND module_no = ? ORDER BY module ASC");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $res = $stmt->get_result();
        while($row = $res->fetch_assoc()){
            $choices[] = $row;
        }
        $stmt->close();
        return $choices;
    }

    public function LoadFund($data){
        // $CDate = date("Y-m-d",strtotime($data["SelectedDate"]));
        // $user = $_SESSION['USERNAME'];
        // $Fund = $this->SelectQuery("SELECT DISTINCT FUND FROM TBL_BOOKS WHERE BOOKTYPE = 'CRB' AND PREPAREDBY = '".$user."' AND  STR_TO_DATE(CDATE,'%Y-%m-%d') = STR_TO_DATE('".$CDate."','%Y-%m-%d') ORDER BY FUND");
        
        $Fund = $this->GetMaintenanceChoices("Cashier","Other Payment","Fund");
        $NewFund = [];
        foreach($Fund as $f){
            $NewFund[] = array("FUND" => $f["choice_value"]);
        }

        echo json_encode(array(
            "FUND" => $NewFund,
            // "QRY" => $qry,
        ));
    }

    public function LoadUndepPrev($data){
        $user = $_SESSION['USERNAME'];
        $CDate = date("Y-m-d",strtotime($data["SelectedDate"]));
        // $qry = "SELECT * FROM TBL_BOOKS USE INDEX(BOOKTYPE) WHERE BOOKTYPE = 'CRB' AND STATUS = 'UNDEPOSITED' AND PREVSTATUS = 'UNDEPOSITED' AND GLNO = '11123' AND DROTHER > 0 AND STR_TO_DATE(CDATE,'%Y-%m-%d') < STR_TO_DATE('".$CDate."','%Y-%m-%d')";

        $UndepPrevList = [];
        $stmt = $this->conn->prepare("SELECT SUM(DROTHER) AS TOTALAMOUNT, CDATE, FUND, PAYMENTTYPE, STATUS FROM TBL_BOOKS USE INDEX(BOOKTYPE) WHERE BOOKTYPE = 'CRB' AND (STATUS = 'UNDEPOSITED' OR STATUS = '' OR STATUS IS NULL) AND GLNO = '11120' AND DROTHER > 0 AND STR_TO_DATE(CDATE,'%Y-%m-%d') < STR_TO_DATE(?,'%Y-%m-%d') GROUP BY CDATE, FUND, PAYMENTTYPE, STATUS");
        $stmt->bind_param("s", $CDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $dsInfo = $this->GetDSNO($row["PAYMENTTYPE"],$row["FUND"],$CDate);
            $dsno = $dsInfo['DSNOENTRY'];
            $lstDSNo = $dsInfo['LASTDSNO'];
            $bank = $dsInfo['BANK'];
            
            $UndepPrevList[] = array(
                "CDATE" => $row["CDATE"],
                "PARTICULARS" => $dsno,
                "PAYMENTTYPE" => $row["PAYMENTTYPE"],
                "TOTALAMOUNT" => $row["TOTALAMOUNT"],
                "STATUS" => $row["STATUS"],
                "FUND" => $row["FUND"],
                "BANK" => $bank,
                "LASTDSNO" => $lstDSNo,
            );
        }

        echo json_encode(array(
            "UNDEPPREVLIST" => $UndepPrevList,
            // "QRY" => $qry,
        ));
    }

    public function GetBanks($data) {
        $user = $_SESSION['USERNAME'];
        $Fund = $data["Fund"];
        $CDate = date("Y-m-d",strtotime($data["SelectedDate"]));

        // $Banks = $this->SelectQuery("SELECT DISTINCT BANK FROM TBL_BOOKS WHERE BOOKTYPE = 'CRB' AND PREPAREDBY = '".$user."' AND STR_TO_DATE(CDATE,'%Y-%m-%d') = STR_TO_DATE('".$CDate."','%Y-%m-%d') AND FUND = '".$Fund."' ORDER BY FUND");

        $M_Banks = $this->GetMaintenanceChoices("Cashier","Other Payment","Bank");
        $Banks = [];
        foreach($M_Banks as $b){
            $Banks[] = array("BANK" => $b["choice_value"]);
        }

        echo json_encode(array(
            "BANKS" => $Banks,
        ));
    }

    public function LoadDepositDetails($data) {
        $Type = $data["Type"];
        $Bank = $data["Bank"];
        $Fund = $data["Fund"];
        $CDate = date("Y-m-d",strtotime($data["SelectedDate"]));
        $user = $_SESSION['USERNAME'];

        $UndepTodayList = [];
        $stmt = $this->conn->prepare("SELECT DISTINCT ORNO, PAYEE, DROTHER FROM TBL_BOOKS WHERE BOOKTYPE = 'CRB' AND FUND = ? AND UPPER(PAYMENTTYPE) = UPPER(?) AND GLNO = '11120' AND DROTHER > 0 AND (STATUS = 'UNDEPOSITED' OR STATUS = '' OR STATUS IS NULL) AND STR_TO_DATE(CDATE,'%Y-%m-%d') = STR_TO_DATE(?,'%Y-%m-%d')");
        $stmt->bind_param("sss",$Fund, $Type, $CDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        while ($row = $result->fetch_assoc()) {
            $UndepTodayList[] = $row;
        }

        $stmt = $this->conn->prepare("SELECT SUM(DROTHER) AS TOTAL FROM TBL_BOOKS WHERE BOOKTYPE = 'CRB' AND FUND = ? AND UPPER(PAYMENTTYPE) = UPPER(?) AND GLNO = '11120' AND DROTHER > 0 AND (STATUS = 'UNDEPOSITED' OR STATUS = '' OR STATUS IS NULL) AND STR_TO_DATE(CDATE,'%Y-%m-%d') = STR_TO_DATE(?,'%Y-%m-%d')");
        $stmt->bind_param("sss",$Fund, $Type, $CDate);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $row = $result->fetch_assoc();
        $UndepTodayTotal = $row;

        echo json_encode(array(
            "UNDEPTODAY" => $UndepTodayList,
            "UNDEPTODAYTOTAL" => $UndepTodayTotal,
        ));
    }

    public function GetDSNO($type,$fund,$date){
        $month = date("m",strtotime($date));
        $day = date("d",strtotime($date));
        
        $stmt = $this->conn->prepare("SELECT * FROM tbl_configuration WHERE ConfigName = 'DSPREFIX'");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $row = $result->fetch_assoc();
        $DSPrefix = $row["Value"];
        
        $stmt = $this->conn->prepare("SELECT * FROM TBL_DSNUMBERS WHERE FUND = ?");
        $stmt->bind_param("s",$fund);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $bank = $row['DefaultBank'];
            $lastDSNo = $row['DSNo'];
            $dsno = $row['DSNo'] + 1;

            // $dsnoEntry = $DSPrefix . date("m") . "-" . date("d") . "-" . $dsno . " " . $bank . " " . $type;

            $dsnoEntry = $DSPrefix . $month . "-" . $day . "-" . $dsno . " " . $bank . " " . $type;
        } else {
            $lastDSNo = "";
            $dsnoEntry = "";
        }

        return array(
            "DSNOENTRY" => $dsnoEntry,
            "LASTDSNO" => $lastDSNo,
            "BANK" => $bank,
        );
    }
    
    public function SetDSNO($data){
        $type = $data["Type"];
        $fund = $data["Fund"];
        $bank = $data["Bank"];
        $month = date("m",strtotime($data["SelectedDate"]));
        $day = date("d",strtotime($data["SelectedDate"]));
        
        $stmt = $this->conn->prepare("SELECT * FROM tbl_configuration WHERE ConfigName = 'DSPREFIX'");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $row = $result->fetch_assoc();
        $DSPrefix = $row["Value"];
        
        $stmt = $this->conn->prepare("SELECT * FROM TBL_DSNUMBERS WHERE FUND = ?");
        $stmt->bind_param("s",$fund);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $lastDSNo = $row['DSNo'];
            $dsno = $row['DSNo'] + 1;

            // $dsnoEntry = $DSPrefix . date("m") . "-" . date("d") . "-" . $dsno . " " . $bank . " " . $type;

            $dsnoEntry = $DSPrefix . $month . "-" . $day . "-" . $dsno . " " . $bank . " " . $type;
            $status = "YES";
        } else {
            $lastDSNo = "";
            $dsnoEntry = "";
            $status = "NO";
        }

        echo json_encode(array(
            "DSNOENTRY" => $dsnoEntry,
            "LASTDSNO" => $lastDSNo,
            "STATUS" => $status,
        ));
    }

    public function SaveDepositSlip($data){

        $lastdsno = $data["lastdsno"];
        $type = $data["type"];
        $fund = $data["fund"];
        $bank = $data["bank"];
        $depositslipno = $data["depositslipno"];
        $amount = str_replace(",", "",$data["amount"]);
        $cdate = date("Y-m-d",strtotime($data["selectedDate"]));
        $user = $_SESSION['USERNAME'];

        $sAcctNo_CIB = "11130";
        $sAcctNo_COH = "11120";

        // Initialize arrays
        $sGL = [];
        $sDebitCredit = [];
        $vGLAmount = [];

        // Assign values for the transaction
        $sGL[0] = $sAcctNo_CIB;
        $sDebitCredit[0] = "DEBIT";
        $vGLAmount[0] = floatval($amount);

        $sGL[1] = $sAcctNo_COH;
        $sDebitCredit[1] = "CREDIT";
        $vGLAmount[1] = floatval($amount);

        $bookpage = $this->GetBookPage("CRB",$fund,$cdate);

        $values = "";

        $branch = "HEADOFFICE";
        $slName = "";
        $hasSL = "";
        $AcctTitle = "";

        for ($i = 0; $i < count($sGL); $i++) {

            $gldtls = $this->GetGLDetails($sGL[$i]);
            $hasSL = $gldtls["HasSL"];
            $slName = $gldtls["SLName"];
            $AcctTitle = $gldtls["AcctTitle"];
                
            $jvno = "-";
            $explanation = "DEPOSIT OF COLLECTIONS UNDER " . $fund . " TO " . $bank . " DTD " . $cdate . " (" . $type . ")";
            $acctitle = $AcctTitle;
            $acctno = $sGL[$i];
            $sldrcr = 0;
            $sldrcr1 = 0;
            $slyesno = $hasSL;
            $slno = "-";
            $slname = $slName;
            $drother = ($sDebitCredit[$i] == "DEBIT") ? $vGLAmount[$i] : 0;
            $crother = ($sDebitCredit[$i] == "CREDIT") ? $vGLAmount[$i] : 0;
            $preparedby = $user;
            $drcr = $sDebitCredit[$i];
            $glno = $sGL[$i];
            $nature = "DEPOSIT OF COLLECTIONS (DS)";
            $posting = "NO";
            $booktype = "CRB";
            $payee = $depositslipno;
            $cvno = "-";
            $stat = "DEPOSITED";

            $values .= ($i > 0) ? "," : "";
            $values .= "('".$cdate."','".$branch."','".$fund."','".$jvno."','".$explanation."'
            ,'".$acctitle."','".$acctno."','".$sldrcr."','".$sldrcr1."','".$slyesno."','".$slno."'
            ,'".$slname."','".$drother."','".$crother."','".$preparedby."'
            ,'".$drcr."','".$glno."','".$nature."','".$posting."','".$booktype."','".$payee."','".$cvno."','".$bank."','".$bookpage."','".$stat."','".$type."')";
            
            if ($hasSL == "YES"){
                $acctitle = ($sGL[$i] == $sAcctNo_CIB) ? "     " . $bank: "      SL HERE";
                $acctno = ($sGL[$i] == $sAcctNo_CIB) ? $this->GetBankID($bank): "SLNo HERE";;
                $sldrcr = ($sDebitCredit[$i] == "DEBIT") ? $vGLAmount[$i] : $vGLAmount[$i] * -1;
                $sldrcr1 = ($sDebitCredit[$i] == "DEBIT") ? $vGLAmount[$i] * -1 : $vGLAmount[$i];
                $slyesno = $hasSL;
                $slno = "0";
                $slname = $slName;
                $drother = 0;
                $crother = 0;

                $values .= ",('".$cdate."','".$branch."','".$fund."','".$jvno."','".$explanation."'
                ,'".$acctitle."','".$acctno."','".$sldrcr."','".$sldrcr1."','".$slyesno."','".$slno."'
                ,'".$slname."','".$drother."','".$crother."','".$preparedby."'
                ,'".$drcr."','".$glno."','".$nature."','".$posting."','".$booktype."','".$payee."','".$cvno."','".$bank."','".$bookpage."','".$stat."','".$type."')";
            }
        }

        // $qry = "INSERT INTO tbl_books (CDate, Branch, Fund, JVNo, Explanation, AcctTitle, AcctNo, SLDrCr, SLDrCr1, SLYesNo, SLNo, SLName, DrOther, CrOther, PreparedBy, CrDr, GLNo, Nature, postingstat, BookType, Payee, CVNo, Bank, BookPage, Status, PaymentType) VALUES ".$values;

        $stmt = $this->conn->prepare("INSERT INTO tbl_books (CDate, Branch, Fund, JVNo, Explanation, AcctTitle, AcctNo, SLDrCr, SLDrCr1, SLYesNo, SLNo, SLName, DrOther, CrOther, PreparedBy, CrDr, GLNo, Nature, postingstat, BookType, Payee, CVNo, Bank, BookPage, Status, PaymentType) VALUES ".$values);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        
        $status = "";
        if($result > 0){
            $this->AdjustDSNO($fund);

            $stmt = $this->conn->prepare("UPDATE TBL_BOOKS SET STATUS = 'DEPOSITED' WHERE BOOKTYPE = 'CRB' AND FUND = ? AND UPPER(PAYMENTTYPE) = UPPER(?) AND STR_TO_DATE(CDATE,'%Y-%m-%d') = STR_TO_DATE(?,'%Y-%m-%d') AND (STATUS = 'UNDEPOSITED' OR STATUS = '' OR STATUS IS NULL)");
            $stmt->bind_param("sss",$fund,$type,$cdate);
            $stmt->execute();
            $stmt->close();

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
    
    public function DSUndepPrevious($data){
        
        $prevdatepaid = $_POST['setdatepaid'];
        $lastdsno = $_POST['lastdsno'];
        $fund = $_POST['fund'];
        $bank = $_POST['bank'];
        $type = $_POST['type'];
        $depositslipno = $_POST['dsentry'];
        $amount = str_replace(",", "", $_POST['amount']);
        $cdate = date("Y-m-d",strtotime($data["SelectedDate"]));
        $user = $_SESSION['USERNAME'];

        $sAcctNo_CIB = "11130";
        $sAcctNo_COH = "11120";

        // Initialize arrays
        $sGL = [];
        $sDebitCredit = [];
        $vGLAmount = [];

        // Assign values for the transaction
        $sGL[0] = $sAcctNo_CIB;
        $sDebitCredit[0] = "DEBIT";
        $vGLAmount[0] = floatval($amount);

        $sGL[1] = $sAcctNo_COH;
        $sDebitCredit[1] = "CREDIT";
        $vGLAmount[1] = floatval($amount);

        $bookpage = $this->GetBookPage("CRB",$fund,$cdate);

        $values = "";

        $branch = "HEADOFFICE";
        $slName = "";
        $hasSL = "";
        $AcctTitle = "";

        for ($i = 0; $i < count($sGL); $i++) {

            $gldtls = $this->GetGLDetails($sGL[$i]);
            $hasSL = $gldtls["HasSL"];
            $slName = $gldtls["SLName"];
            $AcctTitle = $gldtls["AcctTitle"];
                
            $jvno = "-";
            $explanation = "DEPOSIT OF COLLECTIONS UNDER " . $fund . " TO " . $bank . " DTD " . $cdate . " (" . $type . ")";
            $acctitle = $AcctTitle;
            $acctno = $sGL[$i];
            $sldrcr = 0;
            $sldrcr1 = 0;
            $slyesno = $hasSL;
            $slno = "-";
            $slname = $slName;
            $drother = ($sDebitCredit[$i] == "DEBIT") ? $vGLAmount[$i] : 0;
            $crother = ($sDebitCredit[$i] == "CREDIT") ? $vGLAmount[$i] : 0;
            $preparedby = $user;
            $drcr = $sDebitCredit[$i];
            $glno = $sGL[$i];
            $nature = "DEPOSIT OF COLLECTIONS (DS)";
            $posting = "NO";
            $booktype = "CRB";
            $payee = $depositslipno;
            $cvno = "-";
            $stat = "DEPOSITED";

            $values .= ($i > 0) ? "," : "";
            $values .= "('".$cdate."','".$branch."','".$fund."','".$jvno."','".$explanation."'
            ,'".$acctitle."','".$acctno."','".$sldrcr."','".$sldrcr1."','".$slyesno."','".$slno."'
            ,'".$slname."','".$drother."','".$crother."','".$preparedby."'
            ,'".$drcr."','".$glno."','".$nature."','".$posting."','".$booktype."','".$payee."','".$cvno."','".$bank."','".$bookpage."','".$stat."','".$type."')";
            if ($hasSL == "YES"){
                $acctitle = ($sGL[$i] == $sAcctNo_CIB) ? "     " . $bank: "      SL HERE";
                $acctno = ($sGL[$i] == $sAcctNo_CIB) ? $this->GetBankID($bank): "SLNo HERE";;
                $sldrcr = ($sDebitCredit[$i] == "DEBIT") ? $vGLAmount[$i] : $vGLAmount[$i] * -1;
                $sldrcr1 = ($sDebitCredit[$i] == "DEBIT") ? $vGLAmount[$i] * -1 : $vGLAmount[$i];
                $slyesno = $hasSL;
                $slno = "0";
                $slname = $slName;
                $drother = 0;
                $crother = 0;

                $values .= ",('".$cdate."','".$branch."','".$fund."','".$jvno."','".$explanation."'
                ,'".$acctitle."','".$acctno."','".$sldrcr."','".$sldrcr1."','".$slyesno."','".$slno."'
                ,'".$slname."','".$drother."','".$crother."','".$preparedby."'
                ,'".$drcr."','".$glno."','".$nature."','".$posting."','".$booktype."','".$payee."','".$cvno."','".$bank."','".$bookpage."','".$stat."','".$type."')";
            }
        }

        // $qry = "INSERT INTO tbl_books (CDate, Branch, Fund, JVNo, Explanation, AcctTitle, AcctNo, SLDrCr, SLDrCr1, SLYesNo, SLNo, SLName, DrOther, CrOther, PreparedBy, CrDr, GLNo, Nature, postingstat, BookType, Payee, CVNo, Bank, BookPage, Status, PaymentType) VALUES ".$values;

        $stmt = $this->conn->prepare("INSERT INTO tbl_books (CDate, Branch, Fund, JVNo, Explanation, AcctTitle, AcctNo, SLDrCr, SLDrCr1, SLYesNo, SLNo, SLName, DrOther, CrOther, PreparedBy, CrDr, GLNo, Nature, postingstat, BookType, Payee, CVNo, Bank, BookPage, Status, PaymentType) VALUES ".$values);
        $stmt->execute();
        $result = $stmt->affected_rows;
        $stmt->close();
        
        $status = "";
        if($result > 0){
            $this->AdjustDSNO($fund);

            $stmt = $this->conn->prepare("UPDATE TBL_BOOKS SET STATUS = 'DEPOSITED' WHERE BOOKTYPE = 'CRB' AND FUND = ? AND PAYMENTTYPE = ? AND STR_TO_DATE(CDATE,'%Y-%m-%d') = STR_TO_DATE(?,'%Y-%m-%d') AND (STATUS = 'UNDEPOSITED' OR STATUS = '' OR STATUS IS NULL)");
            $stmt->bind_param("sss",$fund,$type,$prevdatepaid);
            $stmt->execute();
            $stmt->close();

            $status = "SUCCESS";
        } else {
            $status = "ERROR";
        }

        echo json_encode(array(
            "DATA" => $data,
            "STATUS" => $status,
            // "VALUES" => $values,
            // "QRY" => $qry,
        ));
    }
    
    public function DSUndepPreviousALL($data){

        $tbldata = $_POST['tblData'];
        $tbldataArray = json_decode($tbldata, true);
        $rowCount = count($tbldataArray);
        $status = "SUCCESS";

        $sAcctNo_CIB = "11130";
        $sAcctNo_COH = "11120";

        $cdate = date("Y-m-d",strtotime($data["SelectedDate"]));
        $user = $_SESSION['USERNAME'];

        foreach ($tbldataArray as $index => $row) {
            $prevdatepaid = $row[0];
            $type = $row[2];
            $amount = str_replace(",", "", $row[3]);
            $fund = $row[5];
            $bank = $row[6];

            // Generate DS Number
            $dsInfo = $this->GetDSNO($type, $fund, $cdate);
            $depositslipno = $dsInfo['DSNOENTRY'];
            
            // Initialize arrays
            $sGL = [];
            $sDebitCredit = [];
            $vGLAmount = [];

            // Assign values for the transaction
            $sGL[0] = $sAcctNo_CIB;
            $sDebitCredit[0] = "DEBIT";
            $vGLAmount[0] = floatval($amount);

            $sGL[1] = $sAcctNo_COH;
            $sDebitCredit[1] = "CREDIT";
            $vGLAmount[1] = floatval($amount);

            $bookpage = $this->GetBookPage("CRB",$fund,$cdate);

            $values = "";

            $branch = "HEADOFFICE";
            $slName = "";
            $hasSL = "";
            $AcctTitle = "";

            for ($i = 0; $i < count($sGL); $i++) {

                $gldtls = $this->GetGLDetails($sGL[$i]);
                $hasSL = $gldtls["HasSL"];
                $slName = $gldtls["SLName"];
                $AcctTitle = $gldtls["AcctTitle"];
                    
                $jvno = "-";
                $explanation = "DEPOSIT OF COLLECTIONS UNDER " . $fund . " TO " . $bank . " DTD " . $cdate . " (" . $type . ")";
                $acctitle = $AcctTitle;
                $acctno = $sGL[$i];
                $sldrcr = 0;
                $sldrcr1 = 0;
                $slyesno = $hasSL;
                $slno = "-";
                $slname = $slName;
                $drother = ($sDebitCredit[$i] == "DEBIT") ? $vGLAmount[$i] : 0;
                $crother = ($sDebitCredit[$i] == "CREDIT") ? $vGLAmount[$i] : 0;
                $preparedby = $user;
                $drcr = $sDebitCredit[$i];
                $glno = $sGL[$i];
                $nature = "DEPOSIT OF COLLECTIONS (DS)";
                $posting = "NO";
                $booktype = "CRB";
                $payee = $depositslipno;
                $cvno = "-";
                $stat = "DEPOSITED";

                $values .= ($i > 0) ? "," : "";
                $values .= "('".$cdate."','".$branch."','".$fund."','".$jvno."','".$explanation."'
                ,'".$acctitle."','".$acctno."','".$sldrcr."','".$sldrcr1."','".$slyesno."','".$slno."'
                ,'".$slname."','".$drother."','".$crother."','".$preparedby."'
                ,'".$drcr."','".$glno."','".$nature."','".$posting."','".$booktype."','".$payee."','".$cvno."','".$bank."','".$bookpage."','".$stat."','".$type."')";
                
                if ($hasSL == "YES"){
                    $acctitle = ($sGL[$i] == $sAcctNo_CIB) ? "     " . $bank: "      SL HERE";
                    $acctno = ($sGL[$i] == $sAcctNo_CIB) ? $this->GetBankID($bank): "SLNo HERE";;
                    $sldrcr = ($sDebitCredit[$i] == "DEBIT") ? $vGLAmount[$i] : $vGLAmount[$i] * -1;
                    $sldrcr1 = ($sDebitCredit[$i] == "DEBIT") ? $vGLAmount[$i] * -1 : $vGLAmount[$i];
                    $slyesno = $hasSL;
                    $slno = "0";
                    $slname = $slName;
                    $drother = 0;
                    $crother = 0;

                    $values .= ",('".$cdate."','".$branch."','".$fund."','".$jvno."','".$explanation."'
                    ,'".$acctitle."','".$acctno."','".$sldrcr."','".$sldrcr1."','".$slyesno."','".$slno."'
                    ,'".$slname."','".$drother."','".$crother."','".$preparedby."'
                    ,'".$drcr."','".$glno."','".$nature."','".$posting."','".$booktype."','".$payee."','".$cvno."','".$bank."','".$bookpage."','".$stat."','".$type."')";
                }
            }

            $stmt = $this->conn->prepare("INSERT INTO tbl_books (CDate, Branch, Fund, JVNo, Explanation, AcctTitle, AcctNo, SLDrCr, SLDrCr1, SLYesNo, SLNo, SLName, DrOther, CrOther, PreparedBy, CrDr, GLNo, Nature, postingstat, BookType, Payee, CVNo, Bank, BookPage, Status, PaymentType) VALUES ".$values);
            $stmt->execute();
            $result = $stmt->affected_rows;
            $stmt->close();
            
            if($result > 0){
                $this->AdjustDSNO($fund);

                $stmt = $this->conn->prepare("UPDATE TBL_BOOKS SET STATUS = 'DEPOSITED' WHERE BOOKTYPE = 'CRB' AND FUND = ? AND PAYMENTTYPE = ? AND STR_TO_DATE(CDATE,'%Y-%m-%d') = STR_TO_DATE(?,'%Y-%m-%d') AND (STATUS = 'UNDEPOSITED' OR STATUS = '' OR STATUS IS NULL)");
                $stmt->bind_param("sss",$fund,$type,$prevdatepaid);
                $stmt->execute();
                $stmt->close();
            } else {
                $status = "ERROR";
            }
        }
        
        echo json_encode(array(
            "DATA" => $data,
            "STATUS" => $status,
            "MESSAGE" => ($status == "SUCCESS") ? "All transactions processed successfully." : "Some transactions failed to process."
        ));
    }

    private function AdjustDSNO($fund) {
        $stmt = $this->conn->prepare("UPDATE TBL_DSNUMBERS SET DSNO = DSNO + 1 WHERE FUND = ?");
        $stmt->bind_param("s",$fund);
        $stmt->execute();
        $stmt->close();
    }

    private function GetBookPage($type,$fund,$date){
        $lastbookpage = "";
        $newbookpage = "";

        $stmt = $this->conn->prepare("SELECT DISTINCT BOOKTYPE,FUND,CDATE,BOOKPAGE FROM TBL_BOOKS USE INDEX(forBookPage) WHERE BOOKTYPE = ? AND FUND = ? AND BOOKPAGE <> '-' ORDER BY CAST(REPLACE(BOOKPAGE,'".$type."-','') AS UNSIGNED) DESC LIMIT 1");
        $stmt->bind_param("ss",$type,$fund);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();

        if($result->num_rows > 0){
            while ($row = $result->fetch_assoc()) {
                $lastbookpage = trim(str_replace($type."-","",$row["BOOKPAGE"]));
                $ddate = date("Y-m-d",strtotime($row["CDATE"]));
                $newbookpage = ($ddate == $date) ? $type."-".$lastbookpage : $type."-".(floatval($lastbookpage) + 1);
            }
        }else{
            $newbookpage = $type."-1";
        }

        return $newbookpage;
    }

    private function GetGLDetails($acctcode){
        $stmt = $this->conn->prepare("SELECT * FROM tbl_accountcodes WHERE acctcodes = ?");
        $stmt->bind_param("s",$acctcode);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $row = $result->fetch_assoc();

        return array(
            "HasSL" => $row["sl"],
            "SLName" => $row["slname"],
            "AcctTitle" => $row["acctitles"],
        );
    }

    private function GetBankID($bank) {
        $stmt = $this->conn->prepare("SELECT * FROM tbl_banksetup WHERE Bank = ?");
        $stmt->bind_param("s",$bank);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        $row = $result->fetch_assoc();
        return $row["ID"];
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