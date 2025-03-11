var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
var dataMonth   = [];
var dataYear    = [];
var dataBankAccount     = [];

// GET BULAN
for(let i = 0; i < 12; i++) {
    let monthNumber     = moment(i + 1, 'M').format('MM');
    let monthName       = moment(monthNumber, 'MM').format('MMMM');

    dataMonth.push({
        'monthNumber'   : monthNumber,
        'monthName'     : monthName,
    });
}

// GET TAHUN
for(let i = moment(today, 'YYYY-MM-DD').subtract(10, 'year').format('YYYY'); i < parseInt(moment(today, 'YYYY-MM-DD').add(10, 'year').format('YYYY')); i++) {
    dataYear.push({
        'yearNumber' : parseInt(i),
    });
}

function doTransaction(url, type, data, message, processData = true, contentType = 'application/x-www-form-urlencoded; charset=UTF-8')
{
    return new Promise((resolve, reject)   => {
        $.ajax({
            cache   : false,
            url     : base_url + '/' + url,
            type    : type,
            processData : processData,
            contentType : contentType,
            data    : data,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN
            },
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