<?php 
session_start();
if (isset($_GET['periode'])) {
	//$_SESSION['periode'] = $_GET['periode'];
}
if (isset($_GET['mandant'])) {
	//$_SESSION['mandant'] = $_GET['mandant'];
}

if (!isset($_SESSION['periode'])) {
	header('Location: index.php');
}

if (!isset($_SESSION['mandant'])) {
	header('Location: index.php');
}


include_once 'config/Database.php';
include('inc/header.php');
?>
<title>Kassabuch</title>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap5.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap5.min.css" />
<script src="js/ajax.js"></script>	
</head>
<body class="">

<div class="container-xxl">	
	<h2>Kassabuch</h2>	
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-3">
					<select class="form-select selectpicker" id="periode" name="periode">
					</select>					
				</div>
				<div class="col-md-6" align="right">
					<p>

					<div class="form-check form-check-inline form-switch">
						<input class="form-check-input" id="flexCheckChecked" type="checkbox" data-bs-toggle="collapse" data-bs-target="#einnahmenausgaben" aria-expanded="true" aria-controls="einnahmenausgaben" value="" checked>
						<label class="form-check-label" for="flexCheckChecked">
							Übersicht anzeigen
						</label>
					</div>
<!--
						<button class="btn btn-primary" type="button" >Übersicht ein/aus</button>
 					<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#multiCollapseExample2" aria-expanded="false" aria-controls="multiCollapseExample2">Buchungen</button>
-->
						<button class="btn btn-success" type="button" name="add" id="addRecord">neue Buchung</button>
<!--						<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target=".multi-collapse" aria-expanded="false" aria-controls="multiCollapseExample1 multiCollapseExample2">Toggle both elements</button>
						<button class="btn btn-warning" type="button" name="logout" id="logout" ">abmelden</button>
-->
						<a href="defaults.php" class="btn btn-primary" role="button" >Einstellungen</a>
						<a href="index.php" class="btn btn-warning" role="button" >abmelden</a>
						
					</p>
				</div>
			</div>
		</div>



		<div class="collapse show" id="einnahmenausgaben">
			<div class="row">
				<div class="col-md-7">

					<div class="row">
						<div class="col-md-7">
							<table id="lstEin" class=" table table-sm">
							<!-- <table id="recordListing" class="table table-bordered table-striped"> -->
								<thead>
									<tr>
										<th>Kategorie</th>					
										<th>Eingang </th>					
									</tr>
								</thead>
								<tfoot>
									<tr class='rhfooter'>
										<th>Gesamt</th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="col-md-4">
							<br><br>
							<canvas id="chart_e"></canvas>
						</div>
					</div>
					<div class="row">
						<div class="col-md-7" align="right">
							<table id="lstAus" class=" table table-sm">
								<thead>
									<tr>
										<th>Kategorie</th>					
										<th>Ausgang </th>					
									</tr>
								</thead>
								<tfoot>
									<tr class='rhfooter'>
										<th>Gesamt</th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
						<div class="col-md-4">
							<br><br>
							<canvas id="chart_a"></canvas>
						</div>
					</div>
				</div>
				<div class="col-md-5">
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
					<br><br>
					<table id="lstEinAus" class=" table table-sm">
					<!-- <table id="recordListing" class="table table-bordered table-striped"> -->
						<thead>
							<tr>
								<th>Projekte</th>					
								<th>Eingang </th>					
								<th>Ausgang</th>
							</tr>
						</thead>
					</table>

				</div>
			</div>
		</div>


		<table id="recordListing" class=" table table-striped table-sm" style="width:100%">
		<!-- <table id="recordListing" class="table table-bordered table-striped"> -->
			<thead>
				<tr>
					<th>#</th>
					<th>Datum</th>					
					<th>Bezeichnung</th>					
					<th>Eingang</th>
					<th>Ausgang</th>
					<th>Konto</th>					
					<th>Kategorie</th>					
					<th>Projekt</th>					
					<th></th>
					<th></th>					
				</tr>
			</thead>
		</table>

	<div class="row">
		&nbsp;
	</div>
	<div class="row">
<!-- PDF-Export
		https://datatables.net/extensions/buttons/examples/html5/footer.html
