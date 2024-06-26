<?php
session_start();
include_once 'config/Database.php';
include_once 'class/Records.php';

$database = new Database();
$db = $database->getConnection();

$record = new Records($db);
$periode = $_SESSION['periode'];
$mandant = $_SESSION['mandant'];

if(!empty($_POST['action']) && $_POST['action'] == 'setSession') {
	if (isset($_POST['periode'])) {
		$_SESSION['periode'] = $_POST['periode'];
	}
}

if(!empty($_POST['action']) && $_POST['action'] == 'listRecords') {
	$record->listRecords($mandant, $periode);
}
if(!empty($_POST['action']) && $_POST['action'] == 'listKontoBuchungen') {
	$record->listKontoBuchungen($mandant, $periode, 43);
}
if(!empty($_POST['action']) && $_POST['action'] == 'listKonten') {
	$record->listKonten($mandant, $periode);
}
if(!empty($_POST['action']) && $_POST['action'] == 'listProjekte') {
	$record->listProjekte($mandant);
}
if(!empty($_POST['action']) && $_POST['action'] == 'listEinAus') {
	$record->listEinAus($mandant, $periode);
}
if(!empty($_POST['action']) && $_POST['action'] == 'listEin') {
	$record->listEin($mandant, $periode);
}
if(!empty($_POST['action']) && $_POST['action'] == 'listAus') {
	$record->listAus($mandant, $periode);
}
if(!empty($_POST['action']) && $_POST['action'] == 'listKategorie') {
	$record->listKategorie($mandant);
}
if(!empty($_POST['action']) && $_POST['action'] == 'addRecord') {	
	$record->beleg 			= $_POST["beleg"];
    $record->datum 			= $_POST["datum"];
    $record->bezeichnung 	= $_POST["bezeichnung"];
	$record->eingang 		= $_POST["eingang"];
	$record->ausgang 		= $_POST["ausgang"];
	$record->konto 			= $_POST["konto"];
	$record->kat 			= $_POST["kat"];
	$record->projekt 		= $_POST["projekt"];
	$record->addRecord($mandant, $periode);
}
if(!empty($_POST['action']) && $_POST['action'] == 'getRecord') {
	$record->id = $_POST["id"];
	$record->getRecord();
}
if(!empty($_POST['action']) && $_POST['action'] == 'getDsKonten') {
	$record->kid = $_POST["kid"];
	$record->getDsKonten();
}
if(!empty($_POST['action']) && $_POST['action'] == 'getDsKategorie') {
	$record->katid = $_POST["katid"];
	$record->getDsKategorie();
}
if(!empty($_POST['action']) && $_POST['action'] == 'getDsProjekt') {
	$record->pid = $_POST["pid"];
	$record->getDsProjekt();
}
if(!empty($_POST['action']) && $_POST['action'] == 'getBeleg') {
	
	$strSQL = "SELECT max(beleg) as lastbeleg FROM tblKassa  
			WHERE periode = :periode AND mandant = :mandant";

	$stmt = $db->prepare($strSQL);
	$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
	$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetch(PDO::FETCH_ASSOC);

	echo $result['lastbeleg'];

}
if(!empty($_POST['action']) && $_POST['action'] == 'getChartE') {
	$record->getChart('eingang', $mandant, $periode);
}

if(!empty($_POST['action']) && $_POST['action'] == 'getChartA') {
	$record->getChart('ausgang', $mandant, $periode);
}

if(!empty($_POST['action']) && $_POST['action'] == 'updateRecord') {
	$record->id 			= $_POST["id"];
	$record->beleg 			= $_POST["beleg"];
    $record->datum 			= $_POST["datum"];
    $record->bezeichnung 	= $_POST["bezeichnung"];
	$record->eingang 		= $_POST["eingang"];
	$record->ausgang 		= $_POST["ausgang"];
	$record->konto 			= $_POST["konto"];
	$record->kat 			= $_POST["kat"];
	$record->projekt 		= $_POST["projekt"];
	$record->updateRecord();			
}

if(!empty($_POST['action']) && $_POST['action'] == 'updateKonto') {
	$record->id 				= $_POST["kid"];
	$record->kontoname 			= $_POST["kontoname"];
	$record->saldostart 		= $_POST["saldostart"];
	$record->updateKonto();					
}

if(!empty($_POST['action']) && $_POST['action'] == 'updateKategorie') {
	$record->id 				= $_POST["katid"];
	$record->katbez_kb 			= $_POST["katbez_kb"];
	$record->katbez 			= $_POST["katbez"];
	$record->katart 			= $_POST["katart"];
	$record->color 				= $_POST["color"];
	$record->updateKategorie();					
}

