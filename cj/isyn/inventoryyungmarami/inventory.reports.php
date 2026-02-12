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
		$pdf->SetMargins(8, 8, 8);
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
        $useUiOrder = isset($_SESSION['use_ui_order']) && $_SESSION['use_ui_order'];
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
                'si' => ['si','sino','suppliersi','supplier si','supplier_si'],
                'serialno' => ['serialno','serialnumber','serialno','serialno'],
                'dateadded' => ['dateadded','dateadded','dateadded','dateadded'],
                'datepurchase' => ['datepurchase','purchasedate','datepurchased'],
                'product' => ['product','productname'],
                'quantity' => ['quantity','qty'],
                'vatsales' => ['vatsales','vatablesales','vat sales','vatable sales'],
                'vat' => ['vat','vats'],
            ];
            foreach ($map as $canon => $alts) {
                if (in_array($n, $alts, true)) return $canon;
            }
            return $n;
        };
        if ($useUiOrder) {
            $cleanHeaders = $rawHeaders;
            $cleanRows = $tableData;
        } else {
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
        }
        if ($useUiOrder) {
            $orderIdx = range(0, count($cleanHeaders)-1);
        } else {
            // Reorder to fixed preferred sequence for print
            $preferredOrder = ['sino','serialno','product','supplier','stock','branch','category','quantity','dateadded','srp','vat','vatsales'];
            $preferredIdx = [];
            $remainingIdx = [];
            for ($i = 0; $i < count($cleanHeaders); $i++) {
                $canon = $alias($cleanHeaders[$i]);
                if (in_array($canon, $preferredOrder, true)) {
                    $preferredIdx[$canon] = $i;
                } else {
                    $remainingIdx[] = $i;
                }
            }
            // Choose SI index by header name priority: SI > SIno > SupplierSI
            $siIdx = null;
            for ($i = 0; $i < count($cleanHeaders); $i++) {
                $hn = strtolower($cleanHeaders[$i]);
                if ($siIdx === null && ($hn === 'si')) { $siIdx = $i; }
            }
            if ($siIdx === null) {
                for ($i = 0; $i < count($cleanHeaders); $i++) {
                    $hn = strtolower($cleanHeaders[$i]);
                    if ($hn === 'sino') { $siIdx = $i; break; }
                }
            }
            if ($siIdx === null) {
                for ($i = 0; $i < count($cleanHeaders); $i++) {
                    $hn = strtolower($cleanHeaders[$i]);
                    if ($hn === 'suppliersi' || $hn === 'supplier si' || $hn === 'supplier_si') { $siIdx = $i; break; }
                }
            }
            $orderedPreferred = [];
            if ($siIdx !== null) { $orderedPreferred[] = $siIdx; }
            foreach (['serialno','product','supplier','stock','branch','category','dateadded','srp','vat','vatsales'] as $p) {
                if (isset($preferredIdx[$p])) { $orderedPreferred[] = $preferredIdx[$p]; }
            }
            $orderIdx = array_merge($orderedPreferred, $remainingIdx);
        }
        $reorderedHeaders = [];
        $reorderedRows = [];
        foreach ($orderIdx as $oi) { $reorderedHeaders[] = $cleanHeaders[$oi]; }
        foreach ($cleanRows as $row) {
            $nr = [];
            foreach ($orderIdx as $oi) { $nr[] = isset($row[$oi]) ? $row[$oi] : ''; }
            $reorderedRows[] = $nr;
        }
        $cleanHeaders = $reorderedHeaders;
        $cleanRows = $reorderedRows;
        // Ensure SI appears only once (skip if using UI order assuming it is already correct)
        if (!$useUiOrder) {
            $indicesToKeep = [];
            $seenSi = false;
            for ($i = 0; $i < count($cleanHeaders); $i++) {
                $canon = $alias($cleanHeaders[$i]);
                if ($canon === 'si') {
                    if ($seenSi) { continue; }
                    $seenSi = true;
                }
                $indicesToKeep[] = $i;
            }
            if (count($indicesToKeep) !== count($cleanHeaders)) {
                $tmpH = []; $tmpR = [];
                foreach ($indicesToKeep as $ix) { $tmpH[] = $cleanHeaders[$ix]; }
                foreach ($cleanRows as $row) {
                    $nr = [];
                    foreach ($indicesToKeep as $ix) { $nr[] = isset($row[$ix]) ? $row[$ix] : ''; }
                    $tmpR[] = $nr;
                }
                $cleanHeaders = $tmpH;
                $cleanRows = $tmpR;
            }
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
                if (strpos($hn,'product') !== false) { $columnWidths[$i] += 8; }
                if (strpos($hn,'supplier') !== false) { $columnWidths[$i] += 6; }
                if (strpos($hn,'sold') !== false) { $columnWidths[$i] += 6; }
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
                <tr style="font-weight:bold;font-size:11pt;text-align:center;line-height:30px;">
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

        $dateCanon = ['dateadded','datepurchase','date','asof'];
        foreach ($cleanRows as $row){
            $contentdata .= '<tr style="font-size:9pt;">';
            for ($i = 0; $i < $colCount; $i++) {
                $value = isset($row[$i]) ? $row[$i] : '';
                $hn = strtolower($cleanHeaders[$i]);
                $canon = $alias($cleanHeaders[$i]);
                // Normalize dates
                if (in_array($canon, $dateCanon, true) || strpos($hn,'date') !== false) {
                    $v = trim((string)$value);
                    if ($v === '0' || $v === '') {
                        $value = '';
                    } else {
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $v)) {
                            $value = date('m/d/Y', strtotime($v));
                        } else {
                            $ts = strtotime($v);
                            $value = $ts ? date('m/d/Y', $ts) : $v;
                        }
                    }
                }
                // Format numerics
                $numRaw = str_replace([',',' '],'', (string)$value);
                $isNumeric = is_numeric($numRaw);
                $isSi = ($canon === 'si');
                if ($isNumeric && !$isSi) {
                    $isMoney = in_array($canon, ['dealerprice','totalprice','srp','totalsrp','vatsales','vat','amountdue','markup','totalmarkup','mpi'], true) ||
                               strpos($hn,'price') !== false ||
                               strpos($hn,'srp') !== false ||
                               strpos($hn,'vat') !== false ||
                               strpos($hn,'amount') !== false ||
                               strpos($hn,'markup') !== false ||
                               strpos($hn,'mpi') !== false;
                    $isQty = (strpos($hn,'quantity') !== false || strpos($hn,'qty') !== false);
                    $valNum = (float)$numRaw;
                    if ($isMoney) {
                        $value = number_format($valNum, 2);
                    } elseif ($isQty) {
                        $value = number_format($valNum, 0);
                    } else {
                        $value = number_format($valNum, 2);
                    }
                }
                $width = isset($columnWidths[$i]) ? $columnWidths[$i] : 5;
                $contentdata .= '<td style="text-align:center;border: 1px solid black;word-wrap:break-word;white-space:normal;" width="'.$width.'%">'.$value.'</td>';
                
                // Accumulate totals
                $numVal = (float)str_replace([',',' '],'', (string)$value);
                $isNumericVal = is_numeric(str_replace([',',' '],'', (string)$value));
                if ($isNumericVal && !in_array($canon, ['si','sino','suppliersi','serialno','dateadded','datepurchase'], true)) {
                    if (!isset($totals[$i])) { $totals[$i] = 0; }
                    $totals[$i] += $numVal;
                }
            }
            $contentdata .= '</tr>';
        }
        
        // Add Totals Row
        $contentdata .= '<tr style="font-size:9pt;font-weight:bold;background-color:#f0f0f0;">';
        $contentdata .= '<td style="border:1px solid black;text-align:center;" width="'.$columnWidths[0].'%">TOTALS</td>';
        for ($i = 1; $i < $colCount; $i++) {
            $width = isset($columnWidths[$i]) ? $columnWidths[$i] : 5;
            $val = '';
            if (isset($totals[$i])) {
                $canon = $alias($cleanHeaders[$i]);
                $hn = strtolower($cleanHeaders[$i]);
                if (strpos($hn,'quantity') !== false || strpos($hn,'qty') !== false) {
                    $val = number_format($totals[$i], 0);
                } else {
                    $val = number_format($totals[$i], 2);
                }
            }
            $contentdata .= '<td style="border:1px solid black;text-align:center;" width="'.$width.'%">'.$val.'</td>';
        }
        $contentdata .= '</tr>';
        
        $content = '';

        $content .= '<table border="0">
                        <tr>
                            <td width="100%" style="font-size:8pt;font-weight:bold;text-align:left;"><p>We make IT possible</p></td>
                        </tr>
                        <tr>
                            <td width="100%" style="font-family:centurynormal;font-size:20pt;font-weight:bold;text-align:left;"><p>iSynergies Inc.</p></td>
                        </tr>
                        <tr>
                            <td width="100%"><p style="font-size:10pt;text-align:left;">'.$orgaddress.'</p></td>
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
