var today   = moment().format('YYYY-MM-DD');

window.onload = () => {
    $('.fc-toolbar.fc-header-toolbar').addClass('row col-lg-12');
}

$(document).ready(function(){
    showCalendar(today, '');
    $('.fc-toolbar.fc-header-toolbar').addClass('row col-lg-12');
})

function showCalendar(tglSekarang, value)
{
    $("#calendar_loading").hide();
    var tgl_sekarang    = tglSekarang;
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
            const current_date      = tglSekarang;
            const start_date        = moment(current_date, 'YYYY-MM-DD').startOf('month').format('YYYY-MM-DD');
            const end_date          = moment(current_date, 'YYYY-MM-DD').endOf('month').format('YYYY-MM-DD');

            const sendData          = {
                "start_date"    : start_date,
                "end_date"      : end_date,
            };
            const message           = Swal.fire({ title : 'Data Sedang Dimuat' }); Swal.showLoading();
            const temp              = [];
            
            doTrans('/divisi/digital/listEventsCalendarDigital', 'GET', sendData, message, '')
                .then((success) => {
                    for(let i = 0; i < success.data.length; i++) {
                        temp.push({
                            title   : success.data[i].pkh_title,
                            start   : success.data[i].pkh_date,
                            end     : moment(success.data[i].pkh_date, 'YYYY-MM-DD').add(1, 'days'),
                            allDay  : true,
                            id      : success.data[i].pkh_id
                        });
                    }
                    Swal.close();
                    successCallback(temp);
                })
                .catch((err)    => {
                    console.log('Tidak ada data pada bulan ini');
                    Swal.close();
                })

            // var url             = '/operasional/daily/listEventsCalendarOperasional';
            // var data            = {
            //     "start_date"    : start_date,
            //     "end_date"      : end_date,
            //     "program"       : value[0] == null ? '%' : value[0],
            //     "sub_divisi"    : value[1] == null ? '%' : value[1],
            //     "aktivitas"     : value[2] == null ? '%' : value[2],
            // };
            // var type            = "GET";
            // var message         = Swal.fire({ title : "Data Sedang Dimuat" });Swal.showLoading();
            // var isAsync         = true;
            // var temp            = [];
            
            // doTrans(url, type, data, message, isAsync)
            //     .then((success) => {
            //         for(var i = 0; i < success.data.length; i++) {
            //             if(success.data[i].prog_pkb_is_created == 'f') {
            //                 var color     = '#dc3545';
            //             } else {
            //                 var color     = '#1ab394';
            //             }
            //             temp.push({
            //                 title   : success.data[i].pkb_title,
            //                 start   : success.data[i].pkb_start_date,
            //                 end     : success.data[i].pkb_end_date,
            //                 allDay  : true,
            //                 id      : success.data[i].pkb_id,
            //                 color   : color
            //             });
            //         }
            //         successCallback(temp);
            //         Swal.close();
            //     })
            //     .catch((err)    => {
            //         console.log(err);
            //         Swal.close();
            //     })
        },
        moreLinkContent:function(args){
            return '+'+args.num+' Lainnya';
        },
        select  : function(arg) {
            // console.log(arg);
            showModal('modal_form', arg, 'add');
        },
        eventClick  : function(arg) {
            // console.log(arg);
            showModal('modal_form', arg, 'edit');
        },
        customButtons: {
            prevCustomButton: {
                // text: "",
                click: function() {
                    var hari_ini_bulan_lalu         = moment(today).subtract(1, 'month').format('YYYY-MM-DD');
                    var program                     = $("#modal_operasional_daily_program").val();
                    var bagian                      = $("#modal_operasional_daily_bagian").val();
                    var aktivitas                   = $("#modal_operasional_daily_aktivitas").val();
                    showCalendar(hari_ini_bulan_lalu, [program, bagian, aktivitas]);
                    today   = hari_ini_bulan_lalu;
                    $("#kalender_bulan").html("("+ moment(today, 'YYYY-MM-DD').format('MMMM') +")");
                }
            },
            nextCustomButton : {
                click : function() {
                    var hari_ini_bulan_depan         = moment(today).add(1, 'month').format('YYYY-MM-DD');
                    var program                     = $("#modal_operasional_daily_program").val();
                    var bagian                      = $("#modal_operasional_daily_bagian").val();
                    var aktivitas                   = $("#modal_operasional_daily_aktivitas").val();
                    showCalendar(hari_ini_bulan_depan, [program, bagian, aktivitas]);
                    today   = hari_ini_bulan_depan;
                    $("#kalender_bulan").html("("+ moment(today, 'YYYY-MM-DD').format('MMMM') +")");
                }
            },
            refreshCustomButton     : {
                click   : function() {
                    today   = moment().format('YYYY-MM-DD');
                    var program                     = $("#modal_operasional_daily_program").val();
                    var bagian                      = $("#modal_operasional_daily_bagian").val();
                    var aktivitas                   = $("#modal_operasional_daily_aktivitas").val();
                    showCalendar(today, [program, bagian, aktivitas]);
                    $("#kalender_bulan").html("("+ moment(today, 'YYYY-MM-DD').format('MMMM') +")");
                }
            }
        },
    });
    calendar.render();
    $(".fc-nextCustomButton-button").html("<i class='fa fa-chevron-right'></i>").prop('title', 'Bulan Selanjutnya');
    $(".fc-prevCustomButton-button").html("<i class='fa fa-chevron-left'></i>").prop('title', 'Bulan Sebelumnya');
    $(".fc-refreshCustomButton-button").html("<i class='fa fa-undo'></i>").prop('title','Hari ini');

    // VISUAL UPDATE
    $("#kalender_bulan").html("("+ moment(today, 'YYYY-MM-DD').format('MMMM') +")");
}

