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
		<script src="js/defaults.js"></script>	
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
	  <!-- Content here -->
	  <div class="row">
			<div class="col-md-2">
				<h3>Stammdaten</h3>
			</div>
			<div class="col-md-2">
			</div>
			<div class="col-md-4">
			</div>
	  </div>
		
	
		<div class="row">		
			<div class="col-md-5">
				<h5>Konten</h5>
				<table id="lstKonten" class=" table table-sm">
				<!-- <table id="recordListing" class="table table-bordered table-striped"> -->
					<thead>
						<tr>
							<th>Konto</th>					
							<th>Saldo start </th>					
							<th class="sum">Saldo aktuell</th>
							<th>Kontobewegung</th>
							<th></th>
						</tr>
					</thead>
					<tfoot>
						<tr class='rhfooter'>
							<th>Gesamt</th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
						</tr>
					</tfoot>
				</table>
				<div class="card mt-3" id="Buchungsjahr">
					<div class="card-body">
						<h5 class="card-title">Buchungsperiode</h5>
						<form method="post" id="periode_form">
							<div class="form-group">
								<label for="pebezeichnung" class="control-label" style="font-weight: bold; color: #b3002d">Bezeichnung</label>							
								<input type="text" class="form-control"  id="pebezeichnung" name="pebezeichnung" value="">
							</div>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label for="datum" class="control-label">Datum von</label>							
										<input type="date" class="form-control" id="vondat" name="vondat" placeholder="datum" value="" required>							
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="datum" class="control-label">Datum bis</label>							
										<input type="date" class="form-control" id="bisdat" name="bisdat" placeholder="datum" value="" required>							
									</div>	   	
								</div>
								<div class="col-md-4">
									<br>
									<input type="button" name="save_pestamm" id="save_pestamm" class="btn btn-info" value="Speichern" />
								</div>
							</div>
						</form>						

					</div>
				</div>
			</div>
			<div id="test" class="col-md-4">
				<h5>Kostenstellen / Kategorie</h5>
				<table id="lstKategorie" class=" table table-sm">
				<!-- <table id="recordListing" class="table table-bordered table-striped"> -->
					<thead>
						<tr>
							<th>Bezeichnung</th>					
							<th>Art</th>					
							<th></th>					
						</tr>
					</thead>
				</table>
			</div>
			<div id="test" class="col-md-3">
				<h5>Projekte</h5>
				<table id="lstProjekte" class=" table table-sm">
				<!-- <table id="recordListing" class="table table-bordered table-striped"> -->
					<thead>
						<tr>
							<th>Bezeichnung</th>								
							<th></th>
						</tr>
					</thead>
				</table>
			</div>
		</div>  

	</div>	

	<!-- START Modal Kategorie -->
	<div class="modal fade" id="kontModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	  <div class="modal-dialog">
	  <form method="post" id="frmKonto">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="staticBackdropLabel">Konto bearbeiten</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body">
			<div class="row">
			  <div class="col-md-8">
				<div class="form-group">
					<label for="kontoname" class="control-label">Kontoname</label>
					<input type="text" class="form-control" id="kontoname" name="kontoname" placeholder="Kontoname" required>			
				</div>
			  </div>
			  <div class="col-md-4">
				<div class="form-group">
					<label for="saldostart" class="control-label">Saldostart</label>							
					<input type="text" class="form-control" id="saldostart" name="saldostart" placeholder="0" required>							
				</div>	   	
			  </div>
			</div>
		  </div>
		  <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">abbrechen</button>
				<input type="submit" name="save" id="save" class="btn btn-info" value="Speichern" />
				<input type="hidden" name="kid" id="kid" />
				<input type="hidden" name="action" id="kontaction" value="" />
		  </div>
		</div>
		</form>
	  </div>
	</div>		
	<!-- END Modal Konten -->
	
	<!-- START Modal Kategorie -->
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
			  <div class="col-md-8">
				<div class="form-group">
					<label for="katbez" class="control-label">Bezeichnung</label>
					<input type="text" class="form-control" id="katbez" name="katbez" placeholder="Bezeichnung" required>			
				</div>
			  </div>
			  <div class="col-md-4">
				<div class="form-group">
					<label for="katbez_kb" class="control-label">Kurzbez.(10 Z)</label>							
					<input type="text" class="form-control" id="katbez_kb" name="katbez_kb" placeholder="katbez_kb" required>							
				</div>	   	
			  </div>
			</div>
			<div class="row">
			  <div class="col-md-4">
				<div class="form-group">
					<label for="katart" class="control-label">Eingang/Ausgang</label>							
					<select id="katart" class="form-control" name="katart" >
						<option value="1" >Eingang</option>
						<option value="2" >Ausgang</option>
						<option value="3" >Wechselgeld</option>
					</select>
				</div>
			  </div>
			  <div class="col-md-4">
				<div class="form-group">
					<label for="color" class="control-label">Farbe</label>							
					<select id="color" class="form-control" name="color" >
					
						<option value="#ffffe6" style="background-color: #ffffe6">#ffffe6</option>
						<option value="#008000" style="background-color: #008000">#008000</option>
						<option value="#98fb98" style="background-color: #98fb98">#98fb98</option>
						<option value="#90ee90" style="background-color: #90ee90">#90ee90</option>
						<option value="#8fbc8f" style="background-color: #8fbc8f">#8fbc8f</option>
						<option value="#adff2f" style="background-color: #adff2f">#adff2f</option>
						<option value="#32cd32" style="background-color: #32cd32">#32cd32</option>
						<option value="#006400" style="background-color: #006400">#006400</option>
						<option value="#d0f0c0" style="background-color: #d0f0c0">#d0f0c0</option>
						<option value="#e9ffdb" style="background-color: #e9ffdb">#e9ffdb</option>
						<option value="#c5e384" style="background-color: #c5e384">#c5e384</option>
						<option value="#8fbc8f" style="background-color: #8fbc8f">#8fbc8f</option>
						<option value="#00d9a3" style="background-color: #00d9a3">#00d9a3</option>
						<option value="#008c69" style="background-color: #008c69">#008c69</option>
						<option value="#96e2cd" style="background-color: #96e2cd">#96e2cd</option>
						<option value="#73ffb9" style="background-color: #73ffb9">#73ffb9</option>
						
						<option value="#ffcccc" style="background-color: #ffcccc">#ffcccc</option>
						<option value="#ffe6ff" style="background-color: #ffe6ff">#ffe6ff</option>
						<option value="#fff2e6" style="background-color: #fff2e6">#fff2e6</option>
						<option value="#ff9999" style="background-color: #ff9999">#ff9999</option>
						<option value="#ffb3ff" style="background-color: #ffb3ff">#ffb3ff</option>
						<option value="#ffcc99" style="background-color: #ffcc99">#ffcc99</option>
						<option value="#ff0000" style="background-color: #ff0000">#ff0000</option>
						<option value="#ffa07a" style="background-color: #ffa07a">#ffa07a</option>
						<option value="#f08080" style="background-color: #f08080">#f08080</option>
						<option value="#fa8072" style="background-color: #fa8072">#fa8072</option>
						<option value="#ff6347" style="background-color: #ff6347">#ff6347</option>
						<option value="#b22222" style="background-color: #b22222">#b22222</option>
						<option value="#ee82ee" style="background-color: #ee82ee">#ee82ee</option>
						<option value="#c71585" style="background-color: #c71585">#c71585</option>
						<option value="#8b008b" style="background-color: #8b008b">#8b008b</option>
					</select>
				</div>
			  </div>
				
			</div>
		  </div>
		  <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">abbrechen</button>
				<input type="submit" name="save" id="save" class="btn btn-info" value="Speichern" />
				<input type="hidden" name="katid" id="katid" />
				<input type="hidden" name="action" id="kataction" value="" />
		  </div>
		</div>
		</form>
	  </div>
	</div>		
	<!-- END Modal Kategorie -->
	
	<!-- START Modal Projekte -->
	<div class="modal fade" id="projModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
	  <div class="modal-dialog">
	  <form method="post" id="frmProj">
		<div class="modal-content">
		  <div class="modal-header">
			<h5 class="modal-title" id="staticBackdropLabel">Projekt bearbeiten</h5>
			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
		  </div>
		  <div class="modal-body">
			<div class="row">
			  <div class="col-md-8">
				<div class="form-group">
					<label for="projektname" class="control-label">Bezeichnung</label>
					<input type="text" class="form-control" id="projektname" name="projektname" placeholder="Bezeichnung" required>			
				</div>
			  </div>
			  <div class="col-md-4">
				<div class="form-group">
					<label for="projekt_kb" class="control-label">Kurzbez.(10 Z)</label>							
					<input type="text" class="form-control" id="projekt_kb" name="projekt_kb" placeholder="projekt_kb" required>							
				</div>	   	
			  </div>
			</div>
			<div class="row">
			  <div class="col-md-4">
				<div class="form-group">
					<label for="pcolor" class="control-label">Farbe</label>							
					<select id="pcolor" class="form-control" name="pcolor" >
					
						<option value="#ffffe6" style="background-color: #ffffe6">#ffffe6</option>
						<option value="#008000" style="background-color: #008000">#008000</option>
						<option value="#98fb98" style="background-color: #98fb98">#98fb98</option>
						<option value="#90ee90" style="background-color: #90ee90">#90ee90</option>
						<option value="#8fbc8f" style="background-color: #8fbc8f">#8fbc8f</option>
						<option value="#adff2f" style="background-color: #adff2f">#adff2f</option>
						<option value="#32cd32" style="background-color: #32cd32">#32cd32</option>
						<option value="#006400" style="background-color: #006400">#006400</option>
						<option value="#d0f0c0" style="background-color: #d0f0c0">#d0f0c0</option>
						<option value="#e9ffdb" style="background-color: #e9ffdb">#e9ffdb</option>
						<option value="#c5e384" style="background-color: #c5e384">#c5e384</option>
						<option value="#8fbc8f" style="background-color: #8fbc8f">#8fbc8f</option>
						<option value="#00d9a3" style="background-color: #00d9a3">#00d9a3</option>
						<option value="#008c69" style="background-color: #008c69">#008c69</option>
						<option value="#96e2cd" style="background-color: #96e2cd">#96e2cd</option>
						<option value="#73ffb9" style="background-color: #73ffb9">#73ffb9</option>
						
						<option value="#ffcccc" style="background-color: #ffcccc">#ffcccc</option>
						<option value="#ffe6ff" style="background-color: #ffe6ff">#ffe6ff</option>
						<option value="#fff2e6" style="background-color: #fff2e6">#fff2e6</option>
						<option value="#ff9999" style="background-color: #ff9999">#ff9999</option>
						<option value="#ffb3ff" style="background-color: #ffb3ff">#ffb3ff</option>
						<option value="#ffcc99" style="background-color: #ffcc99">#ffcc99</option>
						<option value="#ff0000" style="background-color: #ff0000">#ff0000</option>
						<option value="#ffa07a" style="background-color: #ffa07a">#ffa07a</option>
						<option value="#f08080" style="background-color: #f08080">#f08080</option>
						<option value="#fa8072" style="background-color: #fa8072">#fa8072</option>
						<option value="#ff6347" style="background-color: #ff6347">#ff6347</option>
						<option value="#b22222" style="background-color: #b22222">#b22222</option>
						<option value="#ee82ee" style="background-color: #ee82ee">#ee82ee</option>
						<option value="#c71585" style="background-color: #c71585">#c71585</option>
						<option value="#8b008b" style="background-color: #8b008b">#8b008b</option>
					</select>
				</div>
			  </div>
				
			</div>
		  </div>
		  <div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">abbrechen</button>
				<input type="submit" name="save" id="save" class="btn btn-info" value="Speichern" />
				<input type="hidden" name="pid" id="pid" />
				<input type="hidden" name="projaction" id="projaction" value="" />
		  </div>
		</div>
		</form>
	  </div>
	</div>		
	<!-- END Modal Projekte -->	
	
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>


</html>