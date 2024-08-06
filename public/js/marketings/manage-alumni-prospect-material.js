
const query = document.URL;
const  alumniprospectmaterialId = query.substring(query.lastIndexOf("/") + 1);

$(document).ready(function(){
  showTable('tableListAlumni');
});


$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': CSRF_TOKEN
  }
});

function getURL()
{
    return window.location.pathname;
}

function showTable(idTable)
{
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'tableListAlumni') {
        $("#"+idTable).DataTable({
            language    : {
                "zeroRecords"   : "Data Tidak ada, Silahkan tambahkan beberapa data..",
                "emptyTable"    : "Data Tidak ada, Silahkan tambahkan beberapa data..",
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
            },
            processing  : true,
            serverSide  : false,
            ajax        : {
                type    : "POST",
                dataType: "json",
                url     : `/marketings/alumniprospectmaterial/detail/list/${alumniprospectmaterialId}`,
            },
            autoWidth   : false,
            columnDefs  : 
            [
                {"targets" : [0], "className" : "text-center", "width" : "5%"},
                { "targets" : [1], "width" : "15%" },
                { "targets" : [3], "width" : "30%" },
                { "targets" : [2], "className" : "text-center", "width" : "10%" },
                {"targets" : [4], "className" : "text-center", "width" : "8%"},
            ],
        });
    } else if(idTable == 'tableListFile') {
        $("#"+idTable).DataTable({
            language    : {
                "zeroRecords"   : "Data Tidak ada, Silahkan tambahkan beberapa data..",
                "emptyTable"    : "Data Tidak ada, Silahkan tambahkan beberapa data..",
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
            },
            processing  : true,
            serverSide  : false,
            autoWidth   : false,
            bInfo       : false,
            ordering    : false,
            searching   : false,
            paging      : false,
            columnDefs  : [
                { "targets":[0, 2], "className":"text-center","width":"8%" },
            ],
        })
    }
}

const activeSelect2 = () => {
  $('#myModal5 select.select2').select2({
    theme: 'bootstrap4',
    allowClear: false,
    width: $(this).data("width")
      ? $(this).data("width")
      : $(this).hasClass("w-100")
        ? "100%"
        : "style",
  });
}

// modal show
$('#myModal5').on('show.bs.modal', function (e) {
  const button = $(e.relatedTarget);
  const detailId = button.data("id");

  let year = "";
  umrahAPI(year);
  hajiAPI(year);
  tourMuslimAPI(year);


  $.ajax({
    url: `/marketings/alumniprospectmaterial/detail/manage/modal/${detailId}`,
    method: "GET",
    data: {
      _token: CSRF_TOKEN
    },
    beforeSend: () => {
      $('#loading').append(`<div id="spinner" class="spiner-example">
                <div class="sk-spinner sk-spinner-wave">
                    <div class="sk-rect1"></div>
                    <div class="sk-rect2"></div>
                    <div class="sk-rect3"></div>
                    <div class="sk-rect4"></div>
                    <div class="sk-rect5"></div>
                </div>
            </div>`)
    },
    success: function (response) {
      $('.modal-body').html(response.modalContent);
      activeSelect2();
      callIdSelectOptionReason();
      callIdElementInput();
    },
    done: () => {
      $('#spinner').remove();
    }
  });

  function callIdElementInput() {
    $("#year").on("change", async function () {
      year = $("#year").val();
      umrahAPI(year);
      hajiAPI(year);
      tourMuslimAPI(year);
    });
  }

  function callIdSelectOptionReason(){
    $("#response").on("change", async function () {
      let response = $("#response").val();
      if (response === 'Y') {
        $('.div-year').removeClass('d-none');
        $('#div-reason').addClass('d-none');
      }else{
        $('.div-year').addClass('d-none');
        $('#div-reason').removeClass('d-none');
      }
    });
  }

  async function umrahAPI(year) {
    const params = `?year=${year}`;

    await fetch(`https://api.perciktours.com/jadwalumrahbyyeard${params}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json;charset=utf-8",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        const results = data.data.jadwal;
        selectOptionTourcode(results);
      });
  }

  function selectOptionTourcode(result) {
    $("#tourcode").empty();
    $("#tourcode").append('<option value="">-Pilih Tourcode-</option>');
    return $.each(result, function (key, item) {
      $("#tourcode").append(
        '<option value="' + item.KODE + '">' + item.KODE + "</option>"
      );
    });
  }

  async function hajiAPI(year) {
    const params = `?year=${year}`;

    await fetch(`https://api.perciktours.com/jadwalhaji${params}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json;charset=utf-8",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        const results = data.data.jadwal;
        selectOptionHajiCode(results);
      });
  }

  function selectOptionHajiCode(result) {
    $("#tourcodeHaji").empty();
    $("#tourcodeHaji").append('<option value="">-Pilih Kode-</option>');
    return $.each(result, function (key, item) {
      $("#tourcodeHaji").append(
        '<option value="' + item.KODE + '">' + item.KODE + "</option>"
      );
    });
  }

  async function tourMuslimAPI(year) {
    const params = `?year=${year}`;

    await fetch(`https://api.perciktours.com/jadwaltourmuslim${params}`, {
      method: "GET",
      headers: {
        "Content-Type": "application/json;charset=utf-8",
      },
    })
      .then((response) => response.json())
      .then((data) => {
        const results = data.data.jadwal;
        selectOptionTourMuslimCode(results);
      });
  }

  function selectOptionTourMuslimCode(result) {
    $("#tourcodeMuslim").empty();
    $("#tourcodeMuslim").append('<option value="">-Pilih Kode-</option>');
    return $.each(result, function (key, item) {
      $("#tourcodeMuslim").append(
        '<option value="' + item.KODE + '">' + item.KODE + "</option>"
      );
    });
  }

});
// end modal show


function closeModal() {
  $("#myModal5").modal("hide");
}

const saveButton = $('#saveButton');
saveButton.click(function (e) {
  e.preventDefault();
  let formData = new FormData();
  formData.append('idDetail', $('#idDetail').val());
  formData.append('response', $('#response').val());
  formData.append('tourcode', $('#tourcode').val());
  formData.append('tourcodeHaji', $('#tourcodeHaji').val());
  formData.append('tourcodeMuslim', $('#tourcodeMuslim').val());
  formData.append('reason', $('#reason').val());
  formData.append('notes', $('#notes').val());
  formData.append('remember', $('input[name="remember"]:checked').val());

  $.ajax({
    method: 'POST',
    url: `/marketings/alumniprospectmaterial/detail/manage/store`,
    data: formData,
    contentType: false,
    processData: false,
    beforeSend: () => {
    },
    success: (response) => {
      if (response) {

        swal({
          title: "Good job!",
          text: `${response.data.message}`,
          type: "success",
          position: "center",
          showConfirmButton: false,
          width: 500,
          timer: 900,
        });
        closeModal();
        showTable('tableListAlumni');
      }
    },
    error: function (error) {
      swal({
        title: "Gagal!",
        position: "center",
        type: "danger",
        text: error.responseJSON.data.message,
        showConfirmButton: false,
        width: 500,
        timer: 900,
      });
    }
  });
});