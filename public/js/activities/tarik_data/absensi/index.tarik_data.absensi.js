var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
$(document).ready(function(){
    console.log('test');

    showTable('table_group_division');

    $("#tgl_cari").daterangepicker({
        singleDatePicker    : true,
        autoApply           : true,
        minDate             : moment(today, 'YYYY-MM-DD').subtract(1, 'years').format('DD/MM/YYYY'),
        maxDate             : moment(today, 'YYYY-MM-DD').add(1, 'years').format('DD/MM/YYYY'),
    });

    // TEST KIRIM TEXT KE API
    const formData  = {
        "emp_id"    : "ini dari laravel",
    };
    
    const data      = new URLSearchParams(formData).toString();
    
    $.ajax({
        url     : 'http://localhost:3001/api/employees/test_get_dari_fe',
        cache   : false,
        type    : "POST",
        dataType: "json",
        headers : {
            'Content-Type' : 'application/x-www-form-urlencoded',
        },
        data    : data,
        success : (success) => {
            console.log(success)
        },
        error   : (err)     => {
            console.log(err)
        }
    })
});

function showTable(idTable)
{
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'table_group_division') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "Pilih Tanggal Untuk Menampilkan Data..",
            },
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "text-left align-middle"},
                { "targets" : [2, 4], "className" : "text-center align-middle", "width" : "15%" },
                { "targets" : [3, 5], "className" : "text-left align-middle", "width" : "25%" },
            ],
        });
    }
}

function cariData(id)
{
    if(id == 'table_group_division') {
        // DEFINE TABLE
        showTable('table_group_division');
        // GET DATA CARI
        const prs_url       = base_url + "/tarik_data/get_list_absensi";
        const prs_data      = {
            "tgl_cari"  : moment($("#tgl_cari").val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
        };
        $(".dataTables_empty").html("<i class='fa fa-spinner fa-spin'></i> Sedang Mencari Data..");
        
        doTrans(prs_url, "GET", prs_data, "", true)
            .then((success)     => {
                $(".dataTables_empty").html(success.message);
                const prs_getData   = success.data;
                for(const prs_item of prs_getData)
                {
                    $("#table_group_division").DataTable().row.add([
                        prs_item['abs_no'],
                        prs_item['abs_name'],
                        prs_item['abs_in'],
                        prs_item['abs_in_location'],
                        prs_item['abs_out'],
                        prs_item['abs_out_location'],
                    ]).draw(false);
                }
            })
            .catch((err)        => {
                console.log(err);
                $(".dataTables_empty").html(err.responseJSON.message);
            })
    } else if(id == 'table_group_division_tarik_data') {
        const prs_url       = base_url + "/tarik_data/absensi";
        const prs_data      = {
            "tgl_cari"      : moment($("#tgl_cari").val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
        };
        const prs_msg       = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
        
        doTrans(prs_url, "POST", prs_data, prs_msg, true)
            .then((success)     => {
                Swal.fire({
                    icon    : 'success',
                    title   : 'Berhasil',
                    text    : success.message,
                });
            })
            .catch((err)        => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Gagal Menarik Data'
                })
            })
    }
}

function doTrans(url, type, data, message, isAsync)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            url     : url,
            async   : isAsync,
            cache   : false,
            type    : type,
            headers : {
                "X-CSRF-TOKEN"  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                message
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