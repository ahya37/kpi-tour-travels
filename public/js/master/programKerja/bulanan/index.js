$(document).ready(function(){
    console.log('test');
    showCalendar();
});

function showCalendar()
{
    var idCalendar  = document.getElementById('calendar');

    var calendar    = new FullCalendar.Calendar(idCalendar, {
        themeSystem: 'bootstrap',
        headerToolbar: {
            left    : 'prev,next today',
            center  : 'title',
            right   : 'dayGridMonth',
        },
        initialDate: moment().tz('Asia/Jakarta').format('YYYY-MM-DD'), // GET CURRENT DATE
        navLinks: true, // can click day/week names to navigate views
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
            showModal('modalForm','edit', arg.event.id);

            var jenis   = "edit";
            $("#btnSimpan").click(function(){
                do_save(this.value, arg, calendar);
            })
        },
        editable: true,
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
                    var tempData    = [];
                    tempData.push({
                        title   : null,
                        start   : null,
                    });
                });
                
        }
            // {
            //     title: 'All Day Event',
            //     start: '2023-01-01'
            // },
            // {
            //     title: 'Long Event',
            //     start: '2023-01-07',
            //     end: '2023-01-10'
            // },
            // {
            //     groupId: 999,
            //     title: 'Repeating Event',
            //     start: '2023-01-09T16:00:00'
            // },
            // {
            //     groupId: 999,
            //     title: 'Repeating Event',
            //     start: '2023-01-16T16:00:00'
            // },
            // {
            //     title: 'Conference',
            //     start: '2023-01-11',
            //     end: '2023-01-13'
            // },
            // {
            //     title: 'Meeting',
            //     start: '2023-01-12T10:30:00',
            //     end: '2023-01-12T12:30:00'
            // },
            // {
            //     title: 'Lunch',
            //     start: '2023-01-12T12:00:00'
            // },
            // {
            //     title: 'Meeting',
            //     start: '2023-01-12T14:30:00'
            // },
            // {
            //     title: 'Happy Hour',
            //     start: '2023-01-12T17:30:00'
            // },
            // {
            //     title: 'Dinner',
            //     start: '2023-01-12T20:00:00'
            // },
            // {
            //     title: 'Birthday Party',
            //     start: '2023-01-13T07:00:00'
            // },
            // {
            //     title: 'Click for Google',
            //     url: 'http://google.com/',
            //     start: '2023-01-28'
            // },
        // ]
    });

    calendar.render();
    // calendar.on('select', handleEvent);
}

// function handleEvent(info)
// {
//     console.log(info);
// }

function showModal(idModal, jenis, value)
{
    $("#"+idModal).modal({backdrop: 'static', keyboard: false});
    $("#"+idModal).modal('show');
    show_table('tableDetailProkerBulanan');
    $("#btnSimpan").val(jenis);

    if(jenis == 'add') {
        $("#"+idModal).on('shown.bs.modal', function(){
            $("#prokerBulananTitle").focus();
        });

        
        show_select('prokerTahunanID','%','');
        show_select('prokerBulananPIC','','');
        tambah_baris('tableDetailProkerBulanan','');

        var title   = "Tambah Uraian Pekerjaan Tgl. "+moment(value.startStr, 'YYYY-MM-DD').format('DD/MM/YYYY');
        $("#modalTitle").html(title);
    } else if(jenis == 'edit') {
        $("#"+idModal).on('shown.bs.modal', function(){
            $("#prokerTahunanID").prop('disabled',true);
        });
        
        var url     = "/master/programkerja/bulanan/getDataAllProkerBulanan";
        var type    = "GET";
        var data    = {
            "cari"  : value,
        }

        transData(url, type, data,'', false)
            .then(function(xhr){
                var resultData  = xhr.data.header[0];
                $("#prokerBulananID").val(resultData['pkb_uuid']);
                $("#prokerTahunanGroupDivisionID").val(resultData['pkb_gd_id']);
                $("#prokerTahunanGroupDivisionName").val(resultData['pkb_gd_name']);
                $("#prokerTahunanSubDivisionID").val(resultData['pkb_sd_id']);
                $("#prokerTahunanSubDivisionName").val(resultData['pkb_sd_name']);
                $("#prokerBulananTitle").val(resultData['pkb_title']);
                $("#prokerBulananDesc").val(resultData['pkb_description']);

                show_select('prokerTahunanID','%', resultData['pkb_pkt_id']);
                show_select('prokerBulananPIC', resultData['pkb_gd_id'], resultData['pkb_employee_id']);
                
                if(xhr.data.detail.length > 0) {
                    for(var  i = 0; i < xhr.data.detail.length; i++) {
                        tambah_baris('tableDetailProkerBulanan', xhr.data.detail[i]);
                    }
                }

                tambah_baris('tableDetailProkerBulanan','');
            })
            .catch(function(xhr){
                console.log(xhr);
            })
    }
}

function closeModal(idModal) {
    $("#"+idModal).modal('hide');
    $("#"+idModal).on('hidden.bs.modal', function(){
        $("#btnSimpan").off('click');
        $("#prokerBulananTitle").val(null);
        $("#prokerBulananDesc").val(null);
        $("#prokerTahunanGroupDivisionName").val(null);
        $("#btnSimpan").val(null);
        $("#prokerBulananID").val(null);
        $("#btnTambahBaris").val(1);
        $("#prokerTahunanID").prop('disabled', false);
    });
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
                { "targets": [0], "className":"text-center", "width":"5%" },
                { "targets":[1, 2, 3, 4], "width":"17%" },
            ],
        })
    }
} 

function tambah_baris(idTable, value)
{
    if(idTable == 'tableDetailProkerBulanan')
    {
        var currentSeq          = $("#btnTambahBaris").val();
        var inputBtnDelete      = "<button type='button' class='btn btn-sm btn-danger' value='" +currentSeq+ "' title='Hapus Baris' onclick='hapus_baris(`tableDetailProkerBulanan`, "+currentSeq+")'><i class='fa fa-trash'></i></button>";
        var inputJenisPekerjaan = "<input type='text' class='form-control form-control-sm' id='pkbJenisPekerjaan"+currentSeq+"' placeholder='Jenis Pekerjaan' autocomplete='off'>";
        var inputTargetSasaran  = "<input type='text' class='form-control form-control-sm' id='pkbTargetSasaran"+currentSeq+"' placeholder='Target Sasaran' autocomplete='off'>";
        var inputHasil          = "<input type='text' class='form-control form-control-sm' id='pkbHasil"+currentSeq+"' placeholder='Hasil' autocomplete='off'>";
        var inputEvaluasi       = "<input type='text' class='form-control form-control-sm' id='pkbEvaluasi"+currentSeq+"' placeholder='Evaluasi' autocomplete='off'>";
        var inputKeterangan     = "<input type='text' class='form-control form-control-sm' id='pkbKeterangan"+currentSeq+"' placeholder='Keterangan' autocomplete='off'>";

        $("#"+idTable).DataTable().row.add([
            inputBtnDelete,
            inputJenisPekerjaan,
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