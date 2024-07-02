var getUrl  = window.location.pathname;
Dropzone.autoDiscover = false;
// DROPZONE AREA

var current_seq     = 1;
var current_role    = $("#roleName").val();
var isClick         = 0;
var penampung       = [];
var uploadSize      = 25; // megabyte
var jenisFile       = ".jpg, .jpeg, .png, .docx, .doc, .xls, .xlsx, .pdf"; 
var showDropzone    = new Dropzone("#myDropzone", {
    url             : getUrl + "/fileUpload",
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
                    var url     = getUrl + "/deleteUpload";
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

$(document).ready(function(){
    moment.locale('id');
    if(current_role == 'operasional') {
        var url     = "/master/data/getCurrentSubDivision/"+current_role;
        var type    = "GET";
        var data    = {
            "role_name" : current_role,
            "user_id"   : $("#current_uid").val(),
        };
        
        transData(url, type, data, '', true)
            .then((success)=>{
                $("#currentSubDivision").val(success.data[0].sub_division_name);
            })
            .catch((err)=>{
                $("#currentSubDivision").val(null);
            })
    }
    showCalendarButton('global')

    $("#prokerBulananStartDate").val(moment().startOf('month').format('DD/MM/YYYY'));
    $("#prokerBulananEndDate").val(moment().format('DD/MM/YYYY'));

    $("#current_date").val(moment().tz('Asia/Jakarta').format('YYYY-MM-DD'));
    // SHOW COLLAPSE
    $("#btnFilter").on('click', function(){
        $("#filterCalendar").collapse('show');
        // FORMAT CALENDAR DATERANGEPICKER
        $(".date").daterangepicker({
            singleDatePicker : true,
            locale : {
                format  : 'DD/MM/YYYY',
                cancelLabel: 'Clear'
            },
            autoUpdateInput: false,
            autoApply    : true,
        }).attr('readonly','readonly').css({"cursor":"pointer", "background":"white"});

        $('.date').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('DD/MM/YYYY'));
        });

        // SHOW SELECT2
        var current_sub_division    = $("#currentSubDivision").val() == 'pic' ? '%' : $("#currentSubDivision").val();
        show_select('groupDivisionName', '', '', true);
        $("#filterCalendar").on('shown.bs.collapse', function(){
            show_select('groupDivisionName', '%', '', false);
        });
        show_select('jadwalUmrah', '', '');
        show_select('bagian','', current_sub_division, true);
    });

    $("#btnFilter").on('click', function(){
        $("#filterCalendar").collapse('hide');
        $("#filterCalendar").on('hidden.bs.collapse', function(){
            // console.log('test2');
        });
    });
});

function showCalendar(tgl_sekarang, tgl_awal, tgl_akhir, divisi)
{
    if(tgl_sekarang == '') {
        tgl_sekarang    = moment().format('YYYY-MM-DD');
    } else {
        tgl_sekarang    = tgl_sekarang;
    }
    var idCalendar  = document.getElementById('calendar');
    var calendar    = new FullCalendar.Calendar(idCalendar, {
        themeSystem: 'bootstrap',
        headerToolbar: {
            left    : 'prevCustomButton nextCustomButton',
            center  : '',
            right   : 'refreshCustomButton dayGridMonth',
        },
        locale  : 'id',
        initialDate     : tgl_sekarang,
        navLinks: false, // can click day/week names to navigate views
        selectable: true,
        selectMirror: true,
        contentHeight: 750,
        select  : function(arg) {
            var idModal     = "modalForm";
            var jenis       = "add";
            var value       = arg;

            showModal(idModal, jenis, value);
            
            $("#btnSimpan").click(function(){
                do_save(this.value, arg, calendar);
            });
            calendar.unselect();
        },
        moreLinkContent:function(args){
            return '+'+args.num+' Lainnya';
        },
        eventClick: function(arg) {
            var idModal     = "modalForm";
            var jenis       = "edit";
            var value       = arg.event.id;
            var roleId      = arg.event.extendedProps.roleId;

            showModal(idModal, jenis, value);

            $("#btnSimpan").click(function(){
                do_save(this.value, arg.event, calendar);
            });
        },
        customButtons: {
            prevCustomButton: {
                // text: "",
                click: function() {
                    var hari_ini                = $("#current_date").val();
                    var groupDivision           = $("#groupDivisionName").val();
                    var hari_ini_bulan_lalu     = moment(hari_ini).subtract(1, 'month').format('YYYY-MM-DD');
                    var tgl_awal_bulan_lalu     = moment(hari_ini_bulan_lalu).startOf('month').format('YYYY-MM-DD');
                    var tgl_akhir_bulan_lalu    = moment(hari_ini_bulan_lalu).endOf('month').format('YYYY-MM-DD');
                    showCalendar(tgl_awal_bulan_lalu, tgl_awal_bulan_lalu, tgl_akhir_bulan_lalu, groupDivision);
                    $("#current_date").val(hari_ini_bulan_lalu);
                    // VISUAL UPDATE
                    $("#titleBulan").html(moment(hari_ini_bulan_lalu).format('MMMM'));
                    $("#titleTahun").html(moment(hari_ini_bulan_lalu).format('YYYY'));
                    // UPDATE FILTER
                    $("#prokerBulananStartDate").val(moment(tgl_awal_bulan_lalu).format('DD/MM/YYYY'));
                    $("#prokerBulananEndDate").val(moment(tgl_akhir_bulan_lalu).format('DD/MM/YYYY'));
                }
            },
            nextCustomButton : {
                click : function() {
                    var today                   = $("#current_date").val();
                    var groupDivision           = $("#groupDivisionName").val();
                    var hari_ini_bulan_depan    = moment(today).add(1, 'month').format('YYYY-MM-DD');
                    var tgl_awal_bulan_depan    = moment(hari_ini_bulan_depan).startOf('month').format('YYYY-MM-DD');
                    var tgl_akhir_bulan_depan   = moment(hari_ini_bulan_depan).endOf('month').format('YYYY-MM-DD');
                    showCalendar(tgl_awal_bulan_depan, tgl_awal_bulan_depan, tgl_akhir_bulan_depan, groupDivision);
                    $("#current_date").val(hari_ini_bulan_depan);
                    // VISUAL UPDATE
                    $("#titleBulan").html(moment(hari_ini_bulan_depan).format('MMMM'));
                    $("#titleTahun").html(moment(hari_ini_bulan_depan).format('YYYY'));
                    // UPDATE FILTER
                    $("#prokerBulananStartDate").val(moment(tgl_awal_bulan_depan).format('DD/MM/YYYY'));
                    $("#prokerBulananEndDate").val(moment(tgl_akhir_bulan_depan).format('DD/MM/YYYY'));
                }
            },
            refreshCustomButton     : {
                click   : function() {
                    var today           = $("#current_date").val();
                    var groupDivision   = $("#groupDivisionName").val();
                    var tgl_awal        = moment(today).startOf('month').format('YYYY-MM-DD');
                    var tgl_akhir       = moment(today).endOf('month').format('YYYY-MM-DD');
                    showCalendar(today, tgl_awal, tgl_akhir, groupDivision);
                }
            }
        },
        editable: false,
        // fixedMirrorParent: document.body,
        dayMaxEvents: true, // allow "more" link when too many events,
        events: function(fetchInfo, successCallback, failureCallback) {
            var jadwal      = $("#jadwalUmrah").val();
            var bagian      = $("#bagian").val();
            var url         = getUrl + "/getDataAllProkerBulanan";
            var type        = "GET";
            var data        = {
                "cari"                  : "%",
                "tgl_awal"              : tgl_awal == '' ? moment().startOf('month').format('YYYY-MM-DD') : tgl_awal,
                "tgl_akhir"             : tgl_akhir == '' ? moment().endOf('month').format('YYYY-MM-DD') : tgl_akhir,
                "divisi"                : divisi == null ? '' : divisi,
                "jadwal"                : jadwal == null ? '%' : jadwal,
                "sub_divisi"            : bagian == null ? '%' : bagian,
            };
            var message     =   Swal.fire({title   : 'Data Sedang Dimuat',allowOutsideClick: false});
                                Swal.showLoading();
            var isAsync     = true;
            transData(url, type, data, message, isAsync)
                .then(function(xhr){
                    var tempData    = [];
                    var roleColor   = '';
                    for(var i = 0; i < xhr.data.list.length; i++) {
                        
                        if(xhr.data.list[i]['group_division_name'] == 'Marketing') {
                            roleColor   = "#80B9AD";
                        } else if(xhr.data.list[i]['group_division_name'] == 'Operasional') {
                            roleColor   = "#538392";
                        }

                        // if(xhr.data.list[i]['role_id'] == '2') {
                        //     roleColor   = '#1AB394';
                        // } else if(xhr.data.list[i]['role_id'] == '3') {
                        //     roleColor   = '#364B45';
                        // } else if(xhr.data.list[i]['role_id'] == '4') {
                        //     roleColor   = '#00A7AC';
                        // } else if(xhr.data.list[i]['role_id'] == '5') {
                        //     roleColor   = '#43A6EE';
                        // }
                        
                        tempData.push({
                            title   : xhr.data.list[i]['pkb_title'],
                            start   : xhr.data.list[i]['pkb_start_date'], 
                            end     : moment(xhr.data.list[i]['pkb_end_date'], 'YYYY-MM-DD').add(1, 'days').format('YYYY-MM-DD'),
                            allDay  : true,
                            id      : xhr.data.list[i]['pkb_uuid'],
                            roleId  : xhr.data.list[i]['role_id'],
                            color   : roleColor,
                        });
                    }
                    successCallback(tempData);
                    Swal.close();
                })
                .catch(function(xhr){
                    Swal.close();
                    var tempData    = [];
                    tempData.push({
                        title   : null,
                        start   : null,
                    });
                });
                
        }
    });

    calendar.render();
}

