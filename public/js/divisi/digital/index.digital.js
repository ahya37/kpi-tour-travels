var today   = moment().format('YYYY-MM-DD');
$(document).ready(function(){
    console.log('test');

    showCalendar(today, '');
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
            // var current_date    = tglSekarang;
            // var start_date      = moment(current_date, 'YYYY-MM-DD').startOf('month').format('YYYY-MM-DD');
            // var end_date        = moment(current_date, 'YYYY-MM-DD').endOf('month').format('YYYY-MM-DD');

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
            // showModal('modalOperasionalTransaction', arg, 'add');
        },
        eventClick  : function(arg) {
            // console.log(arg);
            // showModal('modalOperasionalTransaction', arg, 'edit');
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
                    // VISUAL UPDATE
                    $("#kalender_bulan").html('test');
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
                    // VISUAL UPDATE
                    $("#modal_operasional_daily_year").empty();
                    $("#modal_operasional_daily_year").append(moment(today, 'YYYY-MM-DD').format('YYYY'));
                    $("#modal_operasional_daily_month").empty();
                    $("#modal_operasional_daily_month").append(moment(today, 'YYYY-MM-DD').format('MMMM'));
                }
            },
            refreshCustomButton     : {
                click   : function() {
                    today   = moment().format('YYYY-MM-DD');
                    var program                     = $("#modal_operasional_daily_program").val();
                    var bagian                      = $("#modal_operasional_daily_bagian").val();
                    var aktivitas                   = $("#modal_operasional_daily_aktivitas").val();
                    showCalendar(today, [program, bagian, aktivitas]);
                }
            }
        },
    });
    calendar.render();
    $(".fc-nextCustomButton-button").html("<i class='fa fa-chevron-right'></i>").prop('title', 'Bulan Selanjutnya');
    $(".fc-prevCustomButton-button").html("<i class='fa fa-chevron-left'></i>").prop('title', 'Bulan Sebelumnya');
    $(".fc-refreshCustomButton-button").html("<i class='fa fa-undo'></i>").prop('title','Hari ini');

    // VISUAL UPDATE
    $("#modal_operasional_daily_year").empty();
    $("#modal_operasional_daily_year").append(moment(tglSekarang, 'YYYY-MM-DD').format('YYYY'));
    $("#modal_operasional_daily_month").empty();
    $("#modal_operasional_daily_month").append(moment(tglSekarang, 'YYYY-MM-DD').format('MMMM'));
}