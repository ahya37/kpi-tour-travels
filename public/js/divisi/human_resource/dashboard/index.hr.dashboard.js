// INDEX JS DASHBOARD

moment().locale('id');
var today   = moment().format('YYYY-MM-DD');

$(document).ready(() => {
    // GET DATA PENGAJUAN

    const pgj_url   = "/pengajuan/listCuti";
    const pgj_type  = "GET";
    const pgj_data  = "";

    const emp_url   = "/master/employees/trans/get/dataTableEmployee";
    const emp_data  = {
        "cari"  : "%",
    };
    const emp_type  = "GET";

    const sendData  = [
        doTrans(pgj_url, pgj_type, pgj_data, "", true),
        doTrans(emp_url, emp_type, emp_data, "", true),
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
    }
}

function showSelect(idSelect, dataSelect, dataSelected, seq)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
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