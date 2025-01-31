// INDEX JS DASHBOARD
moment().locale('id');
var today               = moment().format('YYYY-MM-DD');
var abs_data_global     = [];
var base_url            = window.location.origin;
var dataBulan           = [];

clearUrl();

for(let i = 0; i < 12; i++) {
    dataBulan.push({
        "bulan_ke"  : moment(i + 1, 'M').format('MM'),
        "bulan_name": moment(i + 1, 'M').format('MMMM'),
    })
}

$(document).ready(() => {
    // GET DATA PENGAJUAN
    const pgj_url   = "/pengajuan/listCuti";
    const pgj_type  = "GET";
    const pgj_data  = {
        "bulan" : moment(today, 'YYYY-MM-DD').format('MM')
    };

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
    const pgj_lmb_data  = {
        "bulan" : moment(today, 'YYYY-MM-DD').format('MM')
    };
    

    const sendData  = [
        doTrans(pgj_url, pgj_type, pgj_data, "", true),
        doTrans(emp_url, emp_type, emp_data, "", true),
        doTrans(abs_url, abs_type, abs_data, "", true),
        doTrans(pgj_lmb_url, pgj_lmb_type, pgj_lmb_data, "", true)
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
        let bulanSekarang   = data == '' ? moment(today, 'YYYY-MM-DD').format('MM') : data;
        showSelect('pgj_select_month', dataBulan, bulanSekarang, '');
        showTable('table_list_pengajuan', []);
        // GET DATA PENGAJUAN CUTI
        let pgj_URL     = base_url + "/pengajuan/listCuti";
        let pgj_type    = "GET";
        let pgj_data    = {
            "bulan"     : bulanSekarang,
        };
        let pgj_msg     = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();

        doTrans(pgj_URL, pgj_type, pgj_data, pgj_msg, true)
            .then((success)     => {
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop : 'static', keyboard: false });

                // SHOW DATA
                let pgj_getData     = success.data;
                if(pgj_getData.length > 0) {
                    showTable('table_list_pengajuan', pgj_getData);
                    $(".dataTables_empty").html('Berhasil Menampilkan Data');
                } else {
                    showTable('table_list_pengajuan', []);
                    $(".dataTables_empty").html('Tidak Ada Data Yang Bisa Ditampilkan');
                }
                // CLOSE MODAL
                Swal.close();
            })
            .catch((err)        => {
                // CLOSE SWAL
                Swal.close();
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                console.log(err);
                // SHOW TABLE
                showTable('table_list_pengajuan', []);
                $(".dataTables_empty").html('Tidak Ada Data Yang Bisa Ditampilkan');
            })
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
        const openModal     = (idModal) => {
            $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
        }
        
        // GET DATA KARYAWAN
        let employee_URL        = base_url + "/divisi/human_resource/employee/list";
        let employee_type       = "GET";
        let employee_data       = {
            "cari"  : '%',
        };
        let employee_msg        = Swal.fire({ title : "Data Sedang Dimuat", allowOutsideClick: false }); Swal.showLoading();
        
        doTrans(employee_URL, employee_type, employee_data, employee_msg, true)
            .then((success)     => {
                let employee_getData    = success.data;
                Swal.close();
                openModal(idModal);
                showTable('table_emp', employee_getData);
            })
            .catch((err)        => {
                Swal.close();
                openModal(idModal);
                showTable('table_emp', []);
            })

        showTable('table_emp', '');
    } else if(idModal == 'modal_pgj_lmb') {
        let bulanSekarang   = data == '' ? moment(today, 'YYYY-MM-DD').format('MM') : data;
        showSelect('select_pgj_month', dataBulan, bulanSekarang, '');
        showTable('table_pgj_lmb', []);
        // GET DATA LEMBURAN DULU
        let pgjLembur_URL   = base_url + "/pengajuan/lembur/list_lembur";
        let pgjLembur_type  = "GET";
        let pgjLembur_data  = {
            "bulan"         : data || bulanSekarang,
        };
        let pgjLembur_msg   = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

        doTrans(pgjLembur_URL, pgjLembur_type, pgjLembur_data, pgjLembur_msg, true)
            .then((success) => {
                Swal.close();
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                // SHOW TABLE
                let pgjLembur_getData   = success.data;
                if(pgjLembur_getData.length > 0) {
                    showTable('table_pgj_lmb', pgjLembur_getData);
                    $(".dataTables_empty").html('Berhasil Memuat Data');
                } else {
                    $(".dataTables_empty").html('Tidak Ada Data Yang Bisa Ditampilkan');
                }
            })
            .catch((err)    => {
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                // SHOW TABLE
                showTable('table_pgj_lmb', []);
                $(".dataTables_empty").html('Tidak Ada Data Yang Bisa Dimuat');
            })
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
    } else if(idModal == 'modal_pgj_lmb_preview_tolak') {
        $("#"+idModal).modal({backdrop: 'static', keyboard: false});
        $("#"+idModal).on('shown.bs.modal', () => {
            $("#pgj_lmb_note_tolak").focus();
        });
    } else if(idModal == 'modal_pgj_tolak') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
        $("#pgj_id").val(data);

        $("#"+idModal).on('shown.bs.modal', () => {
            $("#pgj_cuti_note").focus();
        })
    } else if(idModal == 'modal_edit_jam_kerja') {
        const userID    = data.split('|')[0];
        const tanggal   = data.split('|')[1];

        const sendData  = {
            "tanggal_awal"  : tanggal,
            "tanggal_akhir" : tanggal,
            "user_id"       : userID,
            "jml_hari"      : 1,
        };

        const url       = base_url + "/divisi/human_resource/absensi/list";
        const type      = "GET";
        const msg       = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

        doTrans(url, type, sendData, msg, true)
            .then((results)     => {
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                const getData   = results.data[0];

                const nama      = getData.nama;
                const jamMasuk  = moment(getData.jam_masuk, 'HH:mm:ss').format('HH:mm');
                const jamKeluar = moment(getData.jam_keluar, 'HH:mm:ss').format('HH:mm');
                const tanggal   = moment(getData.tanggal_absen, 'YYYY-MM-DD').format('DD/MM/YYYY');

                $("#edit_jam_kerja_nama").val(nama);
                $("#edit_jam_kerja_user_id").val(userID);
                $("#edit_jam_kerja_tanggal").val(tanggal);
                $("#edit_jam_kerja_jam_masuk").val(jamMasuk);
                $("#edit_jam_kerja_jam_keluar").val(jamKeluar);

                Swal.close();
            })
            .catch((error)      => {
                console.log(error);
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Data Yang Dicari Tidak Ditemukan',
                })
            })
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
        let bulanSekarang   = $("#select_pgj_month").val();
        showModal('modal_pgj_lmb', '', bulanSekarang); 
    } else if(idModal == 'modal_pgj_lmb_preview_tolak') {
        $("#"+idModal).modal('hide');
    } else if(idModal == 'modal_edit_jam_kerja') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#edit_jam_kerja_nama").val(null);
            $("#edit_jam_kerja_user_id").val(null);
            $("#edit_jam_kerja_jam_masuk").val(null);
            $("#edit_jam_kerja_jam_keluar").val(null);
            $("#edit_jam_kerja_telat").val(null);
            $("#edit_jam_kerja_lebih").val(null);
        });
    }
}

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'table_list_pengajuan')
    {
        $("#"+idTable).DataTable().clear().destroy();
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
            ],
        });

        if(data.length > 0) {
            let seq     = 1;
            for(const item of data)
            {
                let pengajuan_num       = seq++;
                let pengajuan_id        = item['emp_act_id'];
                let pengajuan_userName  = item['emp_act_user_name'];
                let pengajuan_date      = item['emp_act_end_date'] == item['emp_act_start_date'] ? moment(item['emp_act_start_date'], 'YYYY-MM-DD').format('DD-MM-YYYY') : moment(item['emp_act_start_date'], 'YYYY-MM-DD').format('DD-MM-YYYY') + " s/d " + moment(item['emp_act_end_date'], 'YYYY-MM-DD').format('DD-MM-YYYY');
                let pengajuan_title     = item['emp_act_title'].length > 40 ? item['emp_act_title'].substring(0, 40)+"..." : item['emp_act_title'];
                let pengajuan_type      = item['emp_act_type'];
                let isDisabled          = item['emp_act_status'] != '3' ? 'disabled' : '';

                switch(item['emp_act_status']) {
                    case '1' :
                        var pengajuan_status    = `<span class="badge badge-pills bg-primary"><label class="no-margins font-weight-normal">Disetujui</label></span>`;
                    break;
                    case '2' : 
                        var pengajuan_status    = `<span class="badge badge-pills bg-danger"><label class="no-margins font-weight-normal">Ditolak</label></span>`;
                    break;
                    case '3' : 
                        var pengajuan_status    = `<span class="badge badge-pills bg-warning"><label class="no-margins font-weight-normal text-dark">Menunggu Konfirmasi</label></span>`;
                    break;
                }
                let pengajuan_confirm   = `<button type="button" class="btn btn-sm btn-primary" title="Setuju Cuti" onclick="doSimpan('pengajuan', 'terima', '${pengajuan_id}')" ${isDisabled}><i class="fa fa-check"></i></button>`;
                let pengajuan_reject    = `<button type="button" class="btn btn-sm btn-danger" title="Tolak Cuti" onclick="doSimpan('pengajuan', 'tolak', '${pengajuan_id}')" ${isDisabled}><i class="fa fa-times"></i></button>`;

                $("#"+idTable).DataTable().row.add([
                    `<label class="no-margins font-weight-normal">${pengajuan_num}</label>`,
                    `<label class="no-margins font-weight-normal">${pengajuan_userName}</label>`,
                    `<label class="no-margins font-weight-normal">${pengajuan_date}</label>`,
                    `<label class="no-margins font-weight-normal">${pengajuan_title}</label>`,
                    `<label class="no-margins font-weight-normal">${pengajuan_type}</label>`,
                    pengajuan_status,
                    pengajuan_confirm+" "+pengajuan_reject,
                ]).draw(false);
            }
        }
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
                { "targets" : [6], "width" : "5%", "className" : "text-center align-middle" },
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
                    let abs_waktu_masuk;
                    let abs_waktu_pulang;
                    let seq = 1;

                    for(const abs_item of abs_getData)
                    {
                        let abs_hari    = abs_item.tanggal_absen;
                        if(moment(abs_hari, 'YYYY-MM-DD').format('dddd') == 'Sabtu') {
                            abs_waktu_masuk = "08:00:00";
                            abs_waktu_pulang= "13:30:00";
                        } else if(moment(abs_hari, 'YYYY-MM-DD').format('dddd') == 'Minggu') {
                            abs_waktu_masuk = "00:00:00";
                            abs_waktu_pulang= "00:00:00";
                        } else {
                            abs_waktu_masuk = "08:00:00";
                            abs_waktu_pulang= "16:00:00";
                        }
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

                        const abs_user_id   = abs_item.user_id;
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
                            jam_keluar,
                            `<button class='btn btn-sm btn-primary' type='button' id='btnEditAbsen${seq++}' value="${abs_user_id}|${abs_item.tanggal_absen}" title='Edit Absen' onclick="showModal('modal_edit_jam_kerja', 'edit', this.value)"><i class='fa fa-edit'></i></button>`
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

        if(data.length > 0) {
            let seq = 1;
            for(const emp of data)
            {
                let emp_id      = emp['emp_id'];
                let emp_name    = emp['emp_name'];
                let emp_division= emp['emp_division'];
                let emp_role    = emp['emp_role'];
                let emp_isActive= emp['emp_is_active'];
                let emp_button  = emp_isActive == '1' ? `<button class="btn btn-sm btn-primary" value="${emp_id}" onclick="doSimpan('aktivasi', 'active', this.value)" title="Nonaktifkan akun ini?">Aktif</button>` : `<button class="btn btn-sm btn-danger" value="${emp_id}" onclick="doSimpan('aktivasi','deactive', this.value)" title="Aktifkan Akun ini?">Tidak Aktif</button>`;

                $("#"+idTable).DataTable().row.add([
                    `<label class="no-margins font-weight-normal">${seq++}</label>`,
                    `<label class="no-margins font-weight-normal">${emp_name}</label>`,
                    `<label class="no-margins font-weight-normal">${emp_division}</label>`,
                    `<label class="no-margins font-weight-normal">${emp_role}</label>`,
                    emp_button
                ]).draw(false);
            }
            $("#"+idTable).find('.dataTables_empty').text('Ada Data');
        } else {
            $("#"+idTable).find('.dataTables_empty').text('Tidak Ada Data');
        }

        
    } else if(idTable == 'table_pgj_lmb') {
        $("#"+idTable).DataTable().clear().destroy();
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
            autoWidth   : false,
        });

        if(data.length > 0) {
            let seq = 1;
            for(const item of data)
            {
                let pengajuanID         = item['emp_act_id'];
                let pengajuanUsername   = item['emp_user_name'];
                let pengajuanTanggal    = moment(item['emp_act_date']).format('DD-MM-YYYY');
                let pengajuanButton     = `<button type="button" class="btn btn-sm btn-primary" onclick="showModal('modal_pgj_lmb_preview', 'prv', '${pengajuanID}')" title="Lihat Detail"><i class="fa fa-eye"></i></button>`
                switch(item['emp_trans_status']) {
                    case '1' :
                        var pengajuanStatus = `<span class="badge badge-sm bdage-pills badge-primary pt-1"><label class="no-margins">Diterima</label></span>`; 
                    break;
                    case '2' :
                        var pengajuanStatus = `<span class="badge badge-sm bdage-pills badge-danger pt-1"><label class="no-margins">Ditolak</label></span>`;
                    break;
                    case '3' :
                        var pengajuanStatus = `<span class="badge badge-sm bdage-pills badge-warning pt-1"><label class="no-margins text-dark">Menunggu Konfirmasi</label></span>`;
                    break;
                }

                $("#"+idTable).DataTable().row.add([
                    seq++,
                    pengajuanUsername,
                    pengajuanTanggal,
                    pengajuanStatus ?? '',
                    pengajuanButton,
                ]).draw(false);
            }
        }

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
    } else if(idSelect == 'pgj_select_month') {
        let html    = "<option selected disabled>Pilih Bulan</option>";
        
        for(const item of data)
        {
            html    += `<option value="${item['bulan_ke']}">${item['bulan_name']}</option>`;
        }
        $("#"+idSelect).html(html);
        
        if(selectedData != "") {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'select_pgj_month') {
        let html    = "<option selected disabled>Pilih Bulan</option>";
        
        for(const item of data)
        {
            html    += `<option value="${item['bulan_ke']}">${item['bulan_name']}</option>`;
        }

        $("#"+idSelect).html(html);

        if(selectedData != "") {
            $("#"+idSelect).val(selectedData);
        }
    }
}

