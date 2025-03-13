var base_url    = window.location.origin;
var dataGroupDivision   = [];
var dataRole            = [];
$(document).ready(function(){
    // GET DATA MASTER
    const dataGroupDivisionURL   = base_url + "/master/employees/trans/get/dataGroupDivision";
    const dataGroupDivisionType  = "GET";

    const dataRoleURL       = base_url + "/master/data/trans/get/dataRoles";
    const dataRoleType      = "GET";

    const trans     = [
        do_transaction(dataGroupDivisionURL, dataGroupDivisionType, [], ''),
        do_transaction(dataRoleURL, dataRoleType, [], '')
    ];

    Promise.allSettled(trans)
        .then((success)     => {
            const dataGroupDivisionGetData  = success[0].status == 'fulfilled' ? success[0].value.data : [];
            dataGroupDivision.push(dataGroupDivisionGetData);

            const dataRoleGetData           = success[1].status == 'fulfilled' ? success[1].value.data : [];
            dataRole.push(dataRoleGetData);
        })
        .catch((error)      => {
            console.log(error);
        })

    show_table('tableEmployees','%');
});

function show_table(idTable, value)
{
    if(idTable == 'tableEmployees') {
        $("#tableEmployees").DataTable().clear().destroy();
        $("#tableEmployees").DataTable({
            language    : {
                zeroRecords     : 'Tidak ada data yang bisa ditampilkan, silahkan masukan beberapa data..',
                emptyTable      : 'Tidak ada data yang bisa ditampilkan, silahkan masukan beberapa data..',
                processing      : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
            },
            ordering    : false,
            processing  : true,
            autoWidth   : false,
            ajax    : {
                type    : "GET",
                dataType: "json",
                data    : {
                    cari    : value,
                },
                url      : '/master/employees/trans/get/dataTableEmployee/',
            },
            columnDefs  : [
                { "targets" : [0, 3], "className" : "text-center", "width" : "8%"},
                { "targets" : [1], "className" : "text-left", "width" : "20%"},
            ],
        });
    }
}

function show_modal(idModal, jenis, value) {
    if(idModal == 'modal_form_employees') {
        console.log(jenis);
        if(jenis == 'add') {
            $("#"+idModal).modal({ backdrop : 'static', keyboard: false });
        
            show_select('employee_group_division', dataGroupDivision[0], '');
            show_select('employee_role', dataRole[0], '');
    
            $("#"+idModal).on('shown.bs.modal', () => {
                $("#employee_name").focus();
            });
        } else if(jenis == 'edit') {
            // GET DATA
            const employeeDetailURL     = base_url + '/master/employees/trans/getDataEmployeesDetail';
            const employeeDetailType    = "GET";
            const employeeDetailData    = {
                "idEmployee"    : value,
            };
            const employeeDetailMsg     = Swal.fire({ title : "Sedang Mengambil Data" }); Swal.showLoading();
            
            do_transaction(employeeDetailURL, employeeDetailType, employeeDetailData, employeeDetailMsg)
                .then((success)     => {
                    Swal.close();

                    const employeeDetailGetData     = success.data;
                    // FILL FORM
                    const employeeID    = success.data['employee_id'];
                    const employeeName  = success.data['employee_name'];
                    const employeeRole  = success.data['roles_id'];
                    const employeeDivision  = success.data['group_division_id'] + " | " +  success.data['sub_division_id'];
                    const employeeEmail = success.data['employee_email'];

                    $("#employee_name").prop('readonly', true);

                    $("#employee_id").val(employeeID);
                    $("#employee_name").val(employeeName);
                    $("#employee_username").val(employeeEmail);

                    show_select('employee_group_division', dataGroupDivision[0], employeeDivision);
                    show_select('employee_role', dataRole[0], employeeRole);

                    $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                    
                })
                .catch((error)      => {
                    console.log(error);

                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : 'Data Tidak Ditemukan',
                    });
                })
        }

        $("#btn_simpan_form_employee").val(jenis);
    }
}

