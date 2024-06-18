$(document).ready(function () {
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

  function getStammPeriode() {
    $.post({
      url: "ajax_action.php",
      data: { action: "getStammPeriode" },
      success: function (data) {
        //$("#periode").html(data);
        //$("#txtperiode").html($("#periode option:selected").text());
        console.log(data["pebezeichnung"]);
        $("#pebezeichnung").val(data["pebezeichnung"]);
        $("#vondat").val(data["vondat"]);
        $("#bisdat").val(data["bisdat"]);

        // {"pebezeichnung":"Buchungsjahr 2023","vondat":null,"bisdat":null}
        //$("#txtperiode").html( 'x' );
      },
    });
  }

  // $('#save_stamm').click(function) {
  $("#save_pestamm").click(function () {
    var str_pebezeichnung = $("#pebezeichnung").val();
    var str_vondat = $("#vondat").val();
    var str_bisdat = $("#bisdat").val();
    var action = "updateStammPeriode";
    $.post({
      url: "ajax_action.php",
      data: {
        pebezeichnung: str_pebezeichnung,
        vondat: str_vondat,
        bisdat: str_bisdat,
        action: action,
      },
      dataType: "json",
      success: function (data) {
        alert("save");
      },
    });
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
    processing: true,
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

  var dataProjekte = $("#lstProjekte").DataTable({
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
    processing: true,
    serverSide: true,
    serverMethod: "post",
    ajax: {
      url: "ajax_action.php",
      type: "POST",
      data: { action: "listProjekte" },
      dataType: "json",
    },
    pageLength: 25,
  });

  var dataKategorie = $("#lstKategorie").DataTable({
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
    processing: true,
    serverSide: true,
    serverMethod: "post",
    ajax: {
      url: "ajax_action.php",
      type: "POST",
      data: { action: "listKategorie" },
      dataType: "json",
    },
    pageLength: 25,
    dom: "frtipB",
    buttons: ["copy", "excel", "csv", "pdf", "colvis"],
  });

  $("#lstKonten").on("click", ".update", function () {
    var kid = $(this).attr("id");
    var action = "getDsKonten";
    $.post({
      url: "ajax_action.php",
      data: { kid: kid, action: action },
      dataType: "json",
      success: function (data) {
        $("#kontModal").modal("show");
        $("#kid").val(data.kid);
        $("#kontoname").val(data.kontoname);
        $("#saldostart").val(data.saldostart);
        $(".modal-title").html("<i class='fa fa-plus'></i> bearbeite Konto");
        $("#kontaction").val("updateKonto");
        $("#save").val("speichern");
      },
    });
  });

  $("#lstKategorie").on("click", ".update", function () {
    var katid = $(this).attr("id");
    var action = "getDsKategorie";
    $.post({
      url: "ajax_action.php",
      data: { katid: katid, action: action },
      dataType: "json",
      success: function (data) {
        $("#katModal").modal("show");
        $("#katid").val(data.katid);
        $("#katbez").val(data.katbez);
        $("#katbez_kb").val(data.katbez_kb);
        $("#katart").val(data.katart);
        $("#color").val(data.color);
        $("#color").css("background-color", data.color);
        $(".modal-title").html(
          "<i class='fa fa-plus'></i> bearbeite Kategorie"
        );
        $("#kataction").val("updateKategorie");
        $("#save").val("speichern");
      },
    });
  });

  $("#lstProjekte").on("click", ".update", function () {
    var pid = $(this).attr("id");
    var action = "getDsProjekt";
    $.post({
      url: "ajax_action.php",
      data: { pid: pid, action: action },
      dataType: "json",
      success: function (data) {
        $("#projModal").modal("show");
        $("#pid").val(data.pid);
        $("#projektname").val(data.projektname);
        $("#projekt_kb").val(data.projekt_kb);
        $("#pcolor").val(data.pcolor);
        $("#pcolor").css("background-color", data.pcolor);
        $(".modal-title").html("<i class='fa fa-plus'></i> bearbeite Projekt");
        $("#projaction").val("updateProjekt");
        $("#save").val("speichern");
      },
    });
  });

  $("#kontModal").on("submit", "#frmKonto", function (event) {
    event.preventDefault();
    $("#save").attr("disabled", "disabled");
    var formData = $(this).serialize();
    $.post({
      url: "ajax_action.php",
      data: formData,
      success: function (data) {
        $("#frmKonto")[0].reset();
        $("#kontModal").modal("hide");
        $("#save").attr("disabled", false);
        dataKonten.ajax.reload();
      },
    });
  });

  $("#katModal").on("submit", "#frmKat", function (event) {
    event.preventDefault();
    $("#save").attr("disabled", "disabled");
    var formData = $(this).serialize();
    $.post({
      url: "ajax_action.php",
      data: formData,
      success: function (data) {
        $("#frmKat")[0].reset();
        $("#katModal").modal("hide");
        $("#save").attr("disabled", false);
        dataKategorie.ajax.reload();
      },
    });
  });

  $("#projModal").on("submit", "#frmProj", function (event) {
    event.preventDefault();
    $("#save").attr("disabled", "disabled");
    var formData = $(this).serialize();
    $.post({
      url: "ajax_action.php",
      data: formData,
      success: function (data) {
        $("#frmProj")[0].reset();
        $("#projModal").modal("hide");
        $("#save").attr("disabled", false);
        dataProjekte.ajax.reload();
      },
    });
  });

  $("#pdfDownloader").click(function () {
    var element = document.getElementById("lstKategorie");
    var opt = {
      margin: 1,
      filename: "myfile.pdf",
      image: { type: "jpeg", quality: 0.98 },
      html2canvas: { scale: 2 },
      jsPDF: { unit: "in", format: "letter", orientation: "portrait" },
    };

    // New Promise-based usage:
    html2pdf().set(opt).from(element).save();

    // Old monolithic-style usage:
    html2pdf(element, opt);
  });

  $("#periode").change(function () {
    $.post({
      url: "ajax_action.php",
      data: { periode: $(this).val(), action: "setSession" },
      success: function (data) {
        dataProjekte.ajax.reload();
        dataKategorie.ajax.reload();
        dataKonten.ajax.reload();
        getStammPeriode();
      },
    });
  });
  getPeriode();
  getStammPeriode();
});
