$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': CSRF_TOKEN
  }
});

$('#myModal5').on('show.bs.modal', function (e) {

  $.ajax({
    url: "/marketings/modal/target",
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
    },
    done: () => {
      $('#spinner').remove();
    }
  });
});

function closeModal() {
  $('#year').val('');
  $("#myModal5").modal("hide");
}


const saveButton = $('#saveButton');
saveButton.click(function (e) {
  e.preventDefault();
  let formData = new FormData();
  formData.append('year', $('#year').val());

  $.ajax({
    method: 'POST',
    url: `/marketings/target`,
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
        table.ajax.reload();
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