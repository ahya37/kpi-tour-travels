var site_url    = window.location.pathname;
var isOpen      = 0;
var isCalendarLoaded    = 0;
var today       = moment().format('YYYY-MM-DD');
$(document).ready(function(){
    $("#tableList").DataTable({
        language    : {
            "emptyTable"    : "Tidak ada data yang bisa ditampilkan",
            "zeroRecords"   : "Tidak ada data yang bisa ditampilkan",
        },
    });
});

function showCalendar(tgl_sekaramg)
{
    var tgl_sekarang    = tgl_sekaramg;
    var idCalendar      = document.getElementById('calendar');
    var calendar        = new FullCalendar.Calendar(idCalendar,{
        headerToolbar: {
            left    : 'prevCustomButton nextCustomButton',
            center  : '',
            right   : 'refreshCustomButton dayGridMonth',
        },
        themeSystem     : 'bootstrap',
        locale          : 'id',
        eventDisplay    : 'block',
        initialDate     : tgl_sekarang,
        navLinks        : false,
        selectable      : true,
        selectMirror    : true,
        contentHeight   : 650,
        editable        : false,
        dayMaxEvents    : true,
        events          : function(fetchInfo, successCallback, failureCallback)
        {
            var current_date    = tgl_sekaramg;
            var start_date      = moment(current_date, 'YYYY-MM-DD').startOf('month').format('YYYY-MM-DD');
            var end_date        = moment(current_date, 'YYYY-MM-DD').endOf('month').format('YYYY-MM-DD');

            var url             = '/marketings/programKerja/jenisPekerjaan/dataEventsCalendar';
            var data            = {
                "start_date"    : start_date,
                "end_date"      : end_date
            };
            var type            = "GET";
            var message         = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();
            var isAsync         = true;
            
            doTrans(url, type, data, message, isAsync)
                .then((success) => {
                    var temp    = [];
                    for(var i = 0; i < success.data.length; i++) {
                        temp.push({
                            title   : success.data[i].pkh_title,
                            start   : success.data[i].pkh_date,
                            end     : moment(success.data[i].pkh_date, 'YYYY-MM-DD').add(1, 'days').format('YYYY-MM-DD'),
                            allDay  : true,
                            id      : success.data[i].uuid
                        });
                    }
                    successCallback(temp);
                    Swal.close();
                })
                .catch((err)    => {
                    Swal.close();
                    console.log(err);
                })
        },
        select  : function(arg) {
            // console.log(arg);
            show_modal('modalTransJenisPekerjaan', 'add', arg);
        },
        eventClick  : function(arg) {
            var jpk_id  = arg.event.id;
            show_modal('modalTransJenisPekerjaan', 'edit', jpk_id);
        },
        customButtons: {
            prevCustomButton: {
                // text: "",
                click: function() {
                    var hari_ini_bulan_lalu         = moment(today).subtract(1, 'month').format('YYYY-MM-DD');
                    showCalendar(hari_ini_bulan_lalu);
                    today   = hari_ini_bulan_lalu;
                    // var hari_ini                = $("#current_date").val();
                    // var groupDivision           = $("#groupDivisionName").val();
                    // var hari_ini_bulan_lalu     = moment(hari_ini).subtract(1, 'month').format('YYYY-MM-DD');
                    // var tgl_awal_bulan_lalu     = moment(hari_ini_bulan_lalu).startOf('month').format('YYYY-MM-DD');
                    // var tgl_akhir_bulan_lalu    = moment(hari_ini_bulan_lalu).endOf('month').format('YYYY-MM-DD');
                    // showCalendar(tgl_awal_bulan_lalu, tgl_awal_bulan_lalu, tgl_akhir_bulan_lalu, groupDivision);
                    // $("#current_date").val(hari_ini_bulan_lalu);
                    // // VISUAL UPDATE
                    // $("#titleBulan").html(moment(hari_ini_bulan_lalu).format('MMMM'));
                    // $("#titleTahun").html(moment(hari_ini_bulan_lalu).format('YYYY'));
                    // // UPDATE FILTER
                    // $("#prokerBulananStartDate").val(moment(tgl_awal_bulan_lalu).format('DD/MM/YYYY'));
                    // $("#prokerBulananEndDate").val(moment(tgl_akhir_bulan_lalu).format('DD/MM/YYYY'));
                }
            },
            nextCustomButton : {
                click : function() {
                    var hari_ini_bulan_depan         = moment(today).add(1, 'month').format('YYYY-MM-DD');
                    showCalendar(hari_ini_bulan_depan);
                    today   = hari_ini_bulan_depan;
                    // var today                   = $("#current_date").val();
                    // var groupDivision           = $("#groupDivisionName").val();
                    // var hari_ini_bulan_depan    = moment(today).add(1, 'month').format('YYYY-MM-DD');
                    // var tgl_awal_bulan_depan    = moment(hari_ini_bulan_depan).startOf('month').format('YYYY-MM-DD');
                    // var tgl_akhir_bulan_depan   = moment(hari_ini_bulan_depan).endOf('month').format('YYYY-MM-DD');
                    // showCalendar(tgl_awal_bulan_depan, tgl_awal_bulan_depan, tgl_akhir_bulan_depan, groupDivision);
                    // $("#current_date").val(hari_ini_bulan_depan);
                    // // VISUAL UPDATE
                    // $("#titleBulan").html(moment(hari_ini_bulan_depan).format('MMMM'));
                    // $("#titleTahun").html(moment(hari_ini_bulan_depan).format('YYYY'));
                    // // UPDATE FILTER
                    // $("#prokerBulananStartDate").val(moment(tgl_awal_bulan_depan).format('DD/MM/YYYY'));
                    // $("#prokerBulananEndDate").val(moment(tgl_akhir_bulan_depan).format('DD/MM/YYYY'));
                }
            },
            refreshCustomButton     : {
                click   : function() {
                    today   = moment().format('YYYY-MM-DD');
                    showCalendar(today);
                    // var today           = $("#current_date").val();
                    // var groupDivision   = $("#groupDivisionName").val();
                    // var tgl_awal        = moment(today).startOf('month').format('YYYY-MM-DD');
                    // var tgl_akhir       = moment(today).endOf('month').format('YYYY-MM-DD');
                    // showCalendar(today, tgl_awal, tgl_akhir, groupDivision);
                }
            }
        },
    });
    calendar.render();
}


