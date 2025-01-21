var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
var path        = window.location.pathname;

$(document).ready(function() {
    // GET DATA SUMMARY
    const summaryURL    = "tarik_data/perciktourscom/summary_data";
    const summaryType   = "GET";
    const summaryData   = [];
    const summaryMsg    = "";

    doTransaction(summaryURL, summaryType, summaryData, summaryMsg)
        .then((results) => {
            const summaryGetData    = results.data;
            showTable('table_summary_data', summaryGetData);

            if(summaryGetData.length > 0) {
                $("#table_summary_data").find('.dataTables_empty').html(`Data Berhasil Dimuat`);
            } else {
                $("#table_summary_data").find('.dataTables_empty').html(`Data Gagal Dimuat`);
            }
        })
        .catch((err)    => {
            showTable('table_summary_data', []);
            $("#table_summary_data").find('.dataTables_empty').html(`Data Gagal Dimuat`);
        })
})

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();

    if(idTable == 'table_summary_data') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : `<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..`,
            },
            searching   : false,
            bInfo       : false,
            paging      : false,
            ordering    : false,
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0, 1, 2, 3], "className" : "text-right align-middle", "width" : "25%" },
            ]
        });
        
        if(data.length > 0) {
            $("#"+idTable).DataTable().row.add([
                data[0]['total_jemaah'],
                data[0]['total_perjalanan'],
                data[0]['total_agen'],
                data[0]['total_pembimbing'],
            ]).draw(false);

            $("#table_summary_data_footer").removeClass('text-right').html(`Update Terakhir : ${data[0]['last_update']}`);
        }

        $("#table_summary_data_wrapper").css('padding-bottom', '0px');

    }
}

function doTarikData(idForm)
{
    if(idForm == 'table_summary_data') {
        // GET DATA
        const url   = 'tarik_data/perciktourscom/get_summary_data';
        const type  = 'GET';
        const msg   = '';
        const data  = [];

        showTable('table_summary_data', []);
        
        doTransaction(url, type, data, msg)
            .then((results) => {
                const sendData  = [
                    results.data
                ]
                let message;
                if(results.data.length > 0) {
                    message     = 'Data Berhasil Dimuat';
                } else {
                    message     = 'Data Gagal Dimuat';
                }
                showTable('table_summary_data', sendData);
                $("#"+idForm).find('.dataTables_empty').html(message);
            })
            .catch((err)    => {
                console.log(err);
                const sendData  = [
                    err.responseJSON.data
                ];
                showTable('table_summary_data', sendData);
            })
    }
}

function doTransaction(url, type, data, message)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            url     : base_url + "/" + url,
            type    : type,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                message;
            },
            success     : (success) => {
                resolve(success)
            },
            error       : (error)   => {
                reject(error);
            }
        })
    })
}