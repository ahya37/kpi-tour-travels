var today               = moment().format('YYYY-MM-DD');
var base_url            = window.location.origin;
var temp_pengajuan      = [];
var temp_bulan          = [];
var temp_haji           = [];
var temp_data_bank_account  = [];
var tahun_keberangkatan = [];
var temp_paket          = [];
var temp_tourCode       = [];
var temp_total_pembayaran_haji  = 0;
var temp_master_coa     = [];

var list_kategori_pengajuan     = [
    { 'value' : 'tiket', 'text' : 'Tiket' },
    { 'value' : 'fee_pembimbing', 'text' : 'Fee Pembimbing' },
    { 'value' : 'manasik', 'text' : 'Manasik' },
    { 'value' : 'la', 'text' : 'Biaya Land Arrangement' },
    { 'value' : 'tiket_museum', 'text' : 'Tiket Musemum'},
    { 'value' : 'perlengkapan_jemaah', 'text' : 'Perlengkapan Jemaah' },
    { 'value' : 'handling', 'text' : 'Handling' },
    { 'value' : 'akomodasi', 'text' : 'Akomodasi' },
    { 'value' : 'kereta_cepat', 'text' : 'Kereta Cepat'},
    { 'value' : 'lain_lain', 'text' : 'Lain-Lain' }
];

for(let i = 0; i < 11; i++) {
    let monthNumber     = moment(i + 1, 'M').format('MM');
    let monthName       = moment(i + 1, 'M').format('MMMM');

    temp_bulan.push({
        'bulan_ke'  : monthNumber,
        'bulan_nama': monthName,
    });
}

for(let i = 2000; i < parseInt(moment(today, 'YYYY-MM-DD').add(10, 'year').format('YYYY')); i++) {
    tahun_keberangkatan.push({
        'value' : i,
        'text'  : i,
    });
}

showSelect('filter_bulan', temp_bulan, moment(today, 'YYYY-MM-DD').format('MM'))

const isLoading   = (idForm) => {
    return $("#"+idForm).html(`<span class="spinner spinner-border"></span>`);
} 

temp_paket.push(
    { "paket" : "Semua" },
    { "paket" : "Double" },
    { "paket" : "Triple" },
    { "paket" : "Quad" },
);

$(document).ready(function(){
    // GET DATA PENGAJUAN KEUANGAN
    showDataDashboard(moment(today, 'YYYY-MM-DD').format('MM'));
});

