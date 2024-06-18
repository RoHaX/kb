<?php
session_start();
require('fpdf/fpdf.php');
include_once 'config/db.php';

$periode = $_SESSION['periode'];
$mandant = $_SESSION['mandant'];

function iconv2($text) {
    // return iconv('utf-8', 'cp1252', $text);
    return mb_convert_encoding($text, 'cp1252', 'utf-8');
}
class PDF extends FPDF {

    private $db;
    private $bez;
    private $mandant;
    private $periode;
    private $periode_bezeichnung;
    private $periode_von;
    private $periode_bis;
    private $header;
    protected $ProcessingTable=false;

    function init_data($mandant, $periode) {
        global $db;
        $this->db = $db;
        $this->mandant = $mandant;
        $this->periode = $periode;

        $sqlQuery =   "SELECT * FROM tblAccount WHERE mandant = :mandant";
        
        $stmt = $this->db->prepare($sqlQuery);
        $stmt->bindParam(':mandant', $this->mandant);
        $stmt->execute();
        $account = $stmt->fetch();
        $this->bez = $account['bez'];

        $sqlQuery =  "SELECT pebezeichnung, vondat, bisdat 
                        FROM tblPeriode 
                        WHERE pemandant = :mandant
                        AND peid = :periode";

        $stmt = $this->db->prepare($sqlQuery);
        $stmt->bindParam(':mandant', $this->mandant);
        $stmt->bindParam(':periode', $this->periode);
        $stmt->execute();
        $periode = $stmt->fetch();

        $this->periode_bezeichnung = $periode['pebezeichnung'];
        $this->periode_von = $periode['vondat'];
        $this->periode_bis = $periode['bisdat'];
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
        $this->SetTextColor(0);
        $this->SetDrawColor(20,20,20);
        $this->SetLineWidth(.2);
        $this->SetFont('','B');

        $w = array(10, 20, 85, 22, 22, 30);

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
        $this->Cell(80,10,iconv2($this->bez . ' - ' . $this->periode_bezeichnung),0,0,'L');
        $this->Cell(50,10,'Seite '.$this->PageNo().'/{nb}',0,0,'C');

        $datum = date("d.m.Y");
        $this->Cell(60,10,$datum,0,0,'R');
    }

