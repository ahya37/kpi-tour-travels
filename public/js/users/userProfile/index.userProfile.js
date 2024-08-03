$(document).ready(function(){
    getDataDashboard();
})

var site_url                = window.location.pathname;
var checkPasswordIsTrue     = false;
var isConfirmPasswordSame   = false;

function getDataDashboard()
{
    const getData   = [
        doTransaction('/accounts/userProfiles/getUserData', 'GET', '', '', true),
    ];
    
    Promise.all(getData)
        .then((success) => {
            const dataUser  = success[0].data.user_data;
            const dataAct   = success[0].data.user_act_total;

            const dataAct_total     = dataAct.grand_total_keseluruhan == null ? 0 : dataAct.grand_total_keseluruhan;
            const dataAct_month_1   = dataAct.grand_total_bulan_ini == null ? 0 : dataAct.grand_total_bulan_ini;
            const dataAct_month_2   = dataAct.grand_total_bulan_lalu == null ? 0 : dataAct.grand_total_bulan_lalu;
            
            if(dataUser != '') {
                var dataUser_image    = dataUser.pict_dir;
            } else {
                var dataUser_image    = '';
            }

            $("#user_profiles_sub_division").empty();
            $("#user_profiles_sub_division").html(dataUser.emp_sub_div_name);

            $("#total_act").empty();
            $("#total_act").html(dataAct_total);

            $("#total_act_curr_month").empty();
            $("#total_act_curr_month").html(dataAct_month_1);

            $("#total_act_last_month").empty();
            $("#total_act_last_month").html(dataAct_month_2);

            $("#img_found_image").empty();

            $("#img_loading").addClass('d-none');
            if(dataUser_image != '') {
                $("#img_found").removeClass('d-none');
                $("#img_found_image").append("<img src='"+dataUser_image+"' style='width: 80px; height: 80px;' class='rounded-circle m-b-md' alt='profile'>");
                $("#img_not_found").addClass('d-none');
            } else {
                $("#img_not_found").removeClass('d-none');
                $("#img_found").addClass('d-none');
            }
        })
        .catch((err)    => {
            $("#user_profiles_sub_division").empty();
            $("#user_profiles_sub_division").html('{sub_division_name}');
    
            $("#total_act").empty();
            $("#total_act").html('test');
    
            $("#total_act_curr_month").empty();
            $("#total_act_curr_month").html(0);
    
            $("#total_act_last_month").empty();
            $("#total_act_last_month").html(0);
        })
}

function showModal(idModal) {
    if(idModal == 'modalChangePassword') {
        $("#"+idModal).modal('show');
        $("#"+idModal).on('shown.bs.modal', function() {
            $("#passwordLama").focus();

            $("#passwordLama").on('keyup', function() {
                $(this).removeClass('is-invalid');
                $(this).removeClass('is-valid');
                checkPasswordIsTrue     = false;

                // RESET FORM
                $("#passwordBaru").val(null);
                $("#passwordKonfirmasi").removeClass('is-valid');
                $("#passwordKonfirmasi").prop('readonly', true);
                $("#passwordKonfirmasi").val(null);

                checkPasswordIsTrue     = false;
                isConfirmPasswordSame   = false;
            });
        });
    } else if(idModal == 'modal_change_pict') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
    }
}

function closeModal(idModal) {
    if(idModal == 'modalChangePassword') {
        $("#"+idModal).modal('hide');
        
        $("#"+idModal).on('hidden.bs.modal', function() {
            $("#passwordBaru").val(null);
            
            $("#passwordKonfirmasi").val(null);

            $("#passwordLama").val(null);
            $("#passwordLama").removeClass('is-invalid');
            $("#passwordLama").removeClass('is-valid');

            $("#passwordKonfirmasi").prop('readonly', true);
            $("#passwordKonfirmasi").removeClass('is-invalid');
            $("#passwordKonfirmasi").removeClass('is-valid');
        })
    } else if(idModal == 'modal_change_pict') {
        $("#"+idModal).modal('hide');
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#preview_img").removeAttr('src',);
            $("#img_file").val(null);
        })
    }
}

function verifNewPassword(idForm) {
    if(idForm == 'passwordKonfirmasi') {
        $("#"+idForm).removeClass('is-invalid','is-valid');
        const newPassword           = $("#passwordBaru").val();
        const confirmNewPassword    = $("#"+idForm).val();

        if(confirmNewPassword != '') {
            if(confirmNewPassword != newPassword) {
                $("#"+idForm).addClass('is-invalid');
                isConfirmPasswordSame   = false;
            } else {
                $("#"+idForm).addClass('is-valid');
                isConfirmPasswordSame   = true;
            }
        }
    }
}