function show_modal(id_modal, jenis, value)
{
    if(id_modal == 'modalTableProgram') {
        $("#"+id_modal).modal({ backdrop : 'static', keyboard : false });
        $("#"+id_modal).modal('show');
        
        show_table('table_list_program', '%');
    } else if(id_modal == 'modalProgram') {
        // CLOSE MODAL BEFORE
        close_modal('modalTableProgram');
        // GET ALL DATA
        const getDataSasaran    = doTrans('/marketings/programKerja/program/listSelectSasaranMarketing', 'get', '', '', true);
        const getDataProgram    = value != '' ? doTrans('/marketings/programKerja/program/listSelectedProgramMarketing', 'get', value, '', true) : '';

        var request     = [
            getDataSasaran,
            getDataProgram,
        ];
        
        // SHOW LOADING
        Swal.fire({
            title    : 'Data Sedang Dimuat',
        });
        Swal.showLoading();

        // SHOW DATA
        Promise.all(request)
            // IF TRUE
            .then(function(response) {
                // SHOW SELECT
                var data_sasaran     = response[0].data.detail;
                show_select('program_sasaranID', data_sasaran, '', '');
                show_select('program_bulan', '%', '', true);
                
                // DEFINE HEADER AND TITLE
                if(jenis == 'add') {
                    $("#modalProgram_title").html('Tambah Data Program');
                    $("#modalProgram_header").html("Tambah Data Program");
                    
                    $("#program_bulan").val(moment().format('MM')).trigger('change');

                    show_table('table_jenis_pekerjaan', '');
                    tambah_baris('table_jenis_pekerjaan', '');

                } else if(jenis == 'edit') {
                    var data_program            = response[1].data;
                    $("#modalProgram_title").html('Ubah Data Program');
                    $("#modalProgram_header").html("Ubah Data Program");

                    // FILL FORM
                    $("#program_ID").val(value);
                    $("#program_title").val(data_program.programTitle);
                    $("#program_sasaranID").val(data_program.program_sasaranID+" | "+data_program.program_sasaranSequence).trigger('change');
                    $("#program_bulan").val(moment(data_program.program_bulan, 'YYYY-MM-DD').format('MM')).trigger('change');
                    
                    show_table('table_jenis_pekerjaan', '');
                    for(var i = 0; i < data_program.program_detail.length; i++) {
                        tambah_baris('table_jenis_pekerjaan', data_program.program_detail[i]);
                    }
                    tambah_baris('table_jenis_pekerjaan', '');
                }

                // SHOW MODAL
                $("#"+id_modal).modal({backdrop: 'static', keyboard: false});
                $("#"+id_modal).modal('show');
                $("#"+id_modal).on('shown.bs.modal', function(){
                    // $("#program_sasaranID").select2('open');
                    $("#program_title").focus();
                });

                // CLOSE SWAL
                Swal.close();

                $("#btnSimpan").val(jenis);
            })
            // IF FALSE
            .catch(function(error){
                console.log(error);
                Swal.fire({
                    icon    : 'error',
                    title   : 500,
                    text    : error.statusText,
                }).then((results)   => {
                    show_modal('modalTableProgram', '', '');
                });
            });
    } else if(id_modal == 'modalJenisPekerjaan') {
        $("#"+id_modal).modal({ backdrop : 'static', keyboard : false });
        $("#"+id_modal).modal('show');

        $("#"+id_modal).on('shown.bs.modal', function(){
            isOpen == 1;
            isCalendarLoaded    = 1;
            showCalendar(today);
            $(".fc-nextCustomButton-button").html("<i class='fa fa-chevron-right'></i>").prop('title', 'Bulan Selanjutnya');
            $(".fc-prevCustomButton-button").html("<i class='fa fa-chevron-left'></i>").prop('title', 'Bulan Sebelumnya');
            $(".fc-refreshCustomButton-button").html("<i class='fa fa-undo'></i>").prop('title','Hari ini');
        });
    } else if(id_modal == 'modalTransJenisPekerjaan') {
        $("#jpk_btnSimpan").val(jenis);
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

        var getData     = [
            doTrans('/marketings/programKerja/jenisPekerjaan/dataProgram', 'GET', '', '', true),
            jenis == 'edit' ? doTrans('/marketings/programKerja/jenisPekerjaan/dataDetailEventsCalendar/'+value, "GET", '', '', true) : '',
        ];

        Swal.fire({
            title   : 'Data Sedang Dimuat',
        });
        Swal.showLoading();

        Promise.all(getData)
            .then((success) => {
                $("#"+id_modal).modal({ backdrop : 'static', keyboard : false });
                $("#"+id_modal).modal('show');

                show_select('jpk_programID', success[0], '', true);
                if(jenis == 'add') {
                    show_select('jpk_programDetail', '', '', true);
                    $("#jpk_date").val(value.startStr);
                } else if(jenis == 'edit') {
                    var getData     = success[1].data[0];
                    var masterID    = getData.master_program_id;
                    var programID   = getData.pkh_pkb_id;
                    var start_time  = getData.pkh_start_time.split(' ')[1];
                    var end_time    = getData.pkh_end_time.split(' ')[1];
                    var title       = getData.pkh_title;
                    var pkh_date    = getData.pkh_date;
                    var description = "";

                    $("#jpk_ID").val(value);
                    $("#jpk_date").val(pkh_date);
                    $("#jpk_programID").val(masterID);
                    $("#jpk_start_time").val(start_time);
                    $("#jpk_end_time").val(end_time);
                    $("#jpk_title").val(title);
                    $("#jpk_description").val(description);
                    show_select('jpk_programDetail', masterID, programID, true);
                }
                Swal.close();
            })
            .catch((err)    => {
                console.log(err);
            })
    }
}


