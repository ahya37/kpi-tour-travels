$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN
    }
});
$(document).ready(function(){
    show_table('tableSubDivision','%');
})

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
                    cari    : '%',
                },
                type    : "GET",
                url     : "/master/subDivisions/trans/get/tableDataGroupDivision",
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

function show_modal(id_modal, value)
{
    $("#"+id_modal).modal({backdrop: 'static', keyboard: false});
    if(id_modal == 'modalSubDivisionAdd') {
        $("#modalSubDivisionAdd").modal('show');
        show_select('groupDivisionID','');
    } else if(id_modal == 'modalSubDivisionEdit') {
        show_select('groupDivisionIDEdit', '');
        // GET DATA
        $.ajax({
            async   : true,
            cache   : false,
            type    : "GET",
            dataType: "json",
            data    : {
                idSub   : value,
            },
            url     : "/master/subDivisions/trans/get/modalDataSubDivision",
            beforeSend  : function() {
                Swal.fire({
                    title   : 'Data Sedang Dimuat',
                });
                Swal.showLoading();
            },
            success     : function(xhr) {
                Swal.close();
                var data    = xhr.data[0];
                $("#subDivisionIDEdit").val(data.sub_division_id);
                $("#subDivisionNameEdit").val(data.sub_division_name);
                $("#groupDivisionIDEdit").html("<option selected value='"+data.group_division_id+"'>"+data.group_division_name+"</option>");
            },
            error       : function(xhr) {
                console.log(xhr);
            }
        })
    }
}

function close_modal(id_modal)
{
    $("#"+id_modal).modal('hide');
    if(id_modal == 'modalSubDivisionAdd') {
        $("#modalSubDivisionAdd").on('hidden.bs.modal', function(){
            $("#subDivisionName").val(null);
        });
    } else if(id_modal == 'modalSubDivisionEdit') {
        $("#modalSubDivisionEdit").on('hidden.bs.modal', function(){
            $("#subDivisionIDEdit").val(null);
            $("#subDivisionNameEdit").val(null);
        })
    }
}

function show_select(id_form, value)
{
    if(id_form == 'groupDivisionID') {
        var html    = "<option selected disabled>Pilih Grup Divisi</option>";
        $("#"+id_form).select2({
            placeholder     : 'Pilih Grup Divisi',
            theme           : 'bootstrap4',
        });

        $.ajax({
            cache   : false,
            type    : "GET",
            dataType: "json",
            data    : {
                _token  : CSRF_TOKEN,
                cari    : '%',
            },
            url     : "/master/subDivisions/trans/get/selectDataGroupDivision",
            success : function(xhr) {
                $.each(xhr, function(i,item){
                    html    += "<option value='" + item['id'] + "'>" + item['name'] + "</option>";
                });
                $("#"+id_form).html(html);
            },
            error   : function(xhr) {
                $("#"+id_form).html(html);
                console.log(xhr.responseJSON);
            }
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
    if(jenis == 'simpan') {
        var data    = {
            "groupDivisionID"     : $("#groupDivisionID").val(),
            "subDivisionName"       : $("#subDivisionName").val(),
        };

        $.ajax({
            cache   : false,
            type    : "POST",
            dataType: "json",
            url     : "/master/subDivisions/trans/store/modalDataSubDivision",
            data    : {
                _token  : CSRF_TOKEN,
                "gdID"  : data['groupDivisionID'],
                "sdName": data['subDivisionName'],
            },
            beforeSend   : function() {
                Swal.fire({
                    title   : 'Data Sedang Diproses'
                });
                Swal.showLoading();
            },
            success     : function(xhr) {
                if(xhr.success === true) {
                    Swal.fire({
                        icon    : xhr.alert.icon,
                        title   : xhr.alert.message.title,
                        text    : xhr.alert.message.text,
                    }).then((results)   => {
                        if(results.isConfirmed) {
                            close_modal('modalSubDivisionAdd');
                            show_table('tableSubDivision');
                        }
                    })
                }
            },
            error   : function(xhr)
            {
                var request_error   = "<ul>";
                for(var i in xhr.responseJSON.errors) {
                    request_error   += "<li>"+xhr.responseJSON.errors[i]+"</li>";
                }
                request_error += "</ul>";

                Swal.fire({
                    icon    : 'error',
                    title   : xhr.status,
                    html    : request_error,
                })
            }
        })
    } else if(jenis == 'edit') {
        var value    = {
            "groupDivisionID"   : $("#groupDivisionIDEdit").val(),
            "subDIvisionID"     : $("#subDivisionIDEdit").val(),
            "subDivisionName"   : $("#subDivisionNameEdit").val(),
        };

        $.ajax({
            cache   : false,
            type    : "POST",
            dataType: "json",
            data    : {
                _token      : CSRF_TOKEN,
                gdID        : value['groupDivisionID'],
                sdID        : value['subDIvisionID'],
                sdName      : value['subDivisionName'],
            },
            url     : "/master/subDivisions/trans/store/editDataSubDivision",
            beforeSend  : function() {
                Swal.fire({
                    title   : 'Data Sedang Diproses',
                });
                Swal.showLoading();
            },
            success : function(xhr) {
                if(xhr.status == 200) {
                    Swal.fire({
                        icon    : xhr.alert.icon,
                        title   : xhr.alert.message.title,
                        text    : xhr.alert.message.text,
                    }).then((results)   => {
                        if(results.isConfirmed) {
                            close_modal('modalSubDivisionEdit');
                            show_table('tableSubDivision');
                        }
                    });
                }
            },
            error   : function(xhr) {
                var response    = xhr.responseJSON;
                Swal.fire({
                    icon    : response.alert.icon,
                    title   : response.alert.message.title,
                    text    : response.alert.message.text,
                });
            }
        })
    }
}

function getData(url, type, data) {

}