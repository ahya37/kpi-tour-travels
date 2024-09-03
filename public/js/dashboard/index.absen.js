moment.locale('id');
var today   = moment().format('YYYY-MM-DD');
var latitude    = "";
var longitude   = "";
var base_url    = window.location.origin;
var prsTempData = [];
const CSRF_TOKEN = $('meta[name="csrf-token"]').attr("content");

$(document).ready(()    => {
    // CLOCK TICKING
    function updateTime()
    {
        const now = moment().format('HH:mm:ss');
        $("#abs_clock").html('<label>' + now + '</label>');
    }
    
    $("#abs_date").html("Absensi " + moment().format('dddd') + ", "+moment().format('DD MMMM YYYY'));
    setInterval(updateTime, 1000);

    getDataDashboard();
});

function getDataDashboard()
{
    const prs_url   = "/dashboard/getDataPresenceToday";
    const prs_type  = "GET";
    const prs_data  = "";
    
    const sendData  = [
        doTrans(prs_url, prs_type, prs_data, "", true)
    ];

    Promise.all(sendData)
        .then((success) => {
            const prs_getData   = success[0].data;
            prsTempData.push(prs_getData);

            const prs_in        = prs_getData['prs_in_time'] == null ? '' : "<i>- Masuk : "+moment(prs_getData['prs_in_time'], 'YYY-MM-DD HH:mm:ss').format('HH:mm:ss')+"</i>";
            const prs_out       = prs_getData['prs_out_time'] == null ? '' : "<i>- Keluar : "+moment(prs_getData['prs_out_time'], 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss')+"</i>";

            $("#prs_text_masuk").html(prs_in);
            $("#prs_text_keluar").html(prs_out);

            $("#back_button").removeClass('d-none');
        })
        .catch((err)    => {
            console.log(err);
            $("#back_button").addClass('d-none');
        })
}

// OPEN CAM
async function showCamera(id)
{
    const video     = document.getElementById(id);

    try {
        stream    = await navigator.mediaDevices.getUserMedia({
            video   : true,
        });

        video.srcObject = stream;
    } catch(error) {
        console.log(error);
    }
}

function hideCamera(id)
{
    if(stream) {
        stream.getTracks().forEach(track => track.stop());

        const videoElement  = document.getElementById(id);
        videoElement.srcObject = null;

        stream = null;
    }
}

function shutterCamera()
{
    const video     = document.getElementById('camera');
    const canvas    = document.getElementById('takePhoto');
    const context   = canvas.getContext('2d');

    canvas.width    = video.videoWidth;
    canvas.height   = video.videoHeight;

    context.drawImage(video, 0, 0, canvas.width, canvas.height);

    Swal.fire({
        title   : 'Data Sedang Diproses'
    });
    Swal.showLoading();
    setTimeout(()=> {
        $("#camera").addClass('d-none');
        $("#takePhoto").removeClass('d-none');

        $("#btn_simpanData").removeClass('d-none');
        $("#btn_cancelData").removeClass('d-none');
        $("#btn_takePhoto").addClass('d-none');

        Swal.close();
    }, 1000);
}

function showModal(idModal, jenis, data)
{
    if(idModal == 'modal_open_cam')
    {
        $("#btn_simpanData").val(jenis);
        $("#"+idModal).modal({backdrop: 'static', keyboard: false});
        if(jenis == 'masuk')
        {
            $("#modal_open_cam_title").html("Absensi Kehadiran");

            if(prsTempData.length > 0) {
                $("#body_camera").addClass('d-none');
                $("#body_data").removeClass('d-none');

                var canvas  = document.getElementById('getPhoto');
                var ctx  = document.getElementById('getPhoto');
                if(ctx.getContext)
                {
                    ctx     = ctx.getContext('2d');

                    var img     = new Image;

                    img.onload  = () => {
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                    }
                    if(prsTempData[0]['prs_in_file'] != null) {
                        img.src     = base_url + "/" + prsTempData[0]['prs_in_file'];
                    } else {
                        img.src     = "";
                    }
                }
            } else {
                showCamera('camera');
            }

        } else if(jenis == 'keluar') {
            $("#modal_open_cam_title").html("Absensi Kepulangan");
            if(prsTempData.length > 0) {
                if(prsTempData[0]['prs_out_file'] != null) {
                    $("#body_camera").addClass('d-none');
                    $("#body_data").removeClass('d-none');

                    var canvas  = document.getElementById('getPhoto');
                    var ctx  = document.getElementById('getPhoto');
                    if(ctx.getContext)
                    {
                        ctx     = ctx.getContext('2d');

                        var img     = new Image;

                        img.onload  = () => {
                            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        }
                        img.src     = base_url + "/" + prsTempData[0]['prs_in_file'];
                    }
                } else {
                    $("#body_camera").removeClass('d-none');
                    $("#body_data").addClass('d-none');
                    showCamera('camera');
                }
            } else {
                showCamera('camera');
            }
        }
    } else if(idModal == 'modal_list_pengajuan') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

        showTable('table_list_pengajuan', '');
    }
}