function close_modal(id_modal)
{
    if(id_modal == 'modalTableProgram') {
        $("#"+id_modal).modal('hide');
    } else if(id_modal == 'modalProgram') {
        $("#"+id_modal).modal('hide');

        // RESET FORM
        $("#"+id_modal).on('hidden.bs.modal', function(){
            $("#program_title").val(null);
            $("#program_ID").val(null);
            $("#btnTambahData").val("1");
        });

        // OPEN MODAL BEFORE
        show_modal('modalTableProgram', '', '');
    } else if(id_modal == 'modalJenisPekerjaan') {
        $("#"+id_modal).modal('hide');

        isOpen == 0;
    } else if(id_modal == 'modalTransJenisPekerjaan') {
        $("#"+id_modal).modal('hide');

        $("#"+id_modal).on('hidden.bs.modal', ()=>{
            $(".waktu").val('00:00:00');
            $("#jpk_title").val(null);
            $("#jpk_description").val(null);
            $("#jpk_date").val(null);
            today  = moment().format('YYYY-MM-DD');
        });
    }
}

function show_select(id_select, valueCari, valueSelect, isAsync)
{
    $("#"+id_select).select2({
        theme   : 'bootstrap4',
    });

    if(id_select == 'program_sasaranID')
    {
        var html     = "<option selected disabled>Pilih Sasaran Program</option>";

        if(valueCari != '') {
            $("#"+id_select).html(html);
            // GET DATA PROGRAM
            var getData     = valueCari;
            $.each(getData, (i, item)=> {
                var pkt_id      = item.pkt_id;
                var pkt_title   = item.pktd_title;
                var pkt_target  = item.pktd_target > 0 ? "( Target : "+ item.pktd_target +" )" : "";
                var pkt_seq     = item.pktd_seq;

                html    += "<option value='" + pkt_id + " | " + pkt_seq + "'>" + pkt_title + " "+ pkt_target +"</option>";
            })
            $("#"+id_select).html(html);
            
            if(valueSelect != '') {
                $("#"+id_select).select2('val', valueSelect);
            }

            $("#"+id_select).on('select2:select', function(){
                var prev_seq    = parseInt($("#btnTambahData").val()) - 1;
                $("#jk_title"+prev_seq).focus();
            });
        } else { 
            $("#"+id_select).html(html);
        }
    } else if(id_select == 'program_bulan') {
        var html    = "<option selected disabled>Pilih Bulan</option>";

        if(valueCari != '') {
            const getMonth  = moment.months();
            $.each(getMonth, function(i, item){
                const monthNumber   = moment(item, 'MMM').format('MM');
                const monthName     = item;

                html    += "<option value='" + monthNumber + "'>" + monthName + "</option>";
            });

            $("#"+id_select).html(html);
        }
    } else if(id_select == 'jpk_programID') {
        var html    = "<option selected disabled>Pilih Program</option>";
        
        if(valueCari != '') {
            $.each(valueCari.data, (i, item) => {
                html    += "<option value='" + item.id + "'>" + item.name + " - " + item.pktd_title + "</option>";
            });
            $("#"+id_select).html(html);
        } else {
            $("#"+id_select).html(html);   
        }
    } else if(id_select == 'jpk_programDetail') {
        var html    = "<option selected disabled>Pilih Program Detail</option>";
        
        if(valueCari != '') {
            var url     = "/marketings/programKerja/jenisPekerjaan/dataProgramDetail/"+valueCari;
            var type    = "GET";
            var data    = "";
            var message = Swal.fire({ title : 'Data Sedang Dimuat' }); Swal.showLoading();

            doTrans(url, type, data, message, isAsync)
                .then((success) => {
                    $.each(success.data,(i, item)   => {
                        html    += "<option value='" + item.pkb_id +   " | " + item.pkbd_id + "'>" + item.pkbd_title + "</option>";
                    });
                    $("#"+id_select).html(html);

                    Swal.close();
                    if(valueSelect != '') {
                        $("#"+id_select).val(valueSelect).trigger('change');
                    } else {
                        $("#"+id_select).select2('open');
                    }
                })
                .catch((err)    => {
                    Swal.close();
                    $("#"+id_select).html(html);
                });
        } else {
            $("#"+id_select).html(html);   
        }
    }
}

