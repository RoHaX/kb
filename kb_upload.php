<?php 
session_start();
include('inc/head.php');
?>
<script src="js/buchungen.js?v=2.0"></script>
        
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
    .box {
        max-width:600px;
        width:100%;
        margin: 0 auto;;
    }
    .selected {
        background-color: #DEF3C4;
    }

  </style>
 </head>
 <body>
<?php 

    include('inc/menu.php');
?>    
  <div class="container">
   <br />
   <h4>Bankdaten import (CSV-Datei)</h4>
   <br />
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
    <div class="row">
        <div class="table-responsive">
            <table class="table table-striped table-bordered" id="data-table">
                <thead>
                    <tr>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

   <form id="upload_csv" method="post" enctype="multipart/form-data">
    <div class="col-md-12">
     <br />
     <label>CSV-Datei zum Upload muss vorab eingespielt werden -> Roman Haselsberger</label>
    </div>  
    <div class="col-md-9">  
        <input type="file" name="csv_file" id="csv_file" accept=".csv" style="margin-top:15px;" /><input type="submit" name="upload" id="upload" value="Upload" style="margin-top:10px;" class="btn btn btn-success" />
    </div>  
   </form>
   <br />
   Spalten: <span id="col_vis"></span>
   <br />
   <button id="button">Delete selected row</button>
   <br />
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
			<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">abbrechen</button>
			<input type="submit" name="save" id="save" class="btn btn-info" value="Speichern" />
			<input type="hidden" name="id" id="id" />
			<input type="hidden" name="action" id="action" value="" />
		  </div>
		</div>
		</form>
	  </div>
	</div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>

