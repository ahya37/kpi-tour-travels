var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;

console.log('Hari Ini : ' + today);

$(document).ready(function(){
    showTable('table_list_coa', []);
    showSelect('filter_coa_kode', [], '');

    // GET DATA COA
    let coaURL      = "divisi/finance/master/coa/list";
    let coaType     = "GET";
    let coaMessage  = "";
    let coaData     = {
        'coa_id'    : 'semua',
    };

    let getData     = [
        doTransaction(coaURL, coaType, coaData, coaMessage),
    ];

    Promise.allSettled(getData)
        .then((results)     => {
            let coaGetData  = results[0].status == 'fulfilled' ? results[0].value.data : [];
            let coaSelectData   = [];
            showTable('table_list_coa', coaGetData);
            if(coaGetData.length > 0) {
                $("#table_list_coa").find('.dataTables_empty').html(`Data Berhasil Dimuat`);
                
                coaSelectData.push({
                    'id'    : '%',
                    'text'  : 'Semua',
                });

                coaGetData.forEach((item)   => {
                    if(item['coa_level'] == 0) {
                        coaSelectData.push({
                            'id'    : item['coa_id'],
                            'text'  : item['coa_desc'],
                        })
                    }
                })
                showSelect('filter_coa_kode', coaSelectData, '%');
            } else {
                $("#table_list_coa").find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Ditampilkan`);
            }
        })
        .catch((error)      => {
            console.log(error);
            showTable('table_list_coa', []);
            $("#table_list_coa").find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Ditampilkan`);
        })
});

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();
    
    if(idTable == 'table_list_coa') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"     : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat..`,
                "zeroRecords"   : `Data Yang Dicari Tidak Ditemukan`,
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "15%" },
                { "targets" : [2], "className" : "text-left align-middle" },
                { "targets" : [3], "className" : "text-center align-middle", "width" : "10%" },
            ],
            pageLength  : -1,
            paging      : false,
            ordering    : false,
        })

        if(data.length > 0) {
            let seq     = 1;
            data.forEach((item)   => {
                let coaID           = item['coa_id'];
                let coaLevel        = item['coa_level'];
                let coaDescription  = item['coa_desc'];
                let number;
                let spacing;

                if(coaLevel == 0) {
                    number  = seq++;
                    spacing     = '';
                } else {
                    number  = '';
                    if(coaLevel == 1) {
                        spacing     = `<span class="ml-1"></span>`
                    } else if(coaLevel == 2) {
                        spacing     = `<span class="ml-2"></span>`
                    } else if(coaLevel == 3) {
                        spacing     = `<span class="ml-3"></span>`
                    }
                }

                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${number}</label>`,
                    `${spacing}<label class="font-weight-normal no-margins">${coaID}</label>`,
                    `${spacing}<label class="font-weight-normal no-margins">${coaDescription}</label>`,
                    `<button class="btn btn-sm btn-primary" value="${coaID}" title="Edit" disabled><i class="fa fa-edit"></i></button>`,
                ]).draw(false);
            })
        }
    }
}

function showSelect(idSelect, data, selectedData)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });

    if(idSelect == 'filter_coa_kode')
    {
        let html    = `<option selected disabled>Pilih COA Kode</option>`;
        
        if(data.length > 0) {
            data.forEach((item) => {
                html    += `<option value="${item['id']}">${item['id']} - ${item['text']}</option>`
            })
            
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'coa_parent') {
        let html    = `<option selected disabled>Pilih COA Kode</option>`;
        let spacing;
        $.each(data, (i, item)  => {
            if(item['coa_level'] < 3) {
                if(item['coa_level'] == 1) {
                    spacing     = `&nbsp;&nbsp;`;
                } else if(item['coa_level'] == 2) {
                    spacing     = `&nbsp;&nbsp;&nbsp;&nbsp;`;
                } else {
                    spacing     = '';
                }
                html    += `<option value='${item['coa_level']}|${item['coa_id']}'>${spacing}${item['coa_id']} - ${item['coa_desc']}</option>`
            }
        })

        $("#"+idSelect).html(html);
    }
}

function showSelectDetail(idSelect, data)
{
    if(idSelect == 'filter_coa_kode')
    {
        showTable('table_list_coa', []);
        let coaURL      = "divisi/finance/master/coa/list";
        let coaType     = "GET";
        let coaMessage  = "";
        let coaData     = {
            'coa_id'    : data,
        };

        doTransaction(coaURL, coaType, coaData, coaMessage)
            .then((results)     => {
                let coaGetData  = results.data.length > 0 ? results.data : [];
                console.log(coaGetData);
                showTable('table_list_coa', coaGetData);

                if(coaGetData.length > 0) {
                    $("#table_list_coa").find('.dataTables_empty').html(`Data Berhasil Dimuat`)
                } else {
                    $("#table_list_coa").find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`);
                }
            })
            .catch((error)      => {
                $("#table_list_coa").find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`);
            })

    } else if(idSelect == 'coa_parent') {
        // RESET FORM
        $("#form_coa").trigger('reset');
        
        // FILL FORM
        let coaLevel    = parseInt(data.split('|')[0]) + 1;
        let coaParent   = data.split('|')[1] + '.';

        $("#coa_id_level").val(coaLevel);
        $("#coa_id_parent").val(coaParent);
    }
}

function showModal(idModal, type, data)
{
    if(idModal == 'modal_form_coa') {
        // GET MASTER COA
        let coaURL  = "divisi/finance/master/coa/list";
        let coaType = "GET";
        let coaData = {
            'coa_id'    : '%',
        };
        let coaMsg  = "";

        // GET SELECTED COA DATA
        let getCoaURL   = "divisi/finance/mater/coa/list";
        let getCoaType  = "GET";
        let getCoaData  = {
            'coa_id'    : data,
        };
        let getCoaMsg   = Swal.fire({ title : 'Data Sedang Dimuat..' }); Swal.showLoading();

        let getData     = [
            doTransaction(coaURL, coaType, coaData, coaMsg),
            data != '' ? doTransaction(getCoaURL, getCoaType, getCoaData, getCoaMsg) : ''
        ];

        Promise.allSettled(getData)
            .then((results)     => {
                Swal.close();
                
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

                let coaGetData  = results[0].status == 'fulfilled' ? results[0].value.data : [];
                
                if(data == '') {
                    showSelect('coa_parent', coaGetData, '');
                }
            })
            .catch((error)      => {
                console.log(error);
            })

        $("#btnSimpanFormCoa").val(type);
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_form_coa') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            // RESET FORM
            $("#form_coa").trigger('reset');
        })
    }
}

function simpanData(idForm, jenis, data)
{
    if(idForm == 'form_coa')
    {
        const formID    = document.getElementById(idForm);
        const formData  = new FormData(formID);
        const coaParent = $("#coa_parent");

        // VALIDASI FORM
        if(coaParent.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Referensi Kode Harus Dipilih',
                didClose    : () => {
                    coaParent.select2('open')
                }
            })
        } else {
            formData.append('coa_parent', coaParent.val());

            const saveCoaURL    = "divisi/finance/master/coa/save/" + jenis;
            const saveCoaType   = "POST";
            const saveCoaData   = formData;
            const saveCoaMsg    = Swal.fire({ title : "Data Sedang Diproses.." }); Swal.showLoading();

            doTransactionV2(saveCoaURL, saveCoaType, saveCoaData, saveCoaMsg)
                .then((results)     => {
                    Swal.fire({
                        icon    : 'success',
                        title   : 'Berhasil',
                        text    : results.message,
                    }).then((res)   => {
                        closeModal('modal_form_coa');
                        showTable('table_list_coa', []);
                        // GET DATA COA BARU
                        let coaURL      = "divisi/finance/master/coa/list";
                        let coaType     = "GET";
                        let coaMessage  = "";
                        let coaData     = {
                            'coa_id'    : $("#filter_coa_kode").val(),
                        };

                        doTransaction(coaURL, coaType, coaData, coaMessage)
                            .then((success)     => {
                                showTable('table_list_coa', success.data);
                            })
                            .catch((error)      => {
                                showTable('table_list_coa', []);
                            })
                    })
                })
                .catch((error)      => {
                    const errorCoaMessage   = error.responseJSON;
                    if(errorCoaMessage.status == 422) {
                        $.each(errorCoaMessage.message, (item, value)   => {
                            $("#"+item).addClass('is-invalid');
    
                            $("#"+item).on('keyup', () => {
                                $("#"+item).removeClass('is-invalid');
                            })
    
                            $("#"+item).on('click', () => {
                                $("#"+item).removeClass('is-invalid');
                            })
                        })
                        Swal.close();
                    } else {
                        Swal.fire({
                            icon    : 'error',
                            title   : 'Terjadi Kesalahan',
                            text    : errorCoaMessage.message,
                            didClose    : () => {
                                $("#coa_id_child").addClass('is-invalid');

                                $("#coa_id_child").on('keyup', () => {
                                    $("#coa_id_child").removeClass('is-invalid');
                                })

                                $("#coa_id_child").on('click', () => {
                                    $("#coa_id_child").removeClass('is-invalid');
                                })
                            }
                        });
                    }
                })
        }
    }
}

function doTransaction(link, type, data, message)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            url     : base_url + "/" + link,
            type    : type,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                message;
            },
            success : (success) => {
                resolve(success)
            },
            error   : (error)   => {
                reject(error)
            }
        })
    })
}

function doTransactionV2(link, type, data, message)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            url     : base_url + "/" + link,
            type    : type,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            contentType     : false,
            processData     : false,
            data    : data,
            beforeSend  : () => {
                message;
            },
            success  : (success)    => {
                resolve(success)
            },
            error   : (error)       => {
                reject(error);
            }
        })
    })
}