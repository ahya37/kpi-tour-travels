var today   = moment().format('YYYY-MM-DD');
$(document).ready(function(){
    // console.log('test');
    // current_month
    var currMonth   = moment().format('MM');
    var currYear    = moment().format('YYYY');
    var currPaket   = '%';
    showSelect('programFilterBulan', '%', '%', '');
    showSelect('programFilterTahun', '%', currYear, '');
    showSelect('programFilterPaket', '%', currPaket, true);

    var inputCurrMonth  = $("#programFilterBulan").val();

    showTable('table_program_umrah', [inputCurrMonth, currYear, '%', currPaket]);

    $("#programFilterBtnCari").on('click', function(){
        var selectedMonth   = $("#programFilterBulan").val();
        var selectedYear    = $("#programFilterTahun").val();
        var selectedPaket   = $("#programFilterPaket").val();
        showTable('table_program_umrah', [selectedMonth, selectedYear, '%', selectedPaket])
    })

});

var site_url    = window.location.pathname;

function showTable(idTable, valueCari)
{
    if(idTable == 'table_program_umrah') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat...",
                "zeroRecords"   : "Tidak ada data yang bisa ditampilkan..",
                "emptyTable"    : "Tidak ada data yang bisa ditampilkan.."
            },
            columnDefs  : [
                { targets: [0, 5, 6], className: "text-center", width: "7%" },
                { targets: [3, 4], className: "text-center", width: "16%" },
                { targets: [1], className: "text-left", width: "18%" },
            ],
            processing  : true,
            serverSide  : false,
            ajax        : {
                type    : "GET",
                dataType: "json",
                data    : {
                    sendData    : {
                        cari    : valueCari,
                    },
                },
                url     : '/divisi/operasional/program/listJadwalumrah',
            },
        });
    }
}

function showModal(idModal, valueCari, jenis)
{
    $("#btnSimpan").val(jenis);

    $(".programDate").daterangepicker({
        singleDatePicker : true,
        locale : {
            format  : 'DD/MM/YYYY',
        },
        minYear     : moment().subtract(10, 'years'),
        maxYear     : moment().add(10, 'years'),
        autoApply    : true,
        showDropdowns: true,
    });

    $("#programPembimbing").on('keyup', function(){
        $(this).removeClass('is-invalid');
    });

    // SHOW SELECT
    if(jenis == 'add') {
        $("#"+idModal).modal({backdrop: 'static', keyboard: false});
        $("#"+idModal).modal('show');
        showSelect('programPaket','%','', true);
        $("#btnDelete").hide();
    } else if(jenis == 'edit') {
        $("#btnDelete").show();
        // GET DATA
        var url     = site_url + "/getDataJadwalUmrah";
        var data    = {
            "programID" : valueCari,
        };
        var type    = "GET";
        var isAsync     = true;
        var message     = Swal.fire({title:'Data Sedang Dimuat'});Swal.showLoading();

        doTrans(url, type, data, message, isAsync)
            .then((xhr) => {
                console.log(xhr);
                $("#"+idModal).modal({backdrop: 'static', keyboard: false});
                $("#"+idModal).modal('show');
                
                // INPUT DATA TO FORM
                var getData     = xhr.data[0];
                $("#programID").val(getData['jdw_id']);
                $("#programDepDate").val(moment(getData['jdw_depature_date'], 'YYYY-MM-DD').format('DD/MM/YYYY')).trigger('change');
                $("#programArvDate").val(moment(getData['jdw_arrival_date'], 'YYYY-MM-DD').format('DD/MM/YYYY')).trigger('change');
                $("#programPembimbing").val(getData['jdw_mentor_name']);
                showSelect('programPaket', '%', getData['jdw_programs_id'], false);
                Swal.close();

                if(getData['status_active'] == 'f') {
                    $("#btnDelete").prop('disabled', true);
                    $("#btnBatal").prop('disabled', true);
                    $("#btnSimpan").prop('disabled', true);
                } else {
                    $("#btnDelete").prop('disabled', false);
                    $("#btnBatal").prop('disabled', false);
                    $("#btnSimpan").prop('disabled', false);
                    if(getData['status_generated'] == 'f') {
                        $("#btnDelete").hide();
                    } else {
                        $("#btnDelete").show();
                    }
                }
            })
            .catch((xhr) => {
                Swal.fire({
                    icon    : 'info',
                    title   : 'Tidak ada Data'
                });
                console.log(xhr);
            });
    }
}

