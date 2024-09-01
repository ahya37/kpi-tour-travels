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
            showCamera('camera');
        } else if(jenis == 'keluar') {
            $("#modal_open_cam_title").html("Absensi Kepulangan");
            showCamera('camera');
        }
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
                // Swal.fire({
                //     icon    : 'success',
                //     title   : 'Berhasil',
                //     text    : 'Latitude : '+pos.coords.latitude+'; Longitude : '+pos.coords.longitude,
                // });
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