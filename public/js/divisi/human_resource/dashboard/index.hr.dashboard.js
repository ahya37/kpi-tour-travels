// INDEX JS DASHBOARD

moment().locale('id');
var today   = moment().format('YYYY-MM-DD');
var abs_data_global     = [];
const base_url          = window.location.origin;

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

    const sendData  = [
        doTrans(pgj_url, pgj_type, pgj_data, "", true),
        doTrans(emp_url, emp_type, emp_data, "", true),
        doTrans(abs_url, abs_type, abs_data, "", true)
    ];

    Promise.all(sendData)
        .then((success)     => {

            // EMP AREA
            const emp_getData   = success[1].data;
            $("#emp_total").html(emp_getData.length);
            

            // PENGAJUAN AREA
            let pgj_total_warn_count  = 0;
            const pgj_getData   = success[0].data;
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
            const abs_getData   = success[2].data;
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
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

        showSelect('abs_user_cari', 'semua', '', '');
        showTable('table_list_absensi', '');

        $("#abs_tgl_cari").daterangepicker({
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

    }
}

function showSelect(idSelect, dataSelect, dataSelected, seq)
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

        // GET DATA
        const emp_url   = "/divisi/master/getDataEmployees";
        const emp_data  = "";
        const emp_type  = "GET";

        doTrans(emp_url, emp_type, emp_data, "", true)
            .then((success) => {
                const emp_getData   = success.data;
                
                if(success.data.length > 0) {
                    for(const item of emp_getData)
                    {
                        // EXCLUDE ADMIN
                        if(item.emp_id != 1) {
                            html    += "<option value='" + item.emp_id + "'>" + item.emp_name + "</option>";
                        }
                    }
                } else {
                    html    += "";
                }
                $("#"+idSelect).html(html);
            })
            .catch((err)    => {
                console.log(err);
                $("#"+idSelect).html(html);
            });
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