function closeModal(idModal) {
    if(idModal == 'modalFormV2') {
        $("#"+idModal).modal('hide');
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#tourCode_dptDate").val(null);
            $("#tourCode_arvDate").val(null);
            $("#tourCode_mentorName").val(null);
        });
    }
}

function showSelect(idSelect, valueCari, valueSelect, isAsync)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
    if(idSelect == 'programPaket') {
        var html    = "<option selected disabled>Pilih Paket Program Umrah</option>";
        var url     = "/master/data/getProgramUmrah/umrah";
        var data    = {
            "cari"  : valueCari,
        };
        doTrans(url, "GET", data, '', isAsync)
            .then(function(xhr){
                for(var i = 0; i < xhr.data.length; i++) {
                    html    += "<option value='" + xhr.data[i]['program_id'] + "'>" + xhr.data[i]['program_name'] + "</option>";
                }

                $("#"+idSelect).html(html);
                
                if(valueSelect != '') {
                    $("#"+idSelect).val(valueSelect).trigger('change');
                }
            })
            .catch(function(xhr){
                console.log(xhr);
            });

        $("#"+idSelect).html(html);
    } else if(idSelect == 'programFilterBulan') {
        var month   = moment.months();
        var html    = [
            "<option selected disabled>Pilih Bulan</option>",
            "<option value='%'>Semua</option>"
        ];
        for(var i = 0; i < month.length; i++) {
            var id      = moment(month[i], 'MMM').format('MM');
            var text    = month[i];
            html    += "<option value='" + id + "'>" + text + "</option>";
        }

        $("#"+idSelect).html(html);
        if(valueSelect != '') {
            $("#"+idSelect).val(valueSelect).trigger('change');
        }
    } else if(idSelect == 'programFilterTahun') {
        var html            = "<option selected disabled>Pilih Tahun</option>";
        var current_year    = moment().format('YYYY');
        var past_year_10    = moment(current_year, 'YYYY').subtract(10, 'years').year();
        var future_year_10  = moment(current_year, 'YYYY').add(10, 'years').year();

        for(let i = past_year_10; i <= future_year_10; i++) {
            html    += "<option value='" + i + "'>" + i + "</option>"
        }
        
        $("#"+idSelect).html(html);
        if(valueCari != '') {
            $("#"+idSelect).val(valueSelect).trigger('change');
        }
    } else if(idSelect == 'programFilterPaket') {
        var html    = [
            "<option selected disabled>Pilih Paket Program Umrah</option>",
            "<option value='%'>Semua</option>"
        ];
        var url     = "/master/data/getProgramUmrah/umrah";
        var data    = {
            "cari"  : valueCari,
        };
        doTrans(url, "GET", data, '', isAsync)
            .then(function(xhr){
                for(var i = 0; i < xhr.data.length; i++) {
                    html    += "<option value='" + xhr.data[i]['program_id'] + "'>" + xhr.data[i]['program_name'] + "</option>";
                }

                $("#"+idSelect).html(html);
                
                if(valueSelect != '') {
                    $("#"+idSelect).val(valueSelect).trigger('change');
                }
            })
            .catch(function(xhr){
                console.log(xhr);
            });

        $("#"+idSelect).html(html);
    } else if(idSelect == 'tourCode_id') {
        var html    = "<option selected disabled>Pilih Tour Code</option>";

        if(valueCari != '') {
            if(valueCari.length > 0) {
                $.each(valueCari, (i, item) => {
                    const tourCode      = item.tour_code_umrah;
                    const dateDepature  = item.tour_code_depature;
                    const dateArrival   = item.tour_code_arrival;
                    html    += "<option value='" + tourCode + "'>" + tourCode + " (" + moment(dateDepature, 'YYYY-MM-DD').format('DD/MM/YYYY') + " s/d " + moment(dateArrival, 'YYYY-MM-DD').format('DD/MM/YYYY') + ")</option>";
                });
            }
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'tourCode_programID') {
        var html    = "<option selected disabled>Pilih Program</option>";

        if(valueCari != '') {
            $.each(valueCari, (i, item) => {
                html    += "<option value='" + item.program_id + "'>" + item.program_name + "</option>";
            });
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    }

    // SHOW SELECT V3
    else if(idSelect == 'mst_tourCode_program') {
        var html    = "<option selected disabled>Pilih Program</option>";
        if(valueCari != '') {
            $.each(valueCari, (i, item) => {
                html    += "<option value='" + item.id + "'>" + item.PROGRAM + "</option>";
            });
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'mst_tourCode_mentor') {
        var html    = "<option selected disabled>Pilih Pembimbing</option>";
        if(valueCari != '') {
            $.each(valueCari, (i, item) => {
                html    += "<option value='" + item.ID + "'>" + item.NAMA + "</option>";
            });
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'mst_tourCode_destination') {
        var html    = "<option selected disabled>Pilih Tujuan</option>";
        if(valueCari != '') {
            $.each(valueCari, (i, item) => {
                html    += "<option value='" + item.ID + "'>" + item.KODE + " - " + item.NAMA + "</option>";
            })
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'mst_tourCode_route') {
        var html    = "<option selected disabled>Pilih Rute</option>";
        if(valueCari != '') {
            $.each(valueCari, (i, item) => {
                html    += "<option value='" + item.id + "'>" + item.inisial + "</option>";
            })
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'mst_tourCode_tourLeader') {
        var html    = "<option selected disabled>Pilih Tour Leader</option>";
        if(valueCari != '') {
            $.each(valueCari, (i, item) => {
                html    += "<option value='" + item.ID + "'>" + item.NAMA + "</option>";
            });
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    }
}


function doSimpan(jenis)
{
    var programID       = $("#programID");
    var depatureDate    = $("#programDepDate");
    var arrivalDate     = $("#programArvDate");
    var programPembimbing   = $("#programPembimbing");
    var programPaket        = $("#programPaket");

    if(depatureDate.val() == '') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Tgl. Keberangkatan tidak boleh kosong',
        }).then((xhr) => {
            depatureDate.focus();
        });
    } else if(arrivalDate.val() == '') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Tgl. Kedatanganan tidak boleh kosong',
        }).then((xhr) => {
            arrivalDate.focus();
        });
    } else if(programPembimbing.val() == '') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Nama Pembimbing tidak boleh kosong',
        }).then((xhr) => {
            programPembimbing.addClass('is-invalid');
        });
    } else if(programPaket.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Pilih Paket Program Umrah',
        }).then((xhr) => {
            programPaket.select2('open');
            programPaket.focus();
        });
    } else {
        var dataSend    = {
            "programID"         : programID.val(),
            "depatureDate"      : moment(depatureDate.val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
            "arrivalDate"       : moment(arrivalDate.val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
            "programPembimbing" : programPembimbing.val(),
            "programPaket"      : programPaket.val(),
            "transType"         : jenis,
        };
        
        var url     = site_url + "/simpanJadwalUmrah";
        var type    = "POST";
        var data    = dataSend;
        var customMessage   = Swal.fire({title:'Data Sedang Diproses'});Swal.showLoading();
        var isAsync     = true;

        doTrans(url, type, data, customMessage, isAsync)
            .then((xhr) => {
                Swal.fire({
                    icon    : xhr.alert.icon,
                    title   : xhr.alert.message.title,
                    text    : xhr.alert.message.text,
                }).then((results) => {
                    if(results.isConfirmed) {
                        var insertMonth     = moment(dataSend['depatureDate'], 'YYYY-MM-DD').format('MM');
                        var insertYear      = moment(dataSend['depatureDate'], 'YYYY-MM-DD').format('YYYY');

                        $("#programFilterBulan").val(insertMonth).trigger('change');
                        $("#programFilterTahun").val(insertYear).trigger('change');

                        closeModal('modalForm');
                        showTable('table_program_umrah', [insertMonth, insertYear, '%', '%']);
                    }
                })
            })
            .catch((xhr) => {
                console.log(xhr);
            })
    }
}

function doDelete(idForm)
{
    if(idForm == 'btnDeleteV2') {
        var programID   = $("#tourCode_programUmrah_id").val();

        Swal.fire({
            icon    : 'question',
            title   : 'Hapus Data',
            text    : 'Data yang telah dihapus tidak akan muncul pada tabel, anda yakin?',
            showCancelButton    : true,
            showConfirmButton   : true,
            cancelButtonText    : 'Batal',
            confirmButtonText   : 'Ya, Hapus',
            confirmButtonColor  : '#dc3545',
        }).then((results)=>{
            if(results.isConfirmed) {
                var url         = site_url + "/hapusProgram/"+programID;
                var type        = "POST";
                var data        = "";
                var message     = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
                var isAsync     = true;

                doTrans(url, type, data, message, isAsync)
                    .then((success)=>{
                        Swal.fire({
                            icon    : success.alert.icon,
                            title   : success.alert.message.title,
                            text    : success.alert.message.text,
                        }).then((results)=>{
                            if(results.isConfirmed) {
                                closeModal('modalFormV2');
                                var currYear    = moment().format('YYYY');
                                var currPaket   = '%';
                                var inputCurrMonth  = $("#programFilterBulan").val();

                                showTable('table_program_umrah', [inputCurrMonth, currYear, '%', currPaket]);
                            }
                        })
                    })
                    .catch((err)=>{
                        console.log(err);
                    })
            }
        })
    }
}

// MODAL V2
function showModalV2(idModal, value, jenis)
{
    if(idModal == 'modalFormV2') {
        // DEFINE BUTTON
        $("#btnSimpanV2").val(jenis);
        // FILL TITLE 
        if(jenis == 'add') {
            $("#modalFormV2_title").html('Generate Program Umrah Baru');
            $("#btnDeleteV2").hide();
        } else if(jenis == 'edit') {
            $("#modalFormV2_title").html('Update Program Umrah');
            $("#btnDeleteV2").show();
        }
        // GET TOUR CODE FROM API
        const dataProgram = {
            'cari'  : '%',
        };
        const detailProgramUmrah    = {
            "programID" : value,
        };
        const message     = Swal.fire({ title : "Data Sedang Dimuat", allowOutsideClick: false }); Swal.showLoading();
        var getData     = [
            // doTransAPI('/umrah/tourcode?year=2024', 'GET', '', message, true),
            doTrans('/divisi/operasional/umhaj/umrah_getData_tourCode/'+moment(today, 'YYYY-MM-DD').format('YYYY'), 'GET', '', true),
            doTrans('/master/data/getProgramUmrah/umrah', 'GET', dataProgram, '', true),
            jenis == 'edit' ? doTrans('/divisi/operasional/program/getDataJadwalUmrah', 'GET', detailProgramUmrah, '', true) : [],
        ];
        Promise.all(getData)
            .then((success) => {
                // DATA API
                const tourCode      = success[0].data;
                const list_program      = success[1].data;
                const detail_program    = jenis == 'edit' ? success[2].data[0] : '';
                
                $("#"+idModal).modal({ backdrop : 'static', keyboard: false });
                
                showSelect('tourCode_id', tourCode, '', true);
                showSelect('tourCode_programID', list_program, '', true);

                if(jenis == 'edit') {
                    const program_umrah_id          = detail_program.jdw_id;
                    const program_umrah_tour_code   = detail_program.jdw_tour_code;
                    const program_umrah_dpt_date    = detail_program.jdw_depature_date;
                    const program_umrah_arv_date    = detail_program.jdw_arrival_date;
                    const program_umrah_mentor_name = detail_program.jdw_mentor_name;
                    const program_umrah_program_id  = detail_program.jdw_programs_id;

                    $("#tourCode_programUmrah_id").val(program_umrah_id);
                    $("#tourCode_dptDate").val(moment(program_umrah_dpt_date, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                    $("#tourCode_arvDate").val(moment(program_umrah_arv_date, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                    $("#tourCode_mentorName").val(program_umrah_mentor_name.toUpperCase());

                    $("#tourCode_id").val(program_umrah_tour_code);
                    $("#tourCode_programID").val(program_umrah_program_id);
                }

                Swal.close();
            })
            .catch((err)    => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : err.statusMessage,
                })
                console.log(err);
            })
    }
}

function showData(jenis, value)
{
    if(jenis == 'tourCode') {
        const tourCode_url  = "/divisi/operasional/umhaj/umrah_getData_tourCode_detail";
        const tourCode_data = {
            "tour_code" : value,
        };
        const tourCode_type = "GET";
        const tourCode_msg  = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

        doTrans(tourCode_url, tourCode_type, tourCode_data, tourCode_msg, true)
            .then((success)     => {
                Swal.close();
                // FILL FORM
                const tourCode_getData  = success.data[0];
                const depatureDate      = tourCode_getData['umrah_depature'];
                const arrivalDate       = tourCode_getData['umrah_arrival'];
                const mentorName        = tourCode_getData['umrah_mentor_name'];
                const programID         = tourCode_getData['umrah_program_id'];
                const programName       = tourCode_getData['umrah_program_name'];

                $("#tourCode_dptDate").val(moment(depatureDate, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                $("#tourCode_arvDate").val(moment(arrivalDate, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                $("#tourCode_mentorName").val(mentorName);
                $("#tourCode_programID").val(programID).trigger('change');
                $("#tourCode_programID").prop('disabled', true);
            })
            .catch((error)      => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Data Tour Code '+value+' Tidak Ditemukan'
                });
                
                console.log(error)
            })
    }
}

function doSimpanV2(idModal, jenis)
{
    if(idModal == 'modalFormV2') {
        const program_umrah_id          = $("#tourCode_programUmrah_id");
        const program_umrah_tour_code   = $("#tourCode_id");
        const program_umrah_dpt_date    = $("#tourCode_dptDate");
        const program_umrah_arv_date    = $("#tourCode_arvDate");
        const program_umrah_mentor_name = $("#tourCode_mentorName");
        const program_umrah_program_id  = $("#tourCode_programID");

        if(program_umrah_tour_code.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Tour Code Tidak Harus Dipilih',
            }).then((results)   => {
                if(results.isConfirmed) {
                    program_umrah_tour_code.select2('open');
                }
            })
        } else {
            // DO SIMPAN
            const sendData    = {
                "program_umrah_id"          : program_umrah_id.val(),
                "program_umrah_tour_code"   : program_umrah_tour_code.val(),
                "program_umrah_dpt_date"    : moment(program_umrah_dpt_date.val(), 'DD/MM/YYYY').format('YYYY-MM-DD'), 
                "program_umrah_arv_date"    : moment(program_umrah_arv_date.val(), 'DD/MM/YYYY').format('YYYY-MM-DD'), 
                "program_umrah_mentor_name" : program_umrah_mentor_name.val(), 
                "program_umrah_program_id"  : program_umrah_program_id.val(), 
                "program_umrah_jenis"       : jenis,
            };
            
            const url       = "/operasional/program/simpanJadwalUmrahV2";
            const data      = sendData;
            const type      = "POST";
            const message   = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
            const isAsync   = true;

            doTrans(url, type, data, message, isAsync)
                .then((success) => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text,
                    }).then((results)   => {
                        if(results.isConfirmed) {
                            const selectedMonth   = $("#programFilterBulan").val();
                            const selectedYear    = $("#programFilterTahun").val();
                            const selectedPaket   = $("#programFilterPaket").val();
                            closeModal('modalFormV2');
                            showTable('table_program_umrah', [selectedMonth, selectedYear, '%', selectedPaket]);
                        }
                    })
                })
                .catch((err)    => {
                    const errData   = err.responseJSON;
                    Swal.fire({
                        icon    : errData.alert.icon,
                        title   : errData.alert.message.title,
                        text    : errData.alert.message.text,
                    });
                })
        }
    }
}

