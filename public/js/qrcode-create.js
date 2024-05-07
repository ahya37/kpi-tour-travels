$('#qrcode-generate').submit(function(e) {
    e.preventDefault();
     
    const url = $(this).attr("action");
    const csrfToken = $('meta[name="csrf-token"]').attr('content');

    $.ajax({
            type:'POST',
            url: url,
            headers : {
                'X-CSRF-TOKEN' : csrfToken,
                'Accept' : 'application/json'
            },
            contentType: false,
            processData: false,
            beforeSend: () => {
                const img = document.getElementById("imgqrcode");
                img.src = "";
                $('#Loading').empty();
                $('#Loading').removeClass('d-none');
                $('#Loading').append(
                    `<div class="spinner-border cs-color" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>`
                );
            },
            success: (response) => {
                const img = document.getElementById("imgqrcode");
                img.src = "data:image/png;base64," + response;
            },
            error: function(response){
            },
            complete: () => {
                $('#Loading').addClass('d-none');
            }
       });
    
});