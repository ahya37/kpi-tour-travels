$(document).ready(function(){
    console.log('test');
    show_table('tableEmployees','%');
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN
    }
});

function getURL()
{
    return $(location).attr('pathname');
}

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
    if(idModal == 'modalForm') {

        var sendData    = {
            "idEmployee"    : value,
        };
        // GET DATA
        var getAllData     = [
            getData('/master/employees/trans/get/dataGroupDivision/', 'GET', '', '', true),
            getData('/master/data/trans/get/dataRoles', 'GET', '%', '', true),
            jenis == 'add' ? '' : getData('/master/employees/getDataEmployeesDetail', 'GET', sendData, '', true),
        ];

        Swal.fire({
            title   : 'Data Sedang Dimuat',
        });
        Swal.showLoading();

        Promise.all(getAllData)
            .then((success) => {
                // CLOSE ALERT
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop : 'static', keyboard : false });

                $("#"+idModal).on('shown.bs.modal', () => {
                    $("#empNameAdd").focus();
                });

                // SHOW DATA
                show_select('empGdIDAdd', success[0], '');
                show_select('empRoleAdd', success[1], '');

                // PLACE DATA ON COLUMN
                if(jenis == 'edit') {
                    $("#empIDAdd").val(success[2].data.employee_id);
                    $("#empNameAdd").prop('readonly', true);
                    $("#empNameAdd").val(success[2].data.employee_name);
                    $("#empGdIDAdd").val(success[2].data.group_division_id+" | "+success[2].data.sub_division_id).trigger('change');
                    $("#empRoleAdd").val(success[2].data.roles_name).trigger('change');
                    $("#empUsernameAdd").val(success[2].data.employee_email);
                } else if(jenis == 'add') {
                    $("#empNameAdd").prop('readonly', false);
                }
                Swal.close();
            })
            .catch((err)    => {
                console.log(err);
                show_select('empGdIDAdd', '', '');
                show_select('empRoleAdd', '', '');
            })

        $("#btnSimpan").val(jenis);
    }
}

function close_modal(idModal) {
    $("#"+idModal).modal('hide');
    $("#"+idModal).on('hidden.bs.modal', function(){
        $("#btnSimpan").removeAttr('value');
        $("#empNameAdd").removeAttr('readonly');
        $("#empNameAdd").val(null);
        $("#empUsernameAdd").val(null);

    })
}

function show_select(idSelect, valueCari, valueSelect)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
    if(idSelect == 'empGdIDAdd') {
        var html    = "<option selected disabled>Pilih Grup Divisi</option>";
        if(valueCari != '') {
            $.each(valueCari.data, (i, item) => {
                var gdID    = item['group_division_id'];
                var gdName  = item['group_division_name'];
                var sdID    = item['sub_division_id'];
                var sdName  = item['sub_division_name'];

                html    += "<option value='" +gdID+ " | " + sdID + "'>" + gdName + " > " + sdName + "</option>";
            })
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'empRoleAdd') {
        var html    = "<option selected disabled>Pilih Role</option>";
        if(valueCari != '') {
            $.each(valueCari.data, (i, item)    => {
                html    += "<option value='" + item.role_name + "'>" + item.role_name + "</option>";
            });
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    }
}

// GENERATE EMAIL U/ USER
function generateEmailUser(value)
{
    var lowerValue  = value.toLowerCase();
    var firstWord   = lowerValue.replace(/ .*/,'');
    
    var generate_email  = firstWord == '' ? '' : firstWord.replace(/[^a-zA-Z0-9]/g, '')+'@percik.com';
    $("#empUsernameAdd").val(generate_email);
}

function do_simpan(jenis)
{
    // GET FORM
    var empNama     = $("#empNameAdd");
    var empGDID     = $("#empGdIDAdd");
    var empUserName = $("#empUsernameAdd");
    var empRoles    = $("#empRoleAdd");
    var empID       = $("#empIDAdd");

    var url         = getURL() + "/trans/post/dataEmployeeNew";
    var type        = "POST";
    var sendData    = {
        "empID"         : empID.val(),
        "empNama"       : empNama.val(),
        "empGDID"       : empGDID.val(),
        "empUserName"   : empUserName.val(),
        "empRole"       : empRoles.val(),
        "transJenis"    : jenis,
    };
    var customMessage   = Swal.fire({title : 'Data Sedang Diproses'});Swal.showLoading();

    getData(url, type, sendData, customMessage)
        .then(function(xhr){
            Swal.fire({
                icon    : xhr.alert.icon,
                title   : xhr.alert.message.title,
                text    : xhr.alert.message.text,
            }).then((results)   => {
                if(results.isConfirmed) {
                    close_modal('modalForm');
                    show_table('tableEmployees','%');
                }
            })
        })
        .catch(function(xhr){
            Swal.fire({
                icon    : xhr.responseJSON.alert.icon,
                title   : xhr.responseJSON.alert.message.title,
                text    : xhr.responseJSON.alert.message.text,
            });
        });
}

function getData(url, type, data, customMessage)
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