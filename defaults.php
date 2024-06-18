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

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>

</head>

<body class="">

	<div class="container-xxl">
		<h2>Kassabuch</h2>
		<div class="panel-heading">
			<div class="row">
				<div class="col-md-3">
					<h5>Einstellungen</h5>
				</div>
				<div class="col-md-6" align="right">
					<p>
						<button type="button" id="pdfDownloader">Download</button>
						<a href="kassa.php" class="btn btn-primary" role="button">Kassabuch</a>
						<a href="index.php" class="btn btn-warning" role="button">abmelden</a>
					</p>
				</div>
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
			<div id="test" class="col-md-4">
				<h5>Kategorie</h5>
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
										<select id="katart" class="form-control" name="katart">
											<option value="1">Eingang</option>
											<option value="2">Ausgang</option>
											<option value="3">Wechselgeld</option>
										</select>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label for="color" class="control-label">Farbe</label>
										<select id="color" class="form-control" name="color">

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
							<input type="hidden" name="action" id="action" value="" />
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>



	<script src="https://cdn.datatables.net/buttons/2.0.1/js/dataTables.buttons.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/2.0.1/js/buttons.bootstrap5.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
	<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
	<script src="js/defaults.js"></script>
</body>

</html>