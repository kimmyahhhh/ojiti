<?php
include_once("../../database/connection.php");

class Process extends Database
{
    public function LoadCustomerList(){
        $customerlist = $this->SelectQuery("SELECT * FROM tbl_customer_profiles ORDER BY id ASC");

        echo json_encode(array( 
            "CUSTOMERLIST" => $customerlist,
        ));
    }

    public function GetCustomerInfo($data){
        $client_no = $data['clientNo'];
        $stmt = $this->conn->prepare("SELECT * from tbl_customer_profiles WHERE clientNo = ?");
        $stmt->bind_param('s', $client_no);
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

    public function SaveInfo($data){
        try {
            $this->conn->autocommit(false);
            
            // Check for missing required fields
            $requiredFields = ['customerType', 'customerNo', 'mobileNumber', 'email', 'Region', 'Province', 'CityTown', 'Barangay', 'tin', 'productInfo'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            $customerType = $data['customerType'];
            $customerNo = $data['customerNo'];
            $firstName = $data['firstName'] ?? '';
            $lastName = $data['lastName'] ?? '';
            $middleName = $data['middleName'] ?? '';
            $birthdate = $data['birthdate'] ?? '';
            $age = $data['age'] ?? '';
            $gender = $data['gender'] ?? '';
            $mobileNumber = $data['mobileNumber'] ;
            $companyName = $data['companyName'] ?? '';
            $email = $data['email'] ;
            $Region = $data['Region'] ; 
            $Province = $data['Province'] ;
            $CityTown = $data['CityTown'] ;
            $Barangay = $data['Barangay'] ;
            $tin = $data['tin'] ;
            $street = $data['street'] ;
            $productInfo = $data['productInfo'] ;
            $fullname = $firstName . ' ' . $middleName . ' ' . $lastName;
            $address = $street . ' ' . $Barangay . ' , ' . $CityTown . ', ' . $Province . ', ' . $Region;
    
            if($firstName == "" && $middleName == "" && $lastName == ""){
                $customer = $companyName;
            }else{
                $customer = $fullname;
            }

            date_default_timezone_set('Asia/Manila');
            $asof = date("m/d/Y H:i:s", strtotime("now"));
    
            $stmt1 = $this->conn->prepare("INSERT INTO tbl_customer_profiles (clientNo, firstName, middleName, lastName, birthdate, age, gender, mobileNumber, companyName, email, tinNumber, Region, Province, CityTown, Barangay, street, productInfo, customerType, Name, FullAddress, dateEncoded) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt1->bind_param('sssssssssssssssssssss', $customerNo, $firstName, $middleName, $lastName, $birthdate, $age, $gender, $mobileNumber, $companyName, $email, $tin,$Region, $Province, $CityTown, $Barangay, $street, $productInfo, $customerType, $customer, $address, $asof);
            $stmt1->execute();
            $this->conn->commit();
            $status = "success";
            $message = "Client Information Successfully added ";
    
            echo json_encode(array(
                "STATUS" => $status,
                "MESSAGE" => $message,
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

    public function UpdateInfo($data){
        try {
            $this->conn->autocommit(false);

            // Check for missing required fields
            $requiredFields = ['customerType', 'customerNo', 'mobileNumber', 'email', 'Region', 'Province', 'CityTown', 'Barangay', 'tin', 'productInfo'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Missing required field: $field");
                }
            }

            $customerID = $_POST['customerID'] ?? '';
            $customerType = $_POST['customerType'];
            $customerNo = $_POST['customerNo'];
            $firstName = $_POST['firstName'] ?? '';
            $lastName = $_POST['lastName'] ?? '';
            $middleName = $_POST['middleName'] ?? '';
            $birthdate = $_POST['birthdate'] ?? '';
            $age = $_POST['age'] ?? '';
            $gender = $_POST['gender'] ?? '';
            $mobileNumber = $_POST['mobileNumber'];
            $companyName = $_POST['companyName'] ?? '';
            $email = $_POST['email'];
            $Region = $_POST['Region'];
            $Province = $_POST['Province'];
            $CityTown = $_POST['CityTown'];
            $Barangay = $_POST['Barangay'];
            $tin = $_POST['tin'];
            $street = $_POST['street'] ?? '';
            $productInfo = $_POST['productInfo'];
            $fullname = $firstName . ' ' . $middleName . '. ' . $lastName;
            $address = $street . ' ' . $Barangay . ' , ' . $CityTown . ', ' . $Province . ', ' . $Region;

            if($firstName == "" && $middleName == "" && $lastName == ""){
                $customer = $companyName;
            }else{
                $customer = $fullname;
            }
            
            date_default_timezone_set('Asia/Manila');
            $asof = date("m/d/Y H:i:s", strtotime("now"));
    
            // Prepare the statement
            $stmt1 = $this->conn->prepare("UPDATE tbl_customer_profiles SET firstName = ?, middleName = ?, lastName = ?, birthdate = ?, age = ?, gender = ?, mobileNumber = ?, companyName = ?, email = ?, tinNumber = ?, Region = ?, Province = ?, CityTown = ?, Barangay = ?, street = ?, productInfo = ?, customerType = ?, Name = ?, FullAddress = ?, dateEncoded = ? WHERE id = ?");
            
            // Check if prepare succeeded
            if (!$stmt1) {
                throw new Exception("Prepare failed: " . $this->conn->error);
            }

            // Bind parameters
            $stmt1->bind_param('ssssssssssssssssssssi', $firstName, $middleName, $lastName, $birthdate, $age, $gender, $mobileNumber, $companyName, $email, $tin, $Region, $Province, $CityTown, $Barangay, $street, $productInfo, $customerType, $customer, $address, $asof, $customerID);
            
            // Execute the statement
            if (!$stmt1->execute()) {
                throw new Exception("Execute failed: " . $stmt1->error);
            }

            $this->conn->commit();
            $status = "success";
            $message = "Client Information Successfully updated ";
    
            echo json_encode(array(
                "STATUS" => $status,
                "MESSAGE" => $message
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

}
