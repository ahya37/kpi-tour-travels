// INDEX JS DASHBOARD

moment().locale('id');
var today   = moment().format('YYYY-MM-DD');
var abs_data_global     = [];
var base_url          = window.location.origin;

$(document).ready(() => {
    // GET DATA PENGAJUAN
    const pgj_url   = "/pengajuan/listCuti";
    const pgj_type  = "GET";
    const pgj_data  = "";

    // GET DATA EMPLOYEE
    const emp_url   = "/master/employees/trans/get/dataTableEmployee";
    const emp_data  = {
        "cari"  : "%",
    };
    const emp_type  = "GET";

    // GET DATA ABSEN
    const abs_url   = "/divisi/human_resource/absensi/list";
    const abs_type  = "GET";
    const abs_data  = {
        "tanggal_awal"  : moment().format('YYYY-MM-DD'),
        "tanggal_akhir" : moment().format('YYYY-MM-DD'),
        "user_id"       : "%",
        "jml_hari"      : 1
    };

    const pgj_lmb_url   = base_url + "/pengajuan/lembur/list_lembur";
    const pgj_lmb_type  = "GET";
    

    const sendData  = [
        doTrans(pgj_url, pgj_type, pgj_data, "", true),
        doTrans(emp_url, emp_type, emp_data, "", true),
        doTrans(abs_url, abs_type, abs_data, "", true),
        doTrans(pgj_lmb_url, pgj_lmb_type, "", "", true)
    ];

    Promise.allSettled(sendData)
        .then((success)     => {

            // EMP AREA
            const emp_getData   = success[1].value.data;
            $("#emp_total").html(emp_getData.length);
            

            // PENGAJUAN AREA
            let pgj_total_warn_count  = 0;
            const pgj_getData   = success[0].value.data;
            $("#pgj_total").html(pgj_getData.length);

            for(const item_pgj of pgj_getData)
            {
                if(item_pgj.emp_act_status == "3") {
                    pgj_total_warn_count++;
                }
            }
            if(pgj_total_warn_count > 0) {
                $("#pgj_confirmation_text").html(
                    `
                    <i class='fa fa-exclamation-triangle'></i>
                    `+pgj_total_warn_count+` Butuh Konfirmasi
                    `
                );
            }

            // ABSENSI AREA
            const abs_getData   = success[2].value.data;
            let abs_total       = 0;

            for(const abs_item of abs_getData)
            {
                if(abs_item.jam_masuk != '00:00:00')
                {
                    abs_total   += 1;
                } else {
                    abs_total   = abs_total;
                }
            }
            
            $("#abs_total").html(abs_total);

            // PENGAJUAN LEMBUR
            const pgj_lmb_getData   = success[3].value.data;
            let pgj_lmb_pending     = 0;

            for(const pgj_lmb_item of pgj_lmb_getData) {
                if(pgj_lmb_item['emp_trans_status'] == '3') {
                    pgj_lmb_pending++;
                }
            }

            $("#pgj_lmb_total").html(pgj_lmb_getData.length);
            if(pgj_lmb_pending > 0) {
                $("#pgj_lmb_confirmation_text").html("<i class='fa fa-exclamation-triangle'></i> <label class='no-margins'>" + pgj_lmb_pending+" Butuh Konfirmasi</label>");
            }

        })
        .catch((err)        => {
            console.log(err);
        })
})

