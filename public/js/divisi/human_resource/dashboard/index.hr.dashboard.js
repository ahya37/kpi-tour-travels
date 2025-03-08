// INDEX JS DASHBOARD
moment().locale('id');
var today               = moment().format('YYYY-MM-DD');
var abs_data_global     = [];
var base_url            = window.location.origin;
var dataBulan           = [];
var dataRoles           = [];
var dataEmployees       = [];

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
    const emp_url   = "/master/employees/data_employees";
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

    const pgj_lmb_url   = base_url + "/pengajuan/lembur/list_lembur_v2";
    const pgj_lmb_type  = "GET";
    const pgj_lmb_data  = {
        "month" : moment(today, 'YYYY-MM-DD').format('MM'),
        "role"  : "semua"
    };
    

    const rolesURL      = base_url + '/master/data/trans/get/dataRoles';
    const rolesType     = "GET";
    const rolesData     = [];

    const sendData  = [
        doTrans(pgj_url, pgj_type, pgj_data, "", true),
        doTrans(emp_url, emp_type, "", "", true),
        doTrans(abs_url, abs_type, abs_data, "", true),
        doTrans(pgj_lmb_url, pgj_lmb_type, pgj_lmb_data, "", true),
        doTrans(rolesURL, rolesType, rolesData, "", true)
    ];

    Promise.allSettled(sendData)
        .then((success)     => {

            // EMP AREA
            const emp_getData   = success[1].status == 'fulfilled' ? success[1].value.data : [];
            $("#emp_total").html(emp_getData.length);
            if(emp_getData.length > 0 && dataEmployees.length < 1) {
                dataEmployees.push(emp_getData);
            }
            
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
                if(pgj_lmb_item['emp_act_status'] == '3') {
                    pgj_lmb_pending++;
                }
            }

            $("#pgj_lmb_total").html(pgj_lmb_getData.length);
            if(pgj_lmb_pending > 0) {
                $("#pgj_lmb_confirmation_text").html("<i class='fa fa-exclamation-triangle'></i> <label class='no-margins'>" + pgj_lmb_pending+" Butuh Konfirmasi</label>");
            }


            const rolesSendData     = success[4].status == 'fulfilled' ? success[4].value.data : [];
            dataRoles   = rolesSendData;
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
        // SHOW SELECT
        showSelect('abs_user_cari', dataEmployees);
        showTable('table_list_absensi', []);
        $("#table_list_absensi").find('.dataTables_empty').html(`Pilih Jarak Tanggal dan User untuk mengampilkan Data`);
        
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

        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
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
        // SHOW SELECT
        const bulanSekarang     = data == '' ? moment(today, 'YYYY-MM-DD').format('MM') : data['bulan'];
        showSelect('select_pgj_month', dataBulan, bulanSekarang);

        const roleSekarang      = data == '' ? 'semua' : data['divisi'];
        showSelect('select_pgj_role', dataRoles, roleSekarang);

        // SHOW TABLE
        const pgjLemburUrl  = base_url + "/pengajuan/lembur/list_lembur_v2";
        const pgjLemburType = "GET";
        const pgjLemburData = {
            "month"     : bulanSekarang,
            "role"      : roleSekarang,
        };
        const pgjLemburMsg  = Swal.fire({ title : 'Data Sedang Dimuat..' }); Swal.showLoading();

        doTrans(pgjLemburUrl, pgjLemburType, pgjLemburData, pgjLemburMsg, true)
            .then((results)     => {
                // close swal
                Swal.close();
                // show modal
                $("#"+idModal).modal({ backdrop : 'static', keyboad : false });
                
                // show table
                const pgjLemburGetData  = results.data;
                showTable('table_pgj_lmb', pgjLemburGetData);
                pgjLemburGetData.length > 0 ? $("#table_pgj_lmb").find('.dataTables_empty').html(`Berhasil Memuat Data Pengajuan Lembur`) : $("#table_pgj_lmb").find('.dataTables_empty').html(`Tidak Ada Data Pengajuan Lembur`);

            })
            .catch((error)      => {
                Swal.close();
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

                showTable('table_pgj_lmb', []);
                $("#table_pgj_lmb").find('.dataTables_empty').html(`Tidak Ada Pengajuan Lemburan`);
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
        let divisiSekarang  = $("#select_pgj_role").val();

        let dataCari    = {
            "bulan" : bulanSekarang,
            "divisi": divisiSekarang
        };

        showModal('modal_pgj_lmb', '', dataCari); 
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

        if(data.length > 0) {
            for(let i = 0; i < data.length; i++) {
                let empID       = data[i]['user_id'];
                let nama        = data[i]['nama'];
                let tanggal     = data[i]['tanggal_absen'];
                let jamMasuk    = data[i]['jam_masuk'];
                let jamKeluar   = data[i]['jam_keluar'];
                let buttonEdit  = `<button type="button" class="btn btn-sm btn-primary" value="${empID}|${tanggal}" title="Edit Absen" onclick="showModal('modal_edit_jam_kerja', 'edit', this.value)"><i class="fa fa-edit"></i></button>`;

                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${moment(tanggal, 'YYYY-MM-DD').format('YYYY-MM-DD')}</label>`,
                    `<label class="font-weight-normal no-margins">${nama}</label>`,
                    `<label class="font-weight-normal no-margins">${moment(jamMasuk, 'HH:mm:ss').format('HH:mm:ss')}</label>`,
                    `<label class="font-weight-normal no-margins">${moment(jamKeluar, 'HH:mm:ss').format('HH:mm:ss')}</label>`,
                    `<label class="font-weight-normal no-margins"></label>`,
                    `<label class="font-weight-normal no-margins"></label>`,
                    buttonEdit
                ]).draw(false);
            }
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
                let pengajuanNumber     = seq++;
                let pengajuanNama       = item['user_name'];
                let pengajuanTanggal    = moment(item['emp_act_date'], 'YYYY-MM-DD').format('DD-MMM-YYYY');
                let pengajuanStatus;
                
                switch(item['emp_act_status'])
                {
                    case '1' :
                        pengajuanStatus     = `<span class="badge badge-sm badge-primary"><label class="font-weight-bold no-margins">Approve</label></span>`; 
                    break;
                    case '2' :
                        pengajuanStatus     = `<span class="badge badge-sm badge-danger"><label class="font-weight-bold no-margins">Reject</label></span>`;
                    break;
                    case '3' :
                        pengajuanStatus     = `<span class="badge badge-sm badge-warning"><label class="font-weight-bold no-margins">Pending</label></span>`
                    break;
                    default     : ``;
                }

                let pengajuanActButton  = `<button type="button" class="btn btn-sm btn-success" title="Lihat Detail" value="${pengajuanID}" onclick="showModal('modal_pgj_lmb_preview', 'prv', this.value)"><i class="fa fa-eye"></i></button>`;
                
                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${pengajuanNumber}</label>`,
                    `<label class="font-weight-normal no-margins">${pengajuanNama}</label>`,
                    `<label class="font-weight-normal no-margins">${pengajuanTanggal}</label>`,
                    pengajuanStatus,
                    pengajuanActButton
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
        let html    = [
            "<option selected disabled>Pilih User</option>",
            "<option value='semua'>Semua</option>"
        ];
        
        if(data.length > 0) {
            $.each(data[0], (i, item)  => {
                if(item['active'] == '1' && item['employee_id'] != '1') {
                    html    += `<option value="${item['employee_id']}">${item['employee_name']}</option>`;
                }
            });
        }

        $("#"+idSelect).html(html);

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
    } else if(idSelect == 'select_pgj_role') {
        let html    = [
            `<option selected disabled>Pilih Divisi</option>`,
            `<option value="semua">semua</option>`
        ];


        if(data.length > 0) {
            $.each(data, (i, item)  => {
                if(item['role_name'] != 'admin')
                {
                    html    += `<option value="${item['role_id']}">${item['role_name']}</option>`
                }
            })
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }

        if(selectedData != '') {
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
        let month   = value;
        let role    = $("#select_pgj_role").val();

        let pgjLembur_URL   = base_url + "/pengajuan/lembur/list_lembur_v2";
        let pgjLembur_type  = "GET";
        let pgjLembur_data  = {
            "month"         : month,
            "role"          : role,
            
        };
        let pgjLembur_msg   = "";

        showTable('table_pgj_lmb', []);
        doTrans(pgjLembur_URL, pgjLembur_type, pgjLembur_data, pgjLembur_msg, true)
            .then((success)     => {
                Swal.close();
                let pgjLembur_getData    = success.data;
                if(pgjLembur_getData.length > 0) {
                    showTable('table_pgj_lmb', pgjLembur_getData);
                    $("#table_pgj_lmb").find('.dataTables_empty').html(`Data Pengajuan Lembur Berhasil Dimuat`);
                } else {
                    showTable('table_pgj_lmb', []);
                    $("#table_pgj_lmb").find('.dataTables_empty').html(`Tidak Ada Data Pengajuan Lembur Bulan ${moment(value, 'MM').format('MMMM')}`);
                }
            })
            .catch((err)        => {
                Swal.close();
                showTable('table_pgj_lmb', []);
                $(".dataTables_empty").html('Tidak Ada Data Yang Bisa Ditampilkan');
            })
    } else if(idSelect == 'select_pgj_role') {
        let month   = $("#select_pgj_month").val();
        let role    = value;

        let pgjLembur_URL   = base_url + "/pengajuan/lembur/list_lembur_v2";
        let pgjLembur_type  = "GET";
        let pgjLembur_data  = {
            "month"         : month,
            "role"          : role,
            
        };
        let pgjLembur_msg   = "";

        showTable('table_pgj_lmb', []);
        doTrans(pgjLembur_URL, pgjLembur_type, pgjLembur_data, pgjLembur_msg, true)
            .then((success)     => {
                Swal.close();
                let pgjLembur_getData    = success.data;
                if(pgjLembur_getData.length > 0) {
                    showTable('table_pgj_lmb', pgjLembur_getData);
                    $("#table_pgj_lmb").find('.dataTables_empty').html(`Data Pengajuan Lembur Berhasil Dimuat`);
                } else {
                    showTable('table_pgj_lmb', []);
                    $("#table_pgj_lmb").find('.dataTables_empty').html(`Tidak Ada Data Pengajuan Lembur Bulan ${moment(value, 'MM').format('MMMM')}`);
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
        showTable('table_list_absensi', []);
        const tanggal       = $("#abs_tgl_cari").val();
        const tanggal_awal  = tanggal.split(' s/d ')[0];
        const tanggal_akhir = tanggal.split(' s/d ')[1];
        const user          = $("#abs_user_cari").val();

        if(user == null) {
            $("#abs_user_cari").val('semua').trigger('change');
        }

        const absURL    = "/divisi/human_resource/absensi/list";
        const absType   = "GET";
        const absData   = {
            'tanggal_awal'  : moment(tanggal_awal, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            'tanggal_akhir' : moment(tanggal_akhir, 'DD/MM/YYYY').format('YYYY-MM-DD'),
            'user_id'       : user == 'semua' ? '%' : user,
            'jml_hari'      : moment(tanggal_akhir, 'DD/MM/YYYY').diff(moment(tanggal_awal, 'DD/MM/YYYY'), 'days') + 1,
        };

        doTrans(absURL, absType, absData, '', true)
            .then((success)     => {
                const absGetData    = success.data;
                showTable('table_list_absensi', absGetData);
                if(absGetData.length < 1) {
                    $("#table_list_absensi").find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`);
                }
            })
            .catch((error)      => {
                showTable('table_list_absensi', []);
                $("#table_list_absensi").find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`);
            })

        // const sendData  = {
        //     "tanggal_awal"  : moment(tanggal_awal, 'DD/MM/YYYY').format('YYYY-MM-DD'),
        //     "tanggal_akhir" : moment(tanggal_akhir, 'DD/MM/YYYY').format('YYYY-MM-DD'),
        //     "user_id"       : user == 'semua' ? '%' : user,
        //     "jml_hari"      : moment(tanggal_akhir, 'DD/MM/YYYY').diff(moment(tanggal_awal, 'DD/MM/YYYY'), 'days') + 1,
        // };

        // showTable('table_list_absensi', sendData);
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
                            showData('table_list_absensi');
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