$(document).ready(function(){
    console.log('test');
    // current_month
    var currMonth   = moment().format('MM');
    var currYear    = moment().format('YYYY');
    var currPaket   = '%';
    showSelect('programFilterBulan', '%', '%', '');
    showSelect('programFilterTahun', '%', currYear, '');
    showSelect('programFlterPaket', '%', currPaket, true);

    var inputCurrMonth  = $("#programFilterBulan").val();

    showTable('table_program_umrah', [inputCurrMonth, currYear, '%', currPaket]);

    $("#programFilterBtnCari").on('click', function(){
        var selectedMonth   = $("#programFilterBulan").val();
        var selectedYear    = $("#programFilterTahun").val();
        var selectedPaket   = $("#programFlterPaket").val();
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
                { targets: [0, 5], className: "text-center", width: "7%" },
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
                url     : site_url + '/listJadwalumrah',
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
    } else if(jenis == 'edit') {
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
    $("#"+idModal).modal('hide');
    $("#"+idModal).on('hidden.bs.modal', function(){
        $(".programDate").val(moment().format('DD/MM/YYYY'));
        $("input[type='text']").val(null);
        $("#btnSimpan").val(null);
    });
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
    } else if(idSelect == 'programFlterPaket') {
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