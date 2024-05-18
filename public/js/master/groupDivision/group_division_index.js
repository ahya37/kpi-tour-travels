$(document).ready(function(){
    show_table('table_group_division','%');

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
    if(id_table == 'table_group_division') {
        $("#table_group_division").DataTable().clear().destroy();
        $("#table_group_division").DataTable({
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

function show_modal(id_modal, value)
{
    $("#"+id_modal).modal({backdrop: 'static', keyboard: false});
    if(id_modal == 'modal_add_division') {
        $("#"+id_modal).modal('show');
        $("#"+id_modal).on('shown.bs.modal', function(){
            $("#name_group_division_add").focus();
        })
    } else if(id_modal == 'modal_edit_division') {
        // AMBIL DATA GROUP DIVISION
        $.ajax({
            async   : false,
            cache   : false,
            type    : "GET",
            dataType: "json",
            url     : "/master/groupDivisions/trans/get/modalDataGroupDivisions/"+value,
            success     : function(response) {
                // SHOW MODAL
                $("#modal_edit_division").modal('show');
                $("#modal_edit_division").on('shown.bs.modal', function(){
                    $("#name_group_division_edit").focus();
                });

                // SHOW DATA
                $("#id_group_division_edit").val(value);
                $("#name_group_division_edit").val(response.data.gdName);
            }
        });
    } else if(id_modal == 'modal_hapus_data') {
        var gdID    = value;

        Swal.fire({
            icon    : 'question',
            title   : 'Hapus Data',
            text    : 'Anda yakin ingin menghapus data ini?',
            showConfirmButton   : true,
            showCancelButton    : true,
            confirmButtonText   : 'Ya, Hapus',
            cancelButtonText    : 'Tidak',
        }).then(function(results){
            if(results.isConfirmed) {
                // RUNNING AJAX UPDATE
                $.ajax({
                    cache   : false,
                    type    : "POST",
                    dataType: "json",
                    url     : "/master/groupDivisions/trans/delete/modalDataGroupDivisions/"+value,
                    beforeSend  : function() {
                        Swal.fire({
                            title   : 'Data Sedang diproses',
                        })
                        Swal.showLoading();
                    },
                    success : function(response) {
                        if(response.status == 200) {
                            Swal.fire({
                                icon    : 'success',
                                title   : 'Berhasil',
                                text    : 'Data Berhasil Dihapus',
                            }).then(function(results){
                                if(results.isConfirmed) {
                                    show_table('table_group_division','%');
                                }
                            })
                        } else {
                            Swal.fire({
                                icon    : 'error',
                                title   : 'Terjadi Kesalahan',
                                text    : 'Data Gagal Disimpan'
                            });
                        }
                    },
                    error   : function(xhr) {
                        Swal.fire({
                            icon    : 'error',
                            title   : 'Terjadi Kesalahan',
                            text    : 'Sistem sedang bermasalah, silahkan tunggu..',
                        })
                    }
                });
            }
        });
    }
}

function close_modal(id_modal) {
    if(id_modal == 'modal_add_division') {
        $("#"+id_modal).modal('hide');
        $("#"+id_modal).on('hidden.bs.modal', function(){
            $("#name_group_division_add").val('');
        });
    } else if(id_modal == 'modal_edit_division') {
        $("#"+id_modal).modal('hide');
    }
}

function do_save(type)
{
    if(type == 'save') {
        var group_division_name = $("#name_group_division_add").val();
        $.ajax({
            type    : "POST",
            dataType: "json",
            data    : {
                "group_division_name"   : group_division_name,
                "test_input"            : "test_input",
                "test"                  : "test",
            },
            url     : '/master/groupDivisions/trans/store/dataGroupDivisions',
            beforeSend  : function() {
                close_modal('modal_add_division');
                Swal.fire({
                    title   : 'Data sedang diproses',
                });
                Swal.showLoading();
            },
            success     : function(response) {
                Swal.fire({
                    icon    : 'success',
                    title   : response.message,
                    text    : response.description,
                }).then((results)   => {
                    if(results.isConfirmed) {
                        show_table('table_group_division','%');
                    }
                });
            },
            error   : function(xhr)
            {
                Swal.fire({
                    icon    : 'error',
                    title   : xhr.status,
                    text    : xhr.statusText,
                }).then((results)   => {
                    if(results.isConfirmed) {
                        show_modal('modal_add_division');
                        $("#name_group_division_add").val(group_division_name);
                    }
                });
            }
        })
    } else if(type == 'edit') {
        var groupDivisionName     = $("#name_group_division_edit").val();
        var groupDivisionID       = $("#id_group_division_edit").val();

        if(groupDivisionName == '') {
            close_modal('modal_edit_division');
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Nama Tidak Boleh Kosong',
            }).then((results)   => {
                if(results.isConfirmed) {
                    show_modal('modal_edit_division', groupDivisionID);
                    $("#name_group_division_edit").focus();
                    $("#name_group_division_edit").addClass('is-invalid');
                }
            })
        }
    }
}