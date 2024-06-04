var getUrl  = window.location.pathname;

$(document).ready(function(){
    var current_date    = moment().tz('Asia/Jakarta').format('YYYY-MM-DD');
    showCalendar(current_date);
});

function showCalendar(tanggalCari)
{
    var idCalendar  = document.getElementById('calendar');

    var calendar    = new FullCalendar.Calendar(idCalendar, {
        themeSystem: 'bootstrap',
        headerToolbar: {
            left    : 'prev next',
            center  : 'title',
            right   : 'dayGridMonth',
        },
        locale  : 'id',
        initialDate     : tanggalCari,
        navLinks: false, // can click day/week names to navigate views
        selectable: true,
        selectMirror: true,
        contentHeight: 600,
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
        eventClick: function(arg) {
            var idModal     = "modalForm";
            var jenis       = "edit";
            var value       = arg.event.id;

            showModal(idModal, jenis, value);
            console.log({idModal, jenis, value});
            
            $("#btnSimpan").click(function(){
                do_save(this.value, arg, calendar);
            });
        },
        editable: false,
        fixedMirrorParent: document.body,
        dayMaxEvents: true, // allow "more" link when too many events
        events: function(fetchInfo, successCallback, failureCallback) {
            var url         = "/master/programkerja/bulanan/getDataAllProkerBulanan";
            var type        = "GET";
            var data        = {
                "cari"      : "%",
            };
            var message     =   Swal.fire({
                                    title   : 'Data Sedang Dimuat',
                                });
                                Swal.showLoading();
            var isAsync     = true;
            transData(url, type, data, message, isAsync)
                .then(function(xhr){
                    Swal.close();
                    var tempData    = [];
                    for(var i = 0; i < xhr.data.header.length; i++) {
                        tempData.push({
                            title   : xhr.data.header[i]['pkb_title'],
                            start   : xhr.data.header[i]['pkb_start_date'], 
                            end     : null,
                            allDay  : true,
                            id      : xhr.data.header[i]['pkb_uuid'],
                        });
                    }
                    successCallback(tempData);
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

function showModal(idModal, jenis, value)
{
    $("#"+idModal).modal({backdrop: 'static', keyboard: false});
    $("#"+idModal).modal('show');
    if(idModal == 'modalForm') {
        show_table('tableDetailProkerBulanan');
        $("#btnSimpan").val(jenis);
        $(".fc .fc-popover").hide();

        if(jenis == 'add') {
            $("#prokerTahunanID").prop('disabled',false);
            $("#"+idModal).on('shown.bs.modal', function(){
                $("#prokerBulananTitle").focus();
            });

            show_select('prokerTahunanID','%','');
            show_select('prokerBulananPIC','','');
            show_select('subProkerTahunanSeq','','');
            tambah_baris('tableDetailProkerBulanan','');

            var title   = "Tambah Uraian Pekerjaan Tgl. "+moment(value.startStr, 'YYYY-MM-DD').format('DD/MM/YYYY');
            $("#modalTitle").html(title);
        } else if(jenis == 'edit') {
            $("#prokerTahunanID").prop('disabled',true);
            
            var url     = "/master/programkerja/bulanan/getDataAllProkerBulanan";
            var type    = "GET";
            var data    = {
                "cari"  : value,
            }
            var isAsync = true;

            // SHOW MESSAGE
            if(isAsync == true) {
                var customMessage   = Swal.fire({title:'Data Sedang Dimuat'});Swal.showLoading();
            }

            transData(url, type, data, customMessage, isAsync)
                .then(function(xhr){
                    var resultData  = xhr.data.header[0];
                    $("#prokerBulananID").val(resultData['pkb_uuid']);
                    $("#prokerTahunanGroupDivisionID").val(resultData['pkb_gd_id']);
                    $("#prokerTahunanGroupDivisionName").val(resultData['pkb_gd_name']);
                    $("#prokerTahunanSubDivisionID").val(resultData['pkb_sd_id']);
                    $("#prokerTahunanSubDivisionName").val(resultData['pkb_sd_name']);
                    $("#prokerBulananTitle").val(resultData['pkb_title']);
                    $("#prokerBulananDesc").val(resultData['pkb_description']);

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
        // TUTUP MODAL SEBELUMNYA
        closeModal('modalForm');
        // OPEN MODAL SELANJUTNYA
        show_table('tableActivityUser');
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
        var customMessage   = Swal.fire({title:'Data Sedang Dimuat'});Swal.showLoading();

        transData(url, type, data, customMessage, isAsync)
            .then(function(xhr){
                var getData     = xhr.data.header;
                var getFile     = xhr.data.file;

                if(getData.length > 0) {
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
                                var file_name   = getFile[j]['file_name'].length > 25 ? getFile[j]['file_name'].substring(0, 25) + '...' : getFile[j]['file_name'];
                                pkhd_bukti      += "<li><a href='#'>" + file_name + "</a></li>";
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
                }
                Swal.close();
            })
            .catch(function(xhr){
                console.log(xhr);
            });
    }
}

function closeModal(idModal) {
    $("#"+idModal).modal('hide');
    if(idModal == 'modalForm') {
        $("#"+idModal).on('hidden.bs.modal', function(){
            $("#btnSimpan").off('click');
            $("#prokerBulananTitle").val(null);
            $("#prokerBulananDesc").val(null);
            $("#prokerTahunanGroupDivisionName").val(null);
            $("#btnSimpan").val(null);
            $("#prokerBulananID").val(null);
            $("#btnTambahBaris").val(1);
        });
    } else if(idModal == 'modalAktivitas') {
        var pkb_ID  = $("#prokerBulananID_Activity").val();
        showModal('modalForm', 'edit', pkb_ID);
    }
}

function show_select(idSelect, valueCari, valueSelect)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
    if(idSelect == 'prokerTahunanID') {
        var url     = "/master/programkerja/bulanan/getDataProkerTahunan";
        var type    = "GET";
        var data    = {
            "prokerID"  : valueCari,
        };
        var html    = "<option selected disabled>Pilih Program Kerja Tahunan</option>";
        transData(url, type, data, '', false)
            .then(function(xhr){
                $.each(xhr.data, function(i,item){
                    var pktUID      = item['pkt_uid'];
                    var pktTitle    = item['pkt_title'];

                    html    += "<option value='" + pktUID + "'>" + pktTitle + "</option>";
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
    } else if(idSelect == 'prokerBulananPIC') {
        var html    = "<option selected disabled>Pilih PIC / Penanggung Jawab</option>";

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
    } else if(idSelect == 'subProkerTahunanSeq') {
        var html    = "<option selected disabled>Sub-Program Kerja Tahunan</option>";
        var url     = "/master/programkerja/bulanan/getDataSubProkerTahunan";
        var type    = "GET";
        var data    = {
            "prokerTahunan_ID"  : valueCari,
        };
        var isAsync = false;

        if(valueCari != '') {
            transData(url, type, data, '', isAsync)
            .then((xhr) => {
                $.each(xhr.data['detail'], function(i,item){
                    html    += "<option value='"+ item['detail_seq'] +"'>" + item['detail_title'] + "</option>";
                })
                $("#"+idSelect).html(html);
                if(valueSelect != '') {
                    $("#"+idSelect).val(valueSelect).trigger('change');
                }
            })
            .catch((xhr) => {
                console.log(xhr);
                $("#"+idSelect).html(html);
            })
        }
        $("#"+idSelect).html(html);
    }
}

function show_select_detail(idSelect, valueCari) {
    if(idSelect == 'prokerTahunanID') {
        var url     = "/master/programkerja/bulanan/getDataProkerTahunan";
        var type    = "GET";
        var data    = {
            "prokerID"  : valueCari,
        };
        transData(url, type, data, '', false)
            .then(function(xhr){
                var resultData  = xhr.data[0];
                $("#prokerTahunanGroupDivisionID").val(resultData['group_division_id']);
                $("#prokerTahunanGroupDivisionName").val(resultData['group_division_name']);
                $("#prokerTahunanSubDivisionID").val(resultData['sub_division_id']);
                $("#prokerTahunanSubDivisionName").val(resultData['sub_division_name']);

                show_select('prokerBulananPIC', resultData['group_division_id'],'');
            })
            .catch(function(xhr){
                console.log(xhr);
            });

        show_select('subProkerTahunanSeq', valueCari, '');
    }
}

function show_table(idTable)
{
    if(idTable == 'tableDetailProkerBulanan') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            searching   : false,
            paging      : false,
            bInfo       : false,
            ordering    : false,
            autoWidth   : false,
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
    }
} 

function tambah_baris(idTable, value)
{
    if(idTable == 'tableDetailProkerBulanan')
    {
        var currentSeq          = $("#btnTambahBaris").val();
        var inputBtnDelete      = "<button type='button' class='btn btn-sm btn-danger' value='" +currentSeq+ "' title='Hapus Baris' onclick='hapus_baris(`tableDetaislProkerBulanan`, "+currentSeq+")'><i class='fa fa-trash'></i></button>";
        var inputBtnPreview     = "<button type='button' class='btn btn-sm btn-primary' value='"+currentSeq+"' title='Lihat Aktivitas' onclick='showModal(`modalAktivitas`,``, this.value)'><i class='fa fa-eye'></i></button>";
        var inputDetailID       = "<input type='hidden' id='idDetail"+currentSeq+"'>";
        var inputJenisPekerjaan = "<input type='text' class='form-control form-control-sm' id='pkbJenisPekerjaan"+currentSeq+"' placeholder='Jenis Pekerjaan' autocomplete='off'>";
        var inputTargetSasaran  = "<input type='text' class='form-control form-control-sm' id='pkbTargetSasaran"+currentSeq+"' placeholder='Target Sasaran' autocomplete='off'>";
        var inputHasil          = "<input type='text' class='form-control form-control-sm' id='pkbHasil"+currentSeq+"' placeholder='Hasil' autocomplete='off'>";
        var inputEvaluasi       = "<input type='text' class='form-control form-control-sm' id='pkbEvaluasi"+currentSeq+"' placeholder='Evaluasi' autocomplete='off'>";
        var inputKeterangan     = "<input type='text' class='form-control form-control-sm' id='pkbKeterangan"+currentSeq+"' placeholder='Keterangan' autocomplete='off'>";

        $("#"+idTable).DataTable().row.add([
            inputBtnDelete+" "+inputBtnPreview,
            inputJenisPekerjaan+""+inputDetailID,
            inputTargetSasaran,
            inputHasil,
            inputEvaluasi,
            inputKeterangan,
        ]).draw('false');
        $("#pkbJenisPekerjaan"+currentSeq).focus();
        
        $("#pkbJenisPekerjaan"+currentSeq).on('keyup', function(e){
            if(e.which == 13) {
                tambah_baris(idTable,'');
            }
        });

        if(value != '') {
            $("#idDetail"+currentSeq).val(value.detail_id);
            $("#pkbJenisPekerjaan"+currentSeq).val(value.jenis_pekerjaan);
            $("#pkbTargetSasaran"+currentSeq).val(value.target_sasaran);
            $("#pkbHasil"+currentSeq).val(value.hasil);
            $("#pkbEvaluasi"+currentSeq).val(value.evaluasi);
            $("#pkbKeterangan"+currentSeq).val(value.keterangan);
        }

        $("#btnTambahBaris").val(parseInt(currentSeq) + 1);
    }
}

function hapus_baris(idTable, seq) {
    if(idTable == 'tableDetailProkerBulanan') {
        var btnSeq  = $("#btnTambahBaris").val();
        if(seq != '1') {
            if(parseInt(btnSeq) - parseInt(seq) == 1) {
                $("#"+idTable).DataTable().row(seq - 1).remove().draw('false')
                $("#btnTambahBaris").val(btnSeq  - 1);
                $("#pkbJenisPekerjaan"+(seq - 1)).focus();
            }
        }
    }
}

function do_save(jenis, arg, calendar)
{
    var prokerBulananID     = $("#prokerBulananID").val();
    var prokerTahunanID     = $("#prokerTahunanID").val();
    var prokerSubTahunanID  = $("#subProkerTahunanSeq").val();
    var groupDivisionID     = $("#prokerTahunanGroupDivisionID").val();
    var groupDivisionName   = $("#prokerTahunanGroupDivisionName").val();
    var subDivisionID       = $("#prokerTahunanSubDivisionID").val();
    var subDivisionName     = $("#prokerTahunanSubDivisionName").val();
    var prokerBulananPIC    = $("#prokerBulananPIC").val();
    var prokerBulananTitle  = $("#prokerBulananTitle").val();
    var prokerBulananDesc   = $("#prokerBulananDesc").val();
    var totalDetail         = $("#tableDetailProkerBulanan").DataTable().rows().count();
    var prokerBulananDetail = [];
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
        "prokerBulanan_subProkerTahunan"    : prokerSubTahunanID,
        "prokerBulanan_groupDivisionID"     : groupDivisionID,
        "prokerBulanan_groupDivisionName"   : groupDivisionName,
        "prokerBulanan_subDivisionID"       : subDivisionID,
        "prokerBulanan_subDivisionName"     : subDivisionName,
        "prokerBulanan_employeeID"          : prokerBulananPIC,
        "prokerBulanan_title"               : prokerBulananTitle,
        "prokerBulanan_description"         : prokerBulananDesc,
        "prokerBulanan_startDate"           : arg.startStr,
        "prokerBulanan_typeTrans"           : jenis,
        "prokerBulanan_detail"              : prokerBulananDetail,
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
    } else if(prokerBulananTitle == '') {
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
        var url     = "/master/programkerja/bulanan/postDataProkerBulanan";
        var type    = "POST";
        var data    = dataSimpan;
        var message =   Swal.fire({
                            title   : 'Data Sedang Diproses',
                        });
                        Swal.showLoading();

        transData(url, type, data, message, true)
            .then(function(xhr){
                console.log(xhr);
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
                        closeModal('modalForm');
                        showCalendar();
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