    function Deckblatt() {
        $logo = 'img/logo' . $this->mandant . '.png';
        
        if (file_exists($logo)) {
            // Logo
            $this->Image($logo,10,6,50);
        }

        if ($this->mandant == 2  ) {
            // Move to the right
            $this->Cell(80);
            // Title
            $this->Ln(30);
            $this->Cell(65);
            $this->SetFont('Arial','B',22);
            $this->Cell(60,22,iconv2('Kassabericht'),0,0,'C');
            $this->Ln(18);
            $this->Cell(65);
            $this->SetFont('Arial','B',14);
            $this->Cell(50,14,iconv2($this->periode_bezeichnung),0,0,'C');
            $this->Ln(10);
            $this->Cell(65);
            $this->SetFont('Arial','',10);
            $this->Cell(60,10,'Buchungszeitraum: ' . $this->periode_von . ' bis ' . $this->periode_bis,0,0,'C');
            $this->Ln(20);
            $this->SetFont('Arial','B',14);
            $this->Cell(65);
            $this->Cell(60,10,iconv2('Bundesmusikkapelle Scheffau am Wilden Kaiser'),0,0,'C');
            $this->Ln(40);

        } elseif ($this->mandant == 6 || $this->mandant == 3 ) {

            // Logo
            // $this->Image('img/logo' . $this->mandant . '.png',10,6,30);
            // Move to the right
            $this->Cell(80);
            // Title
            $this->Ln(30);
            $this->Cell(65);
            $this->SetFont('Arial','B',22);
            $this->Cell(60,22,iconv2('Jahresrechnung'),0,0,'C');
            $this->Ln(18);
            $this->Cell(65);
            $this->SetFont('Arial','B',14);
            $this->Cell(50,14,iconv2($this->periode_bezeichnung),0,0,'C');
            $this->Ln(10);
            $this->Cell(65);
            $this->SetFont('Arial','',10);
            $this->Cell(60,10,'Buchungszeitraum: ' . $this->periode_von . ' bis ' . $this->periode_bis,0,0,'C');
            $this->Ln(20);
            $this->SetFont('Arial','B',14);
            $this->Cell(65);
            $this->Cell(60,10,iconv2('Kamaradschaftskasse des Österr. Bergrettungsdienstes'),0,0,'C');
            $this->Ln(10);
            $this->Cell(65);
            $this->Cell(60,10,iconv2('ORTSSTELLE SCHEFFAU - SÖLLANDL'),0,0,'C');
            $this->Ln(40);
            $this->SetFont('Arial','B',11);
            $this->Cell(20);
            $this->Cell(50,10,iconv2('Von der Vollversammlung am'),0,0,'R');
            $this->Cell(60,10,'','B',0,'C');
            $this->Cell(30,10,'genehmigt.',0,0,'C');
            $this->Ln(30);
            $this->SetFont('Arial','',11);
            $this->Cell(20);
            $this->Cell(50,10,iconv2('Der Ortsstellenleiter:'),0,0,'R');
            $this->Cell(80,10,'','B',0,'C');
            $this->Ln(30);
            $this->Cell(20);
            $this->Cell(50,10,iconv2('Der Kassier:'),0,0,'R');
            $this->Cell(80,10,'','B',0,'C');
            $this->Ln(30);
            $this->Cell(20);
            $this->Cell(50,10,iconv2('Die Rechnungsprüfer:'),0,0,'R');
            $this->Cell(80,10,'','B',0,'C');
            $this->AddPage();

        } else {
            $this->SetFont('Arial','B',15);
            // Move to the right

            $this->Cell(65);
            $this->SetFont('Arial','B',22);
            $this->Cell(60,22,iconv2('Kassabericht'),0,0,'C');
            $this->Ln(18);
            $this->Cell(65);
            $this->SetFont('Arial','B',14);
            $this->Cell(50,14,iconv2($this->periode_bezeichnung),0,0,'C');
            $this->Ln(10);
            $this->Cell(65);
            $this->SetFont('Arial','',10);
            $this->Cell(60,10,'Buchungszeitraum: ' . $this->periode_von . ' bis ' . $this->periode_bis,0,0,'C');
            // Line break
            $this->Ln(30);
        }
    }

    // Load data
    function LoadData($konto) {
        global $db;

        $sqlQuery =   "SELECT beleg, datum, bezeichnung, eingang, ausgang, kontoname as konto, katbez_kb as kat, projekt_kb as projekt, periode, color, mandant, id, kid
        FROM tblKassa 
        LEFT JOIN tblKonten on tblKassa.konto = tblKonten.kid AND tblKassa.mandant = tblKonten.kmandant AND tblKassa.periode = tblKonten.kperiode
        LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant 
        LEFT JOIN tblProjekt on tblKassa.projekt = tblProjekt.pid AND tblKassa.mandant = tblProjekt.pmandant ";

        $sqlQuery .= 'WHERE mandant = :mandant AND periode = :periode AND kid = :kid ';
        $sqlQuery .= 'ORDER BY beleg ASC ';
        
        
        $stmt = $db->prepare($sqlQuery);
        $stmt->bindParam(':mandant', $this->mandant);
        $stmt->bindParam(':periode', $this->periode);
        $stmt->bindParam(':kid', $konto);
        $stmt->execute();
        $result = $stmt->fetchAll();

        return $result;

    }

