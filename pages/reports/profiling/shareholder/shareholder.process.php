shprocess
<?php
include_once("../../database/connection.php");

class Process extends Database
{
    public function LoadShareHolderNames(){
        $names = $this->SelectQuery("SELECT DISTINCT fullname FROM tbl_shareholder_info ORDER BY fullname ASC;");

        echo json_encode(array( 
            "NAMES" => $names,
        ));
    }

    public function LoadShareHolderList($data){
        $list = [];
        $name = "%".$data['name']."%";
        
        $stmt = $this->conn->prepare("SELECT * FROM tbl_shareholder_info WHERE fullname LIKE ? ORDER BY dateEncoded ASC;");
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()){
            $list[] = $row;
        }
        
        echo json_encode(array( 
            "LIST" => $list,
        ));
    }

    public function getShareholderInfo($data){
        if(!isset($data['shareholderNo']) || empty($data['shareholderNo'])){
             echo json_encode(array(
                "STATUS" => "ERROR",
                "MESSAGE" => "Invalid Shareholder No"
            ));
            return;
        }
        $shareholderNo = $data['shareholderNo'];
        $stmt = $this->conn->prepare("SELECT * from tbl_shareholder_info WHERE shareholderNo = ?");
        $stmt->bind_param('s', $shareholderNo);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo json_encode(array(
                "INFO" => $row,
                "STATUS" => "LOADED"
            ));
        } else {            
            echo json_encode(array(              
                "STATUS" => "EMPTY",
            ));
        }
    }
    
    public function getShareholderConfig(){
        $certNo = $this->SelectQuery("SELECT * FROM tbl_configuration t WHERE ConfigOwner = 'SHAREHOLDER INFO' AND ConfigName = 'CERTIFICATENO'");
        $sign1 = $this->SelectQuery("SELECT * FROM tbl_configuration t WHERE ConfigOwner = 'SHAREHOLDER INFO' AND ConfigName = 'SIGNATORIES_1'");
        $sign2 = $this->SelectQuery("SELECT * FROM tbl_configuration t WHERE ConfigOwner = 'SHAREHOLDER INFO' AND ConfigName = 'SIGNATORIES_2'");
        $signsub2 = $this->SelectQuery("SELECT * FROM tbl_configuration t WHERE ConfigOwner = 'SHAREHOLDER INFO' AND ConfigName = 'SIGNATORIES_SUB_2'");

        echo json_encode(array(
            "certNo" => $certNo,
            "SIGN1" => $sign1,
            "SIGN2" => $sign2,
            "SIGNSUB2" => $signsub2,
        ));
    }

    public function searchNames($data){
        
        $names = [];
        $stmt = $this->conn->prepare("SELECT fullname FROM tbl_shareholder_info WHERE fullname LIKE ?");
        $searchName = '%' . $data["name"] . '%';
        $stmt->bind_param("s", $searchName);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $names[] = $row['fullname'];
            }
        }
        $stmt->close();
        
        echo json_encode($names);
    }

    public function gnrtCertID(){

        $stmt = $this->conn->prepare("SELECT Value FROM tbl_configuration t WHERE ConfigOwner = 'SHAREHOLDER INFO' AND ConfigName = 'CERTIFICATENO';");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $no = $row['Value'];
            $certNo = str_pad($no, 4, 0, STR_PAD_LEFT);
        } else {
            $no = 1;
            $certNo = str_pad($no, 4, 0, STR_PAD_LEFT);
        }
        $stmt->close(); // Close the statement
        
        echo json_encode(array(
            "certNo" => $certNo,
            "actualNo" => $no,
        ));
    }

    public function updateCertNo($no){
        $updatedCertNo = intval($no) + 1;
        $stmt1 = $this->conn->prepare("UPDATE tbl_configuration SET Value= ? WHERE ConfigOwner = 'SHAREHOLDER INFO' AND ConfigName = 'CERTIFICATENO'");
        if ($stmt1) {
             $stmt1->bind_param('s', $updatedCertNo);
             $stmt1->execute();
             $stmt1->close();
        }
    }



    public function gnrtSID(){
        $stmt = $this->conn->prepare("SELECT shareholderNo FROM tbl_shareholder_info ORDER BY CAST(SUBSTRING(shareholderNo, 5) AS UNSIGNED) DESC LIMIT 1;");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $last_no = substr($row['shareholderNo'], 4);
            $next_number = intval($last_no) + 1;
            $shareNo = 'ISYN' . str_pad($next_number, 7, 0, STR_PAD_LEFT);
        } else {
            $next_number = 0000001; 
            $shareNo = 'ISYN' . str_pad($next_number, 7, 0, STR_PAD_LEFT);
        }
        $stmt->close(); // Close the statement
        
        echo json_encode(array(
            "shareNo" => $shareNo,
        ));
    }

    public function SaveInfo($data){
        file_put_contents('debug_log.txt', "SaveInfo Started\n", FILE_APPEND);
        // try {
        //     $this->conn->autocommit(false);

            $requiredFields = ['shareholderName', 'noofshare'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    // throw new Exception("Missing required field: $field");
                    echo json_encode(array(
                        "STATUS" => "ERROR",
                        "MESSAGE" => "Missing required field: $field"
                    ));
                    return;
                }
            }

            $actualNo = $_POST['actualNo'];
            $shareholderID = $_POST['shareholderID'];
            
            $president = isset($_POST['president']) ? 1 : 0; 
            $president_dummy_val = isset($_POST['president']) ? "Yes" : "No"; // Revert to "No" if unchecked, matching "Yes"/"No" convention for string columns

            $fullname = $_POST['shareholderName']?? '';
            $contact_number = $_POST['contact_number']?? '';
            $email = $_POST['email'] ?? '';
            $facebook_account = $_POST['facebook_account'] ?? '';
            $shareholder_type = $_POST['shareholder_type'] ?? '';
            $type = $_POST['type'] ?? '';
            $noofshare = $_POST['noofshare'] ?? '';
            $amount_share = $_POST['amount_share'] ?? '';
            $cert_no = $_POST['cert_no'] ?? '';
            $emp_resign = isset($_POST['emp_resign']) ? "Yes" : "No"; // Handle checkbox properly
            
           
            date_default_timezone_set('Asia/Manila');
            $asof = date("d/m/Y", strtotime("now"));
    
            $stmt1 = $this->conn->prepare("INSERT INTO tbl_shareholder_info (shareholderNo,fullname, contact_number, email, facebook_account, shareholder_type, type, noofshare, amount_share, cert_no, OtherSignatories, emp_resign, dateEncoded, is_president) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            if (!$stmt1) {
                file_put_contents('debug_log.txt', "Prepare failed: " . $this->conn->error . "\n", FILE_APPEND);
                echo json_encode(array(
                    "STATUS" => "ERROR",
                    "MESSAGE" => "Prepare failed: " . $this->conn->error
                ));
                return;
            }
            // Use 's' for is_president as well, to be safe against strict type issues. MySQL handles string-to-int conversion automatically.
            $stmt1->bind_param('ssssssssssssss', $shareholderID, $fullname, $contact_number, $email, $facebook_account, $shareholder_type, $type, $noofshare, $amount_share, $cert_no, $president_dummy_val, $emp_resign, $asof, $president);
            
            file_put_contents('debug_log.txt', "About to Execute\n", FILE_APPEND);
            
            if (!$stmt1->execute()) {
                file_put_contents('debug_log.txt', "Execute failed: " . $stmt1->error . "\n", FILE_APPEND);
                // throw new Exception("Execute failed: " . $stmt1->error);
                echo json_encode(array(
                    "STATUS" => "ERROR",
                    "MESSAGE" => "Execute failed: " . $stmt1->error
                ));
                return;
            }
            file_put_contents('debug_log.txt', "Execute Success\n", FILE_APPEND);
            
            $stmt1->close();
            
            $this->updateCertNo($actualNo);

            // $this->conn->commit();

            $status = "success";
            $message = "Shareholder Information Successfully added ";

            echo json_encode(array(
                "STATUS" => $status,
                "MESSAGE" => $message
            ));
            
            // $stmt1->close(); // Moved up
           
            // $this->conn->commit(); // Moved up
            // $this->conn->autocommit(true);
        // } catch (Exception $e) {
        //     $this->conn->rollback();
        //     echo json_encode(array(
        //         "STATUS" => "ERROR",
        //         "MESSAGE" => $e->getMessage()
        //     ));
        // }
    }

    public function UpdateInfo($data){
        // ob_start(); // Handled by route file now
        
        // try {
        //     $this->conn->autocommit(false);

            $requiredFields = ['shareholderName', 'noofshare'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    // throw new Exception("Missing required field: $field");
                    echo json_encode(array(
                        "STATUS" => "ERROR",
                        "MESSAGE" => "Missing required field: $field"
                    ));
                    return;
                }
            }

            $shareID = $_POST['shareID'];
            
            $president = isset($_POST['president']) ? 1 : 0; 
            $president_dummy_val = isset($_POST['president']) ? "Yes" : "No";

            $fullname = $_POST['shareholderName'] ?? '';
            $contact_number = $_POST['contact_number'] ?? '';
            $email = $_POST['email'] ?? '' ;
            $facebook_account = $_POST['facebook_account'] ?? '' ;
            $shareholder_type = $_POST['shareholder_type'] ?? '' ;
            $type = $_POST['type'] ?? '' ;
            $noofshare = $_POST['noofshare'] ?? '' ;
            $amount_share = $_POST['amount_share'] ?? '' ;
            $cert_no = $_POST['cert_no'] ?? '' ;
            $emp_resign = isset($_POST['emp_resign']) ? "Yes" : "No"; // Handle checkbox properly
            date_default_timezone_set('Asia/Manila');
            $asof = date("d/m/Y", strtotime("now"));    
           
            $stmt1 = $this->conn->prepare("UPDATE tbl_shareholder_info SET fullname= ?, contact_number= ?, email= ?, facebook_account= ?, shareholder_type= ?, type= ?, noofshare= ?, amount_share= ?, cert_no= ?, OtherSignatories=?, emp_resign=?, is_president=? WHERE id = ?");
            if (!$stmt1) {
                // throw new Exception("Prepare failed: " . $this->conn->error);
                ob_end_clean();
                echo json_encode(array(
                    "STATUS" => "ERROR",
                    "MESSAGE" => "Prepare failed: " . $this->conn->error
                ));
                return;
            }
            // Bind parameters: 13 placeholders
            // Types: sssssssssssss (13 strings) - changed 'i' to 's' for compatibility
            $stmt1->bind_param('sssssssssssss', $fullname, $contact_number, $email, $facebook_account, $shareholder_type, $type, $noofshare, $amount_share, $cert_no, $president_dummy_val, $emp_resign, $president, $shareID);
            
            if (!$stmt1->execute()) {
                // throw new Exception("Execute failed: " . $stmt1->error);
                ob_end_clean();
                echo json_encode(array(
                    "STATUS" => "ERROR",
                    "MESSAGE" => "Execute failed: " . $stmt1->error
                ));
                return;
            }
            // $this->conn->commit();
            
            $status = "success";
            $message = "Shareholder information successfully updated ";

            ob_end_clean(); // Discard any warnings/output buffered so far
            
            echo json_encode(array(
                "STATUS" => $status,
                "MESSAGE" => $message
            ));
            
            $stmt1->close();
    
            //  $this->conn->autocommit(true);
        // } catch (Exception $e) {
        //      $this->conn->rollback();
        //     echo json_encode(array(
        //         "STATUS" => "ERROR",
        //         "MESSAGE" => $e->getMessage()
        //     ));            
        // }
    }

    public function UpdateConfig($data){
        // try {
        //     $this->conn->autocommit(false);

            $requiredFields = ['signatory1Name', 'signatory1Desig', 'signatory2Name', 'signatory2Desig', 'signatorySub2Name', 'signatorySub2Desig', 'currentCertNo'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    // throw new Exception("Missing required field: $field");
                    echo json_encode(array(
                        "STATUS" => "ERROR",
                        "MESSAGE" => "Missing required field: $field"
                    ));
                    return;
                }
            }

            $sign1 = $_POST['signatory1Name'];
            $sign1Desig = $_POST['signatory1Desig'];
            $sign2 = $_POST['signatory2Name'];
            $sign2Desig = $_POST['signatory2Desig'];
            $signSub2 = $_POST['signatorySub2Name'];
            $signSub2Desig = $_POST['signatorySub2Desig'];
            $currentCertNo = $_POST['currentCertNo'];
           
            $stmt1 = $this->conn->prepare("UPDATE tbl_configuration SET Value= ? WHERE ConfigOwner = 'SHAREHOLDER INFO' AND ConfigName = 'CERTIFICATENO'");
            $stmt1->bind_param('s', $currentCertNo);
            $stmt1->execute();
            $stmt1->close();

            $stmt2 = $this->conn->prepare("UPDATE tbl_configuration SET Value= ?, SubValue= ? WHERE ConfigOwner = 'SHAREHOLDER INFO' AND ConfigName = 'SIGNATORIES_1'");
            $stmt2->bind_param('ss', $sign1, $sign1Desig);
            $stmt2->execute();
            $stmt2->close();

            $stmt3 = $this->conn->prepare("UPDATE tbl_configuration SET Value= ?, SubValue= ? WHERE ConfigOwner = 'SHAREHOLDER INFO' AND ConfigName = 'SIGNATORIES_2'");
            $stmt3->bind_param('ss', $sign2, $sign2Desig);
            $stmt3->execute();
            $stmt3->close();

            $stmt3 = $this->conn->prepare("UPDATE tbl_configuration SET Value= ?, SubValue= ? WHERE ConfigOwner = 'SHAREHOLDER INFO' AND ConfigName = 'SIGNATORIES_SUB_2'");
            $stmt3->bind_param('ss', $signSub2, $signSub2Desig);
            $stmt3->execute();
            $stmt3->close();

            // $this->conn->commit();
            
            $status = "success";
            $message = "Configuration successfully updated ";

            echo json_encode(array(
                "STATUS" => $status,
                "MESSAGE" => $message
            ));
            
    
            //  $this->conn->autocommit(true);
        // } catch (Exception $e) {
        //      $this->conn->rollback();
        //     echo json_encode(array(
        //         "STATUS" => "ERROR",
        //         "MESSAGE" => $e->getMessage()
        //     ));            
        // }
    }

    public function ToSession($data){
        $_SESSION["SHNO"] = $data["shareholderNo"];
        $_SESSION["FORMAT"] = $data["format"];
        echo json_encode(array(
            "STATUS" => "SUCCESS",
        ));
    }

    private function SelectQuery($string){
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
