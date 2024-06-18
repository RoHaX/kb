$(document).ready(function () {
  function getKonto() {
    $.post({
      url: "ajax_action.php",
      data: { action: "getKonto" },
      success: function (data) {
        $("#konto").html(data);
      },
    });
  }

  getKonto();
  var dataRecords = $("#recordListing").DataTable({
    responsive: true,
    lengthChange: true,
    processing: true,
    serverSide: true,
    serverMethod: "post",
    order: [],
    ajax: {
      url: "ajax_action.php",
      type: "POST",
      data: { action: "listKontoBuchungen" },
      dataType: "json",
    },
    columnDefs: [
      { type: "num-fmt", symbols: "R$", targets: 4 },
      { className: "green", targets: [3] },
      { className: "red", targets: [4] },
      { width: "10px", targets: [0] },
      { width: "70px", targets: [1] },
      { width: "350px", targets: [2] },
    ],
    pageLength: -1,
  });

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

  $("#periode").change(function () {
    $.post({
      url: "ajax_action.php",
      data: { periode: $(this).val(), action: "setSession" },
      success: function (data) {
        getKonto();
      },
    });
  });

  $("#logout").click(function () {
    //login
    window.location.replace("index.php");
  });

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
