<?php
require_once('../../assets/tcpdf/tcpdf.php');

class MYPDF extends TCPDF {
    public function Header() {
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        $width = $this->getPageWidth();
        $height = $this->getPageHeight();
        $img_file = '../../assets/images/cert_bg.jpg';
        $this->Image($img_file, 0, 0, $width, $height, '', '', '', false, 300, '', false, false, 0);
        // centered logo on top
        $logo = 'C:\\xampp\\htdocs\\isyn-app-v3\\logo\\complete-logo.png';
        if (file_exists($logo)) {
            $lw = 70; // mm bigger logo
            $lx = ($this->getPageWidth() - $lw) / 2;
            $ly = 10; // move logo slightly down
            $ext = strtolower(pathinfo($logo, PATHINFO_EXTENSION));
            $supportsAlpha = extension_loaded('gd') || class_exists('Imagick');
            if ($ext === 'png' && !$supportsAlpha) {
                $logoJpg = 'C:\\xampp\\htdocs\\isyn-app-v3\\logo\\complete-logo.jpg';
                if (file_exists($logoJpg)) {
                    $this->Image($logoJpg, $lx, $ly, $lw, 0, 'JPG', '', '', false, 300, '', false, false, 0);
                }
            } else {
                $this->Image($logo, $lx, $ly, $lw, 0, strtoupper($ext) === 'PNG' ? 'PNG' : '', '', '', false, 300, '', false, false, 0);
            }
        }
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
    }
}

class Reports extends Database 
{
    public function PrintCertificate($shno,$format){
        // $fontname = TCPDF_FONTS::addTTFfont('C:/xampp/htdocs/GitHub/IsynAppV3/assets/tcpdf/fonts/eras-itc-bold.ttf', '', 96);

        if (ob_get_level() > 0) { ob_end_clean(); }
        $prev_err = error_reporting();
        $prev_disp = ini_get('display_errors');
        error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
        ini_set('display_errors', '0');

        $SIGN = $this->GetSHCERTSignatories();
        $sign2Name = "";
        $sign2Desig = "";

        $fullname = "";
        $NoOfShare = "";
        $certNo = "";

        $stmt = $this->conn->prepare("SELECT * FROM tbl_shareholder_info WHERE shareholderNo = ? LIMIT 1");
        $stmt->bind_param("s" ,$shno);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $fullname = $row["fullname"];
            $NoOfShare = $row["noofshare"];
            $certNo = $row["cert_no"];
            $OtherSign = $row["OtherSignatories"];
        }
        $stmt->close();

        $certPrinted = "Yes";
        $stmt = $this->conn->prepare("UPDATE tbl_shareholder_info SET certPrinted = ? WHERE shareholderNo = ?");
        $stmt->bind_param("ss", $certPrinted,$shno);
        $stmt->execute();   
        $stmt->close();

        $nameLenght = strlen($fullname); // name max lenght is 26

        // echo $nameLenght;
        // exit;

        if ($OtherSign == "Yes") {
            $sign2Name = $SIGN["SIGNATORYSUB2NAME"];
            $sign2Desig = $SIGN["SIGNATORYSUB2DESIG"];
        } else {
            $sign2Name = $SIGN["SIGNATORY2NAME"];
            $sign2Desig = $SIGN["SIGNATORY2DESIG"];
        }
        
