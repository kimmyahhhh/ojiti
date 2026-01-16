    <?php
include_once("../../database/connection.php");

class Process extends Database
{
    public function LoadSupplierList(){
        $list = $this->SelectQuery("SELECT * FROM tbl_supplier_info ORDER BY id ASC");

        echo json_encode(array( 
            "LIST" => $list,
        ));
    }
    public function GenerateSupplierNo(){
        $next = "1";
        $stmt = $this->conn->prepare("SELECT MAX(CAST(supplierNo AS UNSIGNED)) AS lastNo FROM tbl_supplier_info");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result && $row = $result->fetch_assoc()) {
            if (!empty($row['lastNo'])) {
                $next = strval(intval($row['lastNo']) + 1);
            } else {
                $stmt2 = $this->conn->prepare("SELECT MAX(id) AS lastId FROM tbl_supplier_info");
                $stmt2->execute();
                $res2 = $stmt2->get_result();
                $stmt2->close();
                if ($res2 && $r2 = $res2->fetch_assoc() && !empty($r2['lastId'])) {
                    $next = strval(intval($r2['lastId']) + 1);
                }
            }
        }
        echo json_encode(array("NEXT" => $next));
    }

    public function GetSupplierInfo($data){
        $id = $data['supplierNo'];
        $stmt = $this->conn->prepare("SELECT * from tbl_supplier_info WHERE supplierNo = ?");
        $stmt->bind_param('s', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
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

    public function SaveInfo($data){
        try {
            $this->conn->autocommit(false);
            
            // Check for missing required fields
            $requiredFields = ['supplierNo', 'supplierName'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            $supplierNo = $_POST['supplierNo'];
            $supplierName = $_POST['supplierName'];
            $tin = $_POST['tin'] ?? '';
            $email = $_POST['email'] ?? '';
            $mobileNumber = $_POST['mobileNumber'] ?? '';
            $telNumber = $_POST['telNumber'];
            $facebookAccount = $_POST['facebookAccount'] ?? '';
            $Region = $_POST['Region'] ?? '';
            $Province = $_POST['Province'] ?? '';
            $CityTown = $_POST['CityTown'] ?? '';
            $Barangay = $_POST['Barangay'] ?? '';
            $street = $_POST['street'] ?? '';
            $address = $street . ' ' . $Barangay . ' , ' . $CityTown . ', ' . $Province . ', ' . $Region;
            date_default_timezone_set('Asia/Manila');
            $asof = date("m/d/Y", strtotime("now"));

            $stmt = $this->conn->prepare("SELECT * from tbl_supplier_info WHERE supplierNo = ?");
            $stmt->bind_param('s', $supplierNo);
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
            if ($result->num_rows > 0) {
                $status = "warning";
                $message = "Supplier No. already exist.";
            } else {
                $stmt1 = $this->conn->prepare("INSERT INTO tbl_supplier_info (supplierNo, supplierName, tinNumber, email, mobileNumber, telephoneNumber, facebookAccount, Region, Province, CityTown, Barangay, street, fullAddress, dateEncoded) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt1->bind_param('ssssssssssssss', $supplierNo, $supplierName, $tin, $email, $mobileNumber, $telNumber, $facebookAccount, $Region, $Province, $CityTown, $Barangay, $street, $address, $asof);
                if ($stmt1->execute()){
                    $this->conn->commit();
                    $status = "success";
                    $message = "Supplier Information successfully added.";
                } else {
                    $status = "success";
                    $message = "Supplier Information failed to add.";
                }
                
                $stmt1->close();
            }

            echo json_encode(array(
                "STATUS" => $status,
                "MESSAGE" => $message
            ));
    
            $this->conn->autocommit(true);
        } catch (Exception $e) {
            $this->conn->rollback();
            echo json_encode(array(
                "STATUS" => "ERROR",
                "MESSAGE" => $e->getMessage()
            ));
        }
    }

    public function UpdateInfo($data){
        try {
            $this->conn->autocommit(false);

            // Check for missing required fields
            $requiredFields = ['supplierNo', 'supplierName'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            $supplierID = $_POST['supplierID'] ?? '';
            $supplierName = $_POST['supplierName'];
            $tin = $_POST['tin'] ?? '';
            $email = $_POST['email'] ?? '';
            $mobileNumber = $_POST['mobileNumber'] ?? '';
            $telNumber = $_POST['telNumber'];
            $facebookAccount = $_POST['facebookAccount'] ?? '';
            $Region = $_POST['Region'] ?? '';
            $Province = $_POST['Province'] ?? '';
            $CityTown = $_POST['CityTown'] ?? '';
            $Barangay = $_POST['Barangay'] ?? '';
            $street = $_POST['street'] ?? '';
            $address = $street . ' ' . $Barangay . ' , ' . $CityTown . ', ' . $Province . ', ' . $Region;
            date_default_timezone_set('Asia/Manila');
            $asof = date("m-d-Y H:i:s", strtotime("now"));
    
           
            $stmt1 = $this->conn->prepare("UPDATE tbl_supplier_info SET supplierName = ?, tinNumber = ?, email = ?, mobileNumber = ?, telephoneNumber = ?, facebookAccount = ?, Region = ?, Province = ?, CityTown = ?, Barangay = ?, street = ?, fullAddress = ?, dateEncoded = ? WHERE id = ?");
            $stmt1->bind_param('ssssssssssssss', $supplierName, $tin, $email, $mobileNumber, $telNumber, $facebookAccount, $Region, $Province, $CityTown, $Barangay, $street, $address, $asof, $supplierID);
            if ($stmt1->execute()){
                $this->conn->commit();
                $status = "success";
                $message = "Supplier Information updated.";
            } else {
                $status = "error";
                $message = "Supplier Information failed to update.";
            }
            
            echo json_encode(array(
                "STATUS" => $status,
                "MESSAGE" => $message,
                "CTY" => $CityTown,
                "BRGY" => $Barangay,
                "ID" => $supplierID,
            ));
            
            $stmt1->close();
    
             $this->conn->autocommit(true);
        } catch (Exception $e) {
             $this->conn->rollback();
            echo json_encode(array(
                "STATUS" => "ERROR",
                "MESSAGE" => $e->getMessage()
            ));            
        }
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