function showModal(idModal, jenis, data)
{
    if(idModal == 'modal_pgj')
    {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

        showTable('table_list_pengajuan', '');
    } else if(idModal == 'modal_abs') {
        // GET DATA
        const emp_url   = "/divisi/master/getDataEmployees";
        const emp_data  = "";
        const emp_type  = "GET";

        const sendData  = [
            doTrans(emp_url, emp_type, emp_data, "", true)
        ];

        const message   = Swal.fire({ title : 'Data Sedang Dimuat', allowOutsideClick: false }); Swal.showLoading();

        Promise.allSettled(sendData)
            .then((success)     => {
                // CLOSE LOADING
                Swal.close();
                // GET DATA EMPLOYEES
                const emp_getData   = success[0]['value']['data'];
                // SHOW SELECT
                showSelect('abs_user_cari', emp_getData, '', '');
                // SHOW TABLE
                showTable('table_list_absensi', '');
                // SHOW DATERANGEPICKER
                $("#abs_tgl_cari").daterangepicker({
                    minDate     : moment(today, 'YYYY-MM-DD').subtract(1, 'year'),
                    maxDate     : moment(today, 'YYYY-MM-DD').add(1, 'year'),
                    autoApply   : true,
                    format      : 'DD/MM/YYYY',
                    setStartDate    : moment(today, 'YYYY-MM-DD'),
                    locale  : {
                        separator   : ' s/d ',
                        cancelLabel : 'Batal',
                        applyLabel  : 'Simpan',
                    },
                });
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });  
            })
            .catch((err)        => {
                Swal.close();
                console.log(err);
            })
    } else if(idModal == 'modal_emp') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

        showTable('table_emp', '');
    } else if(idModal == 'modal_pgj_lmb') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
        
        showTable('table_pgj_lmb', '');
    } else if(idModal == 'modal_pgj_lmb_preview') {

        // GET DATA PENGAJUAN LEMBUR DETAIL
        const pgj_lmb_prev_url  = base_url + "/pengajuan/lembur/get_data";
        const pgj_lmb_prev_type = "GET";
        const pgj_lmb_prev_data = {
            "lmb_id"    : data,
        };
        const pgj_lmb_prev_msg  = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();

        doTrans(pgj_lmb_prev_url, pgj_lmb_prev_type, pgj_lmb_prev_data, pgj_lmb_prev_msg, true)
            .then((success)     => {
                Swal.close();
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                // CLOSE MODAL BEFORE
                closeModal('modal_pgj_lmb');
                // SHOW ALL DATA
                const pgj_lmb_getData_header    = success.data['header'][0];
                const pgj_lmb_getData_detail    = success.data['detail'];
                // FILL DATA HEADER INTO FORM HEADER
                $("#pgj_lmb_act_id").val(pgj_lmb_getData_header['emp_act_id']);
                $("#pgj_lmb_user_name").val(pgj_lmb_getData_header['emp_user_name']);
                $("#pgj_lmb_user_division").val(pgj_lmb_getData_header['emp_group_division']);
                $("#pgj_lmb_act_desc").val(pgj_lmb_getData_header['emp_act_description']);
                // SHOW TABLE
                showTable('tbl_pgj_lmb_preview', pgj_lmb_getData_detail);


                if(pgj_lmb_getData_header['emp_act_status'] != '3') {
                    $("#pgj_lmb_btn_terima").prop('disabled', true);
                    $("#pgj_lmb_btn_tolak").prop('disabled', true);
                } else {
                    $("#pgj_lmb_btn_terima").prop('disabled', false);
                    $("#pgj_lmb_btn_tolak").prop('disabled', false);
                }
            })
            .catch((err)        => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Data yang dicari tidak ditemukan'
                })
            })

        // showTable('tbl_pgj_lmb_preview', data);
    }
}

