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

function show_select(idSelect, value)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
    if(idSelect == 'empGdIDAdd') {
        var url     = "/master/employees/trans/get/dataGroupDivision/%";
        var type    = "GET";
        var sendData= "%";

        var html    = "<option selected disabled>Pilih Grup Divisi</option>";

        getData(url, type, sendData).then(function(xhr){
            var data    = xhr.data;
            $.each(data, function(i,item){
                var gdID    = item['group_division_id'];
                var gdName  = item['group_division_name'];
                var sdID    = item['sub_division_id'];
                var sdName  = item['sub_division_name'];

                html    += "<option value='" +gdID+ " | " + sdID + "'>" + gdName + " > " + sdName + "</option>";
            });
            $("#empGdIDAdd").html(html);
            console.log(html);
        }).catch(function(xhr){
            $("#empGdIDAdd").html(html);
            console.log(xhr.responseJSON);
        })

    } else if(idSelect == 'empRoleAdd') {
        var url     = "/master/data/trans/get/dataRoles";
        var type    = "GET";
        var sendData= "%";

        var html    = "<option selected disabled>Pilih Role User</option>";
        getData(url, type, sendData).then(function(xhr){
            var data    = xhr.data;
            $.each(data, function(i,item){
                var roleID      = item['role_id'];
                var roleName    = item['role_name'];
                html    += "<option value='" + roleName + "'>" + roleName + "</option>";
            });
            $("#empRoleAdd").html(html);
        }).catch(function(xhr){
            $("#empRoleAdd").html(html);
        })
    }
}

function show_modal(idModal, jenis, value) {
    $("#"+idModal).modal({backdrop: 'static', keyboard: false});
    $("#"+idModal).modal('show');
    show_select('empGdIDAdd');
    show_select('empRoleAdd');

    if(jenis == 'add') {
        $("#empNameAdd").val(null);
        $("#empUsernameAdd").val(null);
        

        // FOCUS ON FORM
        $("#"+idModal).on('shown.bs.modal', function(){
            $("#empNameAdd").focus();
        })

        // GENERATE EMAIL AFTER USER TYPING
        $("#empNameAdd").on('keyup', function(){
            generateEmailUser(this.value);
        });

        $("#btnSimpan").val(jenis);
    } else if(jenis == 'edit') {
        // GET DATA
        var url         = getURL()+"/getDataEmployeesDetail";
        var sendData    = {
            "idEmployee"    : value,
        };
        var type        = "GET";

        $("#empNameAdd").attr('readonly', 'readonly');

        getData(url, type, sendData)
            .then(function(xhr){
                var getData     = xhr.data;
                $("#empIDAdd").val(value);
                $("#empNameAdd").val(getData['employee_name']);
                $("#empGdIDAdd").val(getData['group_division_id']+" | "+getData['sub_division_id']).trigger('change');
                $("#empRoleAdd").val(getData['roles_name']);
                $("#empUsernameAdd").val(getData['employee_email']);

                $("#"+idModal).on('shown.bs.modal', function(){
                    $("#empNameAdd").focus();
                })
            })
            .catch(function(xhr){
                console.log(xhr);
            });
        $("#btnSimpan").val(jenis);
    }
}

function close_modal(idModal) {
    $("#"+idModal).modal('hide');
    $("#"+idModal).on('hidden.bs.modal', function(){
        $("#btnSimpan").removeAttr('value');
        $("#empNameAdd").removeAttr('readonly');
    })
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
            async       : false,
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