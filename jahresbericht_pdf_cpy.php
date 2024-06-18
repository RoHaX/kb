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
        $this->SetFont('Arial','',8);
        // Move to the right
        // Title
        $this->Cell(188,6,iconv2('Kassabericht ' . $this->bez),0,0,'R');
        // Line break
        $this->Ln(10);

        if($this->ProcessingTable)
            $this->TableHeader();
    }

    function TableHeader()
    {
        $this->SetFont('Arial','B',12);
        $this->SetX($this->TableX);
        $fill=!empty($this->HeaderColor);
        if($fill)
            $this->SetFillColor($this->HeaderColor[0],$this->HeaderColor[1],$this->HeaderColor[2]);
        foreach($this->aCols as $col)
            $this->Cell($col['w'],6,$col['c'],1,0,'C',$fill);
        $this->Ln();
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','',8);
        // Page number
        $this->Cell(0,10,'Seite '.$this->PageNo().'/{nb}',0,0,'C');
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
            $this->Ln(30);
            $this->Cell(60);
            $this->Cell(50,10,iconv2('Der Ortsstellenleiter:'),0,0,'C');
            $this->Ln(20);
            $this->Cell(60);
            $this->Cell(50,10,iconv2('Der Kassier:'),0,0,'C');
            $this->Ln(20);
            $this->Cell(60);
            $this->Cell(50,10,iconv2('Die Rechnungsprüfer:'),0,0,'C');
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
        $this->SetFillColor(100,100,100);
        $this->SetTextColor(255);
        //$this->SetDrawColor(128,0,0);
        $this->SetDrawColor(200,200,200);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header
        $w = array(10, 20, 85, 22, 22, 30);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(220,220,220);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        $sumein = 0;
        $sumaus = 0;
        foreach($data as $row)
        {
            
            $this->SetFont('','B');
            $this->Cell($w[0],6,$row['beleg'],'LR',0,'R',$fill);
            $this->SetFont('');
            $this->Cell($w[1],6,$row['datum'],'LR',0,'L',$fill);
            $this->Cell($w[2],6,iconv2($row['bezeichnung']),'LR',0,'L',$fill);
            if ($row['eingang']==0) {
                $eingang = "";
            } else {
                $eingang = number_format($row['eingang'], 2, ',', '.');
                $sumein += $row['eingang'];
            }
            $this->Cell($w[3],6,$eingang,'LR',0,'R',$fill);

            if ($row['ausgang']==0) {
                $ausgang = "";
            } else {
                $ausgang = number_format($row['ausgang'], 2, ',', '.');
                $sumaus += $row['ausgang'];
            }
            $this->SetTextColor(194,8,8);
            $this->Cell($w[4],6,$ausgang,'LR',0,'R',$fill);

            $this->SetFont('');
            $this->SetTextColor(0);
            $this->Cell($w[5],6,iconv2($row['kat']),'LR',0,'L',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        $this->SetFont('','B');
        $this->SetFillColor(130,130,130);
        $this->SetTextColor(255);
        //$this->SetDrawColor(128,0,0);
        $this->SetDrawColor(240,240,240);

        $this->Cell($w[0],6,'','TLR',0,'L', true);
        $this->Cell($w[1],6,'','TLR',0,'L', true);
        $this->Cell($w[2],6,'Summe:','TLR',0,'R', true);
        $this->Cell($w[3],6,number_format($sumein, 2, ',', '.'),'TLR',0,'R', true);
        $this->Cell($w[4],6,number_format($sumaus, 2, ',', '.'),'TLR',0,'R', true);
        $this->Cell($w[5],6,'','TLR',0,'L', true);
        $this->Ln();
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
        $this->SetFont('','');
        $this->SetTextColor(0);
        $this->ProcessingTable=false;

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
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(50,10,'Konto: ' . iconv2($row['kontoname']),0,0,'L');
        $pdf->Ln(6);
        $pdf->Cell(50,10,'Saldo start: EUR ' . number_format($row['saldostart'], 2, ',', '.'),0,0,'L');
        $pdf->Cell(50,10,'Saldo aktuell: EUR ' . number_format($row['saldoaktuell'], 2, ',', '.'),0,0,'L');
        $pdf->Ln(10);
        $pdf->SetFont('Arial','',10);
        // Data loading
        $data = $pdf->LoadData($periode, $mandant, $row['kid']);
        $pdf->FancyTable($header,$data);
        $pdf->Ln(1);
        $pdf->Cell(50,4,'Saldo start: EUR ' . number_format($row['saldostart'], 2, ',', '.'),0,0,'L');
        $pdf->Cell(50,4,'Saldo aktuell: EUR ' . number_format($row['saldoaktuell'], 2, ',', '.'),0,0,'L');
    }

}

$pdf->Output();
?>