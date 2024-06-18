<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="UTF-8">
    <title>Bootstrap 4 Responsive Datatable and Export to PDF, CSV</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.1/css/bootstrap.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/dataTables.bootstrap4.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.5.2/css/buttons.bootstrap4.min.css">
	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css">
</head>
<body>
	<table id="example" class=" table table-sm">
	<!-- <table id="recordListing" class="table table-bordered table-striped"> -->
		<thead>
			<tr>
				<th>Bezeichnung</th>					
				<th>Art</th>					
				<th></th>					
			</tr>
		</thead>
		<tbody>
            <tr>
                <td>Tiger</td>
                <td>Nixon</td>
                <td>System Architect</td>
            </tr>		
		</tbody>
	</table>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.colVis.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js"></script>
    <script>
	$(document).ready(function() {
	/*
	var table = $('#example').DataTable( {
		lengthChange: false,
		buttons: [ 'copy', 'excel', 'csv', 'pdf', 'colvis' ]
	} );
	*/
	 /*
	var table = $('#example').DataTable({
		"language": {
			"lengthMenu": "Zeige _MENU_ Einträge pro Seite",
			"zeroRecords": "keine Datensätze gefunden",
			"info": "Seite _PAGE_ von _PAGES_",
			"infoEmpty": "keine Datensätze gefunden",
			"search":         "Suche:",
			"infoFiltered": "(gefiltert aus _MAX_ Datensätzen)",
			"paginate": {
				"first":      "erste",
				"last":       "letzte",
				"next":       "nächste",
				"previous":   "vorige"
			},
		},
		"lengthChange": false,
		'serverMethod': 'post',		
		"ajax":{
			url:"ajax_action.php",
			type:"POST",
			data:{action:'listKategorie'},
			dataType:"json"
		},
		"pageLength": 25,
		buttons: [ 'copy', 'excel', 'csv', 'pdf', 'colvis' ],
	});	
	*/
/*
	var table = $('#example').DataTable({
		"language": {
			"lengthMenu": "Zeige _MENU_ Einträge pro Seite",
			"zeroRecords": "keine Datensätze gefunden",
			"info": "Seite _PAGE_ von _PAGES_",
			"infoEmpty": "keine Datensätze gefunden",
			"search":         "Suche:",
			"infoFiltered": "(gefiltert aus _MAX_ Datensätzen)",
			"paginate": {
				"first":      "erste",
				"last":       "letzte",
				"next":       "nächste",
				"previous":   "vorige"
			},
		},
		"lengthChange": false,
		'serverMethod': 'post',		
		"ajax":{
			url:"ajax_action.php",
			type:"POST",
			data:{action:'listKategorie'},
			dataType:"json"
		},
		"pageLength": 25,
		buttons: [ 'copy', 'excel', 'csv', 'pdf', 'colvis' ],
		  initComplete: function () {
                table.buttons().container()
                  .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );
              }

	});	
*/
/*
	 table.buttons().container()
	        .appendTo( '#example_wrapper .col-md-6:eq(0)' );

new $.fn.dataTable.Buttons( table, {
    buttons: [
        'copy', 'excel', 'pdf'
    ]
} );
 
table.buttons().container()
    .appendTo( $('.col-sm-6:eq(0)', table.table().container() ) );
	*/


			var table = $('#example').DataTable({
				"lengthChange": false,
				'serverMethod': 'post',		
				"ajax":{
					url:"ajax_action.php",
					type:"POST",
					data:{action:'listKategorie'},
					dataType:"json"
				},
				"pageLength": 25,
				'dom': 'Bfrtip',
				"buttons": [ 'copy', 'excel', 'csv', 'pdf', 'colvis' ],
                "deferRender": true,
                "order": [[1, 'asc']],
            } );
			
			
	} );
     </script>
</body>
</html>
