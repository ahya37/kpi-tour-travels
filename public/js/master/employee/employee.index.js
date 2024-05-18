$(document).ready(function(){
    console.log('test');
    show_table('tableEmployees','%');
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN
    }
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

function show_select(idSelect, value)
{
    if(idSelect == 'empGdIDAdd') {
        $("#empGdIDAdd").select2({
            placeholder     : 'Pilih Grup Divisi',
        });

        var html    = "<option selected disabled>Pilih Grup Divisi</option>";

        $.ajax({
            cache   : false,
            type    : "GET",
            dataType: "json",
            data    : {
                _token  : CSRF_TOKEN
            },
            url     : "/master/employees/trans/get/dataGroupDivision/%",
            success : function(xhr) {
                if(xhr.status == 200) {
                    var data    = xhr.data;
                    $.each(data, function(i,item){
                        html    += "<option value='"+item['group_division_id']+" | "+ item['sub_division_id'] +"'>"+item['group_division_name']+" > " + item['sub_division_name'] + "</option>";
                    });
                }
                $("#empGdIDAdd").html(html);
            }
        })
    }
}

function show_modal(idModal, value) {
    $("#"+idModal).modal({backdrop: 'static', keyboard: false});
    $("#"+idModal).modal('show');
    if(idModal == 'modalTambahData') {
        // RESET FORM
        $("#empNameAdd").val(null);
        $("#empUsernameAdd").val(null);
        show_select('empGdIDAdd');

        // FOCUS ON FORM
        $("#modalTambahData").on('shown.bs.modal', function(){
            $("#empNameAdd").focus();
        })

        // GENERATE EMAIL AFTER USER TYPING
        $("#empNameAdd").on('keyup', function(){
            generateEmailUser(this.value);
        })
    }
}

function close_modal(idModal) {
    $("#"+idModal).modal('hide');
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
    if(jenis == 'add') {
        // GET FORM
        var empNama     = $("#empNameAdd");
        var empGDID     = $("#empGdIDAdd");
        var empUserName = $("#empUsernameAdd");

        var data        = {
            "empNama"       : empNama.val(),
            "empGDID"       : empGDID.val(),
            "empUserName"   : empUserName.val(),
        }

        $.ajax({
            cache   : false,
            type    : "POST",
            dataType: "json",
            data    : {
                _token      : CSRF_TOKEN,
                sendData    : data,
            },
            url     : "/master/employees/trans/post/dataEmployeeNew",
            beforeSend  : function() {
                Swal.fire({
                    title   : 'Data Sedang Diproses',
                })
                Swal.showLoading();
            },
            success     : function(xhr) {
                Swal.fire({
                    icon    : xhr.alert.icon,
                    title   : xhr.alert.message.title,
                    text    : xhr.alert.message.text,
                }).then((results)   => {
                    if(results.isConfirmed) {
                        close_modal('modalTambahData');
                        show_table('tableEmployees','%');
                    }
                });
            },
            error       : function(xhr) {
                Swal.fire({
                    icon    : xhr.responseJSON.alert.icon,
                    title   : xhr.responseJSON.alert.message.title,
                    text    : xhr.responseJSON.alert.message.text,
                })
            }
        })
    }
}