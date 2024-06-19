$(document).ready(function(){
    
})

var site_url     = window.location.pathname;
var checkPasswordIsTrue   = false;
var isConfirmPasswordSame = false;

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

async function doTrans(url, type, data, customMessage, isAsync) {
    return $.ajax({
        async   : isAsync,
        url     : url,
        cache   : false,
        type    : type,
        data    : {
            _token      : CSRF_TOKEN,
            sendData    : data,
        },
        dataType : 'json',
    });
}