    function ein_kst() {
		$sqlQuery = "SELECT katbez_kb, katbez, SUM(eingang) as sumein, katart, color FROM tblKassa 
					 LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant
					 WHERE mandant = :mandant AND periode = :periode  
					 GROUP BY katbez, katart, color 
                     HAVING katart != 3 AND sumein != '' ";

        $stmt = $this->db->prepare($sqlQuery);
        $stmt->bindParam(':mandant', $this->mandant);
        $stmt->bindParam(':periode', $this->periode);
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        $this->SetFillColor(230,230,230);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        
        $header = array('KST', 'Bezeichnung', 'Eingang', '');
        $w = array(30, 100, 30, 10); 

        $this->SetFont('','B','14');
        $this->Cell(150,7,'Einnahmen','',0,'L');
        
        $this->Ln(9);
        
        $this->SetFont('','B','10');
        $this->Cell($w[0],6,$header[0], 'B', 0, 'L');
        $this->Cell($w[1],6,$header[1], 'B', 0, 'L');
        $this->Cell($w[2],6,$header[2], 'B', 0, 'R');
        $this->Cell($w[3],6,$header[3], 'B', 0, 'L');
        $this->Ln(6.2);
        $sumein = 0;
        foreach($result as $row)
        {
            
            $fill = !$fill;
            $this->SetFont('','B');
            $this->Cell($w[0],6,iconv2($row['katbez_kb']),'',0,'L',$fill);
            $this->SetFont('');
            $this->Cell($w[1],6,iconv2($row['katbez']),'',0,'L',$fill);
            $sumein += $row['sumein'];
            $this->SetTextColor(0,75,0);
            $this->Cell($w[2],6,number_format($row['sumein'], 2, ',', '.'),'',0,'R',$fill);
            $this->SetTextColor(0,0,0);
            $this->Cell($w[3],6,'EUR','',0,'L',$fill);
            $this->Ln();
        }

        $this->SetFont('','B','10');
        $this->Cell($w[0],6,'','T',0,'L');
        $this->Cell($w[1],6,'Summe Einnahmen:','T',0,'R');
        $this->SetTextColor(0,75,0);
        $this->Cell($w[2],6,number_format($sumein, 2, ',', '.'),'T',0,'R');
        $this->SetTextColor(0,0,0);
        $this->Cell($w[3],6,'EUR','T',0,'L');


		$sqlQuery = "SELECT katbez_kb, katbez, SUM(ausgang) as sumaus, katart, color FROM tblKassa 
					 LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant
					 WHERE mandant = :mandant AND periode = :periode  
					 GROUP BY katbez, katart, color 
                     HAVING katart != 3 AND sumaus != '' ";

        $stmt = $this->db->prepare($sqlQuery);
        $stmt->bindParam(':mandant', $this->mandant);
        $stmt->bindParam(':periode', $this->periode);
        $stmt->execute();
        $result = $stmt->fetchAll();
        
        $this->SetFillColor(230,230,230);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        
        $header = array('KST', 'Bezeichnung', 'Ausgang', '');
        $this->Ln(10);

        $this->SetFont('','B','14');
        $this->Cell(150,7,'Ausgaben','',0,'L');
        
        $this->Ln(9);
        
        $this->SetFont('','B','10');
        $this->Cell($w[0],6,$header[0], 'B', 0, 'L');
        $this->Cell($w[1],6,$header[1], 'B', 0, 'L');
        $this->Cell($w[2],6,$header[2], 'B', 0, 'R');
        $this->Cell($w[3],6,$header[3], 'B', 0, 'L');
        $this->Ln(6.2);
        $sumaus = 0;
        foreach($result as $row)
        {
            
            $fill = !$fill;
            $this->SetFont('','B');
            $this->Cell($w[0],6,iconv2($row['katbez_kb']),'',0,'L',$fill);
            $this->SetFont('');
            $this->Cell($w[1],6,iconv2($row['katbez']),'',0,'L',$fill);
            $sumaus += $row['sumaus'];
            $this->SetTextColor(120,0,0);
            $this->Cell($w[2],6,number_format($row['sumaus'], 2, ',', '.'),'',0,'R',$fill);
            $this->SetTextColor(0,0,0);
            $this->Cell($w[3],6,'EUR','',0,'L',$fill);
            $this->Ln();
        }
        $this->SetFont('','B','10');

        $this->Cell($w[0],6,'','T',0,'L');
        $this->Cell($w[1],6,'Summe Ausgaben:','T',0,'R');
        $this->SetTextColor(120,0,0);
        $this->Cell($w[2],6,number_format($sumaus, 2, ',', '.'),'T',0,'R');
        $this->SetTextColor(0,0,0);
        $this->Cell($w[3],6,'EUR','T',0,'L');

        $this->Ln(10);

        $this->SetFont('','B','14');
        $sumgesamt = $sumein - $sumaus;
        $this->SetFillColor(230);
        $this->SetDrawColor(160);
        $this->SetLineWidth(.1);
        $this->RoundedRect($this->getX(), $this->getY(), 170, 12, 3, '24', 'DF');
        if ($sumgesamt < 0) {
            $this->SetTextColor(120,0,0);
            $this->Cell(170,13,'Ausgaben gesamt: ' . number_format($sumgesamt, 2, ',', '.') . ' EUR','',0,'L');
        } else {
            $this->SetTextColor(0,75,0);
            $this->Cell(170,13,'Einnahmen gesamt: ' . number_format($sumgesamt, 2, ',', '.') . ' EUR','',0,'C');
        }

        $this->Ln();


        // Kontoübersicht
        $sqlQuery =  "SELECT * FROM tblKonten 
		                WHERE kmandant = :mandant AND kperiode = :periode  
		                ORDER BY kid ASC ";

        $stmt = $this->db->prepare($sqlQuery);
        $stmt->bindParam(':mandant', $this->mandant);
        $stmt->bindParam(':periode', $this->periode);
        $stmt->execute();
        $result = $stmt->fetchAll();

		$sumstartsaldo = 0;
		$sumaktsaldo = 0;
		$sumbewegung = 0;

        $this->SetFillColor(230,230,230);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        
        $header = array('Konto', 'Saldo start', 'Saldo aktuell', 'Kontobewegung');
        $w = array(50, 40, 40, 40); 
        $this->Ln(10);

        $this->SetFont('','B','14');
        $this->Cell(150,7,iconv2('Kontoübersicht'),'',0,'L');
        
        $this->Ln(9);
        
        $this->SetFont('','B','10');
        $this->Cell($w[0],6,$header[0], 'B', 0, 'L');
        $this->Cell($w[1],6,$header[1], 'B', 0, 'R');
        $this->Cell($w[2],6,$header[2], 'B', 0, 'R');
        $this->Cell($w[3],6,$header[3], 'B', 0, 'R');
        $this->Ln(6.2);
        $sumein = 0;
        foreach($result as $row)
        {
            if ($row['saldostart'] <> 0 || $row['saldoaktuell'] <> 0 ) {
                $sumstartsaldo += $row['saldostart'];
                $sumaktsaldo += $row['saldoaktuell'];
                $sumbewegung += $row['saldoaktuell'] - $row['saldostart'];
                
                $fill = !$fill;
                $this->SetFont('','B');
                $this->SetTextColor(0);
                $this->Cell($w[0],6,iconv2($row['kontoname']),'',0,'L',$fill);
                $this->SetFont('');
                $this->Cell($w[1],6,number_format($row['saldostart'], 2, ',', '.'),'',0,'R',$fill);
                
                $this->Cell($w[2],6,number_format($row['saldoaktuell'], 2, ',', '.'),'',0,'R',$fill);
                $this->SetTextColor(0,0,0);
                $sumeinaus = $row['saldoaktuell'] - $row['saldostart'];
                if ($sumeinaus >= 0 ) {
                    $this->SetTextColor(0,75,0);
                } else {
                    $this->SetTextColor(120,0,0);
                }

                $this->Cell($w[3],6,number_format($sumeinaus, 2, ',', '.'),'',0,'R',$fill);
                $this->Ln();
            }
        }
        $this->SetTextColor(0);
        $this->SetFont('','B','10');
        $this->Cell($w[0],6,'Summe','T',0,'L');
        $this->Cell($w[1],6,number_format($sumstartsaldo, 2, ',', '.'),'T',0,'R');
        $this->Cell($w[2],6,number_format($sumaktsaldo, 2, ',', '.'),'T',0,'R');

        if ($sumbewegung >= 0 ) {
            $this->SetTextColor(0,75,0);
        } else {
            $this->SetTextColor(120,0,0);
        }
        $this->Cell($w[3],6,number_format($sumbewegung, 2, ',', '.'),'T',0,'R');



        // Projekte / Events
		$sqlQuery = "SELECT SUM(eingang) as sumein, SUM(ausgang) as sumaus, projektname, pcolor FROM tblKassa 
					 INNER JOIN tblProjekt on tblKassa.projekt = tblProjekt.pid AND tblKassa.mandant = tblProjekt.pmandant
					 WHERE mandant = :mandant AND periode = :periode  
					 GROUP BY projekt, pcolor ";

        $stmt = $this->db->prepare($sqlQuery);
        $stmt->bindParam(':mandant', $this->mandant);
        $stmt->bindParam(':periode', $this->periode);
        $stmt->execute();
        $result = $stmt->fetchAll();

		$sumein = 0;
		$sumaus = 0;
        $sumeinaus = 0;

        $this->SetFillColor(230,230,230);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        
        $header = array('Projekt', 'Eingang', 'Ausgang', 'Saldo');
        $w = array(80, 30, 30, 30); 

        if ($stmt->rowCount() > 0 ) {

            $this->Ln(10);
            
            $this->SetFont('','B','14');
            $this->Cell(150,7,iconv2('Projekte/Events'),'',0,'L');
            
            $this->Ln(9);
            
            $this->SetFont('','B','10');
            $this->Cell($w[0],6,$header[0], 'B', 0, 'L');
            $this->Cell($w[1],6,$header[1], 'B', 0, 'R');
            $this->Cell($w[2],6,$header[2], 'B', 0, 'R');
            $this->Cell($w[3],6,$header[3], 'B', 0, 'R');
            $this->Ln(6.2);
            
            foreach($result as $row)
            {
                
                $fill = !$fill;
                $this->SetFont('','B');
                $this->SetTextColor(0);
                $this->Cell($w[0],6,iconv2($row['projektname']),'',0,'L',$fill);
                $sumein = $row['sumein'] == null ? 0 : $row['sumein'];
                $sumaus = $row['sumaus'] == null ? 0 : $row['sumaus'];
                $sumeinaus = $sumein - $sumaus;
                
                $this->SetFont('');
                $this->SetTextColor(0,75,0);
                $this->Cell($w[1],6,number_format($sumein, 2, ',', '.'),'',0,'R',$fill);
                
                $this->SetTextColor(120,0,0);
                $this->Cell($w[2],6,number_format($sumaus, 2, ',', '.'),'',0,'R',$fill);
                
                if ($sumeinaus >= 0 ) {
                    $this->SetTextColor(0,75,0);
                } else {
                    $this->SetTextColor(120,0,0);
                }
                
                $this->Cell($w[3],6,number_format($sumeinaus, 2, ',', '.'),'',0,'R',$fill);
                $this->Ln();
            }
        }
        $this->SetTextColor(0);
        $this->SetFont('','B','10');
        
        
    }
    
