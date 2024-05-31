Dropzone.autoDiscover = false;
$(document).ready(function(){
    console.log('test');
    showTable('tableListHarian');
});

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN
    }
});

function getURL()
{
    return window.location.pathname;
}

var penampung       = [];
var uploadSize      = 25; // megabyte
var jenisFile       = ".jpg, .jpeg, .png, .docx, .doc, .xls, .xlsx, .pdf"; 
var showDropzone    = new Dropzone("#myDropzone", {
    url             : getURL() + "/fileUpload",
    headers         : {
        "X-CSRF-TOKEN"  : CSRF_TOKEN,
    },
    beforeSend  : function() {
        Swal.fire({
            title : 'Data Sedang Diunggah',
        });
        Swal.showLoading();
    },
    addRemoveLinks  : true,
    maxFileSize     : uploadSize,
    parallelUploads : 5,
    acceptedFiles   : jenisFile,
    dictDefaultMessage  : "Tarik dan Lepaskan file disini atau klik untuk mencari data yang akan diunggah",
    dictFileTooBig      : "Ukuran file melebihi batas maksimal unaggah. Batas maksimal ukuran untuk unggh adalah "+uploadSize+"MegaByte",
    init            : function() {
        var dropzone    = this;
        this.on("success", function(file, response){
            penampung.push({"originalName":response['originalName'], "systemName":response['systeName'], "path":response['path']});
            // penampung.push(response['originalName']);
        });

        this.on('removedfile', function(file){
            // var index   = penampung.indexOf(file.name);
            for(var i = 0; i < penampung.length; i++) {
                if(penampung[i].originalName === file.name) {
                    var url     = getURL() + "/deleteUpload";
                    var data    = {
                        "path_files"    : penampung[i].path,
                    };
                    var type    = "POST";
                    transData(url, type, data, '','')
                        .then(function(xhr){
                            penampung.splice(i, 1);
                        })
                        .catch(function(xhr){
                            console.log(xhr);
                        });
                    break;
                }
            }
        });
    }
});

function showTable(idTable)
{
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'tableListHarian') {
        $("#"+idTable).DataTable({
            language    : {
                "zeroRecords"   : "Data Tidak ada, Silahkan tambahkan beberapa data..",
                "emptyTable"    : "Data Tidak ada, Silahkan tambahkan beberapa data..",
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
            },
            processing  : true,
            serverSide  : false,
            ajax        : {
                type    : "GET",
                dataType: "json",
                url     : getURL() + "/listTableProkerHarian",
            },
            autoWidth   : false,
            columnDefs  : 
            [
                {"targets" : [0], "className" : "text-center", "width" : "5%"},
                { "targets" : [1], "width" : "40%" },
                { "targets" : [2], "className" : "text-center", "width" : "10%" },
                {"targets" : [4], "className" : "text-center", "width" : "8%"},
            ],
        });
    } else if(idTable == 'tableListFile') {
        $("#"+idTable).DataTable({
            language    : {
                "zeroRecords"   : "Data Tidak ada, Silahkan tambahkan beberapa data..",
                "emptyTable"    : "Data Tidak ada, Silahkan tambahkan beberapa data..",
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
            },
            processing  : true,
            serverSide  : false,
            autoWidth   : false,
            bInfo       : false,
            ordering    : false,
            searching   : false,
            paging      : false,
            columnDefs  : [
                { "targets":[0, 2], "className":"text-center","width":"8%" },
            ],
        })
    }
}

