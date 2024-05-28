$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN
    }
});
$(document).ready(function(){
    show_table('tableSubDivision','%');
})

function getURL()
{
    return $(location).attr('pathname');
}

function show_table(id_table, value)
{
    if(id_table == 'tableSubDivision') {
        $("#tableSubDivision").DataTable().clear().destroy();
        $("#tableSubDivision").DataTable({
           language     : {
            zeroRecords     : 'Tidak ada data yang bisa ditampilkan, silahkan masukan beberapa data..',
            emptyTable      : 'Tidak ada data yang bisa ditampilkan, silahkan masukan beberapa data..',
            processing      : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat",
           },
           ordering     : false,
           processing   : true,
           ajax         : {
                dataType    : "json",
                data        : {
                    _token  : CSRF_TOKEN,
                    cari    : value,
                },
                type    : "GET",
                url     : getURL()+"/trans/get/tableDataGroupDivision",
           },
           columnDefs  : [
                {
                    targets     : [0, 4],
                    className   : "text-center",
                },
            ],
        });
    }
}

function show_modal(id_modal, jenis, value)
{
    $("#"+id_modal).modal({backdrop: 'static', keyboard: false});
    $("#"+id_modal).modal('show');
    show_select('groupDivisionID','%');
    $("#btnSimpan").val(jenis);

    if(jenis == 'edit') {
        var subDivisionID   = value;
        var url             = getURL()+"/trans/get/modalDataSubDivision";
        var sendData        = {
            "subDivisionID" : subDivisionID,
        };
        var type            = "GET";

        transData(url, type, sendData, '').then(function(xhr){
            var groupDivisionID     = xhr.data[0]['group_division_id'];
            var subDivisionID       = xhr.data[0]['sub_division_id'];
            var subDivisionName     = xhr.data[0]['sub_division_name'];

            $("#subDivisionID").val(subDivisionID);
            $("#subDivisionName").val(subDivisionName);
            $("#groupDivisionID").val(groupDivisionID).trigger('change');

        }).catch(function(xhr){
            console.log(xhr);
        });
    }
}

function close_modal(id_modal)
{
    $("#"+id_modal).modal('hide');
    $("#"+id_modal).on('hidden.bs.modal', function(){
        $("#subDivisionName").val(null);
        $("#btnSimpan").val(null);
        $("#groupDivisionID").val('').trigger('change');
    });
}

function show_select(id_form, value)
{
    $("#"+id_form).select2({
        theme   : 'bootstrap4',
    });
    if(id_form == 'groupDivisionID') {
        var html    = "<option selected disabled>Pilih Grup Divisi</option>";
        var url     = getURL()+"/trans/get/selectDataGroupDivision";
        var type    = "GET";
        var sendData= "%";

        transData(url, type, sendData, '').then(function(xhr){
            $.each(xhr.data, function(i,item){
                html    += "<option value='" + item['groupDivisionID'] + "'>" + item['groupDivisionName'] + "</option>";
            });
            $("#"+id_form).html(html);
        }).catch(function(xhr){
            console.log(xhr);
            $("#"+id_form).html(html);
        });

        if(value != '') {
            $("#"+id_form).select2('val',value);
        }

        if(id_form == 'groupDivisionID') {
            $("#groupDivisionID").on('select2:select', function(){
                $("#subDivisionName").focus();
            });
        }
    }
}

function do_save(jenis)
{
    console.log({jenis});
    var url     = getURL() + "/simpanDataSubDivision/"+jenis;
    var type    = "POST";
    var customMessage   =   Swal.fire({
                                title   : 'Data Sedang Diproses'
                            });
                            Swal.showLoading();
    if(jenis == 'add') {
        var sendData    = {
            "groupDivisionID"   : $("#groupDivisionID").val(),
            "subDivisionName"   : $("#subDivisionName").val(),
        };
    } else if(jenis == 'edit') {
        var sendData    = {
            "groupDivisionID"   : $("#groupDivisionID").val(),
            "subDivisionID"     : $("#subDivisionID").val(),
            "subDivisionName"   : $("#subDivisionName").val(),
        };
    }

    transData(url, type, sendData, customMessage)
        .then(function(xhr){
            Swal.fire({
                icon    : xhr.alert.icon,
                title   : xhr.alert.message.title,
                text    : xhr.alert.message.text,
            }).then(function(results){
                if(results.isConfirmed) {
                    close_modal('modalForm');
                    show_table('tableSubDivision','%');
                }
            })
        })
        .catch(function(xhr){
            console.log(xhr.responeJSON);
        });
}

function transData(url, type, sendData, customMessage) {
    return new Promise(function(resolve, reject){
        $.ajax({
            async   : false,
            cache   : false,
            dataType: "json",
            type    : type,
            url     : url,
            beforeSend  : function() {
                customMessage;
            },
            data    : {
                _token  : CSRF_TOKEN,
                sendData: sendData,
            },
            success     : function(xhr) {
                resolve(xhr);
            },
            error       : function(xhr) {
                Swal.fire({
                    icon    : 'error',
                    title   : xhr.status,
                    text    : xhr.statusMessage,
                })
                reject(xhr);
            }
        })
    });
}