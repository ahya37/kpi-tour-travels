var today   = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;

$(document).ready(function(){
    console.log('js load');
    
    // GET DATA DASHBOARD

    let coaURL  = 'divisi/finance/master/coa/list';
    let coaType = "GET";
    let coaMessage  = "";
    let coaData     = {
        'coa_id'    : '%',
    };

    let accountBankURL  = 'divisi/finance/master/bank/list_bank_account';
    let accountBankType = "GET";
    let accountBankMessage  = "";
    let accountBankData     = [];

    const getData   = [
        doTransaction(coaURL, coaType, coaData, coaMessage),
        doTransaction(accountBankURL, accountBankType, accountBankData, accountBankMessage)
    ];

    Promise.allSettled(getData)
        .then((results)     => {
            let coaGetData  = results[0].status == 'fulfilled' ? results[0].value.data : [];
            $("#master_coa_content").html(`<h2 class="no-margins">${coaGetData.length}</h2>`);

            let accountBankGetData  = results[1].status == 'fulfilled' ? results[1].value.data : [];
            $("#master_account_bank_content").html(`<h2 class="no-margins">${accountBankGetData.total_data}</h2>`)
        })
        .catch((error)      => {
            console.log(error);
        })
});

function doTransaction(url, type, data, message)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache       : false,
            type        : type,
            url         : base_url + '/' + url,
            headers     : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                message;
            },
            processData : true,
            success     : (success) => {
                resolve(success)
            },
            error       : (error)   => {
                reject(error)
            }
        })
    })
}