    function LoadKST() {

        $sqlQuery =   "SELECT * FROM tblKategorie WHERE katmandant = :mandant";
        
        $stmt = $this->db->prepare($sqlQuery);
        $stmt->bindParam(':mandant', $this->mandant);
        $stmt->execute();
        $result = $stmt->fetchAll();
        $header = array('KST', 'Bezeichnung');
        $w = array(30, 85); 
        foreach($result as $row)
        {
            
            $fill = !$fill;
            $this->SetFont('','B');
            $this->Cell($w[0],6,iconv2($row['katbez_kb']),'',0,'R',$fill);
            $this->SetFont('');
            $this->Cell($w[1],6,iconv2($row['katbez']),'',0,'L',$fill);
            $this->Ln();
        }
        

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
            $str_bez = $row['bezeichnung'];
            if (strlen($str_bez) > 50) {
                $str_bez = substr($row['bezeichnung'],0,50) . "...";
            }
            $this->Cell($w[2],6,iconv2($str_bez),'',0,'L',$fill);
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
$pdf->init_data($mandant, $periode);
// Column headings
$header = array('#', 'Datum', 'Bezeichnung', 'Eingang', 'Ausgang', 'KST');
$pdf->AddPage();
$pdf->Deckblatt();
$pdf->SetFont('Arial','',10);

$pdf->ein_kst();
$pdf->AddPage();
$pdf->Ln(1);

$sqlQuery = "SELECT * FROM tblKonten WHERE kmandant = :mandant AND kperiode = :periode ORDER BY kid ASC";
$stmt = $db->prepare($sqlQuery);
$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
$stmt->execute();
$result = $stmt->fetchAll();

foreach($result as $row) {
    if ($row['saldostart'] <> 0 || $row['saldoaktuell'] <> 0 ) {
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

        
        $pdf->Ln(12);
        $pdf->SetFont('Arial','',10);
        // Data loading
        $data = $pdf->LoadData($row['kid']);
        $pdf->FancyTable($header,$data);
        $pdf->Ln(1);
    }
}
$pdf->AddPage();
$pdf->Ln(1);
$pdf->LoadKST();

$pdf->Output();
?>