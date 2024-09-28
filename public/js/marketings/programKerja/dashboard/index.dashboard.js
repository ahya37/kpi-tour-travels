var site_url        = window.location.pathname;
var base_url        = window.location.origin;
var isOpen          = 0;
var isCalendarLoaded= 0;
var today           = moment().format('YYYY-MM-DD');
var tempDataPIC     = [];
var tempDataDetail  = [];
$(document).ready(function(){
    $("#tableList").DataTable({
        language    : {
            "emptyTable"    : "Tidak ada data yang bisa ditampilkan",
            "zeroRecords"   : "Tidak ada data yang bisa ditampilkan",
        },
    });
    showDataDashboard();

    // SHOW SELECT
    showSelectDynamic('lrk_date_year', '', '');
    showSelectDynamic('lrk_sasaran_id', '', '');
    show_table('lrk_tbl_sasaran', '');
    show_table('lrk_tbl_program', '');
    show_table('lrk_tbl_program_daily', '');
});

function showDataDashboard()
{
    var jpkData     = {
        "start_date": moment().startOf('month').format('YYYY-MM-DD'),
        "end_date"  : moment().endOf('month').format('YYYY-MM-DD'), 
    };

    var getData     = [
        doTrans('/marketings/programKerja/program/listProgramMarketing', 'GET', '%', '', true),
        doTrans('/marketings/programKerja/sasaran/listSasaranMarketing', 'GET', '%', '', true),
        doTrans('/marketings/programKerja/jenisPekerjaan/dataEventsCalendar', 'GET', jpkData, '',  true),
    ];

    Promise.all(getData)
        .then((success) => {
            var tahunan     = success[1].data.length;
            var bulanan     = success[0].data.length;
            var harian      = success[2].data.length;

            $("#sasaran_text").empty();
            $("#sasaran_text").append("<h2 style='margin: 0px;' class='mt-1'>"+ tahunan +"</h2>");
            $("#program_text").empty();
            $("#program_text").append("<h2 style='margin: 0px;' class='mt-1'>"+ bulanan +"</h2>");
            $("#jpk_text").empty();
            $("#jpk_text").append("<h2 style='margin: 0px;' class='mt-1'>"+ harian +"</h2>");
        })
        .catch((err)    => {
            $("#sasaran_text").empty();
            $("#sasaran_text").append("<h2 style='margin: 0px;' class='mt-1'>0</h2>");
            $("#program_text").empty();
            $("#program_text").append("<h2 style='margin: 0px;' class='mt-1'>0</h2>");
            $("#jpk_text").empty();
            $("#jpk_text").append("<h2 style='margin: 0px;' class='mt-1'>0</h2>");
        })
}

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
            show_modal('modalTransJenisPekerjaan', 'edit', arg);
        },
        customButtons: {
            prevCustomButton: {
                // text: "",
                click: function() {
                    var hari_ini_bulan_lalu         = moment(today).subtract(1, 'month').format('YYYY-MM-DD');
                    showCalendar(hari_ini_bulan_lalu);
                    today   = hari_ini_bulan_lalu;
                }
            },
            nextCustomButton : {
                click : function() {
                    var hari_ini_bulan_depan         = moment(today).add(1, 'month').format('YYYY-MM-DD');
                    showCalendar(hari_ini_bulan_depan);
                    today   = hari_ini_bulan_depan;
                }
            },
            refreshCustomButton     : {
                click   : function() {
                    today   = moment().format('YYYY-MM-DD');
                    showCalendar(today);
                }
            }
        },
    });
    calendar.render();
    $("#modalJenisPekerjaanTitle").html(moment(tgl_sekaramg, 'YYYY-MM-DD').format('MMMM, YYYY'));
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
        const getDataSasaran        = doTrans('/marketings/programKerja/program/listSelectSasaranMarketing', 'get', '', '', true);
        const getDataProgram        = value != '' ? doTrans('/marketings/programKerja/program/listSelectedProgramMarketing', 'get', value, '', true) : '';
        const getKategoriProgram    = doTrans('/marketings/programKerja/program/listMasterProgram', 'GET', '', '', true);
        const getPIC                = doTrans('/marketings/programKerja/master/getListPIC', 'GET', '', '', true);

        var request     = [
            getDataSasaran,
            getDataProgram,
            getKategoriProgram,
            getPIC
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
                var data_sasaran            = response[0].data.detail;
                var data_sasaran_header     = response[0].data.header;
                var data_master_program     = response[2].data;
                
                tempDataPIC.push(response[3].data);

                show_select('program_sasaranHeaderID', data_sasaran_header, '', '');
                show_select('program_sasaranID', '', '', '');
                show_select('program_bulan', '%', '', true);
                show_select('program_title', data_master_program, '', true);
                
                $("#program_sasaranHeaderID").on('change', () => {
                    var headerID    = $("#program_sasaranHeaderID").val();
                    var tempSasaran = [];
                    for(var i = 0; i < data_sasaran.length; i++) {
                        if(data_sasaran[i].pkt_id == headerID) {
                            tempSasaran.push(data_sasaran[i]);
                        } else {
                            // DO NOTHING
                        }
                    }
                    show_select('program_sasaranID', tempSasaran, '', '');
                })
                
                // DEFINE HEADER AND TITLE
                if(jenis == 'add') {
                    $("#modalProgram_title").html('Tambah Data Program');
                    $("#modalProgram_header").html("Tambah Data Program");
                    
                    $("#program_bulan").val(moment().format('MM')).trigger('change');

                    show_table('table_jenis_pekerjaan', '');
                    tambah_baris('table_jenis_pekerjaan', '');

                    $("#btnHapus").prop('disabled', true);

                } else if(jenis == 'edit') {
                    var data_program            = response[1].data;
                    $("#modalProgram_title").html('Ubah Data Program');
                    $("#modalProgram_header").html("Ubah Data Program");

                    // FILL FORM
                    $("#program_ID").val(value);
                    $("#program_title").val(data_program.program_masterProgramID).trigger('change');
                    $("#program_sasaranHeaderID").val(data_program.program_sasaranID).trigger('change');
                    $("#program_sasaranID").val(data_program.program_sasaranID+" | "+data_program.program_sasaranSequence).trigger('change');
                    $("#program_bulan").val(moment(data_program.program_bulan, 'YYYY-MM-DD').format('MM')).trigger('change');
                    
                    show_table('table_jenis_pekerjaan', '');
                    for(var i = 0; i < data_program.program_detail.length; i++) {
                        tambah_baris('table_jenis_pekerjaan', data_program.program_detail[i]);
                    }
                    tambah_baris('table_jenis_pekerjaan', '');
                    
                    $("#btnHapus").prop('disabled', false);
                }

                // SHOW MODAL
                $("#"+id_modal).modal({backdrop: 'static', keyboard: false});
                $("#"+id_modal).modal('show');

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
        
        if(jenis == 'add') {
            var waktu   = value.startStr;
        } else {
            var waktu   = value.event.startStr;
        }

         // MENGUBAH MENJADI KAPITAL PADA ONKEYUP URAIAN
         $("#jpk_title").on('keyup', ()=> {
            const upper_jpk_title   = $("#jpk_title").val().toUpperCase();
            $("#jpk_title").val(upper_jpk_title);
        });

        // BLUR BANYAKNYA AKTIVITAS
        $("#jpk_aktivitas").on('blur', () => {
            const val_jpk_aktivitas     = $("#jpk_aktivitas").val();
            
            if(val_jpk_aktivitas == '') {
                $("#jpk_aktivitas").val(1);
            } else {
                $("#jpk_aktivitas").val(val_jpk_aktivitas);
            }
        });

        $("#jpk_aktivitas").on('click', () => {
            $("#jpk_aktivitas").select();
        });

        $("#jpk_aktivitas").on('keyup', () => {
            const val_jpk_aktivitas   = $("#jpk_aktivitas").val().replace(/[^0-9\.]/g,'');
            $("#jpk_aktivitas").val(val_jpk_aktivitas);
        });

        var getData     = [
            doTrans('/marketings/programKerja/jenisPekerjaan/dataProgram', 'GET', waktu, '', true),
            jenis == 'edit' ? doTrans('/marketings/programKerja/jenisPekerjaan/dataDetailEventsCalendar/'+value.event.id, "GET", '', '', true) : '',
        ];

        Swal.fire({
            title   : 'Data Sedang Dimuat',
        });
        Swal.showLoading();

        Promise.all(getData)
            .then((success) => {
                // console.log({success});
                $("#"+id_modal).modal({ backdrop : 'static', keyboard : false });
                $("#"+id_modal).modal('show');

                show_select('jpk_programID', success[0], '', true);
                if(jenis == 'add') {
                    show_select('jpk_programDetail', '', '', true);
                    $("#jpk_date").val(value.startStr);
                    $("#jpk_btnHapus").prop('disabled', true);
                } else if(jenis == 'edit') {
                    var getData     = success[1].data[0];
                    var masterID    = getData.master_program_id;
                    var pkb_id      = getData.pkb_id;
                    var programID   = getData.pkh_pkb_id;
                    var start_time  = getData.pkh_start_time.split(' ')[1];
                    var end_time    = getData.pkh_end_time.split(' ')[1];
                    var title       = getData.pkh_title;
                    var pkh_date    = getData.pkh_date;
                    var description = "";
                    var pkh_total_act   = getData.pkh_total_activity;

                    $("#jpk_ID").val(value.event.id);
                    $("#jpk_date").val(pkh_date);
                    $("#jpk_programID").val(pkb_id);
                    $("#jpk_start_time").val(start_time);
                    $("#jpk_end_time").val(end_time);
                    $("#jpk_title").val(title);
                    $("#jpk_description").val(description);
                    show_select('jpk_programDetail', pkb_id, programID, true);
                    $("#jpk_btnHapus").prop('disabled', false);
                    $("#jpk_aktivitas").val(pkh_total_act);
                }
                Swal.close();
            })
            .catch((err)    => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak ada data yang bisa dilakukan transaksi untuk bulan ini',
                })
                console.log(err);
            })
    } else if(id_modal == 'modalDetailProgram') {
        close_modal('modalTableProgram','');

        var url     = "/marketings/programKerja/program/listDetailProgram/"+value;
        var type    = "GET";
        var data    = "";
        var message = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();
        var isAsync = true;

        var listTrans   = [
            doTrans(url, type, data, message, isAsync),
        ];

        Promise.all(listTrans)
            .then((success) => {
                var header  = success[0].data.header;
                var detail  = success[0].data.detail;
                $("#"+id_modal).modal({ backdrop : 'static', keyboard : false });
                $("#"+id_modal).modal('show');

                // HEADER
                $("#modalDetailProgram_programKategori").html(header.pkb_title.toUpperCase());
                $("#modalDetailProgram_programTitle").html(header.pkb_sub_title);
                $("#modalDetailProgram_programBulan").html(moment(header.pkb_month_periode, 'MM').format('MMMM').toUpperCase());
                $("#modalDetailProgram_programDivisi").html(header.group_division_name);

                $("#modalDetailProgram_title").html('Detail Program : '+header.pkb_title.toUpperCase()+' - '+header.pkb_sub_title);
                
                // DETAIL
                show_table('modalDetailProgram_table', '%');
                var total_target    = 0;
                var total_result    = 0;
                
                for(var i = 0; i < detail.length; i++) {
                    var detail_seq      = i + 1;
                    var detail_title    = detail[i].pkbd_title;
                    var detail_target   = detail[i].pkbd_target;
                    var detail_result   = detail[i].pkbd_result;
                    var detail_percent  = (parseFloat(detail_result) / parseFloat(detail_target)) * 100;
                    total_target    += detail_target;
                    total_result    += detail_result;
                    
                    $("#modalDetailProgram_table").DataTable().row.add([
                        detail_seq,
                        detail_title,
                        detail_target,
                        detail_result,
                        $.isNumeric(detail_percent) === false ? parseFloat(0).toFixed(2)+" %" : parseFloat(detail_percent).toFixed(2)+' %',
                    ]).draw('false');
                }
                let percentage  = $.isNumeric((total_result / total_target) * 100) === false ? 0 : (total_result / total_target) * 100;
                $("#modalDetailProgram_table_totalTarget").html(parseFloat(total_target).toFixed(0));
                $("#modalDetailProgram_table_totalRealisasi").html(parseFloat(total_result).toFixed(0));
                $("#modalDetailProgram_table_totalPercent").html(parseFloat(percentage).toFixed(2)+" %");

                Swal.close();
            })
            .catch((err)    => {
                Swal.close();
                console.log(err);
            })
    } else if(id_modal == 'modal_tbl_program_daily') {

        // GET DATA
        const program_bulanan_data  = {
            "program_bulanan_id"    : value,
        };
        const program_bulanan_url   = base_url + "/marketings/programKerja/program/listProgramMarketingByDay";
        const program_bulanan_type  = "GET";
        const program_bulanan_msg   = Swal.fire({ title : 'Data Sedang Dimuat' }); Swal.showLoading();
        
        doTrans(program_bulanan_url, program_bulanan_type, program_bulanan_data, program_bulanan_msg, true)
            .then((success)     => {
                console.log(success);
                Swal.close();

                const program_bulanan_getData   = success.data;
                show_table('tbl_program_daily_detail', program_bulanan_getData);
                
                // SHOW MODAL
                $("#"+id_modal).modal({
                    backdrop    : 'static', 
                    keyboard    : false,
                });
            })
            .catch((error)      => {
                console.log(error);
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : error.responseJSON.message,
                });
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
        $("#"+id_modal).on('hidden.bs.modal', () => {

        })
        isOpen == 0;
    } else if(id_modal == 'modalTransJenisPekerjaan') {
        $("#"+id_modal).modal('hide');

        $("#"+id_modal).on('hidden.bs.modal', ()=>{
            $(".waktu").val('00:00:00');
            $("#jpk_title").val(null);
            $("#jpk_description").val(null);
            $("#jpk_date").val(null);
            today  = moment().format('YYYY-MM-DD');
            $("#jpk_btnHapus").prop('disabled', true);
            $("#jpk_aktivitas").val(1);
        });
    } else if(id_modal == 'modalDetailProgram') {
        $("#"+id_modal).modal('hide');
        show_modal('modalTableProgram', '', '')
    } else if(id_modal == 'modal_tbl_program_daily') {
        $("#"+id_modal).modal('hide');

        $("#"+id_modal).on('hidden.bs.modal', () => {
            $("#tbl_program_daily_detail_total_hasil").html(0);
        });
    }
}