function closeModal(idModal)
{
    $("#"+idModal).modal('hide');
    if(idModal == 'modal_open_cam')
    {
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#modal_open_cam_title").html("");
            batalSimpanData();
            hideCamera('camera');

            $("#body_data").addClass('d-none');
            $("#body_camera").removeClass('d-none');
        })
    } else if(idModal == 'modal_list_pengajuan') {
        $("#"+idModal).on('hidden.bs.modal', () => {
            clearUrl();
        })
    }
}

function batalSimpanData()
{
    $("#takePhoto").addClass('d-none');
    $("#camera").removeClass('d-none');

    $("#btn_takePhoto").removeClass('d-none');
    $("#btn_simpanData").addClass('d-none');
    $("#btn_cancelData").addClass('d-none');
}

function getLocation(jenis)
{
    if(navigator.geolocation)
    {
        Swal.fire({
            title   : 'Lokasi sedang dimuat..',
        });
        Swal.showLoading();
        navigator.geolocation.getCurrentPosition(
            // TRUE
            (pos)   => {
                latitude    = pos.coords.latitude;
                longitude   = pos.coords.longitude;

                simpanData(jenis);
            },
            (fail)  => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : fail,
                });
                latitude    = "";
                longitude   = "";

                simpanData(jenis);
            },
            {
                enableHighAccuracy  : false,
                timeout             : 10000,
                maximumAge          : 10000,
            }
        )
    } else {
        Swal.fire({
            title   : 'Geolocation didnt support'
        })
    }
}

function simpanData(jenis) {
    if(jenis == 'masuk')
    {
        const start_time    = moment().format('YYYY-MM-DD HH:mm:ss');
        const user_id       = $("#prs_user_id").val();

        const prs_url       = "/dashboard/postPresence/"+jenis;
        const prs_type      = "POST";
        const prs_img       = document.getElementById('takePhoto').toDataURL('iamge/png');
        const prs_data      = {
            "prs_date"          : moment().format('YYYY-MM-DD'),
            "prs_start_time"    : start_time,
            "prs_user_id"       : user_id,
            "prs_status"        : jenis,
            "prs_image"         : prs_img,
            "prs_lat"           : latitude,
            "prs_long"          : longitude,
        };

        const prs_message   = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();

        doTrans(prs_url, prs_type, prs_data, prs_message, true)
            .then((success) => {
                Swal.fire({
                    icon    : success.alert.icon,
                    title   : success.alert.message.title,
                    text    : success.alert.message.text,
                }).then((results)   => {
                    if(results.isConfirmed) {
                        closeModal('modal_open_cam');
                        window.location.href = base_url + "/dashboard";
                    }
                });
            })
            .catch((err)    => {
                Swal.fire({
                    icon    : err.responseJSON.alert.icon,
                    title   : err.responseJSON.alert.message.title,
                    text    : err.responseJSON.alert.message.text,
                });
            });
    } else if(jenis == 'keluar') {
        const end_time  = moment().format('YYYY-MM-DD HH:mm:ss');
        const user_id   = $("#prs_user_id").val();
        const prs_img   = document.getElementById('takePhoto').toDataURL('iamge/png');
        
        const prs_url       = "/dashboard/postPresence/"+jenis;
        const prs_type      = "POST";
        const prs_data      = {
            "prs_date"          : moment().format('YYYY-MM-DD'),
            "prs_end_time"      : end_time,
            "prs_user_id"       : user_id,
            "prs_status"        : jenis,
            "prs_image"         : prs_img,
            "prs_lat"           : latitude,
            "prs_long"          : longitude,
        };
        const prs_message   = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();

        doTrans(prs_url, prs_type, prs_data, prs_message, true)
            .then((success) => {
                Swal.fire({
                    icon    : success.alert.icon,
                    title   : success.alert.message.title,
                    text    : success.alert.message.text,
                }).then((res)   => {
                    if(res.isConfirmed) {
                        closeModal('modalShowCamera');
                        prsTempData = [];
                        window.location.href = base_url + "/dashboard";
                    }
                });
            })
            .catch((err)    => {
                Swal.fire({
                    icon    : err.responseJSON.alert.icon,
                    title   : err.responseJSON.alert.message.title,
                    text    : err.responseJSON.alert.message.text,
                });
            });
    }
}

