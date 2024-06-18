<?php
session_start();
require('fpdf/fpdf.php');
include_once 'config/db.php';

$periode = $_SESSION['periode'];
$mandant = $_SESSION['mandant'];

class PDF extends FPDF
{
// Load data
function LoadData($periode, $mandant)
{
    global $db;


    // Read file lines
    /*
    $lines = file($file);
    $data = array();
    foreach($lines as $line)
        $data[] = explode(';',trim($line));
    return $data;
    */
    $sqlQuery =   "SELECT beleg, datum, bezeichnung, eingang, ausgang, kontoname as konto, katbez_kb as kat, projekt_kb as projekt, periode, color, mandant, id
    FROM tblKassa 
    LEFT JOIN tblKonten on tblKassa.konto = tblKonten.kid AND tblKassa.mandant = tblKonten.kmandant AND tblKassa.periode = tblKonten.kperiode
    LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant 
    LEFT JOIN tblProjekt on tblKassa.projekt = tblProjekt.pid AND tblKassa.mandant = tblProjekt.pmandant ";

    $sqlQuery .= 'WHERE mandant = :mandant AND periode = :periode ';
    $sqlQuery .= 'ORDER BY beleg ASC ';
	
    
	$stmt = $db->prepare($sqlQuery);
	$stmt->bindParam(':mandant', $mandant);
    $stmt->bindParam(':periode', $periode);
    $stmt->execute();
    $result = $stmt->fetchAll();



    return $result;

}

// Colored table
function FancyTable($header, $data)
{
    // Colors, line width and bold font
    $this->SetFillColor(100,100,100);
    $this->SetTextColor(255);
    //$this->SetDrawColor(128,0,0);
    $this->SetDrawColor(200,200,200);
    $this->SetLineWidth(.3);
    $this->SetFont('','B');
    // Header
    $w = array(10, 20, 85, 20, 19, 35);
    for($i=0;$i<count($header);$i++)
        $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
    $this->Ln();
    // Color and font restoration
    $this->SetFillColor(220,220,220);
    $this->SetTextColor(0);
    $this->SetFont('');
    // Data
    $fill = false;
    foreach($data as $row)
    {
        
        $this->SetFont('','B');
        $this->Cell($w[0],6,$row['beleg'],'LR',0,'R',$fill);
        $this->SetFont('');
        $this->Cell($w[1],6,$row['datum'],'LR',0,'L',$fill);
        $this->Cell($w[2],6,iconv('UTF-8', 'windows-1252', $row['bezeichnung']),'LR',0,'L',$fill);
        if ($row['eingang']==0) {
            $this->SetTextColor(194,8,8);
            $betrag = "- ".number_format($row['ausgang'], 2, ',', '.');
        } else {
            $this->SetTextColor(0);
            $betrag = number_format($row['eingang'], 2, ',', '.');
        }
        $this->SetFont('','B');
        $this->Cell($w[3],6,$betrag,'LR',0,'R',$fill);
        $this->SetFont('');
        $this->SetTextColor(0);
        $this->Cell($w[4],6,iconv('UTF-8', 'windows-1252', $row['kat']),'LR',0,'L',$fill);
        $this->Cell($w[5],6,iconv('UTF-8', 'windows-1252', $row['konto']),'LR',0,'L',$fill);
        $this->Ln();
        $fill = !$fill;
    }
    // Closing line
    $this->Cell(array_sum($w),0,'','T');
}
}

$pdf = new PDF();
// Column headings
$header = array('#', 'Datum', 'Bezeichnung', 'Betrag', 'KST', 'Konto');
// Data loading
$data = $pdf->LoadData($periode, $mandant);
$pdf->SetFont('Arial','',10);
$pdf->AddPage();
$pdf->FancyTable($header,$data);
$pdf->Output();
?>