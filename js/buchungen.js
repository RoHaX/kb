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
    order: [[0, "desc"]],
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
    pageLength: 5,
    initComplete: record_callback,
  });

  $("#eingang").change(function () {
    hideshowKategorie();
  });

  $("#periode").change(function () {
    $.post({
      url: "ajax_action.php",
      data: { periode: $(this).val(), action: "setSession" },
      success: function (data) {
        if (typeof dataRecords != "undefined") {
          dataRecords.ajax.reload(record_callback);
        }
        if (typeof dataEinAus != "undefined") {
          dataEinAus.ajax.reload();
        }
        if (typeof dataRecords != "undefined") {
          dataEin.ajax.reload();
        }
        if (typeof dataAus != "undefined") {
          dataAus.ajax.reload();
        }
        if (typeof dataKonten != "undefined") {
          dataKonten.ajax.reload();
        }
        getKonto();
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
  });

  var dataRecords = $("#recordListing").DataTable();

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
        // if (typeof dataRecords != "undefined") {
        dataRecords.ajax.reload(record_callback);
        var table = $("#data-table").DataTable();
        table.row(".selected").remove().draw(false);
        // }
      },
    });
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

  $("#upload_csv").on("submit", function (event) {
    event.preventDefault();
    $.ajax({
      url: "kb_import.php",
      method: "POST",
      data: new FormData(this),
      dataType: "json",
      contentType: false,
      cache: false,
      processData: false,
      success: function (jsonData) {
        $("#csv_file").val("");
        // $("#table>thead>tr").append("<th>Second</th>");

        for (let i = 0; i < jsonData.column_count; i++) {
          // text += cars[i] + "<br>";
          $("#data-table tr").append("<th></th>");
        }
        var i = 0;
        Object.values(jsonData.column).forEach((val) => {
          console.log(val.title);
          $("#col_vis").append(
            '<a class="toggle-vis" data-column="' +
              i++ +
              '">' +
              val.title +
              "</a> - "
          );
        });

        var table = $("#data-table").DataTable({
          data: jsonData.data,
          columns: jsonData.column,
        });
        /*
    Column visibility
    https://datatables.net/extensions/buttons/examples/column_visibility/columns.html
    controls
    https://editor.datatables.net/examples/simple/inTableControls.html
    */

        /*
    $('#data-table tbody').on( 'click', 'tr', function () {
        // var table = $("#dt_schule").DataTable();
        // var rid = table.row(this).data()[0];
       this.row.hide(); 
       $(this).remove();
    });
    */
        /*
    $('#data-table tbody').on( 'click', 'img.icon-delete', function () {
        table
            .row( $(this).parents('tr') )
            .remove()
            .draw();
    } );
    */

        $("#data-table tbody").on("click", "tr", function () {
          if ($(this).hasClass("selected")) {
            $(this).removeClass("selected");
          } else {
            table.$("tr.selected").removeClass("selected");
            $(this).addClass("selected");
          }
        });

        $("#data-table tbody").on("click", "button", function () {
          var selData = table.row($(this).parents("tr")).data();
          console.log(selData.Datum);
          console.log(selData.Betreff);
          console.log(selData.Betrag);
          $.post({
            url: "ajax_action.php",
            data: { action: "getBeleg" },
            success: function (data) {
              $("#recordModal").modal("show");
              $("#recordForm")[0].reset();
              $("#beleg").val(parseInt(data) + 1);
              $("#datum").val(selData.Datum);
              $("#bezeichnung").val(selData.Betreff);

              if (selData.Betrag.substring(0, 1) == "-") {
                $("#ausgang").val(selData.Betrag.substring(1));
                $("#eingang").prop("disabled", true);
              } else {
                $("#eingang").val(selData.Betrag);
                $("#ausgang").prop("disabled", true);
              }

              hideshowKategorie();

              $(".modal-title").html(
                "<i class='fas fa-money-check'></i></i> neue Buchung"
              );
              $("#action").val("addRecord");
              $("#save").val("speichern");
              $("#eingang").prop("disabled", false);
              $("#ausgang").prop("disabled", false);
            },
          });
          // alert(data[1]);
        });

        // var table = $('#data-table').DataTable();

        $("#button").click(function () {
          table.row(".selected").remove().draw(false);
          //alert.table.row('.selected');
          //   var selData = table.rows(".selected").data();
          //   console.log(selData);
        });

        $("a.toggle-vis").on("click", function (e) {
          e.preventDefault();

          // Get the column API object
          var column = table.column($(this).attr("data-column"));

          // Toggle the visibility
          column.visible(!column.visible());
        });
      },
    });
  });
});
