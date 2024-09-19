$(document).ready(function(){
    // EASTER EGG
    console.log('Hey, You Found Me!');
    getDataDashboard();
})

var site_url                = window.location.pathname;
var base_url                = window.location.origin;
var checkPasswordIsTrue     = false;
var isConfirmPasswordSame   = false;
var defaultPicturePath      = base_url + '/assets/img/9187604.png';
var tempLocalStorage        = [];
var dataLocalStorage        = JSON.parse(localStorage.getItem('items'))[0];

function getDataDashboard()
{
    const getData   = [
        doTransaction('/accounts/userProfiles/getUserData', 'GET', '', '', true),
        doTransaction('/accounts/userProfiles/getDataLastActUser', 'GET', '', '', true)
    ];
    
    Promise.all(getData)
        .then((success) => {
            const dataUser  = success[0].data.user_data;
            const dataAct   = success[0].data.user_act_total;

            const dataAct_total     = dataAct.grand_total_keseluruhan == null ? 0 : dataAct.grand_total_keseluruhan;
            const dataAct_month_1   = dataAct.grand_total_bulan_ini == null ? 0 : dataAct.grand_total_bulan_ini;
            const dataAct_month_2   = dataAct.grand_total_bulan_lalu == null ? 0 : dataAct.grand_total_bulan_lalu;
            
            if(dataUser != '') {
                var dataUser_image    = dataUser.pict_dir == null ? '' : dataUser.pict_dir;
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
                $("#img_found_image").append("<img src='" + base_url + "/" + dataUser_image + "' style='width: 80px; height: 80px;' class='rounded-circle m-b-md' alt='profile'>");
                $("#img_not_found").addClass('d-none');
                const senData   = {
                    "email"         : dataLocalStorage['email'],
                    "profile_pict"  : base_url + "/" + dataUser_image,
                };
                tempLocalStorage.push(sendData);
                localStorage.setItem('items', JSON.stringify(tempLocalStorage));
                $("#profile_image").prop('src', base_url + "/" + dataUser_image);
            } else {
                $("#img_found_image").append("<img src='"+defaultPicturePath+"' style='width: 80px; height: 80px;' class='rounded-circle m-b-md' alt='profile'>");
                $("#img_not_found").removeClass('d-none');
                $("#img_found").addClass('d-none');
                const sendData  = {
                    "email"             : dataLocalStorage['email'],
                    "profile_pict"      : defaultPicturePath,
                };
                tempLocalStorage.push(sendData);
                localStorage.setItem('items', JSON.stringify(tempLocalStorage));

                $("#profile_image").prop('src', defaultPicturePath);
            }

            $("#table-loading").addClass('d-none');
            $("#table-show").removeClass('d-none');
            showTable('tableLastActUser', success[1].data.table_act_user);
            
            $("#chart-loading").addClass('d-none');
            $("#chart-show").removeClass('d-none');
            showChart('chartActUser', success[1].data.chart_act_user);
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

            $("#table-loading").addClass('d-none');
            $("#table-show").removeClass('d-none');
            showTable('tableLastActUser', '');
            showChart('chartActUser', '');
        })
}

function showModal(idModal, data) {
    if(idModal == 'modalChangePassword') {
        if(data == 'admin') {
            Swal.fire({
                icon    : 'warning',
                title   : 'Peringatan',
                text    : 'Admin Tidak Dapat Mengubah Foto Ataupun Password',
            });
        } else {
            $("#"+idModal).modal({ backdrop : 'static', keyboard: false });
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
    } else if(idModal == 'modal_change_pict') {
        if(data == 'admin') {
            Swal.fire({
                icon    : 'warning',
                title   : 'Peringatan',
                text    : 'Admin Tidak Dapat Mengubah Foto Ataupun Password',
            });
        } else {
            $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
        }
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
    const url       = base_url + "/accounts/userProfiles/ChangePasswordUser";
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

function showTable(idTable, data)
{
    if(idTable == 'tableLastActUser') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "Tidak Ada Data Yang Bisa Ditampilkan",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan",
            },
            searching   : false,
            paging      : true,
            lengthMenu  : [
                [-1, 5, 10, 25, 100],
                ['Semua', 5, 10, 25, 100]
            ],
            pageLength  : 5,
            bInfo       : true,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "text-center align-middle", "width" : "30%" },
            ],
            responsive  : true,
        });

        if(data != '') {
            let num     = 1;
            for(const item of data)
            {
                const actUser_seq           = num++;
                const actUser_tanggal       = item.log_date_time;
                const actUser_description   = item.log_desc.length > 60 ? item.log_desc.substring(0, 60) + "..." : item.log_desc;

                $("#"+idTable).DataTable().row.add([
                    actUser_seq,
                    actUser_tanggal,
                    "<label style='font-weight: normal;' title='" + item.log_desc + "'>" + actUser_description + "</label>"
                ]).draw(false);
            }
        } else {
            // console.log(data);
        }
    }
}

function showChart(idChart, data)
{
    if(idChart == 'chartActUser') {
        const label_bulan   = [];
        const label_data    = [];
        const ctx   = document.getElementById('chartActUser').getContext('2d');
        
        for(const item of data) {
            label_bulan.push(moment(item.bulan_ke, 'MM').format('MMMM'));
            label_data.push(item.total_data);
        }

        // console.log(label_bulan, label_data);

        const myBarChart    = new Chart(ctx, {
            type: 'bar', // Jenis grafik
            data: {
                labels: label_bulan, // Label sumbu X
                datasets: [{
                    label: 'Aktivitas Bulanan', // Label dataset
                    data: label_data, // Data untuk grafik
                    backgroundColor: 'rgba(26, 179, 148, 0.2)', // Warna latar belakang batang
                    borderColor: 'rgba(26, 179, 148, 1)', // Warna batas batang
                    borderWidth: 1 // Lebar batas batang
                }]
            },
            options: {
                responsive: true, // Menjadikan grafik responsif
                maintainAspectRatio: false, // Mengizinkan perubahan rasio aspek
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Months'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Values'
                        },
                        ticks: {
                            stepSize: 5, // Langkah per 5 pada sumbu Y
                            callback: function(value, index, values) {
                                // Menyembunyikan nilai di bawah 0
                                if (value < 0) return '';
                                return value;
                            }
                        },
                        beginAtZero: true, // Mulai sumbu Y dari nol
                        min: 0 // Mengatur batas bawah sumbu Y untuk memulai dari 0
                    }
                }
            }
        })
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