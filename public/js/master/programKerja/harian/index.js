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
        showSelect('programKerjaBulananAktivitas','','', true);
        // CHECK APAKAH CURRENT ROLE == 'UMUM'?
        if(current_role == 'umum') {
            showSelect('programKerjaHarianDivisi', '%', '', false);
            showSelect('programKerjaTahunanID', '', '', true);
            showSelect('programKerjaBulananID','','', true);
            showSelect('programKerjaHarianPIC', '', '', true);
        } else {
            showSelect('programKerjaBulananID','%','', true);
        }

        $("#formUpload").show();
        $("#formListUpload").hide();

        $("#"+idModal).modal({backdrop: 'static', keyboard: false});
        $("#"+idModal).modal('show');

        Swal.close();
    } 
    else if(jenis == 'edit') {
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

                // UPDATE HEADER
                $("#programKerjaHarianTanggal").data('daterangepicker').setStartDate(moment(header.pkh_date, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                $("#programKerjaHarianTanggal").data('daterangepicker').setEndDate(moment(header.pkh_date, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                $("#programKerjaHarianWaktuMulai").val(header.pkh_start_time);
                $("#programKerjaHarianWaktuAkhir").val(header.pkh_end_time);

                $("#programKerjaTahunanID").prop('disabled', true);
                $("#programKerjaBulananID").prop('disabled', true);
                $("#programKerjaHarianDivisi").prop('disabled', true);
                
                if(current_role == 'umum') {

                    var header_pkb_id;
                    var header_pkbd_id;
                    var header_pkb_desc;
                    header.pkbd_id == 'Lainnya' ? header_pkb_id = header.pkbd_id : header_pkb_id = header.pkb_id;
                    header.pkbd_id == 'Lainnya' ? header_pkbd_id = header.pkh_title : header_pkbd_id = header.pkbd_id;
                    header.pkbd_id == 'Lainnya' ? header_pkb_desc = header.pkb_description : header_pkb_desc = header.pkh_title;
                    header.pkbd_id == 'Lainnya' ? $("#pkbID_Lainnya").val(header.pkb_id) : $("#pkbID_Lainnya").val('');

                    showSelect('programKerjaHarianDivisi', '%', header.pkh_gd_id, false);
                    showSelect('programKerjaHarianPIC', header.pkh_gd_id, header.pkh_employee_id, false);
                    showSelect('programKerjaTahunanID', header.pkh_gd_id, header.pkh_pkt_id, false);
                    showSelect('programKerjaBulananID', header.pkh_pkt_id, header_pkb_id, false);
                    showSelect('programKerjaBulananAktivitas', header_pkb_id, header_pkbd_id, false);

                    $("#programKerjaBulananAktivitasText").val(header.pkh_title);
                    $("#programKerjaHarianJudul").val(header_pkb_desc);
                } else {
                    
                    showSelect('programKerjaBulananID', '%', header.pkb_id, false);
                    showSelect('programKerjaBulananAktivitas', header.pkb_id, header.pkbd_id, false);

                    $("#programKerjaBulananAktivitasText").val(null);
                    $("#programKerjaHarianJudul").val(header.pkh_title);
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
    if(idModal == 'modalForm') {
        var jenis = $("#btnSimpan").val();

        $("#"+idModal).on('hidden.bs.modal', function(){
            $(".waktu").val("00:00:00");
            $("#programKerjaHarianJudul").val(null);
            $("#formProgramKerjaBulananAktivitas").show();
            $("#formProgramKerjaBulananAktivitasText").hide();
            $("#programKerjaBulananAktivitasText").val(null);
            $("#programKerjaHarianJudul").val(null);

            $("#programKerjaTahunanID").prop('disabled', false);
            $("#programKerjaBulananID").prop('disabled', false);
            $("#programKerjaHarianDivisi").prop('disabled', false);
        });

        $("#"+idModal).modal('hide');
        if(jenis == 'add')
        {
            $("#"+idModal).on('hidden.bs.modal', function(){
                penampung = [];
                if(isClick == 1) {
                    showDropzone.removeAllFiles(true);
                } 
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
        var html    = "<option selected disabled>Pilih Divisi</option>";
        
        // GET DATA
        var url     = "/master/data/trans/get/groupDivision";
        var type    = "GET";
        var message = "";
        var data    = valueCari;

        // START IF
        if(valueCari != '') {
            transData(url, type, data, message, isAsync)
                .then(function(success){
                    var getData     = success.data;
                    // LOOP INTO SELECT
                    $.each(getData, function(i,item){
                        var divisi_id   = item.id;
                        var divisi_name = item.name;

                        // INSERT TO HTML LOOP
                        html    += "<option value='" + divisi_id + "'>" + divisi_name + "</option>";
                    })

                    // REPALCE ON HTML SELECT
                    $("#"+idSelect).html(html);

                    // VALIDATE IF VALUESELECT IS NOT EMPTY
                    if(valueSelect == '') {
                        $("#"+idSelect).on('change', function(){
                            showSelect('programKerjaHarianPIC', this.value, '', true);
                        });
                    } else {
                        $("#"+idSelect).val(valueSelect);
                    }
                })
                .catch(function(err){
                    console.log(err.responseJSON);
                })
        } else {
            $("#"+idSelect).html(html);
        }
        // ENDIF VALUECARI
    } else if(idSelect == 'programKerjaHarianPIC') {
        var html    = "<option selected disabled>Pilih PIC / Penanggungjawab</option>";

        if(valueCari != '') {
            var url     = "/master/programkerja/bulanan/getDataPICByGroupDivisionID";
            var type    = "GET";
            var data    = { "GroupDivisionID" : valueCari };
            if(isAsync === true) { var message = Swal.fire({ title : "Data Sedang Dimuat" });Swal.showLoading(); } else { var message = ""; }

            // GET DATA
            transData(url, type, data, message, isAsync)
                .then((xhr)=>{
                    var getData     = xhr.data;

                    $.each(getData, function(i,item){
                        var emp_id      = item.employee_id;
                        var emp_name    = item.employee_name;

                        html    += "<option value='" + emp_id + "'>" + emp_name + "</option>";
                    })

                    $("#"+idSelect).html(html);

                    if(valueSelect == '') {
                        Swal.close();
                        $("#"+idSelect).select2('open');

                        $("#"+idSelect).on('change', function(){
                            var divisi  = $("#programKerjaHarianDivisi").val();
                            showSelect('programKerjaTahunanID', divisi, '', true);
                        })
                    } else {
                        $("#"+idSelect).val(valueSelect);
                    }
                })
                .catch((xhr)=>{
                    Swal.close();
                    console.log(xhr);
                })

        } else {
            $("#"+idSelect).html(html);
        }

    } else if(idSelect == 'programKerjaTahunanID') {
        var html    = "<option selected disabled>Pilih Program Kerja Tahunan</option>";
        
        if(valueCari != '') {
            var groupDivisiID   = valueCari;
            var url     = site_url + "/getProgramKerjaTahunan/"+groupDivisiID;
            var type    = "GET";
            var data    = "";
            if(isAsync === true) { var message = Swal.fire({ title:'Data Sedang Dimuat' }); Swal.showLoading(); } else { var message = ""; }

            transData(url, type, data, message, isAsync)
                .then((success)=>{
                    var getData     = success.data;

                    $.each(getData, function(i, item){
                        var pkt_id      = item.pkt_id;
                        var pkt_title   = item.pkt_title;

                        html    += "<option value='" + pkt_id + "'>" + pkt_title + "</option>";
                    })

                    $("#"+idSelect).html(html);

                    if(valueSelect == '') {
                        Swal.close();
                        $("#"+idSelect).select2('open');
                    } else {
                        $("#"+idSelect).val(valueSelect);
                    }

                })
                .catch((err)=>{
                    console.log(err.responseJSON);
                    if(valueSelect == '') {
                        Swal.close();
                    }
                })
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'programKerjaBulananID') {
        // BESOK KERJAIN INI
        var html    = "<option selected disabled>Pilih Program Kerja Bulanan</option>";

        if(valueCari != '') {
            var url     = site_url + "/cariDataProkerBulanan";
            var data    = { "pkt_uid" : valueCari, "pkb_uid": "%"};
            var type    = "GET";
            if(isAsync === true) { var message = Swal.fire({title: 'Data Sedang Dimuat'});Swal.showLoading(); } else { var message = ""; }

            transData(url, type, data, message, isAsync)
                .then((success)=>{
                    var header  = success.data.header;

                    $.each(header, function(i,item){
                        var header_pkb_uuid     = item.pkb_uuid;
                        var header_pkb_date     = item.pkb_date;
                        var header_pkb_title    = item.pkb_title;

                        html    += "<option value='" + header_pkb_uuid + "'>[" + header_pkb_date + "] " + header_pkb_title + "</option>";
                    })

                    if(current_role == 'umum') {
                        html    += "<option value='Lainnya'>Lainnya</option>";
                    }

                    $("#"+idSelect).html(html);

                    if(valueSelect == '') {
                        Swal.close();
                        $("#"+idSelect).select2('open');
                    } else {
                        $("#"+idSelect).val(valueSelect);
                    }
                })
                .catch((err)=> {
                    Swal.close();
                    console.log(err);
                    $("#"+idSelect).html(html);
                }) 
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'programKerjaBulananAktivitas') {
        var html    = "<option selected disabled>Pilih Jenis Pekerjaan</option>";

        if(valueCari != '') {
            // GET DATA
            if(valueCari != 'Lainnya') {
                $("#formProgramKerjaBulananAktivitas").show();
                $("#formProgramKerjaBulananAktivitasText").hide();

                var url     = site_url + "/cariDataProkerBulanan";
                var data    = { "pkt_uid" : "%", "pkb_uid": valueCari};
                var type    = "GET";
                if(isAsync === true) { var message = Swal.fire({title : "Data Sedang Dimuat"}); Swal.showLoading(); } else { var message = ""; }

                transData(url, type, data, message, isAsync)
                    .then((success)=>{
                        var detail  = success.data.detail;
                        
                        $.each(detail, function(i,item){
                            var pkb_detail_id   = item.pkbd_id;
                            var pkb_detail_name = item.pkb_detail;

                            html    += "<option value='" + pkb_detail_id + "'>" + pkb_detail_name + "</option>";
                        });

                        $("#"+idSelect).html(html);

                        if(valueSelect == '') {
                            Swal.close();
                            $("#"+idSelect).select2('open');
                            $("#"+idSelect).on('select2:select', function(){
                                $("#programKerjaHarianJudul").focus();
                            })
                        } else {
                            $("#"+idSelect).val(valueSelect);
                        }
                    })
                    .catch((err)=>{
                        console.log(err.responseJSON);
                    })
            } else {
                $("#formProgramKerjaBulananAktivitas").hide();
                $("#formProgramKerjaBulananAktivitasText").show();

                $("#programKerjaBulananID").on('select2:select', function(){
                    $("#programKerjaBulananAktivitasText").focus();
                })
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
    var programKerjaHarian_divisi_pic       = $("#programKerjaHarianPIC");
    var programKerjaHarian_pkbIDLainnya     = $("#pkbID_Lainnya");
    var programKerjaHarian_file             = penampung;

    var target                             = $("#target");
    var hasil                              = $("#hasil");
    var evaluasi                           = $("#hasil");


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
            "programKerjaHarian_pkbID_Lainnya"  : programKerjaHarian_pkbIDLainnya.val(),
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