<?php
session_start();
require('fpdf/fpdf.php');
include_once 'config/db.php';

$periode = $_SESSION['periode'];
$mandant = $_SESSION['mandant'];

function iconv2($text) {
    return iconv('utf-8', 'cp1252', $text);
}
class PDF extends FPDF {

    private $db;
    private $bez;
    private $mandant;
    private $header;
    protected $ProcessingTable=false;

    function init_data($mandant) {
        global $db;
        $this->db = $db;
        $this->mandant = $mandant;

        $sqlQuery =   "SELECT * FROM tblAccount WHERE mandant = :mandant";
        
        $stmt = $db->prepare($sqlQuery);
        $stmt->bindParam(':mandant', $this->mandant);
        $stmt->execute();
        $account = $stmt->fetch();
        $this->bez = $account['bez'];
    }

    function Header() {
        // $this->SetFont('Arial','',8);
        // // Move to the right
        // // Title
        // $this->Cell(188,6,iconv2('Kassabericht ' . $this->bez),0,0,'R');
        // // Line break
        // $this->Ln(10);

        if($this->ProcessingTable)
            $this->TableHeader();
    }

    function TableHeader()
    {
        $this->SetFont('Arial','B',10);
        // $this->SetX($this->TableX);
        // $fill=!empty($this->HeaderColor);
        // if($fill)
        //     $this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
        // foreach($this->aCols as $col)
            // $this->Cell($col['w'],6,$col['c'],1,0,'C',$fill);
        // $this->SetFillColor(100,100,100);
        $this->SetTextColor(0);
        $this->SetDrawColor(20,20,20);
        $this->SetLineWidth(.2);
        $this->SetFont('','B');

        $w = array(10, 20, 85, 22, 22, 30);
        /*
        for($i=0;$i<count($this->header);$i++)
            $this->Cell($w[$i],7,$this->header[$i],'B',0,'C');
        */
        $this->Cell($w[0],7,$this->header[0], 'B', 0, 'R');
        $this->Cell($w[1],7,$this->header[1], 'B', 0, 'L');
        $this->Cell($w[2],7,$this->header[2], 'B', 0, 'L');
        $this->Cell($w[3],7,$this->header[3], 'B', 0, 'R');
        $this->Cell($w[4],7,$this->header[4], 'B', 0, 'R');
        $this->Cell($w[5],7,$this->header[5], 'B', 0, 'L');
        $this->Ln();
        // $this->Cell(array_sum($w),0,'','B');
            
     
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-14);
        // Arial italic 8
        $this->SetFont('Arial','',8);
        // Page number
        $this->Cell(80,10,iconv2('Kassabericht ' . $this->bez),0,0,'L');
        $this->Cell(50,10,'Seite '.$this->PageNo().'/{nb}',0,0,'C');

