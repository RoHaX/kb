<?php
session_start();
require('fpdf/fpdf.php');


// GET-Parameter abrufen
$beleg = $_GET['beleg'];
$datum = $_GET['datum'];
// Datum umformatieren
$dateObj = DateTime::createFromFormat('Y-m-d', $datum);
$formattedDate = $dateObj->format('d.m.Y');

$bezeichnung = urldecode($_GET['bezeichnung']);
$bezeichnung = iconv('UTF-8', 'windows-1252', $bezeichnung);
$betragtext = '';
$betrag = '';
if (isset($_GET['eingang'])) {
    $betragtext = "Eingang";
    $eingang = $_GET['eingang'];
    $betrag = number_format($eingang, 2, ',', '');
}
if (isset($_GET['ausgang'])) {
    $betragtext = "Ausgang";
    $ausgang = $_GET['ausgang'];
    $betrag = number_format($ausgang, 2, ',', '');
}
$konto = $_GET['konto'];
$konto = iconv('UTF-8', 'windows-1252', $konto);
$kat = $_GET['kat'];
$kat = iconv('UTF-8', 'windows-1252', $kat);

// Neues FPDF-Objekt erstellen
$pdf = new Fpdf();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 10);

// Logo und "Musterfirma" rechts oben
// $logo = 'img/logo' . $_GET['mandant'] . '.png';
// $logo = 'img/logo2.png';
// $pdf->Image($logo, 150, 10, 0, 20);
// $pdf->SetXY(135, 30);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Ln(20);
// $pdf->Cell(0, 10, 'Elternverein Scheffau ', 0, 1, 'C');
// $pdf->Cell(0, 10, 'Beleg, ' . $formattedDate, 0, 1, 'L');

// Aktuelles Datum
// $pdf->SetFont('Arial', 'I', 10);
// $pdf->Cell(0, 10, 'Datum: ' . $datum, 0, 1, 'R');

// Überschrift "Buchungsbeleg"
$pdf->SetFont('Arial', 'B', 16);
$pdf->Ln(20);
$pdf->Cell(0, 10, 'Buchungsbeleg # ' . $beleg, 0, 1, 'C');
// $pdf->Cell(0, 10, '# ' . $beleg, 0, 1, 'C');

// Informationen zentriert
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Datum: ' . $formattedDate, 0, 1, 'C');

$pdf->Ln(10);
// $pdf->Cell(0, 10, 'Bezeichnung: ' . $bezeichnung, 0, 1, 'C');
/*
$pdf->Cell(0, 10, 'Buchungstext:', 0, 1, 'L');
$bezeichnungWrapped = wordwrap($bezeichnung, 60, "\n");
$pdf->SetX(($pdf->GetPageWidth() - $pdf->GetStringWidth('Bezeichnung: ' . $bezeichnungWrapped)) / 2);
$pdf->MultiCell(0, 10, $bezeichnungWrapped, 0, 'L');
*/

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, $betragtext .': ' . $betrag  . ' Euro', 0, 1, 'C');

$pdf->Ln(10);
$pdf->SetFont('Arial', '', 10);

$pdf->SetX(50); // X-Koordinate für die erste Zeile
$pdf->Cell(0, 7, 'Bezeichnung/Verwendungszweck:', 0, 1, 'L');

$pdf->SetFont('Arial', '', 12);
$bezeichnungWrapped = wordwrap($bezeichnung, 60, "\n");
$lines = explode("\n", $bezeichnungWrapped);

foreach ($lines as $line) {
    $pdf->SetX(50);
    $pdf->Cell(0, 6, $line, 0, 1, 'L');
}

$pdf->Ln(10);
$pdf->SetX(50);
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'Konto:');
$pdf->SetX(120);
$pdf->Cell(0, 5, 'Kategorie:');
$pdf->Ln(5);
$pdf->SetX(50);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 5, $konto);
$pdf->SetX(120);
$pdf->Cell(0, 5, $kat);


$pdf->SetLineWidth(0.3);
$pdf->Line(120, 220, 80, 220);
// $pdf->Line(120, 220, 180, 220);
$pdf->SetY(220);
$pdf->SetX(90);
$pdf->Cell(10, 10, 'Kassier');


// PDF ausgeben
$pdf->Output();
?>