var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;

$(document).ready(function(){
    console.log('Hey, You Found Me!');
});

function rupiahFormatter(value, maxDigit = 2, minDigit = 2) {
    if(value) {
        let format  = new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR', maximumFractionDigits: maxDigit, minimumFractionDigits: minDigit}).format(value);
        return format;
    } else {
        let format  = 'Rp. 0.00';
        return format;
    }
}

function doTransaction(url, type, data, message = '', processData = true, contentType = 'application/x-www-form-urlencoded; charset=UTF-8')
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            url     : base_url + '/' + url,
            type    : type,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            cache   : false,
            data    : data,
            processData     : processData,
            contentType     : contentType,
            beforeSend      : () => {
                message;
            },
            success         : (success) => {
                resolve(success);
            },
            error           : (error)   => {
                reject(error);
            }
        })
    })
}