function show_select(id_select, valueCari, valueSelect, isAsync)
{
    $("#"+id_select).select2({
        theme   : 'bootstrap4',
    });

    if(id_select == 'filterSasaran') {
        var html    = "<option selected disabled>Pilih Sasaran Kerja</option>";
        if(valueCari != '') {
            $.each(valueCari, (i, item) => {
                html    += "<option value='" + item.uid + "'>" + item.pkt_title + "</option>";
            });
            
            $("#"+id_select).html(html);
        } else {
            $("#"+id_select).html(html);
        }
    } else if(id_select == 'program_sasaranHeaderID') {
        var html    = "<option selected disabled>Pilih Program Kerja Divisi</option>";
        if(valueCari != '' ) {
            $.each(valueCari, (i, item) => {
                html    += "<option value='" + item.pkt_uuid + "'>" + item.pkt_title + "</option>";
            });
            
            $("#"+id_select).html(html);
        } else {
            $("#"+id_select).html(html);
        }
    } else if(id_select == 'program_sasaranID') {
        var html     = "<option selected disabled>Pilih Sasaran Program Detail</option>";

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
                $("#program_title").select2('open');
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
            // console.log(valueCari);
            $.each(valueCari.data, (i, item) => {
                html    += "<option value='" + item.pkb_id + "'>" + item.name + " - " + item.pktd_title + "</option>";
            });
            $("#"+id_select).html(html);
        } else {
            $("#"+id_select).html(html);   
        }
    } else if(id_select == 'jpk_programDetail') {
        var html    = "<option selected disabled>Pilih Program Detail</option>";
        // console.log({id_select, valueCari, valueSelect, isAsync});
        if(valueCari != '') {
            var url     = "/marketings/programKerja/jenisPekerjaan/dataProgramDetail/"+valueCari;
            var type    = "GET";
            var data    = "";
            if(valueSelect == '') { var message = Swal.fire({ title : 'Data Sedang Dimuat' }); Swal.showLoading(); } else { var message = "" };

            doTrans(url, type, data, message, isAsync)
                .then((success) => {
                    $.each(success.data,(i, item)   => {
                        html    += "<option value='" + item.pkb_id +   " | " + item.pkbd_id + "'>" + item.pkbd_title + " (Target : " + item.pkbd_num_target + ")</option>";
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
    } else if(id_select == 'program_title') {
        var html    = "<option selected disabled>Pilih Kategori Program</option>";

        if(valueCari != '') {
            $.each(valueCari, (i, item) => {
                if(item.is_active == 't') {
                    html    += "<option value='"+ item.id +"'>" + item.name + "</option>";
                }
            });
            $("#"+id_select).html(html);
        } else {
            $("#"+id_select).html(html);
        }
    } else if(id_select == 'filterSasaranDetail') {
        var html    = "<option selected disabled>Pilih Sasaran Kerja Detail</option>";

        if(valueCari != '') {
            $.each(valueCari, (i, item) => {
                html    += "<option value='" + item.pktd_seq + "'>" + item.pktd_title + "</option>";
            });
            $("#"+id_select).html(html);
        } else {
            $("#"+id_select).html(html);
        }
    }
}

function showSelectDynamic(idSelect, data, seq)
{   
    $("#"+idSelect+""+seq).select2({
        theme   : 'bootstrap4'
    });
    if(idSelect == 'jk_pic') {
        var html = [
            "<option selected disabled>PIC</option>",
        ];

        if(data.length > 0) {
            html    += "<option value='0'>Semua</option>";

            $.each(data[0], (i, item) => {
                // console.log(item);
                html    += "<option value='" + item.user_id + "'>" + item.name + "</option>";
            });

            $("#"+idSelect+""+seq).html(html);
        } else {
            $("#"+idSelect+""+seq).html(html);
        }
    } else if(idSelect == 'lrk_date_year') {
        var html    = "<option selected disabled>Pilih Tahun</option>";
        data  = [
            {"value" : "2024", "text" : "2024"}
        ];

        $.each(data, (i, item)  => {
            html    += "<option value='" + item.value + "'>" + item.text + "</option>";
        });
        
        $("#"+idSelect).html(html);
    } else if(idSelect == 'lrk_sasaran_id') {
        var html    = "<option selected disabled>Pilih Sasaran</option>";

        if(data != '') {
            $.each(data, (i, item) => {
                html    += "<option value='" + item.pkt_id + " | " + item.pkt_det_seq + "'>" + item.pkt_det_title + "</option>";
            });
            $("#"+idSelect).html(html);

            setTimeout((
                Swal.close()
            ), 1000);
            $("#"+idSelect).select2('open');
        } else {
            $("#"+idSelect).html(html);
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
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "zeroRecords"   : "Tidak ada data yang bisa ditampilkan..",
            },
            processing  : true,
            serverSide  : false,
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "40%"},
                { "targets" : [2], "className" : "text-left align-middle", "width" : "15%" },
                { "targets" : [3], "className" : "text-left align-middle", "width" : "15%" },
                { "targets" : [4], "className" : "text-left align-middle", "width" : "10%" },
                { "targets" : [5], "className" : "text-center align-middle"},
            ],
        });
        
        // GET DATATABLE
        doTrans('/marketings/programKerja/program/listProgramMarketing', 'GET', '%', '', true)
            .then((success) => {
                if(success.data.length > 0) {
                    let total_result   = 0;
                    let total_target    = 0;
                    for(let i = 0; i < success.data.length; i++) {
                        const seq           = i + 1;
                        const id            = success.data[i].pkb_id;
                        const title         = success.data[i].pkb_title.length > 50 ? success.data[i].pkb_title.slice(0, 50) + "...." : success.data[i].pkb_title;
                        const date          = success.data[i].program_date;
                        const groupDivision = success.data[i].group_division_name;
                        const target        = success.data[i].total_target;
                        const result        = success.data[i].total_result;
                        const buttonEdit    = "<button type='button' class='btn btn-sm btn-primary' title='Ubah Data' value='" + id + "' onclick=' show_modal(`modalProgram`, `edit`, this.value)'><i class='fa fa-edit'></i></button>";
                        const buttonCheck   = "<button type='button' class='btn btn-sm btn-success' title='Lihat Data' value='" + id + "' onclick='show_modal(`modalDetailProgram`, `view`, this.value)'><i class='fa fa-eye'></i></button>";
                        $("#"+id_table).DataTable().row.add([
                            seq,
                            "<label class='no-margins' style='font-weight: normal;' title='"+success.data[i].pkb_title+"'>" + title + "</label>",
                            moment(date, 'YYYY-MM-DD').format('MMMM'),
                            groupDivision,
                            result + " / " + target,
                            buttonEdit+"&nbsp;"+buttonCheck,
                        ]).draw(false);

                        total_result    += parseInt(result);
                        total_target    += parseInt(target);
                    }

                    $("#tableProgram_total").html(total_result + " / " + total_target);
                }
            })
            .catch((err)    => {
                console.log(err);
                $(".dataTables_empty").html('Tidak ada data yang bisa dimuat, silahkan tambahkan beberapa..');
            })

        
    } else if(id_table == 'table_jenis_pekerjaan') {
        $("#"+id_table).DataTable().clear().destroy();
        $("#"+id_table).DataTable({
            language    : {
                "emptyTable"    : "Tidak ada data data..",
                "zeroRecords"   : "Tidak ada data data..",
            },
            columnDefs  : [
                { "targets" : [0], "width" : "5%", "className" : "text-center align-middle" },
                { "targets" : [1], "width" : "10%", "className" : "text-center" },
                { "targets" : [3], "width" : "15%", "className" : "text-right" },
                { "targets" : [4], "width" : "1%"},
            ],
            ordering    : false,
            searching   : false,
            bInfo       : false,
            autoWidth   : false,
            paging      : false,
        });
    } else if(id_table == 'tableSasaran') {
        $("#"+id_table).DataTable().clear().destroy();
        $("#"+id_table).DataTable({
            searching   : false,
            ordering    : false,
            bInfo       : false,
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0, 2], "className": "text-center", "width" : "8%" }
            ],
            pageLength  : 5,
        })
    } else if(id_table == 'modalDetailProgram_table') {
        $("#"+id_table).DataTable().clear().destroy();
        $("#"+id_table).DataTable({
            pageLength  : -1,
            paging      : false,
            searching   : false,
            bInfo       : false,
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width": "8%" },
                { "targets" : [2, 3], "className" : "text-right align-middle", "width" : "10%" },
                { "targets" : [4], "className" : "text-left align-middle", "width" : "15%" },
            ],
        });
    } else if(id_table == 'lrk_tbl_sasaran') {
        // RESET FOOTER
        $("#lrk_tbl_total_target").html(0);
        $("#lrk_tbl_total_realisasi").html(0);
        $("#lrk_tbl_total_presentase").html("");

        $("#"+id_table).DataTable().clear().destroy();
        $("#"+id_table).DataTable({
            language    : {
                "emptyTable"    : "Silahkan Pilih Tahun Dan Sasaran Untuk Menampilkan Data...",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan",
            },
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [2, 3, 4], "className" : "text-center align-middle", "width" : "15%" },
            ],
            autoWidth   : false,
        });

        if(value != '') {
            let seq     = 1;
            let totalTarget     = 0;
            let totalRealisasi  = 0;
            let totalPersentase = 0;
            for(const item of value)
            {
                $("#"+id_table).DataTable().row.add([
                    seq++,
                    "<label class='no-margins font-weight-normal' style='cursor: pointer; color: #18a689' title='Lihat Detail' onclick='cariData(`lrk_tbl_program`, "+item.program_bulan+")'>" + moment(item.program_bulan, 'M').format('MMMM').toUpperCase() + "</label>",
                    item.total_target,
                    item.realisasi,
                    parseFloat((parseInt(item.realisasi) / parseInt(item.total_target)) * 100).toFixed(2) + "%",
                ]).draw(false);
                totalTarget     += parseInt(item.total_target);
                totalRealisasi  += parseInt(item.realisasi);
            }

            totalPersentase     = parseFloat((parseInt(totalRealisasi) / parseInt(totalTarget)) * 100).toFixed(2)+"%";
            $("#lrk_tbl_total_target").html(totalTarget);
            $("#lrk_tbl_total_realisasi").html(totalRealisasi);
            $("#lrk_tbl_total_presentase").html(totalPersentase);

        }
    } else if(id_table == 'lrk_tbl_program') {
        // RESET FOOTER
        $("#lrk_tbl_program_total_target").html(0);
        $("#lrk_tbl_program_total_realisasi").html(0);
        $("#lrk_tbl_program_total_persentase").html("");

        $("#"+id_table).DataTable().clear().destroy();
        $("#"+id_table).DataTable({
            language    : {
                "emptyTable"    : "Tidak Ada Data Yang Bisa Ditampilkan",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan",
            },
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [2, 3, 4], "className" : "text-center align-middle", "width" : "15%" },
            ],
            autoWidth  : false,
        });
        if(value != '') {
            let seq  = 1;
            let persentase      = 0;
            let totalTarget     = 0;
            let totalRealisasi  = 0;
            let totalPersentase = 0;
            for(const item of value)
            {
                persentase  = (parseInt(item.pkb_total_result) / parseInt(item.pkb_total_target)) * 100;
                persentase  = isFinite(persentase) === true ? persentase : (persentase === Number.POSITIVE_INFINITY || persentase === Number.NEGATIVE_INFINITY ? parseInt(item.pkb_total_result)*100 : 0)
                $("#"+id_table).DataTable().row.add([
                    seq++,
                    "<label class='no-margins font-weight-normal' style='cursor: pointer; color: #18a689' title='Lihat Detail' onclick='cariData(`lrk_tbl_program_daily`, `" + item.pkb_id + "`)'>" + item.pkb_title + "</label>",
                    item.pkb_total_target,
                    item.pkb_total_result,
                    parseFloat(persentase).toFixed(2)+"%",
                ]).draw(false);
                totalTarget     += parseInt(item.pkb_total_target);
                totalRealisasi  += parseInt(item.pkb_total_result);
            }

            totalPersentase     = parseFloat((parseInt(totalRealisasi) / parseInt(totalTarget)) * 100).toFixed(2)+"%";
            $("#lrk_tbl_program_total_target").html(totalTarget);
            $("#lrk_tbl_program_total_realisasi").html(totalRealisasi);
            $("#lrk_tbl_program_total_persentase").html(totalPersentase);
        }
    } else if(id_table == 'lrk_tbl_program_daily') {
        // RESET FOOTER
        $("#lrk_tbl_program_daily_total_target").html(0);
        $("#lrk_tbl_program_daily_total_realisasi").html(0);
        $("#lrk_tbl_program_daily_total_persentase").html("");

        $("#"+id_table).DataTable().clear().destroy();        
        $("#"+id_table).DataTable({
            language    : {
                "emptyTable"    : "Tidak Ada Data Yang Bisa Ditampilkan",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan",
            },
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [2, 3], "className" : "text-center align-middle", "width" : "15%" },
                { "targets" : [4], "className" : "text-left align-middle", "width" : "15%" },
            ],
            autoWidth   : false,
        });

        if(value != '') {
            $("#lrk_tbl_program_daily tbody .dataTables_empty").html("Berhasil Memuat Data");

            let seq     = 1;
            let totalTarget     = 0;
            let totalRealisasi  = 0;
            for(const item of value)
            {
                const id        = item.pkb_det_id;
                const title     = item.pkb_det_title == null ? "..." : item.pkb_det_title;
                let persentase  = (parseInt(item.pkb_det_result) / parseInt(item.pkb_det_target)) * 100
                $("#"+id_table).DataTable().row.add([
                    seq++,
                    "<label class='no-margins font-weight-normal' style='cursor:pointer; color: #18a689' title='Lihat Detail' onclick='show_modal(`modal_tbl_program_daily`, ``, `" + id + "`)'>" + title.toUpperCase() + "</label>",
                    item.pkb_det_target,
                    item.pkb_det_result,
                    isFinite(persentase) === true ? parseFloat(persentase).toFixed(2)+"%" : persentase == Number.POSITIVE_INFINITY || persentase == Number.NEGATIVE_INFINITY ? parseFloat(parseInt(item.pkb_det_result) * 100).toFixed(2) + "%" : parseFloat(0).toFixed(2)+"%",
                ]).draw(false)
                
                totalTarget     += item.pkb_det_target;
                totalRealisasi  += item.pkb_det_result;
            }
            
            let hitungPersentaseTotal   = (parseInt(totalRealisasi) / parseInt(totalTarget)) * 100;
            let cekPersentase           = isFinite(hitungPersentaseTotal) === true ? hitungPersentaseTotal : (hitungPersentaseTotal == Number.POSITIVE_INFINITY || hitungPersentaseTotal == Number.NEGATIVE_INFINITY ? parseInt(totalRealisasi) * 100 : 0);
            let persentaseTotal         = parseFloat(cekPersentase).toFixed(2)+"%";
            $("#lrk_tbl_program_daily_total_target").html(totalTarget);
            $("#lrk_tbl_program_daily_total_realisasi").html(totalRealisasi);
            $("#lrk_tbl_program_daily_total_persentase").html(persentaseTotal); 
        } else {
            $("#lrk_tbl_program_daily tbody .dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan");
        }
    } else if(id_table == 'tbl_program_daily_detail') {
        $("#"+id_table).DataTable().clear().destroy();
        $("#"+id_table).DataTable({
            language    : {
                zeroRecords : "Data Yang Dicari Tidak Ditemukan"
            },
            autoWidth   : false,
            pageLength  : -1,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [2], "className" : "text-center align-middle", "width" : "15%"},
                { "targets" : [3], "className" : "text-right align-middle", "width" : "10%" },
                { "targets" : [4], "className" : "align-middle", "width" : "25%" },
            ],
        });

        if(value != '') {
            $("#"+id_table+" tbody .dataTables_empty").html("Data Ditemukan");
            let seq   = 1;
            let total_res   = 0;
            for(const item of value)
            {
                const program_seq   = seq++;
                const program_title = item['pkh_title'];
                const program_date  = item['pkh_date'];
                const program_pic   = item['pkh_pic_name'];
                const program_res   = item['pkh_total_activity'];

                $("#"+id_table).DataTable().row.add([
                    program_seq,
                    program_title,
                    moment(program_date, 'YYYY-MM-DD').format('DD/MM/YYYY'),
                    program_res,
                    program_pic
                ]).draw(false);

                total_res += program_res;
            }
            $("#tbl_program_daily_detail_total_hasil").html(total_res);
        } else {
            $("#"+id_table+" tbody .dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan");
        }
    }
}

