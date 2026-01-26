<?php
require_once('../../assets/tcpdf/tcpdf.php');

class Reports extends Database 
{
    public function PrintInventoryReport($headerData, $tableData,$isynbranch,$reportType){
        ob_clean();
		ob_flush();

        ini_set('memory_limit','-1');
        set_time_limit(0);
        
		$pdf = new TCPDF('L', PDF_UNIT, 'FOLIO', true, 'UTF-8', false);
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

        $contentheader = '';
        $contentdata = '';

        $SINo = "-";
        $totalUnitPrice = 0;
        $totalAmount = 0;
        $totalVATSales = 0;
        $totalVAT = 0;
        $totalTotal = 0;

        $branchsetup = $this->conn->prepare("SELECT * FROM tbl_configuration WHERE ConfigOwner = 'BRANCH SETUP' AND ConfigName IN ('ORGNAME', 'BRANCHADDRESS', 'BRANCHTELNO','AASIG','BKSIG','BMSIG')");
        $branchsetup->execute();
        $bsresult = $branchsetup->get_result();

        $orgname = '';
        $orgaddress = '';
        $orgtelno = '';

        while ($bsrow = $bsresult->fetch_assoc()) {
            switch ($bsrow['ConfigName']) {
                case 'ORGNAME':
                    $orgname = $bsrow["Value"];
                    break;
                case 'BRANCHADDRESS':
                    $orgaddress = $bsrow["Value"];
                    break;
                case 'BRANCHTELNO':
                    $orgtelno = $bsrow["Value"];
                    break;
            }
        }
        $branchsetup->close();

        // Clean header: remove duplicates and columns with no data
        $rawHeaders = [];
        foreach ($headerData as $h) {
            $rawHeaders[] = is_array($h) ? ($h['ColumnName'] ?? '') : $h->ColumnName;
        }
        $normalize = function($s){
            $s = strtolower(trim($s));
            $s = str_replace([' ', '_', '.'], '', $s);
            return $s;
        };
        $alias = function($s) use ($normalize){
            $n = $normalize($s);
            $map = [
                'si' => ['si','sino'],
                'serialno' => ['serialno','serialnumber','serialno','serialno'],
                'dateadded' => ['dateadded','dateadded','dateadded','dateadded'],
                'datepurchase' => ['datepurchase','purchasedate','datepurchased'],
                'product' => ['product','productname'],
                'vat' => ['vat','vats'],
            ];
            foreach ($map as $canon => $alts) {
                if (in_array($n, $alts, true)) return $canon;
            }
            return $n;
        };
        $keepIdx = [];
        $seenCanon = [];
        for ($i = 0; $i < count($rawHeaders); $i++) {
            $name = $rawHeaders[$i];
            if ($name === '') continue;
            $canon = $alias($name);
            // has data?
            $hasData = false;
            foreach ($tableData as $row) {
                $v = isset($row[$i]) ? $row[$i] : '';
                if ($v !== '' && $v !== null) { $hasData = true; break; }
            }
            if (!$hasData) continue;
            if (isset($seenCanon[$canon])) continue;
            $seenCanon[$canon] = true;
            $keepIdx[] = $i;
        }
        // Build cleaned header and rows
        $cleanHeaders = [];
        foreach ($keepIdx as $idx) { $cleanHeaders[] = $rawHeaders[$idx]; }
        $cleanRows = [];
        foreach ($tableData as $row) {
            $new = [];
            foreach ($keepIdx as $idx) { $new[] = isset($row[$idx]) ? $row[$idx] : ''; }
            $cleanRows[] = $new;
        }
        $colCount = count($cleanHeaders);
        if ($colCount === 0) { $cleanHeaders = []; $cleanRows = []; }
        // Column widths: give Product / Supplier more room when present
        $columnWidths = [];
        if ($colCount > 0) {
            $base = floor(100 / $colCount);
            for ($i=0;$i<$colCount;$i++){ $columnWidths[$i] = $base; }
            for ($i=0;$i<$colCount;$i++){
                $hn = strtolower($cleanHeaders[$i]);
                if (strpos($hn,'product') !== false) { $columnWidths[$i] += 6; }
                if (strpos($hn,'supplier') !== false) { $columnWidths[$i] += 4; }
                if (strpos($hn,'serial') !== false) { $columnWidths[$i] += 2; }
            }
            // normalize widths sum to ~100
            $sum = array_sum($columnWidths);
            if ($sum != 100) {
                for ($i=0;$i<$colCount;$i++){
                    $columnWidths[$i] = round(($columnWidths[$i] / $sum) * 100, 2);
                }
            }
        }


        $contentheader .= '
                <tr style="font-weight:bold;font-size:6pt;text-align:center;line-height:30px;">
            ';

        foreach ($cleanHeaders as $index => $headerName){
            $width = isset($columnWidths[$index]) ? $columnWidths[$index] : 5;
            $contentheader .= '
                    <td width="'.$width.'%" height="30px" style="border: 1px solid black;">'.$headerName.'</td>
            ';
        }

        $contentheader .= '
                </tr>
            ';

        foreach ($cleanRows as $row){
            $contentdata .= '<tr style="font-size:8pt;">';
            for ($i = 0; $i < $colCount; $i++) {
                $value = isset($row[$i]) ? $row[$i] : '';
                $width = isset($columnWidths[$i]) ? $columnWidths[$i] : 5;
                $num = preg_replace('/[,\s]/','',$value);
                $align = is_numeric($num) ? 'right' : 'left';
                $contentdata .= '<td style="text-align:'.$align.';border: 1px solid black;" width="'.$width.'%">'.$value.'</td>';
            }
            $contentdata .= '</tr>';
        }
        
        $content = '';

        $content .= '<table border="0">
                        <tr>
                            <td width="2%"></td>
                            <td width="98%" style="font-size:8  pt;font-weight:bold;text-align:left;"><p>We make IT possible</p></td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-family:centurynormal;font-size:20pt;font-weight:bold;text-align:left;"><p>iSynergies Inc.</p></td>
                        </tr>
                        <tr>
                            <td width="100%"><p style="font-size:8pt;text-align:left;">'.$orgaddress.'</p></td>
                        </tr>
                        <tr><td></td></tr>
                        <tr>
                            <td width="8%" style="font-size:9px;font-weight:bold;text-align:left;"><p>Report Type:</p></td>
                            <td width="15%" style="font-size:9px;font-weight:bold;text-align:left;"><p>'.$reportType.'</p></td>
                            <td width="77%"></td>
                        </tr>
                        <tr><td></td></tr>
                        <tr>
                            <td width="8%" style="font-size:9px;font-weight:bold;text-align:left;"><p>Isynergies Branch:</p></td>
                            <td width="15%" style="font-size:9px;font-weight:bold;text-align:left;"><p>'.$isynbranch.'</p></td>
                            <td width="77%"></td>
                        </tr>
                        <tr><td width="100%"></td></tr>
                        <tr><td width="100%"></td></tr>
                        '.$contentheader.'
                        '.$contentdata.'
                    </table>
        ';

        // logs($_SESSION['usertype'], "Printed JV Report", "btnJVPrint", $_SESSION['username'], "JV Reports");        
        
        $pdf->writeHTML($content, true, 0, true, 0);
		$pdf->lastPage();
        $pdf->IncludeJS("print();");
		$pdf->Output('supplierreceipt.pdf', 'I');
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