// MODAL TOUR CODE V3

function showModalTourCode(idModal, jenis)
{

    const doTrans   = [
        doTransAPI('/program/list', 'GET', '', '', true),
        doTransAPI('/pembimbing/list', 'GET', '', '', true),
        doTransAPI('/kota/rute/list', 'GET', '', '', true),
        doTransAPI('/kota/tujuan/list', 'GET', '', '', true),
        doTransAPI('/umrah/tourleader', 'GET', '', '', true)
    ];

    Swal.fire({
        title   : "Data Sedang Dimuat..",
    });
    Swal.showLoading();

    $("#mst_tourCode_date").daterangepicker({
        format      : 'DD/MM/YYYY',
        autoApply   : true,
    });

    Promise.all(doTrans)
        .then((success) => {
            $("#"+idModal).modal({ backdrop : 'static', keyboard : false });
            const data_program      = success[0].data.program;
            const data_pembimbing   = success[1].data.pembimbing;
            const data_rute         = success[2].data.rute;
            const data_kota_tujuan  = success[3].data.kota;
            const data_tour_leader  = success[4].data.tourleader;

            showSelect('mst_tourCode_program', data_program, '', true);
            showSelect('mst_tourCode_mentor', data_pembimbing, '', true);
            showSelect('mst_tourCode_route', data_rute, '', true);
            showSelect('mst_tourCode_destination', data_kota_tujuan, '', true);
            showSelect('mst_tourCode_tourLeader', data_tour_leader, '', true);

            $("#mst_tourCode_cost41").on('keyup', () => {
                const quadCostDollar    = $("#mst_tourCode_cost41").val() != '' ? formatRibuan($("#mst_tourCode_cost41").val()) : '';
                $("#mst_tourCode_cost41").val(quadCostDollar);
            });

            $("#mst_tourCode_cost41").on('blur', () => {
                const quadCostDollar    = $("#mst_tourCode_cost41");
                quadCostDollar.val() == '' ? quadCostDollar.val(0) : quadCostDollar.val(quadCostDollar.val());
            });

            $("#mst_tourCode_cost31").on('keyup', () => {
                const tripleCostDollar  = $("#mst_tourCode_cost31").val() != '' ? formatRibuan($("#mst_tourCode_cost31").val()) : '';
                $("#mst_tourCode_cost31").val(tripleCostDollar);
            });

            $("#mst_tourCode_cost31").on('blur', () => {
                const tripleCostDollar    = $("#mst_tourCode_cost31");
                tripleCostDollar.val() == '' ? tripleCostDollar.val(0) : tripleCostDollar.val(tripleCostDollar.val());
            });

            $("#mst_tourCode_cost21").on('keyup', () => {
                const doubleCostDollar      = $("#mst_tourCode_cost21").val() != '' ? formatRibuan($("#mst_tourCode_cost21").val()) : '';
                $("#mst_tourCode_cost21").val(doubleCostDollar);
            });

            $("#mst_tourCode_cost21").on('blur', () => {
                const doubleCostDollar    = $("#mst_tourCode_cost21");
                doubleCostDollar.val() == '' ? doubleCostDollar.val(0) : doubleCostDollar.val(doubleCostDollar.val());
            });

            $("#mst_tourCode_cost42").on('keyup', () => {
                const quadCostDollar    = $("#mst_tourCode_cost42").val() != '' ? formatRibuan($("#mst_tourCode_cost42").val()) : '';
                $("#mst_tourCode_cost42").val(quadCostDollar);
            });

            $("#mst_tourCode_cost42").on('blur', () => {
                const quadCostDollar    = $("#mst_tourCode_cost42");
                quadCostDollar.val() == '' ? quadCostDollar.val(0) : quadCostDollar.val(quadCostDollar.val());
            });

            $("#mst_tourCode_cost32").on('keyup', () => {
                const tripleCostDollar  = $("#mst_tourCode_cost32").val() != '' ? formatRibuan($("#mst_tourCode_cost32").val()) : '';
                $("#mst_tourCode_cost32").val(tripleCostDollar);
            });

            $("#mst_tourCode_cost32").on('blur', () => {
                const tripleCostDollar    = $("#mst_tourCode_cost32");
                tripleCostDollar.val() == '' ? tripleCostDollar.val(0) : tripleCostDollar.val(tripleCostDollar.val());
            });

            $("#mst_tourCode_cost22").on('keyup', () => {
                const doubleCostDollar      = $("#mst_tourCode_cost22").val() != '' ? formatRibuan($("#mst_tourCode_cost22").val()) : '';
                $("#mst_tourCode_cost22").val(doubleCostDollar);
            });

            $("#mst_tourCode_cost22").on('blur', () => {
                const doubleCostDollar    = $("#mst_tourCode_cost22");
                doubleCostDollar.val() == '' ? doubleCostDollar.val(0) : doubleCostDollar.val(doubleCostDollar.val());
            });

            $("#mst_tourCode_date").on('apply.daterangepicker', (e, ev) => {
                const tgl_awal  = $("#mst_tourCode_date").val().split(' - ')[0];
                const tgl_akhir = $("#mst_tourCode_date").val().split(' - ')[1];

                const lamanya   = moment(tgl_akhir, 'DD/MM/YYYY').diff(moment(tgl_awal, 'DD/MM/YYYY'), 'days');
                $("#mst_tourCode_duration").val(parseInt(lamanya));

            })

            Swal.close();
        })
        .catch((err)    => {
            $("#"+idModal).modal({ backdrop : 'static', keyboard : false });
            showSelect('mst_tourCode_program', '', '', true);
            showSelect('mst_tourCode_mentor', '', '', true);
            showSelect('mst_tourCode_route', '', '', true);
            showSelect('mst_tourCode_destination', '', '', true);
            showSelect('mst_tourCode_tourLeader', '', '', true);
            Swal.close();
        })

}

