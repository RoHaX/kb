<?php
class Records {	
   
	private $recordsTable = 'tblKassa';
	public $id;
	public $mandant;
	public $beleg;
	public $datum;
	public $bezeichnung;
	public $eingang;
	public $ausgang;
	public $konto;
	public $kat;
	public $projekt;
	private $conn;

			
	public function __construct($db){
        $this->conn = $db;
    }	    
	
	public function listRecords($mandant, $periode){
		
		$sqlQuery =   "SELECT beleg, datum, bezeichnung, eingang, ausgang, kontoname as konto, katbez_kb as kat, projekt_kb as projekt, periode, color, mandant, id
			FROM tblKassa 
			LEFT JOIN tblKonten on tblKassa.konto = tblKonten.kid AND tblKassa.mandant = tblKonten.kmandant AND tblKassa.periode = tblKonten.kperiode
			LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant 
			LEFT JOIN tblProjekt on tblKassa.projekt = tblProjekt.pid AND tblKassa.mandant = tblProjekt.pmandant ";

			$sqlQuery .= 'WHERE mandant = '.$mandant.' AND periode = '.$periode.' ';

		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= 'AND bezeichnung LIKE "%'.$_POST["search"]["value"].'%" ';
			/*
			$sqlQuery .= ' OR name LIKE "%'.$_POST["search"]["value"].'%" ';			
			$sqlQuery .= ' OR designation LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR address LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR skills LIKE "%'.$_POST["search"]["value"].'%") ';			
			*/
		}

		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.($_POST['order']['0']['column'] + 1).' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY beleg DESC ';
		}

		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}
		
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();	

		$sqlQuery =   "SELECT id FROM tblKassa WHERE mandant = ".$mandant." AND periode = ".$periode;

		$stmtTotal = $this->conn->prepare($sqlQuery);
		$stmtTotal->execute();
		$allResult = $stmtTotal->get_result();
		$allRecords = $allResult->num_rows;
		
		$displayRecords = $result->num_rows;
		
		$records = array();		
		while ($record = $result->fetch_assoc()) { 				
			$rows = array();			
			$rows[] = $record['beleg'];
			$rows[] = $record['datum'];	
			$rows[] = $record['bezeichnung'];
			$rows[] = $record['eingang'] == null ? "" : number_format($record['eingang'], 2, ',', '.')." €";
			$rows[] = $record['ausgang'] == null ? "" : number_format($record['ausgang'], 2, ',', '.')." €";
			$rows[] = $record['konto'];
			$rows[] = '<span style="color: '.$record['color'].'"><i class="fas fa-ellipsis-v"></i></span> '.$record['kat'];
			$rows[] = $record['projekt'];					
			$rows[] = '<button type="button" name="update" id="'.$record["id"].'" class="btn btn-warning btn-sm update"><i class="far fa-edit"></i></button>';			
			$rows[] = '<a id="'.$record["id"].'" class="btn btn-secondary btn-sm btn-delete-item" ><i class="far fa-trash-alt"></i></a>';
			$records[] = $rows;
		}

		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	$displayRecords,
			"iTotalDisplayRecords"	=>  $allRecords,
			"data"	=> 	$records
		);

		echo json_encode($output);
	}
	
	public function listKontoBuchungen($mandant, $periode, $konto){
		
		$sqlQuery =   "SELECT beleg, datum, bezeichnung, eingang, ausgang, kontoname as konto, katbez_kb as kat, projekt_kb as projekt, periode, color, mandant, id
			FROM tblKassa 
			LEFT JOIN tblKonten on tblKassa.konto = tblKonten.kid AND tblKassa.mandant = tblKonten.kmandant AND tblKassa.periode = tblKonten.kperiode
			LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant 
			LEFT JOIN tblProjekt on tblKassa.projekt = tblProjekt.pid AND tblKassa.mandant = tblProjekt.pmandant ";

			$sqlQuery .= 'WHERE mandant = '.$mandant.' AND periode = '.$periode.'  AND kid = '.$konto.' ';

		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= 'AND bezeichnung LIKE "%'.$_POST["search"]["value"].'%" ';
			/*
			$sqlQuery .= ' OR name LIKE "%'.$_POST["search"]["value"].'%" ';			
			$sqlQuery .= ' OR designation LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR address LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR skills LIKE "%'.$_POST["search"]["value"].'%") ';			
			*/
		}

		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.($_POST['order']['0']['column'] + 1).' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY beleg DESC ';
		}

		if($_POST["length"] != -1){
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
		}
		
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();	

		$records = array();		
		while ($record = $result->fetch_assoc()) { 				
			$rows = array();			
			$rows[] = $record['beleg'];
			$rows[] = $record['datum'];	
			$rows[] = $record['bezeichnung'];
			$rows[] = $record['eingang'] == null ? "" : number_format($record['eingang'], 2, ',', '.')." €";
			$rows[] = $record['ausgang'] == null ? "" : number_format($record['ausgang'], 2, ',', '.')." €";
			$rows[] = '<span style="color: '.$record['color'].'"><i class="fas fa-ellipsis-v"></i></span> '.$record['kat'];
			$records[] = $rows;
		}

		$output = array(
			"data"	=> 	$records
		);

		echo json_encode($output);
	}

	public function listKonten($mandant, $periode){

		//aktualisiere aktuellen kontostand
		$sqlKonto = "UPDATE tblKonten tk 
					INNER JOIN (
						SELECT COALESCE(SUM(eingang),0) as sumein, COALESCE(SUM(ausgang),0) as sumaus, konto
						FROM tblKassa
						WHERE mandant = ".$mandant." AND periode = ".$periode."
						GROUP BY konto) gb ON tk.kid = gb.konto 
					SET tk.saldoaktuell = tk.saldostart + gb.sumein - gb.sumaus
					WHERE kmandant = ".$mandant." AND kperiode = ".$periode.";";

		$stmt = $this->conn->prepare($sqlKonto);
		$stmt->execute();
		
		$sqlQuery =  "SELECT * FROM tblKonten ";
		$sqlQuery .= "WHERE kmandant = ".$mandant." AND kperiode = ".$periode." ";
		$sqlQuery .= "ORDER BY kid ASC ";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();	
		$sumstartsaldo = 0;
		$sumaktsaldo = 0;
		$sumbewegung = 0;
		$records = array();		
		while ($record = $result->fetch_assoc()) { 				
			$sumstartsaldo += $record['saldostart'];
			$sumaktsaldo += $record['saldoaktuell'];
			$sumbewegung += $record['saldoaktuell'] - $record['saldostart'];
			$rows = array();			
			$rows[] = $record['kontoname'];	
			$rows[] = number_format($record['saldostart'], 2, ',', '.')." €";
			$rows[] = number_format($record['saldoaktuell'], 2, ',', '.')." €";

			$sumeinaus = $record['saldoaktuell'] - $record['saldostart'];
			if ($sumeinaus >= 0 ) {
				$color = "#008c23";
			} else {
				$color = "#b3002d";
			}
			$rows[] = "<span style='color: " . $color . "'>" . number_format($sumeinaus, 2, ',', '.') . " €</span>";


			//$rows[] = number_format($record['saldoaktuell'] - $record['saldostart'], 2, ',', '.')." €";
			$rows[] = '<button type="button" name="updatekont" id="'.$record["kid"].'" class="btn btn-warning btn-sm update"><i class="far fa-edit"></i></button>';			

			//$rows[] = $record['ausgang'] == null ? "" : number_format($record['ausgang'], 2, ',', '.')." €";
			//$rows[] = '<button type="button" name="updatek" id="'.$record["kid"].'" class="btn btn-warning btn-sm update"><i class="far fa-edit"></i></button>';			
	
			$records[] = $rows;
		}
		$displayRecords = 1;
		$allRecords = 1;

		if ($sumbewegung >= 0 ) {
			$color = "#008c23";
		} else {
			$color = "#b3002d";
		}
		$rows[] = "<span style='color: " . $color . "'>" . number_format($sumeinaus, 2, ',', '.') . " €</span>";

		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	$displayRecords,
			"iTotalDisplayRecords"	=>  $allRecords,
			"data"	=> 	$records,
			"sumstartsaldo" => number_format($sumstartsaldo, 2, ',', '.')." €",
			"sumaktsaldo" => number_format($sumaktsaldo, 2, ',', '.')." €",
			"sumbewegung" => "<span style='color: " . $color . "'>" . number_format($sumbewegung, 2, ',', '.')." €</span>",

		);
		
		echo json_encode($output);
	}
	
	public function listEinAus($mandant, $periode){
		
		$sqlQuery = "SELECT SUM(eingang) as sumein, SUM(ausgang) as sumaus, projektname, pcolor FROM tblKassa 
					 INNER JOIN tblProjekt on tblKassa.projekt = tblProjekt.pid AND tblKassa.mandant = tblProjekt.pmandant
					 WHERE mandant = ".$mandant." AND periode = ".$periode."  
					 GROUP BY projekt, pcolor ";
					 
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();	
		
		$records = array();		
		while ($record = $result->fetch_assoc()) { 				
			$rows = array();			
			$rows[] = '<span style="color: '.$record['pcolor'].'"><i class="fas fa-square"></i></span> '.$record['projektname'];
			$rows[] = $record['sumein'] == null ? "" : number_format($record['sumein'], 2, ',', '.')." €";
			$rows[] = $record['sumaus'] == null ? "" : number_format($record['sumaus'], 2, ',', '.')." €";

			$sumein = $record['sumein'] == null ? 0 : $record['sumein'];
			$sumaus = $record['sumaus'] == null ? 0 : $record['sumaus'];
			$sumeinaus = $sumein - $sumaus;
			if ($sumeinaus >= 0 ) {
				$color = "#008c23";
			} else {
				$color = "#b3002d";
			}
			$rows[] = "<span style='color: " . $color . "'>" . number_format($sumeinaus, 2, ',', '.') . " €</span>";
	
			$records[] = $rows;
		}

		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	4,
			"iTotalDisplayRecords"	=>  5,
			"data"	=> 	$records,
		);
		
		echo json_encode($output);
	}
	public function listEin($mandant, $periode){
		
		$sqlQuery = "SELECT katbez, SUM(eingang) as sumein, katart, color FROM tblKassa 
					 LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant
					 WHERE mandant = ".$mandant." AND periode = ".$periode."  
					 GROUP BY katbez, katart, color ";

		$sqlQuery .= "HAVING katart != 3 AND sumein != '' ";

		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.($_POST['order']['0']['column'] + 1).' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY katbez ASC ';
		}
		
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();	
		
		$gsum = 0;
		$records = array();		
		while ($record = $result->fetch_assoc()) { 				
			$rows = array();			
			$gsum += $record['sumein'];
			$rows[] = '<span style="color: '.$record['color'].'"><i class="fas fa-square"></i></span> '.$record['katbez'];
			$rows[] = $record['sumein'] == null ? "" : number_format($record['sumein'], 2, ',', '.')." €";
			$records[] = $rows;
		}

		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	4,
			"iTotalDisplayRecords"	=>  5,
			"data"	=> 	$records,
			"gsum" => number_format($gsum, 2, ',', '.')." €",
		);
		
		echo json_encode($output);
	}	

	public function listAus($mandant, $periode){
		
		$sqlQuery = "SELECT katbez, SUM(ausgang) as sumaus, katart, color FROM tblKassa 
					 LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant
					 WHERE mandant = ".$mandant." AND periode = ".$periode."  
					 GROUP BY katbez, katart, color ";

		$sqlQuery .= "HAVING katart != 3 AND sumaus != '' ";

		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.($_POST['order']['0']['column'] + 1).' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY katbez ASC ';
		}

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();	
		
		$gsum = 0;
		$records = array();		
		while ($record = $result->fetch_assoc()) { 				
			$rows = array();		
			$gsum += $record['sumaus'];			
			$rows[] = '<span style="color: '.$record['color'].'"><i class="fas fa-square"></i></span> '.$record['katbez'];
			$rows[] = $record['sumaus'] == null ? "" : number_format($record['sumaus'], 2, ',', '.')." €";
			$records[] = $rows;
		}

		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	4,
			"iTotalDisplayRecords"	=>  5,
			"data"	=> 	$records,
			"gsum" => number_format($gsum, 2, ',', '.')." €",
		);
		
		echo json_encode($output);
	}
	
	public function listKategorie($mandant){
		
		$sqlQuery = "SELECT katbez, katart, color, katid  FROM tblKategorie 
					 WHERE katmandant = ".$mandant." ";

		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.($_POST['order']['0']['column'] + 1).' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY katart, katbez ASC ';
		}

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();	
		
		$records = array();		
		while ($record = $result->fetch_assoc()) { 				
			$rows = array();		

			if ($record['katart'] == 1) {
				$strKatArt = "<span style='font-weight: bold; color: #008c23'>E</span>";
				//$strKatArt = "E";
			} elseif ($record['katart'] == 2) {
				$strKatArt = "<span style='font-weight: bold; color: #b3002d'>A</span>";
			} elseif ($record['katart'] == 3) {
				$strKatArt = "U";
			}
			$rows[] = '<span style="color: '.$record['color'].'"><i class="fas fa-square"></i></span> '.$record['katbez'];
			$rows[] = $strKatArt;
			$rows[] = '<button type="button" name="updatekat" id="'.$record["katid"].'" class="btn btn-warning btn-sm update"><i class="far fa-edit"></i></button>';			
			$records[] = $rows;
		}

		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	4,
			"iTotalDisplayRecords"	=>  5,
			"data"	=> 	$records,
		);
		
		echo json_encode($output);
	}		
	
	public function listProjekte($mandant){
		
		$sqlQuery = "SELECT * FROM tblProjekt 
					 WHERE pmandant = ".$mandant." ";

		if(!empty($_POST["order"])){
			$sqlQuery .= 'ORDER BY '.($_POST['order']['0']['column'] + 1).' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY projektname ASC ';
		}

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();	
		
		$records = array();		
		while ($record = $result->fetch_assoc()) { 				
			$rows = array();		

			$rows[] = '<span style="color: '.$record['pcolor'].'"><i class="fas fa-square"></i></span> '.$record['projektname'];
			//$rows[] = $record['projekt_kb'];
			$rows[] = '<button type="button" name="updatepro" id="'.$record["pid"].'" class="btn btn-warning btn-sm update"><i class="far fa-edit"></i></button>';			
			$records[] = $rows;
		}

		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	4,
			"iTotalDisplayRecords"	=>  5,
			"data"	=> 	$records,
		);
		
		echo json_encode($output);
	}	
	
	public function getChart($einaus, $mandant, $periode){

		$sqlQuery = "SELECT SUM(".$einaus.") as summe, katbez, katart, color FROM tblKassa 
					 LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant
					 WHERE mandant = ".$mandant." AND periode = ".$periode."  
					 GROUP BY katbez, katart, color
					 HAVING summe IS NOT NULL AND katart != 3";
							
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->execute();
		$result = $stmt->get_result();	
		
		$records = array();
		$datax = array();
		$labelx = array();
		while ($record = $result->fetch_assoc()) {			
						
			$datax[] = $record['summe'];	
			$labelx[] = $record['katbez'];
			$color[] = $record['color'];
		}
		
		$output = array(
			"labels" =>  $labelx,
			"data"	 =>  $datax,
			"color"	 =>  $color
		);	

		echo json_encode($output);
	}
	
	public function getRecord(){
		if($this->id) {
			$sqlQuery = "
				SELECT * FROM ".$this->recordsTable." 
				WHERE id = ?";			
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->id);	
			$stmt->execute();
			$result = $stmt->get_result();
			$record = $result->fetch_assoc();
			echo json_encode($record);
		}
	}

	public function getDsKonten(){
		if($this->kid) {
			$sqlQuery = "
				SELECT kid, kontoname, saldostart FROM tblKonten 
				WHERE kid = ?";			
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->kid);	
			$stmt->execute();
			$result = $stmt->get_result();
			$record = $result->fetch_assoc();
			echo json_encode($record);
		}
	}
	
	public function getDsKategorie(){
		if($this->katid) {
			$sqlQuery = "
				SELECT * FROM tblKategorie 
				WHERE katid = ?";			
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->katid);	
			$stmt->execute();
			$result = $stmt->get_result();
			$record = $result->fetch_assoc();
			echo json_encode($record);
		}
	}	

	public function getDsProjekt(){
		if($this->pid) {
			$sqlQuery = "
				SELECT * FROM tblProjekt 
				WHERE pid = ?";			
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->pid);	
			$stmt->execute();
			$result = $stmt->get_result();
			$record = $result->fetch_assoc();
			echo json_encode($record);
		}
	}	

	public function updateRecord(){
		
		if($this->id) {
			

	
			$eingang = str_replace(",", ".", $this->eingang);
			($eingang == '') ? $eingang = null : $eingang = $eingang;
			$ausgang = str_replace(",", ".",$this->ausgang);
			($ausgang == '') ? $ausgang = null : $ausgang = $ausgang;
	
			$stmt = $this->conn->prepare("
			UPDATE ".$this->recordsTable." 
			SET beleg = ?, datum = ?, bezeichnung = ?, eingang = ?, ausgang = ?, konto = ?, kat = ?, projekt = ?
			WHERE id = ?");
	 
			$stmt->bind_param("issssiiii", $this->beleg, $this->datum, $this->bezeichnung, $eingang, $ausgang, $this->konto, $this->kat, $this->projekt, $this->id);
			/*
			$this->id = htmlspecialchars(strip_tags($this->id));
			$this->name = htmlspecialchars(strip_tags($this->name));
			$this->age = htmlspecialchars(strip_tags($this->age));
			$this->skills = htmlspecialchars(strip_tags($this->skills));
			$this->address = htmlspecialchars(strip_tags($this->address));
			$this->designation = htmlspecialchars(strip_tags($this->designation));
			*/
			
			if($stmt->execute()){ 
				return true;
			}
			/*
			if($stmt->execute()){
				echo "OK";
				return true;
			} else {
				echo "Fehler";
			}
			*/
			
		}	
	}

	public function updateKonto(){
		if($this->id) {
			$saldo = str_replace(",", ".", $this->saldostart);
			$stmt = $this->conn->prepare("
			UPDATE tblKonten 
			SET kontoname = ?, saldostart = ?, saldoaktuell = ?
			WHERE kid = ?");
	 
			$stmt->bind_param("sssi", $this->kontoname, $saldo, $saldo, $this->id);
			if($stmt->execute()){
				return true;
			}
			
		} else {
			return true;
		}
	}

	public function updateKategorie(){
		if($this->id) {
			$stmt = $this->conn->prepare("
			UPDATE tblKategorie 
			SET katbez_kb = ?, katbez = ?, katart = ?, color = ?
			WHERE katid = ?");
	 
			$stmt->bind_param("ssisi", $this->katbez_kb, $this->katbez, $this->katart, $this->color, $this->id);
			if($stmt->execute()){ 
				return true;
			}
			
		} else {
			return true;
		}
	}

	public function updateProjekt(){
		if($this->id) {
			$stmt = $this->conn->prepare("
			UPDATE tblProjekt 
			SET projektname = ?, projekt_kb = ?,  pcolor = ?
			WHERE pid = ?");
	
			$stmt->bind_param("sssi", $this->projektname, $this->projekt_kb, $this->pcolor, $this->id);
			if($stmt->execute()){ 
				return true;
			}
			
		} else {
			return true;
		}
	}
	
	public function addRecord($mandant, $periode){
		
		if($this->beleg) {
			
			$eingang = str_replace(",", ".", $this->eingang);
			($eingang == '') ? $eingang = null : $eingang = $eingang;
			$ausgang = str_replace(",", ".",$this->ausgang);
			($ausgang == '') ? $ausgang = null : $ausgang = $ausgang;
			
			$stmt = $this->conn->prepare("
			INSERT INTO ".$this->recordsTable."(`mandant`, `periode`, `beleg`, `datum`, `bezeichnung`, `eingang`, `ausgang`, `konto`, `kat`, `projekt`)
			VALUES(?,?,?,?,?,?,?,?,?,?)");
			/*
			$this->name = htmlspecialchars(strip_tags($this->name));
			$this->age = htmlspecialchars(strip_tags($this->age));
			$this->skills = htmlspecialchars(strip_tags($this->skills));
			$this->address = htmlspecialchars(strip_tags($this->address));
			$this->designation = htmlspecialchars(strip_tags($this->designation));
			*/
			
			$stmt->bind_param("iiissssiii", $mandant, $periode, $this->beleg, $this->datum, $this->bezeichnung, $eingang, $ausgang, $this->konto, $this->kat, $this->projekt);
			
			if($stmt->execute()){
				return true;
			}		
		}
	}
	public function deleteRecord(){
		if($this->id) {			

			$stmt = $this->conn->prepare("
				DELETE FROM ".$this->recordsTable." 
				WHERE id = ?");

			$this->id = htmlspecialchars(strip_tags($this->id));

			$stmt->bind_param("i", $this->id);

			if($stmt->execute()){
				return true;
			}
		}
	}
	
}
?>