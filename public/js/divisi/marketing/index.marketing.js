var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
var dataMonth   = [];
var dataYear    = [];

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

$(document).ready(function(){
    showSelect('select_filter_keberangkatan', dataYear, moment(today, 'YYYY-MM-DD').format('YYYY'));
    showTable('table_pembayaran_haji', []);

    // GET DATA
    const bayarHajiURL  = "divisi/finance/pembayaran/haji/list_pembayaran_haji";
    const bayarHajiType = "GET";

    const transGetData  = [
        doTransaction(bayarHajiURL, bayarHajiType)
    ];

    Promise.allSettled(transGetData)
        .then((success)     => {
            const bayarHajiGetData  = success[0].status == 'fulfilled' ? success[0].value.data : []
            
            showTable('table_pembayaran_haji', bayarHajiGetData);
            if(bayarHajiGetData.length < 1) {
                $("#table_pembayaran_haji").find('.dataTables_empty').html(`Tidak Ada Data Pembayaran Haji`)
            }
        })
        .catch((error)      => {
            console.log(error);
            showTable('table_pembayaran_haji', []);
        })
});

function showSelect(idSelect, data = [], selectedData = '', seq = '')
{
    // DEFAULT STYLE
    $("#"+idSelect+""+seq).select2({
        theme   : 'bootstrap4',
    })
    if(idSelect == 'select_filter_keberangkatan') {
        let html    = `<option selected disabled>Pilih Tahun</option>`;

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value="${item['yearNumber']}">${item['yearNumber']}</option>`;
            })
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    }
}

function showTable(idTable, data = [])
{
    // RESET TABLE
    $("#"+idTable).DataTable().clear().destroy();

    if(idTable == 'table_pembayaran_haji') 
    {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat...`,
                zeroRecords : 'Data Yang Dicari Tidak Ditemukan',
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "align-middle" },
                { "targets" : [2, 3], "className" : "text-center align-middle", "width" : "15%" },
                { "targets" : [4], "className" : "text-center align-middle", "width" : "13%" },
                { "targets" : [5], "className" : "text-center align-middle", "width" : "8%" },
            ]
        });

        if(data.length > 0) {
            for(let i = 0; i < data.length; i++)
            {
                let transHajiID             = data[i]['trans_id'];
                let transHajiJemaahName     = data[i]['jemaah_name'];
                let transHajiJemaahPackage  = data[i]['jemaah_pkg'];
                let transHajiDepature       = data[i]['est_berangkat'];
                let transHajiPayment        = parseInt(data[i]['total_payment']);
                let transHajiHarga          = 0;
                
                switch (transHajiJemaahPackage) {
                    case 'Double'   :
                        transHajiHarga  = 20000;
                    break;
                    case 'Triple'   :
                        transHajiHarga  = 18500;
                    break;
                    case 'Quad' :
                        transHajiHarga  = 17500;
                    break;
                    default : transHajiHarga = 0;
                }

                let transHajiStatusBayar    = transHajiHarga == transHajiPayment ? `<span class="badge badge-sm badge-primary"><label class="no-margins">Lunas</label></span>` : (transHajiPayment < transHajiHarga ? `<span class="badge badge-sm badge-warning"><label class="no-margins">Kurang Bayar</label></span>` : `<span class="badge badge-sm badge-primary"><label class="no-margins">Lebih Bayar</label></span>`);
                
                let transHajiButtonAct      = `<button class="btn btn-sm btn-primary" value="${transHajiID}" title="Lihat Detail"><i class="fa fa-eye"></i></button>`;

                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${i + 1}</label>`,
                    `<label class="font-weight-normal no-margins">${transHajiJemaahName}</label>`,
                    `<label class="font-weight-normal no-margins">${transHajiJemaahPackage}</label>`,
                    `<label class="font-weight-normal no-margins">${transHajiDepature}</label>`,
                    `<label class="font-weight-normal no-margins">${transHajiStatusBayar}</label>`,
                    transHajiButtonAct
                ]).draw(false);
            }
        } 
    }

    // REMOVE FOOTER
    $("#"+idTable+"_wrapper").css('padding-bottom', '0px');
}

function doTransaction(url, type, data, message, processData = false, contentType = 'application/x-www-form-urlencoded; charset=UTF-8')
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