function close_modal(idModal) {
    if(idModal == 'modal_form_employees') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#employee_name").prop('readonly', false);
            $("#employee_name").val(null);
            $("#employee_username").val(null);

            $("#btn_simpan_form_employee").val(null);
        });
    }

    $("#"+idModal).on('hidden.bs.modal', function(){
        $("#btn_simpan_form_employee").removeAttr('value');
        $("#employee_name").removeAttr('readonly');
        $("#employee_name").val(null);
        $("#employee_username").val(null);
    })
}

function show_select(idSelect, data = [], selectedData = '')
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
    if(idSelect == 'employee_group_division') {
        let html    = `<option selected disabled>Pilih Divisi Grup</option>`;

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                let groupDivisionID     = item['group_division_id'];
                let groupDivisionName   = item['group_division_name'];
                let subDivisioNID       = item['sub_division_id'];
                let subDivisionName     = item['sub_division_name'];

                html    += `<option value="${groupDivisionID} | ${subDivisioNID}">${groupDivisionName} > ${subDivisionName}</option>`;
            })
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'employee_role') {
        let html    = `<option selected disabled>Pilih Role</option>`;

        if(data.length > 0 ) {
            $.each(data, (i, item)  => {
                let roleID  = item['role_id'];
                let roleName= item['role_name'];

                html    += `<option value="${roleID}">${roleName}</option>`;
            })
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    }
}

// GENERATE EMAIL U/ USER
function generateEmailUser(value)
{
    var lowerValue  = value.toLowerCase();
    var firstWord   = lowerValue.replace(/ .*/,'');
    
    var generate_email  = firstWord == '' ? '' : firstWord.replace(/[^a-zA-Z0-9]/g, '')+'@percik.com';
    $("#employee_username").val(generate_email);
}

function do_simpan(jenis)
{
    // GET FORM
    let empNama     = $("#employee_name");
    let empGDID     = $("#employee_group_division");
    let empUserName = $("#employee_username");
    let empRoles    = $("#employee_role");
    let empID       = $("#employee_id");

    let url         = base_url + "/master/employees/trans/post/dataEmployeeNew/"+jenis;
    let type        = "POST";
    let sendData    = {
        "employee_id"               : empID.val(),
        "employee_name"             : empNama.val(),
        "employee_group_division"   : empGDID.val(),
        "employee_username"         : empUserName.val(),
        "employee_role"             : empRoles.val(),
    };

    let customMessage   = Swal.fire({title : 'Data Sedang Diproses'});Swal.showLoading();

    do_transaction(url, type, sendData, customMessage)
        .then(function(success){
            Swal.fire({
                icon    : 'success',
                title   : 'Berhasil',
                text    : success.message,
            }).then((res)   => {
                if(res.isConfirmed) {
                    close_modal('modal_form_employees');
                    show_table('tableEmployees', '%');
                }
            })
            // Swal.fire({
            //     icon    : xhr.alert.icon,
            //     title   : xhr.alert.message.title,
            //     text    : xhr.alert.message.text,
            // }).then((results)   => {
            //     if(results.isConfirmed) {
            //         close_modal('modal_form_employees');
            //         show_table('tableEmployees','%');
            //     }
            // })
        })
        .catch(function(error){
            console.log(error);
            if(error.status == 422) {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : error.responseJSON.message,
                }).then((res)   => {
                    if(res.isConfirmed) {
                        const errorList     = error.responseJSON.data;
                        $.each(errorList, (i, item) => {
                            $("#"+i).addClass('is-invalid');

                            $("#"+i).on('click', () => {
                                $("#"+i).removeClass('is-invalid');
                            })

                            $("#"+i).on('select2:open', () => {
                                $("#"+i).removeClass('is-invalid');
                            })
                        })
                    }
                })
            } else {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : error.responseJSON.message,
                })
            }
        });
}

function do_transaction(url, type, data, customMessage)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            cache       : false,
            type        : type,
            beforeSend  : function() {
                customMessage
            },
            data        : {
                _token      : CSRF_TOKEN,
                sendData    : data,
            },
            url     :url,
            success : function(xhr) {
                resolve(xhr);
            },
            error   : function(xhr) {
                reject(xhr);
            }
        });
    });
}