moment.locale('id');
var prsTempData = []; 
var base_url    = window.location.origin;
var latitude;
var longitude;
var today       = moment().format('YYYY-MM-DD');

$(document).ready(()    => {
    showTable('table_absensi', '');
    $("#prs_tgl_cari").daterangepicker({
        minDate     : moment(today, 'YYYY-MM-DD').subtract(1, 'year'),
        maxDate     : moment(today, 'YYYY-MM-DD').add(1, 'year'),
        autoApply   : false,
        format      : 'DD/MM/YYYY',
        setStartDate    : moment(today, 'YYYY-MM-DD'),
        locale  : {
            separator   : ' s/d ',
            cancelLabel : 'Batal',
            applyLabel  : 'Simpan',
        },
    });

    $("#btn_cari_data_absen").on('click', () => {
        const abs_user_id       = $("#prs_user_id").val();
        const abs_tanggal_cari  = $("#prs_tgl_cari").val().split(' s/d ');
        const abs_tanggal_awal  = moment(abs_tanggal_cari[0], 'DD/MM/YYYY').format('YYYY-MM-DD');
        const abs_tanggal_akhir = moment(abs_tanggal_cari[1], 'DD/MM/YYYY').format('YYYY-MM-DD');
        const abs_jml_hari      = moment(abs_tanggal_akhir, 'YYYY-MM-DD').diff(moment(abs_tanggal_awal, 'YYYY-MM-DD'), 'days') + 1;

        const abs_sendData      = {
            "tanggal_awal"      : abs_tanggal_awal,
            "tanggal_akhir"     : abs_tanggal_akhir,
            "user_id"           : abs_user_id,
            "jml_hari"          : abs_jml_hari,
        };
        showTable('table_absensi', abs_sendData);
    })
});

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'table_absensi')
    {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "zeroRecords"   : "Data yang dicari tidak ditemukan..",
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [1, 2, 3, 4], "width" : "20%", "className" : "text-left align-middle" },
            ],
            order       : [
                [0, 'desc'],
            ],
        });

        if(data != '') {
            $(".dataTables_empty").html("<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..");

            // GET DATA
            const abs_get_url   = "/divisi/human_resource/absensi/list";
            const abs_get_data  = data;
            const abs_get_type  = "GET";

            doTrans(abs_get_url, abs_get_type, abs_get_data, "", true)
                .then((success)     => {
                    var total_kurang_jam = moment.duration();
                    var total_lebih_jam = moment.duration();
                    var tanggal_awal    = moment("2024-07-15");
                    for(const abs_item of success.data)
                    {
                        // CHECK
                        const abs_date  = abs_item.tanggal_absen;
                        const abs_in    = abs_item.jam_masuk;
                        const abs_out   = abs_item.jam_keluar;

                        switch(moment(abs_date, 'YYYY-MM-DD').format('dddd'))
                        {
                            case 'Sabtu' :
                                if(moment(abs_date).isBefore(tanggal_awal)) {
                                    var jam_masuk   = moment(abs_date+" "+abs_in, 'YYYY-MM-DD HH:mm:ss').diff(moment(abs_date+" 08:30:00", 'YYYY-MM-DD HH:mm:ss'));
                                    var jam_keluar  = moment(abs_date+" "+abs_out, 'YYYY-MM-DD HH:mm:ss').diff(moment(abs_date+" 12:00:00", 'YYYY-MM-DD HH:mm:ss'));
                                } else {
                                    var jam_masuk   = moment(abs_date+" "+abs_in, 'YYYY-MM-DD HH:mm:ss').diff(moment(abs_date+" 08:00:00", 'YYYY-MM-DD HH:mm:ss'));
                                    var jam_keluar  = moment(abs_date+" "+abs_out, 'YYYY-MM-DD HH:mm:ss').diff(moment(abs_date+" 13:30:00", 'YYYY-MM-DD HH:mm:ss'));
                                }
                                var abs_date_1  = "<label class='no-margins' title='" + moment(abs_date, 'YYYY-MM-DD').format('dddd') + "'>" + abs_date + "</label>";
                            break;
                            case 'Minggu' :
                                var jam_masuk   = moment(abs_date+" "+abs_in, 'YYYY-MM-DD HH:mm:ss').diff(moment(abs_date+" 00:00:00", 'YYYY-MM-DD HH:mm:ss'));
                                var jam_keluar  = moment(abs_date+" "+abs_out, 'YYYY-MM-DD HH:mm:ss').diff(moment(abs_date+" 00:00:00", 'YYYY-MM-DD HH:mm:ss'));
                                var abs_date_1  = "<label class='no-margins text-danger' title='" + moment(abs_date, 'YYYY-MM-DD').format('dddd') + "'>" + abs_date + "</label>";
                            break;
                            default :
                                if(moment(abs_date).isBefore(tanggal_awal)) {
                                    var jam_masuk   = moment(abs_date+" "+abs_in, 'YYYY-MM-DD HH:mm:ss').diff(moment(abs_date+" 08:30:00", 'YYYY-MM-DD HH:mm:ss'));
                                    var jam_keluar  = moment(abs_date+" "+abs_out, 'YYYY-MM-DD HH:mm:ss').diff(moment(abs_date+" 16:30:00", 'YYYY-MM-DD HH:mm:ss'));
                                } else {
                                    var jam_masuk   = moment(abs_date+" "+abs_in, 'YYYY-MM-DD HH:mm:ss').diff(moment(abs_date+" 08:00:00", 'YYYY-MM-DD HH:mm:ss'));
                                    var jam_keluar  = moment(abs_date+" "+abs_out, 'YYYY-MM-DD HH:mm:ss').diff(moment(abs_date+" 16:00:00", 'YYYY-MM-DD HH:mm:ss'));
                                }
                                var abs_date_1  = "<label class='no-margins' title='" + moment(abs_date, 'YYYY-MM-DD').format('dddd') + "'>" + abs_date + "</label>";
                        }
                        
                        const jam_masuk_duration    = moment.duration(jam_masuk);
                        const jam_diff_masuk        = jam_masuk > 0 ? moment.utc(jam_masuk_duration.asMilliseconds()).format('HH:mm:ss') : '00:00:00';
                        const jam_keluar_duration   = moment.duration(jam_keluar);
                        const jam_diff_keluar       = jam_keluar > 0 ? moment.utc(jam_keluar_duration.asMilliseconds()).format('HH:mm:ss') : '00:00:00';

                        jam_masuk > 0 ? total_kurang_jam.add(jam_masuk_duration) : total_kurang_jam.add(0);
                        jam_keluar > 0 ? total_lebih_jam.add(jam_keluar_duration) : total_kurang_jam.add(0);

                        $("#"+idTable).DataTable().row.add([
                            abs_date_1,
                            abs_in,
                            abs_out, 
                            jam_diff_masuk,
                            jam_diff_keluar
                        ]).draw(false);
                    }
                    $("#table_absensi_total_kurang_jam").html(moment.utc(total_kurang_jam.asMilliseconds()).format('HH:mm:ss'));
                    $("#table_absensi_total_lebih_jam").html(moment.utc(total_lebih_jam.asMilliseconds()).format('HH:mm:ss'));
                })
                .catch((err)        => {
                    // $(".dataTables_empty").html("Tidak ada data yang bisa dimuat");
                })
        } else {
            $(".dataTables_empty").html("Tidak ada data yang bisa dimuat");
        }
    }
}

function showModal(idModal, jenis)
{
}

function closeModal(idModal)
{
}

function simpanData(jenis) 
{
}

function doTrans(url, type, data, customMessage, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            cache   : false,
            type    : type,
            async   : isAsync,
            url     : url,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : function() {
                customMessage;
            },
            success     : function(xhr) {
                resolve(xhr)
            },
            error       : function(xhr) {
                reject(xhr)
            }
        })
    });
}