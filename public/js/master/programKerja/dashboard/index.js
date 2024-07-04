$(document).ready(function(){
    // GET DATA
    dashboard();
})

var base_url    = window.location.pathname;

function dashboard() {
    // GET SUMMARY DASHBOARD
    var url     = base_url+"/getDataTotalProgramKerja";
    var type    = "GET";
    var message =   NProgress.start();
    var isAsync = true;

    transData(url, type, '', message, isAsync)
        .then(function(xhr){
            $("#pk_tahunan").text(xhr.data[0].grand_total_proker_tahunan+" Program Kerja");
            $("#pk_bulanan").text(xhr.data[0].grand_total_proker_bulanan+" Program Kerja");
            $("#pk_harian").text(xhr.data[0].grand_total_proker_harian+" Program Kerja");
            $("#summary_header").show();
            showData('table_programKerja_bulanan', true);
            NProgress.done();
        })
        .catch(function(err){
            console.log(err.responseJSON);
            $("#pk_tahunan").text("0 Program Kerja");
            $("#pk_bulanan").text("0 Program Kerja");
            $("#pk_harian").text("0 Program Kerja");
            $("#summary_header").show();
            showData('table_programKerja_bulanan', true);
            NProgress.done();
        })
}

function showTable(idTable)
{
    if(idTable == 'table_programKerja_bulanan') {
        $("#v_table_programKerja_bulanan").show();
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language   : {
                "emptyTable"    : "Tidak ada data yang bisa ditampilkan",
                "zeroRecords"   : "Tidak ada data yang bisa ditampilkan",
            },
            columnDefs : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [2, 3, 4], "className" : "text-left align-middle", "width" : "14%" },
                { "targets" : [1], "className" : "text-left" },
            ],
        });
    }
}

function showData(id, asyncStatus)
{
    if(id == 'table_programKerja_bulanan') {
        moment.locale('id');
        showTable(id);
        var url     = base_url + "/getDataTableDashboard";
        var type    = "GET";
        var data    = "";
        if( asyncStatus === true ) { var message = Swal.fire({ title : 'Table Sedang Dimuat' }); Swal.showLoading(); } else { var message = ""; }

        transData(url, type, data, message, asyncStatus)
            .then((xhr)=>{
                Swal.close();

                var getData     = xhr.data;
                $.each(getData, function(i,item){
                    $("#"+id).DataTable().row.add([
                        i + 1,
                        item['pkb_title'],
                        item['pkb_start_date'] == item['pkb_end_date'] ? moment(item['pkb_start_date'], 'YYYY-MM-DD').format('DD-MMM-YYYY') : moment(item['pkb_start_date'], 'YYYY-MM-DD').format('DD-MMM-YYYY')+" s/d "+moment(item['pkb_end_date'], 'YYYY-MM-DD').format('DD-MMM-YYYY'),
                        item['group_division_name'],
                        item['created_by']
                    ]).draw('false');
                })
            })
            .catch((xhr)=>{
                Swal.close();
                console.log(xhr);
            })
    }
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