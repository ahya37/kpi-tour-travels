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

  const InitialSwall = (text, icon, title) => {
      Swal.fire({
        title: title,
        text: text,
        icon: icon,
        position: "center",
        showConfirmButton: true,
        width: 500,
        timer: 3000,
      });
  }

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

      let text = "";
      let icon = "";
      let title = "";
      if (response.data.code === 4) {
        text = response.data.message;
        icon ="warning";
        title ="Warning";
        InitialSwall(text, icon, title);
      }else{
        text = response.data.message;
        icon ="success";
        title ="Success";

        InitialSwall(text, icon, title);
        closeModal();
        window.location.reload();

      }
    },
    error: function (error) {
      Swal.fire({
        title: "Gagal!",
        position: "center",
        icon: "danger",
        text: 'Gagal simpan target Haji',
        showConfirmButton: false,
        width: 500,
        timer: 900,
      });
    }
  });

});