function closeModal(idModal)
{
    $("#"+idModal).modal('hide');

    if(idModal == 'modal_pgj')
    {
        $("#"+idModal).on('hidden.bs.modal', () => {
            clearUrl();
        })
    } else if(idModal == 'modal_abs') {
        $("#"+idModal).on('hidden.bs.modal', () => {
            clearUrl();

            $("#abs_tgl_cari").data('daterangepicker').setStartDate(moment().format('DD/MM/YYYY'));
            $("#abs_tgl_cari").data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
        });
    } else if(idModal == 'modal_emp') {
        $("#"+idModal).on('hidden.bs.modal', () => {
            clearUrl();
        });
    } else if(idModal == 'modal_pgj_lmb') {
        clearUrl();
    } else if(idModal == 'modal_pgj_lmb_preview') {
        showModal('modal_pgj_lmb', '', ''); 
    }
}

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'table_list_pengajuan')
    {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan" 
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "width" : "5%", "className" : "text-center align-middle" },
                { "targets" : [1], "width" : "15%", "className" : "text-left align-middle" },
                { "targets" : [2], "width" : "20%", "className" : "text-left align-middle" },
                { "targets" : [3], "className" : "text-left align-middle" },
                { "targets" : [4], "width" : "5%", "className" : "text-center align-middle" },
                { "targets" : [5], "width" : "5%", "className" : "text-center align-middle" },
                { "targets" : [6], "width" : "10%", "className" : "text-center align-middle" },
            ]
        });

        // GET DATA
        const pgj_url   = "/pengajuan/listCuti";
        const pgj_type  = "GET";
        const pgj_data  = "";
        const pgj_msg   = "";

        doTrans(pgj_url, pgj_type, pgj_data, pgj_msg, true)
            .then((success) => {
                const pgj_getData   = success.data;
                if(pgj_getData.length > 0) {
                    $(".dataTables_empty").html("Data Telah Dimuat");
                    let i = 1;
                    for(const item of pgj_getData)
                    {
                        const pgj_num           = i++;
                        const pgj_id            = item.emp_act_id;
                        const pgj_username      = item.emp_act_user_name;
                        const pgj_date          = item.emp_act_end_date == item.emp_act_start_date ? moment(item.emp_act_start_date, 'YYYY-MM-DD').format('DD-MMM-YYYY') : moment(item.emp_act_start_date, 'YYYY-MM-DD').format('DD-MMM-YYYY')+" s/d "+moment(item.emp_act_end_date, 'YYYY-MM-DD').format('DD-MMM-YYYY');
                        const pgj_title         = item.emp_act_title.length > 40 ? item.emp_act_title.substring(0, 40)+"..." : item.emp_act_title;
                        const pgj_type          = item.emp_act_type;
                        const pgj_status        = item.emp_act_status;
                        var isDisabled          = item.emp_act_status != "3" ? "disabled" : "";
                        const pgj_btnConfirm    = "<button class='btn btn-sm btn-primary' type='button' value='" + pgj_id + "' title='Disetujui' onclick='doSimpan(`pengajuan`, `terima`, this.value)' "+isDisabled+"><i class='fa fa-check'></i></button>";
                        const pgj_btnReject     = "<button class='btn btn-sm btn-danger' type='button' value='" + pgj_id + "' title='Ditolak' onclick='doSimpan(`pengajuan`, `tolak`, this.value)' "+isDisabled+"><i class='fa fa-times'></i></button>";

                        switch(pgj_status) {
                            case "1" :
                                var pgj_statusName    = "<span class='badge badge-pills bg-primary'><label class='no-margins font-weight-normal'><h5 class='no-margins'>Disetujui</h5></label></sppan>";
                            break;
                            case "2" :
                                var pgj_statusName    = "<span class='badge badge-pills bg-danger'><label class='no-margins font-weight-normal'><h5 class='no-margins'>Ditolak</h5></label></span>";
                            break;
                            case "3" : 
                                var pgj_statusName    = "<span class='badge badge-pills bg-warning text-dark'><label class='no-margins font-weight-normal'><h5 class='no-margins'>Menunggu Konfirmasi</h5></label></span>";
                        }

                        $("#"+idTable).DataTable().row.add([
                            pgj_num,
                            pgj_username,
                            pgj_date,
                            "<label class='no-margins font-weight-normal' title='" + item.emp_act_title + "'>" + pgj_title + "</label>",
                            pgj_type,
                            pgj_statusName,
                            pgj_btnConfirm+"&nbsp;"+pgj_btnReject
                        ]).draw(false);
                    }
                } else {
                    $(".dataTables_empty").html("Tidak Ada Data Yang Bisa Dimuat");
                }
            })
            .catch((err)    => {
                console.log(err);
                $(".dataTables_empty").html("Tidak Ada Data Yang Bisa Dimuat");
            })
    } else if(idTable == 'table_list_absensi') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan",
            },
            autoWidth   : false,
            ordering    : false,
            columnDefs  : [
                { "targets" : [0], "width" : "10%", "className" : "text-center align-middle" },
                { "targets" : [1], "className" : "text-left align-middle" },
                { "targets" : [2], "width" : "15%", "className" : "text-center align-middle" },
                { "targets" : [3], "width" : "15%", "className" : "text-center align-middle" },
                { "targets" : [4], "width" : "15%", "className" : "text-center align-middle" },
                { "targets" : [5], "width" : "15%", "className" : "text-center align-middle" },
            ],
            pageLength  : -1,
            paging      : false,
        });
        
        // TAMPIL DATA
        if(data != '') {
            abs_data_global = [];
            const abs_url   = "/divisi/human_resource/absensi/list";
            const abs_data  = data;
            const abs_type  = "GET";
            
            doTrans(abs_url, abs_type, abs_data, '', true)
                .then((success) => {
                    $(".dataTables_empty").html("Data Berhasil Dimuat");
                    
                    const abs_getData       = success.data;
                    const abs_waktu_masuk   = "08:00:00";
                    const abs_waktu_pulang  = "16:00:00";

                    for(const abs_item of abs_getData)
                    {
                        // MASUK
                        if(moment(abs_item.jam_masuk, 'HH:mm:ss') > moment(abs_waktu_masuk, 'HH:mm:ss'))
                        {
                            const jam_masuk_diff        = moment(abs_item.jam_masuk, 'HH:mm:ss').diff(moment(abs_waktu_masuk, 'HH:mm:ss'));
                            const jam_masuk_duration    = moment.duration(jam_masuk_diff);
                            const jam_masuk_hour        = Math.floor(jam_masuk_duration.asHours()) < 10 ? "0"+Math.floor(jam_masuk_duration.asHours()) : Math.floor(jam_masuk_duration.asHours());
                            const jam_masuk_min         = jam_masuk_duration.minutes() < 10 ? "0"+jam_masuk_duration.minutes() : jam_masuk_duration.minutes();
                            const jam_masuk_sec         = jam_masuk_duration.seconds() < 10 ? "0"+jam_masuk_duration.seconds() : jam_masuk_duration.seconds();

                            var jam_masuk               = jam_masuk_hour+":"+jam_masuk_min+":"+jam_masuk_sec;
                        } else {
                            var jam_masuk               = "00:00:00";
                        }

                        if(moment(abs_item.jam_keluar, 'HH:mm:ss') > moment(abs_waktu_pulang, 'HH:mm:ss'))
                        {
                            const jam_keluar_diff       = moment(abs_item.jam_keluar, 'HH:mm:ss').diff(moment(abs_waktu_pulang, 'HH:mm:ss'));
                            const jam_keluar_duration   = moment.duration(jam_keluar_diff);
                            const jam_keluar_hour       = Math.floor(jam_keluar_duration.asHours()) < 10 ? "0"+Math.floor(jam_keluar_duration.asHours()) : Math.floor(jam_keluar_duration.asHours());
                            const jam_keluar_min        = jam_keluar_duration.minutes() < 10 ? "0"+jam_keluar_duration.minutes() : jam_keluar_duration.minutes();
                            const jam_keluar_sec        = jam_keluar_duration.seconds() < 10 ? "0"+jam_keluar_duration.seconds() : jam_keluar_duration.seconds();
                            
                            var jam_keluar              = jam_keluar_hour+":"+jam_keluar_min+":"+jam_keluar_sec;
                        } else {
                            var jam_keluar              = "00:00:00";
                        }
                        
                        // FOR TABLE PURPOSE
                        // console.log({
                        //     "tanggal"   : abs_item.tanggal_absen,
                        //     "hari"      : moment(abs_item.tanggal_absen, 'YYYY-MM-DD').format('dddd'),
                        // })

                        const abs_tgl       = moment(abs_item.tanggal_absen, 'YYYY-MM-DD').format('dddd')  == 'Minggu' ? "<label class='no-margins font-weight-normal text-danger'>"+ abs_item.tanggal_absen +"</label>" : "<label class='no-margins font-weight-normal'>" + abs_item.tanggal_absen + "</label>";
                        const abs_emp_name  = abs_item.nama;
                        const abs_emp_in    = moment(abs_item.jam_masuk, 'HH:mm:ss').format('HH:mm:ss');
                        const abs_emp_out   = moment(abs_item.jam_keluar, 'HH:mm:ss').format('HH:mm:ss');
                        $("#"+idTable).DataTable().row.add([
                            abs_tgl,
                            abs_emp_name,
                            abs_emp_in,
                            abs_emp_out,
                            jam_masuk,
                            jam_keluar
                        ]).draw(false);
                    }
                })
                .catch((err)    => {
                    $(".dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan");
                })
        } else {
            $(".dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan");
        }

    } else if(idTable == 'table_emp') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                zeroRecords : "Data Yang Dicari Tidak Ditemukan"
            },
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "text-left align-middle" },
                { "targets" : [2], "className" : "text-left align-middle", "width" : "35%" },
                { "targets" : [3], "className" : "text-left align-middle", "width" : "20%" },
                { "targets" : [4], "className" : "text-center align-middle", "width" : "10%" },
            ],
            autoWidth   : false,
        });

        // GET DATA
        const emp_url   = base_url + "/divisi/human_resource/employee/list";
        const emp_type  = "GET";
        const emp_data  = {
            "cari"  : '%',
        };

        doTrans(emp_url, emp_type, emp_data, "", true)
            .then((success)     => {
                const emp_getData   = success.data;
                let seq = 0;
                for(emp of emp_getData)
                {
                    $("#"+idTable).DataTable().row.add([
                        seq++,
                        emp.emp_name,
                        emp.emp_division,
                        emp.emp_role,
                        emp.emp_is_active == '1' ? "<button class='btn btn-sm btn-primary' value='" + emp.emp_id + "' onclick='doSimpan(`aktivasi`, `active`, this.value)'>Aktif</button>" : "<button class='btn btn-sm btn-danger' value='" + emp.emp_id + "' onclick='doSimpan(`aktivasi`, `deactive`, this.value)'>Tidak Aktif</button>",
                    ]).draw(false);
                };
            })
            .catch((err)        => {
                console.log(err);
            })
    } else if(idTable == 'table_pgj_lmb') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat...",
            },
            columnDefs  : [
                { "targets" : [0, 4], "className" : "text-center align-middle", "width" : '5%' },
                { "targets" : [1], "className" : "text-left align-middle" },
                { "targets" : [2], "className" : "text-left align-middle", "width" : "15%" },
                { "targets" : [3], "className" : "text-center align-middle", "width" : "15%" },
            ],
            autoWidth   : false
        });

        // GET DATA
        const pgj_lmb_url   = base_url + "/pengajuan/lembur/list_lembur";
        const pgj_lmb_type  = "GET";
        const pgj_lmb_data  = "";

        doTrans(pgj_lmb_url, pgj_lmb_type, pgj_lmb_data, '', true)
            .then((success)     => {
                const pgj_lmb_getData   = success.data;
                if(pgj_lmb_getData.length > 0) {
                    $(".dataTables_empty").html("Data Berhasil Dimuat");
                    for(let i = 0;  i < pgj_lmb_getData.length; i++) {
                        const pgj_lmb_id            = pgj_lmb_getData[i]['emp_act_id'];
                        const pgj_lmb_btn_preview   = "<button class='btn btn-sm btn-primary' value='"+pgj_lmb_id+"' type='button' title='Lihat Detail' onclick='showModal(`modal_pgj_lmb_preview`, ``, this.value)'><i class='fa fa-eye'></i></button>";
                        const pgj_lmb_user_name     = pgj_lmb_getData[i]['emp_user_name'];
                        const pgj_lmb_user_act_date = moment(pgj_lmb_getData[i]['emp_act_date'], 'YYYY-MM-DD').format('DD-MMM-YYYY');
                        switch(pgj_lmb_getData[i]['emp_trans_status'])
                        {
                            case '1' :
                                var pgj_lmb_status  = "<span class='badge badge-sm badge-pills badge-primary pt-1'><label class='no-margins'>Diterima</label></span>";
                            break;
                            case '2' :
                                var pgj_lmb_status  = "<span class='badge badge-sm badge-pills badge-danger pt-1'><label class='no-margins'>Ditolak</label></span>";    
                            break;
                            case '3' :
                                var pgj_lmb_status  = "<span class='badge badge-sm badge-pills badge-warning pt-1'><label class='no-margins text-dark'>Menunggu Konfirmasi</label></span>";
                            break;
                        }
                        $("#"+idTable).DataTable().row.add([
                            i + 1,
                            pgj_lmb_user_name,
                            pgj_lmb_user_act_date,
                            pgj_lmb_status,
                            pgj_lmb_btn_preview
                        ]).draw(false);
                    }

                } else {
                    $(".dataTables_empty").html("Tidak Ada Data Yang Bisa Dimuat..");
                }
            })
            .catch((err)        => {
                console.log(err);
            })
    } else if(idTable == 'tbl_pgj_lmb_preview') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
            },
            pageLength  : -1,
            autoWidth   : false,
            ordering    : false,
            bInfo       : false,
            searching   : false,
            paging      : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1, 3, 4], "className" : "text-center align-middle", "width" : "10%" },
            ],
        });

        if(data != '') {
            $(".dataTables_empty").html("Data Ditemukan");
            for(const pgj_lmb_item of data) {
                $("#"+idTable).DataTable().row.add([
                    pgj_lmb_item['empd_seq'],
                    moment(pgj_lmb_item['empd_date'], 'YYYY-MM-DD').format('DD-MMM-YYYY'),
                    pgj_lmb_item['empd_description'],
                    moment(pgj_lmb_item['empd_start_time'], 'YYYY-MM-DD HH:mm:ss').format('HH:mm'),
                    moment(pgj_lmb_item['empd_end_time'], 'YYYY-MM-DD HH:mm:ss').format('HH:mm'),
                ]).draw(false);
            }
        } else {
            $(".dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan");
        }
    }
}

