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
    url: "/marketings/modal/target/haji",
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

const saveButton = $('#saveButton');
saveButton.click(function (e) {
  e.preventDefault();
  let formData = new FormData();
  formData.append('year', $('#year').val());

  let inputs = document.querySelectorAll('input[name="month[]"]');
  inputs.forEach(input => {
    
    formData.append('months[]', input.value);
  });

  $.ajax({
    method: 'POST',
    url: `/marketings/haji/target/save`,
    async:true,
    data: formData,
    contentType: false,
    processData: false,
    beforeSend: () => {
      Swal.fire({
        title   : 'Data Sedang Diproses',
    });
    Swal.showLoading();
    },
    success: (response) => {
      const responses = JSON.parse(response)
      console.log(responses);
      let text = "";
      let type = "";
      let title = "";
      if (responses.data.code === 4) {
        text = responses.data.message;
        type ="warning";
        title ="Gagal";

      }else if(responses.data.code === 5){
          text = responses.data.message;
          type ="warning";
          title ="Gagal";
      }else{

        text = "Berhasil simpan target haji";
        type ="success";
        title ="Success";
      }
     
      Swal.fire({
        title: title,
        text: text,
        type: type,
        position: "center",
        showConfirmButton: true,
        width: 500,
        timer: 3000,
      });

      closeModal();
        // window.location.reload();
    },
    error: function (error) {
      Swal.fire({
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