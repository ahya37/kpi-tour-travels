moment.locale('id');
var prsTempData = []; 
var base_url    = window.location.origin;
var latitude;
var longitude;
var today       = moment().format('YYYY-MM-DD');

$(document).ready(()    => {
    // showTable('table_absensi', '');
    // $("#prs_tgl_cari").daterangepicker({
    //     minDate     : moment(today, 'YYYY-MM-DD').subtract(1, 'year'),
    //     maxDate     : moment(today, 'YYYY-MM-DD').add(1, 'year'),
    //     autoApply   : false,
    //     format      : 'DD/MM/YYYY',
    //     setStartDate    : moment(today, 'YYYY-MM-DD'),
    //     locale  : {
    //         separator   : ' s/d ',
    //         cancelLabel : 'Batal',
    //         applyLabel  : 'Simpan',
    //     },
    // });

    // $("#btn_cari_data_absen").on('click', () => {
    //     const abs_user_id       = $("#prs_user_id").val();
    //     const abs_tanggal_cari  = $("#prs_tgl_cari").val().split(' s/d ');
    //     const abs_tanggal_awal  = moment(abs_tanggal_cari[0], 'DD/MM/YYYY').format('YYYY-MM-DD');
    //     const abs_tanggal_akhir = moment(abs_tanggal_cari[1], 'DD/MM/YYYY').format('YYYY-MM-DD');
    //     const abs_jml_hari      = moment(abs_tanggal_akhir, 'YYYY-MM-DD').diff(moment(abs_tanggal_awal, 'YYYY-MM-DD'), 'days') + 1;

    //     const abs_sendData      = {
    //         "tanggal_awal"      : abs_tanggal_awal,
    //         "tanggal_akhir"     : abs_tanggal_akhir,
    //         "user_id"           : abs_user_id,
    //         "jml_hari"          : abs_jml_hari,
    //     };
    //     showTable('table_absensi', abs_sendData);
    // })

    // GET DATA KEHADIRAN
    const abs_url   = base_url + "/dashboard/absensi/get_user_presence";
    const abs_type  = "GET";
    const abs_data  = {
        "data"  : {
            "user_id"           : $("#prs_user_id").val(),
            "selected_month"    : moment(today, 'YYYY-MM-DD').format('MM'),
            "selected_year"     : moment(today, 'YYYY-MM-DD').format('YYYY'),
        }
    };

    const ct_url    = base_url + "/pengajuan/listCuti";
    const ct_type   = "GET";
    const ct_data   = "";
    
    const api_url   = [
        doTrans(abs_url, abs_type, abs_data, "", true),
        doTrans(ct_url, ct_type, ct_data, "", true)
    ];

    Promise.allSettled(api_url)
        .then((success)     => {
            const abs_get_data  = success[0].value.data.length;
            $("#dashboard_total_absen_text").html(abs_get_data);

            const ct_get_data   = success[1].value.data.length;
            $("#dashboard_total_isc_text").html(ct_get_data);
        })
        .catch((error)      => {
            $("#dashboard_total_absen_text").html(0);
            $("#dashboard_total_isc_text").html(0);
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
    } else if(idTable == 'tbl_total_absen') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "Tidak Ada Data Yang Bisa Ditampilkan",
                zeroRecords : "Data Yang Dicari Tidak Ditemukan"
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1, 2, 3, 4, 5, 6], "className" : "text-center align-middle", "width" : "13%" },
            ],
            pageLength  : -1,
            paging      : false,
            bInfo       : false,
        });
        
        if(data != '') {

            function getDiffTime(time_1, time_2)
            {
                const countDiff     = moment(time_2, 'HH:mm:ss').diff(moment(time_1, 'HH:mm:ss'), 'seconds');
                const hours         = Math.floor(countDiff / 3600) < 10 ? "0"+Math.floor(countDiff / 3600) : Math.floor(countDiff / 3600);
                const minute        = Math.floor((countDiff % 3600) / 60) < 10 ? "0"+Math.floor((countDiff % 3600) / 60) : Math.floor((countDiff % 3600) / 60);
                const second        = countDiff % 60 < 10 ? "0"+countDiff % 60 : countDiff % 60;

                return hours+":"+minute+":"+second;
            }

            $("#"+idTable+" tbody .dataTables_empty").html("Data Ditemukan");
            let seq     = 1;
            let totalKeterlambatan  = moment.duration(0);
            let totalLemburan       = moment.duration(0);
            for(const item of data)
            {
                const prs_date      = item['prs_date'];
                const prs_in        = moment(item['prs_in_time'], 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss');
                const prs_out       = item['prs_out_time'] !== null ? moment(item['prs_out_time'], 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss') : "00:00:00";
                
                const prs_max_in    = "08:00:00";
                const prs_max_out   = moment(item['prs_date'], 'YYYY-MM-DD').format('dddd') == 'Sabtu' ? '13:30:00' : (moment(item['prs_date'], 'YYYY-MM-DD').format('dddd') == "Minggu" ? "00:00:00" : "16:00:00"); 

                const prs_jam_kerja     = prs_out != "00:00:00" ? getDiffTime(prs_in, prs_out) : "";
                const prs_jam_telat     = prs_out != "00:00:00" ? (prs_in > moment(prs_max_in, 'HH:mm:ss').add(1, 'seconds').format('HH:mm:ss') ? getDiffTime(prs_max_in, prs_in) : "00:00:00") : "00:00:00";
                const prs_jam_lebih     = prs_out != "00:00:00" ? (prs_out > moment(prs_max_out, 'HH:mm:ss').add(1, 'seconds').format('HH:mm:ss') ? getDiffTime(prs_max_out, prs_out) : "00:00:00") : "00:00:00";
                $("#"+idTable).DataTable().row.add([
                    "<label>" + seq++ + "</label>",
                    "<label>" + moment(prs_date, 'YYYY-MM-DD').format('dddd')+", "+ moment(prs_date, 'YYYY-MM-DD').format('DD/MM/YYYY') + "</label>",
                    "<label>" + prs_in + "</label>",
                    "<label>" + prs_out + "</label>",
                    "<label>" + prs_jam_kerja + "</label>",
                    "<label>" + prs_jam_telat + "</label>",
                    "<label>" + prs_jam_lebih + "</label>",
                ]).draw(false);

                const jam_telat     = moment.duration(prs_jam_telat == "" ? "00:00:00" : prs_jam_telat);
                const jam_lebih     = moment.duration(prs_jam_lebih == "" ? "00:00:00" : prs_jam_lebih);
                totalKeterlambatan.add(jam_telat);
                totalLemburan.add(jam_lebih);
            }

            $("#tbl_total_absen_keterlambatan").html(moment.utc(totalKeterlambatan.asMilliseconds()).format('HH:mm:ss'));
            $("#tbl_total_absen_lebih_jam").html(moment.utc(totalLemburan.asMilliseconds()).format('HH:mm:ss'));

            $("#tbl_total_absen_keterlambatan_1").html($("#tbl_total_absen_keterlambatan").text());
            $("#tbl_total_absen_lebih_jam_1").html($("#tbl_total_absen_lebih_jam").text());
            $("#tbl_total_absensi").html(data.length);
        } else {
            $("#"+idTable+" tbody .dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan");
        }

        $("#tbl_total_absen_title").removeClass('text-center');
        $("#tbl_total_absen_title").addClass('text-right');
    } else if(idTable == 'tbl_total_cuti') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan",
            },
            columnDefs  : [
                { "targets" : [0], "className" : "text-center", "width" : "8%" },
                { "targets" : [1], "className" : "text-left", "width" : "25%" },
                { "targets" : [3, 4], "className" : "text-center align-middle", "width" : "15%" },
            ],
            autoWidth   : false,
            pageLength  : -1,
            paging      : false,
            bInfo       : false,
        });

        if(data.length > 0) {
            $("#tbl_total_cuti tbody .dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan");

            let seq         = 1;
            var ct_status   = "";
            for(const item of data)
            {
                const ct_date   = item.emp_act_start_date == item.emp_act_end_date ? moment(item.emp_act_start_date, 'YYYY-MM-DD').format('DD/MM/YYYY') : moment(item.emp_act_start_date, 'YYYY-MM-DD').format('DD/MM/YYYY')+" s.d "+moment(item.emp_act_end_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
                const ct_desc   = item.emp_act_title;
                const ct_type   = item.emp_act_type;
                switch(item.emp_act_status) {
                    case '1' :
                        ct_status  = "<span class='badge badge-success'>Diterima</span>";
                    break;
                    case '2' :
                        ct_status  = "<span class='badge badge-danger'>Ditolak</span>"; 
                    break;
                    case '3' :
                        ct_status  = "<span class='badge badge-warning text-dark'>Menunggu Konfirmasi</span>";
                    break;
                }


                $("#"+idTable).DataTable().row.add([
                    seq++,
                    "<label>" + ct_date+ "</label>",    
                    "<label>" + ct_desc + "</label>",    
                    "<label>" + ct_type + "</label>",    
                    "<label>" + ct_status + "</label>",    
                ]).draw(false);
            }
        } else {
            $("#tbl_total_cuti tbody .dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan");
        }
    }
}