function closeModalTourCode(idModal)
{
    $("#"+idModal).modal('hide');

    if(idModal == 'modalFormV2')
    {
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#tourCode_programID").prop('disabled', false);
        })
    }
}


function simpanProgramV2(jenis)
{
    // VALIDATE
    if($("#mst_tourCode_program").val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Program Tidak Boleh Kosong',
            didClose : () => {
                $("#mst_tourCode_program").select2('open');
            }
        });
    } else if($("#mst_tourCode_capacity").val() == '') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Kapasitas Tidak Boleh Kosong',
            didClose    : () => {
                $("#mst_tourCode_capacity").focus();
            }
        })
    } else if($("#mst_tourCode_destination").val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Tujuan Harus Dipilih',
            didClose    : () => {
                $("#mst_tourCode_destination").select2('open')
            }
        })
    } else if($("#mst_tourCode_route").val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Rute Harus Dipilih',
            didClose    : () => {
                $("#mst_tourCode_route").select2('open')
            }
        })
    } else if($("#mst_tourCode_mentor").val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Pembimbing Harus Dipilih',
            didClose    : () => {
                $("#mst_tourCode_mentor").select2('open');
            }
        })
    } else if($("#mst_tourCode_tourLeader").val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Pilih Tour Leader',
            didClose    : () => {
                $("#mst_tourCode_tourLeader").select2('open')
            }
        })
    } else {
        const url   = "/umrah/jadwal/save";
        const type  = "POST";
        const data  = {
            "program"           : $("#mst_tourCode_program option:selected").text(),
            "kapasitas"         : $("#mst_tourCode_capacity").val(),
            "tgl_berangkat"     : moment($("#mst_tourCode_date").val().split(' - ')[0], 'DD/MM/YYYY').format('DD-MM-YYYY'),
            "pembimbing"        : $("#mst_tourCode_mentor option:selected").text(),
            "lama"              : $("#mst_tourCode_duration").val(),
            "tujuan"            : $("#mst_tourCode_destination option:selected").text().split(' - ')[0],
            "rute"              : $("#mst_tourCode_route option:selected").text(),
            "tgl_pulang"        : moment($("#mst_tourCode_date").val().split(' - ')[1], 'DD/MM/YYYY').format('DD-MM-YYYY'),
            "tourleader"        : $("#mst_tourCode_tourLeader option:selected").text(),
            "quad_cost_dolar"   : $("#mst_tourCode_cost41").val().replace(/,/g,''),
            "triple_cost_dolar" : $("#mst_tourCode_cost31").val().replace(/,/g,''),
            "double_cost_dolar" : $("#mst_tourCode_cost21").val().replace(/,/g,''),
            "quad_cost_rupiah"  : $("#mst_tourCode_cost42").val().replace(/,/g,''),
            "triple_cost_rupiah": $("#mst_tourCode_cost32").val().replace(/,/g,''),
            "double_cost_rupiah": $("#mst_tourCode_cost22").val().replace(/,/g,''),
            "note"              : $("#mst_tourCode_note").val(),
            "created_by"        : $("#mst_tourCode_createdBy").val(),
        };

        const sendData  = [
            doTransAPIV2(url, type, data, '', true),
        ];

        Swal.fire({
            title   : 'Data Sedang Diproses',
        });
        Swal.showLoading();

        Promise.all(sendData)
            .then((success) => {
                Swal.fire({
                    icon    : 'success', 
                    title   : 'Berhasil',
                    text    : success.data.message,
                }).then((results)   => {
                    if(results.isConfirmed) {
                        closeModal('modalShowTourCode');
                        showTable('table_program_umrah');
                    }
                });
            })
            .catch((err)    => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak Bisa Menyimpan Data',
                });
            })
    }
}

