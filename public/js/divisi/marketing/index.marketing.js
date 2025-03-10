var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
var dataMonth   = [];
var dataYear    = [];
var dataBankAccount     = [];

// GET BULAN
for(let i = 0; i < 12; i++) {
    let monthNumber     = moment(i + 1, 'M').format('MM');
    let monthName       = moment(monthNumber, 'MM').format('MMMM');

    dataMonth.push({
        'monthNumber'   : monthNumber,
        'monthName'     : monthName,
    });
}

// GET TAHUN
for(let i = moment(today, 'YYYY-MM-DD').subtract(10, 'year').format('YYYY'); i < parseInt(moment(today, 'YYYY-MM-DD').add(10, 'year').format('YYYY')); i++) {
    dataYear.push({
        'yearNumber' : parseInt(i),
    });
}

$(document).ready(function(){
    showSelect('select_filter_keberangkatan', dataYear, moment(today, 'YYYY-MM-DD').format('YYYY'));
    showTable('table_pembayaran_haji', []);

    // GET DATA
    const bayarHajiURL  = "divisi/finance/pembayaran/haji/list_pembayaran_haji";
    const bayarHajiType = "GET";

    const bankAccountURL    =  "divisi/finance/master/bank/list_bank_account";
    const bankAccountType   = "GET";

    const transGetData  = [
        doTransaction(bayarHajiURL, bayarHajiType),
        doTransaction(bankAccountURL, bankAccountType)
    ];

    Promise.allSettled(transGetData)
        .then((success)     => {
            // TABLE PEMBAYARAN HAJI
            const bayarHajiGetData  = success[0].status == 'fulfilled' ? success[0].value.data : []
            showTable('table_pembayaran_haji', bayarHajiGetData);
            if(bayarHajiGetData.length < 1) {
                $("#table_pembayaran_haji").find('.dataTables_empty').html(`Tidak Ada Data Pembayaran Haji`)
            }

            // BANK ACCOUNT
            const bankAccountGetData    = success[1].status == 'fulfilled' ? success[1].value.data : [];
            dataBankAccount.push(bankAccountGetData.data);
        })
        .catch((error)      => {
            console.log(error);
            showTable('table_pembayaran_haji', []);
        })
});

function showModal(idModal, data = '')
{
    if(idModal == 'modal_pembayaran_haji')
    {   
        if(data == '') {
            $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            $(".is_empty_data").removeClass('d-none');
            $(".is_not_empty_data").addClass('d-none');

            showSelect('hj_member_id');
            showSelect('hj_depature_code');
            showSelect('hj_estimasi_keberangkatan', dataYear, moment(today, 'YYYY-MM-DD').format('YYYY'));

            $("#btn_simpan_ph").val('add');

            showTable('table_pembayaran_haji_form', []);
            $("#table_pembayaran_haji_form").find('.dataTables_empty').html(`Klik 'Tambah Baris' Untuk Menambahkan Baris`);
        } else {
            $(".is_empty_data").addClass('d-none');
            $(".is_not_empty_data").removeClass('d-none');

            // GET DATA 
            const hajiDetailURL     = "divisi/marketing/pembayaran/pembayaran_haji_detail";
            const hajiDetailType    = "GET";
            const hajiDetailData    = {
                'trans_id'  : data,
            };
            const hajiDetailMsg     = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

            doTransaction(hajiDetailURL, hajiDetailType, hajiDetailData, hajiDetailMsg)
                .then((success)     => {
                    Swal.close();
                    const hajiDataHeader    = success.data.header;
                    const hajiDataDetail    = success.data.detail;

                    showSelect('hj_estimasi_keberangkatan', dataYear, hajiDataHeader['estimasi_keberangkatan']);

                    $("#hj_trans_id").val(data);
                    $("#hj_member_name_edit").val(hajiDataHeader['jemaah_name']);
                    $("#hj_no_daftar").val(hajiDataHeader['no_daftar']);
                    $("#hj_tgl_daftar").val(hajiDataHeader['tgl_daftar']);
                    $("#hj_no_bpih").val(hajiDataHeader['no_bpih']);

                    $("#hj_depature_code_edit").val(hajiDataHeader['tour_code']);
                    $("#hj_room").val(hajiDataHeader['jemaah_pkg']);
                    $("#hj_room_price").val(hajiDataHeader['jemaah_harga_paket'].toLocaleString('en-US'));
                    $("#hj_current_payment").val(parseInt(hajiDataHeader['jemaah_total_bayar']).toLocaleString('en-US'));
                    $("#hj_status_payment").val(hajiDataHeader['jemaah_status_bayar']);

                    showTable('table_pembayaran_haji_form', hajiDataDetail);
                    
                    $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                })
                .catch((error)      => {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : error.responseJSON.message,
                    })
                })

            $("#btn_simpan_ph").val('edit');
        }
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_pembayaran_haji')
    {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            
            $("#hj_member_id").val('');
            $("#hj_member_id_edit").val('');
            $("#hj_member_name_edit").val('');
            $("#hj_room").val('');
            $("#hj_no_daftar").val('');
            $("#hj_tgl_daftar").val('');
            $("#hj_no_bpih").val('');
            
            $("#hj_room_price").val('');
            $("#hj_current_payment").val('');
            $("#hj_status_payment").val('');

            $("#btn_tambah_baris_ph").val(1);
            $("#btn_simpan_ph").val('');

            $("#hj_trans_id").val();
        })
    }
}

