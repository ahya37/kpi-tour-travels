var today       = moment().format('YYYY-MM-DD');
var baseURL     = window.location.origin;
var dataTahun   = [] 

for(let i = 0; i < 10; i++)
{
    let tahun   = moment(today, 'YYYY-MM-DD').subtract(i, 'years').year();
    dataTahun.push(tahun);
}

$(document).ready(() => {
    // SHOW SELECT
    showSelect('filter_tahun_umhaj', dataTahun, moment().year());
    // GET DATA
    let umrah_URL   = "tarik_data/umhaj/data_jadwal_umrah";
    let umrah_type  = "GET";
    let umrah_data  = {
        "tahun"     : moment(today).year(),
    };

    let transaction     = [
        doTransaction(umrah_URL, umrah_type, umrah_data, "", true)
    ];

    Promise.allSettled(transaction)
        .then((success)     => {
            let umrah_getData   = success[0].status == 'fulfilled' ? success[0].value.data : [];
            showTable('table_jadwal_umrah', umrah_getData);
        })
        .catch((err)        => {
            showTable('table_jadwal_umrah', []);
            $("#table_jadwal_umrah .dataTables_empty").html(`Tidak Ada Data Tour Code Pada Tahun ${moment(today).year()}`);
        })
})

function showModal(idModal, value)
{

}

function closeModal(idModal)
{

}

function showSelect(idSelect, data, value, seq = null)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });

    if(idSelect == 'filter_tahun_umhaj')
    {
        let html    = "<option selected disabled></option>";

        if(data.length > 0) {
            for(const item of data)
            {
                html    += `<option value="${item}">${item}</option>`;
            }
        }
        
        $("#"+idSelect).html(html);

        if(value != '') {
            $("#"+idSelect).val(value);
        }
    }
}

function showSelectDetail(idSelect, value)
{
    if(idSelect == 'filter_tahun_umhaj')
    {
        showTable('table_jadwal_umrah', []);
        // GET DATA
        let umrah_URL   = "tarik_data/umhaj/data_jadwal_umrah";
        let umrah_type  = "GET";
        let umrah_data  = {
            "tahun"     : value,
        };

        let transaction     = [
            doTransaction(umrah_URL, umrah_type, umrah_data, "", true)
        ];

        Promise.allSettled(transaction)
            .then((success)     => {
                let umrah_getData   = success[0].status == 'fulfilled' ? success[0].value.data : [];
                showTable('table_jadwal_umrah', umrah_getData);
            })
            .catch((err)        => {
                showTable('table_jadwal_umrah', []);
                $("#table_jadwal_umrah .dataTables_empty").html(`Tidak Ada Data Tour Code Pada Tahun ${moment(today).year()}`);
            })
    }
}

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();
    
    if(idTable == 'table_jadwal_umrah') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan..",
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "20%" },
                { "targets" : [2], "className" : "text-left align-middle" },
                { "targets" : [3], "className" : "text-left align-middle", "width" : "10%" },
                { "targets" : [4], "className" : "text-left align-middle", "width" : "10%" },
            ],
        });

        if(data.length > 0)
        {
            // SORT DESC
            const sortData  = data.sort((a, b)  => {
                let tgl1    = moment(a.UMRAH_DEPATURE, 'YYYY-MM-DD').format('YYYY-MM-DD');
                let tgl2    = moment(b.UMRAH_DEPATURE, 'YYYY-MM-DD').format('YYYY-MM-DD');
                return moment(tgl2).isBefore(moment(tgl1)) ? -1 : 1;
            })

            let seq = 1;
            for(const item of sortData)
            {
                let umrahTourCode   = item['UMRAH_TOUR_CODE'];
                let umrahTourLeader = item['UMRAH_MENTOR_NAME'];
                let umrahDepatureDate   = item['UMRAH_DEPATURE'];
                let umrahArrivalDate    = item['UMRAH_ARRIVAL'];

                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${seq++}</label>`,
                    `<label class="font-weight-normal no-margins">${umrahTourCode}</label>`,
                    `<label class="font-weight-normal no-margins">${umrahTourLeader}</label>`,
                    `<label class="font-weight-normal no-margins">${moment(umrahDepatureDate).format('YYYY-MM-DD')}</label>`,
                    `<label class="font-weight-normal no-margins">${moment(umrahArrivalDate).format('YYYY-MM-DD')}</label>`,
                ]).draw(false);
            }
        }
    }
}

function doSyncData()
{
    let tahun   = $("#filter_tahun").val();
    
    let
}

function doTransaction(url, type, data, message, isAsync)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            async   : isAsync,
            url     : baseURL + "/" + url,
            cache   : false,
            type    : type,
            data    : data,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            beforeSend  : () => {
                message;
            },
            success     : (success) => {
                resolve(success)
            },
            error       : (err)     => {
                reject(err)
            }
        })
    })
}
