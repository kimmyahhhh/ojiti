<?php
require_once('../../assets/tcpdf/tcpdf.php');

class OrderConfirmationReports extends Database 
{
    private function getOCTable(){ return "tbl_orderconfirmation"; }
    private function getOCColumnNames($table){ return ["oc"=>"OrderNo","name"=>"NameTo","date"=>"DateAdded"]; }
    public function PrintOCByNo($ocNo){
        ob_clean();
        ob_flush();
        ini_set('memory_limit','-1');
        set_time_limit(0);
        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('isynergiesinc');
        $pdf->SetTitle('Order Confirmation');
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->AddPage();

        $header = [
            "OCNo" => $ocNo,
            "NameTO" => "-",
            "NameFROM" => "ISYNERGIESINC",
            "DatePrepared" => "-",
        ];
        $items = [];
        $totalAmount = 0.0;

        $table = $this->getOCTable();
        $cols = $this->getOCColumnNames($table);
        $stmt = $this->conn->prepare("SELECT * FROM `".$table."` WHERE `".$cols["oc"]."` = ?");
        $stmt->bind_param("s", $ocNo);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            if ($header["NameTO"] === "-" && !empty($row[$cols["name"]])) $header["NameTO"] = $row[$cols["name"]];
            if ($header["DatePrepared"] === "-" && !empty($row[$cols["date"]])) $header["DatePrepared"] = $row[$cols["date"]];
            
            $items[] = [
                "Quantity" => $row["Quantity"] ?? 0,
                "Product" => $row["Product"] ?? "",
                "SerialNo" => $row["SerialNo"] ?? "",
                "SRP" => floatval($row["SRP"] ?? 0),
                "Amount" => floatval(($row["Quantity"] ?? 0)) * floatval(($row["SRP"] ?? 0)),
            ];
            $totalAmount += (floatval($row["Quantity"]) * floatval($row["SRP"]));
        }
        $stmt->close();

        // Add centered logo
        $rootPath = dirname(__DIR__, 2);
        $logoPath = $rootPath . '/logo/complete-logo.png';
        if (!file_exists($logoPath)) $logoPath = $rootPath . '/assets/images/complete-logo.png';

        $hasImageLib = extension_loaded('gd') || class_exists('Imagick');
        if ($logoPath && file_exists($logoPath) && $hasImageLib) {
            $logoWidth = 60;
            $logoHeight = 18;
            $logoX = ($pdf->getPageWidth() - $logoWidth) / 2;
            $logoY = 15;
            $pdf->Image($logoPath, $logoX, $logoY, $logoWidth, $logoHeight, '', '', '', false, 300, '', false, false, 0);
            
            $pdf->SetFont('helvetica','',10);
            $pdf->SetXY(0, $logoY + $logoHeight + 2);
            $pdf->Cell($pdf->getPageWidth(), 5, '105 Maharlika Highway, Cabanatuan City', 0, 1, 'C');
            
            $pdf->SetFont('helvetica','B',18);
            $pdf->SetXY(0, $logoY + $logoHeight + 8);
            $pdf->Cell($pdf->getPageWidth(), 7, 'ORDER CONFIRMATION', 0, 1, 'C');
        }

        $pdf->SetY(60);
        $pdf->SetFont('helvetica','',10);
        
        $htmlHeader = '
            <table border="0" cellpadding="4">
                <tr>
                    <td width="15%"><b>TO:</b></td>
                    <td width="45%" style="border-bottom:1px solid #000;">'.strtoupper($header["NameTO"]).'</td>
                    <td width="15%"><b>OC No.:</b></td>
                    <td width="25%" style="border-bottom:1px solid #000;">'.$header["OCNo"].'</td>
                </tr>
                <tr>
                    <td width="15%"><b>FROM:</b></td>
                    <td width="45%" style="border-bottom:1px solid #000;">'.strtoupper($header["NameFROM"]).'</td>
                    <td width="15%"><b>DATE:</b></td>
                    <td width="25%" style="border-bottom:1px solid #000;">'.$header["DatePrepared"].'</td>
                </tr>
            </table>';
        
        $pdf->writeHTML($htmlHeader, true, false, true, false, '');

        $pdf->Ln(5);
        
        $contentRows = '';
        foreach ($items as $it) {
            $desc = $it["Product"];
            if ($it["SerialNo"] && $it["SerialNo"] != "-") $desc .= " (S/N: ".$it["SerialNo"].")";
            
            $contentRows .= '
                <tr>
                    <td width="15%" style="text-align:center;">'.$it["Quantity"].'</td>
                    <td width="55%">'.strtoupper($desc).'</td>
                    <td width="15%" style="text-align:right;">'.number_format($it["SRP"],2).'</td>
                    <td width="15%" style="text-align:right;">'.number_format($it["Amount"],2).'</td>
                </tr>';
        }
        
        // Add empty rows to maintain height
        for ($i = count($items); $i < 12; $i++) {
            $contentRows .= '<tr><td height="20"></td><td></td><td></td><td></td></tr>';
        }

        $htmlTable = '
            <table border="1" cellpadding="5">
                <tr style="background-color:#f2f2f2; font-weight:bold; text-align:center;">
                    <td width="15%">QTY</td>
                    <td width="55%">PARTICULARS</td>
                    <td width="15%">UNIT PRICE</td>
                    <td width="15%">AMOUNT</td>
                </tr>
                '.$contentRows.'
                <tr style="font-weight:bold;">
                    <td colspan="3" style="text-align:right;">TOTAL AMOUNT</td>
                    <td style="text-align:right;">'.number_format($totalAmount,2).'</td>
                </tr>
            </table>';

        $pdf->writeHTML($htmlTable, true, false, true, false, '');

        $pdf->Ln(20);
        
        $htmlFooter = '
            <table border="0">
                <tr>
                    <td width="50%">Confirmed by:</td>
                    <td width="50%" style="text-align:right;">Prepared by:</td>
                </tr>
                <tr>
                    <td height="30"></td>
                    <td></td>
                </tr>
                <tr>
                    <td width="40%" style="border-bottom:1px solid #000;"></td>
                    <td width="20%"></td>
                    <td width="40%" style="border-bottom:1px solid #000; text-align:center;">'.$_SESSION['USERNAME'].'</td>
                </tr>
                <tr>
                    <td style="text-align:center; font-size:8pt;">Customer Signature over Printed Name</td>
                    <td></td>
                    <td style="text-align:center; font-size:8pt;">Authorized Personnel</td>
                </tr>
            </table>';
            
        $pdf->writeHTML($htmlFooter, true, false, true, false, '');

        $pdf->lastPage();
        $pdf->IncludeJS("print()");
        $pdf->Output('order_confirmation_'.$ocNo.'.pdf', 'I');
    }
}
