var today   = moment().format('YYYY-MM-DD');
moment.locale('id');
$(document).ready(function(){
    console.log('test');
    showCalendar(today);
})

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
    $(".fc-nextCustomButton-button").html("<i class='fa fa-chevron-right'></i>").prop('title', 'Bulan Selanjutnya');
    $(".fc-prevCustomButton-button").html("<i class='fa fa-chevron-left'></i>").prop('title', 'Bulan Sebelumnya');
    $(".fc-refreshCustomButton-button").html("<i class='fa fa-undo'></i>").prop('title','Hari ini');

    $("#jpk_year_periode").empty();
    $("#jpk_year_periode").html(moment(today, 'YYYY-MM-DD').format('YYYY'));
    $("#jpk_month_periode").empty();
    $("#jpk_month_periode").html(moment(today, 'YYYY-MM-DD').format('MMMM'));
}

function show_modal(id_modal, jenis, value)
{
    console.log({id_modal, jenis, value});
    if(id_modal == 'modalTransJenisPekerjaan') {
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

                    $("#jpk_ID").val(value.event.id);
                    $("#jpk_date").val(pkh_date);
                    $("#jpk_programID").val(pkb_id);
                    $("#jpk_start_time").val(start_time);
                    $("#jpk_end_time").val(end_time);
                    $("#jpk_title").val(title);
                    $("#jpk_description").val(description);
                    show_select('jpk_programDetail', pkb_id, programID, true);
                    $("#jpk_btnHapus").prop('disabled', false);
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
    }
}

function close_modal(id_modal)
{
    if(id_modal == 'modalTransJenisPekerjaan') {
        $("#"+id_modal).modal('hide');

        $("#"+id_modal).on('hidden.bs.modal', ()=>{
            $(".waktu").val('00:00:00');
            $("#jpk_title").val(null);
            $("#jpk_description").val(null);
            $("#jpk_date").val(null);
            today  = moment().format('YYYY-MM-DD');
            $("#jpk_btnHapus").prop('disabled', true);
        });
    }
}

function show_select(id_select, valueCari, valueSelect, isAsync)
{
    $("#"+id_select).select2({
        theme   : 'bootstrap4',
    });
    if(id_select == 'jpk_programID') {
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
        console.log({id_select, valueCari, valueSelect, isAsync});
        if(valueCari != '') {
            var url     = "/marketings/programKerja/jenisPekerjaan/dataProgramDetail/"+valueCari;
            var type    = "GET";
            var data    = "";
            if(valueSelect == '') { var message = Swal.fire({ title : 'Data Sedang Dimuat' }); Swal.showLoading(); } else { var message = "" };

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

function do_simpan(id_form, jenis)
{
    if(id_form == 'modalTransJenisPekerjaan') {
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

function hapus_data(id_form) {
    if(id_form == 'modalTransJenisPekerjaan') {
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