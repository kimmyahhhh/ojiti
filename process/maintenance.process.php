<?php
include_once("../database/connection.php");

class Maintenance Extends Database
{
    public function get_All(){
        // $curr_empno = "";
        $region = [];
        $category = [];
        $customertype = [];
        $gender = [];
        $shareholdertype = [];
        $shareholdercat = [];
        $employeestatus = [];
        $designation = [];
        $boddesig = [];
        $committee = [];
        $specialdesig = [];

        // $stmt = $this->conn->prepare("SELECT DISTINCT Region FROM tbl_barangays WHERE Region != '' ORDER BY Region ASC");
        // $stmt->execute();
        // $result = $stmt->get_result();
        // while($row = $result->fetch_assoc()){
        //     array_push($region, $row['Region']);
        // }
        // $stmt->close();

        $stmt = $this->conn->prepare("SELECT DISTINCT REGION FROM tbl_locate ORDER BY REGION ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($region, $row['REGION']);
        }
        $stmt->close();

        $stmt = $this->conn->prepare("SELECT DISTINCT Category FROM tbl_prodtype ORDER BY Category ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($category, $row['Category']);
        }
        $stmt->close();

        $stmt = $this->conn->prepare("SELECT DISTINCT ItemName FROM tbl_maintenance WHERE ItemType='CUSTOMERTYPE' ORDER BY itemname ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($customertype, $row['ItemName']);
        }
        $stmt->close();

        $stmt = $this->conn->prepare("SELECT DISTINCT ItemName FROM tbl_maintenance WHERE ItemType='GENDER' ORDER BY itemname ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($gender, $row['ItemName']);
        }
        $stmt->close();
  
        $stmt = $this->conn->prepare("SELECT DISTINCT ItemName FROM tbl_maintenance WHERE ItemType='SHAREHOLDERTYPE' ORDER BY itemname ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($shareholdertype, $row['ItemName']);
        }
        $stmt->close();
       
        $stmt = $this->conn->prepare("SELECT DISTINCT ItemName FROM tbl_maintenance WHERE ItemType='SHAREHOLDERCAT' ORDER BY itemname ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($shareholdercat, $row['ItemName']);
        }
        $stmt->close();

        $stmt = $this->conn->prepare("SELECT DISTINCT ItemName FROM tbl_maintenance WHERE ItemType='EMPLOYEE STATUS' ORDER BY itemname ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($employeestatus, $row['ItemName']);
        }
        $stmt->close();

        $stmt = $this->conn->prepare("SELECT DISTINCT ItemName FROM tbl_maintenance WHERE ItemType='DESIGNATION' ORDER BY itemname ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($designation, $row['ItemName']);
        }
        $stmt->close();

        $stmt = $this->conn->prepare("SELECT DISTINCT ItemName FROM tbl_maintenance WHERE ItemType='BOD DESIGNATION' ORDER BY itemname ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($boddesig, $row['ItemName']);
        }
        $stmt->close();

        $stmt = $this->conn->prepare("SELECT DISTINCT ItemName FROM tbl_maintenance WHERE ItemType='COMMITTEE TYPE' ORDER BY itemname ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($committee, $row['ItemName']);
        }
        $stmt->close();
        
         $stmt = $this->conn->prepare("SELECT DISTINCT ItemName FROM tbl_maintenance WHERE ItemType='SPECIAL POSITION' ORDER BY itemname ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($specialdesig, $row['ItemName']);
        }
        $stmt->close();

        echo json_encode(array(
            "region" => $region,
            "category" => $category,
            "customertype" => $customertype,
            "gender" => $gender,
            "shareholdertype" => $shareholdertype,
            "shareholdercat" => $shareholdercat,
            "employeestatus" => $employeestatus,
            "designation" => $designation,
            "boddesig" => $boddesig,
            "committee" => $committee,
            "specialdesig" => $specialdesig
       ));
    }

    public function get_province($region_selected){
        $data = [];
        $stmt = $this->conn->prepare("SELECT DISTINCT PROVINCE FROM tbl_locate WHERE REGION = ? ORDER BY PROVINCE ASC");
        $stmt->bind_param('s',$region_selected);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($data, $row['PROVINCE']);
        }
        $stmt->close();
        echo json_encode($data);
    }

    public function get_citytown($province_selected){
        $data = [];
        $stmt = $this->conn->prepare("SELECT DISTINCT MUNICIPALITY FROM tbl_locate WHERE PROVINCE = ? ORDER BY MUNICIPALITY ASC");
        $stmt->bind_param('s',$province_selected);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($data, $row['MUNICIPALITY']);
        }
        $stmt->close();
        echo json_encode($data);
    }
   
    public function get_brgy($citytown_selected){
        $data = [];
        $stmt = $this->conn->prepare("SELECT DISTINCT BARANGAY FROM tbl_locate WHERE MUNICIPALITY = ? ORDER BY BARANGAY ASC");
        $stmt->bind_param('s',$citytown_selected);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($data, $row['BARANGAY']);
        }
        $stmt->close();
        echo json_encode($data);
    }
    
    public function get_street($barangay_selected){
        $data = [];
        $stmt = $this->conn->prepare("SELECT DISTINCT street FROM tbl_supplier_info WHERE Barangay = ? AND street IS NOT NULL AND street <> '' ORDER BY street ASC");
        $stmt->bind_param('s',$barangay_selected);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            array_push($data, $row['street']);
        }
        $stmt->close();
        echo json_encode($data);
    }
    
    public function getRegion(){
        $region = [];
        $stmt = $this->conn->prepare("SELECT DISTINCT REGION FROM tbl_locate ORDER BY REGION ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            array_push($region,$row["REGION"]);
        }
        $stmt->close();
        echo json_encode($region);
    }

}
