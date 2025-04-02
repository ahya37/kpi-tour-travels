var today   = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
var roleData    = [];

$(document).ready(function(){

    const getData   = [
        doTransaction('master/data/trans/get/dataRoles', 'GET')
    ];

    Promise.allSettled(getData)
        .then((results)     => {
            let dataRoles     = results[0].status == 'fulfilled' ? results[0].value.data : [];
            if(dataRoles.length > 0 && roleData.length < 1) {
                roleData.push(dataRoles);
            }
        })
        .catch((error)      => {
            console.log(error);
        })
});

const collapseAction    = (id, type = '', data = '') => {
    if(id == 'collapse_form_role') {
        if(data == '') {
            $("#"+id).on('shown.bs.collapse', () => {
                $("#fr_name").focus();
            });
        } else {
            let url         = "master/get_role/" + data;
            let transType   = "GET";
            let message     = Swal.fire({ title : 'Data Sedang Dimuat..', allowOutsideClick: true }); Swal.showLoading();

            doTransaction(url, transType, [], message)
                .then((success)     => {
                    Swal.close();
                    
                    $("#"+id).collapse('show');

                    $("#fr_id").val(success.data[0].role_id);
                    $("#fr_name").val(success.data[0].role_name);
                })
                .catch((error)      => {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : error.responseJSON.message,
                    })
                })
        }

        $("#btn_act_fr").val(type);

        $("#"+id).on('hidden.bs.collapse', () => {
            $("#form_role").trigger('reset');
            $("#btn_act_fr").val('');
        })
    }
}

const showModal     = (idModal = '', jenis = '', data = '') => {
    // REMOVE ENTER ON FORM
    
    (event) => {
        if(event.which == '13') {
            event.preventDefault();
        }
    }

    if(idModal == 'modal_role') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

        showTable('table_role', roleData[0]);
        if(roleData[0].length < 1) {
            $("#table_role").find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`);
        }
    }
}

const closeModal    = (idModal = '') => {
    if(idModal == 'modal_role') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#collapse_form_role").collapse('hide');
        })
    }
}

const showTable     = (idTable = '', data = []) => {
    if(idTable == 'table_role') {
        destroyTable(idTable);

        $("#"+idTable).DataTable({
            language    : {
                'emptyTable'     : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat..`,
            },
            searching   : false,
            bInfo       : false,
            pageLength  : 5,
            lengthMenu  : [
                [-1, 5, 10, 25],
                ['All', 5, 10, 25]
            ],
            columnDefs  : [
                { "targets" : [0, 2], "className" : "text-center align-middle", "width" : "15%" },
                { "targets" : [1], "className" : "align-middle" },
            ],
            autoWidth   : false,
        });

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                let seq     = i + 1;
                let roleID  = item['role_id'];
                let roleName= item['role_name'];

                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${seq++}</label>`,
                    `<label class="font-wieght-normal no-margins">${roleName}</label>`,
                    `<button class="btn btn-sm btn-primary" type="button" value="${roleID}" title="Edit Data" onclick="collapseAction('collapse_form_role', 'edit', this.value)"><i class="fa fa-edit"></i></button>`
                ]).draw(false);
            })
        }
    }

    $("#"+idTable+"_wrapper").css('padding-bottom', '0px');
}

const destroyTable  = async (idTable = '') => {
    if(idTable != '') {
        return await $("#"+idTable).DataTable().clear().destroy();
    }
}

const doSaveTransaction     = (idForm = '', type = '', data = '') => {
    switch (idForm) {
        case 'form_role' :
            let formData  = new FormData(document.getElementById(idForm));

            let url         = "master/trans_role/" + type;
            let transType   = "POST";
            let data        = formData;
            let msg         = Swal.fire({ title : 'Data Sedang Diproses..', allowOutsideClick : true }); Swal.showLoading();

            doTransaction(url, transType, data, msg, false, false)
                .then((success)     => {
                    Swal.fire({
                        icon    : 'success',
                        title   : 'Berhasil',
                        text    : success.message,
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            showTable('table_role', []);
                            
                            $("#collapse_form_role").collapse('hide');
                            
                            setTimeout(()   => {
                                // GET DATA ROLE
                                let roleURL     = 'master/data/trans/get/dataRoles';
                                let roleType    = "GET";

                                doTransaction(roleURL, roleType)
                                    .then((isSuccess)     => {
                                        roleData    = [];
                                        roleData.push(isSuccess.data);
                                        showTable('table_role', roleData[0]);
                                    })
                                    .catch((error)      => {
                                        showTable('table_role', roleData[0]);
                                    })
                            }, 1000);
                        }
                    })
                })
                .catch((error)      => {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : error.responseJSON.message,
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            const errorMsg  = error.responseJSON.data;
                            $.each(errorMsg, (i, item)  => {
                                if(i == 0) {
                                    $("#"+i).focus();
                                }
                                $("#"+i).addClass('is-invalid');

                                $("#"+i).on('click',() => {
                                    $("#"+i).removeClass('is-invalid');
                                })
                            })
                        }
                    })
                })
        break;
    }
}

const doTransaction     = (url = '', type = '', data = [], message = '', processData = true, contentType = 'application/x-www-form-urlencoded') => {
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : true,
            url     : base_url + '/' + url,
            type    : type,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                return message;
            },
            processData     : processData,
            contentType     : contentType,
            success         : (isSuccess)   => {
                resolve(isSuccess);
            },
            error           : (isError)     => {
                reject(isError);
            }
        })
    })
}