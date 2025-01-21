var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
var path        = window.location.pathname;

$(document).ready(function() {
    // GET DATA SUMMARY
    const summaryURL    = "website/summary_data";
    const summaryType   = "GET";
    const summaryData   = [];
    const summaryMsg    = "";

    const umrahURL      = "website/master/jadwal";
    const umrahType     = "GET";
    const umrahData     = {
        "tahun" : moment(today).year(),
    };
    const umrahMsg      = "";

    const getData       = [
        doTransaction(summaryURL, summaryType, summaryData, summaryMsg),
        doTransaction(umrahURL, umrahType, umrahData, umrahMsg),
    ];

    Promise.allSettled(getData)
        .then((results) => {
            // SUMMARY DATA
            const summaryGetData    = results[0].value.data;

            $("#sum_total_jemaah").html(summaryGetData['data_summary']['total_jemaah']);
            $("#sum_total_perjalanan").html(summaryGetData['data_summary']['total_perjalanan']);
            $("#sum_total_pembimbing").html(summaryGetData['data_summary']['total_pembimbing']);
            $("#sum_total_agen").html(summaryGetData['data_summary']['total_agen']);
            $("#sum_last_update").html(summaryGetData['data_summary']['last_update']);

            $("#sum_total_produk").html(summaryGetData['data_product']['total_product']);

            $("#sum_total_program").html(summaryGetData['data_program']['total_program']);

            const umrahGetData      = results[1].value.data;
            showTable('table_jadwal_umrah', umrahGetData);
            $("#table_jadwal_umrah").find('.dataTables_empty').html(`Data Berhasil Dimuat`);
        })
        .catch((err)    => {
            console.log(err);
            $("#sum_total_jemaah").html(0);
            $("#sum_total_perjalanan").html(0);
            $("#sum_total_pembimbing").html(0);
            $("#sum_total_agen").html(0);
            $("#sum_last_update").html(today);

            $("#sum_total_produk").html(0);
            $("#sum_total_program").html(0);
            
            showTable('table_jadwal_umrah', []);
            $("#table_jadwal_umrah").find('.dataTables_empty').html(`Data Gagal Dimuat`);
            $("#refresh_table_jadwal_umrah").removeClass('d-none');
        })
})

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();

    if(idTable == 'table_jadwal_umrah') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "<i class='fa fa-spinner fa-spin'></i> Data sedang dimuat..",
                zeroRecords : "Data yang dicari tidak ditemukan"
            },
            autoWidth   : false,
            paging      : true,
            ordering    : false,
            columnDefs  : [
                { "targets" : [0, 6], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [3, 4, 5], "className" : "align-middle", "width" : "10%" },
                { "targets" : [1], "className" : "align-middle" },
                { "targets" : [2], "className" : "align-middle", "width" : "15%" },
            ]
        })

        if(data.length > 0) {
            
            data.sort((a, b)    => {
                return new Date(b['jdw_depature_date']) - new Date(a['jdw_depature_date']);
            })

            $.each(data, (i, item)  => {
                const umrahTourCode     = item['jdw_tour_code'];
                const umrahProgramName  = item['programs_name'];
                const umrahSeat         = item['jdw_seat'];
                const umrahTakeSeat     = item['jdw_take_seat'];
                const umrahAvailSeat    = item['jdw_available_seat'];
                let isDisabled          = item['is_active'] == 't' ? '' : 'disabled';
                const umrahButtonEdit   = `<button class="btn btn-sm btn-primary" type="button" title="Lihat Detail" value="${umrahTourCode}" ${isDisabled} onclick="showModal('modal_detail_jadwal', this.value, '')"><i class="fa fa-eye"></i></button>`;
                const umrahIsActive     = item['is_active'];
                const tourCode          = umrahIsActive == 't' ? umrahTourCode : umrahTourCode + " <span class='badge badge-sm badge-danger'>Tidak Aktif</span>";

                $("#"+idTable).DataTable().row.add([
                    `<label class="no-margins font-weight-normal">${i + 1}</label>`,
                    `<label class="no-margins font-weight-normal">${tourCode}</label>`,
                    `<label class="no-margins font-weight-normal">${umrahProgramName}</label>`,
                    `<label class="no-margins font-weight-normal">${umrahSeat}</label>`,
                    `<label class="no-margins font-weight-normal">${umrahTakeSeat}</label>`,
                    `<label class="no-margins font-weight-normal">${umrahAvailSeat}</label>`,
                    umrahButtonEdit
                ]).draw(false);
            })
        }

        $("#"+idTable+"_wrapper").css('padding-bottom', '0px');
    }
}

