<?php
include_once("../../database/connection.php");

class Process extends Database
{
    public function Initialize(){
        $isynBranch = $this->SelectQuery("SELECT DISTINCT Stock FROM tbl_invlist ORDER BY Stock");
        $branch = $this->SelectQuery("SELECT DISTINCT ItemName FROM tbl_maintenance WHERE ItemType='BRANCH'");
        $prodType = $this->SelectQuery("SELECT DISTINCT Type FROM tbl_prodtype ORDER BY Type");
        $customertype = $this->SelectQuery("SELECT DISTINCT Type FROM tbl_clientlist ORDER BY Type");
        $transmittno = 0000;
        $orgname = "Set organization name in system data.";

        $stmt = $this->conn->prepare("SELECT Value FROM tbl_configuration WHERE ConfigOwner='TRANSMITTALRECEIPT' AND ConfigName='CURRENTTRANSMITTALNO'");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $transmittno = $row['Value'];
        }

        $morno = "";
        $zeroes = 7 - strlen($transmittno);
        for ($i=0; $i < $zeroes; $i++) { 
            $morno = "0".$morno;
        }
        
        $currUser = $_SESSION['USERNAME'];
        $FL = substr($currUser, 0, 1);
        $LL = substr($currUser, -1, 1);

        $transmittno = "TM". $FL . "" . $LL . "" . $morno . "" . $transmittno;

