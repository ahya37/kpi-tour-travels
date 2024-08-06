 // Sumber pendaftaran-umrah
 const filterSumberPendaftaranUmrah = $('input[name="filter-sumber-pendaftaran-umrah"]');
 filterSumberPendaftaranUmrah.daterangepicker({
     format: 'DD/MM/YYYY',
     locale: {
                applyLabel: 'Submit',
                cancelLabel: 'Cancel',
            }
 }, function(start, end, label){
    let start_date=start.format('YYYY-MM-DD');
    let end_date     =  end.format('YYYY-MM-DD');
    $.ajax({
        url: 'https://api-percik.perciktours.com/api/employees/presensi/report/multisheet',
        method: 'POST',
        headers: {
            'x-api-key': window.appConfig.appUrl, 
        },
        data: {
            start: start_date,
            end: end_date
        },
        xhrFields: {
            responseType: 'blob' 
        },
        beforeSend: function(){
            Swal.fire({
                title   : 'Data Sedang Diproses',
            });
            Swal.showLoading();
        },
        success: function(response, status, xhr) {
            var filename = '';
            var disposition = xhr.getResponseHeader('Content-Disposition');
            
            if (disposition && disposition.indexOf('attachment') !== -1) {
                var filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                var matches = filenameRegex.exec(disposition);
                if (matches != null && matches[1]) filename = matches[1].replace(/['"]/g, '');
            }

            var blob = new Blob([response], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });

            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = filename +'Presensi '+start_date+' to '+end_date;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            Swal.close();
            Swal.fire({
                title: "Sukses download!",
                type: "success",
                position: "center",
                showConfirmButton: false,
                width: 500,
                timer: 2000,
              });
        },
        error: function(xhr, status, error) {
            console.error('Error:', error);
            Swal.close();
        },
        done: function(){
            Swal.close();
        }
    });

 });