// PENGAJUAN CUTI
function showView(idView, jenis)
{
    if(idView == 'pengajuan_cuti')
    {
        // RESET FORM
        $("#pgj_title").val(null);
        $("#pgj_duration").val(null);
        
        $("#pgj_date").daterangepicker({
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
        $("#pgj_date").on('apply.daterangepicker', (ev, picker) => {
            const pgj_date          = $("#pgj_date").val().split(' s/d ');
            const pgj_date_start    = moment(pgj_date[0], 'DD/MM/YYYY').format('YYYY-MM-DD');
            const pgj_date_end      = moment(pgj_date[1], 'DD/MM/YYYY').format('YYYY-MM-DD');
            const pgj_date_diff     = moment(pgj_date_end, 'YYYY-MM-DD').diff(moment(pgj_date_start, 'YYYY-MM-DD'), 'days') + 1;

            $("#pgj_duration").val(pgj_date_diff+" Hari");
        })

        $("#pgj_date").data('daterangepicker').setStartDate(moment(today, 'YYYY-MM-DD').format('DD/MM/YYYY'));
        $("#pgj_date").data('daterangepicker').setEndDate(moment(today, 'YYYY-MM-DD').format('DD/MM/YYYY'));
        
        $("#pgj_title").on('keyup', () => {
            const pgj_title_val     = $("#pgj_title").val();
            $("#pgj_title").val(pgj_title_val.toUpperCase());
        })

        const pgj_type_data     = [
            { "id" : "Cuti", "name" : "Cuti" },
            { "id" : "Izin", "name" : "Izin" },
            { "id" : "Sakit", "name" : "Sakit" },
            { "id" : "Lainnya", "name" : "Lainnya" },
        ];
        showSelect('pgj_type', pgj_type_data);
        
        $("#"+idView).removeClass('d-none');
        $("#"+idView).addClass('animated fadeInRight');

        $("#absen_view").addClass('d-none');
        $("#btn_simpan_pgj").val(jenis);

    } else if(idView == 'absen_view')
    {
        $("#"+idView).removeClass('d-none');
        $("#"+idView).addClass('animated fadeInRight');

        $("#pengajuan_cuti").addClass('d-none');
    } else if(idView == 'modal_list_pengajuan') {
        $("#"+idView).modal({backdrop: 'static', keyboard: false});
    }
}

function showSelect(idSelect, data)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4'
    });

    if(idSelect == 'pgj_type')
    {
        var html    = "<option selected disabled>Pilih Jenis Pengajuan</option>";

        $.each(data, (i, item)  => {
            html    += "<option value='" + item.id + "'>" + item.name + "</option>";
        });

        $("#"+idSelect).html(html);
    }
}