function showDataCalendar()
{
    var start_date      = $("#prokerBulananStartDate").val() != '' ? moment($("#prokerBulananStartDate").val(), 'DD/MM/YYYY').format('YYYY-MM-DD') : '';
    var end_date        = $("#prokerBulananEndDate").val() != '' ? moment($("#prokerBulananEndDate").val(), 'DD/MM/YYYY').format('YYYY-MM-DD') : '';
    var divisi          = $("#groupDivisionName").val();

    if($("#prokerBulananStartDate").val() == '') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Tanggal Awal Harus diisi',
        }).then((results)   => {
            if(results.isConfirmed) {
                $("#prokerBulananStartDate").focus();
            }
        })
    } else if($("#prokerBulananEndDate").val() == '') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Tanggal Akhir Harus diisi',
        }).then((results)   => {
            if(results.isConfirmed) {
                $("#prokerBulananEndDate").focus();
            }
        })
    } else {
        var current_date    = start_date.split('-')[0]+"-"+start_date.split('-')[1]+"-01";
        showCalendar(current_date, start_date, end_date, divisi);
    }
}

function showCalendarOperasional()
{
    var table   = $("#tableCalendarOperasional").DataTable({
        scrollX     : true,
    });
}

function showModal(idModal, jenis, value)
{
    const current_role    = $("#roleName").val();
    $(".form-select").select2({
        theme   : 'bootstrap4',
    })
    if(idModal == 'modalForm') {
        if(current_role != 'admin') {
            if(current_role == 'operasional') {
                $("#formTableDetailProkerBulanan").hide();
                $("#formUpload").show();
                $("#formWaktuAktivitias_prokerBulanan").show();
            } else {
                $("#formTableDetailProkerBulanan").show();
                $("#formUpload").hide();
                $("#formWaktuAktivitias_prokerBulanan").hide();
            }
        } else {
            $("#formTableDetailProkerBulanan").show();
            $("#formUpload").show();
            $("#formWaktuAktivitias_prokerBulanan").show();
        }
        show_table('tableDetailProkerBulanan');

        $("#btnSimpan").val(jenis);
        $(".fc .fc-popover").hide();

        // DATERANGEPICKER HANYA WAKTU
        $(".waktu").daterangepicker({
            singleDatePicker    : true,
            autoApply           : true,
            timePicker          : true,
            timePicker24Hour    : true,
            timePickerIncrement : 1,
            timePickerSeconds   : true,
            locale: {
                format: 'HH:mm:ss'
            },
        }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find(".calendar-table").hide();
        });

        // DATERANGEPICKER HANYA TANGGAL
        $(".tanggal").daterangepicker({
            singleDatePicker : true,
            locale : {
                format  : 'DD/MM/YYYY',
            },
            minYear     : moment().subtract(10, 'years'),
            maxYear     : moment().add(10, 'years'),
            autoApply    : true,
            showDropdowns: true,
        }).css({"cursor":"pointer","background":"white"}).attr('readonly','readonly');
        $(".tanggal").val(null);

        $("#prokerBulananCheckSameDay").on('click', function(){
            var tanggal     = $("#prokerBulananTanggal").val();
            if(($(this).is(":checked") == true) && (tanggal != '')) {
                $("#prokerBulananTanggalAkhir").data('daterangepicker').setStartDate(tanggal);
                $("#prokerBulananTanggalAkhir").data('daterangepicker').setEndDate(tanggal);
            } else {
                $("#prokerBulananTanggalAkhir").val(null);
            }
        });

        $("#prokerBulananTanggalAkhir").on('apply.daterangepicker', function(){
            $("#prokerBulananCheckSameDay").prop('checked', false);
        });


        if(jenis == 'add') {
            // SHOW MODAL
            $("#"+idModal).modal({backdrop: 'static', keyboard: false});
            $("#"+idModal).modal('show');

            // SHOW SELECT
            show_select('prokerTahunanID','%','', true);
            show_select('prokerBulananPIC','','', true);
            show_select('subProkerTahunanSeq','','', true);
            
            tambah_baris('tableDetailProkerBulanan','');

            var title   = "Tambah Uraian Pekerjaan Tgl. "+moment(value.startStr, 'YYYY-MM-DD').format('DD/MM/YYYY');
            $("#modalTitle").html(title);
            $("#prokerTahunanID").prop('disabled', false);
        } else if(jenis == 'edit') {
            $("#prokerTahunanID").prop('disabled', true);

            var url     = "/master/programkerja/bulanan/getDataAllProkerBulanan";
            var type    = "GET";
            var data    = {
                "cari"      : value,
                "tgl_awal"  : moment($("#prokerBulananStartDate").val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "tgl_akhir" : moment($("#prokerBulananEndDate").val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "divisi"    : '%',
                "jadwal"    : '%',
                "sub_divisi": '%',
            }
            var isAsync = true;
            
            // SHOW MESSAGE
            if(isAsync == true) {
                var customMessage   = Swal.fire({title:'Data Sedang Dimuat'});Swal.showLoading();
            }

            transData(url, type, data, customMessage, isAsync)
                .then(function(xhr){
                    
                    $("#"+idModal).modal({backdrop: 'static', keyboard: false});
                    $("#"+idModal).modal('show');
                    var resultData  = xhr.data.header[0];

                    var start_date  = resultData['pkb_start_date'] != null ? moment(resultData['pkb_start_date'], 'YYYY-MM-DD').format('DD/MM/YYYY') : null;
                    var end_date    = resultData['pkb_end_date'] != null ? moment(resultData['pkb_end_date'], 'YYYY-MM-DD').format('DD/MM/YYYY') : null;
                    var start_time  = resultData['pkb_start_time'] != null ? resultData['pkb_start_time'].split(' ')[1] : "00:00:00";
                    var end_time    = resultData['pkb_end_time'] != null ? resultData['pkb_end_time'].split(' ')[1] : "00:00:00" ;

                    $("#prokerBulananID").val(resultData['pkb_uuid']);
                    $("#prokerTahunanGroupDivisionID").val(resultData['pkb_gd_id']);
                    $("#prokerTahunanGroupDivisionName").val(resultData['pkb_gd_name']);
                    $("#prokerTahunanSubDivisionID").val(resultData['pkb_sd_id']);
                    $("#prokerTahunanSubDivisionName").val(resultData['pkb_sd_name']);
                    $("#prokerBulananTitle").val(resultData['pkb_title']);
                    $("#prokerBulananDesc").val(resultData['pkb_description']);
                    $("#prokerBulananStartTime").val(start_time);
                    $("#prokerBulananEndTime").val(end_time); 

                    if(start_date == end_date) {
                        $("#prokerBulananCheckSameDay").prop('checked', true);
                    } else {
                        $("#prokerBulananCheckSameDay").prop('checked', false);
                    }

                    if(($("#prokerBulananTanggal").val() !== undefined) && ($("#prokerBulananTanggalAkhir").val() !== undefined)) {
                        if(start_date != null) {
                            $("#prokerBulananTanggal").data('daterangepicker').setStartDate(start_date);
                            $("#prokerBulananTanggal").data('daterangepicker').setEndDate(start_date);
                        } else {
                            $("#prokerBulananTanggal").val(null);
                        }
                        
                        if(end_date != null) {
                            $("#prokerBulananTanggalAkhir").data('daterangepicker').setStartDate(end_date);
                            $("#prokerBulananTanggalAkhir").data('daterangepicker').setEndDate(end_date);
                        } else {
                            $("#prokerBulananTanggalAkhir").val(null)
                        }
                    }

                    var title   = "Preview Uraian Pekerjaan Tgl. "+moment(resultData['pkb_start_date'],'YYYY-MM-DD').format('DD/MM/YYYY');
                    $("#modalTitle").html(title);

                    show_select('prokerTahunanID','%', resultData['pkb_pkt_id']);
                    show_select('prokerBulananPIC', resultData['pkb_gd_id'], resultData['pkb_employee_id']);
                    show_select('subProkerTahunanSeq',resultData['pkb_pkt_id'], resultData['pkb_pkt_id_seq']);
                    
                    if(xhr.data.detail.length > 0) {
                        for(var  i = 0; i < xhr.data.detail.length; i++) {
                            tambah_baris('tableDetailProkerBulanan', xhr.data.detail[i]);
                        }
                    }
                    tambah_baris('tableDetailProkerBulanan','');
                    $("#pkbJenisPekerjaan"+(xhr.data.detail.length + 1)).focus();
                    $("#prokerTahunanID").select2('open');
                    // KETIKA SUDAH KELUAR SEMUA MAKA CLOSE MESSAGE
                    Swal.close();
                })
                .catch(function(xhr){
                    Swal.fire({
                        icon    : 'error',
                        title   : xhr.status,
                        text    : xhr.statusText,
                    });
                    console.log(xhr);
                })
        }
    } else if(idModal == 'modalAktivitas') {
        // CHECK
        if($("#pkbJenisPekerjaan"+value).val() == '') {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Tidak ada aktivitas untuk kolom ini',
            })
        } else {
            var prokerBulananHeaderID   = $("#prokerBulananID").val();
            var prokerBulananDetailID   = $("#idDetail"+value).val();
            $("#prokerBulananID_Activity").val(prokerBulananHeaderID);

            var url     = getUrl+ "/getListDataHarian";
            var type    = "GET";
            var data    = {
                "pkb_id"    : prokerBulananHeaderID,
                "pkbd_id"   : prokerBulananDetailID,
            }

            var isAsync = true;
            var customMessage   = Swal.fire({title:'Data Sedang Dimuat', allowOutsideClick: false});Swal.showLoading();

            transData(url, type, data, customMessage, isAsync)
                .then(function(xhr){
                    var getData     = xhr.data.header;
                    var getFile     = xhr.data.file;

                    if(getData.length > 0) {
                        $("#"+idModal).modal({backdrop: 'static', keyboard: false});
                        $("#"+idModal).modal('show');
                        // TUTUP MODAL SEBELUMNYA
                        closeModal('modalForm');
                        // OPEN MODAL SELANJUTNYA
                        show_table('tableActivityUser');
                        // console.log(getData, getFile);
                        for(var i = 0; i < getData.length; i++) {
                            var pkhd_seq        = i + 1;
                            var pkhd_title      = getData[i]['pkh_title'];
                            var pkhd_pic        = getData[i]['pkh_create_by'];
                            var pkhd_duration   = moment(getData[i]['pkh_date'], 'YYYY-MM-DD').format('DD/MM/YYYY')+" ("+getData[i]['pkh_start_time']+' s/d '+getData[i]['pkh_end_time']+")";
                            // KOLOM BUKTI
                            var pkhd_bukti      = "<ul>";
                            for(var j = 0; j < getFile.length; j++) {
                                if(getData[i]['pkh_id'] == getFile[j]['file_header_id']) {
                                    var file_path   = getFile[j]['file_path'].split('/')[1];
                                    var file_name   = getFile[j]['file_name'].length > 25 ? getFile[j]['file_name'].substring(0, 25) + '...' : getFile[j]['file_name'];
                                    pkhd_bukti      += "<li><a href='/master/programkerja/harian/downloadFile/"+file_path+"'>" + file_name + "</a></li>";
                                } else {
                                    var file_name   = "";
                                }
                            }
                            pkhd_bukti += "</ul>";

                            $("#tableActivityUser").DataTable().row.add([
                                pkhd_seq,
                                pkhd_title,
                                pkhd_pic,
                                pkhd_duration,
                                pkhd_bukti
                            ]).draw('false');
                        }
                        Swal.close();
                    } else {
                        Swal.fire({
                            icon    : 'error',
                            title   : 'Terjadi Kesalahan',
                            text    : 'Tidak ada data yang bisa ditampilkan',
                        })
                    }
                })
                .catch(function(xhr){
                    console.log(xhr);
                });
        }
    }
}