function showModal(idModal, type, data = '')
{
    if(idModal == 'modal_pengajuan_keuangan') {
        let selectedBulan   = $("#filter_bulan").val();
        Swal.fire({
            title   : 'Data Sedang Dimuat..'
        });
        Swal.showLoading();

        setTimeout(()   => {
            Swal.close();
            $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            $("#title_bulan_modal_pengajuan_keuangan").html(`Bulan : ${moment(selectedBulan, 'MM').format('MMMM')}, ${moment(today, 'YYYY-MM-DD').format('YYYY')}`)
            showTable('table_pengajuan_keuangan', temp_pengajuan);
            if(data.length < 1) {
                $("#table_pengajuan_keuangan").find('.dataTables_empty').html(`Tidak Ada Data Pengajuan Keuangan`);
            }
        }, 1000);
    } else if(idModal == 'detail_modal_pengajuan_keuangan') {
        closeModal('modal_pengajuan_keuangan');

        // GET DATA PENGAJUAN KEUANGAN DETAIL
        const detailPengajuanURL    = "divisi/finance/pengajuan/keuangan_detail";
        const detailPengajuanType   = "GET";
        const detailPengajuanMsg    = Swal.fire({ title : "Data Sedang Diproses..", allowOutsideClick: false }); Swal.showLoading();
        const detailPengajuanData   = {
            'id'    : data,
        };

        doTransaction(detailPengajuanURL, detailPengajuanType, detailPengajuanData, detailPengajuanMsg)
            .then((success)     => {
                Swal.close();
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                
                const detailPengajuanHeader     = success.data.header;
                const detailPengajuanDetail     = success.data.detail;

                if(detailPengajuanHeader.length > 0 || detailPengajuanDetail.length > 0) {
                    // FILL FORM
                    // HEADER
                    const pengajuanNomor        = detailPengajuanHeader[0]['pengajuan_nomor'];
                    const pengajuanDeskripsi    = detailPengajuanHeader[0]['pengajuan_deskripsi'];
                    const pengajuanTanggal      = detailPengajuanHeader[0]['pengajuan_tanggal'];
                    const pengajuanTotal        = detailPengajuanHeader[0]['pengajuan_total'];
                    const pengajuanMataUang     = detailPengajuanHeader[0]['pengajuan_mata_uang'];
                    const pengajuanMetode       = detailPengajuanHeader[0]['pengajuan_metode'] == "" ? "TRANSFER" : detailPengajuanHeader[0]['pengajuan_metode'];
                    const pengajuanMetodeTujuan = detailPengajuanHeader[0]['pengajuan_rekening'];
                    const pengajuanFile         = detailPengajuanHeader[0]['pengajuan_file'];
                    let pengajuanCurrency;

                    if(pengajuanMataUang == 'DOLLAR') {
                        pengajuanCurrenfy   = '$';
                    } else {
                        pengajuanCurrency   = 'Rp.'
                    }
                    
                    $("#pgj_no_surat").val(pengajuanNomor);
                    $("#pgj_deskripsi").val(pengajuanDeskripsi);
                    $("#pgj_tgl_aju").val(moment(pengajuanTanggal, 'YYYY-MM-DD').format('DD MMM YYYY'));
                    $("#pgj_total_uang_kurs").html(pengajuanCurrency);
                    $("#pgj_total_uang").val(parseInt(pengajuanTotal).toLocaleString('id-ID'));
                    $("#pgj_metode").val(pengajuanMetode);
                    $("#pgj_no_rekening").val(pengajuanMetodeTujuan);
                    $("#pgj_file_list").val(pengajuanFile);
                    $("#pgj_umhaj_id").val(data);

                    if(pengajuanFile === null) {
                        $("#pgj_file").html(`<li>Tidak Ada File</li>`)
                    } else {
                        let html = "";
                        if(pengajuanFile.includes('^') === true) {
                            let split   = pengajuanFile.split('^');
                            for(let i = 0; i < split.length; i++) {
                                html    += `<li><a href="http://umhaj.perciktours.com/${split[i]}" target="_blank">File ${i + 1}</li>`;
                            }
                            $("#pgj_file").html(html);
                        } else {
                            html    = `<li><a href="https://umhaj.perciktours.com/${pengajuanFile}" target="_blank">File 1</li>`
                        }
                        $("#pgj_file").html(html);
                    }

                    showSelect('pgj_tr_tour_code', temp_tourCode[0]['header'], '');
                    showSelect('pgj_tr_kredit', temp_master_coa[0]);
                    showSelect('pgj_tr_debit', temp_master_coa[0]);
                    showSelect('pgj_tr_category', list_kategori_pengajuan);
                    showTable('table_detail_pengajuan_keuangan', detailPengajuanDetail);

                    // KEYUP EVENT
                    $("#pgj_tr_debit_amount").on('keyup', () => {
                        let replaceValue    = $("#pgj_tr_debit_amount").val().replace(/[^0-9]/g, '');
                        let debitAmount     = parseInt(replaceValue);

                        isNaN(debitAmount) ? $("#pgj_tr_debit_amount").val('') : $("#pgj_tr_debit_amount").val(debitAmount.toLocaleString('id-ID'));
                    });

                    $("#pgj_tr_kredit_amount").on('keyup', () => {
                        let replaceValue    = $("#pgj_tr_kredit_amount").val().replace(/[^0-9]/g, '');
                        let kreditAmount    = parseInt(replaceValue);

                        isNaN(kreditAmount) ? $("#pgj_tr_kredit_amount").val('') : $("#pgj_tr_kredit_amount").val(kreditAmount.toLocaleString('id-ID'));
                    })
                }
            })
            .catch((error)      => {
                console.log(error);
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : error.responseJSON.message,
                    didClose    : () => {
                        showModal('modal_pengajuan_keuangan', 'list', '');
                    }
                })
            })
    } else if(idModal == 'modal_pembayaran_haji') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

        showTable('table_pembayaran_haji', []);
        showSelect('filter_keberangkatan', tahun_keberangkatan, moment(today, 'YYYY-MM-DD').format('YYYY'));
        showSelect('filter_paket', temp_paket, "Semua");

        // GET DATA PEMBAYARAN HAJI
        const paymentHajiURL    = "divisi/finance/pembayaran/haji/list_pembayaran_haji";
        const paymentHajiType   = "GET";
        const paymentHajiData   = {};
        const paymentHajiMsg    = "";

        doTransaction(paymentHajiURL, paymentHajiType, paymentHajiData, paymentHajiMsg)
            .then((success)     => {
                const paymentHajiGetData    = success.data;
                showTable('table_pembayaran_haji', paymentHajiGetData);

                if(paymentHajiGetData.length < 1) {
                    $("#table_pembayaran_haji").find('.dataTables_empty').html(success.message);
                }
            })
            .catch((error)      => {
                showTable('table_pembayaran_haji', []);
                $("#table_pembayaran_haji").find('.dataTables_empty').html(error.responseJSON.message);
            })
    } else if(idModal == 'modal_pembayaran_haji_form') {
        closeModal('modal_pembayaran_haji');

        if(data == '') {
            $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            showSelect('hj_member_id', []);
            showSelect('hj_depature_code', []);
            showSelect('hj_estimasi_keberangkatan', tahun_keberangkatan, moment(today, 'YYYY-MM-DD').format('YYYY'));
            showTable('table_pembayaran_haji_form', []);
        } else {
            $(".is_not_empty_data").removeClass('d-none');
            $(".is_empty_data").addClass('d-none');
            
            const paymentHajiURL    = "divisi/finance/pembayaran/haji/pembayaran_detail_jemaah";
            const paymentHajiType   = "GET";
            const paymentHajiData   = {
                'trans_id'  : data,
            };
            const paymentHajiMsg    = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

            doTransaction(paymentHajiURL, paymentHajiType, paymentHajiData, paymentHajiMsg)
                .then((success)     => {
                    Swal.close();
                    $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                    
                    const paymentHajiHeader     = success.data.header;
                    const paymentHajiDetail     = success.data.detail;

                    // FILL FORM
                    $("#hj_trans_id").val(data);
                    $("#hj_member_name_edit").val(paymentHajiHeader['jemaah_name']);
                    $("#hj_member_id_edit").val(paymentHajiHeader['jemaah_id']);
                    $("#hj_depature_code_edit").val(paymentHajiHeader['tour_code']);
                    $("#hj_no_daftar").val(paymentHajiHeader['no_daftar']);
                    $("#hj_tgl_daftar").val(paymentHajiHeader['tgl_daftar']);
                    $("#hj_no_bpih").val(paymentHajiHeader['no_bpih']);
                    $("#hj_room").val(paymentHajiHeader['jemaah_pkg']);
                    $("#hj_room_price").val(paymentHajiHeader['jemaah_harga_paket'].toLocaleString('en-US'));
                    $("#hj_current_payment").val(parseInt(paymentHajiHeader['jemaah_total_bayar']).toLocaleString('en-US'));
                    $("#hj_status_payment").val(paymentHajiHeader['jemaah_status_bayar']);

                    showSelect('hj_estimasi_keberangkatan', tahun_keberangkatan, paymentHajiHeader['estimasi_keberangkatan']);

                    showTable('table_pembayaran_haji_form', paymentHajiDetail);
                })
                .catch((error)      => {
                    console.log(error);
                })

        }

        $("#btn_simpan_pembayaran_haji").val(type);
    } else if(idModal == 'modal_report_pembayaran_haji') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

        showSelect('report_pb_hj_year', tahun_keberangkatan, moment(today, 'YYYY-MM-DD').format('YYYY'));
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_pengajuan_keuangan') {
        $("#"+idModal).modal('hide');
    } else if(idModal == 'detail_modal_pengajuan_keuangan') {
        $("#"+idModal).modal('hide');
        
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#pgj_no_surat").val('');
            $("#pgj_deskripsi").val('');
            $("#pgj_tgl_aju").val('');
            $("#pgj_total_uang_kurs").html();
            $("#pgj_total_uang").val('');
            $("#pgj_metode").val('');
            $("#pgj_no_rekening").val('');
            $("#pgj_umhaj_id").val('');

            $("#pgj_tr_debit_amount").val(0);
            $("#pgj_tr_kredit_amount").val(0);

            // HIDE FORM
            $("#pgj_tr_category_view").addClass('d-none');
            $("#pgj_tr_debit_amount_view").addClass('d-none');
            $("#pgj_tr_kredit_amount_view").addClass('d-none');
        });

        showModal('modal_pengajuan_keuangan', 'list', '');
    } else if(idModal == 'modal_pembayaran_haji') {
        $("#"+idModal).modal('hide');
    } else if (idModal == 'modal_pembayaran_haji_form') {
        $("#"+idModal).modal('hide');
        showModal('modal_pembayaran_haji', '', []);
        $("#btn_tambah_baris_haji").val(1);

        $("#"+idModal).on('hidden.bs.modal', () => {

            // HIDE FORM
            $(".is_empty_data").removeClass('d-none');
            $(".is_not_empty_data").addClass('d-none');
            $("#hj_member_id_edit").val(null);
            $("#hj_member_name_edit").val(null);
            $("#hj_depature_code_edit").val(null);

            $("#hj_member_id").val('');
            $("#hj_no_daftar").val(null);
            $("#hj_tgl_daftar").val(null);
            $("#hj_no_bpih").val(null);
            $("#hj_room").val(null);
            $("#hj_room_price").val(null);
            $("#hj_status_payment").val(null);
            $("#hj_current_payment").val(null);
        })
    } else if(idModal == 'modal_report_pembayaran_haji') {
        $("#"+idModal).modal('hide');
    }
}

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();

    if(idTable == 'table_pengajuan_keuangan') {
        $("#"+idTable).DataTable({
            language    : {
                'emptyTable'    : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat..`,
                'zeroRecords'   : `Data Yang Dicari Tidak Ditemukan`
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "10%" },
                { "targets" : [2], "className" : "text-left align-middle", "width" : "15%" },
                { "targets" : [3], "className" : "text-left align-middle" },
                { "targets" : [4], "className" : "text-left align-middle", "width" : "15%" },
                { "targets" : [5], "className" : "text-center align-middle", "width" : "8%" },
            ],
        })

        if(data.length > 0) {
            let seq = 1;
            for(const item of data[0])
            {
                let no_aju                  = item['pengajuan_id'];
                let aju_tgl                 = item['pengajuan_tanggal'];
                let aju_nama                = item['pengaju_nama'];
                let aju_deskripsi           = item['pengajuan_deskripsi'];
                let aju_deskripsi_short     = aju_deskripsi.length > 55 ? aju_deskripsi.substring(0, 55) + ' ...' : aju_deskripsi;
                let aju_jml_uang            = item['total_pengajuan'];
                let aju_mata_uang           = item['pengajuan_mata_uang'];
                let aju_preview             = `<button class="btn btn-success btn-sm" type="button" value="${no_aju}" title="Lihat Detail" onclick="showModal('detail_modal_pengajuan_keuangan', 'view', this.value)"><i class="fa fa-eye"></i></button>`

                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${seq++}</label>`,
                    `<label class="font-weight-normal no-margins">${moment(aju_tgl, 'YYYY-MM-DD').format('DD MMM YYYY')}</label>`,
                    `<label class="font-weight-normal no-margins">${aju_nama}</label>`,
                    `<label class="font-weight-normal no-margins" title="${aju_deskripsi}">${aju_deskripsi_short}</label>`,
                    `<label class="font-weight-normal no-margins">${rupiahFormatter(aju_jml_uang, 0, 0, aju_mata_uang)}</label>`,
                    aju_preview
                ]).draw(false);
            }
        }        
    } else if(idTable == 'table_detail_pengajuan_keuangan') {
        $("#"+idTable).DataTable({
            language    : {
                'emptyTable'    : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat..`,
                'zeroRecords'   : `Data Yang Dicari Tidak Ditemukan`
            },
            autoWidth   : false,
            pageLength  : -1,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "text-left align-middle"},
                { "targets" : [2], "className" : "text-left align-middle", "width" : "15%" },
                { "targets" : [3], "className" : "text-right align-middle", "width" : "20%" },
            ],
            paging      : false,
            bInfo       : false,
        })

        if(data.length > 0) {
            let seq = 1;
            let grandTotal = 0;
            for(const item of data) {
                const detailSeq     = seq++;
                const detailDesc    = item['pengajuan_detail_deskripsi'];
                const detailCurr    = item['pengajuan_detail_currency'];
                const detailJumlah  = item['pengajuan_detail_jumlah'];

                // INPUT FORM
                let inputSeq    = `<input type='hidden' value="${detailSeq}" id="pgj_seq${detailSeq}">`;
                let inputDesc   = `<input type='hidden' value="${detailDesc}" id="pgj_description${detailSeq}">`;
                let inputCurr   = `<input type='hidden' value="${detailCurr}" id="pgj_currency${detailSeq}">`;
                let inputAmount = `<input type='hidden' value='${detailJumlah}' id="pgj_amount${detailSeq}">`
                grandTotal += parseInt(detailJumlah);

                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${detailSeq}</label> ${inputSeq}`,
                    `<label class="font-weight-normal no-margins">${detailDesc}</label> ${inputDesc}`,
                    `<label class="font-weight-normal no-margins">${detailCurr}</label> ${inputCurr}`,
                    `<label class="font-weight-normal no-margins">${parseInt(detailJumlah).toLocaleString('id-ID')}</label> ${inputAmount}`
                ]).draw(false);
            }

            $("#total_pengajuan_keuangan").html(`<label class="no-margins">${parseInt(grandTotal).toLocaleString('id-ID')}</label>`);
        }
    } else if(idTable == 'table_pembayaran_haji') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat..`,
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "align-middle" },
                { "targets" : [2], "className" : "align-middle", "width" : "10%" },
                { "targets" : [3], "className" : "text-center align-middle", "width" : "10%" },
                { "targets" : [4, 5], "className" : "text-center align-middle", "width" : "10%" },
            ],
        })
        
        if(data.length > 0) {
            for(let i = 0; i < data.length; i++) {

                let tourPrice   = 0;
                switch(data[i]['jemaah_pkg']) {
                    case 'Double' :
                        tourPrice   = 20000;
                    break;
                    case 'Triple' :
                        tourPrice   = 18500;
                    break;
                    case 'Quad' :
                        tourPrice   = 17500;
                    break;
                }

                let seq                 = i + 1;
                let transID             = data[i]['trans_id'];
                let jemaahNama          = data[i]['jemaah_name'];
                let jemaahPaket         = data[i]['jemaah_pkg'];
                let jemaahTotalBayar    = data[i]['total_payment'];
                let jemaahStatusBayar   = parseInt(jemaahTotalBayar) - tourPrice == 0 ? `<span class="badge badge-sm badge-primary">Lunas</span>` : (parseInt(jemaahTotalBayar) - tourPrice < 0 ? `<span class="badge badge-sm badge-warning" style="color: #000">Kurang Bayar</span>` : `<span class="badge badge-sm badge-primary">Lebih Bayar</span>`);
                let btnJemaahAct        = `<button class="btn btn-sm btn-primary" value="${transID}" title="Lihat Data" onclick="showModal('modal_pembayaran_haji_form', 'edit', this.value)"><i class="fa fa-eye"></i></button>`;
                let estimasi_berangkat  = data[i]['est_berangkat'];
                $("#"+idTable).DataTable().row.add([
                    `<label class="no-margins font-weight-normal">${seq}</label>`,
                    `<label class="no-margins font-weight-normal">${jemaahNama}</label>`,
                    `<label class="no-margins font-weight-normal">${jemaahPaket}</label>`,
                    `<label class="no-margins font-weight-normal">${estimasi_berangkat}</label>`,
                    `<label class="no-margins font-weight-normal">${jemaahStatusBayar}</label>`,
                    `<label class="no-margins font-weight-normal">${btnJemaahAct}</label>`,
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
            // LOOP DATA LALU ADD ROW
            let seq = 1;
            for(let i = 0; i < data.length; i++) {
                addRowTable('table_pembayaran_haji_form', data[i], seq++);
            }
        } else {
            let currentSeq  = $("#btn_tambah_baris_haji").val();
            addRowTable('table_pembayaran_haji_form', [], parseInt(currentSeq));
        }
    }

    $("#"+idTable+"_wrapper").css('padding-bottom', '0px');
}

function addRowTable(idTable, data, seq)
{
    if(idTable == 'table_pembayaran_haji_form') {
        let buttonSeq       = $("#btn_tambah_baris_haji");
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
            console.log(data['payment_method'])
            if(data['payment_method'] == 'cash') {
                $("#hj_detail_bank_acc"+seq).prop('disabled', true);
                showSelect('hj_detail_bank_acc', [], bankAccountID, seq);
            } else {
                $("#hj_detail_bank_acc"+seq).prop('disabled', false);
                showSelect('hj_detail_bank_acc', temp_data_bank_account, bankAccountID, seq);
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
            let currentSeq      = $("#btn_tambah_baris_haji").val();
            if(parseInt(currentSeq) - seq == 1) {
                // DELETE ROW
                $("#"+idTable).DataTable().row(parseInt(seq) - 1).remove().draw();
                $("#hj_detail_no"+ (parseInt(seq) - 1)).focus();
                $("#btn_tambah_baris_haji").val(parseInt(currentSeq) - 1);
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

function showSelect(idSelect, data, selectedData = '', seq = '')
{
    $("#"+idSelect+""+seq).select2({
        theme   : 'bootstrap4'
    });
    if (idSelect == 'filter_bulan') {
        
        $("#"+idSelect).select2({
            theme   : 'bootstrap4',
            minimumResultsForSearch : -1,
        })

        let html    = `<option selected disabled>Pilih Bulan</option>`;

        $.each(data, (i, item)  => {
            html    += `<option value="${item['bulan_ke']}">${item['bulan_nama']}</option>`
        });

        $("#"+idSelect).html(html);
        $("#"+idSelect).val(selectedData);
    } else if(idSelect == 'hj_member_id') {
        $("#"+idSelect).select2({
            theme   : 'bootstrap4',
            placeholder     : `Pilih Nama Jemaah`,
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
        });        
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

    } else if(idSelect == 'hj_detail_method') {
        $("#"+idSelect+""+seq).select2({
            theme   : 'bootstrap4',
            minimumResultsForSearch     : -1,
        })

        let html    = `<option selected disabled>Metode Bayar</option>`;

        $.each(data, (i, item)  => {
            html    += `<option value="${item['id']}">${item['text']}</option>`
        });

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
        let html    = `<option selected disabled>No. Rekening</option>`;

        if(data.length > 0 ) {
            $.each(data[0], (i, item)  => {
                html    += `<option value="${item['account_id']}">${item['account_bank_name']} - ${item['account_number']}</option>`
            })
        }

        $("#"+idSelect+""+seq).html(html);

        if(selectedData != '') {
            $("#"+idSelect+""+seq).val(selectedData);
        }
    } else if(idSelect == 'hj_estimasi_keberangkatan') {
        let html    = [
            `<option selected disabled>Pilih Tahun Keberangkatan</option>`
        ];

        for(let i = 0; i < data.length; i++) {
            html    += `<option value="${data[i]['value']}">${data[i]['text']}</option>`
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'filter_keberangkatan') {
        let html    = [
            `<option selected disabled>Pilih Tahun Keberangkatan</option>`,
            `<option value='9999'>Belum Tersedia</option>`,
        ];

        for(let i = 0; i < data.length; i++) {
            html    += `<option value="${data[i]['value']}">${data[i]['text']}</option>`
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'filter_paket') {
        let html    = `<option selected disabled>Pilih Paket</option>`;

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value="${item['paket']}">${item['paket']}</option>`
            });
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'report_pb_hj_year') {
        let html    = `<option selected disabled>Pilih Tahun</option>`;

        // SORT
        data.sort((a, b)    => {
            return new Date(b['value']) - new Date(a['value']);
        })
        
        $.each(data, (i, item)  => {
            html    += `<option value="${item['value']}">${item['text']}</option>`
        });

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'pgj_tr_tour_code') {
        $("#"+idSelect).select2({
            theme   : 'bootstrap4',
            tags    : false,
            dropdownParent  : $("#detail_modal_pengajuan_keuangan"),
        });

        let html    = [
            `<option selected disabled>Pilih Tour Code</option>`
        ];

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value="${item['tour_code']}">${item['tour_code']}</option>`
            });
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'pgj_tr_debit') {
        $("#"+idSelect).select2({
            theme   : 'bootstrap4',
            tags    : false,
            dropdownParent  : $("#detail_modal_pengajuan_keuangan"),
        });

        let html    = `<option selected disabled>Pilih COA Debit</option>`;

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value="${item['coa_id']}">${item['coa_id']} | ${item['coa_desc']}</option>`
            })
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData)
        }
    } else  if(idSelect == 'pgj_tr_kredit') {
        $("#"+idSelect).select2({
            theme   : 'bootstrap4',
            tags    : false,
            dropdownParent  : $("#detail_modal_pengajuan_keuangan"),
        });

        let html    = `<option selected disabled>Pilih COA Kredit</option>`;

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value="${item['coa_id']}">${item['coa_id']} | ${item['coa_desc']}</option>`
            })
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData)
        }
    } else if(idSelect == 'pgj_tr_category') {
        $("#"+idSelect).select2({
            theme   : 'bootstrap4',
            dropdownParent  : $("#detail_modal_pengajuan_keuangan")
        });

        let html    = `<option selected disabled>Pilih Kategori</option>`;
        
        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value="${item['value']}">${item['text']}</option>`;
            });
        }

        $("#"+idSelect).html(html);
    }
}