function showSelect(idSelect, data = [], selectedData = '', seq = '')
{
    // DEFAULT STYLE
    $("#"+idSelect+""+seq).select2({
        theme   : 'bootstrap4',
    })
    if(idSelect == 'select_filter_keberangkatan') {
        let html    = `<option selected disabled>Pilih Tahun</option>`;

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value="${item['yearNumber']}">${item['yearNumber']}</option>`;
            })
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'hj_member_id') {
        $("#"+idSelect).select2({
            theme       : 'bootstrap4',
            placeholder : `Pilih Nama Jemaah`,
            language    : {
                "inputTooShort" : () => {
                    return `Minimal 3 Karakter Untuk Melakukan Pencarian`
                },
                "searching"     : () => {
                    return `Data Sedang Dicari..`
                },
                "noResults"     : () => {
                    return `Data Yang Dicari Tidak Ditemukan`
                },
                "errorLoading"  : () => {
                    return `Gagal Melakukan Pencarian`
                }
            },
            ajax    : {
                url     : base_url + "/divisi/finance/master/member/list",
                dataType: "json",
                headers : {
                    'X-CSRF-TOKEN'  : CSRF_TOKEN,
                },
                delay   : 250,
                data    : (e) => {
                    return {
                        keyword     : e.term,
                    }
                },
                processResults  : (data) => {
                    if(data.status == 200) {
                        return {
                            results     : data.data.map((item)  => {
                                return {
                                    id      : item.member_id,
                                    text    : item.member_name,
                                }
                            })
                        }
                    }
                },
                cache   : true,
            },
            minimumInputLength  : 3,
        })
    } else if(idSelect == 'hj_depature_code') {
        let html    = [
            "<option selected disabled>Pilih Kode Keberangkatan</option>",
            "<option value='-'>Belum Ada Kode</option>"
        ];
        
        if(data.length > 0) {
            for(const item of data) {
                html    += `<option value="${item['depature_code']}">${item['depature_code']}</option>`
            }
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'hj_estimasi_keberangkatan') {
        let html    = `<option selected disabled>Pilih Estimasi Keberangkatan</option>`;

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value="${item['yearNumber']}">${item['yearNumber']}</option>`
            });
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'hj_detail_method') {
        $("#"+idSelect+""+seq).select2({
            theme   : 'bootstrap4',
            minimumResultsForSearch     : -1,
        })

        let html    = `<option selected disabled>Metode</option>`;

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value="${item['id']}">${item['text']}</option>`    
            });
        }

        $("#"+idSelect+""+seq).html(html);

        if(selectedData != '') {
            $("#"+idSelect+""+seq).val(selectedData);
        }
    } else if(idSelect == 'hj_detail_curr') {
        $("#"+idSelect+""+seq).select2({
            theme   : 'bootstrap4',
            minimumResultsForSearch     : -1,
        })

        let html    = `<option selected disabled>Mata Uang</option>`;

        $.each(data, (i, item)  => {
            html    += `<option value="${item['id']}">${item['text']}</option>`
        });

        $("#"+idSelect+""+seq).html(html);

        if(selectedData != '') {
            $("#"+idSelect+""+seq).val(selectedData);
        }
    } else if(idSelect == 'hj_detail_bank_acc') {
        let html    = `<option selected disabled>Pilih No. Rekening</option>`;

        if(data.length > 0) {
            $.each(data[0], (i, item)  => {
                html    += `<option value="${item['account_id']}">${item['account_bank_name']} | ${item['account_number']}</option>`
            });
        } 

        $("#"+idSelect+""+seq).html(html);

        if(selectedData != '') {
            $("#"+idSelect+""+seq).val(selectedData);
        }
    }
}

function showSelectDetail(idSelect, data = '', seq = '')
{
    if(idSelect == 'hj_member_id') {
        const hajiCodeURL   = "divisi/finance/pembayaran/haji/detail_jemaah";
        const hajiCodeType  = "GET";
        const hajiCodeData  = {
            "member_id" : data,
            "haji_kode" : "semua",
        };
        const hajiCodeMsg   = Swal.fire({ title : "Data Sedang Diambil.." }); Swal.showLoading();

        doTransaction(hajiCodeURL, hajiCodeType, hajiCodeData, hajiCodeMsg)
            .then((success)     => {
                Swal.close();
                let dataCodeDepature    = [];
                for(const item of success.data)
                {
                    dataCodeDepature.push({
                        'depature_code' : item['kode_keberangkatan'],
                    })
                }
                showSelect('hj_depature_code', dataCodeDepature, '');
            })
            .catch((error)      => {
                Swal.close();
                console.log(error);
            })
    } else if(idSelect == 'hj_depature_code') {
        const hajiCodeURL   = "divisi/finance/pembayaran/haji/detail_jemaah";
        const hajiCodeType  = "GET";
        const hajiCodeData  = {
            "member_id" : $("#hj_member_id").val(),
            "haji_kode" : data,
        };
        const hajiCodeMsg   = Swal.fire({ title : "Data Sedang Diambil.." }); Swal.showLoading();

        doTransaction(hajiCodeURL, hajiCodeType, hajiCodeData, hajiCodeMsg)
            .then((success)     => {
                Swal.close();
                const hajiGetData   = success.data[0];
                let hajiRoomPrice   = 0;

                switch(hajiGetData['haji_paket']) {
                    case 'Double' :
                        hajiRoomPrice   = 20000;
                    break;
                    case 'Triple' : 
                        hajiRoomPrice   = 18500;
                    break;
                    case 'Quad'     : 
                        hajiRoomPrice    = 17500;
                    break;
                    default : hajiRoomPrice = 0;
                }
                // ISI FORM
                $("#hj_room").val(hajiGetData['haji_paket']);
                $("#hj_no_daftar").val(hajiGetData['no_daftar']);
                $("#hj_tgl_daftar").val(hajiGetData['tgl_keberangkatan']);
                $("#hj_no_bpih").val(hajiGetData['no_bpih']);
                
                $("#hj_room_price").val(parseInt(hajiRoomPrice).toLocaleString('en-US'));
                $("#hj_current_payment").val();
                $("#hj_status_payment").val();
            })
            .catch((error)      => {
                Swal.close();
                $("#hj_room").val('-');
                $("#hj_no_daftar").val('-');
                $("#hj_tgl_daftar").val(moment(today, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                $("#hj_no_bpih").val('-');
                
                $("#hj_room_price").val(0);
                $("#hj_current_payment").val();
                $("#hj_status_payment").val();
                console.log(error);
            })
    } else if(idSelect == 'hj_detail_method') {
        const selectedMethod   = data;
        if(selectedMethod == 'tf') {
            $("#hj_detail_bank_acc"+seq).prop('disabled', false);
            showSelect('hj_detail_bank_acc', dataBankAccount, '', seq);
        } else {
            $("#hj_detail_bank_acc"+seq).prop('disabled', true);
            showSelect('hj_detail_bank_acc', [], '', seq);
        }
    }
}

function showTable(idTable, data = [])
{
    // RESET TABLE
    $("#"+idTable).DataTable().clear().destroy();

    if(idTable == 'table_pembayaran_haji') 
    {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat...`,
                zeroRecords : 'Data Yang Dicari Tidak Ditemukan',
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "align-middle" },
                { "targets" : [2, 3], "className" : "text-center align-middle", "width" : "15%" },
                { "targets" : [4], "className" : "text-center align-middle", "width" : "13%" },
                { "targets" : [5], "className" : "text-center align-middle", "width" : "8%" },
            ]
        });

        if(data.length > 0) {
            for(let i = 0; i < data.length; i++)
            {
                let transHajiID             = data[i]['trans_id'];
                let transHajiJemaahName     = data[i]['jemaah_name'];
                let transHajiJemaahPackage  = data[i]['jemaah_pkg'];
                let transHajiDepature       = data[i]['est_berangkat'];
                let transHajiPayment        = parseInt(data[i]['total_payment']);
                let transHajiHarga          = 0;
                
                switch (transHajiJemaahPackage) {
                    case 'Double'   :
                        transHajiHarga  = 20000;
                    break;
                    case 'Triple'   :
                        transHajiHarga  = 18500;
                    break;
                    case 'Quad' :
                        transHajiHarga  = 17500;
                    break;
                    default : transHajiHarga = 0;
                }

                let transHajiStatusBayar    = transHajiHarga == transHajiPayment ? `<span class="badge badge-sm badge-primary"><label class="no-margins">Lunas</label></span>` : (transHajiPayment < transHajiHarga ? `<span class="badge badge-sm badge-warning"><label class="no-margins">Kurang Bayar</label></span>` : `<span class="badge badge-sm badge-primary"><label class="no-margins">Lebih Bayar</label></span>`);
                
                let transHajiButtonAct      = `<button class="btn btn-sm btn-primary" value="${transHajiID}" title="Lihat Detail" onclick="showModal('modal_pembayaran_haji', this.value)"><i class="fa fa-eye"></i></button>`;

                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${i + 1}</label>`,
                    `<label class="font-weight-normal no-margins">${transHajiJemaahName}</label>`,
                    `<label class="font-weight-normal no-margins">${transHajiJemaahPackage}</label>`,
                    `<label class="font-weight-normal no-margins">${transHajiDepature}</label>`,
                    `<label class="font-weight-normal no-margins">${transHajiStatusBayar}</label>`,
                    transHajiButtonAct
                ]).draw(false);
            }
        } 
    } else if(idTable == 'table_pembayaran_haji_form') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat..`,
            },
            autoWidth   : false,
            searching   : false,
            pageLength  : -1,
            paging      : false,
            bInfo       : false,
            ordering    : false,
            columnDefs  : [
                { "targets" : [0, 1], "className" : "text-center align-middle", "width" : "6%" },
                { "targets" : [2, 4, 5], "width" : "15%" },
                { "targets" : [3], "width" : "10%"}
            ],
        })

        if(data.length > 0) {
            let seq = 1;
            for(let i = 0; i < data.length; i++) {
                addRowTable('table_pembayaran_haji_form', data[i], seq++);
            }
        } else {
            let currentSeq  = $("#btn_tambah_baris_ph").val();
            addRowTable('table_pembayaran_haji_form', '', parseInt(currentSeq));
        }
    }

    // REMOVE FOOTER
    $("#"+idTable+"_wrapper").css('padding-bottom', '0px');
}

