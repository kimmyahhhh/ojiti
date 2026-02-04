<?php
include_once("../../database/connection.php");
require_once("../../assets/PHPSpreadsheet/vendor/autoload.php");

use PhpOffice\PhpSpreadsheet\IOFactory;

class AcashInfoProcess extends Database
{
    public function LoadAcashInfo($data){
        // ACASH entries are recorded in tbl_books under the SLName column as 'ACASH' in the SQL dump.
        // Return the latest rows so the UI can display something useful.
        $limit = isset($data['limit']) ? (int)$data['limit'] : 50;
        if ($limit <= 0) { $limit = 5000; } // Default large limit if 0 or negative
        // if ($limit > 500) { $limit = 500; } // Removed hard cap of 500 to allow seeing all data
        
        $type = isset($data['type']) ? $data['type'] : 'Main';

        $rows = [];

        if ($type == 'Custom') {
             // Check if custom table exists
             $check = $this->conn->query("SHOW TABLES LIKE 'tbl_acash_custom'");
             if ($check->num_rows > 0) {
                 $stmt = $this->conn->prepare("
                    SELECT CDate, Branch, Fund, AcctNo, AcctTitle FROM tbl_acash_custom
                    ORDER BY STR_TO_DATE(CDate, '%Y-%m-%d') DESC, ID DESC LIMIT ?
                 ");
                 $stmt->bind_param('i', $limit);
                 $stmt->execute();
                 $result = $stmt->get_result();
                 while ($row = $result->fetch_assoc()) {
                     $rows[] = $row;
                 }
                 $stmt->close();
             }
        } elseif ($type == 'Raw') {
             // Check if raw table exists
             $check = $this->conn->query("SHOW TABLES LIKE 'tbl_acash_raw'");
             if ($check->num_rows > 0) {
                 $stmt = $this->conn->prepare("
                    SELECT CDate, Branch, Fund, AcctNo, AcctTitle FROM tbl_acash_raw
                    ORDER BY STR_TO_DATE(CDate, '%Y-%m-%d') DESC, ID DESC LIMIT ?
                 ");
                 $stmt->bind_param('i', $limit);
                 $stmt->execute();
                 $result = $stmt->get_result();
                 while ($row = $result->fetch_assoc()) {
                     $rows[] = $row;
                 }
                 $stmt->close();
             }
        } else {
            // Main - Query tbl_books
            $stmt = $this->conn->prepare("
                SELECT
                    ID,
                    CDate,
                    Branch,
                    Fund,
                    AcctNo,
                    AcctTitle,
                    Explanation,
                    SLName,
                    DrOther,
                    CrOther
                FROM tbl_books
                WHERE SLName = 'ACASH'
                ORDER BY STR_TO_DATE(CDate, '%Y-%m-%d') DESC, ID DESC
                LIMIT ?
            ");
            $stmt->bind_param('i', $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $stmt->close();
        }

        echo json_encode([
            "STATUS" => "LOADED",
            "ACASHINFO" => $rows,
        ]);
    }

    public function LoadEcpayTransactions($data){
        $limit = isset($data['limit']) ? (int)$data['limit'] : 50;
        if ($limit <= 0) { $limit = 5000; } // Default large limit if 0 or negative
        // if ($limit > 500) { $limit = 50; } // Removed hard cap


        $type = isset($data['type']) ? $data['type'] : 'Main';
        $rows = [];

        if ($type == 'Custom') {
             // Check if custom table exists
             $check = $this->conn->query("SHOW TABLES LIKE 'tbl_ecpay_custom'");
             if ($check->num_rows > 0) {
                 $stmt = $this->conn->prepare("
                    SELECT CDate, Branch, Payee, Explanation, DrOther, CrOther FROM tbl_ecpay_custom
                    ORDER BY STR_TO_DATE(CDate, '%Y-%m-%d') DESC, ID DESC LIMIT ?
                 ");
                 $stmt->bind_param('i', $limit);
                 $stmt->execute();
                 $result = $stmt->get_result();
                 while ($row = $result->fetch_assoc()) {
                     $rows[] = $row;
                 }
                 $stmt->close();
             }
        } elseif ($type == 'Raw') {
             // Check if raw table exists
             $check = $this->conn->query("SHOW TABLES LIKE 'tbl_ecpay_raw'");
             if ($check->num_rows > 0) {
                 $stmt = $this->conn->prepare("
                    SELECT CDate, Branch, Payee, Explanation, DrOther, CrOther FROM tbl_ecpay_raw
                    ORDER BY STR_TO_DATE(CDate, '%Y-%m-%d') DESC, ID DESC LIMIT ?
                 ");
                 $stmt->bind_param('i', $limit);
                 $stmt->execute();
                 $result = $stmt->get_result();
                 while ($row = $result->fetch_assoc()) {
                     $rows[] = $row;
                 }
                 $stmt->close();
             }
        } else {
            // Main - ECpay transactions appear in tbl_books via Payee and/or Explanation.
            $like = "%EC%PAY%";
            $like2 = "%ECPAY%";

            $stmt = $this->conn->prepare("
                SELECT
                    ID,
                    CDate,
                    Branch,
                    Fund,
                    Payee,
                    Explanation,
                    DrOther,
                    CrOther,
                    BookType
                FROM tbl_books
                WHERE (Payee LIKE ? OR Explanation LIKE ? OR Explanation LIKE ?)
                ORDER BY STR_TO_DATE(CDate, '%Y-%m-%d') DESC, ID DESC
                LIMIT ?
            ");
            $stmt->bind_param('sssi', $like, $like, $like2, $limit);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()) {
                $rows[] = $row;
            }
            $stmt->close();
        }

        echo json_encode([
            "STATUS" => "LOADED",
            "ECPAYTXNS" => $rows,
        ]);
    }

    public function LoadGraphData($data) {
        $rows = [];
        
        // 1. ACash Counts by Branch (Main Data from tbl_books)
        // Aggregating from tbl_books ensures we show ALL data (Historical + Uploaded)
        $acashData = [];
        $sqlAcash = "SELECT Branch, COUNT(*) as Count FROM tbl_books WHERE SLName = 'ACASH' GROUP BY Branch";
        $resultAcash = $this->conn->query($sqlAcash);
        if ($resultAcash) {
            while($row = $resultAcash->fetch_assoc()) {
                $acashData[] = $row;
            }
        }

        // 2. ECPay Amounts by Branch (Main Data from tbl_books)
        $ecpayData = [];
        $like = "%EC%PAY%";
        $like2 = "%ECPAY%";
        $sqlEcpay = "SELECT Branch, SUM(DrOther) as TotalAmount FROM tbl_books 
                     WHERE (Payee LIKE '$like' OR Explanation LIKE '$like' OR Explanation LIKE '$like2') 
                     GROUP BY Branch";
        $resultEcpay = $this->conn->query($sqlEcpay);
        if ($resultEcpay) {
            while($row = $resultEcpay->fetch_assoc()) {
                $ecpayData[] = $row;
            }
        }

        echo json_encode([
            "STATUS" => "LOADED",
            "ACASH_GRAPH" => $acashData,
            "ECPAY_GRAPH" => $ecpayData
        ]);
    }

    public function UploadCustom($files){
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate(__FILE__, true);
        }
        // Start output buffering to catch any unwanted output
        ob_start();
        ini_set('display_errors', 0);
        ini_set('memory_limit', '512M');
        register_shutdown_function(function() {
            $error = error_get_last();
            if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
                // Clean buffer
                while (ob_get_level()) ob_end_clean();
                http_response_code(200); // Ensure 200 OK so JS parses JSON
                echo json_encode(["STATUS" => "ERROR", "MESSAGE" => "Server Fatal Error: " . $error['message'] . " in " . $error['file'] . " line " . $error['line']]);
            }
        });

        if (!isset($files['file']) || $files['file']['error'] != 0) {
            $msg = "No file uploaded or error occurred.";
            if (isset($files['file']['error'])) {
                $msg .= " Error code: " . $files['file']['error'];
            }
            while (ob_get_level()) ob_end_clean();
            echo json_encode(["STATUS" => "ERROR", "MESSAGE" => $msg]);
            return;
        }

        $file = $files['file']['tmp_name'];
        $originalName = $files['file']['name'];
        
        // Ensure table exists with correct schema. 
        // Note: For Custom, we usually append, so we don't DROP. 
        // But to prevent schema mismatch crashes, we use a robust prepare check.
        $this->conn->query("CREATE TABLE IF NOT EXISTS tbl_acash_custom (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            CDate VARCHAR(50),
            Branch VARCHAR(100),
            Fund VARCHAR(100),
            AcctNo VARCHAR(100),
            AcctTitle VARCHAR(255)
        )");

        $this->conn->query("CREATE TABLE IF NOT EXISTS tbl_ecpay_custom (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            CDate VARCHAR(50),
            Branch VARCHAR(100),
            Payee VARCHAR(255),
            Explanation TEXT,
            DrOther DECIMAL(15,2),
            CrOther DECIMAL(15,2)
        )");

        try {
            // Identify file type and load using PhpSpreadsheet
            $inputFileType = IOFactory::identify($file);
            $reader = IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file);
            
            $insertedCount = 0;
            $debugInfo = [];
            
            foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                $sheetName = $worksheet->getTitle();
                $rows = $worksheet->toArray();
                
                $currentSection = ''; // 'ACASH' or 'ECPAY'
                $colMap = [];
                
                // Helper to find index case-insensitively
                $findCol = function($map, $possibleNames) {
                    foreach ($possibleNames as $name) {
                        if (isset($map[$name])) return $map[$name];
                        foreach ($map as $key => $index) {
                            if (strcasecmp(trim($key), trim($name)) === 0) return $index;
                        }
                    }
                    return false;
                };

                foreach ($rows as $rowIndex => $data) {
                    if (empty(array_filter($data))) continue;
                    
                    // Clean data
                    $data = array_map(function($v){ 
                        $v = (string)$v;
                        // Replace NBSP with space
                        $v = str_replace(["\xc2\xa0", "\xa0"], ' ', $v);
                        return trim($v); 
                    }, $data);
                    $tempMap = array_flip($data);
                    
                    // Debug: Log row and current state
                    if (count($debugInfo) < 20) {
                        $info = "Row $rowIndex: " . implode(", ", array_slice($data, 0, 5));
                        $info .= " | Section: '$currentSection'";
                        
                        // Check for Headers
                        $acctNoIdx = $findCol($tempMap, ['AcctNo', 'Account No', 'Account Number', 'Acct #', 'Acct. No.', 'Mobile No', 'Accountno', 'Email']);
                        $dateIdx = $findCol($tempMap, ['Date', 'CDate', 'Posting Date', 'Trans. Date', 'Transaction Date']);
                        $payeeIdx = $findCol($tempMap, ['Payee', 'Payee Name', 'Name', 'Payer/Payee', 'Biller', 'Service', 'Email']);
                        
                        $info .= " | DateIdx: " . ($dateIdx !== false ? $dateIdx : 'false');
                        $info .= " | AcctIdx: " . ($acctNoIdx !== false ? $acctNoIdx : 'false');
                        $info .= " | PayeeIdx: " . ($payeeIdx !== false ? $payeeIdx : 'false');
                        $info .= " | HasEmail: " . (isset($tempMap['Email']) ? 'Yes' : 'No');
                        
                        $debugInfo[] = $info;
                    }

                    // Check for Headers (Redundant but needed for logic flow)
                    $acctNoIdx = $findCol($tempMap, ['AcctNo', 'Account No', 'Account Number', 'Acct #', 'Acct. No.', 'Mobile No', 'Accountno', 'Email']);
                    $dateIdx = $findCol($tempMap, ['Date', 'CDate', 'Posting Date', 'Trans. Date', 'Transaction Date']);
                    $payeeIdx = $findCol($tempMap, ['Payee', 'Payee Name', 'Name', 'Payer/Payee', 'Biller', 'Service', 'Email']);
                    
                    // Force detection if Email and Date are present, default to ACASH first
                    if ($dateIdx !== false && isset($tempMap['Email'])) {
                         $currentSection = 'ACASH';
                         $colMap = $tempMap;
                         continue;
                    }
                    
                    if ($acctNoIdx !== false && $dateIdx !== false) {
                        $currentSection = 'ACASH';
                        $colMap = $tempMap;
                        continue; // Skip header row
                    }
                    
                    if ($payeeIdx !== false && $dateIdx !== false) {
                        $currentSection = 'ECPAY';
                        $colMap = $tempMap;
                        continue; // Skip header row
                    }

                    // Section reset detection
                    $rowString = implode(" ", $data);
                    if (stripos($rowString, "ACash Information") !== false || stripos($rowString, "ECpay Transaction") !== false) {
                        $currentSection = ''; 
                        continue;
                    }

                    if ($currentSection == 'ACASH') {
                        $dateIdx = $findCol($colMap, ['Date', 'CDate', 'Posting Date', 'Trans. Date', 'Transaction Date']);
                        $branchIdx = $findCol($colMap, ['Branch', 'Branch Name']);
                        $fundIdx = $findCol($colMap, ['Fund']);
                        $acctNoIdx = $findCol($colMap, ['AcctNo', 'Account No', 'Account Number', 'Acct #', 'Acct. No.', 'Mobile No', 'Accountno', 'Email']);
                        $titleIdx = $findCol($colMap, ['AcctTitle', 'Account Title', 'Title', 'Telco']);

                        $date = ($dateIdx !== false && isset($data[$dateIdx])) ? $data[$dateIdx] : '';
                        // Excel Date Handling
                        if (is_numeric($date)) {
                             try {
                                 $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
                             } catch (Exception $e) {
                                 // Fallback if conversion fails
                             }
                        } else {
                            // Try to parse string dates
                            $timestamp = strtotime($date);
                            if ($timestamp !== false) {
                                $date = date('Y-m-d', $timestamp);
                            }
                        }

                        $branch = ($branchIdx !== false && isset($data[$branchIdx])) ? $data[$branchIdx] : '';
                        
                        // Attempt to resolve Branch from Email if missing
                        if (empty($branch)) {
                             // Check if AcctNo is actually an email (since Email is an alias for AcctNo)
                             $potentialEmail = ($acctNoIdx !== false && isset($data[$acctNoIdx])) ? trim($data[$acctNoIdx]) : '';
                             
                             // Or specifically look for an Email column if it exists separately
                             $emailIdx = $findCol($colMap, ['Email']);
                             if ($emailIdx !== false && isset($data[$emailIdx])) {
                                 $potentialEmail = trim($data[$emailIdx]);
                             }

                             if (!empty($potentialEmail) && filter_var($potentialEmail, FILTER_VALIDATE_EMAIL)) {
                                 $stmtMap = $this->conn->prepare("SELECT branch FROM tbl_email_branch_mapping WHERE email = ? LIMIT 1");
                                 if ($stmtMap) {
                                     $stmtMap->bind_param("s", $potentialEmail);
                                     $stmtMap->execute();
                                     $resMap = $stmtMap->get_result();
                                     if ($rowMap = $resMap->fetch_assoc()) {
                                         $branch = $rowMap['branch'];
                                     }
                                     $stmtMap->close();
                                 }
                             }
                        }

                        $fund = ($fundIdx !== false && isset($data[$fundIdx])) ? $data[$fundIdx] : '';
                        $acctNo = ($acctNoIdx !== false && isset($data[$acctNoIdx])) ? $data[$acctNoIdx] : '';
                        $acctTitle = ($titleIdx !== false && isset($data[$titleIdx])) ? $data[$titleIdx] : '';
                        
                        // Append Variant if available (for Loads)
                        $variantIdx = $findCol($colMap, ['Variant']);
                        if ($variantIdx !== false && isset($data[$variantIdx]) && !empty($data[$variantIdx])) {
                            $acctTitle .= " " . $data[$variantIdx];
                        }

                        if (!empty($acctNo) || !empty($acctTitle)) {
                            $stmt = $this->conn->prepare("INSERT INTO tbl_acash_custom (CDate, Branch, Fund, AcctNo, AcctTitle) VALUES (?, ?, ?, ?, ?)");
                            if ($stmt) {
                                $stmt->bind_param("sssss", $date, $branch, $fund, $acctNo, $acctTitle);
                                $stmt->execute();
                                $stmt->close();
                            }
                            
                            $stmt = $this->conn->prepare("INSERT INTO tbl_books (CDate, Branch, Fund, AcctNo, AcctTitle, SLName) VALUES (?, ?, ?, ?, ?, 'ACASH')");
                            if ($stmt) {
                                $stmt->bind_param("sssss", $date, $branch, $fund, $acctNo, $acctTitle);
                                $stmt->execute();
                                $stmt->close();
                            }

                            // Update Master Data
                            $this->updateMasterData($acctTitle, $branch, $potentialEmail ?? null);

                            $insertedCount++;
                        }
                    } elseif ($currentSection == 'ECPAY') {
                        $dateIdx = $findCol($colMap, ['Date', 'CDate', 'Posting Date', 'Trans. Date', 'Transaction Date']);
                        $branchIdx = $findCol($colMap, ['Branch', 'Branch Name']);
                        $payeeIdx = $findCol($colMap, ['Payee', 'Payee Name', 'Name', 'Payer/Payee', 'Biller', 'Service', 'Email']);
                        $explIdx = $findCol($colMap, ['Explanation', 'Particulars', 'Description', 'Memo', 'Account No', 'AccountNo', 'Reference No', 'Reference']);
                        $amtIdx = $findCol($colMap, ['Amount', 'Debit', 'DrOther', 'Amount(PHP)', 'Total', 'Amount Transacted']);

                        $date = ($dateIdx !== false && isset($data[$dateIdx])) ? $data[$dateIdx] : '';
                        if (is_numeric($date)) {
                             try {
                                 $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
                             } catch (Exception $e) {
                             }
                        } else {
                            // Try to parse string dates
                            $timestamp = strtotime($date);
                            if ($timestamp !== false) {
                                $date = date('Y-m-d', $timestamp);
                            }
                        }
                        
                        $branch = ($branchIdx !== false && isset($data[$branchIdx])) ? $data[$branchIdx] : '';

                        // Attempt to resolve Branch from Email if missing
                        if (empty($branch)) {
                             // Check if Payee is actually an email
                             $potentialEmail = ($payeeIdx !== false && isset($data[$payeeIdx])) ? trim($data[$payeeIdx]) : '';
                             
                             // Or specifically look for an Email column
                             $emailIdx = $findCol($colMap, ['Email']);
                             if ($emailIdx !== false && isset($data[$emailIdx])) {
                                 $potentialEmail = trim($data[$emailIdx]);
                             }

                             if (!empty($potentialEmail) && filter_var($potentialEmail, FILTER_VALIDATE_EMAIL)) {
                                 $stmtMap = $this->conn->prepare("SELECT branch FROM tbl_email_branch_mapping WHERE email = ? LIMIT 1");
                                 if ($stmtMap) {
                                     $stmtMap->bind_param("s", $potentialEmail);
                                     $stmtMap->execute();
                                     $resMap = $stmtMap->get_result();
                                     if ($rowMap = $resMap->fetch_assoc()) {
                                         $branch = $rowMap['branch'];
                                     }
                                     $stmtMap->close();
                                 }
                             }
                        }

                        $payee = ($payeeIdx !== false && isset($data[$payeeIdx])) ? $data[$payeeIdx] : '';
                        $explanation = ($explIdx !== false && isset($data[$explIdx])) ? $data[$explIdx] : '';
                        $amount = ($amtIdx !== false && isset($data[$amtIdx])) ? $data[$amtIdx] : 0;
                        
                        // Append additional info to Explanation for Paybills/Services
                        if ($explIdx !== false) {
                             // If we found 'Account No', check if we also have 'Reference No' to append
                             $refIdx = $findCol($colMap, ['Reference No', 'Reference', 'ServiceRef', 'TransactionID']);
                             if ($refIdx !== false && isset($data[$refIdx]) && !empty($data[$refIdx])) {
                                 $explanation = "Acct: " . $explanation . " Ref: " . $data[$refIdx];
                             }
                        }

                        $amount = str_replace(',', '', $amount);
                        $dr = is_numeric($amount) ? $amount : 0;
                        $cr = 0;

                        if (!empty($payee) || !empty($explanation)) {
                            $stmt = $this->conn->prepare("INSERT INTO tbl_ecpay_custom (CDate, Branch, Payee, Explanation, DrOther, CrOther) VALUES (?, ?, ?, ?, ?, ?)");
                            if ($stmt) {
                                $stmt->bind_param("ssssdd", $date, $branch, $payee, $explanation, $dr, $cr);
                                $stmt->execute();
                                $stmt->close();
                            }

                            $stmt = $this->conn->prepare("INSERT INTO tbl_books (CDate, Branch, Payee, Explanation, DrOther, CrOther) VALUES (?, ?, ?, ?, ?, ?)");
                            if ($stmt) {
                                $stmt->bind_param("ssssdd", $date, $branch, $payee, $explanation, $dr, $cr);
                                $stmt->execute();
                                $stmt->close();
                            }
                            
                            // Update Master Data
                            $this->updateMasterData($payee, $branch, $potentialEmail ?? null);

                            $insertedCount++;
                        }
                    }
                }
            }

            if ($insertedCount > 0) {
                 echo json_encode(["STATUS" => "SUCCESS", "MESSAGE" => "Uploaded $insertedCount records successfully."]);
            } else {
                 echo json_encode(["STATUS" => "ERROR", "MESSAGE" => "[DEBUG_V3] No records uploaded. Could not detect 'Date' AND 'AcctNo'/'Payee' headers. Debug Info: " . implode(" | ", $debugInfo)]);
            }

        } catch (Exception $e) {
            echo json_encode(["STATUS" => "ERROR", "MESSAGE" => "Error processing file: " . $e->getMessage()]);
        }
    }

    public function UploadRaw($files){
        ini_set('display_errors', 0);
        ini_set('memory_limit', '512M');
        register_shutdown_function(function() {
            $error = error_get_last();
            if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
                while (ob_get_level()) ob_end_clean();
                http_response_code(200); // Ensure 200 OK
                echo json_encode(["STATUS" => "ERROR", "MESSAGE" => "Server Fatal Error: " . $error['message'] . " in " . $error['file'] . " line " . $error['line']]);
            }
        });

        if (!isset($files['file']) || $files['file']['error'] != 0) {
            $msg = "No file uploaded or error occurred.";
            if (isset($files['file']['error'])) {
                $msg .= " Error code: " . $files['file']['error'];
            }
            echo json_encode(["STATUS" => "ERROR", "MESSAGE" => $msg]);
            return;
        }

        $file = $files['file']['tmp_name'];
        $originalName = $files['file']['name'];
        
        // Reset Raw tables to ensure clean state and correct schema
        $this->conn->query("DROP TABLE IF EXISTS tbl_acash_raw");
        $this->conn->query("CREATE TABLE tbl_acash_raw (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            CDate VARCHAR(50),
            Branch VARCHAR(100),
            Fund VARCHAR(100),
            AcctNo VARCHAR(100),
            AcctTitle VARCHAR(255)
        )");
        
        $this->conn->query("DROP TABLE IF EXISTS tbl_ecpay_raw");
        $this->conn->query("CREATE TABLE tbl_ecpay_raw (
            ID INT AUTO_INCREMENT PRIMARY KEY,
            CDate VARCHAR(50),
            Branch VARCHAR(100),
            Payee VARCHAR(255),
            Explanation TEXT,
            DrOther DECIMAL(15,2),
            CrOther DECIMAL(15,2)
        )");

        try {
            // Identify file type and load using PhpSpreadsheet
            $inputFileType = IOFactory::identify($file);
            $reader = IOFactory::createReader($inputFileType);
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file);
            
            $insertedCount = 0;
            $debugInfo = [];
            
            foreach ($spreadsheet->getWorksheetIterator() as $worksheet) {
                $sheetName = $worksheet->getTitle();
                $rows = $worksheet->toArray();
                
                $currentSection = ''; // 'ACASH' or 'ECPAY'
                $colMap = [];
                
                // Helper to find index case-insensitively
                $findCol = function($map, $possibleNames) {
                    foreach ($possibleNames as $name) {
                        if (isset($map[$name])) return $map[$name];
                        foreach ($map as $key => $index) {
                            if (strcasecmp(trim($key), trim($name)) === 0) return $index;
                        }
                    }
                    return false;
                };

                foreach ($rows as $rowIndex => $data) {
                    if (empty(array_filter($data))) continue;
                    
                    // Clean data
                    $data = array_map(function($v){ return trim((string)$v); }, $data);
                    $tempMap = array_flip($data);
                    
                    // Check for Headers
                    $acctNoIdx = $findCol($tempMap, ['AcctNo', 'Account No', 'Account Number', 'Acct #', 'Acct. No.', 'Mobile No', 'Accountno', 'Email']);
                    $dateIdx = $findCol($tempMap, ['Date', 'CDate', 'Posting Date', 'Trans. Date', 'Transaction Date']);
                    $payeeIdx = $findCol($tempMap, ['Payee', 'Payee Name', 'Name', 'Payer/Payee', 'Biller', 'Service']);

                    if (count($debugInfo) < 20) {
                        $info = "Row $rowIndex: " . implode(", ", array_slice($data, 0, 5));
                        $info .= " | Section: '$currentSection'";
                        $info .= " | DateIdx: " . ($dateIdx !== false ? $dateIdx : 'false');
                        $info .= " | AcctIdx: " . ($acctNoIdx !== false ? $acctNoIdx : 'false');
                        $info .= " | HasEmail: " . (isset($tempMap['Email']) ? 'Yes' : 'No');
                        $debugInfo[] = $info;
                    }
                    
                    // Force detection if Email and Date are present (Explicit Check)
                    if ($dateIdx !== false && isset($tempMap['Email'])) {
                         $currentSection = 'ACASH';
                         $colMap = $tempMap;
                         continue;
                    }

                    if ($acctNoIdx !== false && $dateIdx !== false) {
                        $currentSection = 'ACASH';
                        $colMap = $tempMap;
                        continue; // Skip header row
                    }
                    
                    if ($payeeIdx !== false && $dateIdx !== false) {
                        $currentSection = 'ECPAY';
                        $colMap = $tempMap;
                        continue; // Skip header row
                    }

                    // Section reset detection
                    $rowString = implode(" ", $data);
                    if (stripos($rowString, "ACash Information") !== false || stripos($rowString, "ECpay Transaction") !== false) {
                        $currentSection = ''; 
                        continue;
                    }

                    if ($currentSection == 'ACASH') {
                        $dateIdx = $findCol($colMap, ['Date', 'CDate', 'Posting Date', 'Trans. Date', 'Transaction Date']);
                        $branchIdx = $findCol($colMap, ['Branch', 'Branch Name']);
                        $fundIdx = $findCol($colMap, ['Fund']);
                        $acctNoIdx = $findCol($colMap, ['AcctNo', 'Account No', 'Account Number', 'Acct #', 'Acct. No.', 'Mobile No', 'Accountno', 'Email']);
                        $titleIdx = $findCol($colMap, ['AcctTitle', 'Account Title', 'Title', 'Telco']);

                        $date = ($dateIdx !== false && isset($data[$dateIdx])) ? $data[$dateIdx] : '';
                        // Excel Date Handling
                        if (is_numeric($date)) {
                             try {
                                 $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
                             } catch (Exception $e) {
                             }
                        } else {
                            // Try to parse string dates
                            $timestamp = strtotime($date);
                            if ($timestamp !== false) {
                                $date = date('Y-m-d', $timestamp);
                            }
                        }

                        $branch = ($branchIdx !== false && isset($data[$branchIdx])) ? $data[$branchIdx] : '';
                        
                        // Attempt to resolve Branch from Email if missing
                        $potentialEmail = '';
                        if (empty($branch)) {
                             // Check if AcctNo is actually an email
                             $potentialEmail = ($acctNoIdx !== false && isset($data[$acctNoIdx])) ? trim($data[$acctNoIdx]) : '';
                             
                             // Or specifically look for an Email column
                             $emailIdx = $findCol($colMap, ['Email']);
                             if ($emailIdx !== false && isset($data[$emailIdx])) {
                                 $potentialEmail = trim($data[$emailIdx]);
                             }

                             if (!empty($potentialEmail) && filter_var($potentialEmail, FILTER_VALIDATE_EMAIL)) {
                                 $stmtMap = $this->conn->prepare("SELECT branch FROM tbl_email_branch_mapping WHERE email = ? LIMIT 1");
                                 if ($stmtMap) {
                                     $stmtMap->bind_param("s", $potentialEmail);
                                     $stmtMap->execute();
                                     $resMap = $stmtMap->get_result();
                                     if ($rowMap = $resMap->fetch_assoc()) {
                                         $branch = $rowMap['branch'];
                                     }
                                     $stmtMap->close();
                                 }
                             }
                        }

                        $fund = ($fundIdx !== false && isset($data[$fundIdx])) ? $data[$fundIdx] : '';
                        $acctNo = ($acctNoIdx !== false && isset($data[$acctNoIdx])) ? $data[$acctNoIdx] : '';
                        $acctTitle = ($titleIdx !== false && isset($data[$titleIdx])) ? $data[$titleIdx] : '';
                        
                        // Append Variant if available (for Loads)
                        $variantIdx = $findCol($colMap, ['Variant']);
                        if ($variantIdx !== false && isset($data[$variantIdx]) && !empty($data[$variantIdx])) {
                            $acctTitle .= " " . $data[$variantIdx];
                        }

                        if (!empty($acctNo) || !empty($acctTitle)) {
                            $stmt = $this->conn->prepare("INSERT INTO tbl_acash_raw (CDate, Branch, Fund, AcctNo, AcctTitle) VALUES (?, ?, ?, ?, ?)");
                            if ($stmt) {
                                $stmt->bind_param("sssss", $date, $branch, $fund, $acctNo, $acctTitle);
                                $stmt->execute();
                                $stmt->close();
                            }
                            
                            $stmt = $this->conn->prepare("INSERT INTO tbl_books (CDate, Branch, Fund, AcctNo, AcctTitle, SLName) VALUES (?, ?, ?, ?, ?, 'ACASH')");
                            if ($stmt) {
                                $stmt->bind_param("sssss", $date, $branch, $fund, $acctNo, $acctTitle);
                                $stmt->execute();
                                $stmt->close();
                            }

                            // Update Master Data
                            $this->updateMasterData($acctTitle, $branch, $potentialEmail);

                            $insertedCount++;
                        }
                    } elseif ($currentSection == 'ECPAY') {
                        $dateIdx = $findCol($colMap, ['Date', 'CDate', 'Posting Date', 'Trans. Date', 'Transaction Date']);
                        $branchIdx = $findCol($colMap, ['Branch', 'Branch Name']);
                        $payeeIdx = $findCol($colMap, ['Payee', 'Payee Name', 'Name', 'Payer/Payee', 'Biller', 'Service']);
                        $explIdx = $findCol($colMap, ['Explanation', 'Particulars', 'Description', 'Memo', 'Account No', 'AccountNo', 'Reference No', 'Reference']);
                        $amtIdx = $findCol($colMap, ['Amount', 'Debit', 'DrOther', 'Amount(PHP)', 'Total', 'Amount Transacted']);

                        $date = ($dateIdx !== false && isset($data[$dateIdx])) ? $data[$dateIdx] : '';
                        if (is_numeric($date)) {
                             try {
                                 $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($date)->format('Y-m-d');
                             } catch (Exception $e) {
                             }
                        } else {
                            // Try to parse string dates
                            $timestamp = strtotime($date);
                            if ($timestamp !== false) {
                                $date = date('Y-m-d', $timestamp);
                            }
                        }
                        
                        $branch = ($branchIdx !== false && isset($data[$branchIdx])) ? $data[$branchIdx] : '';

                        // Attempt to resolve Branch from Email if missing
                        $potentialEmail = '';
                        if (empty($branch)) {
                             // Check if Payee is actually an email
                             $potentialEmail = ($payeeIdx !== false && isset($data[$payeeIdx])) ? trim($data[$payeeIdx]) : '';
                             
                             // Or specifically look for an Email column
                             $emailIdx = $findCol($colMap, ['Email']);
                             if ($emailIdx !== false && isset($data[$emailIdx])) {
                                 $potentialEmail = trim($data[$emailIdx]);
                             }

                             if (!empty($potentialEmail) && filter_var($potentialEmail, FILTER_VALIDATE_EMAIL)) {
                                 $stmtMap = $this->conn->prepare("SELECT branch FROM tbl_email_branch_mapping WHERE email = ? LIMIT 1");
                                 if ($stmtMap) {
                                     $stmtMap->bind_param("s", $potentialEmail);
                                     $stmtMap->execute();
                                     $resMap = $stmtMap->get_result();
                                     if ($rowMap = $resMap->fetch_assoc()) {
                                         $branch = $rowMap['branch'];
                                     }
                                     $stmtMap->close();
                                 }
                             }
                        }

                        $payee = ($payeeIdx !== false && isset($data[$payeeIdx])) ? $data[$payeeIdx] : '';
                        $explanation = ($explIdx !== false && isset($data[$explIdx])) ? $data[$explIdx] : '';
                        $amount = ($amtIdx !== false && isset($data[$amtIdx])) ? $data[$amtIdx] : 0;
                        
                        // Append additional info to Explanation for Paybills/Services
                        if ($explIdx !== false) {
                             // If we found 'Account No', check if we also have 'Reference No' to append
                             $refIdx = $findCol($colMap, ['Reference No', 'Reference', 'ServiceRef']);
                             if ($refIdx !== false && isset($data[$refIdx]) && !empty($data[$refIdx])) {
                                 $explanation = "Acct: " . $explanation . " Ref: " . $data[$refIdx];
                             }
                        }

                        $amount = str_replace(',', '', $amount);
                        $dr = is_numeric($amount) ? $amount : 0;
                        $cr = 0;

                        if (!empty($payee) || !empty($explanation)) {
                            $stmt = $this->conn->prepare("INSERT INTO tbl_ecpay_raw (CDate, Branch, Payee, Explanation, DrOther, CrOther) VALUES (?, ?, ?, ?, ?, ?)");
                            if ($stmt) {
                                $stmt->bind_param("ssssdd", $date, $branch, $payee, $explanation, $dr, $cr);
                                $stmt->execute();
                                $stmt->close();
                            }

                            $stmt = $this->conn->prepare("INSERT INTO tbl_books (CDate, Branch, Payee, Explanation, DrOther, CrOther) VALUES (?, ?, ?, ?, ?, ?)");
                            if ($stmt) {
                                $stmt->bind_param("ssssdd", $date, $branch, $payee, $explanation, $dr, $cr);
                                $stmt->execute();
                                $stmt->close();
                            }
                            
                            // Update Master Data
                            $this->updateMasterData($payee, $branch, $potentialEmail);

                            $insertedCount++;
                        }
                    }
                }
            }

            if ($insertedCount > 0) {
                 while (ob_get_level()) ob_end_clean();
                 echo json_encode(["STATUS" => "SUCCESS", "MESSAGE" => "Uploaded $insertedCount records successfully."]);
            } else {
                 while (ob_get_level()) ob_end_clean();
                 echo json_encode(["STATUS" => "ERROR", "MESSAGE" => "No records uploaded. Could not detect 'Date' AND 'AcctNo'/'Payee' headers in any sheet. First rows seen: " . implode(" | ", $debugInfo)]);
            }

        } catch (Exception $e) {
            while (ob_get_level()) ob_end_clean();
            echo json_encode(["STATUS" => "ERROR", "MESSAGE" => "Error processing file: " . $e->getMessage()]);
        }
    }