function showSelectDetail(idSelect, data, seq = '')
{
    if(idSelect == 'hj_member_id') {
        // GET DATA NO DAFTAR HAJI
        const hajiCodeURL   = "divisi/finance/pembayaran/haji/detail_jemaah";
        const hajiCodeType  = "GET";
        const hajiCodeData  = {
            "member_id" : data,
            "haji_kode" : "semua",
        };
        const hajiCodeMsg   = "";

        doTransaction(hajiCodeURL, hajiCodeType, hajiCodeData, hajiCodeMsg)
            .then((success)     => {
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
                console.log(error);
                // Swal.fire({
                //     icon    : 'error',
                //     title   : 'Terjadi Kesalahan',
                //     text    : error.responseJSON.message,
                // })
            })
    } else if(idSelect == 'hj_depature_code') {
        if(data != '-') {
            const hajiCodeURL   = "divisi/finance/pembayaran/haji/detail_jemaah";
            const hajiCodeType  = "GET";
            const hajiCodeData  = {
                "member_id" : $("#hj_member_id").val(),
                "haji_kode" : data,
            };
            const hajiCodeMsg   = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

            doTransaction(hajiCodeURL, hajiCodeType, hajiCodeData, hajiCodeMsg)
                .then((success)     => {
                    Swal.close();
                    // FILL FORM
                    const hajiCodeGetData   = success.data[0];
                    
                    $("#hj_no_daftar").val(hajiCodeGetData.no_daftar);
                    $("#hj_tgl_daftar").val(hajiCodeGetData.tgl_keberangkatan);
                    $("#hj_no_bpih").val(hajiCodeGetData.no_bpih);
                    $("#hj_room").val(hajiCodeGetData.haji_paket);
                    $("#hj_room_price").val(parseInt(hajiCodeGetData.haji_harga).toLocaleString('en-US'));
                    $("#hj_current_payment").val();
                    $("#hj_status_payment").val();
                })
                .catch((error)      => {
                    Swal.close();
                    console.log(error);
                })
        } else {
            $("#hj_no_daftar").val('-');
            $("#hj_tgl_daftar").val(moment(today, 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#hj_no_bpih").val('-');
            $("#hj_room").val('-');
            $("#hj_room_price").val(0);
            $("#hj_current_payment").val();
            $("#hj_status_payment").val();
        }
    } else if(idSelect == 'hj_detail_method') {
        if(data == 'tf') {
            $("#hj_detail_bank_acc"+seq).prop('disabled', false);
            showSelect('hj_detail_bank_acc', temp_data_bank_account, '', seq);
        } else {
            $("#hj_detail_bank_acc"+seq).prop('disabled', true);
            showSelect('hj_detail_bank_acc', [], '', seq);
        }
    }
}

function showDataDashboard(selectedMonth)
{
    // SHOW DEFAULT LOADING
    isLoading('dashboard_pengajuan_keuangan');
    isLoading('dashboard_pembayaran_umrah');
    isLoading('dashboard_pembayaran_haji');
    $("#title_bulan_saldo_awal").html(moment(selectedMonth, 'MM').format('MMMM'));
    $("#title_bulan_debit").html(moment(selectedMonth, 'MM').format('MMMM'));
    $("#title_bulan_kredit").html(moment(selectedMonth, 'MM').format('MMMM'));

    temp_pengajuan  = [];

    const pengajuanURL  = "divisi/finance/pengajuan/keuangan";
    const pengajuanType = "GET";
    const pengajuanData = {
        'selected_month'    : selectedMonth,
        'selected_year'     : moment(today, 'YYYY-MM-DD').format('YYYY'),
    };
    const pengajuanMsg  = "";

    const hajiURL       = "divisi/finance/pembayaran/haji/list_pembayaran_haji";
    const hajiType      = "GET";
    const hajiData      = [];
    const hajiMsg       = [];
    
    const bankAccountURL    = "divisi/finance/master/bank/list_bank_account";
    const bankAccountType   = "GET";
    const bankAccountData   = [];
    const bankAccountMsg    = "";

    const tourCodeURL       = "divisi/finance/master/tour_code/list_tour_code/semua";
    const tourCodeType      = "GET";
    const tourCodeData      = [];
    const tourCodeMsg       = "";

    const coaListURL        = "divisi/finance/master/coa/list";
    const coaListData       = {
        'coa_id'    : 'semua'
    };
    const coaListType       = "GET";
    
    // COLLECTIVE GET DATA
    const collectApi    = [
        doTransaction(pengajuanURL, pengajuanType, pengajuanData, pengajuanMsg),
        temp_total_pembayaran_haji == 0 ? doTransaction(hajiURL, hajiType, hajiData, hajiMsg) : '',
        temp_data_bank_account.length < 1 ? doTransaction(bankAccountURL, bankAccountType, bankAccountData, bankAccountMsg) : '',
        temp_tourCode.length < 1 ? doTransaction(tourCodeURL, tourCodeType, tourCodeData, tourCodeMsg) : '',
        temp_master_coa.length < 1 ? doTransaction(coaListURL ,coaListType, coaListData, '') : '',
    ];

    Promise.allSettled(collectApi)
        .then((success)     => {
            // PENGAJUAN SECTION
            const pengajuanGetData  = success[0].status == 'fulfilled' ? success[0].value.data : [];
            if(temp_pengajuan.length < 1 && pengajuanGetData.length > 0) {
                temp_pengajuan.push(pengajuanGetData);
            }
            $("#dashboard_pengajuan_keuangan").html(`<h2 class="no-margins">${pengajuanGetData.length}</h2>`);

            // PEMBAYARAN JEMAAH
            $("#dashboard_pembayaran_umrah").html(`<h2 class="no-margins">0</h2>`);

            if(temp_total_pembayaran_haji == 0) {
                const hajiGetData   = success[1].status == 'fulfilled' ? success[1].value.data : [];
                temp_total_pembayaran_haji  = hajiGetData.length;
            }
            $("#dashboard_pembayaran_haji").html(`<h2 class="no-margins">${temp_total_pembayaran_haji}</h2>`);

            // GET BANK ACCOUNT
            if(temp_data_bank_account.length < 1) {
                const bankAccountGetData    = success[2].status == 'fulfilled' ? success[2].value.data.data : [];
                if(bankAccountGetData.length > 0) {
                    temp_data_bank_account.push(bankAccountGetData);
                }
            }

            // GET TOUR CODE
            if(temp_tourCode.length < 1) {
                const tourCodeGetData   = success[3].status == 'fulfilled' ? success[3].value.data : [];
                if(tourCodeGetData['header'].length > 0) {
                    temp_tourCode.push(tourCodeGetData);
                }
            }

            // GET COA
            if(temp_master_coa.length < 1) {
                const coaListGetData    = success[4].status == 'fulfilled' ? success[4].value.data : [];
                if(coaListGetData.length > 0) {
                    const coaData   = coaListGetData.filter((item)    => {
                        return item['coa_level'] == 3
                    });
                    temp_master_coa.push(coaData);
                }
            }
        })
        .catch((error)      => {
            console.log(error);
        })
}

function simpanData(idForm, type = '', data = [])
{
    if(idForm == 'modal_pembayaran_haji') {
        // GET DATA FORM
        let hajiDetail  = [];
        let hajiHeader  = {
            'trans_id'      : $("#hj_trans_id").val(),
            'jemaah_id'     : $("#hj_member_id").val(),
            'jemaah_nama'   : $("#hj_member_id option:selected").text(),
            'tour_code'     : $("#hj_depature_code").val(),
            'no_daftar'     : $("#hj_no_daftar").val(),
            'tgl_daftar'    : $("#hj_tgl_daftar").val() != '' ? moment($("#hj_tgl_daftar").val(), 'DD/MM/YYYY').format('YYYY-MM-DD') : $("#hj_tgl_daftar").val(),
            'no_bpih'       : $("#hj_no_bpih").val(),
            'paket'         : $("#hj_room").val(),
            'estimasi_berangkat'    : $("#hj_estimasi_keberangkatan").val(),
        };

        const hajiDetailTable   = $("#table_pembayaran_haji_form").DataTable().rows().count();
        for(let i = 0; i < hajiDetailTable; i++) {
            let seq     = i + 1;
            hajiDetail.push({
                'seq'           : $("#hj_detail_no"+seq).val(),
                'tgl_bayar'     : moment($("#hj_detail_tglBayar"+seq).val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                'metode_bayar'  : $("#hj_detail_method"+seq).val(),
                'no_rekening'   : $("#hj_detail_bank_acc"+seq).val(),
                'mata_uang'     : $("#hj_detail_curr"+seq).val(),
                'jml_bayar'     : parseInt($("#hj_detail_amount"+seq).val().replace(',', '')),
            })
        }

        const bayarHajiURL  = "divisi/finance/pembayaran/haji/simpan_haji/"+type;
        const bayarHajiType = "POST";
        const bayarHajiData = {
            'header'    : hajiHeader,
            'detail'    : hajiDetail,
        };
        const bayarHajiMsg  = Swal.fire({ title : "Data Sedang Diproses.." }); Swal.showLoading();

        doTransaction(bayarHajiURL, bayarHajiType, bayarHajiData, bayarHajiMsg)
            .then((success)     => {
                Swal.fire({
                    icon    : 'success',
                    title   : 'Berhasil',
                    text    : success.message,
                }).then((res)   => {
                    if(res.isConfirmed) {
                        closeModal('modal_pembayaran_haji_form');
                    }
                })
            })
            .catch((error)      => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Ada Data Yang Harus Diisi',
                }).then((res)   => {
                    if(res.isConfirmed) {
                        const errMsg    = error.responseJSON.message;
                        $.each(errMsg, (i, item)    => {
                            if(i == 'jemaah_id') {
                                $("#hj_member_id").addClass('is-invalid');

                                $("#hj_member_id").on('select2:opening', () => {
                                    $("#hj_member_id").removeClass('is-invalid');
                                })
                            } else if(i == 'tour_code') {
                                $("#hj_depature_code").addClass('is-invalid');
                                
                                $("#hj_depature_code").on('select2:opening', () => {
                                    $("#hj_depature_code").removeClass('is-invalid');
                                })
                            }
                        })
                    }
                })
            })
    } else if(idForm == 'detail_modal_pengajuan_keuangan') {
        let pgj_detail  = [];
        let pgj_header  = {
            'pgj_no_surat'          : $("#pgj_no_surat").val(),
            'pgj_umhaj_id'          : $("#pgj_umhaj_id").val(),
            'pgj_tgl_aju'           : moment($("#pgj_tgl_aju").val(), 'DD MMM YYYY').format('YYYY-MM-DD'),
            'pgj_total_uang'        : $("#pgj_total_uang").val().replace('.', ''),
            'pgj_total_uang_kurs'   : $("#pgj_total_uang_kurs").text() == 'Rp.' ? 'RUPIAH' : 'DOLLAR',
            'pgj_file_list'         : $("#pgj_file_list").val(),
            'pgj_deskripsi'         : $("#pgj_deskripsi").val(),
            'pgj_metode'            : $("#pgj_metode").val(),
            'pgj_no_rekening'       : $("#pgj_no_rekening").val(),
            'pgj_tr_tour_code'      : $("#pgj_tr_tour_code").val(),
            'pgj_tr_category'       : $("#pgj_tr_category").val(),
            'pgj_tr_debit'          : $("#pgj_tr_debit").val(),
            'pgj_tr_debit_amount'   : $("#pgj_tr_debit_amount").val() == '' ? 0 : parseInt($("#pgj_tr_debit_amount").val().replace('.', '')),
            'pgj_tr_kredit'         : $("#pgj_tr_kredit").val(),
            'pgj_tr_kredit_amount'  : $("#pgj_tr_kredit_amount").val() == '' ? 0 : parseInt($("#pgj_tr_kredit_amount").val().replace('.', '')),
        };

        let detail  = $("#table_detail_pengajuan_keuangan").DataTable().rows().count();

        for(let i = 0; i < detail; i++) {
            let ke  = i + 1;
            pgj_detail.push({
                'pgj_seq'           : $("#pgj_seq"+ke).val(),
                'pgj_description'   : $("#pgj_description"+ke).val(),
                'pgj_currency'      : $("#pgj_currency"+ke).val(),
                'pgj_amount'        : $("#pgj_amount"+ke).val(),
            })
        }

        const pgjURL    = "divisi/finance/pengajuan/simpan_keuangan";
        const pgjType   = "POST";
        const pgjData   = {
            'header'    : pgj_header,
            'detail'    : pgj_detail,
        };
        const pgjMessage = Swal.fire({ title : "Data Sedang Diproses.." }); Swal.showLoading();
        
        doTransaction(pgjURL, pgjType, pgjData, pgjMessage)
            .then((success)     => {
                Swal.fire({
                    icon    : 'success',
                    title   : 'Berhasil',
                    text    : success.message
                }).then((res)   => {
                    if(res.isConfirmed) {
                        closeModal('detail_modal_pengajuan_keuangan');
                    }
                })
            })
            .catch((error)      => {
                console.log(error);
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : error.responseJSON.message
                });
            })
    }
}

