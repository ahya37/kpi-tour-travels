$(document).ready(function(){
    // GET DATA
    dashboard();
})

var base_url    = window.location.pathname;

function dashboard() {
    // GET SUMMARY DASHBOARD
    var url     = base_url+"/getDataTotalProgramKerja";
    var type    = "GET";
    var message =   Swal.fire({title   : 'Data Sedand Dimuat'});Swal.showLoading();
    var isAsync = true;

    transData(url, type, '', message, isAsync)
        .then(function(xhr){
            $("#pk_tahunan").text(xhr.data[0].grand_total_proker_tahunan+" Program Kerja");
            $("#pk_bulanan").text(xhr.data[0].grand_total_proker_bulanan+" Program Kerja");
            $("#pk_harian").text(xhr.data[0].grand_total_proker_harian+" Program Kerja");
            $("#summary_header").show();
            Swal.close();
        })
        .catch(function(xhr){
            console.log(xhr);
        })
}

function transData(url, type, data, customMessage, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            cache   : false,
            type    : type,
            dataType: "json",
            data    : {
                _token  : CSRF_TOKEN,
                sendData : data,
            },
            async   : isAsync,
            url     : url,
            beforeSend  : function() {
                customMessage;
            },
            success : function(xhr) {
                resolve(xhr);
            },
            error   : function(xhr) {
                reject(xhr);
            },
        })
    });
}