        $pdf = new MYPDF('L', PDF_UNIT, 'LETTER', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('SHAREHOLDER CERTIFICATE');
        $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
        $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
        $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
        $pdf->SetDefaultMonospacedFont('helvetica');       
        $pdf->SetMargins('14', '16', '13');        
        $pdf->SetAutoPageBreak(TRUE, 10);
        $pdf->AddPage();


      

        $content = 
                    '
                        <table border="0" style="width:100%; text-align:center; font-size:14px;">
                            <tr>
                                <td width="100%" style="line-height:70px;"></td>
                            </tr>
                            <tr>
                                <td width="4%"></td>
                                <td width="21%" style="font-size:13px;font-family: times;">NUMBER: <span style="font-family: times; font-size:13px; font-weight:normal; border-bottom: solid black 1px;">'.$certNo.'</span></td>
                                <td width="50%" style="text-align:center;"></td>
                                <td width="25%" style="font-size:13px;font-family: times;">SHARES: <span style="font-family: times; font-size:13px; font-weight:normal; border-bottom: solid black 1px;">'.$NoOfShare.'</span></td>
                            </tr>
                            ';

                            if ($format == "10M") {
                            $content .= '<tr>
                                            <td width="5%"></td>
                                            <td width="90%" style="text-align:center;font-family: mtcorsva; font-size:18px;">Authorized Capital Stock of Ten Million Pesos (10,000,000.00)</td>
                                            <td width="5%"></td>
                                        </tr>
                                        ';
                            } elseif ($format == "4M") {
                            $content .= '<tr>
                                            <td width="15%"></td>
                                            <td width="70%" style="text-align:center;font-family: mtcorsva; font-size:18px;">Authorized Capital Stock of Four Million Pesos (4,000,000.00)</td>
                                            <td width="15%"></td>
                                        </tr>
                                        ';

                            }

                $content .= '<tr><td></td></tr>';
                            
                            if ($nameLenght < 27) {
                                $content .= '
                                                <tr>
                                                    <td width="2%"></td>
                                                    <td width="23%" style="font-family: mtcorsva; font-size:18px; font-style: italic;">This Certifies that</td>
                                                    <td width="43%" style="border-bottom: solid black 1px;text-align:center;"><span style="font-family: times; font-size:20px; font-style: italic;">'.$fullname.'</span></td>
                                                    <td width="15%" style="text-align:center;font-family: mtcorsva; font-size:18px;">is the owner of</td>
                                                    <td width="5%" style="border-bottom: solid black 1px;text-align:center;"><span style="font-family: times; font-size:20px; font-weight:bold;">'.$NoOfShare.'</span></td>
                                                    <td width="12%"></td>
                                                </tr>
                                                <tr>
                                                    <td width="2%"></td>
                                                    <td width="96%" style="text-align:justify;font-family: mtcorsva; font-size:18px;">Shares of the Capital Stock of</td>
                                                    <td width="2%"></td>    
                                                </tr>
                                            '; 
                            } else {

                                $maxWidthMm = 280;
                                $initialFontSizePt = 18;
                                $minFontSizePt = 8;
                                $characterWidthFactor = 0.4;

                                $adjustedFontSize = $this->adjustFontSizeToFitWidth($fullname, $maxWidthMm, $initialFontSizePt, $minFontSizePt, $characterWidthFactor);

                                $content .= '
                                                <tr>
                                                    <td width="2%"></td>
                                                    <td width="23%" style="font-family: mtcorsva; font-size:18px; font-style: italic;">This Certifies that</td>
                                                    <td width="73%" style="border-bottom: solid black 1px;text-align:center;font-family: times; font-size:15px'.$adjustedFontSize.'px; font-style: italic;">'.$fullname.'</td>
                                                    <td width="2%"></td>
                                                </tr>
                                                <tr>
                                                    <td width="2%"></td>
                                                    <td width="18%" style="text-align:center;font-family: mtcorsva; font-size:18px;">is the owner of</td>
                                                    <td width="10%" style="border-bottom: solid black 1px;text-align:center;"><span style="font-family: times; font-size:15px; font-weight:normal;">'.$NoOfShare.'</span></td>
                                                    <td width="68%" style="text-align:justify;font-family: mtcorsva; font-size:18px;">Shares of the Capital Stock</td>
                                                    <td width="2%"></td>
                                                </tr>
                                            '; 
                            }

                $content .= '<tr><td width="100%"></td></tr>
                            <tr>
                                <td width="29%"></td>
                                <td width="46%" style="text-align:center;font-family:oldengl; font-size:25px;">iSynergies Inc.,</td>
                                <td width="25%"></td>
                            </tr>
                            <tr><td width="100%"></td></tr>
                            <tr>
                                <td width="15%"></td>
                                <td width="70%" style="text-align:center;font-family: mtcorsva;font-size:17px;">Transferable only on the books of the Corporation by the holder hereof in person or by Attorney, upon surrender of this Certificate properly endorsed.</td>
                                <td width="15%"></td>
                            </tr>
                            
                            <tr><td width="100%" style="line-height:24px;"></td></tr>
                            <tr>
                                <td width="15%"></td>
                                <td width="70%" style="text-align:center;"><span style="font-family: oldengl;">In Witness Whereof,</span> <span style="font-family: mtcorsva;font-size:17px;">the said Corporation has caused this Certificate  to be signed by its duly authorized officers and to be sealed with the Seal of the Corporation this (Day) day of (Month) A.D (Year).</span></td>
                                <td width="15%"></td>
                            </tr>
                            <tr><td width="100%" style="line-height:42px;"></td></tr>
                            <tr>
                                <td width="20%"></td>
                                <td width="30%" style="text-align:center;font-family: mtcorsva;font-size:20px;border-bottom: solid black 1px;">'.$SIGN["SIGNATORY1NAME"].'</td>
                                <td width="30%" style="text-align:center;font-family: mtcorsva;font-size:20px;border-bottom: solid black 1px;">'.$sign2Name.'</td>
                                <td width="20%"></td>
                            </tr>
                            <tr><td width="100%" style="line-height:10px;"></td></tr>
                            <tr>
                                <td width="20%"></td>
                                <td width="30%" style="text-align:center;font-family: mtcorsva;font-size:16px;">'.$SIGN["SIGNATORY1DESIG"].'</td>
                                <td width="30%" style="text-align:center;font-family: mtcorsva;font-size:16px;">'.$sign2Desig.'</td>
                                <td width="20%"></td>
                            </tr>
                            <tr><td width="100%"></td></tr>
                            ';
                $content .= '<tr>
                                <td width="25%" style="line-height:70px;"></td>
                                <td width="50%" style="text-align:center;font-family: times;font-size:16px; font-style: italic;">Shares 100 Each.</td>
                                <td width="25%"></td>
                            </tr>
                        </table>
                    ';

        $pdf->setCellPadding(0);
        $pdf->writeHTML($content, true, 0, true, 0);
		$pdf->lastPage();
		$pdf->Output('shareholder_certificate.pdf', 'I');
        ini_set('display_errors', $prev_disp);
        error_reporting($prev_err);
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