function showModal(idModal, jenis)
{
    if(idModal == 'modal_total_absen')
    {
        const user_id       = $("#prs_user_id").val();
        const current_month = moment(today, 'YYYY-MM-DD').format('MM');
        const current_year  = moment(today, 'YYYY-MM-DD').format('YYYY');

        const abs_url       = base_url + "/dashboard/absensi/get_user_presence";
        const abs_type      = "GET";
        const abs_data      = {
            "data"          : {
                "user_id"       : user_id,
                "selected_month": current_month,
                "selected_year" : current_year,
            }
        };
        const abs_msg       = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();

        doTrans(abs_url, abs_type, abs_data, abs_msg, true)
            .then((success)     => {
                // CLOSE MODAL
                Swal.close();
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                // SHOW TABLE
                showTable('tbl_total_absen', success.data);
            })
            .catch((error)      => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak Ada Data Yang Bisa Ditampilkan'
                });
            })
    } else if(idModal == 'modal_total_ketidakhadiran') {
        // GET DATA CUTI
        const cuti_url  = base_url + "/pengajuan/listCuti";
        const cuti_data = "";
        const cuti_type = "GET";
        const cuti_msg  = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();

        doTrans(cuti_url, cuti_type, cuti_data, cuti_msg, true)
            .then((success)     => {
                $("#"+idModal).modal({
                    keyboard    : false,
                    backdrop    : 'static'
                });
                
                const cuti_getData   = success.data;
                showTable('tbl_total_cuti', cuti_getData);
                Swal.close();
            })
            .catch((error)      => {
                console.log(error);
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Data Izin / Sakit / Cuti Tidak Ada',
                });
            })
        // $("#"+idModal).modal({
        //     backdrop    : 'static', 
        //     keyboard    : false
        // });

        // showTable('tbl_total_cuti', []);
    }
}

function closeModal(idModal)
{
    $("#"+idModal).modal('hide');
    clearUrl();
    if(idModal == 'modal_total_absen') {

    }
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


function clearUrl()
{
    var url     = window.location.href;
    var cleanUrl= url.split('#')[0];
    window.history.replaceState({}, document.title, cleanUrl);
}