async function checkCurrentPassword(password) {
    const url       = site_url + "/CheckPasswordCurrentUser";
    const data      = {
        "currentPassword"   : password,
        "currentUserId"     : $("#passwordAccount").val(),
    };

    try {
        if(checkPasswordIsTrue == false) {
            const resultData    = await doTrans(url, "GET", data, '');
            $("#passwordLama").addClass('is-valid');
            checkPasswordIsTrue     = true;
            $("#passwordKonfirmasi").prop('readonly', false);
        }
    } catch(error) {
        checkPasswordIsTrue     = false;
        $("#passwordLama").addClass('is-invalid');
        
        $("#passwordKonfirmasi").prop('readonly', true);
        $("#passwordKonfirmasi").val(null);
    }
}

async function TransUbahPassword() {
    const url       = site_url + "/ChangePasswordUser";
    const data      = {
        "userId"            : $("#passwordAccount").val(),
        "userOldPassword"   : $("#passwordLama").val(),
        "userNewPassword"   : $("#passwordBaru").val(),
    };
    if(checkPasswordIsTrue == false) {
        Swal.fire({
            icon    : 'warning',
            title   : 'Terjadi Kesalahan',
            text    : 'Password Akun Tidak Sesuai, silahkan cek kembali',
        })
    } else if(isConfirmPasswordSame == false) {
        Swal.fire({
            icon    : 'warning',
            title   : 'Terjadi Kesalahan',
            text    : 'Konfirmasi Password tidak sesuai, silahkan cek kembali..',
        })
    } else if(data['userOldPassword'] == '') {
        Swal.fire({
            icon    : 'warning',
            title   : 'Terjadi Kesalahan',
            text    : 'Password Lama Harus diisi',
        });
    } else if((data['userNewPassword'] == '') || ($("#passwordKonfirmasi").val() == '')) {
        Swal.fire({
            icon    : 'warning',
            title   : 'Terjadi Kesalahan',
            text    : 'Password Baru dan Konfirmasi Password Harus diisi',
        })
    } else {
        const message   = Swal.fire({title:'Data Sedang Diproses'});Swal.showLoading();
        try {
            const resultData    = await doTrans(url, 'GET', data, message, true);
            Swal.fire({
                icon    : 'success', 
                title   : 'Berhasil',
                text    : 'Berhasil Mengubah Password. Anda akan dialihkan kembali ke Menu Login, silahkan Login kembali..',
            }).then((results)=>{
                if(results.isConfirmed) {
                    closeModal('modalChangePassword');
                    $("#logout-form").submit();
                }
            });
        } catch(error) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Sistem sedang gangguan, tidak ada perubahan pada data..',
            });
            console.log(error);
        }
    }
}

function uploadPreview(input)
{
    if(input.files && input.files[0] && input.value != '') {
        var reader  = new FileReader();

        reader.onload   = (e) => {
            $("#preview_img").attr('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        $("#preview_img").attr('src', '');
    }
}

async function TransUploadImage()
{
    var fd  = new FormData();

    let name    = "test";
    var pict    = $("#input_file").prop('files')[0];

    fd.append('name', name);
    fd.append('photo', pict);

    const url       = '/accounts/userProfiles/updatePicture';
    const type      = "POST";
    const data      = fd;
    const message   = Swal.fire({ title : 'Data Sedang Diunggah' }); Swal.showLoading();

    try {
        const getData   = await doTransUpload(url, type, data, message);
        Swal.fire({
            icon    : getData.alert.icon,
            title   : getData.alert.message.title,
            text    : getData.alert.message.text,
        }).then((res)   => {
            if(res.isConfirmed) {
                closeModal('modal_change_pict');
                $("#img_loading").removeClass('d-none');
                $("#img_found").addClass('d-none');
                $("#img_not_found").addClass('d-none');
                getDataDashboard();
            }
        })
    } catch(error) {
        Swal.fire({
            icon    : error.responseJSON.alert.icon,
            title   : error.responseJSON.alert.message.title,
            text    : error.responseJSON.alert.message.text
        });
    }
}

// PROMISE FUNCTION
function doTransaction(url, type, data, message, isAsync)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            async   : isAsync,
            url     : url,
            cache   : false,
            type    : type,
            data    : data, 
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            beforeSend  : () => {
                message;
            },
            success     : (success) => {
                resolve(success)
            },
            error       : (err)     => {
                reject(err);
            },
        })
    })
}

async function doTrans(url, type, data, customMessage, isAsync) {
    return $.ajax({
        async   : isAsync,
        url     : url,
        cache   : false,
        type    : type,
        processData     : true,
        headers : {
            'X-CSRF-TOKEN'  : CSRF_TOKEN,
        },
        beforeSend  : function() {
            customMessage;
        },
        data    : {
            "sendData"  : data,
        },
        dataType : 'json',
    });
}

async function doTransUpload(url, type, data, message)
{
    return new Promise((resolve, reject)   => {
        $.ajax({
            url             : url,
            type            : type,
            cache           : false,
            data            : data,
            processData     : false,
            contentType     : false,
            headers         : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data            : data,
            beforeSend      : () => {
                message;
            },
            success         : (success) => {
                resolve(success);
            },
            error           : (err)     => {
                reject(err);
            }
        })
    })
}