        $datum = date("d.m.Y");
        $this->Cell(60,10,$datum,0,0,'R');
    }

    function Deckblatt() {
        if ($this->mandant == 6 || $this->mandant == 2 ) {
            // Logo
            $this->Image('img/logo' . $this->mandant . '.png',10,6,30);
            // Arial bold 15
            $this->SetFont('Arial','B',15);
            // Move to the right
            $this->Cell(80);
            // Title
            $this->Ln(30);
            $this->Cell(60);
            $this->Cell(50,10,iconv2('Jahresrechnung 2022'),0,0,'C');
            $this->Ln(20);
            $this->Cell(60);
            $this->Cell(50,10,iconv2('Kamaradschaftskasse des Österr. Bergrettungsdienstes'),0,0,'C');
            $this->Ln(10);
            $this->Cell(60);
            $this->Cell(50,10,iconv2('ORTSSTELLE  SCHEFFAU - SÖLLANDL'),0,0,'C');
            $this->Ln(10);
            $this->SetFont('Arial','B',12);
            $this->Cell(60);
            $this->Cell(50,10,iconv2('Von der Vollversammlung am 30.11.2022 genehmigt.'),0,0,'C');
            $this->Ln(40);
            $this->Cell(20);
            $this->Cell(50,10,iconv2('Der Ortsstellenleiter:'),0,0,'L');
            $this->Ln(40);
            $this->Cell(20);
            $this->Cell(50,10,iconv2('Der Kassier:'),0,0,'L');
            $this->Ln(40);
            $this->Cell(20);
            $this->Cell(50,10,iconv2('Die Rechnungsprüfer:'),0,0,'L');
            $this->AddPage();

        } else {
            $this->SetFont('Arial','B',15);
            // Move to the right
            $this->Cell(80);
            // Title
            $this->Cell(50,10,iconv2('Kassabericht ' . $this->bez),0,0,'C');
            // Line break
            $this->Ln(30);
        }
    }

    // Load data
    function LoadData($periode, $mandant, $konto) {
        global $db;

        $sqlQuery =   "SELECT beleg, datum, bezeichnung, eingang, ausgang, kontoname as konto, katbez_kb as kat, projekt_kb as projekt, periode, color, mandant, id, kid
        FROM tblKassa 
        LEFT JOIN tblKonten on tblKassa.konto = tblKonten.kid AND tblKassa.mandant = tblKonten.kmandant AND tblKassa.periode = tblKonten.kperiode
        LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant 
        LEFT JOIN tblProjekt on tblKassa.projekt = tblProjekt.pid AND tblKassa.mandant = tblProjekt.pmandant ";

        $sqlQuery .= 'WHERE mandant = :mandant AND periode = :periode AND kid = :kid ';
        $sqlQuery .= 'ORDER BY beleg ASC ';
        
        
        $stmt = $db->prepare($sqlQuery);
        $stmt->bindParam(':mandant', $mandant);
        $stmt->bindParam(':periode', $periode);
        $stmt->bindParam(':kid', $konto);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;

    }

    // Colored table
    function FancyTable($header, $data) {
        // Colors, line width and bold font
        $this->ProcessingTable=true;
        $this->header = $header;

        $w = array(10, 20, 85, 22, 22, 30);
        $this->TableHeader();
/*
        // Header
        
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
*/        
        // Color and font restoration
        $this->SetFillColor(230,230,230);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        $sumein = 0;
        $sumaus = 0;
        foreach($data as $row)
        {
            
            $this->SetFont('','B');
            $this->Cell($w[0],6,$row['beleg'],'',0,'R',$fill);
            $this->SetFont('');
            $this->Cell($w[1],6,$row['datum'],'',0,'L',$fill);
            $this->Cell($w[2],6,iconv2($row['bezeichnung']),'',0,'L',$fill);
            if ($row['eingang']==0) {
                $eingang = "";
            } else {
                $eingang = number_format($row['eingang'], 2, ',', '.');
                $sumein += $row['eingang'];
            }
            $this->SetTextColor(0,75,0);
            $this->Cell($w[3],6,$eingang,'',0,'R',$fill);

            if ($row['ausgang']==0) {
                $ausgang = "";
            } else {
                $ausgang = number_format($row['ausgang'], 2, ',', '.');
                $sumaus += $row['ausgang'];
            }
            $this->SetTextColor(120,0,0);
            $this->Cell($w[4],6,$ausgang,'',0,'R',$fill);

            $this->SetFont('');
            $this->SetTextColor(0);
            $this->Cell($w[5],6,iconv2($row['kat']),'',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        $this->SetFont('','B');
        // $this->SetFillColor(130,130,130);
        $this->SetTextColor(0);
        //$this->SetDrawColor(128,0,0);
        $this->SetDrawColor(20,20,20);

        $this->Cell($w[0],6,'','T',0,'L');
        $this->Cell($w[1],6,'','T',0,'L');
        $this->Cell($w[2],6,'Summe/Bewegung EUR:','T',0,'R');
        $this->SetTextColor(0,75,0);
        $this->Cell($w[3],6,number_format($sumein, 2, ',', '.'),'T',0,'R');
        $this->SetTextColor(120,0,0);
        $this->Cell($w[4],6,number_format($sumaus, 2, ',', '.'),'T',0,'R');
        
        $bewegung = $sumein - $sumaus;
        if ($bewegung >= 0 ) 
            $this->SetTextColor(0,75,0);
        
        $this->Cell($w[5],6,number_format($bewegung, 2, ',', '.'),'T',0,'R');
        
        $this->Ln();
        // Closing line
        // $this->Cell(array_sum($w),0,'','T');
        $this->SetFont('','');
        $this->SetTextColor(0);
        $this->ProcessingTable=false;

    }

function RoundedRect($x, $y, $w, $h, $r, $corners = '1234', $style = '')
    {
        $k = $this->k;
        $hp = $this->h;
        if($style=='F')
            $op='f';
        elseif($style=='FD' || $style=='DF')
            $op='B';
        else
            $op='S';
        $MyArc = 4/3 * (sqrt(2) - 1);
        $this->_out(sprintf('%.2F %.2F m',($x+$r)*$k,($hp-$y)*$k ));

        $xc = $x+$w-$r;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l', $xc*$k,($hp-$y)*$k ));
        if (strpos($corners, '2')===false)
            $this->_out(sprintf('%.2F %.2F l', ($x+$w)*$k,($hp-$y)*$k ));
        else
            $this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);

        $xc = $x+$w-$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-$yc)*$k));
        if (strpos($corners, '3')===false)
            $this->_out(sprintf('%.2F %.2F l',($x+$w)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);

        $xc = $x+$r;
        $yc = $y+$h-$r;
        $this->_out(sprintf('%.2F %.2F l',$xc*$k,($hp-($y+$h))*$k));
        if (strpos($corners, '4')===false)
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-($y+$h))*$k));
        else
            $this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);

        $xc = $x+$r ;
        $yc = $y+$r;
        $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$yc)*$k ));
        if (strpos($corners, '1')===false)
        {
            $this->_out(sprintf('%.2F %.2F l',($x)*$k,($hp-$y)*$k ));
            $this->_out(sprintf('%.2F %.2F l',($x+$r)*$k,($hp-$y)*$k ));
        }
        else
            $this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
        $this->_out($op);
    }

    function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
    {
        $h = $this->h;
        $this->_out(sprintf('%.2F %.2F %.2F %.2F %.2F %.2F c ', $x1*$this->k, ($h-$y1)*$this->k,
            $x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
    }    
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->init_data($mandant);
// Column headings
$header = array('#', 'Datum', 'Bezeichnung', 'Eingang', 'Ausgang', 'KST');
$pdf->AddPage();
$pdf->Deckblatt();
$pdf->SetFont('Arial','',10);

$sqlQuery =  "SELECT * FROM tblKonten ";
$sqlQuery .= "WHERE kmandant = ".$mandant." AND kperiode = ".$periode." ";
$sqlQuery .= "ORDER BY kid ASC ";

$stmt = $db->prepare($sqlQuery);
$stmt->bindParam(':mandant', $mandant);
$stmt->bindParam(':periode', $periode);
$stmt->bindParam(':kid', $konto);
$stmt->execute();
$result = $stmt->fetchAll();

foreach($result as $row) {
    if ($row['saldostart'] > 0 AND $row['saldoaktuell'] > 0 ) {
        $pdf->Ln(8);
        $pdf->SetFillColor(230);
        $pdf->SetDrawColor(160);
        $pdf->SetLineWidth(.1);
        $pdf->RoundedRect($pdf->getX(), $pdf->getY(), 190, 22, 3, '12', 'DF');
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(10,10,'',0,0,'L');
        $pdf->Cell(70,10,'Konto: ' . iconv2($row['kontoname']),0,0,'L');
        $pdf->SetFont('Arial','B',10);

        $pdf->Cell(70,10,'Saldo start:',0,0,'R');
        $pdf->Cell(25,10, number_format($row['saldostart'], 2, ',', '.'),0,0,'R');
        $pdf->Cell(10,10,'EUR',0,0,'R');

        $pdf->Ln(6);
        $pdf->Cell(80,10,'',0,0,'L');
        $pdf->Cell(70,10,'Saldo aktuell:',0,0,'R');
        $pdf->Cell(25,10, number_format($row['saldoaktuell'], 2, ',', '.'),0,0,'R');
        $pdf->Cell(10,10,'EUR',0,0,'R');

        $bewegung = $row['saldoaktuell'] - $row['saldostart'];    
        $pdf->Ln(6);
        $pdf->Cell(80,10,'',0,0,'L');
        $pdf->Cell(70,10,'Bewegung:',0,0,'R');
        
        if ($bewegung > 0 )  {
            $pdf->SetTextColor(0,75,0);
        } else {
            $pdf->SetTextColor(120,0,0);
        }
        
        $pdf->Cell(25,10, number_format($bewegung, 2, ',', '.'),0,0,'R');
        $pdf->Cell(10,10,'EUR',0,0,'R');

        
        /*
        $pdf->Ln(6);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(10,10,'',0,0,'L');
        $pdf->Cell(50,10,'Saldo start: EUR ' . number_format($row['saldostart'], 2, ',', '.'),0,0,'L');
        $pdf->Cell(50,10,'Saldo aktuell: EUR ' . number_format($row['saldoaktuell'], 2, ',', '.'),0,0,'L');
        */
        $pdf->Ln(12);
        $pdf->SetFont('Arial','',10);
        // Data loading
        $data = $pdf->LoadData($periode, $mandant, $row['kid']);
        $pdf->FancyTable($header,$data);
        $pdf->Ln(1);
    }

}

$pdf->Output();
?>