function show_table(id_table, value)
{
    if(id_table == 'table_list_program') {
        $("#"+id_table).DataTable().clear().destroy();
        $("#"+id_table).DataTable({
            language    : {
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "emptyTable"    : "Tidak ada data yang bisa ditampilkan..",
                "zeroRecords"    : "Tidak ada data yang bisa ditampilkan..",
            },
            processing  : true,
            serverSide  : false,
            ajax        : {
                url     : "/marketings/programKerja/program/listProgramMarketing",
                type    : "GET",
                dataType: "json",
                data    : {
                    "_token"    : CSRF_TOKEN,
                    "sendData"  : '%',
                },
            },
            columnDefs  : [
                { "targets" : [1, 3], "className" : "align-middle" },
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [2], "className" : "text-left align-middle", "width" : "20%" },
                { "targets" : [4], "className" : "text-right align-middle", "width" : "5%" },
                { "targets" : [5], "className" : "text-center align-middle", "width" : "15%" },
            ],
            footerCallback: function (tr, data, start, end, display) {
                var api = this.api();
                $(api.column(4).footer()).html(
                    api
                        .column(4)
                        .data()
                        .reduce(function (a, b) {
                            return parseInt(a) + parseInt(b);
                        }, 0)
                );
            }
        });
    } else if(id_table == 'table_jenis_pekerjaan') {
        $("#"+id_table).DataTable().clear().destroy();
        $("#"+id_table).DataTable({
            language    : {
                "emptyTable"    : "Tidak ada data data..",
                "zeroRecords"   : "Tidak ada data data..",
            },
            columnDefs  : [
                { "targets" : [0], "width" : "5%", "className" : "text-center" },
                { "targets" : [1], "width" : "10%", "className" : "text-center" },
                { "targets" : [3], "width" : "15%", "className" : "text-right" },
            ],
            ordering    : false,
            searching   : false,
            bInfo       : false,
            autoWidth   : false,
            paging      : false,
        });
    }
}

