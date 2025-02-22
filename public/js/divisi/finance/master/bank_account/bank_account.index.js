var today       = moment(today).format('YYYY-MM-DD');
var base_url    = window.location.origin;
var table_list_bank_account     = "table_list_bank_account";
var coa_data    = [];
var bank_data   = [];

$(document).ready(function(){
    showTable(table_list_bank_account, []);

    // GET DATA
    // TABLE ACCOUNT BANK
    const accountBankURL    = "divisi/finance/master/bank/list_bank_account";
    const accountBankType   = "GET";
    const accountBankMsg    = "";
    const accountBankData   = [];

    const coaURL            = "divisi/finance/master/coa/list";
    const coaType           = "GET";
    const coaMsg            = "";
    const coaData           = {
        "coa_id"    : 'semua'
    };

    const bankURL           = "divisi/finance/master/bank/list_bank";
    const bankType          = "GET";
    const bankMsg           = "";
    const bankData          = [];

    const getDataTransaction    = [
        doTransaction(accountBankURL, accountBankType, accountBankData, accountBankMsg),
        doTransaction(coaURL, coaType, coaData, coaMsg),
        doTransaction(bankURL, bankType, bankData, bankMsg)
    ];

    Promise.allSettled(getDataTransaction)
        .then((success)     => {
            const accountBankGetData    = success[0].status == 'fulfilled' ? success[0].value.data : [];
            const coaGetData            = success[1].status == 'fulfilled' ? success[1].value.data : [];
            const bankGetData           = success[2].status == 'fulfilled' ? success[2].value.data : [];

            // ACCOUNT BANK
            if(accountBankGetData.total_data > 0) {
                showTable(table_list_bank_account, accountBankGetData);
                $("#"+table_list_bank_account).find('.dataTables_empty').html(success[0].value.message);
            } else {
                $("#"+table_list_bank_account).find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`);
            }

            // COA
            if(coaGetData.length > 0 && coa_data.length < 1) {
                for(const item of coaGetData)
                {
                    if(item['coa_parent'] == '10.02') {
                        coa_data.push(item);
                    }
                }
            }

            // BANK CODE
            // console.log(bankGetData.total_data, bankGetData.data);
            if(bankGetData.total_data > 0 && bank_data.length < 1) {
                for(const item of bankGetData.data)
                {
                    bank_data.push(item);
                }
            }
        })
        .catch((error)      => {
            $("#"+table_list_bank_account).find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`);
        })
});

function showModal(idModal, type, data)
{
    if(idModal == 'modal_form_account_bank') {
        if(data != '') {
            // GET DATA
            const accountBankURL    = "divisi/finance/master/bank/selected_bank_account";
            const accountBankType   = "GET";
            const accountBankData   = {
                "acb_id"    : data,
            };
            const accountBankMsg    = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();
            
            doTransaction(accountBankURL, accountBankType, accountBankData, accountBankMsg)
                .then((success)     => {
                    Swal.close();
                    $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                    // FILL FORM
                    $("#acb_id").val(data);
                    $("#acb_bank_account").val(success.data.bank_account_number);
                    
                    showSelect('acb_bank_id', bank_data, success.data.bank_id);
                    showSelect('acb_coa_id', coa_data, success.data.coa_id === null ? '999' : success.data.coa_id);
                    showSelect('acb_currency', [], success.data.bank_account_currency);
                })
                .catch((error)      => {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : error.responseJSON.message,
                    });
                })
        } else {
            $("#"+idModal).modal({backdrop: 'static', keyboard: false});

            // SHOW SELECT
            showSelect('acb_bank_id', bank_data, '');
            showSelect('acb_coa_id', coa_data, '');
            showSelect('acb_currency', [], '');
        }
        
        $("#btnSimpanFormBankAccount").val(type);
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_form_account_bank') {
        $("#"+idModal).modal('hide');
        $("#btnSimpanFormBankAccount").val("");
        $("#"+idModal).on('hidden.bs.modal', () => {
            // RESET FORM
            $("#acb_bank_account").val(null);
        })
    }
}

function showSelect(idSelect, data, selectedData)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });

    if(idSelect == 'acb_bank_id') {
        let html    = `<option selected disabled>Pilih Bank</option>`;
        if(data.length > 0) {
            for(const item of data) {
                html    += `<option value="${item['bank_id']}">${item['bank_code']} - ${item['bank_name']}</option>`;
            }
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'acb_coa_id') {
        let html    = [
            `<option selected disabled>Pilih Kode CoA</option>`,
            `<option value='999'>Tidak Ada Kode CoA</option>`
        ];

        if(data.length > 0) {
            for(const item of data) {
                html    += `<option value='${item['coa_id']}'>${item['coa_desc']}</option>`
            }
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        };
    } else if(idSelect == 'acb_currency') {
        data    = [
            'IDR', 'USD', 'SAR'
        ];

        let html    = `<option selected disabled>Pilih Mata Uang</option>`;

        $.each(data, (i, item)  => {
            html    += `<option value=${item}>${item}</option>`
        });

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    }
}

