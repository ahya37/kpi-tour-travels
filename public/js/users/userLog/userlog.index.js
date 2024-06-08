var site_url    = window.location.pathname;

$(document).ready(function(){
    console.log('test');
    showTable('table_log_user');
});

function showTable(idTable)
{
    if(idTable == 'table_log_user')
    {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "Tidak ada data, silahkan masukkan beberapa",
                "zeroRecords"   : "Tidak ada data, silahkan masukkan beberapa",
            },
            columnDefs   : [
                {
                    "targets"   : [0],
                    "className" : "text-center",
                    "width"     : "5%",
                },
                {
                    "targets"   : [2],
                    "className" : "text-left",
                    "width"     : "15%",
                },
                {
                    "targets"   : [3],
                    "className" : "text-center",
                    "width"     : "15%"
                }
            ],
            processing  : true,
            serverSide  : false,
            ajax        : {
                type    : "GET",
                dataType: "json",
                url     : site_url + "/dataTableUserLog",
            },
        })
    }
}