function closeModal(idModal) {
    $("#"+idModal).modal('hide');
    if(idModal == 'modalForm') {
        $("#"+idModal).on('hidden.bs.modal', function(){
            // RESET FORM
            $("#btnSimpan").off('click');
            $("#prokerBulananTitle").val(null);
            $("#prokerBulananDesc").val(null);
            $("#prokerTahunanGroupDivisionName").val(null);
            $("#btnSimpan").val(null);
            $("#prokerBulananID").val(null);
            $("#btnTambahBaris").val(1);
            $(".waktu").val('00:00:00');
            current_seq = 1;
            // HIDE FORM
            $("#formTableDetailProkerBulanan").hide();
            $("#formUpload").hide();
            $("#formWaktuAktivitias_prokerBulanan").hide();

            $("#prokerBulananCheckSameDay").prop('checked', false);
            // REMOVE FILE FROM DROPZONE
            penampung = [];
            if(isClick == 1) {
                showDropzone.removeAllFiles(true);
            } 

            $("#collapseLainnya").collapse('hide');
            $("#jadwalProgramUraianPktSeq").val(null);
            $("#jadwalProgramUraianRulSeq").val(null);
            $("#formProkerBulananTitle").show();
        });
        if(isClick == 0) {
            showDropzone.removeAllFiles(true);
        }
    } else if(idModal == 'modalAktivitas') {
        var pkb_ID  = $("#prokerBulananID_Activity").val();
        showModal('modalForm', 'edit', pkb_ID);
    }
}

