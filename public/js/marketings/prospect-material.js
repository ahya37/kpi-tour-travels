const table = $(".data").DataTable({
    pageLength: 10,
});

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': CSRF_TOKEN
  }
});

$('#myModal5').on('show.bs.modal', function (e) {

  $.ajax({
    url: "/marketings/prospectmaterial/modal/create",
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
      activeSelect2('-Pilih CS-');
      //   $('#data_5 .input-daterange').datepicker({
      //     keyboardNavigation: false,
      //     forceParse: false,
      //     autoclose: true
      // });
    },
    done: () => {
      $('#spinner').remove();
    }
  });
});

function closeModal() {
  $("#myModal5").modal("hide");
}

const saveButton = $('#saveButton');
saveButton.click(function (e) {
  e.preventDefault();
  let formData = new FormData();
  formData.append('cs', $('#cs').val());
  formData.append('start_year', $('#startYear').val());
  formData.append('end_year', $('#endYear').val());
  formData.append('jml_data', $('#jmlData').val());
  $.ajax({
    method: 'POST',
    url: `/marketings/prospectmaterial/store`,
    data: formData,
    contentType: false,
    processData: false,
    beforeSend: () => {
    },
    success: (response) => {
      if (response.data.status === 2) {
        swal({
          title: "Gagal!",
          text: `${response.data.message}`,
          type: "warning",
          position: "center",
          showConfirmButton: true,
          width: 500,
          timer: 3000,
        });

      }else{
        swal({
          title: "Good job!",
          text: `${response.data.message}`,
          type: "success",
          position: "center",
          showConfirmButton: false,
          width: 500,
          timer: 2000,
        });

        closeModal();
        window.location.reload();
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