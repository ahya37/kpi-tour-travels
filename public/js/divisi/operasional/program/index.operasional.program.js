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
    if(idModal == 'modalForm') {
        $("#"+idModal).modal('hide');
        $("#"+idModal).on('hidden.bs.modal', function(){
            $(".programDate").val(moment().format('DD/MM/YYYY'));
            $("input[type='text']").val(null);
            $("#btnSimpan").val(null);
            
            $("#btnDelete").prop('disabled', false);
            $("#btnDelete").show();
            $("#btnBatal").prop('disabled', false);
            $("#btnSimpan").prop('disabled', false);
        });
    } else if(idModal == 'modalFormV2') {
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
                    html    += "<option value='" + item.KODE + "'>" + item.KODE + "</option>";
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
            doTransAPI('/umrah/tourcode?year=2024', 'GET', '', message, true),
            doTrans('/master/data/getProgramUmrah/umrah', 'GET', dataProgram, '', true),
            jenis == 'edit' ? doTrans('/divisi/operasional/program/getDataJadwalUmrah', 'GET', detailProgramUmrah, '', true) : '',
        ];
        Promise.all(getData)
            .then((success) => {
                // DATA API
                const tour_code_api     = success[0].data.jadwal;
                const list_program      = success[1].data;
                const detail_program    = jenis == 'edit' ? success[2].data[0] : '';
                
                $("#"+idModal).modal({ backdrop : 'static', keyboard: false });
                
                showSelect('tourCode_id', tour_code_api, '', true);
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
        var message     = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();
        var url         = "/umrah/tourcode?year=2024&tourcode="+value;
        var getData     = [
            doTransAPI(url, 'GET', '', message, true)
        ];

        Promise.all(getData)
            .then((success) => {
                setTimeout(() => {
                    // console.log(success[0].data.jadwal);
                    // FILL FORM
                    const data  = success[0].data.jadwal;

                    $("#tourCode_dptDate").val(moment(data.BERANGKAT).format('DD/MM/YYYY'));
                    $("#tourCode_arvDate").val(moment(data.PULANG).format('DD/MM/YYYY'));
                    $("#tourCode_mentorName").val(data.PEMBIMBING);
                    $("#tourCode_programID").val(data.ERP_PROGRAM_ID).trigger('change');
                    Swal.close();
                }, 2000);
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