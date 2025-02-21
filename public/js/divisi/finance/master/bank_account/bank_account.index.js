var today       = moment(today).format('YYYY-MM-DD');
var base_url    = window.location.origin;
var table_list_bank_account     = "table_list_bank_account";

$(document).ready(function(){
    showTable(table_list_bank_account, []);

    // GET DATA
    // TABLE ACCOUNT BANK
    const accountBankURL    = "divisi/finance/master/bank/list_bank_account";
    const accountBankType   = "GET";
    const accountBankMsg    = "";
    const accountBankData   = [];

    const getDataTransaction    = [
        doTransaction(accountBankURL, accountBankType, accountBankData, accountBankMsg)
    ];

    Promise.allSettled(getDataTransaction)
        .then((success)     => {
            const accountBankGetData    = success[0].status == 'fulfilled' ? success[0].value.data : [];
            if(accountBankGetData.total_data > 0) {
                showTable(table_list_bank_account, accountBankGetData);
                $("#"+table_list_bank_account).find('.dataTables_empty').html(success[0].value.message);
            } else {
                $("#"+table_list_bank_account).find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`);
            }
        })
        .catch((error)      => {
            $("#"+table_list_bank_account).find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`);
        })
});

function showTable(idTable, data)
{
    // DEFAULT DESTROY TABLE
    $("#"+idTable).DataTable().clear().destroy();

    if(idTable == 'table_list_bank_account') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Diproses..`,
                "zeroRecords"   : `Data Yang Dicari Tidak Ditemukan`,
            },
            pageLength  : -1,
            paging      : false,
            ordering    : false,
            columnDefs  : [
                { "targets" : [0, 4], "width" : "8%", "className" : "text-center align-middle" },
                { "targets" : [2, 3], "width" : "20%", "className" : "text-left align-middle"},
                { "targets" : [1], "className" : "text-left align-middle" }
            ]
        })

        if(data.total_data > 0) {
            let i = 1;
            for(const item of data.data)
            {
                const accountBankName       = item.account_bank_name;
                const accountBankNumber     = item.account_number;
                const accountBankCurrency   = item.account_currency;
                const accountBankCoaCode    = item.coa_id;
                const accountBankAction     = `<button class="btn btn-success btn-sm" title="Lihat" id="${item.account_id}"><i class="fa fa-eye"></i></button>`;
                
                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${i++}</label>`,
                    `<label class="font-weight-normal no-margins">${accountBankName} (${accountBankCurrency})</label>`,
                    `<label class="font-weight-normal no-margins">${accountBankNumber}</label>`,
                    `<label class="font-weight-normal no-margins">${accountBankCoaCode}</label>`,
                    accountBankAction
                ]).draw(false);
            }
        }

        $('.dataTables_wrapper').css('padding-bottom', '0px');
    }
}

function doTransaction(url, type, data, message, processData = true, contentType = 'application/x-www-form-urlencoded')
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            url     : base_url + '/' + url,
            type    : type,
            headers     : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            contentType     : contentType,
            processData     : processData,
            beforeSend  : () => {
                message;
            },
            success     : (success) => {
                resolve(success)
            },
            error       : (error)   => {
                reject(error)
            }
        })
    })
}