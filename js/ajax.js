$(document).ready(function () {
  function hideshowKategorie() {
    var intKatart = 2;

    if ($("#eingang").val() == null || $("#eingang").val() == "") {
      intKatart = 1;
      $("#eingang").prop("disabled", true);
      $("#ausgang").prop("disabled", false);
    } else {
      $("#eingang").prop("disabled", false);
      $("#ausgang").prop("disabled", true);
    }

    if (
      ($("#eingang").val() == null || $("#eingang").val() == "") &&
      ($("#ausgang").val() == null || $("#ausgang").val() == "")
    ) {
      $("#eingang").prop("disabled", false);
      $("#ausgang").prop("disabled", false);
    }

    $("#kat > option").each(function () {
      if ($(this).attr("katart") == intKatart) {
        $(this).hide();
      } else {
        $(this).show();
      }
    });
  }

  function getPeriode() {
    $.post({
      url: "ajax_action.php",
      data: { action: "getPeriode" },
      success: function (data) {
        $("#periode").html(data);
        $("#txtperiode").html($("#periode option:selected").text());
        //$("#txtperiode").html( 'x' );
      },
    });
  }

  function getKonto() {
    $.post({
      url: "ajax_action.php",
      data: { action: "getKonto" },
      success: function (data) {
        $("#konto").html(data);
      },
    });
  }

  function getKategorie() {
    $.post({
      url: "ajax_action.php",
      data: { action: "getKategorie" },
      success: function (data) {
        $("#kat").html(data);
      },
    });
  }

  function getProjekt() {
    $.post({
      url: "ajax_action.php",
      data: { action: "getProjekt" },
      success: function (data) {
        $("#projekt").html(data);
      },
    });
  }

  getPeriode();
  getKategorie();
  getProjekt();
  getKonto();

  function ajax_chart(chart, action, data) {
    var data = data || {};
    $.post({
      url: "ajax_action.php",
      data: { action: action },
      dataType: "json",
      success: function (response) {
        chart.data.labels = response.labels;
        chart.data.datasets[0].backgroundColor = response.color;
        chart.data.datasets[0].data = response.data; // or you can iterate for multiple datasets
        chart.update(); // finally update our chart
      },
    });
  }

  ajax_chart(myChartE, "getChartE");
  ajax_chart(myChartA, "getChartA");

  var record_callback = function () {
    $(".btn-delete-item").btsConfirmButton(
      {
        // msg: "<i class='far fa-trash-alt'></i>löschen?",
        msg: "löschen?",
        timeout: 3000,
        className: "btn-danger",
      },
      function (e) {
        var id = $(this).attr("id");
        deleteRecord(id);
      }
    );
  };

  var dataRecords = $("#recordListing").DataTable({
    language: {
      lengthMenu: "Zeige _MENU_ Einträge pro Seite",
      zeroRecords: "keine Datensätze gefunden",
      info: "Seite _PAGE_ von _PAGES_",
      infoEmpty: "keine Datensätze gefunden",
      search: "Suche:",
      infoFiltered: "(gefiltert aus _MAX_ Datensätzen)",
      paginate: {
        first: "erste",
        last: "letzte",
        next: "nächste",
        previous: "vorige",
      },
    },
    responsive: true,
    lengthChange: true,
    processing: true,
    serverSide: true,
    serverMethod: "post",
    order: [],
    ajax: {
      url: "ajax_action.php",
      type: "POST",
      data: { action: "listRecords" },
      dataType: "json",
    },
    columnDefs: [
      { type: "num-fmt", symbols: "R$", targets: 4 },
      {
        targets: [8, 9],
        orderable: false,
      },
      { className: "green", targets: [3] },
      { className: "red", targets: [4] },
      { width: "10px", targets: [0] },
      { width: "70px", targets: [1] },
      { width: "350px", targets: [2] },
      { responsivePriority: 1, targets: [1, 2, 3, 4, 8] },
      { responsivePriority: 2, targets: [0, 6] },
      { responsivePriority: 3, targets: [5, 7] },
      { responsivePriority: 4, targets: [9] },
    ],
    pageLength: 25,
    initComplete: record_callback,
  });

  /*
buttons: [ 'excel', 'csv', 'pdf'],
buttons : [ {
            extend : 'excel',
            text : 'Export to Excel',
            exportOptions : {
                modifier : {
                    // DataTables core
                    order : 'index',  // 'current', 'applied', 'index',  'original'
                    page : 'all',      // 'all',     'current'
                    search : 'none'     // 'none',    'applied', 'removed'
                }
            }
        } ]

				'dom': 'frtipB',
		"buttons": [ 'copy', 'excel', 'csv', 'pdf', 'colvis' ],
*/

  var dataKonten = $("#lstKonten").DataTable({
    language: {
      lengthMenu: "Zeige _MENU_ Einträge pro Seite",
      zeroRecords: "keine Datensätze gefunden",
      info: "Seite _PAGE_ von _PAGES_",
      infoEmpty: "keine Datensätze gefunden",
      search: "Suche:",
      infoFiltered: "(gefiltert aus _MAX_ Datensätzen)",
      paginate: {
        first: "erste",
        last: "letzte",
        next: "nächste",
        previous: "vorige",
      },
    },
    paging: false,
    searching: false,
    info: false,
    lengthChange: false,
    processing: true,
    ordering: false,
    serverSide: true,
    serverMethod: "post",
    ajax: {
      url: "ajax_action.php",
      type: "POST",
      data: { action: "listKonten" },
      dataType: "json",
    },
    footerCallback: function (tfoot, data, start, end, display) {
      var response = this.api().ajax.json();
      if (response) {
        var $td = $(tfoot).find("th");
        $td.eq(1).html(response["sumstartsaldo"]);
        $td.eq(2).html(response["sumaktsaldo"]);
        $td.eq(3).html(response["sumbewegung"]);
      }
    },
    columnDefs: [
      { type: "num-fmt", symbols: "R$", targets: 2 },
      { className: "rechtsbnd", targets: [1, 2, 3] },
    ],
    pageLength: 25,
  });

  var dataEinAus = $("#lstEinAus").DataTable({
    language: {
      lengthMenu: "Zeige _MENU_ Einträge pro Seite",
      zeroRecords: "keine Datensätze gefunden",
      info: "Seite _PAGE_ von _PAGES_",
      infoEmpty: "keine Datensätze gefunden",
      search: "Suche:",
      infoFiltered: "(gefiltert aus _MAX_ Datensätzen)",
      paginate: {
        first: "erste",
        last: "letzte",
        next: "nächste",
        previous: "vorige",
      },
    },
    paging: false,
    searching: false,
    info: false,
    lengthChange: false,
    processing: true,
    ordering: false,
    serverSide: true,
    serverMethod: "post",
    ajax: {
      url: "ajax_action.php",
      type: "POST",
      data: { action: "listEinAus" },
      dataType: "json",
    },
    columnDefs: [
      { className: "green", targets: [1] },
      { className: "red", targets: [2] },
      { className: "sum_grey", targets: [3] },
    ],
    pageLength: 25,
  });

  var dataEin = $("#lstEin").DataTable({
    language: {
      lengthMenu: "Zeige _MENU_ Einträge pro Seite",
      zeroRecords: "keine Datensätze gefunden",
      info: "Seite _PAGE_ von _PAGES_",
      infoEmpty: "keine Datensätze gefunden",
      search: "Suche:",
      infoFiltered: "(gefiltert aus _MAX_ Datensätzen)",
      paginate: {
        first: "erste",
        last: "letzte",
        next: "nächste",
        previous: "vorige",
      },
    },
    paging: false,
    searching: false,
    info: false,
    lengthChange: false,
    processing: true,
    ordering: true,
    serverSide: true,
    serverMethod: "post",
    ajax: {
      url: "ajax_action.php",
      type: "POST",
      data: { action: "listEin" },
      dataType: "json",
    },
    footerCallback: function (tfoot, data, start, end, display) {
      var response = this.api().ajax.json();
      if (response) {
        var $td = $(tfoot).find("th");
        $td.eq(1).html(response["gsum"]);
      }
    },
    columnDefs: [{ className: "green", targets: [1] }],
    pageLength: 25,
  });

  var dataAus = $("#lstAus").DataTable({
    language: {
      lengthMenu: "Zeige _MENU_ Einträge pro Seite",
      zeroRecords: "keine Datensätze gefunden",
      info: "Seite _PAGE_ von _PAGES_",
      infoEmpty: "keine Datensätze gefunden",
      search: "Suche:",
      infoFiltered: "(gefiltert aus _MAX_ Datensätzen)",
      paginate: {
        first: "erste",
        last: "letzte",
        next: "nächste",
        previous: "vorige",
      },
    },
    paging: false,
    searching: false,
    info: false,
    lengthChange: false,
    processing: true,
    ordering: true,
    serverSide: true,
    serverMethod: "post",
    ajax: {
      url: "ajax_action.php",
      type: "POST",
      data: { action: "listAus" },
      dataType: "json",
    },
    footerCallback: function (tfoot, data, start, end, display) {
      var response = this.api().ajax.json();
      if (response) {
        var $td = $(tfoot).find("th");
        $td.eq(1).html(response["gsum"]);
      }
    },
    columnDefs: [{ className: "red", targets: [1] }],
    pageLength: 25,
  });

  $("#eingang").change(function () {
    hideshowKategorie();
  });

  $("#periode").change(function () {
    $.post({
      url: "ajax_action.php",
      data: { periode: $(this).val(), action: "setSession" },
      success: function (data) {
        dataRecords.ajax.reload(record_callback);
        dataEinAus.ajax.reload();
        dataEin.ajax.reload();
        dataAus.ajax.reload();
        dataKonten.ajax.reload();
        getKonto();
        ajax_chart(myChartE, "getChartE");
        ajax_chart(myChartA, "getChartA");
      },
    });
  });

  $("#ausgang").change(function () {
    hideshowKategorie();
  });

  $("#logout").click(function () {
    //login
    window.location.replace("index.php");
  });

  $("#addRecord").click(function () {
    $.post({
      url: "ajax_action.php",
      data: { action: "getBeleg" },
      success: function (data) {
        $("#recordModal").modal("show");
        $("#recordForm")[0].reset();
        $("#beleg").val(parseInt(data) + 1);
        $(".modal-title").html(
          "<i class='fas fa-money-check'></i></i> neue Buchung"
        );
        $("#action").val("addRecord");
        $("#save").val("speichern");
        $("#eingang").prop("disabled", false);
        $("#ausgang").prop("disabled", false);
        $("#konto option:eq(1)").prop("selected", true);
      },
    });
  });

  $("#lstKategorie").on("click", ".update", function () {
    var kid = $(this).attr("kid");
    $("#katModal").modal("show");
    /*
		var action = 'getRecord';
		$.post({
			url:'ajax_action.php',
			data:{id:id, action:action},
			dataType:"json",
			success:function(data){
				$('#recordModal').modal('show');
				

			}
		})
		*/
  });

  $("#recordListing").on("click", ".update", function () {
    var id = $(this).attr("id");
    var action = "getRecord";
    $.post({
      url: "ajax_action.php",
      data: { id: id, action: action },
      dataType: "json",
      success: function (data) {
        $("#recordModal").modal("show");
        $("#id").val(data.id);
        $("#beleg").val(data.beleg);
        $("#datum").val(data.datum);
        $("#bezeichnung").val(data.bezeichnung);
        $("#eingang").val(data.eingang);
        $("#ausgang").val(data.ausgang);
        $("#konto").val(data.konto);
        $("#kat").val(data.kat);
        $("#projekt").val(data.projekt);
        $(".modal-title").html("<i class='fa fa-plus'></i> bearbeite Buchung");
        $("#action").val("updateRecord");
        $("#save").val("speichern");
        hideshowKategorie();
      },
    });
  });

  $("#recordModal").on("submit", "#recordForm", function (event) {
    event.preventDefault();
    $("#save").attr("disabled", "disabled");
    var formData = $(this).serialize();
    $.post({
      url: "ajax_action.php",
      data: formData,
      success: function (data) {
        $("#recordForm")[0].reset();
        $("#recordModal").modal("hide");
        $("#save").attr("disabled", false);
        dataRecords.ajax.reload(record_callback);
      },
    });
  });

  /*
  $("#recordListing").change(function () {
    $(".btn-delete-item").btsConfirmButton(
      {
        msg: "Confirm Deletion",
        timeout: 3000,
        className: "btn-danger",
      },
      function (e) {
        console.log("Item deleted!");
      }
    );
  });
	*/
  $("#recordModal").on("click", "#btnBeleg", function (event) {
    event.preventDefault();
    // $("#save").attr("disabled", "disabled");
    var formData = $("#recordForm");
    // var dataToSend = formData.find('input[name="beleg"], input[name="datum"], input[name="bezeichnung"], select[name="kat"], select[name="konto"], select[name="projekt"], input[name="eingang"]').serialize();
    // console.log(formData);

    // Textarea-Inhalt und Dropdown-Optionstexte abrufen
    var bezeichnung = encodeURIComponent(
      formData.find('textarea[name="bezeichnung"]').val()
    );
    var katText = encodeURIComponent(
      formData.find('select[name="kat"] option:selected').text()
    );
    var kontoText = encodeURIComponent(
      formData.find('select[name="konto"] option:selected').text()
    );
    var projektText = encodeURIComponent(
      formData.find('select[name="projekt"] option:selected').text()
    );

    // Formulardaten serialisieren und Textarea-Inhalt sowie Dropdown-Optionstexte hinzufügen
    var dataToSend = formData
      .find(
        'input[name="beleg"], input[name="datum"], input[name="eingang"], input[name="ausgang"]'
      )
      .serialize();
    dataToSend +=
      "&bezeichnung=" +
      bezeichnung +
      "&kat=" +
      katText +
      "&konto=" +
      kontoText +
      "&projekt=" +
      projektText;

    window.open("beleg_pdf.php?" + dataToSend);
    /*
    $.post({
      url: "beleg_pdf.php",
      data: formData,
      success: function (data) {
        // $("#recordForm")[0].reset();
        // $("#recordModal").modal("hide");
        // $("#save").attr("disabled", false);
      },
    });
    */
  });
  function deleteRecord(id) {
    var action = "deleteRecord";
    $.post({
      url: "ajax_action.php",
      data: { id: id, action: action },
      success: function (data) {
        dataRecords.ajax.reload(record_callback);
      },
    });
  }
  /*
  $("#recordListing").on("click", ".btn-delete-item", function () {
    var id = $(this).attr("id");
    var action = "deleteRecord";
    if (confirm("Diese Buchung wirklich löschen?")) {
      $.post({
        url: "ajax_action.php",
        data: { id: id, action: action },
        success: function (data) {
          dataRecords.ajax.reload();
        },
      });
    } else {
      return false;
    }
  });
*/
  $("#print").click(function () {
    //var element = document.getElementById('printeinaus');
    var element = document.getElementById("printeinaus");
    /*
	var secHeight1 = document.getElementById('printsection1').offsetHeight;
	var secHeight2 = secHeight1 + document.getElementById('printsection2').offsetHeight;
	var secHeight3 = secHeight2 + document.getElementById('printsection3').offsetHeight;
	if (secHeight3 >= 780) {
		$('#pagebrk3').addClass("html2pdf__page-break");
	}
	
	var secHeight4 = secHeight3 + document.getElementById('printsection4').offsetHeight;
	
	if (secHeight4 >= 780) {
		$('#pagebrk4').addClass("html2pdf__page-break");
	}
	
	var secHeight5 = secHeight4 + document.getElementById('printsection4').offsetHeight;
	secHeight5 += document.getElementById('printsection5').offsetHeight;
	if (secHeight5 >= 780) {
		$('#pagebrk5').addClass("html2pdf__page-break");
	}
	
	
	var offsetHeight = element.offsetHeight;
	alert(secHeight);
	*/
    var opt = {
      margin: 10,
      filename: "myfile.pdf",
      image: { type: "jpeg", quality: 0.99 },
      html2canvas: { scale: 8 },
      jsPDF: { unit: "mm", format: "a4", orientation: "portrait" },
    };

    html2pdf().set(opt).from(element).save();
  });
});
