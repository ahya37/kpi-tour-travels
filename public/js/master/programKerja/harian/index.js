Dropzone.autoDiscover = false;
$(document).ready(function(){
    const data  = {
        "current_month" : $("#filterHarianBulan").val(),
        "current_role"  : $("#filterHarianRole").val(),
    };
    showTable('tableListHarian', data);
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

var current_role    = $("#currentRole").val();
var site_url        = window.location.pathname;

var isClick         = 0;
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

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'tableListHarian') {
        // console.log(data);
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
                data    : {
                    "current_month"     : data.current_month,
                    "current_role"      : data.current_role,
                }
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
    $("#btnSimpan").val(jenis);
    showTable('tableListFile');
    
    // CURRENT ROLE
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
        $("#"+idModal).modal({backdrop: 'static', keyboard: false});
        $("#"+idModal).modal('show');

        
        showSelect('programKerjaBulananAktivitas','','');

        // CHECK APAKAH CURRENT ROLE == 'UMUM'?
        if(current_role == 'umum') {
            showSelect('programKerjaHarianDivisi', '%', '', true);
            showSelect('programKerjaTahunanID', '', '', true);
            showSelect('programKerjaBulananID','','', true);
            showSelect('prograKerjaHarianPIC', '', '', true);
        } else {
            showSelect('programKerjaBulananID','%','', true);
        }

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
                $("#"+idModal).modal({backdrop: 'static', keyboard: false});
                $("#"+idModal).modal('show');
                var header  = xhr.data.header[0];
                var detail  = xhr.data.detail;

                console.log(header);

                // UPDATE HEADER
                $("#programKerjaHarianTanggal").data('daterangepicker').setStartDate(moment(header.pkh_date, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                $("#programKerjaHarianTanggal").data('daterangepicker').setEndDate(moment(header.pkh_date, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                $("#programKerjaHarianWaktuMulai").val(header.pkh_start_time);
                $("#programKerjaHarianWaktuAkhir").val(header.pkh_end_time);
                $("#programKerjaHarianJudul").val(header.pkh_title);
                
                if(current_role == 'umum') {
                    showSelect('programKerjaHarianDivisi', '%', header.pkh_gd_id, false);
                    showSelect('prograKerjaHarianPIC', header.pkh_gd_id, header.pkh_employee_id, false);
                    // showSelect('programKerjaTahunanID', '', '', false);
                    // showSelect('programKerjaBulananID','', '', false);
                } else {
                    showSelect('programKerjaBulananID', '%', header.pkb_id, false);
                    showSelect('programKerjaBulananAktivitas', header.pkb_id, header.pkbd_id, false);
                }

                // UPDATE DETAIL
                if( detail.length > 0 ) {
                    for(var i = 0; i < detail.length; i++) {
                        var seq         = i + 1;
                        var namaFile    = detail[i]['pkhf_name'].length > 80 ? detail[i]['pkhf_name'].substring(0, 80) + "..." : detail[i]['pkhf_name'];
                        var pathFile    = detail[i]['pkhf_path'].split('/')[1];
                        var dataFile    = value + " | " + seq;
                        var button_delete       = "<button class='btn btn-sm btn-danger' type='button' value='"+dataFile+"'><i class='fa fa-trash'></i></button>";
                        var url         = getURL() + "/downloadFile/" + pathFile;

                        $("#tableListFile").DataTable().row.add([
                            seq,
                            "<a href='"+url+"' title='"+detail[i]['pkhf_name']+"' target='_blank'>" + namaFile + "</a>",
                            button_delete
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
            if(isClick == 1) {
                showDropzone.removeAllFiles(true);
            } 
            $(".waktu").val("00:00:00");
            $("#programKerjaHarianJudul").val(null);
        });

        if(isClick == 0) {
            showDropzone.removeAllFiles(true);
        }
    } else if(jenis == 'edit') {
        $("#"+idModal).on('hidden.bs.modal', function(){
            $("#programKerjaHarianID").val(null);
        });
    }
}

function showSelect(idSelect, valueCari, valueSelect, isAsync)
{
    var jenisTrans    = $("#btnSimpan").val();
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });

    if(idSelect == 'filterHarianBulan') {
        var html    = [
            "<option selected disabled>Pilih Bulan</option>",
            "<option value='01'>Januari</option>",
            "<option value='02'>Februari</option>",
            "<option value='03'>Maret</option>",
            "<option value='04'>April</option>",
            "<option value='05'>Mei</option>",
            "<option value='06'>Juni</option>",
            "<option value='07'>Juli</option>",
            "<option value='08'>Agustus</option>",
            "<option value='09'>September</option>",
            "<option value='10'>Oktober</option>",
            "<option value='11'>November</option>",
            "<option value='12'>Desember</option>"
        ];
        $("#"+idSelect).html(html);

        if(valueSelect != '') {
            $("#"+idSelect).val(valueSelect).trigger('change');
        }

    } else if(idSelect == 'filterHarianRole') {
        var url     = "/master/data/trans/get/dataRoles";
        var type    = "GET";
        var html    = "<option selected disabled>Role</option>";

        transData(url, type, '', '', isAsync)
            .then(function(success){
                var getData     = success.data;
                $.each(getData, function(i,item){
                    var role_id     = item.role_name == 'admin' ? '%' : item.role_name;
                    var role_name   = item.role_name == 'admin' ? 'semua' : item.role_name;
                    html    += "<option value='"+ role_id +"'>" + role_name + "</option>";
                });

                $("#"+idSelect).html(html);

                if(valueSelect != '') {
                    $("#"+idSelect).val(valueSelect).trigger('change');
                }

            })
            .catch(function(err){
                $("#"+idSelect).html(html);
                console.log(err.responseJSON);
            })
    } else if(idSelect == 'programKerjaHarianDivisi') {
        var html    = "<option selected disabled>Pilih Group Divisi</option>";

        // GET DATA
        const url   = "/master/data/trans/get/groupDivision";
        const type  = "GET";

        transData(url, type, valueCari, '', true)
            .then((success) => {
                var data    = success.data;
                $.each(data, function(i,item){
                    var groupDivision_name  = item.name;
                    var groupDivision_id    = item.id;

                    html    += "<option value='" + groupDivision_id + "'>" + groupDivision_name + "</option>";
                })
                $("#"+idSelect).html(html);
                
                if(valueSelect != '') {
                    $("#"+idSelect).val(valueSelect);
                }

                $("#"+idSelect).on('change', function(){
                    showSelect('prograKerjaHarianPIC', this.value, '', true);
                    showSelect('programKerjaTahunanID', this.value, '', true);
                });
            })
            .catch((err) => {
                console.log(err);
            })

        $("#"+idSelect).html(html);
    } else if(idSelect == 'prograKerjaHarianPIC') {
        var html    = "<option selected disabled>Pilih PIC / Penanggung Jawab</option>";

        if(valueCari != '') {
            var url     = "/master/programkerja/bulanan/getDataPICByGroupDivisionID";
            var data    = {'GroupDivisionID'   : valueCari};
            var type    = "GET";
            
            if(isAsync === true) {
                var message     = Swal.fire({title:'Data Sedang Dimuat'});Swal.showLoading();
            } else {
                var message     = "";
            }
            
            transData(url, type, data, message, isAsync)
                .then((success)=>{
                    $.each(success.data, function(i,item){
                        const pic_id    = item.employee_id;
                        const pic_name  = item.employee_name;

                        html    += "<option value='" +pic_id+ "'>" + pic_name + "</option>";
                    })
                    $("#"+idSelect).html(html);

                    if(valueSelect != '') {
                        // JIKA VALUE SELECT != NULL MAKA ISI VALUE SELECT
                        $("#"+idSelect).val(valueSelect).trigger('change');
                    } else {
                        // JIKA VALUE SELECT == '' MAKA OPEN SELECT2 INI
                        $("#"+idSelect).select2('open');
                    }
                    Swal.close();

                    $("#"+idSelect).on('change', function() {
                        $("#programKerjaTahunanID").select2('open');
                    })
                })
                .catch((xhr)=>{
                    Swal.close();
                })
        }
        $("#"+idSelect).html(html);
    } else if(idSelect == 'programKerjaTahunanID') {
        // RESET FORM
        showSelect('programKerjaBulananAktivitas', '', '', true);
        showSelect('programKerjaBulananID', '', '', true);
        $("#programKerjaBulananAktivitasText").val(null);

        // SHOW FORM
        var html    = "<option selected disabled>Pilih Program Kerja Tahunan</option>";

        if(valueCari != '' ) {
            var url   = site_url + "/getProgramKerjaTahunan/"+valueCari;

            transData(url, "GET", '', '', isAsync)
            .then((success)=>{
                $.each(success.data, function(i,item){
                    html    += "<option value='" + item.pkt_id + "'>" + item.pkt_title + "</option>";
                });

                $("#"+idSelect).html(html);

                if(valueSelect != '') {
                    $("#"+idSelect).val(valueSelect).trigger('change');
                }
            })
            .catch((err)=> {
                $("#"+idSelect).html(html);
            })
        } else {
            $("#"+idSelect).html(html);
        }

        $("#"+idSelect).on('change', function(){
            showSelect('programKerjaBulananID', this.value, '', true);
        });

        // -- NOTE : BUG DI MDOAL TIDAK MAU SCROLL MODAL
        // $("#"+idSelect).on('change', function(){
        //     $("#programKerjaBulananID").select2('open');
        // })
    } else if(idSelect == 'programKerjaBulananID') {
        // RESET FORM
        showSelect('programKerjaBulananAktivitas', '', '', true);
        $("#programKerjaBulananAktivitasText").val(null);
        
        if(current_role != 'umum') {
            var html    = [
                "<option selected disabled>Pilih Program Bulanan</option>",
            ];
            
            // GET DATA
            var url     = getURL()+"/cariDataProkerBulanan";
            var data    = {
                "pkb_uuid"  : valueCari,
            };
            var type    = "GET";
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
                    Swal.close();
                    var getDataHeader     = xhr.data.header;
                    $.each(getDataHeader, function(i,item){
                        html    += "<option value='" + item['pkb_uuid'] + "'>  [" + moment(item['pkb_date'], 'YYYY-MM-DD').format('DD-MM-YYYY') + "] "+ item['pkb_title'] +"</option>";
                    })
    
                    if(current_role == 'umum') {
                        html    += "<option value='Lainnya'>Lainnya</option>";
                    }
    
                    $("#"+idSelect).html(html);
    
                    if(valueSelect != '') {
                        $("#"+idSelect).val(valueSelect);
                    } else {
                        $("#"+idSelect).select2('open');
                    }
                })
                .catch(function(xhr){
                    Swal.close();
                    $("#"+idSelect).html(html);
                });
        } else if(current_role == 'umum') {
            var html    = "<option selected disabled>Pilih Program Kerja Bulanan</option>";

            if(valueCari != '') {
                var url   = site_url + "/getProgramKerjaBulanan/"+valueCari;
                var type  = "GET";
                // if(isAsync === true) {
                //     var message = Swal.fire({title : 'Data Sedang Dimuat'});Swal.showLoading();
                // } else {
                //     var message = "";
                // }
                
                transData(url, type, '', '', isAsync)
                    .then((success)=> {
                        
                        $.each(success.data, function(i,item){
                            var tgl     = moment(item.pkb_date, 'YYYY-MM-DD').format('DD-MM-YYYY');
                            var text    = "["+tgl+"] "+item.pkb_title;
                            var id      = item.pkb_id;

                            html        += "<option value='" + id + "'>" + text + "</option>";
                        })

                        html    += "<option value='Lainnya'>Lainnya</option>";

                        $("#"+idSelect).html(html);
                        // CLOSE MODAL
                        Swal.close();

                        if(valueSelect != '') {
                            $("#"+idSelect).val(valueSelect).trigger('change');
                        }
                    })
                    .catch((error)=> {
                        console.log(error.responseJSON);
                        $("#"+idSelect).html(html);
                    })
            } else {
                $("#"+idSelect).html(html);
            }

            $("#"+idSelect).on('select2:select', function(){
                $("#programKerjaBulananAktivitasText").focus();
            })
        }
    } else if(idSelect == 'programKerjaBulananAktivitas') {
        var html    = "<option selected disabled>Jenis Pekerjaan</option>";

        if(valueCari != '') {
            // console.log({idSelect, valueCari, valueSelect, isAsync});
            if(valueCari != 'Lainnya') {
                $("#formProgramKerjaBulananAktivitas").show();
                $("#formProgramKerjaBulananAktivitasText").hide();
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
                            $("#"+idSelect).select2('open');
                        }

                        if(valueSelect != '') {
                            $("#"+idSelect).val(valueSelect);
                        }
                    })
                    .catch(function(xhr){
                        Swal.close();
                        console.log(xhr);
                    });
                $("#"+idSelect).html(html);
                $("#"+idSelect).on('select2:select', function(){
                    $("#programKerjaHarianJudul").focus();
                })
            } else {
                $("#formProgramKerjaBulananAktivitas").hide();
                $("#formProgramKerjaBulananAktivitasText").show();
                $("#programKerjaBulananAktivitasText").focus();
            }
        } else {
            $("#"+idSelect).html(html);
        }
    }
}


