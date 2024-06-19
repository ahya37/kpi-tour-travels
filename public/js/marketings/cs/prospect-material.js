const table = $(".data").DataTable({
    pageLength: 10,
});

$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': CSRF_TOKEN
  }
});

const saveButton = $('#saveButton');
saveButton.click(function (e) {
  e.preventDefault();
  const id = $(this).data('id');

  $.ajax({
    method: 'GET',
    url: `/marketings/alumniprospectmaterial/singkronisasi/${id}`,
    async:true,
    contentType: false,
    processData: false,
    beforeSend: () => {
      Swal.fire({
                                title   : 'Data Sedang Diproses',
                            });
                            Swal.showLoading();
    },

    success: (response) => {
      if (response) {
          Swal.fire({
            title: "Good Job!",
            text: `${response.data.message}`,
            type: "success",
            position: "center",
            showConfirmButton: true,
            width: 500,
            timer: 3000,
          });

          window.location.reload();
      }
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