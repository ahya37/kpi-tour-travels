var tempData    = [];

$(document).ready(function(){
    showTable('table_list_master_program');
});

function showModal(idModal, data, category)
{
    if(idModal == 'modal_master_program') {
        $("#btnSave").val(category);
        
        // GET DATA GROUP DIVISI
        Swal.fire({
            title   : 'Data Sedang Diproses',
        });
        Swal.showLoading();

        const sendData  = {
            "id"    : data,
        };
        const getData   = [
            doTrans('/master/data/trans/get/groupDivision', 'GET', '', ''),
            category == 'edit' ? doTrans('/master/programkerja/master_program/list_master_program', 'GET', sendData, '') : ''
        ];

        Promise.all(getData)
            .then((success) => {
                Swal.close();

                const groupDivisionData     = success[0].data;
                showSelect('master_program_divisi', groupDivisionData);
                
                if(category == 'edit') {
                    const masterProgramData     = success[1].data[0];
                    $("#master_program_id").val(masterProgramData.id);
                    $("#master_program_divisi").val(masterProgramData.division_group_id);
                    $("#master_program_divisi").prop('disabled', true);
                    $("#master_program_uraian").val(masterProgramData.name);
                }

                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            })
            .catch((err)    => {
                Swal.close();
                showSelect('master_program_divisi', [])

                console.log(err);
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            });
        
        $("#master_program_uraian").on('keyup', ()=> {
            const idForm  = $("#master_program_uraian").val();
            $("#master_program_uraian").val(idForm.toUpperCase());
        })
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_master_program') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#master_program_uraian").val(null);
            $("#master_program_divisi").prop('disabled', false);
        })
    }
}

function showTable(idTable)
{
    if(idTable == 'table_list_master_program') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan",
            },
            processing  : true,
            autoWidth   : false,
            columnDefs  : [
                {
                    "targets"   : [0, 3],
                    "width"     : "8%",
                    "className" : "text-center",
                },
            ],
        });
        
        // GET DATA
        const sendData  = {
            "id"    : '%',
        };
        doTrans('/master/programkerja/master_program/list_master_program', 'GET', sendData, '')
            .then((success) => {
                let i = 1;
                for(const item of success.data)
                {
                    $("#"+idTable).DataTable().row.add([
                        i++,
                        item.name,
                        item.group_division_name,
                        "<button class='btn btn-sm btn-primary' title='Ubah Data' value='" + item.id + "' onclick='showModal(`modal_master_program`, this.value, `edit`)'><i class='fa fa-edit'></i></button>",
                    ]).draw(false);
                }
            })
            .catch((err)    => {
                console.log(err);
                $(".dataTables_empty").empty();
                $(".dataTables_empty").html('Tidak ada data yang bisa dimuat..');
            })
    }
}

function showSelect(idSelect, data)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });

    if(idSelect == 'master_program_divisi') {
        var html;
        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += "<option value='" + item.id + "'>" + item.name + "</option>";
            })
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
        
        $("#"+idSelect).html(html);
    }
}

function simpanData(idForm, jenis)
{
    if(idForm == 'modal_master_program') {
        // GET DATA FROM FORM
        const divisi    = $("#master_program_divisi");
        const uraian    = $("#master_program_uraian");
        const id        = $("#master_program_id");
        
        // VALIDASI
        if(divisi.val().length <= 0) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Divisi Tidak Boleh Kosong',
                didClose    : () => {
                    divisi.select2('open');
                }
            })
        } else if(uraian.val() == '') {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Uraian Harus Diisi',
                didClose    : ()    => {
                    uraian.focus();
                }
            })
        } else {
            // DO SIMPAN
            const sendData  = {
                "id"        : id.val(),
                "divisi"    : divisi.val(),
                "uraian"    : uraian.val(),
            }

            const message   = Swal.fire({ title : "Data Sedang Diproses" }); Swal.showLoading();

            // SIMPAN DATA
            const doSimpan  = doTrans('/master/programkerja/master_program/simpan_master_group/'+jenis, 'POST', sendData, message);

            doSimpan
                .then((success) => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text,
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            closeModal('modal_master_program');
                            showTable('table_list_master_program');
                        }
                    })
                })
        }
    }
}

function doTrans(url, type, data, message)
{
    return new Promise ((resolve, reject)   => {
        $.ajax({
            cache   : false,
            type    : type, 
            url     : url,
            dataType: 'json',
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : ()    => {
                message;
            },
            success : (success)    => {
                resolve(success);
            },
            error   : (error)       => {
                reject(error);
            }
        });
    })
}