function doSimpan(jenis) 
{
    var programKerjaHarian_startDate        = $("#programKerjaHarianTanggal");
    var programKerjaHarian_startTime        = $("#programKerjaHarianWaktuMulai");
    var programKerjaHarian_endTime          = $("#programKerjaHarianWaktuAkhir");
    var programKerjaHarian_pkbID            = $("#programKerjaBulananID");
    var programKerjaHarian_pkbSeq           = $("#programKerjaBulananAktivitas");
    var programKerjaHarian_description      = $("#programKerjaHarianJudul");
    var programKerjaHarian_id               = $("#programKerjaHarianID");
    var programKerjaHarian_text             = $("#programKerjaBulananAktivitasText");
    var programKerjaHarian_prokerTahunanID  = $("#programKerjaTahunanID");
    var programKerjaHarian_divisi           = $("#programKerjaHarianDivisi");
    var programKerjaHarian_divisi_pic       = $("#prograKerjaHarianPIC");
    var programKerjaHarian_file             = penampung;

    if(current_role == 'umum' && programKerjaHarian_divisi.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Divisi Harus Dipilih'
        }).then((results)=>{
            programKerjaHarian_divisi.select2('open')
        })
    } else if(current_role == 'umum' && programKerjaHarian_divisi_pic.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'PIC Harus Dipilih'
        }).then((results)=>{
            programKerjaHarian_divisi_pic.select2('open')
        })
    } else if(current_role == 'umum' && programKerjaHarian_prokerTahunanID.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Program Kerja Tahunan Harus Dipilih'
        }).then((results)=>{
            if(results.isConfirmed) {
                programKerjaHarian_prokerTahunanID.select2('open');
            }
        })
    } else if(programKerjaHarian_pkbID.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Program Bulanan Harus Dipilih',
        }).then((results)   => {
            if(results.isConfirmed) {
                programKerjaHarian_pkbID.select2('open');
            }
        })
    } else if(programKerjaHarian_pkbSeq.val() == null && programKerjaHarian_pkbID.val() != 'Lainnya') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Jenis Pekerjaan Harus Dipilih',
        }).then((results)   => {
            if(results.isConfirmed) {
                programKerjaHarian_pkbSeq.select2('open');
            }
        })
    } else if(programKerjaHarian_pkbID.val() == 'Lainnya' && programKerjaHarian_text.val() == '') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Jenis Pekerjaan Harus Diisi'
        }).then((results)=>{
            if(results.isConfirmed) {
                programKerjaHarian_text.focus();
            }
        })
    } else if(programKerjaHarian_description.val() == '') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : '',
        }).then((results)   => {
            if(results.isConfirmed) {
                programKerjaHarian_description.focus();
            }
        })
    } else {
        var dataSimpan  = {
            "programKerjaHarian_ID"             : programKerjaHarian_id.val(),
            "programKerjaHarian_startDate"      : moment(programKerjaHarian_startDate.val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
            "programKerjaHarian_startTime"      : programKerjaHarian_startTime.val(),
            "programKerjaHarian_endTime"        : programKerjaHarian_endTime.val(),
            "programKerjaHarian_pkbID"          : programKerjaHarian_pkbID.val(),
            "programKerjaHarian_pkbSeq"         : programKerjaHarian_pkbSeq.val(),
            "programKerjaHarian_description"    : programKerjaHarian_description.val(),
            "programKerjaHarian_file"           : penampung.length > 0 ? penampung : [],
            "programKerjaHarian_jenisTrans"     : jenis,
            "programKerjaHarian_act_text"       : programKerjaHarian_text.val(),
            "programKerjaHarian_pktID"          : current_role == 'umum' ? programKerjaHarian_prokerTahunanID.val() : '',
            "programKerjaHarian_gdID"           : current_role == 'umum' ? programKerjaHarian_divisi.val() : '',
            "programKerjaHarian_gd_picID"       : current_role == 'umum' ? programKerjaHarian_divisi_pic.val() : '',
        };
        
        var url         = getURL() + "/doSimpanTransHarian";
        var type        = "POST";
        var isAsync     = true;
        var customMessage       = Swal.fire({title:'Data Sedang Diproses'});Swal.showLoading();
        transData(url, type, dataSimpan, customMessage, isAsync)
            .then(function(xhr){
                isClick = 1;
                Swal.fire({
                    icon    : xhr.alert.icon,
                    title   : xhr.alert.message.title,
                    text    : xhr.alert.message.text,
                }).then(function(results){
                    if(results.isConfirmed) {
                        closeModal('modalForm')
                        isClick = 0;
                        showTable('tableListHarian', '%');
                    }
                })
            })
            .catch(function(xhr){
                isClick     = 0;
                Swal.fire({
                    icon    : xhr.responseJSON.alert.icon,
                    title   : xhr.responseJSON.alert.message.title,
                    text    : xhr.responseJSON.alert.message.text,
                });
            })
    }
}

function showFilter() {
    var current_month   = moment().format('MM');
    var current_role    = $("#currentRole").val() == 'admin' ? '%' : $("#currentRole").val();
    showSelect('filterHarianBulan', '', current_month, false);
    showSelect('filterHarianRole','', current_role, true);
}

function showFilteredData()
{
    var selectedMonth   = $("#filterHarianBulan").val();
    var selectedRole    = $("#filterHarianRole").val();

    const data          = {
        "current_month" : selectedMonth,
        "current_role"  : selectedRole,
    };

    showTable('tableListHarian', data);
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
            error   : function(xhr) {
                reject(xhr);
                console.log(xhr);
            }
        });
    })
}