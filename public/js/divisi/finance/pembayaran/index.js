var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
var temp_pengajuan  = [];
var temp_bulan      = [];

for(let i = 0; i < 11; i++) {
    let monthNumber     = moment(i + 1, 'M').format('MM');
    let monthName       = moment(i + 1, 'M').format('MMMM');

    temp_bulan.push({
        'bulan_ke'  : monthNumber,
        'bulan_nama': monthName,
    });
}

showSelect('filter_bulan', temp_bulan, moment(today, 'YYYY-MM-DD').format('MM'))

const isLoading   = (idForm) => {
    return $("#"+idForm).html(`<span class="spinner spinner-border"></span>`);
} 

$(document).ready(function(){
    // GET DATA PENGAJUAN KEUANGAN
    showDataDashboard(moment(today, 'YYYY-MM-DD').format('MM'));
});

function showModal(idModal, type, data)
{
    if(idModal == 'modal_pengajuan_keuangan') {
        Swal.fire({
            title   : 'Data Sedang Dimuat..'
        });
        Swal.showLoading();

        setTimeout(()   => {
            Swal.close();
            $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            $("#title_bulan_modal_pengajuan_keuangan").html(`Bulan : ${moment(today, 'YYYY-MM-DD').format('MMMM')},${moment(today, 'YYYY-MM-DD').format('YYYY')}`)
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

                    showTable('table_detail_pengajuan_keuangan', detailPengajuanDetail);
                }
            })
            .catch((error)      => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : error.responseJSON.message,
                    didClose    : () => {
                        showModal('modal_pengajuan_keuangan', 'list', '');
                    }
                })
            })
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_pengajuan_keuangan') {
        $("#"+idModal).modal('hide');
    } else if(idModal == 'detail_modal_pengajuan_keuangan') {
        $("#"+idModal).modal('hide');
        showModal('modal_pengajuan_keuangan', 'list', '');
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
                grandTotal += parseInt(detailJumlah);

                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${detailSeq}</label>`,
                    `<label class="font-weight-normal no-margins">${detailDesc}</label>`,
                    `<label class="font-weight-normal no-margins">${detailCurr}</label>`,
                    `<label class="font-weight-normal no-margins">${parseInt(detailJumlah).toLocaleString('id-ID')}</label>`
                ]).draw(false);
            }

            $("#total_pengajuan_keuangan").html(`<label class="no-margins">${parseInt(grandTotal).toLocaleString('id-ID')}</label>`);
        }
    }

    $("#"+idTable+"_wrapper").css('padding-bottom', '0px');
}

function showSelect(idSelect, data, selectedData)
{
    $("#"+idSelect).select2({
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
    }
}

function showDataDashboard(selectedMonth)
{
    // SHOW DEFAULT LOADING
    isLoading('dashboard_pengajuan_keuangan');
    isLoading('dashboard_pembayaran_jemaah');
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

    
    // COLLECTIVE GET DATA
    const collectApi    = [
        doTransaction(pengajuanURL, pengajuanType, pengajuanData, pengajuanMsg)
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
            $("#dashboard_pembayaran_jemaah").html(`<h2 class="no-margins">0</h2>`);
        })
        .catch((error)      => {
            console.log(error);
        })
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