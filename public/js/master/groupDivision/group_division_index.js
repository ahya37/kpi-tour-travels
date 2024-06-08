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

var site_url    = window.location.pathname;

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
                data:{
                    q: value
                },
                url     : site_url+'/trans/get/dataGroupDivisions/',
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
    // console.log({id_modal, jenis, value});
    $("#"+id_modal).modal({backdrop: 'static', keyboard: false});
    $("#"+id_modal).modal('show');
    $("#btnSimpan").val(jenis);
    if(jenis == 'add') {
        $("#"+id_modal).on('shown.bs.modal', function(){
            $("#groupDivisionName").focus();
        });

        show_select('groupDivisionRole','%','', true);
    } else if(jenis == 'edit') {
        var url     = site_url+"/trans/get/modalDataGroupDivisions/"+value;
        var type    = "GET";
        var message  =   Swal.fire({
                            title   : 'Data Sedang Dimuat',
                        });
                        Swal.showLoading();
        
        showData(url, type, '', message, true)
            .then((xhr) => {
                var data    = xhr.data;
                console.log(data);
                $("#groupDivisionID").val(data['gdID']);
                $("#groupDivisionName").val(data['gdName']);
                
                show_select('groupDivisionRole','%', data['roleID'], false);

                Swal.close();
            })
            .catch((xhr)    => {
                console.log(xhr.responseJSON);
            });
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

function show_select(id_select, value_cari, value_select, isAsync)
{
    $("#"+id_select).select2({
        theme   : 'bootstrap4',
    });
    if(id_select == 'groupDivisionRole') {
        var html    = "<option selected disabled>Pilih Role Sistem</option>";
        var url     = "/master/data/trans/get/dataRoles";
        var sendData= {
            "sendData"  : value_cari,
        };
        
        showData(url, "GET", sendData, '', isAsync)
            .then(function(xhr){
                $.each(xhr.data, function(i,item){
                    html    += "<option value='" + item['role_id'] + "'>" + item['role_name'] + "</option>";
                });
                $("#"+id_select).html(html);
                if(value_select != '') {
                    $("#"+id_select).val(value_select).trigger('change')
                }
            })
            .catch(function(xhr){
                console.log(xhr);
            })

        $("#"+id_select).html(html);
    }
}

function do_save(jenis)
{
    var sendData    = {
        "groupDivisionID"   : $("#groupDivisionID").val(),
        "groupDivisionName" : $("#groupDivisionName").val(),
        "groupDivisionRole" : $("#groupDivisionRole").val(),
    };
    var type       = "POST";
    var url         = site_url+"/trans/store/dataGroupDivisions/"+jenis;
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

function showData(url, type, sendData, customMessage, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            async   : isAsync,
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