function downloadFile(jenis, fileFormat)
{
    if(jenis == 'haji')
    {
        // VARIABLE
        let tahunCari   = $("#report_pb_hj_year").val();

        // FUNC
        const reportHajiURL     = "divisi/finance/report/pembayaran_haji/"+fileFormat;
        const reportHajiType    = "POST";
        const reportHajiData    = {
            'tahun_cari'    : tahunCari,
        };
        const reportHajiMsg     = Swal.fire({ title : "Download File" }); Swal.showLoading();

        doTransaction(reportHajiURL, reportHajiType, reportHajiData, reportHajiMsg)
            .then((success)     => {
                Swal.fire({
                    icon    : 'success',
                    title   : 'Berhasil',
                    text    : 'Klik `OK` Untuk Download File',
                }).then((res)   => {
                    if(res.isConfirmed) {
                        window.open(base_url + '/' + success.data.data_url);

                        setTimeout(()   => {
                            const deleteReportHajiURL   = "divisi/finance/report/delete_pembayaran_haji";
                            const deleteReportHajiType  = "POST";
                            const deleteReportHajiData  = {
                                'file_path' : success.data.data_url
                            };
                            doTransaction(deleteReportHajiURL, deleteReportHajiType, deleteReportHajiData, '')
                                .then((isSuccess)   => {
                                    console.log(isSuccess);
                                })
                                .catch((isError)    => {
                                    console.log(isError);
                                })
                        }, 5000);
                    }
                })
            })
            .catch((error)      => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Gagal Download File'
                })
            })
    }
}