function showModal(idModal, data, jenis)
{
    if(idModal == 'modal_form') {
        $(".waktu").daterangepicker({
            singleDatePicker    : true,
            autoApply           : true,
            timePicker          : true,
            timePicker24Hour    : true,
            timePickerIncrement : 1,
            timePickerSeconds   : true,
            locale: {
                format: 'HH:mm:ss'
            }
        }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find(".calendar-table").hide();
        });

        $("#jpk_title").on('keyup', ()=> {
            const jpk_title     = $("#jpk_title").val().toUpperCase();
            $("#jpk_title").val(jpk_title);
        });

        const pkh_id    = jenis != 'add' ? { 'id' : data.event.id } : '';
        // GET DATA
        const sendData = [
            doTrans('/divisi/digital/getDataProgramDigital', 'GET', {today : jenis == 'add' ? data.startStr : data.event.startStr}, ''),
            jenis != 'edit' ? '' : doTrans('/divisi/digital/listEventsCalendarDigitalDetail', 'GET', pkh_id, '', '')
        ];

        Swal.fire({
            title   : 'Data Sedang Dimuat',
        });
        Swal.showLoading();

        Promise.all(sendData)
            .then((success) => {
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                const dataProgram       = success[0];

                showSelect('jpk_programID', dataProgram.data.header, '', '');
                showSelect('jpk_programDetailID', [], '', '');

                $("#jpk_programID").on('change', () => {
                    const dataProgramDetail     = [];
                    const programID             = $("#jpk_programID").val();
                    for(let i = 0; i < dataProgram.data.detail.length; i++) {
                        if(dataProgram.data.detail[i].pkb_id == programID)
                        {
                            dataProgramDetail.push(dataProgram.data.detail[i]);
                        }
                    }

                    showSelect('jpk_programDetailID', dataProgramDetail, '', '');
                });

                if(jenis == 'edit') {
                    $("#btnHapus").removeClass('d-none');
                    const detailProkerHarian    = success[1].data[0];
                    $("#jpk_ID").val(detailProkerHarian.pkh_id);
                    $("#jpk_programID").val(detailProkerHarian.pkh_pkb_id.split(' | ')[0]).trigger('change');
                    $("#jpk_programDetailID").val(detailProkerHarian.pkh_pkb_id.split(' | ')[1]).trigger('change');
                    $("#jpk_startTime").val(detailProkerHarian.pkh_start_time.split(' ')[1]);
                    $("#jpk_endTime").val(detailProkerHarian.pkh_end_time.split(' ')[1]);
                    $("#jpk_title").val(detailProkerHarian.pkh_title);
                }

                Swal.close();
            })
            .catch((err)    => {
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                console.log(err);
                showSelect('jpk_programID', [], '', '');
                showSelect('jpk_programDetailID', [], '', '');

                Swal.close();
            })

        if(jenis == 'add') {
            $("#modal_form_title").html("Tambah Aktivitas Harian "+moment(data.startStr, 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#jpk_date").val(data.startStr);
        } else if(jenis == 'edit') {
            $("#modal_form_title").html("Ubah Aktivitas Harian");
            $("#jpk_date").val(data.event.startStr);
        }

        // DEFINE BUTTON
        $("#"+idModal).on('shown.bs.modal', () => {
            $("#btnSimpan").val(jenis);
        })
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_form') {
        $("#"+idModal).modal('hide');
        
        $("#"+idModal).on('hidden.bs.modal', () => {
            $(".waktu").val('00:00:00');
            $("#jpk_title").val(null);
            $("#jpk_ID").val(null);
            $("#jpk_date").val(null);
            $("#btnHapus").addClass('d-none');
        })
    }
}

function showSelect(idSelect, data, value, seq)
{
    const select    = $("#"+idSelect+""+seq);
    select.select2({
        theme   : 'bootstrap4',
    });

    if(idSelect == 'jpk_programID') {
        var html    = "<option selected disabled>Pilih Program</option>";
        if(data.length > 0 ) {
            $.each(data, (i, item)  => {
                html += "<option value='" + item.pkb_id + "'>" + item.name  + " - " + item.pktd_title + "</option>";
            });
            select.html(html);
        } else {
            select.html(html);
        }
    } else if(idSelect == 'jpk_programDetailID') {
        var html    = "<option selected disabled>Pilih Jenis Pekerjaan</option>";

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html += "<option value='" + item.pkbd_id + "'>" + item.pkbd_type + "</option>";
            });
            select.html(html);
        } else {
            select.html(html);
        }
    }
}

