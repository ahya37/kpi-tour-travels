
$('#myModal5').on('show.bs.modal', function (e) {

    $.ajax({
        url: "/activities/modal/create",
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
            console.log(response.proker_bulanan);
            $('.modal-body').html(response.modalContent);
            activeSelect2();
        },
        done: () => {
            $('#spinner').remove();
        }
    });
});


function closeModal() {
    $("#myModal5").modal("hide");
  }