<?php
require_once('../../assets/tcpdf/tcpdf.php');

class Reports extends Database 
{
    public function PrintOutgoingSalesInvoice($tableData,$SalesNoVAT,$SalesWithVAT,$SIRef,$DateAdded){
        ob_clean();
		ob_flush();

        ini_set('memory_limit','-1');
        set_time_limit(0);
        
		$pdf = new TCPDF('P', PDF_UNIT, 'A5', true, 'UTF-8', false);
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('isynergiesinc');
		$pdf->SetTitle('SUPPLIER\'S RECEIPT');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
		$pdf->SetMargins(4, 8, 4);
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		    require_once(dirname(__FILE__).'/lang/eng.php');
		    $pdf->setLanguageArray($l);
		}
		$pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();

        $contentdata = '';

        $datesold = "-";
        $soldto = "-";
        $tin = "-";
        $address = "-";
        $totalAmountDue = 0;

        $stmt = $this->conn->prepare("SELECT * FROM tbl_salesjournal WHERE STR_TO_DATE(DateSold,'%m/%d/%Y') = STR_TO_DATE(?,'%m/%d/%Y') AND Reference = ?");
        $stmt->bind_param("ss",$DateAdded,$SIRef);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $datesold = $row["DateSold"];
            $soldto = $row["Customer"];
            $tin = $row["TIN"];
            $address = $row["Address"];
        }

        $rowsqty = "8";

        foreach ($tableData as $row){
            $rowsqty--;
            $contentdata .= '
                    <tr style="font-size:6pt;">
                        <td style="text-align:center;">'.$row[4].'</td>
                        <td style="text-align:center;">'.$row[5].'</td>
                        <td style="">'.$row[6].'</td>
                        <td style="text-align:right;">'.number_format(str_replace(",","",$row[7]),2).'</td>
                        <td style="text-align:right;">'.number_format(str_replace(",","",$row[8]),2).'</td>
                    </tr>
            ';

            $totalAmountDue = floatval($totalAmountDue) + floatval(str_replace(",","",$row[8]));
        }

        for ($i = 0; $i < $rowsqty; $i++) {
            $contentdata .= '
                    <tr style="font-size:6pt;">
                        <td style="text-align:center;"></td>
                        <td style="text-align:center;"></td>
                        <td style=""></td>
                        <td style="text-align:right;"></td>
                        <td style="text-align:right;"></td>
                    </tr>
            ';
        }
        
        $content = '';

        $content .= '<table border="0">
                        <tr>
                            <td width="100%" style="line-height:100px;"></td>
                        </tr>
                        <tr>
                            <td width="70%" style="font-size:7px;"><table border="0">
                                    <tr>
                                        <td width="10%" style="font-weight:bold;">SOLD to </td>
                                        <td width="90%">'.$soldto.'</td>
                                    </tr>
                                </table>
                            </td>
                            <td width="30%" style="font-size:7px;"><table border="0">
                                    <tr>
                                        <td width="15%" style="font-weight:bold;">Date </td>
                                        <td width="85%">'.$datesold.'</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-size:7px;"><table border="0">
                                    <tr>
                                        <td width="7%" style="font-weight:bold;">TIN </td>
                                        <td width="93%">'.$tin.'</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-size:7px;"><table border="0">
                                    <tr>
                                        <td width="7%" style="font-weight:bold;">Address </td>
                                        <td width="93%">'.$address.'</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr><td width="100%"></td></tr>
                        <tr><td width="100%"></td></tr>
                        <tr style="font-weight:bold;font-size:6pt;text-align:center;">
                            <td width="10%">QUANTITY</td>
                            <td width="5%">UNIT</td>
                            <td width="65%">ARTICLES</td>
                            <td width="10%">UNIT PRICE</td>
                            <td width="10%">AMOUNT</td>
                        </tr>
                        '.$contentdata.'
                        <tr style="font-size:6pt;">
                            <td width="10%"></td>
                            <td width="5%"></td>
                            <td width="10%">VATable Sales</td>
                            <td width="55%">'.$SalesNoVAT.'</td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                        </tr>
                        <tr style="font-size:6pt;">
                            <td width="10%"></td>
                            <td width="5%"></td>
                            <td width="10%"></td>
                            <td width="55%"></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                        </tr>
                        <tr style="font-size:6pt;">
                            <td width="10%"></td>
                            <td width="5%"></td>
                            <td width="10%"></td>
                            <td width="55%"></td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                        </tr>
                        <tr style="font-size:6pt;">
                            <td width="10%"></td>
                            <td width="5%"></td>
                            <td width="10%">VAT Amount</td>
                            <td width="55%">'.$SalesWithVAT.'</td>
                            <td width="10%"></td>
                            <td width="10%"></td>
                        </tr>
                        <tr style="font-size:6pt;text-align:right;">
                            <td width="90%"></td>
                            <td width="10%">'.number_format($totalAmountDue,2).'</td>
                        </tr>
                    </table>
        ';

        // logs($_SESSION['usertype'], "Printed JV Report", "btnJVPrint", $_SESSION['username'], "JV Reports");        
        
        $pdf->writeHTML($content, true, 0, true, 0);
		$pdf->lastPage();
        $pdf->IncludeJS("print();");
        $pdf->Output('supplierreceipt.pdf', 'I');
    }

    public function PrintTransmittalByNo($transNo){
        ob_clean();
        ob_flush();
        ini_set('memory_limit','-1');
        set_time_limit(0);
        $pdf = new TCPDF('P', PDF_UNIT, 'A5', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('isynergiesinc');
        $pdf->SetTitle('Transmittal Receipt');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(4, 8, 4);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();

        $header = [
            "TransmittalNO" => $transNo,
            "NameTO" => "-",
            "NameFROM" => "-",
            "DatePrepared" => "-",
        ];
        $items = [];
        $totalAmount = 0.0;

        $stmt = $this->conn->prepare("SELECT * FROM tbl_transmittal WHERE TransmittalNO = ?");
        $stmt->bind_param("s", $transNo);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if ($header["NameTO"] === "-" && !empty($row["NameTO"])) $header["NameTO"] = $row["NameTO"];
            if ($header["NameFROM"] === "-" && !empty($row["NameFROM"])) $header["NameFROM"] = $row["NameFROM"];
            if ($header["DatePrepared"] === "-" && !empty($row["DatePrepared"])) $header["DatePrepared"] = $row["DatePrepared"];
            $qty = isset($row["Quantity"]) ? $row["Quantity"] : (isset($row["InOrder"]) ? $row["InOrder"] : 0);
            $article = isset($row["Product"]) ? $row["Product"] : (isset($row["ProductSerialNo"]) ? $row["ProductSerialNo"] : "");
            $amount = isset($row["Amount"]) ? floatval($row["Amount"]) : 0.0;
            $items[] = [
                "Quantity" => $qty,
                "Article" => $article,
                "Amount" => $amount,
            ];
            $totalAmount += $amount;
        }
        $stmt->close();

        $contentRows = '';
        foreach ($items as $it) {
            $contentRows .= '
                <tr style="font-size:6pt;">
                    <td style="text-align:center;">'.$it["Quantity"].'</td>
                    <td style="">'.$it["Article"].'</td>
                    <td style="text-align:right;">'.number_format($it["Amount"],2).'</td>
                </tr>';
        }
        for ($i = count($items); $i < 10; $i++) {
            $contentRows .= '
                <tr style="font-size:6pt;">
                    <td style="text-align:center;"></td>
                    <td style=""></td>
                    <td style="text-align:right;"></td>
                </tr>';
        }

        $content = '
            <table border="0">
                <tr><td width="100%" style="line-height:80px;"></td></tr>
                <tr>
                    <td width="70%" style="font-size:7px;"><table border="0">
                        <tr><td width="20%" style="font-weight:bold;">Transmittal No</td><td width="80%">'.$header["TransmittalNO"].'</td></tr>
                        <tr><td width="20%" style="font-weight:bold;">To</td><td width="80%">'.$header["NameTO"].'</td></tr>
                        <tr><td width="20%" style="font-weight:bold;">From</td><td width="80%">'.$header["NameFROM"].'</td></tr>
                    </table></td>
                    <td width="30%" style="font-size:7px;"><table border="0">
                        <tr><td width="30%" style="font-weight:bold;">Date</td><td width="70%">'.$header["DatePrepared"].'</td></tr>
                    </table></td>
                </tr>
                <tr><td width="100%"></td></tr>
                <tr style="font-weight:bold;font-size:6pt;text-align:center;">
                    <td width="15%">QUANTITY</td>
                    <td width="70%">ARTICLES</td>
                    <td width="15%">AMOUNT</td>
                </tr>
                '.$contentRows.'
                <tr style="font-size:6pt;text-align:right;">
                    <td width="85%"></td>
                    <td width="15%">'.number_format($totalAmount,2).'</td>
                </tr>
            </table>
        ';

        $pdf->writeHTML($content, true, 0, true, 0);
        $pdf->lastPage();
        $pdf->IncludeJS("print();");
        $pdf->Output('transmittal.pdf', 'I');
    }

    // Function to determine the best font size
    private function adjustFontSizeToFitWidth($text, $maxWidth, $initialFontSize, $minFontSize, $characterWidthFactor) {
        $fontSize = $initialFontSize;

        while ($this->calculateTextWidth($text, $fontSize, $characterWidthFactor) > $maxWidth && $fontSize > $minFontSize) {
            $fontSize -= 0.5; // Decrease font size incrementally
        }
        return $fontSize;
    }

    // Function to estimate text width based on character count and font size
    private function calculateTextWidth($text, $fontSize, $characterWidthFactor) {
        return strlen($text) * $fontSize * $characterWidthFactor;
    }

}
?>
