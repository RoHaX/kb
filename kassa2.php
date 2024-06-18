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
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js" integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="js/ajax.js"></script>	
<style>
body {
  font-size: .9rem;
}


/*
 * Sidebar
 */

.sidebar {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  z-index: 100; /* Behind the navbar */
  padding: 48px 0 0; /* Height of navbar */
  box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

@media (max-width: 767.98px) {
  .sidebar {
    top: 5rem;
  }
}

.sidebar-sticky {
  position: relative;
  top: 0;
  height: calc(100vh - 48px);
  padding-top: .5rem;
  overflow-x: hidden;
  overflow-y: auto; /* Scrollable contents if viewport is shorter than content. */
}

.sidebar .nav-link {
  font-weight: 500;
  color: #333;
}

.sidebar .nav-link .feather {
  margin-right: 4px;
  color: #727272;
}

.sidebar .nav-link.active {
  color: #2470dc;
}

.sidebar .nav-link:hover .feather,
.sidebar .nav-link.active .feather {
  color: inherit;
}

.sidebar-heading {
  font-size: .75rem;
  text-transform: uppercase;
}

/*
 * Navbar
 */

.navbar-brand {
  padding-top: .75rem;
  padding-bottom: .75rem;
  font-size: 1rem;
  background-color: rgba(0, 0, 0, .25);
  box-shadow: inset -1px 0 0 rgba(0, 0, 0, .25);
}

.navbar .navbar-toggler {
  top: .25rem;
  right: 1rem;
}

.navbar .form-control {
  padding: .75rem 1rem;
  border-width: 0;
  border-radius: 0;
}

.form-control-dark {
  color: #fff;
  background-color: rgba(255, 255, 255, .1);
  border-color: rgba(255, 255, 255, .1);
}

.form-control-dark:focus {
  border-color: transparent;
  box-shadow: 0 0 0 3px rgba(255, 255, 255, .25);
}

</style>
</head>
<body class="">

<!--Main Navigation-->
<header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
	<a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">Kassabuch</a>
	<button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	</button>
	<select class="form-select selectpicker" id="periode" name="periode">
	</select>					
	<div class="navbar-nav">
		<div class="nav-item text-nowrap">
			<a class="nav-link px-3" href="index.php">abmelden</a>
		</div>
	</div>
</header>

<div class="container-fluid">
  <div class="row">
    <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
      <div class="position-sticky pt-3">
        <ul class="nav flex-column">
          <li class="nav-item">
            <a class="nav-link active" aria-current="page" href="#">
              <i class="fas fa-balance-scale-left"></i>
              Dashboard
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="defaults.php">
              <i class="fas fa-money-check"></i>
              Berichte
            </a>
          </li>
        </ul>
      </div>
    </nav>

    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">

      <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h2 class="h3">Kassabuch</h2>
        <div class="btn-toolbar mb-2 mb-md-0">
          <div class="btn-group me-2">
          </div>
						<input class="form-check-input" id="flexCheckChecked" type="checkbox" data-bs-toggle="collapse" data-bs-target="#einnahmenausgaben" aria-expanded="true" aria-controls="einnahmenausgaben" value="" checked>
						<label class="form-check-label" for="flexCheckChecked">
							Übersicht anzeigen
						</label>
						<button class="btn btn-outline-success" type="button" name="print" id="print">drucken</button>
						<button class="btn btn-success" type="button" name="add" id="addRecord">neue Buchung</button>
						<a href="defaults.php" class="btn btn-primary" role="button" >Einstellungen</a>
						<a href="index.php" class="btn btn-warning" role="button" >abmelden</a>
        </div>
      </div>
	  




		<div class="collapse show" id="einnahmenausgaben">
			<div class="row">
				<div class="col-md-7" id="printeinaus">

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
					</div>
					<div class="row">
						<div class="col-md-4">
							<canvas id="chart_e"></canvas>
							<br><br>
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
					</div>
					<div class="row">
						<div class="col-md-4">
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

	</main>
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
	


</body>

    <script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>

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
	  
	/*  

      const ctx2 = document.getElementById("charteinnahmen").getContext('2d');

      const myChart2 = new Chart(ctx2, {

        type: 'pie',

        data: {

          labels: ["rice", "yam", "tomato", "potato", "beans", "maize", "oil"],

          datasets: [{

            label: 'food Items',

            backgroundColor: 'rgba(161, 198, 247, 1)',

            borderColor: 'rgb(47, 128, 237)',

            data: [30, 40, 20, 50, 80, 90, 20],

          }]

        },

      });

*/
</script>

</html>

