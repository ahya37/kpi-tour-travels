$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN
    }
});
$(document).ready(function(){
    show_table('tableSubDivision','%');
})

var getURL = window.location.pathname;

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
                url     : getURL +"/trans/get/tableDataGroupDivision",
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
    $("#btnSimpan").val(jenis);
    if (jenis == 'add') {
        show_select('groupDivisionID','%', true);
    } else if (jenis == 'edit') {
        // SHOW INTO FORM

        var url         = getURL + "/getDataSubDivision";
        var sendData    = { "subDivisionID" : value };
        var type        = "GET";
        var isAsync     = true;
        var message     = Swal.fire({title:'Data Sedang Dimuat'}); Swal.showLoading();

        transData(url, type, sendData, message, isAsync)
            .then(function(xhr){
                var getData     = xhr.data[0];
                // SHOW DATA ON SELECT
                show_select('groupDivisionID', getData['group_division_id'], false);
                // SHOW DATA ON TEXT FORM
                $("#subDivisionID").val(getData['sub_division_id']);
                $("#subDivisionName").val(getData['sub_division_name']);

                Swal.close();
            })
            .catch(function(xhr){
                console.log(xhr);
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : xhr.status+" "+xhr.statusMessage
                });
            })
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

function show_select(id_form, value, isAsync)
{
    $("#"+id_form).select2({
        theme   : 'bootstrap4',
    });
    if(id_form == 'groupDivisionID') {
        console.log({value, isAsync});
        var html    = "<option selected disabled>Pilih Grup Divisi</option>";
        var url     = getURL +"/trans/get/selectDataGroupDivision";
        var type    = "GET";
        var sendData= "%";
        if(isAsync == true) {
            var message     = Swal.fire({title:'Data Sedang Dimuat'});Swal.showLoading();
        } else {
            var message     = "";
        }

        transData(url, type, sendData, message, isAsync).then(function(xhr){
            $.each(xhr.data, function(i,item){
                html    += "<option value='" + item['groupDivisionID'] + "'>" + item['groupDivisionName'] + "</option>";
            });
            $("#"+id_form).html(html);

            if(value != '%') {
                $("#"+id_form).val(value).trigger('change');
            }
            isAsync == true ? Swal.close() : '';
            isAsync == true ? $("#"+id_form).select2('open') : '';
        }).catch(function(xhr){
            $("#"+id_form).html(html);
        });

        if(id_form == 'groupDivisionID') {
            $("#groupDivisionID").on('select2:select', function(){
                $("#subDivisionName").focus();
            });
        }

        $("#"+id_form).html(html);
    }
}

function do_save(jenis)
{
    console.log({jenis});
    var url     = getURL  + "/simpanDataSubDivision/"+jenis;
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

function transData(url, type, sendData, customMessage, isAsync) {
    return new Promise(function(resolve, reject){
        $.ajax({
            async   : isAsync,
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