function showTable(idTable, data)
{
    // DEFAULT DESTROY TABLE
    $("#"+idTable).DataTable().clear().destroy();

    if(idTable == 'table_list_bank_account') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Diproses..`,
                "zeroRecords"   : `Data Yang Dicari Tidak Ditemukan`,
            },
            pageLength  : -1,
            paging      : false,
            ordering    : false,
            columnDefs  : [
                { "targets" : [0, 4], "width" : "8%", "className" : "text-center align-middle" },
                { "targets" : [2, 3], "width" : "20%", "className" : "text-left align-middle"},
                { "targets" : [1], "className" : "text-left align-middle" }
            ]
        })

        if(data.total_data > 0) {
            let i = 1;
            for(const item of data.data)
            {
                const accountBankName       = item.account_bank_name;
                const accountBankNumber     = item.account_number;
                const accountBankCurrency   = item.account_currency;
                const accountBankCoaCode    = item.coa_id === null ? '' : item.coa_id;
                const accountBankAction     = `<button class="btn btn-success btn-sm" title="Lihat" value="${item.account_id}" onclick="showModal('modal_form_account_bank', 'edit', this.value)"><i class="fa fa-eye"></i></button>`;
                
                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${i++}</label>`,
                    `<label class="font-weight-normal no-margins">${accountBankName} (${accountBankCurrency})</label>`,
                    `<label class="font-weight-normal no-margins">${accountBankNumber}</label>`,
                    `<label class="font-weight-normal no-margins">${accountBankCoaCode}</label>`,
                    accountBankAction
                ]).draw(false);
            }
        }

        $('.dataTables_wrapper').css('padding-bottom', '0px');
    }
}

function doSimpan(idForm, type, data)
{
    if(idForm =='modal_form_account_bank') {
        const fd    = new FormData;
        fd.append('acb_id', $("#acb_id").val());
        fd.append('acb_bank_id', $("#acb_bank_id").val() == null ? '' : $("#acb_bank_id").val());
        fd.append('acb_coa_id', $("#acb_coa_id").val() == null ? '' : $("#acb_coa_id").val());
        fd.append('acb_currency', $("#acb_currency").val() == null ? '' : $("#acb_currency").val());
        fd.append('acb_bank_account', $("#acb_bank_account").val());

        const saveAccountBankURL    = "divisi/finance/master/bank/save_bank_account/" + type;
        const saveAccountBankType   = "POST";
        const saveAccountBankMsg    = Swal.fire({ title : 'Data Sedang Diproses..' }); Swal.showLoading();
        const saveAccountBankData   = fd;

        doTransaction(saveAccountBankURL, saveAccountBankType, saveAccountBankData, saveAccountBankMsg, false, false)
            .then((results)     => {
                Swal.fire({
                    icon    : 'success',
                    title   : 'Berhasil',
                    text    : results.message,
                }).then((res)   => {
                    if(res.isConfirmed) {
                        closeModal('modal_form_account_bank');
                        showTable(table_list_bank_account, []);
                        const accountBankURL    = "divisi/finance/master/bank/list_bank_account";
                        const accountBankType   = "GET";
                        const accountBankMsg    = "";
                        const accountBankData   = [];

                        doTransaction(accountBankURL, accountBankType, accountBankData, accountBankMsg)
                            .then((success)     => {
                                const accountBankGetData    = success.data;
                                if(accountBankGetData.total_data > 0) {
                                    showTable(table_list_bank_account, accountBankGetData);
                                    $("#"+table_list_bank_account).find('.dataTables_empty').html(success[0].value.message);
                                } else {
                                    $("#"+table_list_bank_account).find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`);
                                }
                            })
                            .catch((err)        => {
                                console.log(err);
                            })
                    }
                })
            })
            .catch((error)      => {
                if(error.status == 422) {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : 'Cek Kembali Inputan'
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            $.each(error.responseJSON.message, (i, value)   => {
                                if($("#"+i).data('select2')) {
                                    $("#"+i).addClass('is-invalid');
                                    $("#"+i).on('change', () => {
                                        $("#"+i).removeClass('is-invalid')
                                    })
                                } else {
                                    $("#"+i).addClass('is-invalid');

                                    $("#"+i).on('click', () => {
                                        $("#"+i).removeClass('is-invalid');
                                    })
                                }
                            })
                        }
                    });
                }
            })
    }
}

function doTransaction(url, type, data, message, processData = true, contentType = 'application/x-www-form-urlencoded')
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            url     : base_url + '/' + url,
            type    : type,
            headers     : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            contentType     : contentType,
            processData     : processData,
            beforeSend  : () => {
                message;
            },
            success     : (success) => {
                resolve(success)
            },
            error       : (error)   => {
                reject(error)
            }
        })
    })
}