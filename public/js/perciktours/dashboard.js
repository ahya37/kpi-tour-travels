var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
var path        = window.location.pathname;
var tourCode    = [];

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

    const tourCodeUrl   = "website/master/jadwal";
    const tourCodeType  = "GET";
    const tourCodeData  = {
        "tahun" : moment(today).year(),
    };

    const getData       = [
        doTransaction(summaryURL, summaryType, summaryData, summaryMsg),
        doTransaction(umrahURL, umrahType, umrahData, umrahMsg),
        doTransaction(tourCodeUrl, tourCodeType, tourCodeData)
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

            
            // TABLE UMRAH
            const umrahGetData      = results[1].status == 'fulfilled' ? results[1].value.data : [];
            showTable('table_jadwal_umrah', umrahGetData);
            if(umrahGetData.length > 0) {
                $("#table_jadwal_umrah").find('.dataTables_empty').html(`Data Berhasil Dimuat`);
            } else {
                $("#table_jadwal_umrah").find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`)
            }

            // SELECT TOUR CODE
            const tourCodeGetData   = results[2].status == "fulfilled" ? results[2].value.data : [];
            if(tourCodeGetData.length > 0 && tourCode.length < 1) {
                $.each(tourCodeGetData, (i, item)  => {
                    tourCode.push({
                        "jdw_tour_code"     : item['jdw_tour_code'],
                        "jdw_depature_date" : item['jdw_depature_date'],
                    })
                })
            }
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
                { "targets" : [0, 7], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [3, 4, 5, 6], "className" : "align-middle", "width" : "10%" },
                { "targets" : [1], "className" : "align-middle" },
                { "targets" : [2], "className" : "align-middle", "width" : "15%" },
            ]
        })

        if(data.length > 0) {
            // SORT DATA
            data.sort((a, b)    => {
                return new Date(b['jdw_depature_date']) - new Date(a['jdw_depature_date']);
            })

            $.each(data, (i, item)  => {
                const umrahTourCode     = item['jdw_tour_code'];
                const umrahDate         = moment(item['jdw_depature_date'], 'YYYY-MM-DD').format('DD MMM YYYY');
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
                    `<label class="no-margins font-weight-normal">${umrahDate}</label>`,
                    `<label class="no-margins font-weight-normal">${umrahProgramName}</label>`,
                    `<label class="no-margins font-weight-normal">${umrahSeat}</label>`,
                    `<label class="no-margins font-weight-normal">${umrahTakeSeat}</label>`,
                    `<label class="no-margins font-weight-normal">${umrahAvailSeat}</label>`,
                    umrahButtonEdit
                ]).draw(false);
            })
        }

        $("#"+idTable+"_wrapper").css('padding-bottom', '0px');
    } else if(idTable == 'table_active_program_umrah') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "zeroRecords"   : "Data yang dicari tidak ditemukan",
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0, 5], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "20%" },
                { "targets" : [2], "className" : "text-left align-middle"},
                { "targets" : [3], "className" : "text-left align-middle", "width" : "15%" },
                { "targets" : [4], "className" : "text-center align-middle", "width" : "10%" }
            ],
        })

        if(data.length > 0 || data != '') {
            let articleData     = data;
            for(let i = 0; i < articleData.length; i++) {
                let seq   = i + 1;
                let articleID   = articleData[i]['jdw_uuid'];
                let articleTourCode     = articleData[i]['jdw_tour_code'];
                let articleTitle        = articleData[i]['jdw_title_name'];
                let articleProgramName  = "";
                let articleBadge;
                let articleAction;
                let articleActionColorButton;

                switch(articleData[i]['jdw_status_upload']) {
                    case 'pending'  :
                        articleBadge    = `badge-success`;
                        articleAction   = `<button class="btn btn-sm btn-success" value="${articleID}" onclick="showModal('modal_active_program_umrah_form', this.value, 'edit')" title="Lihat Program"><i class="fa fa-eye"></i></button>`;
                    break;
                    case 'appprove' : 
                        articleBadge    = `badge-primary`;
                        articleAction   = `<button class="btn btn-sm btn-primary" value="${articleID}" onclick="showModal('modal_active_program_umrah_form', this.value, 'approve')" title="Lihat Program" disabled><i class="fa fa-eye"></i></button>`;
                    break;
                    case 'reject'   :
                        articleBadge    = 'badge-danger';
                        articleAction   = `<button class="btn btn-sm btn-danger" value="${articleID}" onclick="showModal('modal_active_program_umrah_form', this.value, 'reject')" title="Lihat Program" disabled><i class="fa fa-eye"></i></button>`;
                    break 
                };

                let articleStatus   = `<span class='badge ${articleBadge}'><label class='font-weight-bold no-margins'>${articleData[i]['jdw_status_upload']}</label></span>`;

                $("#"+idTable).DataTable().row.add([
                    seq,
                    articleTourCode,
                    articleTitle,
                    articleProgramName,
                    articleStatus,
                    articleAction
                ]).draw(false);
            }
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
                $("#detail_update_terakhir").html(detailGetData['updated_at']);

                $("#tour_code").val(detailGetData['jdw_tour_code'])
                
                if(detailGetData['jdw_flyer'].length > 0) {
                    $("#uploadFlyer").addClass('d-none');
                    $("#flyer_exist").removeClass('d-none');

                    $("#flyer_exist_link").attr('href', base_url + '/' + detailGetData['jdw_flyer']).html(`<i class='fa fa-download'></i> Download Flyer`);
                } else {
                    $("#uploadFlyer").removeClass('d-none');
                    $("#flyer_exist").addClass('d-none');

                    $("#flyer_exist_link").attr('href', '#').html('');
                }
            })
            .catch((error)        => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : error.responseJSON.message,
                });
            })
    } else if(idModal == 'modal_active_program_umrah') {
        const article   = {
            'url'       : 'website/article/list',
            'type'      : 'GET',
            'data'      : {},
        };
        const message   = Swal.fire({ title : 'Data Sedang Dimuat..' }); Swal.showLoading();

        doTransaction(article['url'], article['type'], article['data'], message)
            .then((results)     => {
                Swal.close();
                let articleData     = results.data;
                let articleTable    = 'table_active_program_umrah';
                
                if(articleData.length > 0) {
                    showTable(articleTable, articleData);
                    $("#table_active_program_umrah").find('.dataTables_empty').html(`Berhasil Memuat Data`);
                } else {
                    showTable(articleTable, []);
                    $("#table_active_program_umrah").find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Ditampilkan. Silahkan Klik Tombol 'Tambah' untuk Menambah Data`);
                }

                // OPEN MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            })
            .catch((error)      => {
                Swal.fire({
                    icon    : 'warning',
                    title   : 'Terjadi Kesalahan',
                    text    : error.responseJSON.message,
                });
            })
    } else if(idModal == 'modal_active_program_umrah_form') {
        // DEFINE VALUE BUTTON
        $("#btn_act_modal_active_program_umrah_form").val(type);
        // CLOSE BACKGROUND MODAL
        closeModal('modal_active_program_umrah');
        if(data == "") {
            // OPEN MODAL
            $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            // SHOW SELECT
            showSelect('act_prog_tour_code', tourCode);
        } else {
            // CHECK TYPE
            if(type == 'edit') {
                // GET DATA
                const article   = {
                    url     : 'website/article/detail/' + data,
                    type    : 'GET',
                    data    : '',
                }

                const message   = Swal.fire({ title : 'Data Sedang Dimuat..' }); Swal.showLoading();

                doTransaction(article['url'], article['type'], article['data'], message)
                    .then((results)     => {
                        Swal.close();

                        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                        
                        fillForm('modal_active_program_umrah_form_edit', results.data);
                    })
                    .catch((error)      => {
                        console.log(error);
                        Swal.fire({
                            icon    : 'error',
                            title   : 'Terjadi Kesalahan',
                            text    : error.responseJSON.message,
                            didClose    : () => {
                                showModal('modal_active_program_umrah', '', '');
                            }
                        });
                    })
            } else if(type == 'reject') {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Artikel Ditolak, Tidak Bisa Akses Artikel ini',
                })
            } else if(type == 'approve') {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Artikel Sudah Diapprove'
                })
            }
        }
    }
}