        $stmt = $this->conn->prepare("SELECT Value FROM tbl_configuration WHERE ConfigOwner='TRANSMITTALRECEIPT' AND ConfigName='ORGNAME'");
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $orgname = $row['Value'];
        }

        $stmtClear = $this->conn->prepare("DELETE FROM tbl_transaction");
        $stmtClear->execute();

        echo json_encode(array( 
            "ISYNBRANCH" => $isynBranch,
            "PRODTYPE" => $prodType,
            "CUSTOMERTYPE" => $customertype,
            "TRANSMITNO" => $transmittno,
            "ORGNAME" => $orgname,
        ));
    }
    
    public function LoadBranch($data){
        $branch = [];
        $value = $data['value'];
        $stmt = $this->conn->prepare("SELECT DISTINCT Stock FROM tbl_invlistconsign WHERE Branch = ? ORDER BY Stock;");
        $stmt->bind_param('s', $value);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $branch[] = $row;
            }
        }

        echo json_encode(array( 
            "BRANCH" => $branch,
        ));
    }

    public function LoadCategory($data){
        $categ = [];
        $isConsign = $data['isConsign'];
        $type = $data['type'];
        $isynBranch = $data['isynBranch'];
        $consignBranch = $data['consignBranch'];

        if ($isConsign === "Yes"){
            $stmt = $this->conn->prepare("SELECT DISTINCT Category FROM tbl_invlistconsign WHERE Type = ? AND Stock = ? AND Branch = ? ORDER BY Category");
            $stmt->bind_param('sss', $type, $consignBranch, $isynBranch);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $categ[] = $row;
                }
            }
        } else {
            $stmt = $this->conn->prepare("SELECT DISTINCT Category FROM tbl_invlist WHERE Type = ? AND Branch = ? ORDER BY Category");
            $stmt->bind_param('ss', $type, $isynBranch);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $categ[] = $row;
                }
            }
        }

        echo json_encode(array( 
            "CATEG" => $categ,
        ));
    }

    public function LoadSerialProduct($data){
        $serialproduct = [];
        $productSINo = [];
        $isConsign = $data['isConsign'];
        $type = $data['type'];
        $category = $data['category'];
        $isynBranch = $data['isynBranch'];
        $consignBranch = $data['consignBranch'];

        if ($isConsign === "Yes"){
            $stmt = $this->conn->prepare("SELECT SIno, Serialno, Product FROM tbl_invlistconsign WHERE Category = ? AND Type = ? AND Stock = ? AND Branch = ?");
            $stmt->bind_param('ssss', $category, $type, $consignBranch, $isynBranch);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $serialproduct[] = $row;
                }
            }

            $stmt2 = $this->conn->prepare("SELECT DISTINCT SIno FROM tbl_invlistconsign WHERE Category = ? AND Type = ? AND Stock = ? AND Branch = ?");
            $stmt2->bind_param('ssss', $category, $type, $consignBranch, $isynBranch);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            if ($result2->num_rows > 0) {
                while ($row = $result2->fetch_assoc()) {
                    $productSINo[] = $row;
                }
            }
        } else {
            $stmt = $this->conn->prepare("SELECT SIno, Serialno, Product FROM tbl_invlist WHERE Category = ? AND Type = ? AND Branch = ?");
            $stmt->bind_param('sss', $category, $type, $isynBranch);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $serialproduct[] = $row;
                }
            }

            $stmt2 = $this->conn->prepare("SELECT DISTINCT SIno FROM tbl_invlist WHERE Category = ? AND Type = ? AND Branch = ?");
            $stmt2->bind_param('sss', $category, $type, $isynBranch);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            if ($result2->num_rows > 0) {
                while ($row = $result2->fetch_assoc()) {
                    $productSINo[] = $row;
                }
            }
        }

        echo json_encode(array( 
            "SRKPRDT" => $serialproduct,
            "PRDTSINO" => $productSINo,
        ));
    }

    public function LoadProductSummary($data){
        $branch = $data["isConsign"] == "No" ? $data["isynBranch"] : $data["consignBranch"];
        $table = $data["isConsign"] == "No" ? "tbl_invlist" : "tbl_invlistconsign";
        $selectBy = $data["selectBy"];
        $type = $data["type"];
        $category = $data["category"];
        $serialProduct = $data["serialProduct"];
        $SINo = $data["SINo"];
        // $isynBranch = $data["isynBranch"];
        // $consignBranch = $data["consignBranch"];
        $productSummary = "";

        if ($selectBy == "serial"){
            $stmt = $this->conn->prepare("SELECT * FROM ".$table." WHERE Type = ? AND Category = ? AND SIno = ? AND stock = ? AND Serialno = ?");
            $stmt->bind_param('sssss', $type, $category, $SINo, $branch, $serialProduct);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $productSummary = $row;
            }
        } else {
            $stmt = $this->conn->prepare("SELECT * FROM ".$table." WHERE Type = ? AND Category = ? AND SIno = ? AND stock = ? AND Product = ?");
            $stmt->bind_param('sssss', $type, $category, $SINo, $branch, $serialProduct);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $productSummary = $row;
            }

        }

        echo json_encode(array(
            "PSUMMARY" => $productSummary,
        ));
    }

    // ===================================================================================

    public function TransmittalSearch($data){
        $transactlist = [];

        $client = isset($data['client']) ? trim($data['client']) : '';       

        $like = "%".$client."%";
        $stmt = $this->conn->prepare("SELECT DISTINCT TransmittalNO, NameTO, DatePrepared, isOUT, SalesInvoice FROM tbl_transmittal WHERE NameTO LIKE ? ORDER BY str_to_date(DatePrepared,'%m/%d/%Y') DESC, TransmittalNO");
        $stmt->bind_param('s', $like);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $transactlist[] = $row;
            }
        }

        echo json_encode(array( 
            "TRANSACTIONLIST" => $transactlist,
        ));
    }

    public function SubmitInvOut($data){
        try {
            $this->conn->autocommit(false);

            $entries = json_decode($data["DATA"]);
            $values = "";

            $TotalAmount = 0;

            for ($i=0; $i < count($entries); $i++) {
                $TotalAmount .= $entries[$i][2];
            }


            $stmt = $this->conn->prepare("INSERT INTO tbl_transmittal (TransmittalNO, NameTO, NameFROM, InOrder, Quantity, ProductSerialNo, Amount, Remarks, Carrier, DateCarrier, ReceivedBy, DateReceived, totalAmount, DatePrepared, SIno, SerialNo, Product, Supplier, Category, Type, Branch, Consignment, Stock) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            // $stmt->bind_param('sssssssssssssssssssssss', $);
            $stmt->execute();


            date_default_timezone_set('Asia/Manila');
            $AsOf = date("m/d/Y", strtotime("now"));

            $user = $_SESSION['USERNAME'];
            $SalesInvoice = "";
            $stmt = $this->conn->prepare("SELECT SIcount FROM TBL_SINUMBER WHERE user = ?");
            $stmt->bind_param('s', $user);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $SalesInvoice = $row['SIcount'];
            }
            $stmt->close();

            $NewSalesInvoice = $SalesInvoice + 1;
    
            $stmt1 = $this->conn->prepare("UPDATE tbl_transaction SET SI = ? WHERE User = ?");
            $stmt1->bind_param('ss', $SalesInvoice,$user);
            $stmt1->execute();
            $stmt1->close();
            
            $stmt2 = $this->conn->prepare("UPDATE tbl_sinumber SET SIcount = ? WHERE User = ?");
            $stmt2->bind_param('ss', $NewSalesInvoice,$user);
            $stmt2->execute();
            $stmt2->close();

            $currentQuantity = 0;
            $DealerPrice = 0;
            $ProdSRP = 0;

            $Totalprice = 0;
            $TSRP = 0;
            $MyMark = 0;
            $MyTMark = 0;

            $MyVat = 0;
            $MySalesVat = 0;

            $SerialNo = "";
            $SI = "";
            $ProductName = "";
            $MyQuantity = "";
            $TotalSales = 0;
            $MyCategory = "";
            $ItemConsign = "";
            $isynBranch = "";
            $askiBranch = "";
            $Type = "";

            $SalesNoVat = 0;
            $SalesWithVat = 0;
            $AmountDue = 0;
            $VAT = 0.12;

            $SIRef = "-";
            $DateAdded = "-";

            $stmt3 = $this->conn->prepare("SELECT * FROM tbl_transaction WHERE User = ?");
            $stmt3->bind_param('s', $user);
            $stmt3->execute();
            $result3 = $stmt3->get_result();
            if ($result3->num_rows > 0) {
                while ($row3 = $result3->fetch_assoc()) {
                    $SerialNo = $row3['Serialno'];
                    $SI = $row3['SupplierSI'];
                    $ProductName = $row3['Product'];
                    $MyQuantity = $row3['Quantity'];
                    $Type = $row3['Type'];

                    if ($row3['DiscProduct'] == "Yes") {
                        $TotalSales = $row3['DiscNewTotalSRP'];
                    } else {
                        $TotalSales = $row3['TotalSRP'];
                    }
                    
                    $MyCategory = $row3['Category'];
                    $ItemConsign = $row3['itemConsign']  ;
                    $isynBranch = $row3['Branch'];
                    $askiBranch = $row3['Stock'];

                    if ($ItemConsign == "CONSIGNMENT") {
                        $stmt4 = $this->conn->prepare("SELECT * FROM tbl_invlistconsign WHERE SINO = ? AND SERIALNO = ? AND PRODUCT = ? AND CATEGORY = ? AND STOCK = ? AND BRANCH = ?");
                        $stmt4->bind_param('ssssss', $SI, $SerialNo, $ProductName, $MyCategory, $askiBranch, $isynBranch);
                        $stmt4->execute();
                        $result4 = $stmt4->get_result();
                        if ($result4->num_rows > 0) {
                            $row4 = $result4->fetch_assoc();
                            $currentQuantity = $row4["Quantity"];
                            $DealerPrice = $row4["DealerPrice"];
                            $ProdSRP = $row4["SRP"];

                            $rcQuantity = $currentQuantity - $MyQuantity;

                            if ($rcQuantity == 0){
                                $stmt = $this->conn->prepare("INSERT INTO tbl_invlistconsignhistory SELECT * FROM tbl_invlistconsign WHERE SERIALNO = ? AND SINO = ? AND PRODUCT = ? AND CATEGORY = ? AND STOCK = ? AND BRANCH = ?");
                                $stmt->bind_param('ssssss', $SerialNo,$SI,$ProductName,$MyCategory,$askiBranch,$isynBranch);
                                $stmt->execute();
                                $stmt->close();

                                $stmt = $this->conn->prepare("DELETE FROM tbl_invlistconsign WHERE SERIALNO = ? AND SINO = ? AND PRODUCT = ? AND CATEGORY = ? AND STOCK = ? AND BRANCH = ?");
                                $stmt->bind_param('ssssss', $SerialNo,$SI,$ProductName,$MyCategory,$askiBranch,$isynBranch);
                                $stmt->execute();
                                $stmt->close();
                            } else {

                                $Totalprice = floatval($DealerPrice * $rcQuantity);
                                $TSRP = floatval($ProdSRP * $rcQuantity);
                                $MyMark = floatval($ProdSRP * $rcQuantity);
                                $MyTMark = round($MyMark - $Totalprice, 2);

                                if ($Type == "WITH VAT") {
                                    $MyVat = round(((floatval($DealerPrice) / 1.12) * 0.12), 2) * floatval($rcQuantity);
                                    $MySalesVat = floatval($Totalprice) - $MyVat;
                                } else {
                                    $MyVat = 0;
                                    $MySalesVat = $Totalprice;
                                }

                                $stmt = $this->conn->prepare("UPDATE tbl_invlistconsign SET QUANTITY = ?, TOTALPRICE = ?, TOTALSRP =?, TOTALMARKUP = ?, VATSALES = ?, VAT = ?, AMOUNTDUE = ? WHERE SERIALNO = ? AND SINO = ? AND PRODUCT = ? AND CATEGORY = ? AND STOCK = ? AND BRANCH = ?");
                                $stmt->bind_param('sssssssssssss', $rcQuantity,$Totalprice,$TSRP,$MyTMark,$MySalesVat,$MyVat,$Totalprice,$SerialNo,$SI,$ProductName,$MyCategory,$askiBranch,$isynBranch);
                                $stmt->execute();
                                $stmt->close();
                            }                            
                        }
                        $stmt4->close();

                        // ==================
                        $stmt5 = $this->conn->prepare("SELECT * FROM tbl_invlist WHERE SINO = ? AND SERIALNO = ? AND PRODUCT = ? AND CATEGORY = ? AND BRANCH = ?");
                        $stmt5->bind_param('sssss', $SI, $SerialNo, $ProductName, $MyCategory, $isynBranch);
                        $stmt5->execute();
                        $result5 = $stmt5->get_result();
                        if ($result5->num_rows > 0) {
                            $row5 = $result5->fetch_assoc();
                            $currentQuantity = $row5["Quantity"];
                            $DealerPrice = $row5["DealerPrice"];
                            $ProdSRP = $row5["SRP"];

                            $rcQuantity = $currentQuantity - $MyQuantity;

                            if ($rcQuantity == 0){
                                $stmt = $this->conn->prepare("INSERT INTO tbl_prodhistory SELECT * FROM tbl_invlistconsign WHERE SERIALNO = ? AND SINO = ? AND PRODUCT = ? AND CATEGORY = ? AND BRANCH = ?");
                                $stmt->bind_param('sssss', $SerialNo,$SI,$ProductName,$MyCategory,$isynBranch);
                                $stmt->execute();
                                $stmt->close();

                                $stmt = $this->conn->prepare("DELETE FROM tbl_invlist WHERE SERIALNO = ? AND SINO = ? AND PRODUCT = ? AND CATEGORY = ? AND BRANCH = ?");
                                $stmt->bind_param('sssss', $SerialNo,$SI,$ProductName,$MyCategory,$isynBranch);
                                $stmt->execute();
                                $stmt->close();
                            } else {
                                $Totalprice = floatval($DealerPrice * $rcQuantity);
                                $TSRP = floatval($ProdSRP * $rcQuantity);
                                $MyMark = floatval($ProdSRP * $rcQuantity);
                                $MyTMark = round($MyMark - $Totalprice, 2);

                                if ($Type == "WITH VAT") {
                                    $MyVat = round(((floatval($DealerPrice) / 1.12) * 0.12), 2) * floatval($rcQuantity);
                                    $MySalesVat = floatval($Totalprice) - $MyVat;
                                } else {
                                    $MyVat = 0;
                                    $MySalesVat = $Totalprice;
                                }

                                $stmt = $this->conn->prepare("UPDATE tbl_invlist SET QUANTITY = ?, TOTALPRICE = ?, TOTALSRP =?, TOTALMARKUP = ?, VATSALES = ?, VAT = ?, AMOUNTDUE = ? WHERE SERIALNO = ? AND SINO = ? AND PRODUCT = ? AND CATEGORY = ? AND BRANCH = ?");
                                $stmt->bind_param('ssssssssssss', $rcQuantity,$Totalprice,$TSRP,$MyTMark,$MySalesVat,$MyVat,$Totalprice,$SerialNo,$SI,$ProductName,$MyCategory,$isynBranch);
                                $stmt->execute();
                                $stmt->close();
                            }                            
                        }
                        $stmt5->close();
                    } else {
                        $stmt6 = $this->conn->prepare("SELECT * FROM tbl_invlist WHERE SINO = ? AND SERIALNO = ? AND PRODUCT = ? AND CATEGORY = ? AND BRANCH = ?");
                        $stmt6->bind_param('sssss', $SI, $SerialNo, $ProductName, $MyCategory, $isynBranch);
                        $stmt6->execute();
                        $result6 = $stmt6->get_result();
                        if ($result6->num_rows > 0) {
                            $row6 = $result6->fetch_assoc();
                            $currentQuantity = $row6["Quantity"];
                            $DealerPrice = $row6["DealerPrice"];
                            $ProdSRP = $row6["SRP"];

                            $rcQuantity = $currentQuantity - $MyQuantity;

                            if ($rcQuantity == 0){
                                $stmt = $this->conn->prepare("INSERT INTO tbl_prodhistory SELECT * FROM tbl_invlist WHERE SERIALNO = ? AND SINO = ? AND PRODUCT = ? AND CATEGORY = ? AND BRANCH = ?");
                                $stmt->bind_param('sssss', $SerialNo,$SI,$ProductName,$MyCategory,$isynBranch);
                                $stmt->execute();
                                $stmt->close();

                                $stmt = $this->conn->prepare("DELETE FROM tbl_invlist WHERE SERIALNO = ? AND SINO = ? AND PRODUCT = ? AND CATEGORY = ? AND BRANCH = ?");
                                $stmt->bind_param('sssss', $SerialNo,$SI,$ProductName,$MyCategory,$isynBranch);
                                $stmt->execute();
                                $stmt->close();
                            } else {
                                $Totalprice = floatval($DealerPrice * $rcQuantity);
                                $TSRP = floatval($ProdSRP * $rcQuantity);
                                $MyMark = floatval($ProdSRP * $rcQuantity);
                                $MyTMark = round($MyMark - $Totalprice, 2);

                                if ($Type == "WITH VAT") {
                                    $MyVat = round(((floatval($DealerPrice) / 1.12) * 0.12), 2) * floatval($rcQuantity);
                                    $MySalesVat = floatval($Totalprice) - $MyVat;
                                } else {
                                    $MyVat = 0;
                                    $MySalesVat = $Totalprice;
                                }

                                $stmt = $this->conn->prepare("UPDATE tbl_invlist SET QUANTITY = ?, TOTALPRICE = ?, TOTALSRP =?, TOTALMARKUP = ?, VATSALES = ?, VAT = ?, AMOUNTDUE = ? WHERE SERIALNO = ? AND SINO = ? AND PRODUCT = ? AND CATEGORY = ? AND BRANCH = ?");
                                $stmt->bind_param('ssssssssssss', $rcQuantity,$Totalprice,$TSRP,$MyTMark,$MySalesVat,$MyVat,$Totalprice,$SerialNo,$SI,$ProductName,$MyCategory,$isynBranch);
                                $stmt->execute();
                                $stmt->close();
                            }
                        }
                        $stmt6->close();
                    }

                    $SalesNoVat += $TotalSales / 1.12;
                    $SalesWithVat = round($SalesNoVat * $VAT, 2);
                    $AmountDue = round($SalesNoVat + $SalesWithVat, 2);
                }

                $stmt7 = $this->conn->prepare("SELECT DateAdded, SI, Soldto, SUM(AmountDue) as forTotal, SUM(VatSAles) as forSales, SUM(VAT) as forVAT, TIN, Address, Status, Branch FROM tbl_transaction GROUP BY SI, User, Branch;");
                $stmt7->execute();
                $result7 = $stmt7->get_result();
                if ($result7->num_rows > 0) {
                    while ($row7 = $result7->fetch_assoc()) {
                        $SIRef = $row7['SI'];
                        $DateAdded = $row7['DateAdded'];
                        $Total = $row7['forTotal'];
                        $SoldTo = $row7['Soldto'];
                        $Tin = $row7['TIN'];
                        $Address = $row7['Address'];
                        $isynBranch = $row7['Branch'];

                        $sSales = round(floatval($Total) / 1.12, 2);
                        $sVAT = round((floatval($Total) / 1.12) * 0.12, 2);

                        $stmt = $this->conn->prepare("INSERT INTO tbl_salesjournal (DateSold,Reference,Customer,GrossSales,VAT,NetSales,TIN,Address,Stock) VALUES (?,?,?,?,?,?,?,?,?)");
                        $stmt->bind_param('sssssssss', $DateAdded,$SIRef,$SoldTo,$Total,$sVAT,$sSales,$Tin,$Address,$isynBranch);
                        $stmt->execute();
                        $stmt->close();
                    }

                    $stmt = $this->conn->prepare("UPDATE tbl_transaction SET VatSales = TotalPrice, VAT = 0, AmountDue = TotalPrice WHERE Type = 'NON-VAT' AND User = ?");
                    $stmt->bind_param('s', $user);
                    $stmt->execute();
                    $stmt->close();

                    $stmt = $this->conn->prepare("UPDATE tbl_transaction SET VatSales = ((DealerPrice * Quantity)-(round(((DealerPrice/1.12)*0.12),2) * Quantity)),VAT = (round(((DealerPrice/1.12)*0.12),2) * Quantity), AmountDue=TotalPrice WHERE Type = 'WITH VAT' AND User = ?");
                    $stmt->bind_param('s', $user);
                    $stmt->execute();
                    $stmt->close();

                    $stmt = $this->conn->prepare("INSERT INTO tbl_inventoryout (SI, SupplierSI, Batchno, Serialno, Product, Supplier, Category, Type, Quantity, DealerPrice, TotalPrice, SRP, TotalSRP, Markup, TotalMarkup, VatSales, VAT, AmountDue, DateAdded, User, Soldto, TIN, Address, Status, Stock, Branch, itemConsign, myClient, Area, Department, DiscProduct, DiscInterest, DiscAmount, DiscNewSRP, DiscNewTotalSRP, Warranty, imgname) SELECT SI, SupplierSI, Batchno, Serialno, Product, Supplier, Category, Type, Quantity, DealerPrice, TotalPrice, SRP, TotalSRP, Markup, TotalMarkup, VatSales, VAT, AmountDue, DateAdded, User, Soldto, TIN, Address, Status, Stock, Branch, itemConsign, myClient, Area, Department, DiscProduct, DiscInterest, DiscAmount, DiscNewSRP, DiscNewTotalSRP, Warranty, imgname FROM tbl_transaction WHERE User = ?");
                    $stmt->bind_param('s', $user);
                    $stmt->execute();
                    $stmt->close();

                    $stmt = $this->conn->prepare("DELETE FROM tbl_transaction WHERE User = ?");
                    $stmt->bind_param('s', $user);
                    $stmt->execute();
                    $stmt->close();
                }
                $stmt7->close();
            }
            $stmt3->close();
            
            $status = "success";
            $message = "Product details were saved successfully.";

            $tableData = json_decode($data['DATA']);
            unset($_SESSION['tableData']);
            unset($_SESSION['SalesNoVAT']);
            unset($_SESSION['SalesWithVAT']);
            unset($_SESSION['SIRef']);
            unset($_SESSION['DateAdded']);
            $_SESSION['tableData'] = $tableData;
            $_SESSION['SalesNoVAT'] = $SalesNoVat;
            $_SESSION['SalesWithVAT'] = $SalesWithVat;
            $_SESSION['SIRef'] = $SIRef;
            $_SESSION['DateAdded'] = $DateAdded;

            $this->conn->commit();
            
            echo json_encode(array(
                "STATUS" => $status,
                "MESSAGE" => $message,
                // "NOVAT" => $SalesNoVat,
                // "YESVAT" => $SalesWithVat,
                // "TTLSLS" => $TotalSales,
                "SRLNO" => $SerialNo,
                "SI" => $SI,
                "PN" => $ProductName,
                "MYCAT" => $MyCategory,
                "ISYNBRANCH" => $isynBranch,
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

    public function PrintSalesInvoice ($data) {
        $products = [];
        date_default_timezone_set('Asia/Manila');
        $AsOf = date("m/d/Y", strtotime("now"));
        $ProdPend = "NO";

        $stmt = $this->conn->prepare("SELECT Quantity, DateAdded, SIno, Supplier, SUM(TotalPrice) AS forTotal, SUM(Vat) AS forVAT, SUM(VatSales) AS forSVat, Stock, Branch, User FROM tbl_inventoryin WHERE ProdPend = 'YES' AND STR_TO_DATE(AsOf, '%m/%d/%Y') = STR_TO_DATE(?, '%m/%d/%Y') GROUP BY SIno, Supplier, Stock, Branch, DateAdded, User");
        $stmt->bind_param('s', $AsOf);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;

                $purchaseDate = $row['DateAdded'];
                $SINo = $row['SIno'];
                $supplier = $row['Supplier'];
                $total = $row['forTotal'];
                $vat = $row['forVAT'];
                $svat = $row['forSVat'];
                $stock = $row['Stock'];
                $branch = $row['Branch'];

                $stmt1 = $this->conn->prepare("INSERT INTO tbl_purchasejournal (DatePurchase, Reference, Supplier, GrossPurchase, InputVAT, NetPurchase, Stock, Branch) VALUES (?,?,?,?,?,?,?,?,?)");
                $stmt1->bind_param('ssssssss', $purchaseDate, $SINo, $supplier, $total, $vat, $svat, $stock, $branch);
                $stmt1->execute();

                $stmt2 = $this->conn->prepare("SELECT tinNumber, fullAddress FROM tbl_supplier_info WHERE supplierName = ?");
                $stmt2->bind_param('s', $supplier);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                if ($result2->num_rows > 0) {
                    $row2 = $result2->fetch_assoc();
                    $tin = $row2['tinNumber'];
                    $address = $row2['fullAddress'];
                    
                    $stmt3 = $this->conn->prepare("UPDATE tbl_purchasejournal SET TIN = ?,  Address = ? WHERE Supplier = ? AND TIN = '-' AND Address = '-'");
                    $stmt3->bind_param('sss', $tin, $address, $supplier);
                    $stmt3->execute();
                }
            }
        }

        $stmt3 = $this->conn->prepare("INSERT INTO tbl_invlist (SIno, Serialno, Product, Supplier, Category, Type, Quantity, DealerPrice, TotalPrice, SRP, TotalSRP, Markup, TotalMarkup, VatSales, Vat, AmountDue, DateAdded, DatePurchase, User, AsOf, ProdPend, Stock, Branch, Warranty, imgname) SELECT SIno, Serialno, Product, Supplier, Category, Type, Quantity, DealerPrice, TotalPrice, SRP, TotalSRP, Markup, TotalMarkup, VatSales, Vat, AmountDue, DateAdded, DatePurchase, User, AsOf, ?, Stock, Branch, Warranty, imgname FROM tbl_inventoryin WHERE ProdPend = 'YES' AND STR_TO_DATE(AsOf, '%m/%d/%Y') = STR_TO_DATE(?, '%m/%d/%Y')");
        $stmt3->bind_param('ss', $ProdPend, $AsOf);
        $stmt3->execute();

        $stmt3 = $this->conn->prepare("UPDATE tbl_inventoryin SET ProdPend = 'NO' WHERE ProdPend = 'YES'");
        $stmt3->execute();

        $tableData = json_decode($data['DATA']);
        unset($_SESSION['tableData']);
        $_SESSION['tableData'] = $tableData;

        echo json_encode(array(
            "PRODS" => $products,
            "DATAINVSESS" => $tableData,
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
