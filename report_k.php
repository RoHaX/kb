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
		<script src="js/dataTables.bootstrap5.min.js"></script>		
		<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script src="js/ajax_k.js"></script>
		<title>Kassabuch online</title>
		<style>
			body {
				font-size: .9rem;
			}
			.table-sm > :not(caption) > * > * {
				padding: .1rem .1rem;
			}
		</style>
  </head>
  <body>
	<nav class="navbar navbar-expand-lg navbar-light bg-light">
	  <div class="container">
		<a class="navbar-brand" href="#">RH_KB</a>
		<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		  <span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarSupportedContent">
		  <ul class="navbar-nav me-auto mb-2 mb-lg-0">
			<li class="nav-item">
				<select class="form-select selectpicker" id="periode" name="periode">
				</select>													
			</li>
			<li class="nav-item">
			  <a class="nav-link active" aria-current="page" href="kassa.php">Buchungen</a>
			</li>
			<li class="nav-item">
			  <a class="nav-link active" aria-current="page" href="report.php">Berichte</a>
			</li>
			<li class="nav-item">
			  <a class="nav-link" href="stammdaten.php">Stammdaten</a>
			</li>
		  </ul>
		  <a class="btn btn-outline-success" aria-current="page" href="index.php">abmelden</a>
		</div>
	  </div>
	</nav>

	<div class="container">
		<div class="row">
			<div class="col-md-8">
			</div>
			<div class="col-md-2">
				<button class="btn btn-outline-success" type="button" name="print" id="print">drucken</button>
			</div>
		</div>
	</div>
	
	<div class="container shadow-lg p-3 mb-5 bg-white rounded" style="width: 595px; font-size: .8rem;" id="printeinaus">
	  <!-- Content here -->
	  <div class="row" id="printsection1">
			<div class="col-md-12">
				<h5>Kassabericht - <span class="text-muted" id="txtperiode"></span></h5>
			</div>
	  </div>
		<div class="row" id="printsection2">
			<div class="col-md-12">
				<table id="lstKonten" class=" table table-sm">
				<!-- <table id="recordListing" class="table table-bordered table-striped"> -->
					<thead>
						<tr>
							<th>Konto</th>					
							<th>Saldo start </th>					
							<th class="sum">Saldo aktuell</th>
							<th>Kontobewegung</th>
						</tr>
					</thead>
					<tfoot>
						<tr class='rhfooter'>
							<th>Gesamt</th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</tfoot>
				</table>

			</div>		
		</div>		
	
		<div id="pagebrk3" class=""></div>

		<div class="row">		
			<table id="recordListing" class=" table table-striped table-sm responsive table-hover">
			<!-- <table id="recordListing" class="table table-bordered table-striped"> -->
				<thead>
					<tr>
						<th>#</th>
						<th>Datum</th>					
						<th>Bezeichnung</th>					
						<th>Eingang</th>
						<th>Ausgang</th>
						<th>Kategorie</th>					
					</tr>
				</thead>
			</table>	  
		</div>
			
		<!-- Seitenwechsel 
			<div class="html2pdf__page-break"></div>
		-->
		<div id="pagebrk4" class=""></div>
		
		
		<div id="pagebrk5" class=""></div>
		<!-- 		<div class="html2pdf__page-break"></div> -->
		
		<div class="row mt-4" id="printsection5">
			<table id="lstEinAus" class=" table table-sm">
			<!-- <table id="recordListing" class="table table-bordered table-striped"> -->
				<thead>
					<tr>
						<th>Projekte</th>					
						<th>Eingang </th>					
						<th>Ausgang</th>
						<th>Saldo</th>
					</tr>
				</thead>
			</table>
		</div>
		
		
	</div>
		
		


	
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>


</html>