function showSelect(idSelect, data, selectedData, seq)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });

    if(idSelect == 'abs_user_cari')
    {
        var html    = [
            "<option selected disabled>Pilih User</option>",
            "<option value='semua'>Semua</option>"
        ];
        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += "<option value='" + item.emp_id + "'>" + item.emp_name + "</option>";
            });
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    }
}

function showData(idData)
{
    if(idData == 'table_list_absensi')
    {
        const tanggal       = $("#abs_tgl_cari").val();
        const tanggal_awal  = tanggal.split(' s/d ')[0];
        const tanggal_akhir = tanggal.split(' s/d ')[1];
        const user          = $("#abs_user_cari").val();

        if(user == null) {
            $("#abs_user_cari").val('semua').trigger('change');
        }

        const sendData  = {
            "tanggal_awal"  : moment(tanggal_awal, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            "tanggal_akhir" : moment(tanggal_akhir, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            "user_id"       : user == 'semua' ? '%' : user,
            "jml_hari"      : moment(tanggal_akhir, 'DD/MM/YYYY').diff(moment(tanggal_awal, 'DD/MM/YYYY'), 'days') + 1,
        };

        showTable('table_list_absensi', sendData);
    } else if(idData == 'download_data_excel') {
        const tanggal       = $("#abs_tgl_cari").val();
        const tanggal_awal  = tanggal.split(' s/d ')[0];
        const tanggal_akhir = tanggal.split(' s/d ')[1];
        const user          = $("#abs_user_cari").val();

        if(user == null)
        {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Pilih User Terlebih Dahulu',
                didClose    : () => {
                    $("#abs_user_cari").select2('open');
                }
            });
        } else {
            const expAbs_url    = "/divisi/human_resource/absensi/excelDownload";
            const expAbs_data   = {
                "tanggal_awal"  : moment(tanggal_awal, 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "tanggal_akhir" : moment(tanggal_akhir, 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "user_id"       : user == "semua" ? '%' : user,
                "jml_hari"      : moment(tanggal_akhir, 'DD/MM/YYYY').diff(moment(tanggal_awal, 'DD/MM/YYYY'), 'days')+1,
            };
            const expAbs_type   = "GET";
            const expAbs_message= Swal.fire({ title : 'File Sedang Dibuat..' }); Swal.showLoading();
            
            doTrans(expAbs_url, expAbs_type, expAbs_data, expAbs_message, true)
                .then((success)     => {
                    // var url_download    = base_url+"/"+success.data.file_url;
                    var link = document.createElement('a');
                    link.href = base_url+"/"+success.data.file_url+"/"+success.data.file_name;
                    document.body.appendChild(link);
                    link.click();
                    Swal.close();
                    
                    // DELETE FILE
                    setTimeout(()=> {
                        const abs_del_data  = {
                            "file_url"  : success.data.file_url+"/"+success.data.file_name,
                        };
                        const abs_del_type  = "POST";
                        const abs_del_url   = "/divisi/human_resource/absensi/excelDelete";
                        
                        doTrans(abs_del_url, abs_del_type, abs_del_data, "", true)
                            .then((sc)  => {
                                console.log(sc);
                            })
                            .catch((err)    => {
                                console.log(err);
                            })
                    }, 1000);
                })
                .catch((err)    => {
                    console.log(err);
                    Swal.close();
                })
        }
    }
}

function doSimpan(type, jenis, data)
{
    switch(type)
    {
        case "pengajuan" :
            switch(jenis) {
                case "terima" :
                    Swal.fire({
                        icon    : 'question',
                        title   : 'Terima Pengajuan Ini?',
                        showConfirmButton   : true,
                        showCancelButton    : true,
                        confirmButtonText   : 'Ya, Terima',
                        cancelButtonText    : 'Batal',
                        confirmButtonColor  : '#1ab394',
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            const pgj_sendData = {
                                "pgj_id"        : data,
                                "pgj_title"     : "",
                                "pgj_date_start": "",
                                "pgj_date_end"  : "",
                                "pgj_type"      : "",
                                "pgj_status"    : "1",
                            };

                            const pgj_url       = "/pengajuan/simpanCuti";
                            const pgj_type      = "POST";
                            const pgj_message   = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();                         
                            doTrans(pgj_url, pgj_type, pgj_sendData, pgj_message, true)
                                .then((success) => {
                                    Swal.fire({
                                        icon    : success.alert.icon,
                                        title   : success.alert.message.title,
                                        text    : success.alert.message.text,
                                        didClose    : () => {
                                            showTable('table_list_pengajuan', '');
                                        }
                                    })
                                })
                                .catch((err)    => {
                                    console.log(err);
                                    Swal.fire({
                                        icon    : err.responseJSON.alert.icon,
                                        title   : err.responseJSON.alert.message.title,
                                        text    : err.responseJSON.alert.message.text,
                                    });
                                });
                        }
                    })
                break;
                case "tolak" :
                    Swal.fire({
                        icon    : 'question',
                        title   : 'Tolak Pengajuan Ini?',
                        showConfirmButton   : true,
                        showCancelButton    : true,
                        confirmButtonText   : 'Ya, Tolak',
                        cancelButtonText    : 'Batal',
                        confirmButtonColor  : '#ED5565',
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            const pgj_sendData = {
                                "pgj_id"        : data,
                                "pgj_title"     : "",
                                "pgj_date_start": "",
                                "pgj_date_end"  : "",
                                "pgj_type"      : "",
                                "pgj_status"    : "2",
                            };

                            const pgj_url       = "/pengajuan/simpanCuti";
                            const pgj_type      = "POST";
                            const pgj_message   = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();                         
                            doTrans(pgj_url, pgj_type, pgj_sendData, pgj_message, true)
                                .then((success) => {
                                    Swal.fire({
                                        icon    : success.alert.icon,
                                        title   : success.alert.message.title,
                                        text    : success.alert.message.text,
                                        didClose    : () => {
                                            showTable('table_list_pengajuan', '');
                                        }
                                    })
                                })
                                .catch((err)    => {
                                    console.log(err);
                                    Swal.fire({
                                        icon    : err.responseJSON.alert.icon,
                                        title   : err.responseJSON.alert.message.title,
                                        text    : err.responseJSON.alert.message.text,
                                    });
                                });
                        }
                    })
                break; 
            }
        break;
        case "aktivasi" :
            const emp_url   = base_url + "/divisi/human_resource/employee/ubahStatus";
            const emp_data  = {
                "emp_id"    : data,
                "emp_status": jenis,
            };
            const emp_type  = "POST";
            const emp_msg   = Swal.fire({ title : 'Permintaan Sedang Diproses' });Swal.showLoading();
            
            doTrans(emp_url, emp_type, emp_data, emp_msg, true)
                .then((success) => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text,
                    }).then((results)   => {
                        if(results.isConfirmed) {
                            showTable('table_emp', '');
                        }
                    })
                })
                .catch((err)    => {
                    Swal.fire({
                        icon    : err.responseJSON.alert.icon,
                        title   : err.responseJSON.alert.message.title,
                        text    : err.responseJSON.alert.message.text,
                    })
                });
        break;
        case "pengajuan_lembur" :
            switch(jenis) {
                case "terima" :
                    Swal.fire({
                        icon    : 'question',
                        title   : 'Konfirmasi Pengajuan?',
                        showConfirmButton   : true,
                        showCancelButton    : true,
                        confirmButtonText   : 'Ya, Terima',
                        cancelButtonText    : 'Batal',
                        confirmButtonColor  : '#1ab394',
                    }).then((results)   => {
                        if(results.isConfirmed) {
                            const pgj_lmb_url   = base_url + "/pengajuan/lembur/konfirmasi";
                            const pgj_lmb_data  = {
                                "emp_act_id"    : $("#pgj_lmb_act_id").val(),
                                "emp_act_status": "1",
                            };
                            const pgj_lmb_type  = "PUT";
                            const pgj_lmb_msg   = Swal.fire({ title : 'Data Sedang Diproses', allowOutsideClick: false }); Swal.showLoading();

                            doTrans(pgj_lmb_url, pgj_lmb_type, pgj_lmb_data, pgj_lmb_msg, true)
                                .then((success)     => {
                                    Swal.fire({
                                        icon    : success.alert.icon,
                                        title   : success.alert.message.title,
                                        text    : success.alert.message.text,
                                    }).then((res)   => {
                                        if(res.isConfirmed) {
                                            closeModal('modal_pgj_lmb_preview');
                                        }
                                    })
                                })
                                .catch((err)        => {
                                    Swal.fire({
                                        icon    : err.responseJSON.alert.icon,
                                        title   : err.responseJSON.alert.message.title,
                                        text    : err.responseJSON.alert.message.text,
                                    })
                                })
                        }
                    });
                break;
                case "tolak" :
                    Swal.fire({
                        icon    : 'question',
                        title   : 'Konfirmasi Pengajuan?',
                        showConfirmButton   : true,
                        showCancelButton    : true,
                        confirmButtonText   : 'Ya, Tolak',
                        cancelButtonText    : 'Batal',
                        confirmButtonColor  : '#ED5565',
                    }).then((results)   => {
                        if(results.isConfirmed) {
                            const pgj_lmb_url   = base_url + "/pengajuan/lembur/konfirmasi";
                            const pgj_lmb_data  = {
                                "emp_act_id"    : $("#pgj_lmb_act_id").val(),
                                "emp_act_status": "2",
                            };
                            const pgj_lmb_type  = "PUT";
                            const pgj_lmb_msg   = Swal.fire({ title : 'Data Sedang Diproses', allowOutsideClick: false }); Swal.showLoading();

                            doTrans(pgj_lmb_url, pgj_lmb_type, pgj_lmb_data, pgj_lmb_msg, true)
                                .then((success)     => {
                                    Swal.fire({
                                        icon    : success.alert.icon,
                                        title   : success.alert.message.title,
                                        text    : success.alert.message.text,
                                    }).then((res)   => {
                                        if(res.isConfirmed) {
                                            closeModal('modal_pgj_lmb_preview');
                                        }
                                    })
                                })
                                .catch((err)        => {
                                    Swal.fire({
                                        icon    : err.responseJSON.alert.icon,
                                        title   : err.responseJSON.alert.message.title,
                                        text    : err.responseJSON.alert.message.text,
                                    })
                                })
                        }
                    });
                break;
            }
        break;
    }
}

function doTrans(url, type, data, customMessage, isAsync)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            async   : isAsync,
            url     : url,
            type    : type,
            headers : {
                'X-CSRF-TOKEN' : CSRF_TOKEN
            },
            data    : data,
            beforeSend  : () => {
                customMessage;
            },
            success : (success) => {
                resolve(success);
            },
            error   : (err)     => {
                reject(err);
            }
        });
    })
}

function clearUrl()
{
    var url     = window.location.href;
    var cleanUrl= url.split('#')[0];
    window.history.replaceState({}, document.title, cleanUrl);
}