function show_select(idSelect, valueCari, valueSelect, isAsync)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
    var html    = "<option selected disabled>Pilih Program Kerja Tahunan</option>";
    if(idSelect == 'prokerTahunanID') {
        if(valueCari != '') {
            var url     = getUrl + "/getDataProkerTahunan";
            var type    = "GET";
            var data    = {
                "prokerID"  : valueCari,
            };
            if(isAsync === true) { var message = Swal.fire({ title : 'Data Sedang Dimuat' });Swal.showLoading(); } else { var message = ""; }
            transData(url, type, data, message, isAsync)
                .then(function(xhr){
                    $.each(xhr.data, function(i,item){
                        var pktUID      = item['pkt_uid'];
                        var pktTitle    = item['pkt_title'];

                        html    += "<option value='" + pktUID + "'>" + pktTitle + "</option>";
                        Swal.close();
                    });
                    $("#"+idSelect).html(html);
                    if(valueSelect != '') {
                        $("#"+idSelect).val(valueSelect);
                    }
                })
                .catch(function(xhr){
                    console.log(xhr);
                    $("#"+idSelect).html(html);
                });
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'subProkerTahunanSeq') {
        var html    = "<option selected disabled>Sub-Program Kerja Tahunan</option>";
        var url     = getUrl + "/getDataSubProkerTahunan";
        var type    = "GET";
        var data    = {
            "prokerTahunan_ID"  : valueCari,
        };
        if(isAsync === true) { var message = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading(); } else { var message = ""; }

        if(valueCari != '') {
            transData(url, type, data, message, isAsync)
            .then((xhr) => {
                $.each(xhr.data['detail'], function(i,item){
                    html    += "<option value='"+ item['detail_seq'] +"'>" + item['detail_title'] + "</option>";
                })
                
                if(current_role == 'operasional' || current_role == 'admin') {
                    html    += "<option value='lainnya'>Lainnya (Umrah / Haji)</option>";
                }

                $("#"+idSelect).html(html);
                
                if(valueSelect != '') {
                    $("#"+idSelect).val(valueSelect).trigger('change');
                }
                
                if( isAsync === true ) {
                    Swal.close();
                    $("#"+idSelect).select2('open');
                }
            })
            .catch((xhr) => {
                console.log(xhr);
                $("#"+idSelect).html(html);
            })
        }
        $("#"+idSelect).html(html);
    } else if(idSelect == 'prokerBulananPIC') {
        var currentUser     = $("#currentRole").val();
        var html            = "<option selected disabled>Pilih PIC / Penanggung Jawab</option>";

        if(valueCari != '') {
            var url     = "/master/programkerja/bulanan/getDataPICByGroupDivisionID";
            var type    = "GET";
            var data    = {
                "GroupDivisionID"   : valueCari,
            };

            transData(url, type, data, '', false)
                .then(function(xhr){
                    $.each(xhr.data, function(i,item){
                        var empID   = item['employee_id'];
                        var empName = item['employee_name'];
                        
                        html    += "<option value='" +empID+ "'>" + empName + "</option>";
                    });
                    $("#"+idSelect).html(html);
                    if(valueSelect != '') {
                        $("#"+idSelect).val(valueSelect).trigger('change');
                    } else {
                        $("select option").filter(function(){
                            return $(this).text() == currentUser;
                        }).prop('selected', true).trigger('change');
                    }
                })
                .catch(function(xhr){
                    console.log(xhr);
                    $("#"+idSelect).html(html);
                })
        } else {    
            $("#"+idSelect).html(html);
        }

        $("#"+idSelect).on('select2:select', function(){
            $("#prokerBulananTitle").focus();
        })
    } else if(idSelect == 'groupDivisionName') {
        var html    = "<option selected disabled>Pilih Group Divisi</option>";
        if(valueCari != '') {
            var url     = "/master/data/getGroupDivisionWRole";
            var type    = "GET";

            transData(url, type, '', '', isAsync)
                .then(function(xhr){
                    html    += "<option value='%'>Semua</option>";
                    $.each(xhr.data, function(i,item){
                        html    += "<option value='" + item['role_name'] + "'><div class='badge badge-sm badge-primary'>" + item['gd_name'] + "</div></option>";
                    });
                    $("#"+idSelect).html(html);
                })
                .catch(function(xhr){
                    console.log(xhr);
                    $("#"+idSelect).html(html);
                })
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'jadwalUmrah') {
        
        moment.locale('id');
        var html    = [
            "<option selected disabled>Pilih Jadwal</option>",
            "<option value='%'>Semua</option>"
        ];

        var url     = getUrl + "/listSelectJadwalUmrah";
        transData(url, 'GET', '', '', true)
            .then((xhr)=>{
                var getData     = xhr.data;
                $.each(getData, function(i,item){
                    var program_jdw_id          = item['jdw_uuid'];
                    var program_name            = item['name'];
                    var program_depature_date   = item['jdw_depature_date'];
                    var program_arrival_date    = item['jdw_arrival_date'];

                    var customVal               = "[" + program_name.toUpperCase() + "] (" + moment(program_depature_date, 'YYYY-MM-DD').format('DD/MMM/YYYY') + " s/d " + moment(program_arrival_date, 'YYYY-MM-DD').format('DD/MMM/YYYY') + ")";
                    var customText              = program_name+" - "+moment(program_depature_date,'YYYY-MM-DD').format('DD/MM/YYYY')+" s/d "+moment(program_arrival_date, 'YYYY-MM-DD').format('DD/MM/YYYY');

                    html                        += "<option value='" + program_jdw_id + "'>" +customText+ "</option>";
                });
                $("#"+idSelect).html(html);
            })
            .catch((xhr)=>{
                console.log(xhr);
                $("#"+idSelect).html(html);
            })
    } else if(idSelect == 'bagian') {
        console.log({
            "currentSubDivision"    : $("#currentSubDivision").val(),
            "current_role"          : current_role,
        });
        var html    = [
            "<option selected disabled>Pilih Sub-Divisi</option>",
            "<option value='%'>Semua</option>",
            "<option value='admin'>Admin Operasional</option>",
            "<option value='operasional'>Operasional</option>",
        ];

        $("#"+idSelect).html(html);

        if(valueSelect != '') {
            $("#"+idSelect).val(valueSelect).trigger('change');
        }
    } else if(idSelect == 'jadwalProgram') {
        var html    = "<option selected disabled>Pilih Jadwal Umrah / Haji</option>";

        // GET DATA
        var url     = getUrl + "/listSelectJadwalUmrahForm";
        var type    = "GET";
        var data    = "";
        var message = "";

        if(valueCari != '') {
            transData(url, type, data, message, false)
                .then((success)=> {
                    $.each(success.data, function(i,item){
                        var jdw_id          = item.prog_jdw_id;
                        var jdw_prog_name   = item.prog_jdw_name;
                        var jdw_arv_date    = moment(item.prog_jdw_arrival_date, 'YYYY-MM-DD').format('DD/MMM/YYYY');
                        var jdw_dpt_date    = moment(item.prog_jdw_depature_date, 'YYYY-MM-DD').format('DD/MMM/YYYY');

                        var text            = "[" + jdw_prog_name.toUpperCase() + "] (" + jdw_dpt_date + " s/d " + jdw_arv_date + ")";
                        html                += "<option value='"+jdw_id+"'>" + text + "</option>";
                    })
                    $("#"+idSelect).html(html);
                })
                .catch((err)=>{
                    $("#"+idSelect).html(html);
                })

        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'jadwalProgramUraian') {
        var html    = "<option selected disabled>Pilih Uraian Pekerjaan</option>";
        
        if(valueCari != '') {
            // GET DATA
            var url     = getUrl+"/listSelectedJadwalUmrahForm";
            var type    = "GET";
            var data    = {
                "prog_jdw_id"   : valueCari,
            };
            // if ( isAsync === true ) { var message = Swal.fire({ title : 'Data Sedang Dimuat' }); Swal.close(); } else { var message = ""; }
            var message     = "";
            
            transData(url, type, data, message, isAsync)
                .then(( success ) => {
                    var getData     = success.data;
                    $.each(getData, (i, item)=> {
                        var prog_jdw_seq            = item.prog_jdw_seq;
                        var prog_jdw_title          = item.prog_jdw_title;
                        var prog_jdw_sub_pkt_seq    = item.prog_pkt_seq;

                        html    += "<option value='" + prog_jdw_seq +"|"+ prog_jdw_sub_pkt_seq + "'>" + (i + 1) + ". " + prog_jdw_title + "</option>";
                    });

                    $("#"+idSelect).html(html);

                    if(valueSelect != '') {
                        $("#"+idSelect).val(valueSelect);
                    }
                    
                    Swal.close();
                })
                .catch(( error ) => {
                    console.log(error.responseJSON);
                    $("#"+idSelect).html(html);
                    Swal.close();
                })
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'jadwalProgramUraianPktSeq') {
        $("#"+idSelect).val(valueCari);
    }
}

function show_select_detail(idSelect, valueCari, isAsync) {
    if(idSelect == 'prokerTahunanID') {
        var url     = getUrl + '/getDataProkerTahunan';
        var type    = "GET";
        var data    = {
            "prokerID"  : valueCari,
        };
        if( isAsync === true ) { var message = Swal.fire({ title : 'Data Sedang Dimuat' }); Swal.showLoading(); } else { var message = ""; }
        transData(url, type, data, message, isAsync)
            .then(function(xhr){
                var resultData  = xhr.data[0];
                $("#prokerTahunanGroupDivisionID").val(resultData['group_division_id']);
                $("#prokerTahunanGroupDivisionName").val(resultData['group_division_name']);
                $("#prokerTahunanSubDivisionID").val(resultData['sub_division_id']);
                $("#prokerTahunanSubDivisionName").val(resultData['sub_division_name']);

                show_select('prokerBulananPIC', resultData['group_division_id'],'', true);
            })
            .catch(function(xhr){
                console.log(xhr);
            });

        show_select('subProkerTahunanSeq', valueCari, '', true);
    } else if(idSelect == 'subProkerTahunanSeq') {
        var divisi  = $("#prokerTahunanGroupDivisionName").val();
        var button  = $("#btnSimpan").val();
        if((divisi == 'operasionl') && (button == 'edit')) {
            var selected_text   = $("#subProkerTahunanSeq option:selected").text();
            $("#prokerBulananTitle").val(selected_text);
        } else if(button == 'add') {
            if(current_role == 'operasional' || current_role == 'admin') {
                if(valueCari == 'lainnya') {
                    $("#collapseLainnya").collapse('show');
                    $("#formProkerBulananTitle").hide();

                    show_select('jadwalProgram', '%', '', true);
                    show_select('jadwalProgramUraian', '', '', true);
                } else {
                    $("#collapseLainnya").collapse('hide');
                    $("#formProkerBulananTitle").show();
                }
            }
        }
    } else if(idSelect == 'groupDivisionName') {
        // console.log(idSelect, valueCari);
        if(valueCari == 'operasional') {
            $("#filterOperasional").collapse('show');
        } else {
            $("#filterOperasional").collapse('hide');

            $("#filterOperasional").on('hidden.bs.collapse', function() {
                $("#jadwalUmrah").val('%').trigger('change');
                $("#bagian").val('%').trigger('change');
            });
        }
    }
}

function show_table(idTable, jmlTable)
{
    if(idTable == 'tableDetailProkerBulanan') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            ordering    : false,
            paging      : false,
            pageLength  : -1,
            autoWidth   : false,
            searching   : false,
            bInfo       : false,
            columnDefs  : [
                { "targets": [0], "className":"text-center", "width":"8%" },
                { "targets":[1, 2, 3, 4], "width":"17%" },
            ],
        })
    } else if(idTable == 'tableActivityUser') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            autoWidth    : false,
            columnDefs   : [
                { "targets" : [0], "className":"text-center","width":"7%" },
                { "targets" : [1], "className":"text-left", "width":"35%" },
                { "targets" : [3], "className":"text-left", "width":"15%" },
                { "targets" : [4], "className":"text-left", "width":"25%" },
                
            ],
        });
    } else if(idTable == 'tableCalendarOperasional') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            autoWidth   : false,
            scrollX     : true,
            ordering    : false,
            pageLength  : -1,
            fixedHeader: true,
            fixedColumns: {
                start: 1,
                end: 1
            },
            layout  : {
                topStart    : 'buttons',
            },
            buttons : [
                {
                    attr : {id:'btn_prev_table', class:'btn btn-primary'},
                    action  : function(e, dt, node, config) {
                        var hari_ini        = $("#current_date").val();
                        var bulan_lalu_hari_ini     = moment(hari_ini, 'YYYY-MM-DD').subtract(1, 'month').format('YYYY-MM-DD');
                        var bulan_lalu_awal = moment(bulan_lalu_hari_ini, 'YYYY-MM-DD').startOf('month').format('YYYY-MM-DD');
                        var bulan_lalu_akhir= moment(bulan_lalu_hari_ini, 'YYYY-MM-DD').endOf('month').format('YYYY-MM-DD');

                        // UPDATE VISUAL
                        $("#titleBulan").html(moment(bulan_lalu_hari_ini).format('MMMM'));
                        $("#titleTahun").html(moment(bulan_lalu_hari_ini).format('YYYY'));
                        // UPDATE FORM FILTER
                        $("#prokerBulananStartDate").val(moment(bulan_lalu_awal, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                        $("#prokerBulananEndDate").val(moment(bulan_lalu_akhir, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                        $("#current_date").val(bulan_lalu_hari_ini);
                        showDataTable(idTable);
                    }
                },
                {
                    attr    : {id:'btn_next_table', class:'btn btn-primary btn-next-table'},
                    action  : function(e, dt, node, config) {
                        var hari_ini                = $("#current_date").val();
                        var bulan_depan_hari_ini    = moment(hari_ini, 'YYYY-MM-DD').add(1, 'month').format('YYYY-MM-DD');
                        var bulan_depan_awal        = moment(bulan_depan_hari_ini, 'YYYY-MM-DD').startOf('month').format('YYYY-MM-DD');
                        var bulan_depan_akhir       = moment(bulan_depan_hari_ini, 'YYYY-MM-DD').endOf('month').format('YYYY-MM-DD');

                         // UPDATE VISUAL
                         $("#titleBulan").html(moment(bulan_depan_hari_ini).format('MMMM'));
                         $("#titleTahun").html(moment(bulan_depan_hari_ini).format('YYYY'));
                         // UPDATE FORM FILTER
                        $("#prokerBulananStartDate").val(moment(bulan_depan_awal, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                        $("#prokerBulananEndDate").val(moment(bulan_depan_akhir, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                        $("#current_date").val(bulan_depan_hari_ini);
                        showDataTable(idTable);
                    }
                }
            ],
            columnDefs  : [
                {
                    "targets"   : [0],
                    "width"     : "19%",
                },
                {
                    "targets"   : [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31],
                    "className" : "text-center",
                }
            ],
        });
        $("#btn_prev_table").html("<i class='fa fa-chevron-left'></i>");
        $("#btn_next_table").html("<i class='fa fa-chevron-right'></i>").css('margin-left: 0.75em;');
    }
} 

function showDataTable(idTable)
{
    show_table(idTable,'');
    // console.log(getLastDay);    
    // GET DATA
    var url     = getUrl + "/listProkerTahunan";
    var type    = "GET";
    var isAsync = true;
    var customMessage   = Swal.fire({title:'Data Sedang Dimuat', allowOutsideClick: false});Swal.showLoading();

    transData(url, type, '', customMessage, isAsync)
        .then((xhr) => {
            for(var i = 0; i < xhr.data.length; i++) {
                $("#"+idTable).DataTable().row.add([
                    xhr.data[i]['pktd_title'],
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                    null,
                ]).draw('false');
            }

            // FILL CELL
            var cellUrl     = getUrl + "/cellProkerBulanan";
            var cellType    = "GET";
            var cellAsync   = false;
            var cellData    = {
                "tgl_awal"  : $("#prokerBulananStartDate").val() != '' ? moment($("#prokerBulananStartDate").val(), 'DD/MM/YYYY').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD'),
                "tgl_akhir" : $("#prokerBulananEndDate").val() != '' ? moment($("#prokerBulananEndDate").val(), 'DD/MM/YYYY').format('YYYY-MM-DD') : moment().format('YYYY-MM-DD'),  
            }
            
            transData(cellUrl, cellType, cellData, '', cellAsync)
                .then((xhr) => {
                    if(xhr.data.length > 0) {
                        for(var j = 0; j < xhr.data.length; j++) {
                            var rowKe   = xhr.data[j]['row_ke'];
                            var cellKe  = xhr.data[j]['cell_ke'];
                            var text    = xhr.data[j]['text'];
                            $("#"+idTable).DataTable().cell(rowKe,cellKe).data(text).draw();
                        }
                    }
                })
                .catch((xhr) => {
                    console.log(xhr);
                });
            Swal.close();
        })
        .catch((xhr) => {
            // Swal.fire({
            //     icon    : 'error',
            //     title   : 'Terjadi Kesalahan',
            //     text    : xhr.status+" "+xhr.statusText,
            // })
            Swal.close();
            console.log(xhr);
        })
}

function tambah_baris(idTable, value)
{
    if(idTable == 'tableDetailProkerBulanan')
    {
        var seq                 = current_seq
        var inputBtnDelete      = "<button type='button' class='btn btn-sm btn-danger' value='" +seq+ "' title='Hapus Baris' id='btnHapus"+seq+"' onclick='hapus_baris(`tableDetailProkerBulanan`, "+seq+")'><i class='fa fa-trash'></i></button>";
        var inputBtnPreview     = "<button type='button' class='btn btn-sm btn-primary' value='"+seq+"' title='Lihat Aktivitas' onclick='showModal(`modalAktivitas`,``, this.value)'><i class='fa fa-eye'></i></button>";
        var inputDetailID       = "<input type='hidden' id='idDetail"+seq+"'>";
        var inputJenisPekerjaan = "<input type='text' class='form-control form-control-sm' id='pkbJenisPekerjaan" +seq+ "' placeholder='Jenis Pekerjaan' autocomplete='off'>";
        var inputTargetSasaran  = "<input type='text' class='form-control form-control-sm' id='pkbTargetSasaran" +seq+ "' placeholder='Target Sasaran' autocomplete='off'>";
        var inputHasil          = "<input type='text' class='form-control form-control-sm' id='pkbHasil" +seq+ "' placeholder='Hasil' autocomplete='off'>";
        var inputEvaluasi       = "<input type='text' class='form-control form-control-sm' id='pkbEvaluasi" +seq+ "' placeholder='Evaluasi' autocomplete='off'>";
        var inputKeterangan     = "<input type='text' class='form-control form-control-sm' id='pkbKeterangan" +seq+ "' placeholder='Keterangan' autocomplete='off'>";

        $("#"+idTable).DataTable().row.add([
            inputBtnPreview,
            inputJenisPekerjaan+""+inputDetailID,
            inputTargetSasaran,
            inputHasil,
            inputEvaluasi,
            inputKeterangan,
        ]).draw('false');

        $("#pkbJenisPekerjaan"+seq).focus();
        
        $("#pkbJenisPekerjaan"+seq).on('keyup', function(e){
            if(e.which == 13) {
                tambah_baris(idTable,'');
            }
        });

        if(value != '') {
            $("#idDetail"+seq).val(value.detail_id);
            $("#pkbJenisPekerjaan"+seq).val(value.jenis_pekerjaan);
            $("#pkbTargetSasaran"+seq).val(value.target_sasaran);
            $("#pkbHasil"+seq).val(value.hasil);
            $("#pkbEvaluasi"+seq).val(value.evaluasi);
            $("#pkbKeterangan"+seq).val(value.keterangan);
        }
        current_seq     = current_seq + 1;
    }
}

function hapus_baris(idTable, seq)
{
    if(seq != 1) {
        if(current_seq - seq == 1) {
            $("#"+idTable).DataTable().row(seq - 1).remove().draw('false');
            current_seq     = current_seq - 1;
        } else {
            console.log('Row Tidak Bisa Dihapus');
        }
    }
}

function show_text(idText, valueCari)
{
    if(idText == 'jadwalProgramUraianPktSeq') {
        // $("#"+idText).val(valueCari);
        var split   = valueCari.split('|');
        
        $("#"+idText).val(split[1]);
        $("#jadwalProgramUraianRulSeq").val(split[0]);
    }
}

function do_save(jenis, arg, calendar)
{
    var prokerBulananID         = $("#prokerBulananID").val();
    var prokerTahunanID         = $("#prokerTahunanID").val();
    var prokerSubTahunanID      = $("#subProkerTahunanSeq").val();
    var groupDivisionID         = $("#prokerTahunanGroupDivisionID").val();
    var groupDivisionName       = $("#prokerTahunanGroupDivisionName").val();
    var subDivisionID           = $("#prokerTahunanSubDivisionID").val();
    var subDivisionName         = $("#prokerTahunanSubDivisionName").val();
    var prokerBulananPIC        = $("#prokerBulananPIC").val();
    var prokerBulananTitle      = $("#prokerBulananTitle").val();
    var prokerBulananDesc       = $("#prokerBulananDesc").val();
    var prokerBulananStartTime  = $("#prokerBulananStartTime").val();
    var prokerBulananEndTime    = $("#prokerBulananEndTime").val();
    var prokerBulananStartDate  = $("#prokerBulananTanggal").val() === undefined ? moment(arg.startStr, 'YYYY-MM-DD').format('DD/MM/YYYY') : $("#prokerBulananTanggal").val();
    var prokerBulananEndDate    = $("#prokerBulananTanggalAkhir").val() === undefined ? moment(arg.startStr, 'YYYY-MM-DD').format('DD/MM/YYYY') :  $("#prokerBulananTanggalAkhir").val();
    // UNTUK UPDATE LAINNYA
    var programJadwalID         = (current_role != 'operasional') ? '' : $("#jadwalProgram").val();
    var programJadwalText       = (current_role != 'operasional') ? '' : $("#jadwalProgram option:selected").text()+" "+$("#jadwalProgramUraian option:selected").text().split('. ')[1];
    var programJadwalRulSeq     = (current_role != 'operasional') ? '' : $("#jadwalProgramUraianRulSeq").val();
    var programJadwalPktSeq     = (current_role != 'operasional') ? '' : $("#jadwalProgramUraianPktSeq").val();
    var totalDetail             = $("#tableDetailProkerBulanan").DataTable().rows().count();
    var prokerBulananDetail     = [];
    for(var i = 0; i < totalDetail; i++) {
        var seq     = i + 1;
        prokerBulananDetail.push({
            "jenisPekerjaan"    : $("#pkbJenisPekerjaan"+seq).val(),
            "targetSasaran"     : $("#pkbTargetSasaran"+seq).val(),
            "hasil"             : $("#pkbHasil"+seq).val(),
            "evaluasi"          : $("#pkbEvaluasi"+seq).val(),
            "keterangan"        : $("#pkbKeterangan"+seq).val(),
        });
    }

    var dataSimpan  = {
        "prokerBulanan_ID"                  : prokerBulananID,
        "prokerBulanan_prokerTahunanID"     : prokerTahunanID,
        "prokerBulanan_subProkerTahunan"    : prokerSubTahunanID == 'lainnya' ? programJadwalPktSeq : prokerSubTahunanID,
        "prokerBulanan_groupDivisionID"     : groupDivisionID,
        "prokerBulanan_groupDivisionName"   : groupDivisionName,
        "prokerBulanan_subDivisionID"       : subDivisionID,
        "prokerBulanan_subDivisionName"     : subDivisionName,
        "prokerBulanan_employeeID"          : prokerBulananPIC,
        "prokerBulanan_title"               : prokerSubTahunanID == 'lainnya' ? programJadwalText : prokerBulananTitle,
        "prokerBulanan_description"         : prokerBulananDesc,
        "prokerBulanan_startDate"           : (prokerBulananStartDate == '' || prokerBulananStartDate === undefined) ? arg.startStr : moment(prokerBulananStartDate, 'DD/MM/YYYY').format('YYYY-MM-DD'),
        "prokerBulanan_endDate"             : (prokerBulananEndDate == '' || prokerBulananEndDate === undefined) ? arg.startStr : moment(prokerBulananEndDate, 'DD/MM/YYYY').format('YYYY-MM-DD'),
        "prokerBulanan_typeTrans"           : jenis,
        "prokerBulanan_detail"              : prokerBulananDetail,
        "prokerBulanan_startActivity"       : prokerBulananStartTime,
        "prokerBulanan_endActivity"         : prokerBulananEndTime,
        "prokerBulanan_file_list"           : penampung.length > 0 ? penampung : null,
        "prokerBulanan_programJadwalID"     : programJadwalID,
        "prokerBulanan_programJadwalRulSeq" : programJadwalRulSeq,
    };

    // CREATE VALIDATE
    if(prokerTahunanID == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Program Kerja Tahunan Harus Dipilih',
        }).then((results)   => {
            if(results.isConfirmed) {
                $("#prokerTahunanID").select2('open');
            }
        });
    } else if(prokerSubTahunanID == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Sub-Program Kerta Tahunan Harus Dipilih',
        }).then((results)   => {
            if(results.isConfirmed) {
                $("#subProkerTahunanSeq").select2('open');
            }
        })
    } else if(prokerBulananPIC == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'PIC / Penanggung Jawab Harus Dipilih',
        }).then((results)   => {
            if(results.isConfirmed) {
                $("#prokerBulananPIC").select2('open');
            }
        })
    } else if(prokerBulananTitle == '' && prokerSubTahunanID != 'lainnya') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Uraian Pekerjaan Tidak Boleh Kosong',
        }).then((results)   => {
            if(results.isConfirmed) {
                $("#prokerBulananTitle").focus();
            }
        })
    } else {
        var url     = getUrl+"/postDataProkerBulanan";
        var type    = "POST";
        var data    = dataSimpan;
        var message =   Swal.fire({
                            title   : 'Data Sedang Diproses',
                            allowOutsideClick: false
                        });
                        Swal.showLoading();
        transData(url, type, data, message, true)
            .then(function(xhr){
                isClick = 1;
                Swal.fire({
                    icon    : xhr.alert.icon,
                    title   : xhr.alert.message.title,
                    text    : xhr.alert.message.text,
                }).then(function(results){
                    if(results.isConfirmed) {
                        calendar.addEvent({
                            title   : dataSimpan['prokerBulanan_title'],
                            start   : arg.startStr,
                            end     : arg.endStr,
                            allDay  : arg.allDay,
                            customValue     : dataSimpan,
                        });
                        isClick = 0;
                        closeModal('modalForm');
                        
                        // SHOW CALENDAR
                        $(".fc-refreshCustomButton-button").click();
                    }
                })
            })
            .catch(function(xhr){
                Swal.fire({
                    icon    : xhr.responseJSON.alert.icon,
                    title   : xhr.responseJSON.alert.message.title,
                    text    : xhr.responseJSON.alert.message.text,
                });
            });
    }
}