function showModal(idModal, jenis, value)
{
    $("#"+idModal).modal({backdrop: 'static', keyboard: false});
    $("#"+idModal).modal('show');
    $("#btnSimpan").val(jenis);
    showTable('tableListFile');

    $("#programKerjaHarianTanggal").daterangepicker({
        singleDatePicker : true,
        locale : {
            format  : 'DD/MM/YYYY',
        },
        minYear     : moment().subtract(10, 'years'),
        maxYear     : moment().add(10, 'years'),
        autoApply    : true,
        showDropdowns: true,
    });

    $(".waktu").daterangepicker({
        singleDatePicker    : true,
        autoApply   : true,
        timePicker: true,
        timePicker24Hour: true,
        timePickerIncrement: 1,
        timePickerSeconds: true,
        locale: {
            format: 'HH:mm:ss'
        }
    }).on('show.daterangepicker', function (ev, picker) {
        picker.container.find(".calendar-table").hide();
    });
    showDropzone;
    
    if(jenis == 'add'){
        showSelect('programKerjaBulananID','%','', true);
        showSelect('programKerjaBulananAktivitas','','');  

        $("#formUpload").show();
        $("#formListUpload").hide();
    } else if(jenis == 'edit') {
        $("#formUpload").hide();
        $("#formListUpload").show();
        $("#programKerjaHarianID").val(value);
        var url     = getURL() + "/detailDataProkerHarian";
        var data    = {
            "pkh_id"    : value,
        };
        var type    = "GET";
        var isAsync = true;
        var customMessage   = Swal.fire({title:"Data Sedang Dimuat"});Swal.showLoading();

        transData(url, type, data, customMessage, isAsync)
            .then(function(xhr){
                Swal.close();
                var header  = xhr.data.header[0];
                var detail  = xhr.data.detail;

                // UPDATE HEADER
                $("#programKerjaHarianTanggal").val(moment(header.pkh_date, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                $("#programKerjaHarianWaktuMulai").val(header.pkh_start_time);
                $("#programKerjaHarianWaktuAkhir").val(header.pkh_end_time);
                $("#programKerjaHarianJudul").val(header.pkh_title);
                
                showSelect('programKerjaBulananID', '%', header.pkb_id, false);
                showSelect('programKerjaBulananAktivitas', header.pkb_id, header.pkbd_id, false);

                // UPDATE DETAIL
                if( detail.length > 0 ) {
                    for(var i = 0; i < detail.length; i++) {
                        var seq         = i + 1;
                        var namaFile    = detail[i]['pkhf_name'];
                        var pathFile    = detail[i]['pkhf_path'];
                        var dataFile    = value + " | " + seq;
                        var button      = "<button class='btn btn-sm btn-danger' type='button' value='"+dataFile+"'><i class='fa fa-trash'></i></button>";

                        $("#tableListFile").DataTable().row.add([
                            seq,
                            "<a href='#'>" + namaFile + "</a>",
                            button
                        ]).draw('false');
                    }
                }
            })
            .catch(function(xhr){
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : xhr.statusText,
                });
            });
    }
}

function closeModal(idModal)
{
    var jenis = $("#btnSimpan").val();
    $("#"+idModal).modal('hide');
    if(jenis == 'add')
    {
        $("#"+idModal).on('hidden.bs.modal', function(){
            penampung = [];
            showDropzone.removeAllFiles(true);
            $(".waktu").val("00:00:00");
            $("#programKerjaHarianJudul").val(null);
        });
    } else if(jenis == 'edit') {
        $("#"+idModal).on('hidden.bs.modal', function(){
            $("#programKerjaHarianID").val(null);
        });
    }
}

function showSelect(idSelect, valueCari, valueSelect, isAsync)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
    
    if(idSelect == 'programKerjaBulananID') {
        var html    = "<option selected disabled>Pilih Program Bulanan</option>";
        
        // GET DATA
        var url     = getURL()+"/cariDataProkerBulanan";
        var data    = {
            "pkb_uuid"  : valueCari,
        };
        var type    = "GET";
        var isAsync = isAsync;
        if(isAsync === true) {
            var message     =   Swal.fire({
                                    title    : 'Data Sedang Dimuat..',
                                });
                                Swal.showLoading();
        } else {
            var message     = "";
        }
        transData(url, type, data, message, isAsync)
            .then(function(xhr){
                var getDataHeader     = xhr.data.header;
                $.each(getDataHeader, function(i,item){
                    html    += "<option value='" + item['pkb_uuid'] + "'>  [" + moment(item['pkb_date'], 'YYYY-MM-DD').format('DD-MM-YYYY') + "] "+ item['pkb_title'] +"</option>";
                    
                })
                $("#"+idSelect).html(html);
                if(isAsync === true) {
                    Swal.close();
                }

                if(valueSelect != '') {
                    // $("#"+idSelect).html(valueSelect).trigger('change');
                    $("#"+idSelect).val(valueSelect);
                }
            })
            .catch(function(xhr){
                if(isAsync === true) {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : xhr.statusText,
                    });
                }
                $("#"+idSelect).html(html);
            });
            $("#"+idSelect).html(html);
    } else if(idSelect == 'programKerjaBulananAktivitas') {
        var html    = "<option selected disabled>Jenis Pekerjaan</option>";

        if(valueCari != '') {
            var url     = getURL()+"/cariDataProkerBulanan";
            var data    = {
                "pkb_uuid"  : valueCari,
            };
            var type    = "GET";
            var isAsync = isAsync;
            if(isAsync === true) {
                var message     =   Swal.fire({
                                        title    : 'Data Sedang Dimuat..',
                                    });
                                    Swal.showLoading();
            } else {
                var message     = "";
            }
            transData(url, type, data, message, isAsync)
            .then(function(xhr){
                var getDataDetail     = xhr.data.detail;
                $.each(getDataDetail, function(i,item){
                    html    += "<option value='"+item['pkbd_id']+"'>" + item['pkb_detail'] + "</option>";
                })
                $("#"+idSelect).html(html);
                if(isAsync === true) {
                    Swal.close();
                }

                if(valueSelect != '') {
                    $("#"+idSelect).val(valueSelect);
                }
            })
            .catch(function(xhr){
                console.log(xhr);
                if(isAsync === true) {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : xhr.statusText,
                    });
                }
                $("#"+idSelect).html(html);
            });
            $("#"+idSelect).html(html);
            $("#"+idSelect).on('select2:select', function(){
                $("#programKerjaHarianJudul").focus();
            })
        } else {
            $("#"+idSelect).html(html);
        }
    }
}