function showSelect(idSelect, data, selectedData = '')
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });

    if(idSelect == 'act_prog_tour_code') {
        let html    = `<option selected disabled>Pilih Tour Code</option>`;
        if(data.length > 0) {
            // SORT DATA DESC
            data.sort((a, b)    => {
                return new Date(b['jdw_depature_date']) - new Date(a['jdw_depature_date']);
            })
            // DEFINE CURRENT_DATE + 1 MONTH
            let currentDataPlusOneMonth     = moment(today).add(1, 'month').format('YYYY-MM-DD');

            $.each(data, (i, item)  => {
                if(currentDataPlusOneMonth <= item['jdw_depature_date']) {
                    html    += `<option value="${item['jdw_tour_code']}">${item['jdw_tour_code']}</option>`;
                }
            })

            $("#"+idSelect).html(html);

            if(selectedData != '') {
                $("#"+idSelect).val(selectedData);
            }
        } else {
            $("#"+idSelect).html(html);
        }
    }
}

function showSelectDetail(idSelect, selectedData) {
    if(idSelect == 'act_prog_tour_code') {
        const tourCode  = {
            url     : 'website/article/tour_detail',
            type    : 'GET',
            data    : {
                'tour_code' : selectedData,
            },
        };

        const message   = Swal.fire({ title : 'Sedang Mencari Tour Code..' }); Swal.showLoading();

        doTransaction(tourCode['url'], tourCode['type'], tourCode['data'], message)
            .then((results) => {
                Swal.close();
                const tourCode_data     = results !== undefined ? results.data : [];

                fillForm('form_modal_active_program_umrah', tourCode_data);
                closeModal('modal_active_program_umrah');
            })
            .catch((error)  => {
                console.log(error);
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

        $("#detail_tour_code").val('');
        $("#detail_program").val('');
        $("#detail_keberangkatan").val('');
        $("#detail_kepulangan").val('');
        $("#detail_pembimbing").val('');
        $("#detail_jml_seat").val(0);
        $("#detail_seat_avail").val(0);
        $("#detail_seat_use").val(0);

        $("#uploadFlyer").trigger('reset');

        $("#uploadFlyer").removeClass('d-none');

        $("#flyer_exist").addClass('d-none');
        $("#flyer_exist_link").attr('href', '').html('');
    } else if(idModal == 'modal_active_program_umrah') {
        $("#"+idModal).modal('hide');
    } else if(idModal == 'modal_active_program_umrah_form') {
        $("#"+idModal).modal('hide');
        showModal('modal_active_program_umrah', '', '');
        $("#"+idModal).on('hidden.bs.modal', () => {
            resetForm('form_modal_active_program_umrah');
        })
    }
}

function fillForm(idForm, data)
{
    if(idForm == 'form_modal_active_program_umrah') {
        // RESET FORM
        resetForm(idForm);

        if(data.length > 0) {
            $("#act_prog_depature_date").val(moment(data[0]['depature_date'], 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#act_prog_arrival_date").val(moment(data[0]['arrival_date'], 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#act_prog_category").val(data[0]['product_name']);
            $("#act_prog_program").val(data[0]['program_name']);
            $("#act_prog_title").focus();
        }
    } else if(idForm == 'modal_active_program_umrah_form_edit') {
        if(data.length > 0) {
            showSelect('act_prog_tour_code', tourCode, data[0]['tour_code']);

            $("#act_prog_uuid").val(data[0]['article_id']);
            $("#act_prog_depature_date").val(moment(data[0]['depature_date'], 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#act_prog_arrival_date").val(moment(data[0]['arrival_date'], 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#act_prog_category").val(data[0]['product_name']);
            $("#act_prog_program").val(data[0]['program_name']);
            $("#act_prog_title").val(data[0]['article_title']);
            $("#act_prog_destination").val(data[0]['destination']);
            $("#act_prog_duration").val(data[0]['duration']);
            $("#act_prog_airlines").val(data[0]['airline_name']);
            $("#act_prog_hotel_mekkah").val(data[0]['hotel_mekkah']);
            $("#act_prog_hotel_madinah").val(data[0]['hotel_madinah']);
            $("#act_prog_cost_quad").val(parseInt(data[0]['cost_quad']));
            $("#act_prog_cost_triple").val(parseInt(data[0]['cost_triple']));
            $("#act_prog_cost_double").val(parseInt(data[0]['cost_double']));
        }
    }
}

function resetForm(idForm) {
    if(idForm == 'form_modal_active_program_umrah') {
        const formName  = document.getElementById(idForm);
        formName.reset();
    }
}

function doUpload()
{
    let form    = new FormData($("#uploadFlyer")[0]);

    $.ajax({
        cache   : false,
        url     : base_url + '/' + 'website/transaction/flyer',
        method  : "POST",
        headers : {
            'X-CSRF-TOKEN'  : CSRF_TOKEN,
        },
        data            : form,
        contentType     : false,
        processData     : false,
        beforeSend      : () => {
            Swal.fire({ title : 'Flyer Sedang Diupload..' }); Swal.showLoading();
            closeModal('modal_detail_jadwal');
        },
        success         : (response)    => {
            Swal.fire({
                icon    : response.alert.icon,
                title   : response.alert.message.title,
                text    : response.alert.message.text,
                didClose    : () => {
                    showModal('modal_detail_jadwal', $("#tour_code").val());
                }
            });
        },
        error           : (error)       => {
            let errMsg  = error.responseJSON.alert;

            Swal.fire({
                icon    : errMsg.icon,
                title   : errMsg.message.title,
                text    : errMsg.message.text,
                didClose    : () => {
                    showModal('modal_detail_jadwal', $("#tour_code").val());
                }
            })
        }
    })
}

function doSaveData(idForm, type)
{
    if(idForm == 'form_modal_active_program_umrah')
    {
        let tourCode  = $("#act_prog_tour_code");

        let formName    = document.getElementById(idForm);
        let formData    = new FormData(formName);
        formData.append('act_prog_tour_code', tourCode.val());

        const article   = {
            url     : "website/article/save/"+type,
            type    : "POST",
            data    : formData,
        };

        const message   = Swal.fire({ title : 'Data Sedang Diproses..' }); Swal.showLoading();

        doPostTransaction(article['url'], article['type'], article['data'], message)
            .then((results)     => {
                console.log(results);
                // Swal.fire({
                //     icon    : 'success',
                //     title   : 'Berhasil',
                //     text    : results.message,
                // }).then((res)   => {
                //     if(res.isConfirmed) {
                //         closeModal('modal_active_program_umrah_form');
                //     }
                // })
            })
            .catch((error)      => {
                console.log(error);
                Swal.fire({
                    icon    : 'warning',
                    title   : 'Terjadi Kesalan',
                    text    : 'Kolom yang berwarna merah harus diisi',
                    didClose    : () => {
                        const message     = error.responseJSON.message;

                        $.each(message, (i, item)   => {
                            $("#"+i).addClass('is-invalid');

                            $("#"+i).on('keyup', () => {
                                $("#"+i).removeClass('is-invalid');
                            })
                        })
                    }
                });
            })
    }
}

function doTransaction(url, type, data, message = null)
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

function doPostTransaction(url, type, data, message)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            url     : base_url + "/" + url,
            type    : type,
            data    : data,
            headers     : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            processData     : false,
            contentType     : false,
            beforeSend      :  () => {
                message;
            },
            success         : (success) => {
                resolve(success)
            },
            error           : (error)   => {
                reject(error)
            },
        })
    })
}