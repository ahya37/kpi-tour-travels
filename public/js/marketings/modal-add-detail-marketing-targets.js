const query = document.URL;
const detailMarketingTargetId = query.substring(query.lastIndexOf("/") + 1);
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': CSRF_TOKEN
  }
});

$('#myModal5').on('show.bs.modal', function (e) {

  $.ajax({
    url: "/marketings/modal/target/detail",
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
  $("#myModal5").modal("hide");
}

$(`#programs`).select2({
  theme: 'bootstrap4',
  allowClear: true,
});

const saveButton = $('#saveButton');
saveButton.click(function (e) {
  e.preventDefault();
  let formData = new FormData();
  formData.append('month', $('#month').val());
  formData.append('program_id', $('#program_id').val());
  formData.append('target', $('#target').val());

  $.ajax({
    method: 'POST',
    url: `/marketings/target/detail/${detailMarketingTargetId}/store`,
    data: formData,
    contentType: false,
    processData: false,
    beforeSend: () => {
    },
    success: (response) => {
      if (response) {
        swal({
          title: response.data.success === 1 ? 'Good job!' : 'Warning',
          type: response.data.success === 1 ? 'success' : 'warning',
          text: response.data.message,
          position: "center",
          showConfirmButton: false,
          width: 500,
          timer: 1500,
        });
        closeModal();
        table.ajax.reload();
      }
    },
    error: function (error) {
      swal({
        title: "Gagal!",
        type: "danger",
        position: "center",
        text: error.responseJSON.data.message,
        showConfirmButton: false,
        width: 500,
        timer: 1500,
      });
    }
  });
});