
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': CSRF_TOKEN
  }
});

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

  let year = "";
  umrahAPI(year);


  $.ajax({
    url: "/marketings/alumniprospectmaterial/detail/manage/modal",
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
    });
  }

  function callIdSelectOptionReason(){
    $("#response").on("change", async function () {
      let response = $("#response").val();
      if (response === 'Y') {
        $('#div-year').removeClass('d-none');
        $('#div-reason').addClass('d-none');
      }else{
        $('#div-year').addClass('d-none');
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
});
// end modal show


function closeModal() {
  $("#myModal5").modal("hide");
}