function doTrans(url, type, data, customMessage, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            cache   : false,
            type    : type,
            data    : {
                _token  : CSRF_TOKEN,
                sendData: data,
            },
            url     : url,
            beforeSend   : function() {
                customMessage;
            },
            async   : isAsync,
            success : function(xhr) {
                resolve(xhr);
            },
            error   : function(xhr) {
                reject(xhr);
            }
        })
    });
}

function doTransAPI(url, type, data, customMessage, isAsync)
{
    var url_api     = 'https://api-percik.perciktours.com/api';
    var url_header  = {
        "x-api-key" : 'YjIzMTE5NTg1ZDQ1MDJiYWMyMTJmMDZhZDAxMGY1MjM4NWNhOTQxOQ==',
    };
    return new Promise((resolve, reject)    => {
        $.ajax({
            isAsync : isAsync,
            url     : url_api+""+url,
            headers : url_header,
            type    : type,
            dataType: "json",
            beforeSend : () => {
                customMessage;
            },
            success     : (success) => {
                resolve(success);
            },
            error       : (err)     => {
                reject(err);
            }
        })
    })
}

function doTransAPIV2(url, type, data, customMessage, isAsync)
{
    var url_api     = 'https://api-percik.perciktours.com/api';
    var url_header  = {
        "x-api-key" : 'YjIzMTE5NTg1ZDQ1MDJiYWMyMTJmMDZhZDAxMGY1MjM4NWNhOTQxOQ==',
    };
    return new Promise((resolve, reject)    => {
        $.ajax({
            isAsync : isAsync,
            url     : url_api+""+url,
            headers : url_header,
            type    : type,
            dataType: "json",
            data    : data, 
            beforeSend : () => {
                customMessage;
            },
            success     : (success) => {
                resolve(success);
            },
            error       : (err)     => {
                reject(err);
            }
        })
    })
}

function formatRibuan(val)
{
    if(typeof val === 'undefined') {
        return null;
    } else {
        var number_string   = val.replace(/[^.\d]/g, '').toString(),
        split               = number_string.split('.'),
        sisa                = split[0].length % 3,
        sisa                = split[0].length % 3,
        angka_hasil         = split[0].substr(0, sisa),
        ribuan              = split[0].substr(sisa).match(/\d{3}/gi);

        if(ribuan){
            separator = sisa ? ',' : '';
            angka_hasil += separator + ribuan.join(',');
        }

        angka_hasil = split[1] != undefined ? angka_hasil + '.' + split[1] : angka_hasil;
        return angka_hasil; 
    }
}