if(!empty($_POST['projaction']) && $_POST['projaction'] == 'updateProjekt') {
	$record->id 					= $_POST["pid"];
	$record->projektname 			= $_POST["projektname"];
  	$record->projekt_kb 			= $_POST["projekt_kb"];
	$record->pcolor 				= $_POST["pcolor"];
	$record->updateProjekt();	

}

if(!empty($_POST['action']) && $_POST['action'] == 'deleteRecord') {
	$record->id = $_POST["id"];
	$record->deleteRecord();
}

if(!empty($_POST['action']) && $_POST['action'] == 'getPeriode') {
	
	$strSQL = "SELECT * FROM tblPeriode WHERE pemandant = :mandant";

	$stmt = $db->prepare($strSQL);
	$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	foreach($result as $row) {
		if ($row['status'] == 1 && $strSelected == '' && $periode == 0) {
			$strSelected = 'selected';
			$_SESSION['periode'] = $row['peid'];
		} elseif ($periode == $row['peid'] && $strSelected == '') {
			$strSelected = 'selected';
		} else {
			$strSelected = '';
		}
		echo '<option value="'.$row['peid'].'" '.$strSelected.'>'.$row['pebezeichnung'].'</option>';
	}
}


if(!empty($_POST['action']) && $_POST['action'] == 'getKonto') {
	
	$strSQL = "SELECT * FROM tblKonten WHERE kmandant = :mandant AND kperiode = :periode";

	$stmt = $db->prepare($strSQL);
	$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
	$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	echo '<option value=""></option>';
	foreach($result as $row) {
		echo '<option value="'.$row['kid'].'">'.$row['kontoname'].'</option>';
	}

}

if(!empty($_POST['action']) && $_POST['action'] == 'getKategorie') {
	
	$strSQL = "SELECT * FROM tblKategorie WHERE katmandant = :mandant";

	$stmt = $db->prepare($strSQL);
	$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	echo '<option value=""></option>';
	foreach($result as $row) {
		echo '<option value="'.$row['katid'].'" katart="'.$row['katart'].'">'.$row['katbez'].'</option>';
	}

}

if(!empty($_POST['action']) && $_POST['action'] == 'getProjekt') {
	
	$strSQL = "SELECT * FROM tblProjekt WHERE pmandant = :mandant";

	$stmt = $db->prepare($strSQL);
	$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

	echo '<option value=""></option>';
	foreach($result as $row) {
		echo '<option value="'.$row['pid'].'">'.$row['projektname'].'</option>';
	}

}

if(!empty($_POST['action']) && $_POST['action'] == 'getStammPeriode') {

	$strSQL = "SELECT pebezeichnung, vondat, bisdat 
			FROM tblPeriode 
			WHERE peid = :periode AND pemandant = :mandant";

	$stmt = $db->prepare($strSQL);
	$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
	$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);
	$stmt->execute();
	$record = $stmt->fetch(PDO::FETCH_ASSOC);

	header('Content-type: application/json');
	echo json_encode($record);

}

if(!empty($_POST['action']) && $_POST['action'] == 'updateStammPeriode') {
	// $bisdat = '2022-01-01';
	// $vondat = '2022-12-01';
	if (empty($_POST["bisdat"]) || $_POST["bisdat"] == null ) {
		$bisdat = '2022-01-01';
	} else {
		$bisdat = $_POST["bisdat"];
	}

	if (empty($_POST["vondat"]) || $_POST["vondat"] == null ) {
		$vondat = '2022-01-01';
	} else {
		$vondat = $_POST["vondat"];
	}
	$strSQL = "UPDATE tblPeriode SET pebezeichnung = :pebezeichnung, vondat = :vondat, bisdat = :bisdat
			WHERE peid = :periode AND pemandant = :mandant";

	$stmt = $db->prepare($strSQL);
	$stmt->bindParam(':pebezeichnung', $_POST["pebezeichnung"], PDO::PARAM_STR);
	$stmt->bindParam(':vondat', $vondat, PDO::PARAM_STR);
	$stmt->bindParam(':bisdat', $bisdat, PDO::PARAM_STR);
	$stmt->bindParam(':periode', $periode, PDO::PARAM_INT);
	$stmt->bindParam(':mandant', $mandant, PDO::PARAM_INT);

	if($stmt->execute()){ 
		return true;
	} else {
		return false;
	}

}

?>