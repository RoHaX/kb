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
		$sqlQuery = "SELECT beleg, datum, bezeichnung, eingang, ausgang, kontoname as konto, katbez_kb as kat, projekt_kb as projekt, periode, color, mandant, id
			FROM tblKassa 
			LEFT JOIN tblKonten on tblKassa.konto = tblKonten.kid AND tblKassa.mandant = tblKonten.kmandant AND tblKassa.periode = tblKonten.kperiode
			LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant 
			LEFT JOIN tblProjekt on tblKassa.projekt = tblProjekt.pid AND tblKassa.mandant = tblProjekt.pmandant 
			WHERE mandant = :mandant AND periode = :periode";

		// Bind parameters for mandant and periode
		$params = [
			':mandant' => $mandant,
			':periode' => $periode
		];

		if(!empty($_POST["search"]["value"])){
			$sqlQuery .= ' AND bezeichnung LIKE :search_value';
			$params[':search_value'] = '%'.$_POST["search"]["value"].'%';
		}

		if(!empty($_POST["order"])){
			$sqlQuery .= ' ORDER BY '.($_POST['order']['0']['column'] + 1).' '.$_POST['order']['0']['dir'];
		} else {
			$sqlQuery .= ' ORDER BY beleg DESC';
		}


		if ($_POST["length"] != -1) {
			$sqlQuery .= ' LIMIT :start, :length';
			$params[':start'] = intval($_POST['start']);
			$params[':length'] = intval($_POST['length']);
		}

		// Prepare and execute the query
		$stmt = $this->conn->prepare($sqlQuery);
		foreach ($params as $key => &$val) {
			if ($key == ':start' || $key == ':length') {
				$stmt->bindValue($key, $val, PDO::PARAM_INT);
			} else {
				$stmt->bindValue($key, $val);
			}
		}
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		/*
		// Total records query
		$sqlTotalQuery = "SELECT COUNT(id) as total FROM tblKassa WHERE mandant = :mandant AND periode = :periode";
		$stmtTotal = $this->conn->prepare($sqlTotalQuery);
		$stmtTotal->bindParam(':mandant', $mandant, PDO::PARAM_INT);
		$stmtTotal->bindParam(':periode', $periode, PDO::PARAM_INT);
		$stmtTotal->execute();
		$totalResult = $stmtTotal->fetch(PDO::FETCH_ASSOC);
		$allRecords = $totalResult['total'];
*/
		// Prepare the output
		$records = [];
		foreach ($result as $record) {
			$rows = [];
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

		$output = [
			"draw" => intval($_POST["draw"]),
			"iTotalRecords" => count($result),
			"iTotalDisplayRecords" => $allRecords,
			"data" => $records
		];

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

		// Aktualisiere aktuellen Kontostand
		$sqlKonto = "UPDATE tblKonten tk 
					INNER JOIN (
						SELECT COALESCE(SUM(eingang),0) as sumein, COALESCE(SUM(ausgang),0) as sumaus, konto
						FROM tblKassa
						WHERE mandant = :mandant AND periode = :periode
						GROUP BY konto) gb ON tk.kid = gb.konto 
					SET tk.saldoaktuell = tk.saldostart + gb.sumein - gb.sumaus
					WHERE kmandant = :mandant AND kperiode = :periode";

		$stmt = $this->conn->prepare($sqlKonto);
		$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
		$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
		$stmt->execute();

		// Hole Konteninformationen
		$sqlQuery = "SELECT * FROM tblKonten WHERE kmandant = :mandant AND kperiode = :periode ORDER BY kid ASC";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
		$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$sumstartsaldo = 0;
		$sumaktsaldo = 0;
		$sumbewegung = 0;
		$records = array();

		foreach ($result as $record) {
			$sumstartsaldo += $record['saldostart'];
			$sumaktsaldo += $record['saldoaktuell'];
			$sumbewegung += $record['saldoaktuell'] - $record['saldostart'];

			$rows = array();
			$rows[] = $record['kontoname'];
			$rows[] = number_format($record['saldostart'], 2, ',', '.')." €";
			$rows[] = number_format($record['saldoaktuell'], 2, ',', '.')." €";

			$sumeinaus = $record['saldoaktuell'] - $record['saldostart'];
			$color = $sumeinaus >= 0 ? "#008c23" : "#b3002d";
			$rows[] = "<span style='color: " . $color . "'>" . number_format($sumeinaus, 2, ',', '.') . " €</span>";

			$rows[] = '<button type="button" name="updatekont" id="'.$record["kid"].'" class="btn btn-warning btn-sm update"><i class="far fa-edit"></i></button>';

			$records[] = $rows;
		}

		$displayRecords = count($records);
		$allRecords = $displayRecords;

		$output = array(
			"draw" => intval($_POST["draw"]),
			"iTotalRecords" => $displayRecords,
			"iTotalDisplayRecords" => $allRecords,
			"data" => $records,
			"sumstartsaldo" => number_format($sumstartsaldo, 2, ',', '.')." €",
			"sumaktsaldo" => number_format($sumaktsaldo, 2, ',', '.')." €",
			"sumbewegung" => "<span style='color: " . $color . "'>" . number_format($sumbewegung, 2, ',', '.')." €</span>",
		);

		echo json_encode($output);
	}

	
	public function listEinAus($mandant, $periode){
		$sqlQuery = "SELECT SUM(eingang) as sumein, SUM(ausgang) as sumaus, projektname, pcolor FROM tblKassa 
					INNER JOIN tblProjekt on tblKassa.projekt = tblProjekt.pid AND tblKassa.mandant = tblProjekt.pmandant
					WHERE mandant = :mandant AND periode = :periode  
					GROUP BY projekt, pcolor";

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
		$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$records = array();
		foreach ($result as $record) {
			$rows = array();
			$rows[] = '<span style="color: '.$record['pcolor'].'"><i class="fas fa-square"></i></span> '.$record['projektname'];
			$rows[] = $record['sumein'] == null ? "" : number_format($record['sumein'], 2, ',', '.')." €";
			$rows[] = $record['sumaus'] == null ? "" : number_format($record['sumaus'], 2, ',', '.')." €";

			$sumein = $record['sumein'] == null ? 0 : $record['sumein'];
			$sumaus = $record['sumaus'] == null ? 0 : $record['sumaus'];
			$sumeinaus = $sumein - $sumaus;
			$color = $sumeinaus >= 0 ? "#008c23" : "#b3002d";
			$rows[] = "<span style='color: " . $color . "'>" . number_format($sumeinaus, 2, ',', '.') . " €</span>";

			$records[] = $rows;
		}

		$output = array(
			"draw" => intval($_POST["draw"]),
			"iTotalRecords" => count($records),
			"iTotalDisplayRecords" => count($records),
			"data" => $records,
		);

		echo json_encode($output);
	}


	public function listEin($mandant, $periode){
		$sqlQuery = "SELECT katbez, SUM(eingang) as sumein, katart, color FROM tblKassa 
					LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant
					WHERE mandant = :mandant AND periode = :periode  
					GROUP BY katbez, katart, color 
					HAVING katart != 3 AND sumein != ''";

		if(!empty($_POST["order"])){
			$sqlQuery .= ' ORDER BY '.($_POST['order']['0']['column'] + 1).' '.$_POST['order']['0']['dir'];
		} else {
			$sqlQuery .= ' ORDER BY katbez ASC';
		}

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
		$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$gsum = 0;
		$records = array();

		foreach ($result as $record) {
			$rows = array();
			$gsum += $record['sumein'];
			$rows[] = '<span style="color: '.$record['color'].'"><i class="fas fa-square"></i></span> '.$record['katbez'];
			$rows[] = $record['sumein'] == null ? "" : number_format($record['sumein'], 2, ',', '.')." €";
			$records[] = $rows;
		}

		$output = array(
			"draw" => intval($_POST["draw"]),
			"iTotalRecords" => count($records),
			"iTotalDisplayRecords" => count($records),
			"data" => $records,
			"gsum" => number_format($gsum, 2, ',', '.')." €",
		);

		echo json_encode($output);
	}


	public function listAus($mandant, $periode){
		$sqlQuery = "SELECT katbez, SUM(ausgang) as sumaus, katart, color FROM tblKassa 
					LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant
					WHERE mandant = :mandant AND periode = :periode  
					GROUP BY katbez, katart, color 
					HAVING katart != 3 AND sumaus != ''";

		if(!empty($_POST["order"])){
			$sqlQuery .= ' ORDER BY '.($_POST['order']['0']['column'] + 1).' '.$_POST['order']['0']['dir'];
		} else {
			$sqlQuery .= ' ORDER BY katbez ASC';
		}

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
		$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$gsum = 0;
		$records = array();

		foreach ($result as $record) {
			$rows = array();
			$gsum += $record['sumaus'];
			$rows[] = '<span style="color: '.$record['color'].'"><i class="fas fa-square"></i></span> '.$record['katbez'];
			$rows[] = $record['sumaus'] == null ? "" : number_format($record['sumaus'], 2, ',', '.')." €";
			$records[] = $rows;
		}

		$output = array(
			"draw" => intval($_POST["draw"]),
			"iTotalRecords" => count($records),
			"iTotalDisplayRecords" => count($records),
			"data" => $records,
			"gsum" => number_format($gsum, 2, ',', '.')." €",
		);

		echo json_encode($output);
	}

	public function listKategorie($mandant){
		$sqlQuery = "SELECT katbez, katart, color, katid FROM tblKategorie 
					WHERE katmandant = :mandant";

		if (!empty($_POST["order"])) {
			$sqlQuery .= ' ORDER BY ' . ($_POST['order']['0']['column'] + 1) . ' ' . $_POST['order']['0']['dir'];
		} else {
			$sqlQuery .= ' ORDER BY katart, katbez ASC';
		}

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$records = array();
		foreach ($result as $record) {
			$rows = array();

			if ($record['katart'] == 1) {
				$strKatArt = "<span style='font-weight: bold; color: #008c23'>E</span>";
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
			"draw" => intval($_POST["draw"]),
			"iTotalRecords" => count($records),
			"iTotalDisplayRecords" => count($records),
			"data" => $records,
		);

		echo json_encode($output);
	}


	public function listProjekte($mandant){
		$sqlQuery = "SELECT * FROM tblProjekt 
					WHERE pmandant = :mandant";

		if (!empty($_POST["order"])) {
			$sqlQuery .= ' ORDER BY ' . ($_POST['order']['0']['column'] + 1) . ' ' . $_POST['order']['0']['dir'];
		} else {
			$sqlQuery .= ' ORDER BY projektname ASC';
		}

		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$records = array();
		foreach ($result as $record) {
			$rows = array();

			$rows[] = '<span style="color: '.$record['pcolor'].'"><i class="fas fa-square"></i></span> '.$record['projektname'];
			$rows[] = '<button type="button" name="updatepro" id="'.$record["pid"].'" class="btn btn-warning btn-sm update"><i class="far fa-edit"></i></button>';
			$records[] = $rows;
		}

		$output = array(
			"draw" => intval($_POST["draw"]),
			"iTotalRecords" => count($records),
			"iTotalDisplayRecords" => count($records),
			"data" => $records,
		);

		echo json_encode($output);
	}

	
	public function getChart($einaus, $mandant, $periode){
		$sqlQuery = "SELECT SUM($einaus) as summe, katbez, katart, color FROM tblKassa 
					LEFT JOIN tblKategorie on tblKassa.kat = tblKategorie.katid AND tblKassa.mandant = tblKategorie.katmandant
					WHERE mandant = :mandant AND periode = :periode  
					GROUP BY katbez, katart, color
					HAVING summe IS NOT NULL AND katart != 3";
		
		$stmt = $this->conn->prepare($sqlQuery);
		$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
		$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

		$records = array();
		$datax = array();
		$labelx = array();
		$color = array();

		foreach ($result as $record) {
			$datax[] = $record['summe'];
			$labelx[] = $record['katbez'];
			$color[] = $record['color'];
		}

		$output = array(
			"labels" => $labelx,
			"data" => $datax,
			"color" => $color
		);

		echo json_encode($output);
	}

	
	public function getRecord(){
		if ($this->id) {
			$sqlQuery = "SELECT * FROM " . $this->recordsTable . " WHERE id = :id";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
			$stmt->execute();
			$record = $stmt->fetch(PDO::FETCH_ASSOC);
			echo json_encode($record);
		}
	}


	public function getDsKonten(){
		if ($this->kid) {
			$sqlQuery = "SELECT kid, kontoname, saldostart FROM tblKonten WHERE kid = :kid";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(':kid', $this->kid, PDO::PARAM_INT);
			$stmt->execute();
			$record = $stmt->fetch(PDO::FETCH_ASSOC);
			echo json_encode($record);
		}
	}

	
	public function getDsKategorie(){
		if ($this->katid) {
			$sqlQuery = "SELECT * FROM tblKategorie WHERE katid = :katid";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(':katid', $this->katid, PDO::PARAM_INT);
			$stmt->execute();
			$record = $stmt->fetch(PDO::FETCH_ASSOC);
			echo json_encode($record);
		}
	}


	public function getDsProjekt(){
		if ($this->pid) {
			$sqlQuery = "SELECT * FROM tblProjekt WHERE pid = :pid";
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(':pid', $this->pid, PDO::PARAM_INT);
			$stmt->execute();
			$record = $stmt->fetch(PDO::FETCH_ASSOC);
			echo json_encode($record);
		}
	}


	public function updateRecord(){
		if($this->id) {
			$eingang = str_replace(",", ".", $this->eingang);
			$eingang = $eingang === '' ? null : $eingang;
			$ausgang = str_replace(",", ".", $this->ausgang);
			$ausgang = $ausgang === '' ? null : $ausgang;

			$sqlQuery = "
			UPDATE " . $this->recordsTable . " 
			SET beleg = :beleg, datum = :datum, bezeichnung = :bezeichnung, eingang = :eingang, ausgang = :ausgang, konto = :konto, kat = :kat, projekt = :projekt
			WHERE id = :id";

			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(':beleg', $this->beleg, PDO::PARAM_INT);
			$stmt->bindParam(':datum', $this->datum, PDO::PARAM_STR);
			$stmt->bindParam(':bezeichnung', $this->bezeichnung, PDO::PARAM_STR);
			$stmt->bindParam(':eingang', $eingang, PDO::PARAM_STR);
			$stmt->bindParam(':ausgang', $ausgang, PDO::PARAM_STR);
			$stmt->bindParam(':konto', $this->konto, PDO::PARAM_INT);
			$stmt->bindParam(':kat', $this->kat, PDO::PARAM_INT);
			$stmt->bindParam(':projekt', $this->projekt, PDO::PARAM_INT);
			$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

			if($stmt->execute()){ 
				return true;
			} else {
				return false;
			}
		}
	}


	public function updateKonto(){
		if ($this->id) {
			$saldo = str_replace(",", ".", $this->saldostart);

			$sqlQuery = "
			UPDATE tblKonten 
			SET kontoname = :kontoname, saldostart = :saldostart, saldoaktuell = :saldoaktuell
			WHERE kid = :id";

			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(':kontoname', $this->kontoname, PDO::PARAM_STR);
			$stmt->bindParam(':saldostart', $saldo, PDO::PARAM_STR);
			$stmt->bindParam(':saldoaktuell', $saldo, PDO::PARAM_STR);
			$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

			if ($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


	public function updateKategorie(){
		if ($this->id) {
			$sqlQuery = "
			UPDATE tblKategorie 
			SET katbez_kb = :katbez_kb, katbez = :katbez, katart = :katart, color = :color
			WHERE katid = :id";

			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(':katbez_kb', $this->katbez_kb, PDO::PARAM_STR);
			$stmt->bindParam(':katbez', $this->katbez, PDO::PARAM_STR);
			$stmt->bindParam(':katart', $this->katart, PDO::PARAM_INT);
			$stmt->bindParam(':color', $this->color, PDO::PARAM_STR);
			$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

			if ($stmt->execute()) { 
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}


	public function updateProjekt(){
		if ($this->id) {
			$sqlQuery = "
			UPDATE tblProjekt 
			SET projektname = :projektname, projekt_kb = :projekt_kb, pcolor = :pcolor
			WHERE pid = :id";

			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(':projektname', $this->projektname, PDO::PARAM_STR);
			$stmt->bindParam(':projekt_kb', $this->projekt_kb, PDO::PARAM_STR);
			$stmt->bindParam(':pcolor', $this->pcolor, PDO::PARAM_STR);
			$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

			if ($stmt->execute()) { 
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	
	public function addRecord($mandant, $periode){
		if($this->beleg) {
			$eingang = str_replace(",", ".", $this->eingang);
			$eingang = $eingang === '' ? null : $eingang;
			$ausgang = str_replace(",", ".", $this->ausgang);
			$ausgang = $ausgang === '' ? null : $ausgang;

			$sqlQuery = "
			INSERT INTO " . $this->recordsTable . "(`mandant`, `periode`, `beleg`, `datum`, `bezeichnung`, `eingang`, `ausgang`, `konto`, `kat`, `projekt`)
			VALUES(:mandant, :periode, :beleg, :datum, :bezeichnung, :eingang, :ausgang, :konto, :kat, :projekt)";

			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
			$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
			$stmt->bindParam(':beleg', $this->beleg, PDO::PARAM_INT);
			$stmt->bindParam(':datum', $this->datum, PDO::PARAM_STR);
			$stmt->bindParam(':bezeichnung', $this->bezeichnung, PDO::PARAM_STR);
			$stmt->bindParam(':eingang', $eingang, PDO::PARAM_STR);
			$stmt->bindParam(':ausgang', $ausgang, PDO::PARAM_STR);
			$stmt->bindParam(':konto', $this->konto, PDO::PARAM_INT);
			$stmt->bindParam(':kat', $this->kat, PDO::PARAM_INT);
			$stmt->bindParam(':projekt', $this->projekt, PDO::PARAM_INT);

			if($stmt->execute()){
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function deleteRecord(){
		if ($this->id) {
			$sqlQuery = "DELETE FROM " . $this->recordsTable . " WHERE id = :id";

			$stmt = $this->conn->prepare($sqlQuery);
			$this->id = htmlspecialchars(strip_tags($this->id));
			$stmt->bindParam(':id', $this->id, PDO::PARAM_INT);

			if ($stmt->execute()) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	
}
?>