function addRowTable(idTable, data = '', seq = '')
{
    if(idTable == 'table_pembayaran_haji_form') {
        let buttonSeq       = $("#btn_tambah_baris_ph");
        let inputSeq        = `<input type="text" class="form-control text-center" id="hj_detail_no${seq}" readonly>`;
        let buttonDelete    = `<button class="btn btn-sm btn-danger" type="button" title="Hapus Baris" onclick="deleteRowTable('table_pembayaran_haji_form', [], ${seq})"><i class="fa fa-trash"></i></button>`;
        let inputTglBayar   = `<input type="text" class="form-control" placeholder="DD/MM/YYYY" id="hj_detail_tglBayar${seq}" readonly title="Pilih Tgl. Transaksi">`;
        let selectMetode    = `<select class="form-control" id="hj_detail_method${seq}" style="width: 120px;" onchange="showSelectDetail('hj_detail_method', this.value, ${seq})"></select>`;
        let selectBankAcc   = `<select class="form-control" id="hj_detail_bank_acc${seq}" style="width: 290px;" disabled></select>`
        let selectKurs      = `<select class="form-control" id="hj_detail_curr${seq}" style="width: 100%;"></select>`;
        let inputJmlBayar   = `<input type="text" class="form-control" placeholder="Jml. Bayar" value="0" onclick="this.select()" id="hj_detail_amount${seq}">`

        $("#"+idTable).DataTable().row.add([
            buttonDelete,
            inputSeq,
            inputTglBayar,
            selectMetode,
            selectBankAcc,
            selectKurs,
            inputJmlBayar
        ]).draw(false);
        
        // SHOW ANOTHER ELEMENT
        let dataMethod  = [
            { 'id' : 'tf', 'text' : 'Transfer' },
            { 'id' : 'cash', 'text' : 'Cash' },
        ];

        let dataKurs    = [
            { 'id' : 'IDR', 'text' : '(Rp.) Rupiah' },
            { 'id' : 'USD', 'text' : '($) Dollar' },
        ];

        $("#hj_detail_tglBayar"+seq).daterangepicker({
            drops       : 'up',
            minDate     : moment(today, 'YYYY-MM-DD').subtract(10, 'year'),
            maxDate     : moment(today, 'YYYY-MM-DD').add(10, 'year'),
            autoApply   : true,
            showDropdowns : true,
            format      : 'DD/MM/YYYY',
            setStartDate    : moment(today, 'YYYY-MM-DD'),
            singleDatePicker    : true,
            locale  : {
                cancelLabel : 'Batal',
                applyLabel  : 'Simpan',
            },
        }).css({'cursor':'pointer', 'background':'white'});

        $("#hj_detail_amount"+seq).on('keyup', () => {
            let currentValue    = $("#hj_detail_amount"+seq).val();
            let formatText      = currentValue.replace(/[^0-9]/g, '');
            let usdFormat       = parseInt(formatText).toLocaleString('en-US');
            
            if(currentValue != '') {
                $("#hj_detail_amount"+seq).val(usdFormat);
            }
        })

        // DEFAULT VALUE
        $("#hj_detail_no"+seq).val(seq);

        if(data.length < 1) {
            $("#hj_detail_no"+seq).focus();
            showSelect('hj_detail_method', dataMethod, '', seq);
            showSelect('hj_detail_curr', dataKurs, '', seq);
            showSelect('hj_detail_bank_acc', [], '', seq);
        } else {
            $("#hj_detail_tglBayar"+seq).data('daterangepicker').setStartDate(moment(data['payment_date'], 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#hj_detail_tglBayar"+seq).data('daterangepicker').setEndDate(moment(data['payment_date'], 'YYYY-MM-DD').format('DD/MM/YYYY'));

            let bankAccountID   = data['bank_account_id'] == null ? '' : data['bank_account_id'];

            showSelect('hj_detail_method', dataMethod, data['payment_method'], seq);
            showSelect('hj_detail_curr', dataKurs, data['payment_curr'], seq);
            if(data['payment_method'] == 'cash') {
                $("#hj_detail_bank_acc"+seq).prop('disabled', true);
                showSelect('hj_detail_bank_acc', [], bankAccountID, seq);
            } else {
                $("#hj_detail_bank_acc"+seq).prop('disabled', false);
                showSelect('hj_detail_bank_acc', dataBankAccount, bankAccountID, seq);
            }
            $("#hj_detail_amount"+seq).val(parseInt(data['payment_amount']).toLocaleString('en-US'));
        }

        buttonSeq.val(parseInt(seq) + 1);
    }
}


function deleteRowTable(idTable, data, seq)
{
    if(idTable == 'table_pembayaran_haji_form') {
        if(parseInt(seq) == 1) {
            Swal.fire({
                icon    : 'info',
                title   : 'Terjadi Kesalahan',
                text    : 'Tidak Bisa Menghapus Baris Pertama'
            })
        } else {
            // CHECK DULU APAKAH SEQ ADA DI DATA?
            let currentSeq      = $("#btn_tambah_baris_ph").val();
            if(parseInt(currentSeq) - seq == 1) {
                // DELETE ROW
                $("#"+idTable).DataTable().row(parseInt(seq) - 1).remove().draw();
                $("#hj_detail_no"+ (parseInt(seq) - 1)).focus();
                $("#btn_tambah_baris_ph").val(parseInt(currentSeq) - 1);
            } else {
                Swal.fire({
                    icon    : 'info',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Hanya Baris Terakhir Yang Bisa Dihapus'
                })
            }
            
        }
    }
}

function doSimpanData(idForm, jenis)
{
    if(idForm == 'pembayaran_haji')
    {
        // HEADER
        let header = {
            'hj_trans_id'               : $("#hj_trans_id").val(),
            'hj_member_id'              : $("#hj_member_id").val(),
            'hj_member_name'            : $("#hj_member_id option:selected").text(),
            'hj_estimasi_keberangkatan' : $("#hj_estimasi_keberangkatan").val(),
            'hj_no_daftar'              : $("#hj_no_daftar").val(),
            'hj_tgl_daftar'             : $("#hj_tgl_daftar").val() != '' ? moment($("#hj_tgl_daftar").val(), 'DD/MM/YYYY').format('YYYY-MM-DD') : null,
            'hj_no_bpih'                : $("#hj_no_bpih").val(),
            'hj_depature_code'          : $("#hj_depature_code").val(),
            'hj_room'                   : $("#hj_room").val(),
        };

        let detail          = [];
        let detailTable     = $("#table_pembayaran_haji_form").DataTable().rows().count();

        for(let i = 0; i < detailTable; i++)
        {
            let detailSeq           = i + 1;
            let detailTglBayar      = !moment($("#hj_detail_tglBayar"+detailSeq).val(), 'DD/MM/YYYY') ? today : moment($("#hj_detail_tglBayar"+detailSeq).val(), 'DD/MM/YYYY').format('YYYY-MM-DD');
            let detailMetodeBayar   = $("#hj_detail_method"+detailSeq).val();
            let detailNoRekening    = $("#hj_detail_bank_acc"+detailSeq).val();
            let detailKurensi       = $("#hj_detail_curr"+detailSeq).val();
            let detailJmlBayar      = $("#hj_detail_amount"+detailSeq).val() == '' ? 0 : parseInt($("#hj_detail_amount"+detailSeq).val().replace(',', ''));

            detail.push({
                'seq'           : detailSeq,
                'tgl_bayar'     : detailTglBayar,
                'metode_bayar'  : detailMetodeBayar,
                'no_rekening'   : detailNoRekening,
                'kurensi'       : detailKurensi,
                'jml_bayar'     : detailJmlBayar,
            });
        }

        const simpanHajiURL     = "divisi/marketing/pembayaran/simpan_pembayaran_haji/" + jenis;
        const simpanHajiType    = "POST";
        const simpanHajiData    = {
            'header'    : header,
            'detail'    : detail,
        };
        const simpanHajiMsg     = Swal.fire({ title : "Data Sedang Diproses" }); Swal.showLoading();

        doTransaction(simpanHajiURL, simpanHajiType, simpanHajiData, simpanHajiMsg)
            .then((success)     => {
                showTable('table_pembayaran_haji', []);

                Swal.fire({
                    icon    : 'success',
                    title   : 'Berhasil',
                    text    : success.message,
                }).then((res)   => {
                    if(res.isConfirmed) {
                        closeModal('modal_pembayaran_haji');
                        // GET DATA TABLE
                        const bayarHajiURL  = "divisi/finance/pembayaran/haji/list_pembayaran_haji";
                        const bayarHajiType = "GET";

                        doTransaction(bayarHajiURL, bayarHajiType)
                            .then((isSuccess)   => {
                                const bayarHajiGetData  = isSuccess.data.length > 0 ? isSuccess.data : [];

                                showTable('table_pembayaran_haji', bayarHajiGetData);

                                if(bayarHajiGetData.length < 1) {
                                    $("#table_pembayaran_haji").find('.dataTables_empty').html(`Tidak Ada Data Pembayaran Haji`);
                                }
                            })
                            .catch((isError)    => {
                                showTable('table_pembayaran_haji', []);
                                $("#table_pembayaran_haji").find('.dataTables_empty').html(`Tidak Ada Data Pembayaran Haji`);
                            })
                    }
                })
            })
            .catch((error)      => {
                if(error.status == 522) {
                    const errMsg    = error.responseJSON.message;
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : 'Periksa Kembali Form Berwarna Merah',
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            $.each(errMsg, (i, item)    => {
                                $("#"+i).addClass('is-invalid');

                                $("#"+i).on('select2:opening', () => {
                                    $("#"+i).removeClass('is-invalid');
                                })
                            })
                        }
                    })
                } else {
                    Swal.close();
                    console.log(error);
                }
            })
    }
}

function doTransaction(url, type, data, message, processData = true, contentType = 'application/x-www-form-urlencoded; charset=UTF-8')
{
    return new Promise((resolve, reject)   => {
        $.ajax({
            cache   : false,
            url     : base_url + '/' + url,
            type    : type,
            processData : processData,
            contentType : contentType,
            data    : data,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN
            },
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