function tambah_baris(id_table, value)
{
    if(id_table == 'table_jenis_pekerjaan') {
        var seq     = $("#btnTambahData").val();

        var input_delete    = "<button class='btn btn-sm btn-danger' value='"+ seq +"' id='jk_btn_delete"+seq+"' title='Hapus Data' onclick='hapus_baris(`table_jenis_pekerjaan`, "+seq+")'><i class='fa fa-trash'></i></button>";
        var input_seq       = "<input type='text' class='form-control form-control-sm text-center' id='jk_seq"+seq+"' placeholder='seq' readonly>";
        var input_title     = "<input type='text' class='form-control form-control-sm' id='jk_title"+seq+"' placeholder='Title'>";
        var input_target    = "<input type='number' class='form-control form-control-sm text-right' onclick='this.select()' id='jk_target"+seq+"' placeholder='Target' max='9999' min='0' step='1'>";

        $("#"+id_table).DataTable().row.add([
            input_delete,
            input_seq,
            input_title,
            input_target,
        ]).draw('false');

        $("#jk_seq"+seq).val(seq);

        $("#jk_title"+seq).on('keyup', function(e){
            if(e.key === "Enter") {
                e.preventDefault();
                tambah_baris('table_jenis_pekerjaan', '');
            }
        });

        $("#jk_target"+seq).on('keyup', function(e){
            if(e.key === "Enter") {
                e.preventDefault();
                tambah_baris('table_jenis_pekerjaan', '');
            }
        });

        if(seq != 1) {
            $("#jk_title"+seq).focus();
        }

        if(value != '') {
            $("#jk_title"+seq).val(value.pkbd_title);
            $("#jk_target"+seq).val(value.pkbd_num_target);
        } else {
            $("#jk_target"+seq).val(0);
        }

        $("#btnTambahData").val(parseInt(seq) + 1);
    }
}

function hapus_baris(id_table, seq)
{
    if(id_table == 'table_jenis_pekerjaan')
    {
        var current_seq     = $("#btnTambahData").val();
        if(seq != 1) {
            if(parseInt(current_seq) - seq == 1) {
                $("#"+id_table).DataTable().row(seq - 1).remove().draw(false);
                var prev_seq    = seq - 1;
                $("#jk_title"+prev_seq).focus();
                
                $("#btnTambahData").val(parseInt(current_seq) - 1);
            } else {
                console.log('tidak bisa dihapus');
            }
        }
    }
}

