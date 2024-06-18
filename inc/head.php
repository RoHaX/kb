<?php 
session_start();
if (isset($_GET['periode'])) {
	
}
if (isset($_GET['mandant'])) {
	
}

if (!isset($_SESSION['periode'])) {
	header('Location: index.php');
}

if (!isset($_SESSION['mandant'])) {
	header('Location: index.php');
}


include_once 'config/Database.php';
?>
<!doctype html>
<html lang="de">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
		<link rel="stylesheet" href="css/dataTables.bootstrap5.min.css" />
		<link rel="stylesheet" href="css/style.css">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.13.0/css/all.css" integrity="sha384-Bfad6CLCknfcloXFOyFnlgtENryhrpZCe29RTifKEixXQZ38WheV+i/6YWSzkz3V" crossorigin="anonymous"/>

		<script src="js/jquery-3.6.0.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/dataTables.responsive.min.js"></script>
		<script src="js/dataTables.bootstrap5.min.js"></script>		
        <script type="text/javascript" src="js/bootstrap-confirm-button.min.js"></script>