function simpanData(jenis)
{
    const daily_ID              = $("#jpk_ID");
    const daily_date            = $("#jpk_date");
    const daily_programID       = $("#jpk_programID");
    const daily_programDetailID = $("#jpk_programDetailID");
    const daily_startTime       = $("#jpk_startTime");
    const daily_endTime         = $("#jpk_endTime");
    const daily_title           = $("#jpk_title");

    // VALIDASI
    if(daily_programID.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Program Harus Dipilih',
            didClose    : () => {
                daily_programID.select2('open');
            }
        })
    } else if(daily_programDetailID.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Program Detail Harus Dipilih',
            didClose    : () => {
                daily_programDetailID.select2('open');
            }
        })
    } else if(daily_title.val() == '') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Uraian tidak boleh kosong',
            didClose    : () => {
                daily_title.focus();
            }
        })
    } else {
        const sendData  = {
            "daily_ID"              : daily_ID.val(),
            "daily_date"            : daily_date.val(),
            "daily_programID"       : daily_programID.val(),
            "daily_programDetailID" : daily_programDetailID.val(),
            "daily_startTime"       : daily_startTime.val(),
            "daily_endTime"         : daily_endTime.val(),
            "daily_title"           : daily_title.val(),
        };

        const message   = Swal.fire({ title : 'Data Sedang Dimuat' }); Swal.showLoading();

        const simpanData    = doTrans('/divisi/digital/simpanAktivitasHarian/'+jenis, 'POST', sendData, message);

        simpanData.then((success)   => {
            Swal.fire({
                icon    : success.alert.icon,
                title   : success.alert.message.title,
                text    : success.alert.message.text,
            }).then((results)   => {
                if(results.isConfirmed) {
                    closeModal('modal_form');
                    showCalendar(today);
                }
            })
        }).catch((err)  => {
            Swal.fire({
                icon    : err.responseJSON.alert.icon,
                title   : err.responseJSON.alert.message.title,
                text    : err.responseJSON.alert.message.text,
            });
            console.log(err);
        })
    }
}

function doTrans(url, type, data, message)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            dataType: "json",
            cache   : false,
            type    : type,
            url     : url,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                message;
            },
            success     : (success) => {
                resolve(success)
            },
            error       : (err) => {
                reject(err);
            }
        })
    })
}