function doTarikData(idForm)
{
    if(idForm == 'summary_data') {
        $("#btn_summary_tarik").html(`<i class='fa fa-spinner fa-spin'></i> Data Sedang Diproses`);
        $("#btn_summary_tarik").prop('disabled', true);
        // GET DATA
        const url   = 'website/get_summary_data';
        const type  = 'GET';
        const msg   = '';
        const data  = [];
        
        doTransaction(url, type, data, msg)
            .then((results) => {
                $("#btn_summary_tarik").html(`<i class='fa fa-redo'></i> Tarik Data`);
                $("#btn_summary_tarik").prop('disabled', false);

                const getData   = results.data;
                $("#sum_total_jemaah").html(getData['total_jemaah']);
                $("#sum_total_perjalanan").html(getData['total_perjalanan']);
                $("#sum_total_pembimbing").html(getData['total_pembimbing']);
                $("#sum_total_agen").html(getData['total_agen']);
                $("#sum_last_update").html(getData['last_update']);
            })
            .catch((err)    => {
                $("#btn_summary_tarik").html(`<i class='fa fa-redo'></i> Tarik Data`);
                $("#btn_summary_tarik").prop('disabled', false);

                $("#sum_total_jemaah").html(0);
                $("#sum_total_perjalanan").html(0);
                $("#sum_total_pembimbing").html(0);
                $("#sum_total_agen").html(0);
                $("#sum_last_update").html(today);
            })
    } else if(idForm == 'jadwal_umrah') {
        showTable('table_jadwal_umrah', []);
        $("#btn_jadwal_tarik").prop('disabled', true);
        $("#btn_jadwal_tarik").html(`<i class="fa fa-sync fa-spin"></i> Data Sedang Diperbarui`);

        const umrahURL  = "website/master/jadwal_tarik";
        const umrahType = "GET";
        const umrahData = {
            "tahun" : moment(today).year(),
        };
        const umrahMsg  = "";

        doTransaction(umrahURL, umrahType, umrahData, umrahMsg)
            .then((results)     => {
                showTable('table_jadwal_umrah', results.data);

                $("#btn_jadwal_tarik").prop('disabled', false);
                $("#btn_jadwal_tarik").html(`<i class="fa fa-sync"></i> Perbarui Data`);
            })
            .catch((err)        => {
                console.log(err);
                showTable('table_jadwal_umrah', []);
                
                $("#btn_jadwal_tarik").prop('disabled', false);
                $("#btn_jadwal_tarik").html(`<i class="fa fa-sync"></i> Perbarui Data`);
            })
    } else if(idForm == 'refresh_table_umrah') {
        showTable('table_jadwal_umrah', []);
        $("#refresh_table_jadwal_umrah").html(`<i class="fa fa-sync fa-spin"></i>`);
        $("#refresh_table_jadwal_umrah").prop('disabled', true);
        const jadwalURL     = "website/master/jadwal";
        const jadwalType    = "GET";
        const jadwalData    = {
            'tahun'     : moment(today).year(),
        };
        const jadwalMsg     = "";

        doTransaction(jadwalURL, jadwalType, jadwalData, jadwalMsg)
            .then((results)     => {
                showTable('table_jadwal_umrah', results.data);
                
                $("#refresh_table_jadwal_umrah").html(`<i class="fa fa-sync"></i>`);
                $("#refresh_table_jadwal_umrah").prop('disabled', false);
                $("#refresh_table_jadwal_umrah").addClass('d-none');
            })
            .catch((error)      => {
                showTable('table_jadwal_urmah', []);
                
                $("#refresh_table_jadwal_umrah").html(`<i class="fa fa-sync"></i>`);
                $("#refresh_table_jadwal_umrah").prop('disabled', false);
                $("#refresh_table_jadwal_umrah").removeClass('d-none');
            })
    }
}

function showModal(idModal, data, type)
{
    if(idModal == 'modal_detail_jadwal') {
        // $("#"+idModal).modal({backdrop: 'static', keyboard: false});

        const detailUrl     = "website/master/jadwal_detail";
        const detailData    = {
            "tour_code" : data,
        };
        const detailMsg     = Swal.fire({ title : "Data Sedang Dimuat..", allowOutsideClick: true }); Swal.showLoading();
        const detailType    = "GET";

        doTransaction(detailUrl, detailType, detailData, detailMsg)
            .then((results)     => {
                Swal.close();
                $("#"+idModal).modal({backdrop: 'static', keyboard: false});

                // SHOW DATA
                const detailGetData     = results.data[0];
                $("#detail_tour_code").val(detailGetData['jdw_tour_code']);
                $("#detail_keberangkatan").val(moment(detailGetData['jdw_depature_date'], 'YYYY-MM-DD').format('DD MMM YYYY'));
                $("#detail_kepulangan").val(moment(detailGetData['jdw_arrival_date'], 'YYYY-MM-DD').format('DD MMM YYYY'));
                $("#detail_pembimbing").val(detailGetData['jdw_mentor_name']);
                $("#detail_jml_seat").val(detailGetData['jdw_seat']);
                $("#detail_seat_avail").val(detailGetData['jdw_available_seat']);
                $("#detail_seat_use").val(detailGetData['jdw_take_seat']);
                $("#detail_update_terakhir").html(detailGetData['updated_at']);})
            .catch((error)        => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : error.responseJSON.message,
                });
            })
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_detail_jadwal') {
        $("#"+idModal).modal('hide');
    }
}

function doTransaction(url, type, data, message)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            url     : base_url + "/" + url,
            type    : type,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                message;
            },
            success     : (success) => {
                resolve(success)
            },
            error       : (error)   => {
                reject(error);
            }
        })
    })
}