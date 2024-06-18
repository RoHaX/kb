<?php 
session_start();
include('inc/head.php');
?>

	<script src="js/ajax.js?v=2.0"></script>
    <title>Kassabuch online</title>
		<style>
			body {
				font-size: .9rem;
			}
			.table-sm > :not(caption) > * > * {
				padding: .1rem .1rem;
			}
			.table > :not(:first-child) {
			    border-top: 2px solid currentColor;
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
				<li class="nav-item">
					<a class="nav-link" href="kb_upload.php">Bankdaten import</a>
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
				<h3>Kassabuch</h3>
			</div>
			<div class="col-md-2">
			</div>
			<div class="col-md-4">
				<div class="form-check form-check-inline form-switch">
					<input class="form-check-input" id="flexCheckChecked" type="checkbox" data-bs-toggle="collapse" data-bs-target="#einnahmenausgaben" aria-expanded="true" aria-controls="einnahmenausgaben" value="" checked>
					<label class="form-check-label" for="flexCheckChecked">
						Übersicht anzeigen
					</label>
				</div>
			</div>
			<div class="col-md-4">
				<button class="btn btn-success" type="button" name="add" id="addRecord">neue Buchung</button>
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
					<div class="col-md-5 p-4">
						<canvas id="chart_e"></canvas>
					</div>
				</div>
				<div class="row">
					<div class="col-md-7">
						<table id="lstAus" class=" table table-sm table-hover">
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
					<div class="col-md-5 p-4">
						<canvas id="chart_a"></canvas>
					</div>
				</div>
			</div>
			<div class="col-md-5">
				<table id="lstKonten" class=" table table-sm table-hover">
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
				<table id="lstEinAus" class=" table table-sm table-hover">
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
	</div>
	
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
					<th>Konto</th>					
					<th>Kategorie</th>					
					<th>Projekt</th>					
					<th></th>
					<th></th>					
				</tr>
			</thead>
		</table>	  
	</div>

	<a href="buchungen_pdf.php" target="_blank" class="btn btn-secondary btn-sm">PDF-Download</a>
	<a href="jahresbericht_pdf.php" target="_blank" class="btn btn-secondary btn-sm">Jahresbericht - PDF</a>
	</div>	
	<div class="row">&nbsp;</div>

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
				<div class="form-group">
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
			<button type="button" id="btnBeleg" class="btn btn-outline-secondary"><i class="fas fa-print"></i>&nbsp;Beleg drucken</button>
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">abbrechen</button>
			<input type="submit" name="save" id="save" class="btn btn-info" value="Speichern" />
			<input type="hidden" name="id" id="id" />
			<input type="hidden" name="action" id="action" value="" />
		  </div>
		</div>
		</form>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
	const ctx = document.getElementById("chart_e").getContext('2d');
	const myChartE = new Chart(ctx, {
        type: 'pie',
        data: {
          labels: ["JHV", "Basar", "Ausflug",
          "Trenkwalder", "Elternsp.", "VS", "saturday"],
          datasets: [{
            label: 'Einnahmen',
            backgroundColor: ['#6cd900', '#2db300', '#40ff00', '#86b300', '#00d936', '#00d936'],
            borderColor: '#fff',
            data: [3000, 4000, -2000, 5000, 8000, 9000, 2000],
          }]
        },
		options: {
			plugins: {
				legend: {
					display: false,
				}
			}
		},
      });
	  
	const ctxa = document.getElementById("chart_a").getContext('2d');
	const myChartA = new Chart(ctxa, {
        type: 'pie',
        data: {
          labels: ["JHV", "Basar", "Ausflug",
          "Trenkwalder", "Elternsp.", "VS", "saturday"],
          datasets: [{
            label: 'Ausgaben',
            backgroundColor: ['#ff99b3', '#ff73b9', '#ff4c79', '#b3002d', '#ff99b3', '#ff73dc'],
            borderColor: '#fff',
            data: [3000, 4000, -2000, 5000, 8000, 9000, 2000],
          }]
        },
		options: {
			plugins: {
				legend: {
					display: false,
				}
			}
		},
	});
	  
</script>	
</html>