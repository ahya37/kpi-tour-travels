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

function showTable(idTable)
{
    if(idTable == 'tableListHarian') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "zeroRecords"   : "Data Tidak ada, Silahkan tambahkan beberapa data..",
                "emptyTable"    : "Data Tidak ada, Silahkan tambahkan beberapa data..",
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
            },
            processing  : true,
            serverSide  : false,
        });
    }
}

var penampung    = [];

function showDropzone(idDropzone)
{
    var uploadSize      = 25; // megabyte
    var jenisFile       = ".jpg, .jpeg, .png, .docx, .doc, .xls, .xlsx, .pdf"; 
    $("#"+idDropzone).dropzone({
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
            this.on("success", function(file, response){
                penampung.push(response);
            });

            this.on('removedfile', function(file){
                var fileName    = file.name;
                penampung   = penampung.filter(function(response){
                    return response.filename !== fileName;
                });
            });
        }
        // success     : function(xhr) {
        //     console.log(xhr);
        // },
        // error   : function(file, errorMessage) {
        //     console.log(file, errorMessage);
        // }
    });
}

function showModal(idModal)
{
    $("#"+idModal).modal({backdrop: 'static', keyboard: false});
    $("#"+idModal).modal('show');

    if(idModal == 'modalForm')
    {
        $("#btnSimpan").val('add');
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
        showSelect('programKerjaBulananID','%','', true);
        showSelect('programKerjaBulananAktivitas','','');
        showDropzone('myDropzone');

        // $("#btnSimpan").on('click', function(){
        //     doSimpan('add', myDropZone);
        // })
        // var myDropzone    : new Dropzone('#myDropzone', {
        //     url     : getURL()+"/fileUpload",
        //     method  : "POST",
        //     headers  : {
        //         'X-CSRF-TOKEN' : CSRF_TOKEN,
        //     },
        //     addRemoveLinks  : true,
        //     maxFilesize     : maxFile,
        //     acceptedFiles   : ".jpeg, .jpg, .doc, .docx, .xls, .xlsx, .pdf",
        //     dictDefaultMessage  : "Drag n Drop File disini atau klik untuk mencari data yang akan diupload..",
        //     dictFileTooBig      : "Ukuran File Melebihi Batas Max. Unggah. Batas Max. Ukuran unggah adalah "+maxFile+"MB",
        //     autoProcessQueue    : false,
        // });
    }
}

function closeModal(idModal)
{
    $("#"+idModal).modal('hide');
    $("#"+idModal).on('hidden.bs.modal', function(){
        $("div.dropzone").removeAttr('id');
        penampung = [];
    });
    $("#myDropzone").dropzone().removeAllFiles();
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
                } else {
                    // DO NOTHING
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
                } else {
                    // DO NOTHING
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
                        html    += "<option value='"+(i+1)+"'>" + item['pkb_detail'] + "</option>";
                    })
                    $("#"+idSelect).html(html);
                    if(isAsync === true) {
                        Swal.close();
                    } else {
                        // DO NOTHING
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
                    } else {
                        // DO NOTHING
                    }
                    $("#"+idSelect).html(html);
                });
                $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    }
}


function doSimpan(value, myDropZone) 
{
    if(value == 'add') {
        console.log(penampung);
        // 
        // transData(url, type, data, customMessage, isAsync)
        //     .then(function(xhr){
        //         console.log(xhr)
        //     })
        //     .catch(function(xhr){
        //         console.log(xhr);
        //     });
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