function doSimpan(jenis) 
{
    var programKerjaHarian_startDate    = $("#programKerjaHarianTanggal");
    var programKerjaHarian_startTime    = $("#programKerjaHarianWaktuMulai");
    var programKerjaHarian_endTime      = $("#programKerjaHarianWaktuAkhir");
    var programKerjaHarian_pkbID        = $("#programKerjaBulananID");
    var programKerjaHarian_pkbSeq       = $("#programKerjaBulananAktivitas");
    var programKerjaHarian_description  = $("#programKerjaHarianJudul");
    var programKerjaHarian_file         = penampung;

    if(programKerjaHarian_pkbID.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Program Bulanan harus dipilih',
        }).then((results)   => {
            if(results.isConfirmed) {
                programKerjaHarian_pkbID.select2('open');
            }
        })
    } else if(programKerjaHarian_pkbSeq.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Jenis Pekerjaan harus dipilih',
        }).then((results)   => {
            if(results.isConfirmed) {
                programKerjaHarian_pkbSeq.select2('open');
            }
        })
    } else if(programKerjaHarian_description.val() == '') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Tulis Deskripsi Pekrerjaan harian',
        }).then((results)   => {
            if(results.isConfirmed) {
                programKerjaHarian_description.focus();
            }
        })
    } else {
        var dataSimpan  = {
            "programKerjaHarian_startDate"      : moment(programKerjaHarian_startDate.val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
            "programKerjaHarian_startTime"      : programKerjaHarian_startTime.val(),
            "programKerjaHarian_endTime"        : programKerjaHarian_endTime.val(),
            "programKerjaHarian_pkbID"          : programKerjaHarian_pkbID.val(),
            "programKerjaHarian_pkbSeq"         : programKerjaHarian_pkbSeq.val(),
            "programKerjaHarian_description"    : programKerjaHarian_description.val(),
            "programKerjaHarian_file"           : penampung.length > 0 ? penampung : [],
            "programKerjaHarian_jenisTrans"     : jenis,
        };
        
        var url         = getURL() + "/doSimpanTransHarian";
        var type        = "POST";
        var isAsync     = true;
        var customMessage       = Swal.fire({title:'Data Sedang Diproses'});Swal.showLoading();
        transData(url, type, dataSimpan, customMessage, isAsync)
            .then(function(xhr){
                Swal.fire({
                    icon    : xhr.alert.icon,
                    title   : xhr.alert.message.title,
                    text    : xhr.alert.message.text,
                }).then(function(results){
                    if(results.isConfirmed) {
                        closeModal('modalForm')
                    }
                })
            })
            .catch(function(xhr){
                Swal.fire({
                    icon    : xhr.responseJSON.alert.icon,
                    title   : xhr.responseJSON.alert.message.title,
                    text    : xhr.responseJSON.alert.message.text,
                });
            })
    }
}

function transData(url, type, data, customMessage, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            async   : isAsync,
            cache   : false,
            type    : type,
            url     : url,
            dataType: "json",
            beforeSend  : function() {
                customMessage;
            },
            data    : {
                _token      : CSRF_TOKEN,
                sendData    : data,
            },
            success : function(xhr) {
                resolve(xhr);
            },
            reject  : function(xhr) {
                reject(xhr);
            }
        });
    })
}