function showSelectDetail(idSelect, value, seq = null)
{
    if(idSelect == 'pgj_select_month') {
        let pgj_URL     = base_url + "/pengajuan/listCuti";
        let pgj_type    = "GET";
        let pgj_data    = {
            "bulan"     : value,
        };
        let pgj_msg     = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();

        doTrans(pgj_URL, pgj_type, pgj_data, pgj_msg, true)
            .then((success)     => {
                Swal.close();
                let pgj_getData     = success.data;
                showTable('table_list_pengajuan', pgj_getData);
            })
            .catch((err)        => {
                Swal.close();
                showTable('table_list_pengajuan', []);
            })
    } else if(idSelect == 'select_pgj_month') {
        let pgjLembur_URL   = base_url + "/pengajuan/lembur/list_lembur";
        let pgjLembur_type  = "GET";
        let pgjLembur_data  = {
            "bulan"         : value,
        };
        let pgjLembur_msg   = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

        showTable('table_pjg_lmb', []);
        doTrans(pgjLembur_URL, pgjLembur_type, pgjLembur_data, pgjLembur_msg, true)
            .then((success)     => {
                Swal.close();
                let pgjLembur_getData    = success.data;
                if(pgjLembur_getData.length > 0) {
                    showTable('table_pgj_lmb', pgjLembur_getData);
                    $(".dataTables_empty").html('Data Berhasil Dimuat');
                } else {
                    showTable('table_pgj_lmb', []);
                    $(".dataTables_empty").html('Tidak Ada Data Yang Bisa Ditampilkan');
                }
            })
            .catch((err)        => {
                Swal.close();
                showTable('table_pgj_lmb', []);
                $(".dataTables_empty").html('Tidak Ada Data Yang Bisa Ditampilkan');
            })
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
            "user_id"       :  user == 'semua' ? '%' : user,
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
                    console.log(data);
                    showModal('modal_pgj_tolak', '', data);
                break; 
                case "konfirmasi_tolak" :
                    let pgjID   = $("#pgj_id").val();
                    let pgjNote = $("#pgj_cuti_note").val();
                    
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
                                "pgj_id"        : pgjID,
                                "pgj_title"     : "",
                                "pgj_date_start": "",
                                "pgj_date_end"  : "",
                                "pgj_type"      : "",
                                "pgj_status"    : "2",
                                "pgj_note"      : pgjNote,
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
                                    }).then((res)   =>{
                                        if(res.isConfirmed) {
                                            closeModal('modal_pgj');
                                            closeModal('modal_pgj_tolak');
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
            Swal.fire({
                icon    : 'question',
                title   : jenis == 'active' ? 'Nonaktifkan Akun ini?' : 'Aktifkan Akun ini?',
                showConfirmButton   : true,
                showCancelButton    : true,
                confirmButtonText   : jenis == 'active' ? 'Ya, Nonaktitkan' : 'Ya, Aktifkan',
                confirmButtonColor  : jenis == 'active' ? "#ED5565" : "#1AB394",
                cancelButtonText    : 'Batalkan',
            }).then((res)   => {
                if(res.isConfirmed) {
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
                                    showModal('modal_emp', '', '');
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
                }
            })
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
                    showModal('modal_pgj_lmb_preview_tolak', '', '');
                break;
                case "konfirm_tolak" :
                    let pgjLmbID    = $("#pgj_lmb_act_id").val();
                    let pgjLmbNote  = $("#pgj_lmb_note_tolak").val();
                    let pgjLmbStatus= "2";

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
                                "emp_act_id"    : pgjLmbID,
                                "emp_act_status": pgjLmbStatus,
                                "emp_act_note"  : pgjLmbNote,
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
                                            closeModal('modal_pgj_lmb_preview_tolak');
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
        case "edit_jam_kerja" :
            const editData  = {
                "user_id"   : $("#edit_jam_kerja_user_id").val(),
                "tanggal"   : moment($("#edit_jam_kerja_tanggal").val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "jam_masuk" : moment($("#edit_jam_kerja_tanggal").val(), 'DD/MM/YYYY').format('YYYY-MM-DD') + " " + $("#edit_jam_kerja_jam_masuk").val() + ":00",
                "jam_keluar": moment($("#edit_jam_kerja_tanggal").val(), 'DD/MM/YYYY').format('YYYY-MM-DD') + " " + $("#edit_jam_kerja_jam_keluar").val() + ":00"
            };

            const editType  = "POST";
            const editUrl   = base_url + "/divisi/human_resource/absensi/simpan_edit";
            const editMsg   = Swal.fire({ title : 'Data Sedang Diproses..' }); Swal.showLoading();

            doTrans(editUrl, editType, editData, editMsg, true)
                .then((results) => {
                    Swal.fire({
                        icon    : 'success',
                        title   : 'Berhasil',
                        text    : results.message,
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            closeModal('modal_edit_jam_kerja');

                            const tabelData     = {
                                "tanggal_akhir" : moment($("#abs_tgl_cari").val().split(' s/d ')[1], 'DD/MM/YYYY').format('YYYY-MM-DD'),
                                "tanggal_awal"  : moment($("#abs_tgl_cari").val().split(' s/d ')[0], 'DD/MM/YYYY').format('YYYY-MM-DD'),
                                "user_id"       : "%",
                                "jml_hari"      : 1
                            };
                            showTable('table_list_absensi', tabelData);
                        }
                    })
                })
                .catch((error)  => {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : error.responseJSON.message,
                    });
                })
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