    private function updateMasterData($name, $branch, $email = null) {
        if (empty($name) && empty($email)) return;
        
        // 1. Update Branch
        if (!empty($branch)) {
            $branch = trim($branch);
            $checkBr = $this->conn->query("SELECT id FROM tbl_master_branches WHERE branch_name = '$branch'");
            if ($checkBr->num_rows == 0) {
                $stmt = $this->conn->prepare("INSERT INTO tbl_master_branches (branch_name) VALUES (?)");
                $stmt->bind_param("s", $branch);
                $stmt->execute();
                $branchId = $stmt->insert_id;
                $stmt->close();
            } else {
                $branchId = $checkBr->fetch_assoc()['id'];
            }
        } else {
            $branchId = null;
        }

        // 2. Update Identity
        $name = trim($name);
        $email = trim($email);
        $identityId = null;

        // Try to find by email first
        if (!empty($email)) {
            $checkId = $this->conn->query("SELECT id FROM tbl_master_identities WHERE email = '$email'");
            if ($checkId->num_rows > 0) {
                $identityId = $checkId->fetch_assoc()['id'];
            }
        }
        
        // If not found by email, try by name
        if (!$identityId && !empty($name)) {
            $checkId = $this->conn->query("SELECT id FROM tbl_master_identities WHERE full_name = '$name'");
            if ($checkId->num_rows > 0) {
                $identityId = $checkId->fetch_assoc()['id'];
            }
        }

        // Insert if new
        if (!$identityId) {
            $insertName = !empty($name) ? $name : $email; // Fallback to email as name
            $stmt = $this->conn->prepare("INSERT INTO tbl_master_identities (full_name, email, branch_id, source_table) VALUES (?, ?, ?, 'upload')");
            $stmt->bind_param("ssi", $insertName, $email, $branchId);
            $stmt->execute();
            $stmt->close();
        } else {
            // Update branch if missing
            if ($branchId) {
                $this->conn->query("UPDATE tbl_master_identities SET branch_id = $branchId WHERE id = $identityId AND branch_id IS NULL");
            }
            // Update email if missing
            if (!empty($email)) {
                 $this->conn->query("UPDATE tbl_master_identities SET email = '$email' WHERE id = $identityId AND email IS NULL");
            }
        }
    }
}
