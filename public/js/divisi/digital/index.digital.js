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

            Swal.fire({
                title   : 'Data Sedang Dimuat',
                allowOutsideClick   : false,
            });
            Swal.showLoading();

            // GET DATA CALENDAR
            const calendar_data     = {
                "start_date"    : start_date,
                "end_date"      : end_date,
            };
            const calendar_url      = "/divisi/digital/listEventsCalendarDigital";

            // GET DATA ACT UESR
            const actUser_data  = {
                "today"     : start_date,
            };
            const actUser_url   = "/divisi/digital/listAktivitasHarian";

            const globalType        = "GET";

            const transData     = [
                doTrans(calendar_url, globalType, calendar_data, ''),
                doTrans(actUser_url, globalType, actUser_data, '')
            ];
            
            Promise.all(transData)
                .then((success)     => {
                    // CALENDAR AREA
                    const calendar_getData  = success[0].data;
                    const calendar_temp     = [];
                    for(const item of calendar_getData)
                    {
                        calendar_temp.push({
                            title   : item.pkh_title,
                            start   : item.pkh_date,
                            end     : moment(item.pkh_date, 'YYYY-MM-DD').add(1, 'days').format('YYYY-MM-DD'),
                            allDay  : true,
                            id      : item.pkh_id
                        });
                    }
                    successCallback(calendar_temp);

                    // ACT USER AREA
                    const actUser_getData   = success[1].data;
                    $("#total_act_user").empty();
                    $("#total_act_user").html(actUser_getData.length);

                    Swal.close();
                })
                .catch((err)        => {
                    Swal.close();
                    console.log('Tidak ada data yang bisa dimuat');
                })
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
    } else if(idModal == 'modal_act_user') {
        // GET DATA
        const actUser_url   = "/divisi/digital/listAktivitasHarian";
        const actUser_data  = {
            "today" : moment(today, 'YYYY-MM-DD').startOf('month').format('YYYY-MM-DD'),
        };
        const actUser_type  = "GET";
        
        Swal.fire({
            title   : 'Data Sedang Dimuat',
        });
        Swal.showLoading();

        doTrans(actUser_url, actUser_type, actUser_data, '')
            .then((success) => {
                $("#"+idModal).modal({backdrop: 'static', keyboard: false});
                Swal.close();

                // SHOW TABLE
                $("#modal_act_usert_month").html(moment(today, 'YYYY-MM-DD').format('MMMM'));
                const actUser_data  = success.data;
                showTable('table_list_act_user', actUser_data);
                $("#dataTables_empty").empty();
                $("#dataTables_empty").html("Data Berhasil Dimuat");
            })
            .catch((err)    => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak ada Program pada bulan '+moment(today, 'YYYY-MM-DD').format('MMMM')
                });
                console.log(err);
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
    } else if(idModal == 'modal_act_user') {
        $("#"+idModal).modal('hide');
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

function showTable(idTable, data)
{
    if(idTable == 'table_list_act_user') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Diproses",
                "zeroRecords"   : "Data yang dicari tidak ada",
            },
            paging      : false,
            pageLength  : -1,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "30%" },
                { "targets" : [2], "className" : "text-left align-middle"},
                { "targets" : [3], "className" : "text-left align-middle", "width" : "15%" },
                { "targets" : [4], "className" : "text-left align-middle", "width" : "10%" },
                { "targets" : [5], "className" : "text-left align-middle", "width" : "15%" },
            ],
        });

        if(data.length > 0) {
            let i = 1;
            let total_num_result    = 0;
            let total_num_target    = 0;
            for(const item of data)
            {
                let actUser_detail_persentase   = (parseInt(item.pkbd_num_result) / parseInt(item.pkbd_num_target)) * 100;
                const actUser_detail_type       = item.pkbd_pic == '0' ? 'Umum' : 'Khusus';
                $("#"+idTable).DataTable().row.add([
                    i++,
                    item.pkb_title,
                    item.pkbd_type,
                    item.pkbd_num_result + " / " + item.pkbd_num_target,
                    parseFloat(actUser_detail_persentase).toFixed(0)+"%",
                    actUser_detail_type
                ]).draw(false);

                total_num_target += item.pkbd_num_target;
                total_num_result += item.pkbd_num_result;
            }
            let total_persentase    = (parseInt(total_num_target) / parseInt(total_num_result)) * 100;
            $("#table_list_act_user_total_target").html(total_num_target + " / " + total_num_result);
            $("#table_list_act_user_total_persentase").html(parseFloat(total_persentase).toFixed(0)+"%");
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