function rupiahFormatter(value, maxDigit = 2, minDigit = 2, currency) {
    if(value) {
        let country;
        let curr;
        if(currency == 'RUPIAH') {
            country     = 'id-ID';
            curr        = 'IDR'
        } else if(currency == 'DOLLAR') {
            country     = 'en-US';
            curr        = 'USD';
        } else if(currency == 'RIYAL') {
            country     = 'ar-SA';
            curr        = 'SAR'
        } else {
            country     = 'id-ID';
            curr        = 'IDR';   
        }
        let format  = new Intl.NumberFormat(country, {style: 'currency', currency: curr, maximumFractionDigits: maxDigit, minimumFractionDigits: minDigit}).format(value);
        return format;
    } else {
        let format  = 'Rp. 0.00';
        return format;
    }
}

function doTransaction(url, type, data, message = '', processData = true, contentType = 'application/x-www-form-urlencoded; charset=UTF-8')
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            url     : base_url + '/' + url,
            type    : type,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            cache   : false,
            data    : data,
            processData     : processData,
            contentType     : contentType,
            beforeSend      : () => {
                message;
            },
            success         : (success) => {
                resolve(success);
            },
            error           : (error)   => {
                reject(error);
            }
        })
    })
}

function showForm(idForm)
{
    $("#"+idForm).removeClass('d-none');
}

function clearUrl()
{
    let url     = window.location.href;
    let cleanUrl= url.split('#')[0];
    window.history.replaceState({}, document.title, cleanUrl);
}