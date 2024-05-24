$(document).ready(function(){
    show_table('tableGroupDivision','%');

    $("#name_group_division_edit").on('keyup', function(){
        $(this).removeClass('is-invalid');
    })
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN
    }
});

function show_table(id_table, value)
{
    if(id_table == 'tableGroupDivision') {
        $("#tableGroupDivision").DataTable().clear().destroy();
        $("#tableGroupDivision").DataTable({
            language    : {
                zeroRecords     : 'Tidak ada data yang bisa ditampilkan, silahkan masukan beberapa data..',
                emptyTable      : 'Tidak ada data yang bisa ditampilkan, silahkan masukan beberapa data..',
                processing      : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat",
            },
            ordering    : false,
            processing  : true,
            serverSide  : false,
            ajax        : {
                type    : "GET",
                dataType: "json",
                url     : '/master/groupDivisions/trans/get/dataGroupDivisions/'+value,
            },
            columnDefs  : [
                { "targets":[0], "className":"text-center", "width": "5%"},
                { "targets":[3], "className":"text-center", "width": "10%"},
            ],
        });
    }
}

function show_modal(id_modal, jenis, value)
{
    $("#"+id_modal).modal({backdrop: 'static', keyboard: false});
    $("#"+id_modal).modal('show');
    if(jenis == 'add') {
        $("#"+id_modal).on('shown.bs.modal', function(){
            $("#groupDivisionName").focus();
        });
        $("#btnSimpan").val('add');
    } else if(jenis == 'edit') {
        var url     = "/master/groupDivisions/trans/get/modalDataGroupDivisions/"+value;
        var type    = "GET";
        var messag  =   Swal.fire({
                            title   : 'Data Sedang Dimuat',
                        });
                        Swal.showLoading();
        showData(url, type, '')
            .then((xhr) => {
                var data    = xhr.data;
                $("#groupDivisionID").val(data['gdID']);
                $("#groupDivisionName").val(data['gdName']);
                Swal.close();
            })
            .catch((xhr)    => {
                console.log(xhr.responseJSON);
            });
        $("#btnSimpan").val('edit');
    }
}

function close_modal(id_modal) {
    $("#"+id_modal).modal('hide');
    var jenis = $("#btnSimpan").val();
    if((jenis == 'add') || (jenis == 'edit')) {
        $("#"+id_modal).on('hidden.bs.modal', function(){
            $("#groupDivisionID").val(null);
            $("#groupDivisionName").val(null);
        });
    }
}

function do_save(jenis)
{
    var sendData    = {
        "groupDivisionID"   : $("#groupDivisionID").val(),
        "groupDivisionName" : $("#groupDivisionName").val(),
    };
    var type       = "POST";
    var url         = "/master/groupDivisions/trans/store/dataGroupDivisions/"+jenis;
    var customMessage   =   Swal.fire({
                                title   : 'Data Sedang Diproses',
                            });
                            Swal.showLoading();
    showData(url, type, sendData, customMessage)
        .then((xhr) => {
            Swal.fire({
                icon    : xhr.alert.icon,
                title   : xhr.alert.message.title,
                text    : xhr.alert.message.text,
            }).then(function(results){
                if(results.isConfirmed) {
                    close_modal('modalForm');
                    show_table('tableGroupDivision','%');
                }
            })
        })
        .catch((xhr) => {
            console.log(xhr.responseJSON);
        });
}

function showData(url, type, sendData, customMessage)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            async   : true,
            cache   : false,
            type    : type,
            dataType: "json",
            data    : {
                _token  : CSRF_TOKEN,
                sendData: sendData,
            },
            url     : url,
            beforeSend  : function(){
                customMessage
            },
            success : function(xhr) {
                resolve(xhr);
            },
            error   : function(xhr) {
                Swal.fire({
                    icon     : 'error',
                    text    : xhr.statusText,
                });
                reject(xhr);
            }
        })
    });
}