function showCalendarButton(jenis)
{
    if(jenis == 'global') {
        // SHOW CALENDAR GLOBAL
        $("#btnCalendarGlobal").addClass('active');
        $("#calendar").show();
        
        var current_date    = moment().format('YYYY-MM-DD');
        var tgl_awal        = $("#prokerBulananStartDate").val() != '' ? moment($("#prokerBulananStartDate").val(), 'DD/MM/YYYY').format('YYYY-MM-DD') : '';
        // var tgl_akhir       = $("#prokerBulananEndDate").val() != '' ? moment($("#prokerBulananEndDate").val()).format('YYYY-MM-DD') : '';
        var tgl_akhir       = $("#prokerBulananEndDate").val() != '' ? moment($("#prokerBulananEndDate").val(), 'DD/MM/YYYY').format('YYYY-MM-DD') : '';
        var group_divisi    = $("#groupDivisionName").val();
        showCalendar(current_date, tgl_awal, tgl_akhir, group_divisi);
        $(".fc-nextCustomButton-button").html("<i class='fa fa-chevron-right'></i>");
        $(".fc-prevCustomButton-button").html("<i class='fa fa-chevron-left'></i>");
        $(".fc-refreshCustomButton-button").html("<i class='fa fa-undo'></i>").prop('title','Refresh Calendar');
        // HIDE CALENDAR OPERASIONAL ONLY
        $("#btnCalendarOperasional").removeClass('active');
        $("#calendarOperasional").hide();
    } else if(jenis == 'operasional') {
        // HIDE CALENDAR GLOBAL
        $("#btnCalendarGlobal").removeClass('active');
        $("#calendar").hide();
        // SHOW CALENDAR OPERASIONAL
        $("#btnCalendarOperasional").addClass('active');
        $("#calendarOperasional").show();
        showDataTable('tableCalendarOperasional')
    }
}

function transData(url, type, data, customBeforeSend, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            async   : isAsync,
            type    : type,
            url     : url,
            data    : {
                _token      : CSRF_TOKEN,
                sendData    : data,
            },
            beforeSend  : function() {
                customBeforeSend;
            },
            success : function(xhr) {
                resolve(xhr);
            },
            error   : function(xhr) {
                console.log(xhr);
                reject(xhr);
            }
        })
    })
}