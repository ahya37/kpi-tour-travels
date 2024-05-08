const CSRF_TOKEN = $('meta[name="csrf-token"]').attr("content");
$.ajaxSetup({
  headers: {
    'X-CSRF-TOKEN': CSRF_TOKEN
  }
});

const table = $(".data").DataTable({
  pageLength: 10,

  bLengthChange: true,
  bFilter: true,
  bInfo: true,
  processing: true,
  bServerSide: true,
  order: [[1, "desc"]],
  autoWidth: false,
  ajax: {
    url: "/marketings/target/list",
    type: "POST",
    data: function (q) {
      q._token = CSRF_TOKEN;
      return q;
    },
  },
  columnDefs: [
    {
      targets: 0,
      visible: false,
      render: function (data, type, row, meta) {
        return `<p>${row.id}</p>`;
      },
    },
    {
      targets: 1,
      render: function (data, type, row, meta) {
        return `<p>${row.year}</p>`;
      },
    },
    {
      targets: 2,
      render: function (data, type, row, meta) {
        return `<p>${row.total_target}</p>`;
      },
    },
    {
      targets: 3,
      render: function (data, type, row, meta) {
        return `<p>${row.total_realization}</p>`;
      },
    },
    {
      targets: 4,
      render: function (data, type, row, meta) {
        return `<p>${row.total_difference}</p>`;
      },
    },
    {
      targets: 5,
      render: function (data, type, row, meta) {
        return ` <button class="btn btn-sm btn-primary dim" type="button" title="Detail"><i class="fa fa-eye"></i></button>`;
      },
    },
  ],
});



const form = $('#saveButton');
form.click(function (e) {
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

function closeModal() {
  $('#year').val('');
  $("#myModal5").modal("hide");
}

$('#myModal5').on('show.bs.modal', function (e) {

  $.ajax({
    url: "/marketings/target/modal",
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