-->		
		<div class="col-md-6">
		</div>
		<div class="col-md-1">
		</div>
		<div class="col-md-5">
		</div>
	</div>
	
	<div class="modal fade" id="recordModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	  <div class="modal-dialog">
	  <form method="post" id="recordForm">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="staticBackdropLabel">Buchung bearbeiten</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body">
			<div class="row">
			  <div class="col-md-4">
				<div class="form-group"
					<label for="beleg" class="control-label">Beleg</label>
					<input type="number" class="form-control" id="beleg" name="beleg" placeholder="Beleg" required>			
				</div>
			  </div>
			  <div class="col-md-8">
				<div class="form-group">
					<label for="datum" class="control-label">Datum</label>							
					<input type="date" class="form-control" id="datum" name="datum" placeholder="datum" required>							
				</div>	   	
			  </div>
			</div>
			<div class="row">
			  <div class="col-md-6">
			  </div>
			  <div class="col-md-6">
			  </div>
			</div>
				<div class="form-group">
					<label for="bezeichnung" class="control-label">Bezeichnung</label>							
					<textarea class="form-control" rows="2" id="bezeichnung" name="bezeichnung"></textarea>	
				</div>	 
			<div class="row">
			  <div class="col-md-6">
				<div class="form-group">
					<label for="eingang" class="control-label" style="font-weight: bold; color: #238c00">Eingang</label>							
					<input type="text" class="form-control"  id="eingang" name="eingang">
				</div>
			  </div>
			  <div class="col-md-6">
				<div class="form-group">
					<label for="ausgang" class="control-label" style="font-weight: bold; color: #b3002d">Ausgang</label>							
					<input type="text" class="form-control"  id="ausgang" name="ausgang">
				</div>
			  </div>
			</div>
				<div class="form-group">
					<label for="konto" class="control-label">Konto</label>							
					<select id="konto" class="form-control" name="konto" >
					</select>

				</div>						
				<div class="form-group">
					<label for="kat" class="control-label">Kategorie</label>	
					<select id="kat" class="form-control" name="kat" >
					</select>
				</div>						
				<div class="form-group">
					<label for="projekt" class="control-label">Projekt</label>							
					<select id="projekt" class="form-control" name="projekt" >
					</select>
				</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">abbrechen</button>
			<input type="submit" name="save" id="save" class="btn btn-info" value="Speichern" />
			<input type="hidden" name="id" id="id" />
			<input type="hidden" name="action" id="action" value="" />
		  </div>
		</div>
		</form>
	  </div>
	</div>	

	<div class="modal fade" id="katModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	  <div class="modal-dialog">
	  <form method="post" id="frmKat">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="staticBackdropLabel">Kategorie bearbeiten</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body">
			<div class="row">
			  <div class="col-md-4">
				<div class="form-group"
					<label for="katbez" class="control-label">Bezeichnung</label>
					<input type="text" class="form-control" id="katbez" name="katbez" placeholder="Bezeichnung" required>			
				</div>
			  </div>
			  <div class="col-md-8">
				<div class="form-group">
					<label for="katbez_kb" class="control-label">Kurzbezeichnung (max. 10 Zeichen)</label>							
					<input type="text" class="form-control" id="katbez_kb" name="katbez_kb" placeholder="katbez_kb" required>							
				</div>	   	
			  </div>
			</div>
			<div class="row">
			  <div class="col-md-4">
				<div class="form-group">
					<label for="color" class="control-label">Eingang/Ausgang</label>							
					<select id="color" class="form-control" name="color" >
						<option value="1" >Eingang</option>
						<option value="2" >Ausgang</option>
						<option value="3" >Wechselgeld</option>
					</select>
				</div>
			  </div>
			  <div class="col-md-8">
				<div class="form-group">
					<label for="color" class="control-label">Farbe</label>							
					<select id="color" class="form-control" name="color" >
						<option value="1" style="background-color: #00bfff">#00bfff</option>
						<option value="2" style="background-color: #ff26ff">#ff26ff</option>
						<option value="3" style="background-color: #00b3b2">#00b3b2</option>
					</select>
				</div>
			  </div>
				
			</div>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">abbrechen</button>
			<input type="submit" name="save" id="save" class="btn btn-info" value="Speichern" />
			<input type="hidden" name="kid" id="kid" />
			<input type="hidden" name="action" id="action" value="" />
		  </div>
		</div>
		</form>
	  </div>
	</div>	
	

<?php include('inc/footer.php');?>