function do_simpan(id_form, jenis)
{
    if(id_form == 'modalProgram')
    {
        // GET DATA
        var program_sasaranID   = $("#program_sasaranID");
        var program_uraian      = $("#program_title");
        var program_ID          = $("#program_ID");
        var program_bulan       = $("#program_bulan");
        var program_detail      = [];

        for(var i = 0; i < $("#table_jenis_pekerjaan").DataTable().rows().count(); i++) {
            var seq     = i + 1;
            program_detail.push({
                "detail_seq"    : $("#jk_seq"+seq).val(),
                "detail_title"  : $("#jk_title"+seq).val(),
                "detail_target" : $("#jk_target"+seq).val(),
            });
        }

        if(program_sasaranID.val() == null) {
            Swal.fire({
                icon    : 'error',
                text    : 'Terjadi Kesalahan',
                title   : 'Sasaran Harus Dipiilih',
            }).then((results)   => {
                if(results.isConfirmed) {
                    program_sasaranID.select2('open');
                }
            })
        } else if(program_uraian.val() == '') {
            Swal.fire({
                icon    : 'error',
                text    : 'Terjadi Kesalahan',
                title   : 'Uraian Harus diisi',
            }).then((results)   => {
                if(results.isConfirmed) {
                    program_uraian.focus();
                }
            })
        } else {
            var data    = {
                "program_ID"        : program_ID.val(),
                "program_sasaranID" : program_sasaranID.val(),
                "program_uraian"    : program_uraian.val(),
                "program_bulan"     : program_bulan.val(),
                "program_detail"    : program_detail,
            };
            var url     = "/marketings/programKerja/program/simpanProgram/"+jenis;
            var type    =  "POST";
            var message = Swal.fire({ title : "Data Sedang Diproses" }); Swal.showLoading();
            
            doTrans(url, type, data, message, true)
                .then((success) => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text
                    }).then((results)   => {
                        if(results.isConfirmed) {
                            close_modal('modalProgram');
                        }
                    })
                })
                .catch((error)  => {
                    Swal.fire({
                        icon    : error.responseJSON.alert.icon,
                        title   : error.responseJSON.alert.message.title,
                        text    : error.responseJSON.alert.message.text,
                    });
                })
        }
    } else if(id_form == 'modalTransJenisPekerjaan') {
        var jenis_pekerjaan_ID              = $("#jpk_ID");
        var jenis_pekerjaan_programID       = $("#jpk_programID");
        var jenis_pekerjaan_programIDDetail = $("#jpk_programDetail");
        var jenis_pekerjaan_start_time      = $("#jpk_start_time");
        var jenis_pekerjaan_end_time        = $("#jpk_end_time");
        var jenis_pekerjaan_title           = $("#jpk_title");
        var jenis_pekerjaan_description     = $("#jpk_description");
        var jenis_pekerjaan_date            = $("#jpk_date");

        if(jenis_pekerjaan_programID.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Program Harus Dipilih',
            }).then((results)   => {
                if(results.isConfirmed) {
                    jenis_pekerjaan_programID.select2('open');
                }
            })
        } else if(jenis_pekerjaan_programIDDetail.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Program Detail Harus Dipilih',
            }).then((results)   => {
                if(results.isConfirmed) {
                    jenis_pekerjaan_programIDDetail.select2('open');
                }
            })
        } else{
            var data_simpan     = {
                "jenis_pekerjaan_ID"                : jenis_pekerjaan_ID.val(),
                "jenis_pekerjaan_date"              : jenis_pekerjaan_date.val(),
                "jenis_pekerjaan_programID"         : jenis_pekerjaan_programID.val(),
                "jenis_pekerjaan_programIDDetail"   : jenis_pekerjaan_programIDDetail.val(),
                "jenis_pekerjaan_start_time"        : jenis_pekerjaan_start_time.val(),
                "jenis_pekerjaan_end_time"          : jenis_pekerjaan_end_time.val(),
                "jenis_pekerjaan_title"             : jenis_pekerjaan_title.val(),
                "jenis_pekerjaan_description"       : jenis_pekerjaan_description.val(),
                "jenis_pekerjaan_type_trans"        : jenis,
            };

            var url     = '/marketings/programKerja/jenisPekerjaan/doSimpan';
            var type    = "POST";
            var data    = data_simpan;
            var message = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
            var async   = true;

            doTrans(url, type, data, message, async)
                .then((success) => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text
                    }).then((results)   => {
                        if(results.isConfirmed) {
                            close_modal('modalTransJenisPekerjaan');
                            showCalendar(moment().format('YYYY-MM-DD'));
                        }
                    })
                })
                .catch((err)    => {
                    Swal.close();
                    console.log(err);
                })
        }
    }
}

function doTrans(url, type, data, message, isAsync) {
    return new Promise(function(resolve, reject){
        $.ajax({
            async   : isAsync,
            url     : url,
            cache   : false,
            type    : type,
            beforeSend  : function() {
                message;
            },
            data    : {
                _token      : CSRF_TOKEN,
                sendData    : data,
            },
            dataType : 'json',
            success : function(xhr) {
                resolve(xhr);
            },
            error   : function(xhr) {
                reject(xhr);
            }
        });
    });
}