function tambah_baris(id_table, value)
{
    if(id_table == 'table_jenis_pekerjaan') {
        var seq     = $("#btnTambahData").val();

        var input_delete    = "<button class='btn btn-danger' value='"+ seq +"' id='jk_btn_delete"+seq+"' title='Hapus Data' onclick='hapus_baris(`table_jenis_pekerjaan`, "+seq+")'><i class='fa fa-trash'></i></button>";
        var input_id        = "<input type='hidden' class='form-control text-center' id='jk_id"+seq+"'>";
        var input_seq       = "<input type='text' class='form-control text-center' id='jk_seq"+seq+"' placeholder='seq' readonly style='height: 37.5px;'>";
        var input_title     = "<input type='text' class='form-control' id='jk_title"+seq+"' placeholder='Title' style='height: 37.5px;'>";
        var input_target    = "<input type='number' class='form-control text-right' onclick='this.select()' id='jk_target"+seq+"' placeholder='Target' max='9999' min='0' step='1' style='height: 37.5px;'>";
        var input_pic       = "<select id='jk_pic"+seq+"' class='form-control' style='width: 180px;'></select>";

        $("#"+id_table).DataTable().row.add([
            input_delete,
            input_seq+""+input_id,
            input_title,
            input_target,
            input_pic,
        ]).draw('false');

        showSelectDynamic('jk_pic', tempDataPIC, seq);

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
            $("#jk_id"+seq).val(value.pkbd_id);
            $("#jk_title"+seq).val(value.pkbd_title);
            $("#jk_target"+seq).val(value.pkbd_num_target);
            $("#jk_pic"+seq).val(value.pkbd_pic).trigger('change');
        } else {
            $("#jk_target"+seq).val(0);
        }

        if($("#jk_id"+seq).val()  == '') {
            $("#jk_btn_delete"+seq).prop('disabled', false);
        } else {
            $("#jk_btn_delete"+seq).prop('disabled', true);
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
        var program_sasaranID           = $("#program_sasaranHeaderID");
        var kategori                    = $("#program_title");
        var program_ID                  = $("#program_ID");
        var program_detailSasaranID     = $("#program_sasaranID");
        var program_bulan               = $("#program_bulan");
        var program_detail              = [];

        for(var i = 0; i < $("#table_jenis_pekerjaan").DataTable().rows().count(); i++) {
            var seq     = i + 1;
            program_detail.push({
                "detail_id"     : $("#jk_id"+seq).val(),
                "detail_seq"    : $("#jk_seq"+seq).val(),
                "detail_title"  : $("#jk_title"+seq).val(),
                "detail_target" : $("#jk_target"+seq).val(),
                "detail_pic"    : $("#jk_pic"+seq).val(),
            });
        }

        if(kategori.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Program Harus Dipilih',
            }).then((results)   => {
                if(results.isConfirmed) {
                    kategori.select2('open')
                }
            })
        } else if(program_sasaranID.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Sasaran Harus Dipiilih',
            }).then((results)   => {
                if(results.isConfirmed) {
                    program_sasaranID.select2('open');
                }
            })
        } else if(program_detailSasaranID.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Sasaran Detail Harus Dipilih',
            }).then((results)   => {
                if(results.isConfirmed) {
                    program_detailSasaranID.select2('open');
                }
            })
        } else {
            var data    = {
                "program_ID"                : program_ID.val(),
                "program_sasaranID"         : program_sasaranID.val(),
                "program_detailSasaranID"   : program_detailSasaranID.val().split(' | ')[1],
                "program_masterID"          : kategori.val(),
                "program_bulan"             : program_bulan.val(),
                "program_detail"            : program_detail,
                "program_uraian"            : $("#program_title option:selected").text(),
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
                            showDataDashboard();
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
        var jenis_pekerjaan_total_act       = $("#jpk_aktivitas");

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
        } else if(jenis_pekerjaan_title.val() == '') {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Judul Jenis Pekerjaan Harus Diisi',
            }).then((results)   => {
                if(results.isConfirmed) {
                    jenis_pekerjaan_title.focus();
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
                "jenis_pekerjaan_total_activity"    : jenis_pekerjaan_total_act.val(),
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
                            showCalendar(today);
                            showDataDashboard();
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

function hapus_data(id_form)
{
    if(id_form == 'modalProgram') {
        var idProgram   = $("#program_ID").val();
        Swal.fire({
            icon    : 'question',
            title   : 'Hapus Data',
            text    : 'Data yang dihapus tidak akan muncul di tabel',
            showConfirmButton   : true,
            showCancelButton    : true,
            confirmButtonText   : 'Ya, Hapus',
            cancelButtonText    : 'Batal',
            confirmButtonColor  : '#dc3545',
        }).then((results)   => {
            if(results.isConfirmed) {
                var url     = "/marketings/programKerja/program/deleteProgram/"+idProgram;
                var type    = "POST";
                var data    = "";
                var message = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
                var isAsync = true;

                doTrans(url, type, data, message, isAsync)
                    .then((success) => {
                        Swal.fire({
                            icon    : success.alert.icon,
                            title   : success.alert.message.title,
                            text    : success.alert.message.text,
                        }).then((results)   => {
                            if(results.isConfirmed) {
                                close_modal('modalProgram');
                                showDataDashboard();
                            }
                        })
                    })
                    .catch((err)    => {
                        Swal.fire({
                            icon    : err.responseJSON.alert.icon,
                            title   : err.responseJSON.alert.message.title,
                            text    : err.responseJSON.alert.message.text,
                        });
                    })
            }
        })
    } else if(id_form == 'modalTransJenisPekerjaan') {
        var jpk_ID  = $("#jpk_ID").val();
        Swal.fire({
            icon    : 'question',
            title   : 'Hapus Data',
            text    : 'Data yang dihapus tidak akan muncul lagi di kalender',
            showConfirmButton   : true,
            showCancelButton    : true,
            confirmButtonText   : 'Ya, Hapus',
            cancelButtonText    : 'Batal',
            confirmButtonColor  : '#dc3545',
        }).then((results)   => {
            if(results.isConfirmed) {
                var url     = "/marketings/programKerja/jenisPekerjaan/deleteJeniPekerjaan/"+jpk_ID;
                var type    = "POST";
                var data    = "";
                var message = Swal.fire({ title : "Data Sedang Diproses" }); Swal.showLoading();
                
                doTrans(url, type, data, message, true)
                    .then((success) => {
                        Swal.fire({
                            icon    : success.alert.icon,
                            title   : success.alert.message.title,
                            text    : success.alert.message.text
                        }).then((results)   => {
                            if(results.isConfirmed) {
                                close_modal('modalTransJenisPekerjaan');
                                showCalendar(today);
                                showDataDashboard();
                            }
                        })
                    })
                    .catch((err)    => {
                        Swal.fire({
                            icon    : err.responseJSON.alert.icon,
                            title   : err.responseJSON.alert.message.title,
                            text    : err.responseJSON.alert.message.text
                        })
                    })

            }
        })
    }
}

function cariData(formSearch, value)
{
    if(formSearch == 'lrk_sasaran_id') {
        // SEARCH DATA 
        const prog_mkt_url  = base_url + "/marketings/programKerja/program/listProgramMarketingByYear";
        const prog_mkt_type = "GET";
        const prog_mkt_data = {
            "tahun"         : value,
            "program_id"    : "",
        };
        const prog_mkt_msg  = Swal.fire({ title : "Data Sedang Dicari..", allowOutsideClick: false}); Swal.showLoading();

        doTrans(prog_mkt_url, prog_mkt_type, prog_mkt_data, prog_mkt_msg)
            .then((success)     => {
                const prog_mkt_getData  = success.data.header;
                showSelectDynamic('lrk_sasaran_id', prog_mkt_getData, '');
            })
            .catch((err)        => {
                console.log(err);
            })

    } else if(formSearch == 'lrk_tbl_sasaran') {
        show_table('lrk_tbl_sasaran', '');
        show_table('lrk_tbl_program', '');
        show_table('lrk_tbl_program_daily', '');
        const prog_mkt_url  = base_url + "/marketings/programKerja/program/listProgramMarketingByYear";
        const prog_mkt_type = "GET";
        const prog_mkt_data = {
            "tahun"     : $("#lrk_date_year").val(),
            "program_id": $("#lrk_sasaran_id").val(),
        };
        $("#lrk_tbl_sasaran tbody .dataTables_empty").html("<i class='fa fa-spinner fa-spin'></i> Data Sedang Dicari..");

        doTrans(prog_mkt_url, prog_mkt_type, prog_mkt_data, "", true)
            .then((success)     => {
                // UBAH HEADER
                const prog_mkt_selected     = $("#lrk_sasaran_id option:selected").text();
                $("#lrk_tbl_sasaran_title").html("List Sasaran "+prog_mkt_selected);
                // REMOVE LOADER
                $("#lrk_tbl_sasaran tbody .dataTables_empty").html("Data Berhasil Dimuat..");
                
                // SHOW DATA
                show_table('lrk_tbl_sasaran', success.data.detail);
            })
            .catch((error)      => {
                $("#lrk_tbl_sasaran tbody .dataTables_empty").html("Tidak Ada Data Yang Bisa Dimuat");
                console.log(error);
            })
    } else if(formSearch == 'lrk_tbl_program') {
        $("#lrk_program_selected_month").val(moment(value, 'M').format('MM'));
        show_table('lrk_tbl_program', '');
        show_table('lrk_tbl_program_daily', '');
        const prog_mkt_url      = base_url + "/marketings/programKerja/program/listProgramMarketingByMonth";
        const prog_mkt_type     = "GET";
        const prog_mkt_data     = {
            "program_pkt_id"    : $("#lrk_sasaran_id").val(),
            "program_month"     : value,
            "program_pkb_id"    : "",
            "program_pkb_seq"   : "",
        };
        const prog_mkt_msg      = $("#lrk_tbl_program tbody .dataTables_empty").html("<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..");
        
        doTrans(prog_mkt_url, prog_mkt_type, prog_mkt_data, prog_mkt_msg, true)
            .then((success)     => {
                // UPDATE TITLE
                $("#lrk_program_title").html("Laporan Program Bulan "+moment(value, 'M').format('MMMM'));
                $("#lrk_program_weekly_title").html("Laporan Program Harian Bulan "+moment(value, 'M').format('MMMM'));
                // REMOVE LOADER
                $("#lrk_tbl_program tbody .dataTables_empty").html("Data Berhasil Dimuat");
                // DATA
                const prog_mkt_getData  = success.data.header;
                show_table('lrk_tbl_program', prog_mkt_getData);
            })
            .catch((err)        => {
                $("#lrk_tbl_program tbody .dataTables_empty").html("Data Berhasil Dimuat");
                console.log(err)
            })
    } else if(formSearch == 'lrk_tbl_program_daily') {   
        show_table('lrk_tbl_program_daily', '');     
        const prog_mkt_url      = base_url + "/marketings/programKerja/program/listProgramMarketingByWeek";
        const prog_mkt_type     = "GET";
        const prog_mkt_data     = {
            "program_pkb_id"        : value,
        };
        const prog_mkt_msg      = $("#lrk_tbl_program_daily tbody .dataTables_empty").html("<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..");

        doTrans(prog_mkt_url, prog_mkt_type, prog_mkt_data, prog_mkt_msg, true)
            .then((success)     => {
                const prog_mkt_getData  = success.data;
                // REMOVE LOADER
                show_table('lrk_tbl_program_daily', prog_mkt_getData);
            })
            .catch((error)      => {
                console.log(error);
                // REMOVE LOADER
                $("#lrk_tbl_program_daily tbody .dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan");
                
            })
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