function simpanDataPengajuan(jenis)
{
    const pgj_title     = $("#pgj_title");
    const pgj_type      = $("#pgj_type");
    const pgj_date      = $("#pgj_date");
    const pgj_status    = "3" // Pending

    if(pgj_title.val() == "") {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Uraian Pengajuan Harus Diisi',
            didClose    : () => {
                pgj_title.focus();
            }
        })
    } else if(pgj_type.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Jenis Pengajuan Harus Dipilih',
            didClose    : () => {
                pgj_type.select2('open');
                pgj_type.focus()
            }
        })
    } else {
        // SIMPAN DATA
        const pgj_sendData  = {
            "pgj_id"            : "",
            "pgj_title"         : pgj_title.val(),
            "pgj_date_start"    : moment(pgj_date.val().split(' s/d ')[0], 'DD/MM/YYYY').format('YYYY-MM-DD'),
            "pgj_date_end"      : moment(pgj_date.val().split(' s/d ')[1], 'DD/MM/YYYY').format('YYYY-MM-DD'),
            "pgj_type"          : pgj_type.val(),
            "pgj_status"        : pgj_status,
        };

        const pgj_url       = "/pengajuan/simpanCuti";
        const pgj_msg       = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
        doTransV2(pgj_url, "POST", pgj_sendData, pgj_msg, true)
            .then((success) => {
                Swal.fire({
                    icon    : success.alert.icon,
                    title   : success.alert.message.title,
                    text    : success.alert.message.text,
                }).then((results)   => {
                    if(results.isConfirmed) {
                        showView('absen_view', '');
                    }
                })
            })
            .catch((err)    => {
                Swal.fire({
                    icon    : err.responseJSON.alert.icon,
                    title   : err.responseJSON.alert.title,
                    text    : err.responseJSON.alert.message.text,
                });
            })
    }
}

function showTable(idTable, data)
{
    if(idTable == 'table_list_pengajuan')
    {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                emptyTable : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                zeroRecords: "Data Yang Dicari Tidak Ditemukan",
            },
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "20%" },
                { "targets" : [2], "className" : "text-left align-middle"},
                { "targets" : [3], "className" : "text-center align-middle", "width" : "14%" },
                { "targets" : [4], "className" : "text-center align-middle", "width" : "15%" },
            ],
        });

        // GET DATA
        const pgj_list_url  = "/pengajuan/listCuti";
        const pgj_list_type = "GET";
        const pgj_list_data = "";
        
        doTransV2(pgj_list_url, pgj_list_type, pgj_list_data, "", true)
            .then((success) => {
                const pgj_list_getData  = success.data;
                $.each(pgj_list_getData, (i, item)  => {

                    const pgj_list_date         = item.emp_act_start_date != item.emp_act_end_date ? moment(item.emp_act_start_date, 'YYYY-MM-DD').format('DD-MMM-YYYY') + " s/d " + moment(item.emp_act_end_date, 'YYYY-MM-DD').format('DD-MMM-YYYY') : moment(item.emp_act_start_date, "YYYY-MM-DD").format('DD-MMM-YYYY');
                    const pgj_list_keterangan   = item.emp_act_title;
                    const pgj_list_type         = item.emp_act_type;
                    var pgj_list_status;
                    switch(item.emp_act_status)
                    {
                        case '1' :
                            pgj_list_status     = "<span class='badge badge-primary'><label class='font-weight-bold no-margins'>Diterima</label></span>";
                            break;
                        case '2' :
                            pgj_list_status     = "<span class='badge badge-danger'><label class='font-weight-bold no-margins'>Ditolak</label></span>";
                            break;
                        case '3' :
                            pgj_list_status     = "<span class='badge badge-warning'><label class='font-weight-bold no-margins text-dark'>Menunggu Konfirmasi</label></span>";
                            break;
                    }
                    $("#"+idTable).DataTable().row.add([
                        i + 1,
                        pgj_list_date,
                        pgj_list_keterangan,
                        pgj_list_type,
                        pgj_list_status,
                    ]).draw(false);
                })
            })
            .catch((err)    => {
                console.log(err);
            })
    }
}

function doTrans(url, type, data, customMessage, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            cache   : false,
            type    : type,
            async   : isAsync,
            url     : url,
            data    : {
                _token  : CSRF_TOKEN,
                sendData: data,
            },
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

function doTransV2(url, type, data, customMessage, isAsync)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            cache   : false,
            type    : type,
            async   : isAsync,
            url     : url,
            data    : data,
            beforeSend  : () => {
                customMessage;
            },
            success     : (success) => {
                resolve(success);
            },
            error       : (err)     => {
                reject(err);
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