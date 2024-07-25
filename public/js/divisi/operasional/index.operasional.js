moment.locale('id');

var temp_rules  = [];
var site_url    = window.location.pathname;
var today       = moment().format('YYYY-MM-DD');

$(document).ready(function(){
    var currMonth   = moment().format('MM');
    var currYear    = moment().format('YYYY');
    var currPaket   = '%';
    var data_calendar   = {
        "start_date"    : currYear + "-01-01",
        "end_date"      : currYear + "-12-31",
        "program"       : "%",
        "sub_divisi"    : "%",
        "aktivitas"     : "%",
    };

    // SHOW DATA DASHBOARD
    
    // ambilData()
    //     .then((success) => {
    //         console.log(success);
    //     })
    //     .catch((err)    => {
    //         console.log(err);
    //     })

    var getAllData  = [
        doTrans('/divisi/operasional/getDataDashboard/'+currYear, 'GET', '', '', true),
        doTrans('/operasional/daily/listEventsCalendarOperasional/', 'GET', data_calendar, '', true)
    ];

    Promise.all(getAllData)
        .then((success) => {
            var getData     = success[0].data[0];
            var daily       = success[1];
            $("#dashboard_jadwal_umrah").html(getData.grand_total_jadwal_umrah);
            $("#dashboard_rules").html(getData.grand_total_rule);
            $("#dashboard_activity").html(daily.data.length);

            // SHOW SELECT FOR FILTER
            showSelect('programFilterBulan', '%', '%', '');
            showSelect('programFilterTahun', '%', currYear, '');
            showSelect('programFilterPaket', '%', currPaket, true);

            // SHOW TABLE JADWAL
            var inputCurrMonth  = $("#programFilterBulan").val();
            showTable('table_jadwal_umrah', [inputCurrMonth, currYear, '%', currPaket]);
            
            // SHOW CHART
            showDataOperasional();
        })
        .catch((err)    => {
            console.log(err);
            $("#dashboard_jadwal_umrah").html(0);
            $("#dashboard_rules").html(0);
            showSelect('programFilterBulan', '%', '%', '');
            showSelect('programFilterTahun', '%', currYear, '');
            showSelect('programFilterPaket', '%', currPaket, true);

            var inputCurrMonth  = $("#programFilterBulan").val();

            showTable('table_jadwal_umrah', [inputCurrMonth, currYear, '%', currPaket]);
            showDataOperasional();
        })

    $("#programFilterBtnCari").on('click', function(){
        var selectedMonth   = $("#programFilterBulan").val();
        var selectedYear    = $("#programFilterTahun").val();
        var selectedPaket   = $("#programFilterPaket").val();
        showTable('table_jadwal_umrah', [selectedMonth, selectedYear, '%', selectedPaket])
    });
});

function showModal(idForm, valueCari, jenis)
{
    if(idForm == 'modalForm') {
        showTable('table_list_program_kerja');

        var url     =  site_url + "/getDataRulesJadwal/"+valueCari;
        var type    = "GET";
        var message = Swal.fire({title: 'Data Sedang Dimuat'});Swal.showLoading();

        doTrans(url, type, '', message, true)
            .then((xhr)=>{
                Swal.close();
                $("#"+idForm).modal('show');
                var getData     = xhr.data;
                // console.log(getData);

                // GENERATE JADWAL TO TABLE
                if(getData['list_rules'].length > 0) {
                    for(var i = 0; i < getData['list_rules'].length; i++) {
                        var data_rules                  = getData['list_rules'][i];
                        var rules_id                    = data_rules['rul_id'];
                        var rules_title                 = data_rules['rul_title'];
                        var rules_date                  = moment(data_rules['start_date_job'], 'YYYY-MM-DD').format('DD-MMM-YYYY')+" s/d "+moment(data_rules['end_date_job'], 'YYYY-MM-DD').format('DD-MMM-YYYY');
                        var rules_pic                   = data_rules['pic_role_name'];
                        var rules_duration              = data_rules['number_of_processing_day']+" Hari";
                        var rules_bobot                 = data_rules['rul_bobot'];
                        var rules_realization_duration  = "";
                        var rules_realization_date      = "";
                        var rules_realization_num       = "";
                        var rules_realization_bobot     = "";
                        if(getData['proker_bulanan'].length > 0) {
                            for(var j = 0; j < getData['proker_bulanan'].length; j++) {
                                var data_pkb        = getData['proker_bulanan'][j];
                                var pkb_rul_id      = data_pkb['prog_rul_id'];
                                var pkb_start_date  = moment(data_pkb['pkb_start_date'], 'YYYY-MM-DD').format('DD-MMM-YYYY');
                                var pkb_end_date    = moment(data_pkb['pkb_end_date'], 'YYYY-MM-DD').format('DD-MMM-YYYY');
                                var pkb_status      = data_pkb['status_created'];

                                if(pkb_status == 't') {
                                    if(rules_id == pkb_rul_id)
                                    {
                                        if(pkb_start_date == pkb_end_date) {
                                            // CHECK APAKAH START_DATE > DEPATURE DATE & START_DATE < ARRIVAL DATE
                                            if(data_pkb['pkb_start_date'] >= data_rules['start_date_job'] && data_pkb['pkb_start_date'] <= data_rules['end_date_job']) {
                                                rules_realization_date  += "<span style='color: #1AB394'>"+pkb_start_date+"</span></li>";
                                            } else {
                                                rules_realization_date  += "<span class='text-danger'>"+pkb_start_date+"</span></li>";
                                            }
                                            // CHECK CHECK APAKAH REALISASI HARI = DURASI SLA
                                            if(data_rules['number_of_processing_day'] = 1) {
                                                rules_realization_duration  += "<span style='color: #1AB394'>1 Hari</span>";
                                                rules_realization_bobot     += "<span style='color: #1AB394'>" + parseInt(rules_bobot); + "</span>";
                                            } else {
                                                rules_realization_duration  += "<span class='text-danger'>1 Hari</span>";
                                                rules_realization_bobot     += "<span class='text-danger'>"+(parseInt(rules_bobot) - 1)+"</span>";
                                            }
                                        } else {
                                            // CHECK APAKAH START_DATE > DEPATURE DATE & END_DATE < ARRIVAL DATE
                                            if(data_pkb['pkb_start_date'] > data_rules['start_date_job'] && data_pkb['pkb_end_date'] < data_rules['end_date_job']) {
                                                rules_realization_date  += "<li><span style='color: #1AB394'>"+ pkb_start_date +" s/d " + pkb_end_date + "</span></li>"
                                            } else {
                                                rules_realization_date  += "<li><span class='text-danger'>" + pkb_start_date + " s/d " + pkb_end_date + "</span></li>"
                                            }
                                            // CHECK APAKAH REALISASTI HAIR = DURASI SLA
                                            if(data_rules['number_of_processing_day'] >= moment(data_pkb['pkb_end_date']).diff(moment(data_pkb['pkb_start_date']), 'days')) {
                                                rules_realization_duration  += "<span style='color: #1AB394'>" + moment(data_pkb['pkb_end_date']).diff(moment(data_pkb['pkb_start_date']), 'days') + " Hari</span>";
                                                rules_realization_bobot     += "<span style='color: #1AB394'>" + parseInt(rules_bobot) + "</span>";
                                            } else {
                                                rules_realization_duration  += "<span class='text-danger'>" + moment(data_pkb['pkb_end_date']).diff(moment(data_pkb['pkb_start_date']), 'days') + " Hari</span>";
                                                rules_realization_bobot     += "<span class='text-danger'>" + parseInt(rules_bobot) - moment(data_pkb['pkb_end_date']).diff(moment(data_pkb['pkb_start_date']), 'days') + "</span>";
                                            }
                                        }
                                    }
                                } else {
                                    rules_realization_bobot     += "";
                                    rules_realization_date      += "";
                                    rules_realization_duration  += "";
                                }
                            }
                        }

                        $("#table_list_program_kerja").DataTable().row.add([
                            i + 1,
                            rules_title,
                            rules_date,
                            rules_pic,
                            rules_duration,
                            rules_realization_date,
                            rules_realization_duration,
                            rules_realization_bobot,
                        ]).draw('false');
                    }
                }
            }) 
            .catch((xhr)=>{
                console.log(xhr);
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak ada List yang bisa ditampilkan'
                });
            })

    } else if (idForm == 'modaGenerateRules') {
        // GET DATA
        var url     = site_url + "/getDataRulesJadwalDetail";
        var type    = "GET";
        var data    = {
            "jadwalID"  : valueCari,
        };
        var isAsync = true;
        if(isAsync === true) { var message = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading(); } else { var message = ""; }
        
        doTrans(url, type, data, message, isAsync)
            .then((success)=>{
                $("#"+idForm).modal('show');

                var header  = success.data.header[0];
                var detail  = success.data.detail;
                // INSERT TO HEADER
                $("#jdw_id").val(valueCari);
                $("#programUmrah_text").html(header.jdw_programs_name);
                $("#programUmrah_Jadwal").html(moment(header.jdw_depature_date, 'YYYY-MM-DD').format('DD-MMMM-YYYY')+" s/d "+moment(header.jdw_arrival_date, 'YYYY-MM-DD').format('DD-MMMM-YYYY'));
                $("#programUmrah_Pembimbing").html(header.jdw_mentor_name);
                showTable('tableListRules', valueCari);
                
                for(var i = 0; i < detail.length; i++) {
                    var seq     = i + 1;
                    $("#tableListRules").DataTable().row.add([
                        "<input type='checkbox' id='check_"+seq+"' onclick='transTempData(`check`, "+seq+")'>",
                        detail[i][0],
                        detail[i][1],
                        detail[i][2],
                        detail[i][3],
                        detail[i][4],
                    ]).draw('false')
                }

                for(var i = 0; i < $("#tableListRules").DataTable().rows().count(); i++) {
                    var data_temp_rules     = {
                        "prog_jdw_id"   : $("#jdw_id").val(),
                        "prog_rul_id"   : ""
                    };

                    temp_rules.push(data_temp_rules);
                }
                Swal.close();
            })
            .catch((err)=>{
                console.log(err.responseJSON);
                Swal.close();
            })
    } else if(idForm == 'modalOperasionalDaily') {
        // DATA U/ CARI LIST AKTIVITAS
        var sub_division_name   = $("#current_sub_division").val();
        var sub_division_id     = sub_division_name == 'pic' ? '%' : $("#current_sub_division_id").val();
        var data_aktivitas      = {
            "sub_division"      : sub_division_id,
        };
        // GET DATA
        Swal.fire({
            title   : 'Data Sedang Dimuat'
        });
        Swal.showLoading();
        var getData     = [
            doTrans('/operasional/daily/listFilterDaily', 'GET', '', '', true),
            doTrans('/operasional/daily/listAktivitasProgram', 'GET', data_aktivitas, '', true)
        ];

        Promise.all(getData)
            .then((success) => {
                var data_program        = success[0].data.program;
                var data_sub_division   = success[0].data.sub_division;
                var current_sub_division= $("#current_sub_division").val() == 'pic' ? '%' : $("#current_sub_division_id").val();
                var aktitivas           = success[1].data;

                showSelect('modal_operasional_daily_program', data_program, '', true);
                showSelect('modal_operasional_daily_bagian', data_sub_division, '', true);
                showSelect('modal_operasional_daily_aktivitas', aktitivas, '', true);

                $("#modal_operasional_daily_program").val('%').trigger('change');
                $("#modal_operasional_daily_aktivitas").val('%').trigger('change');
                
                if($("#current_sub_division").val() != 'pic') {
                    $("#modal_operasional_daily_bagian").val(current_sub_division).trigger('change');
                    $("#modal_operasional_daily_bagian").prop('disabled', true);
                } else {
                    $("#modal_operasional_daily_bagian").val(current_sub_division).trigger('change');
                    $("#modal_operasional_daily_bagian").prop('disabled', false);
                }

                $("#"+idForm).modal({ backdrop : 'static', keyboard : false });
                $("#"+idForm).modal('show');

                $("#"+idForm).on('shown.bs.modal', () => {
                    cariData();
                });
            })
            .catch((err)    =>{
                console.log(err);
                $("#"+idForm).modal({ backdrop : 'static', keyboard : false });
                $("#"+idForm).modal('show');
            })
    } else if(idForm == 'modalOperasionalTransaction') {
        // console.log({idForm, valueCari, jenis});
        // LOAD HEADER
        if(jenis == 'add') {
            $("#modalOpperasionalTransaction_title").html('Tambah data pekerjaan bulanan');
            var tgl_awal    = moment(valueCari.startStr).format('DD/MM/YYYY');
            var tgl_akhir   = moment(valueCari.startStr).format('DD/MM/YYYY');
            $("#btnHapus").prop('disabled', true);
        } else if(jenis == 'edit') {
            $("#modalOpperasionalTransaction_title").html('Ubah data pekerjaan bulanan');
            $("#btnHapus").prop('disabled', false);
        }
        // LOAD DATA DULU
        var getData     = [
            doTrans('/operasional/daily/listProkerOperasional', 'GET', '', '', true),
            doTrans('/operasional/daily/listPIC', 'GET', '', '', true),
            jenis == 'add' ? '' : doTrans('/operasional/daily/detailEventsCalendarOperasional', 'GET', valueCari.event.id, true),
        ];
        // SHOW SWAL
        Swal.fire({
            title   : 'Data Sedang Dimuat',
        });
        Swal.showLoading();
        Promise.all(getData)
            .then((success) => {
                // LOAD COMPONENT
                // DATERANGEPICKER
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
                // DATA SELECT
                var tahunan     = success[0].data.tahunan;
                var pic         = success[1].data;
                var detail      = jenis == 'add' ? '' : success[2].data;

                // COMPONENT SELECT
                showSelect('modalOperasionalTransaction_sasaranID', tahunan, '', true);
                showSelect('modalOperasionalTransaction_sasaranDetailID', '', '', true);
                showSelect('modalOperasionalTransaction_picID', pic, $("#current_user").val(), true);
                
                if(jenis == 'add') {
                    $("#modalOperasionalTransaction_startDate").data('daterangepicker').setStartDate(tgl_awal);
                    $("#modalOperasionalTransaction_startDate").data('daterangepicker').setEndDate(tgl_awal);
                    $("#modalOperasionalTransaction_endDate").data('daterangepicker').setStartDate(tgl_akhir);
                    $("#modalOperasionalTransaction_endDate").data('daterangepicker').setEndDate(tgl_akhir);
                } else if(jenis == 'edit') {
                    var pkb_id          = detail.pkb_id;
                    var pkb_title       = detail.pkb_title;
                    var pkb_description = detail.pkb_description;
                    var pkb_start_date  = moment(detail.pkb_start_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
                    var pkb_end_date    = moment(detail.pkb_end_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
                    var pkb_pkt_id      = detail.pkt_id;
                    var pkb_pktd_id     = detail.pktd_id;
                    var pkb_created_by  = detail.created_by;

                    $("#modalOperasionalTransaction_jpkID").val(pkb_id);
                    $("#modalOperasionalTransaction_sasaranID").val(pkb_pkt_id).trigger('change');
                    $("#modalOperasionalTransaction_sasaranDetailID").val(pkb_pktd_id).trigger('change');
                    $("#modalOperasionalTransaction_picID").val(pkb_created_by);
                    $("#modalOperasionalTransaction_title").val(pkb_title);
                    $("#modalOperasionalTransaction_startDate").data('daterangepicker').setStartDate(pkb_start_date);
                    $("#modalOperasionalTransaction_startDate").data('daterangepicker').setEndDate(pkb_start_date);
                    $("#modalOperasionalTransaction_endDate").data('daterangepicker').setStartDate(pkb_end_date);
                    $("#modalOperasionalTransaction_endDate").data('daterangepicker').setEndDate(pkb_end_date);
                    $("#modalOperasionalTransaction_description").val(pkb_description);
                }

                // CLOSE SWAL
                Swal.close();
                // SWOW MODAL
                $("#"+idForm).modal({ backdrop : 'static', keyboard : false });
            })
            .catch((err)    => {
                console.log(err);
                Swal.close();
            })

            $("#btnSave").val(jenis);
    }
}

function showDataOperasional()
{
    var url     = site_url + "/getJobUser";
    var type    = "GET";
    
    doTrans(url, type, '', '', true)
        .then((success)=>{
            // SHOW CART
            $("#showLoading_chart").hide();
            $("#showView_chart").show();

            var getData     = success.data;
            var dataLabels      = [];
            var data_DataSets   = [];

            if(getData['chart'].length > 0) {
                for(var i = 0; i < getData['chart'].length; i++) {
                    dataLabels.push(getData['chart'][i]['employee_name']);
                    data_DataSets.push(getData['chart'][i]['total_job']);
                }
            }

            // SHOW CHART
            const canvas    = document.getElementById('myChart');
            var ctx = canvas.getContext('2d');
            var myChart = new Chart(ctx, {
                type: 'bar',
                maintainAspectRatio: false,
                data: {
                    labels: dataLabels,
                    datasets: [{
                        label: 'Program Kerja Bulanan',
                        data: data_DataSets,
                        backgroundColor: "#1AB394",
                        borderWidth: 1
                    }]
                },
                options: {
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    },
                    responsive  : true,
                    maintainAspectRatio  : false
                }
            });

            ctx.canvas.onclick = function(evt) {
                var activePoints = myChart.getElementsAtEvent(evt);
                if (activePoints.length > 0) {
                    var clickedDatasetIndex = activePoints[0]._datasetIndex;
                    var clickedElementindex = activePoints[0]._index;
                    var label = myChart.data.labels[clickedElementindex];
                    var value = myChart.data.datasets[clickedDatasetIndex].data[clickedElementindex];
                }
            }

            // SHOW TABLE
            $("#showLoading_table").hide();
            $("#showView_table").show();
            showTable('table_ListUser', '');
            if(getData['table'].length > 0) {
                for(var i = 0; i < getData['table'].length; i++) {
                    $("#table_ListUser").DataTable().row.add([
                        i + 1,
                        getData['table'][i]['full_name'],
                        getData['table'][i]['sub_division_name'],
                    ]).draw('false');
                }
            }

        })
        .catch((error)=>{
            console.log(error);
            showTable('table_ListUser', '');
            $("#showLoading_chart_icon").hide();
            $("#showLoading_chart_text").html('Tidak Ada Data');
            
        })
}


function showCalendar(tglSekarang, value)
{
    console.log({value});
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
            var current_date    = tglSekarang;
            var start_date      = moment(current_date, 'YYYY-MM-DD').startOf('month').format('YYYY-MM-DD');
            var end_date        = moment(current_date, 'YYYY-MM-DD').endOf('month').format('YYYY-MM-DD');

            var url             = '/operasional/daily/listEventsCalendarOperasional';
            var data            = {
                "start_date"    : start_date,
                "end_date"      : end_date,
                "program"       : value[0] == null ? '%' : value[0],
                "sub_divisi"    : value[1] == null ? '%' : value[1],
                "aktivitas"     : value[2] == null ? '%' : value[2],
            };
            var type            = "GET";
            var message         = Swal.fire({ title : "Data Sedang Dimuat" });Swal.showLoading();
            var isAsync         = true;
            var temp            = [];
            
            doTrans(url, type, data, message, isAsync)
                .then((success) => {
                    for(var i = 0; i < success.data.length; i++) {
                        if(success.data[i].prog_pkb_is_created == 'f') {
                            var color     = '#dc3545';
                        } else {
                            var color     = '#1ab394';
                        }
                        temp.push({
                            title   : success.data[i].pkb_title,
                            start   : success.data[i].pkb_start_date,
                            end     : success.data[i].pkb_end_date,
                            allDay  : true,
                            id      : success.data[i].pkb_id,
                            color   : color
                        });
                    }
                    successCallback(temp);
                    Swal.close();
                })
                .catch((err)    => {
                    console.log(err);
                    Swal.close();
                })
        },
        moreLinkContent:function(args){
            return '+'+args.num+' Lainnya';
        },
        select  : function(arg) {
            // console.log(arg);
            showModal('modalOperasionalTransaction', arg, 'add');
        },
        eventClick  : function(arg) {
            // console.log(arg);
            showModal('modalOperasionalTransaction', arg, 'edit');
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
                    $("#modal_operasional_daily_year").empty();
                    $("#modal_operasional_daily_year").append(moment(today, 'YYYY-MM-DD').format('YYYY'));
                    $("#modal_operasional_daily_month").empty();
                    $("#modal_operasional_daily_month").append(moment(today, 'YYYY-MM-DD').format('MMMM'));
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

function showTable(idTable, valueCari)
{
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'table_jadwal_umrah') {
        $("#"+idTable).DataTable({
            language    : {
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat...",
                "zeroRecords"   : "Tidak ada data yang bisa ditampilkan..",
                "emptyTable"    : "Tidak ada data yang bisa ditampilkan.."
            },
            columnDefs  : [
                { targets: [0, 6], className: "text-center", width: "7%" },
                { targets: [4, 5], className: "text-center", width: "16%" },
                { targets: [2], className: "text-left", width: "18%" },
            ],
            processing  : true,
            serverSide  : false,
            ajax        : {
                type    : "GET",
                dataType: "json",
                data    : {
                    sendData    : {
                        cari    : valueCari,
                    },
                },
                url     : '/divisi/operasional/dataTableGenerateJadwalUmrah',
            },
        })
    } else if(idTable == 'table_list_program_kerja') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "zeroRecords"   : "Tidak ada data yang bisa ditampilkan..",
                "emptyTable"    : "Tidak ada data yang bisa ditampilkan..",
            },
            columnDefs  : [
                { "targets" : [0, 4], "className":"text-center align-middle" },
                { "targets" : [0], "width": "5%" },
                { "targets" : [2], "width": "18%" },
                { "targets" : [3], "width": "10%" },
                { "targets" : [5], "width": "22%" , "className" : "align-middle"},
                { "targets" : [4, 6], "width" : "8%"},
                { "targets" : [7], "width" : "5%" },
                { "targets" : [0, 1, 2, 3, 4, 5, 6, 7], "className":"align-middle"},
            ],
            pageLength  : -1,
            autoWidth   : false,
            paging      : false
        });
    } else if(idTable == 'tableListRules') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "emptyTable"    : "Tidak ada data yang bisa dimunculkan..",
                "zeroRecords"   : "Tidak ada data yang bisa dimunculkan.."
            },
            pageLength  : -1,
            paging      : false,
            ordering    : false,
            bInfo       : false,
            columnDefs  : [
                { "targets" : [0, 2, 3, 4, 5], "className" : "text-center align-middle"},
                { "targets" : [0], "width" : "5%" },
            ],
        })
    } else if(idTable == 'table_ListUser') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "emptyTable"    : "Tidak ada data yang bisa ditampilkan..",
                "zeroRecords"   : "Tidak ada data yang bisa ditampilkan..",
            },
            searching   : false,
            pageLength  : -1,
            scrollY: '250px',
            scrollCollapse: true,
            paging: false,
            columnDefs  : [
                { "targets" : [0], "width" : "5%", "className" : "text-center align-middle" },
            ],
        });
    }
}

function showSelect(idSelect, valueCari, valueSelect, isAsync)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
    if(idSelect == 'programFilterBulan') {
        var month   = moment.months();
        var html    = [
            "<option selected disabled>Pilih Bulan</option>",
            "<option value='%'>Semua</option>"
        ];
        for(var i = 0; i < month.length; i++) {
            var id      = moment(month[i], 'MMM').format('MM');
            var text    = month[i];
            html    += "<option value='" + id + "'>" + text + "</option>";
        }

        $("#"+idSelect).html(html);
        if(valueSelect != '') {
            $("#"+idSelect).val(valueSelect).trigger('change');
        }
    } else if(idSelect == 'programFilterTahun') {
        var html            = "<option selected disabled>Pilih Tahun</option>";
        var current_year    = moment().format('YYYY');
        var past_year_10    = moment(current_year, 'YYYY').subtract(10, 'years').year();
        var future_year_10  = moment(current_year, 'YYYY').add(10, 'years').year();

        for(let i = past_year_10; i <= future_year_10; i++) {
            html    += "<option value='" + i + "'>" + i + "</option>"
        }
        
        $("#"+idSelect).html(html);
        if(valueCari != '') {
            $("#"+idSelect).val(valueSelect).trigger('change');
        }
    } else if(idSelect == 'programFilterPaket') {
        var html    = [
            "<option selected disabled>Pilih Paket Program Umrah</option>",
            "<option value='%'>Semua</option>"
        ];
        var url     = "/master/data/getProgramUmrah/umrah";
        var data    = {
            "cari"  : valueCari,
        };
        doTrans(url, "GET", data, '', isAsync)
            .then(function(xhr){
                for(var i = 0; i < xhr.data.length; i++) {
                    html    += "<option value='" + xhr.data[i]['program_id'] + "'>" + xhr.data[i]['program_name'] + "</option>";
                }

                $("#"+idSelect).html(html);
                
                if(valueSelect != '') {
                    $("#"+idSelect).val(valueSelect).trigger('change');
                }
            })
            .catch(function(xhr){
                console.log(xhr);
            });

        $("#"+idSelect).html(html);
    } else if(idSelect == 'modalOperasionalTransaction_sasaranID') {
        var html    = "<option selected disabled>Pilih Sasaran</option>";

        if(valueCari != '') {
            $.each(valueCari, (i, item) => {
                html    += "<option value='" + item.pkt_id + "'>" + item.pkt_title + "</option>";
            });
            $("#"+idSelect).html(html);
            
            $("#"+idSelect).on('change', function(){
                var sasaranID   = this.value;
                var sasaranDetail   = [];

                for(var i = 0; i < valueCari.length; i++) {
                    if(valueCari[i].pkt_id == sasaranID) {
                        sasaranDetail.push(valueCari[i].detail);
                        $("#modalOperasionalTransaction_groupDivisionName").val(valueCari[i].pkt_group_division_name);
                    }
                }
                showSelect('modalOperasionalTransaction_sasaranDetailID', sasaranDetail[0], '', true);
            });
            
        } else {
            $("#"+idSelect).html(html);
        }

    } else if(idSelect == 'modalOperasionalTransaction_sasaranDetailID') {
        var html    = "<option selected disabled>Pilih Sasaran Detail</option>";

        if(valueCari != '') {
            var jenis   = $("#btnSave").val();
            if(jenis == 'add') {
                Swal.fire({
                    title   : 'Data Sedang Dimuat',
                });
                Swal.showLoading();
            }

            setTimeout(()   => {
                Swal.close();
                jenis == 'add' ? $("#"+idSelect).select2('open') : '';
            }, 500)
            $.each(valueCari, (i, item) => {
                html    += "<option value='"+ item.pktd_seq +"'>"+ item.pktd_title +"</option>";
            });
            html    += "<option value='more'>Lainnya</option>";
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'modalOperasionalTransaction_picID') {
        var html    = "<option selected disabled>Pilih PIC / Penanggung Jawab</option>";
        if(valueCari != '') {
            $.each(valueCari, (i, item) => {
                html    += "<option value='"+ item.user_id +"'>" + item.user_name + "</option>";
            });
            $("#"+idSelect).html(html);
            if(valueSelect != '') {
                $("#"+idSelect).val(valueSelect).trigger('change');
            }
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'modal_operasional_daily_program') {
        var html    = "<option selected disabled>Pilih Program</option>";
        
        if(valueCari != '') {
            html        += [
                "<option value='%'>Semua</option>",
            ];

            // console.log({valueCari});
            $.each(valueCari, (i, item) => {
                var prog_id         = item.jdw_uuid;
                var prog_name       = item.program_name;
                var prog_start_date = item.program_start_date;
                var prog_end_date   = item.program_end_date;
                var prog_title      = prog_name+" - "+moment(prog_start_date, 'YYYY-MM-DD').format('DD/MM/YYYY')+" s/d "+moment(prog_end_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
                html    += "<option value='"+ prog_id +"'>" + prog_title + "</option>";
            })
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'modal_operasional_daily_bagian') {
        var html    = "<option selected disabled>Pilih Bagian</option>";
        
        if(valueCari != '') {
            html    += "<option value='%'>Semua</option>";

            $.each(valueCari, (i, item) => {
                var sub_div_id  = item.sub_division_id;
                var sub_div_name= item.sub_division_name;

                html    += "<option value='" + sub_div_id + "'>" + sub_div_name + "</option>";
            });
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'modal_operasional_daily_aktivitas') {
        var html    = "<option selected disabled>Pilih Aktivitas</option>";

        if(valueCari != '') {
            html    += "<option value='%'>Semua</option>";
            $.each(valueCari, (i, item) => {
                var rul_id      = item.rule_id;
                var rul_title   = item.rul_title;
                var rul_sla     = item.rul_sla;

                html    += "<option value='" + rul_id + "'>("+ rul_sla +") " + rul_title + "</option>";
            });
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    }
}

function generateRules(element, id)
{
    var depature_date   = $(element).data('startdate');
    var arrival_date    = $(element).data('enddate');
    var program_id      = id;

    var url             = site_url + "/generateRules";
    var type            = "GET";
    var data            = {
        "depature_date" : depature_date,
        "arrival_date"  : arrival_date,
        "program_id"    : program_id,
    }
    var isAsync         = true;
    var message         = Swal.fire({title:'Data sedang diproses'});Swal.showLoading();

    doTrans(url, type, data, message, isAsync)
        .then((xhr) => {
            Swal.fire({
                icon    : xhr.alert.icon,
                title   : xhr.alert.message.title,
                text    : xhr.alert.message.text,
            }).then((results)=>{
                if(results.isConfirmed) {
                    showTable('table_jadwal_umrah', ['07', '2024', '%', '%']);
                }
            });
        })
        .catch((xhr) => {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Tidak ada Rules baru yang bisa digenerate',
            })
        })
}

function closeModal(idForm) {
    if(idForm == 'modalForm') {
        $("#"+idForm).modal('hide');
        
    } else if(idForm == 'modaGenerateRules') {
        $("#selectAll").prop('checked', false);
        
        temp_rules  = [];
    } else if(idForm == 'modalOperasionalDaily') {
        $("#"+idForm).modal('hide');
        $("#"+idForm).on('hidden.bs.modal', function(){
            today   = moment().format('YYYY-MM-DD');
            $("#filterOperasional").collapse('hide');
        })
    } else if(idForm == 'modalOperasionalTransaction') {
        $("#"+idForm).modal('hide');

        $("#"+idForm).on('hidden.bs.modal', function(){
            $("#modalOperasionalTransaction_title").val(null);
            $(".calendar").val(null);
            $("#modalOperasionalTransaction_description").val(null);
            $("#modalOperasionalTransaction_jpkID").val(null);
        })
    }
}

function selectAllTable(idTable, idCheck)
{
    if(idTable == 'tableListRules') {
        if($("#"+idCheck).is(":checked") === true) {
            for(var i = 0; i < temp_rules.length; i++) {
                var seq     = i + 1;
                $("#check_"+seq).prop('checked', true);

                temp_rules[i]['prog_rul_id'] = seq;
            }
        } else {
            for(var i = 0; i < temp_rules.length; i++) {
                var seq     = i + 1;
                $("#check_"+seq).prop('checked', false);

                temp_rules[i]['prog_rul_id'] = "";
            }
        }
    }
}

// DIGUNKANA UNTUK CHECKBOX
function transTempData(idCheck, seq)
{
    if(temp_rules.length > 0) {
        var new_seq     = seq - 1;
        if($("#"+idCheck+"_"+seq).is(":checked") == true) {
            temp_rules[new_seq]['prog_rul_id'] = seq;
        } else if($("#"+idCheck+"_"+seq).is(":checked") == false) {
            temp_rules[new_seq]['prog_rul_id'] = '';
        } 
    } else {
        var data    = {
            "prog_jdw_id"       : $("#jdw_id").val(),
            "prog_rul_id"       : seq,
        };

        temp_rules.push(data);
    }
}

function doSimpan(idForm, jenis)
{
    if(idForm == "modalOperasionalTransaction") {
        // GET DATA
        var pkb_id  = $("#modalOperasionalTransaction_jpkID");
        var pkt_id  = $("#modalOperasionalTransaction_sasaranID");
        var pktd_id = $("#modalOperasionalTransaction_sasaranDetailID");
        var pkb_created_by  = $("#modalOperasionalTransaction_picID");
        var pkb_title       = $("#modalOperasionalTransaction_title");
        var pkb_start_date  = $("#modalOperasionalTransaction_startDate");
        var pkb_end_date    = $("#modalOperasionalTransaction_endDate");
        var pkb_description = $("#modalOperasionalTransaction_description");


        if(pkt_id.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Sasaran Harus Dipilih',
            }).then((results)   => {
                if(results.isConfirmed) {
                    pkt_id.select2('open');
                }
            })
        } else if(pktd_id.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Sasaran Detail Harus Dipilih',
            }).then((resulst)   => {
                if(resulst.isConfirmed) {
                    pktd_id.select2('open');
                }
            })
        } else if(pkb_title.val() == '') {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Uraian Harus diisi',
            }).then((results)   => {
                if(results.isConfirmed) {
                    pkb_title.focus();
                }
            })
        } else {
            var sendData    = {
                "pkb_id"            : pkb_id.val(),
                "pkt_id"            : pkt_id.val(),
                "pktd_id"           : pktd_id.val(),
                "pkb_created_by"    : pkb_created_by.val(),
                "pkb_title"         : pkb_title.val(),
                "pkb_start_date"    : moment(pkb_start_date.val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "pkb_end_date"      : moment(pkb_end_date.val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "pkb_description"   : pkb_description.val(),
                "jenis"             : jenis,
            };

            // DO SIMPAN
            var url         = "/operasional/daily/simpan";
            var type        = "POST";
            var data        = sendData;
            var message     = Swal.fire({ title : "Data Sedang Diproses" }); Swal.showLoading();
            doTrans(url, type, data, message, true)
                .then((success) => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text,
                    }).then((results)   => {
                        if(results.isConfirmed) {
                            closeModal(idForm);
                            var program                     = $("#modal_operasional_daily_program").val();
                            var bagian                      = $("#modal_operasional_daily_bagian").val();
                            var aktivitas                   = $("#modal_operasional_daily_aktivitas").val();
                            showCalendar(today, [program, bagian, aktivitas]);
                        }
                    })
                    // console.log({success});
                })
                .catch((err)    => {
                    Swal.fire({
                        icon    : err.responseJSON.alert.icon,
                        title   : err.responseJSON.alert.message.title,
                        text    : err.responseJSON.alert.message.text,
                    });
                    console.log(err);
                })

        }
    }
}

function cariData()
{
    var program_id  = $("#modal_operasional_daily_program").val();
    var sub_divisi  = $("#modal_operasional_daily_bagian").val();
    var aktivitas   = $("#modal_operasional_daily_aktivitas").val();
    showCalendar(today, [program_id, sub_divisi, aktivitas]);
}

function doHapus(idForm)
{
    if(idForm == 'modalOperasionalTransaction') {
        Swal.fire({
            icon    : 'question',
            title   : 'Hapus Data?',
            text    : 'Data yang telah dihapus tidak akan muncul kembali di kalender, apakah anda yakin?',
            showConfirmButton : true,
            showCancelButton  : true,
            confirmButtonText   : 'Ya, Hapus',
            cancelButtonText    : 'Batal',
            confirmButtonColor  : '#dc3545'
        }).then((results)   => {
            if(results.isConfirmed) {
                // TRANS DELETE
                var id      = $("#modalOperasionalTransaction_jpkID").val();
                var url     = "/operasional/daily/hapus";
                var data    = {
                    "id"    : id,
                };
                var message = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
                var isAsync = true;

                doTrans(url, 'POST', data, message, isAsync)
                    .then((success) => {
                        // console.log(success);
                        Swal.fire({
                            icon    : success.alert.icon,
                            title   : success.alert.message.title,
                            text    : success.alert.message.text,
                        }).then((results)   => {
                            if(results.isConfirmed) {
                                closeModal(idForm);
                                cariData();
                            }
                        })
                    })
                    .catch((err)    => {
                        console.log(err);
                        Swal.fire({
                            icon    : err.responseJSON.alert.icon,
                            title   : err.responseJSON.alert.mesasge.title,
                            text    : err.responseJSON.alert.message.text, 
                        })
                    })
            }
        })
    }
}

function doTrans(url, type, data, customMessage, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            cache   : false,
            type    : type,
            async   : isAsync,
            url     : url,
            data    : {
                _token  : CSRF_TOKEN,
                sendData: data,
            },
            beforeSend  : function() {
                customMessage;
            },
            success     : function(xhr) {
                resolve(xhr)
            },
            error       : function(xhr) {
                reject(xhr)
            }
        })
    });
}

// function ambilData()
// {
//     var apiUrl  = 'https://api-percik.perciktours.com/api';
//     var apiKey  = 'YjIzMTE5NTg1ZDQ1MDJiYWMyMTJmMDZhZDAxMGY1MjM4NWNhOTQxOQ==';

//     var headers = {
//         'x-api-key' : apiKey,
//         'Access-Control-Allow-Origin': '*',
//         'Content-Type':'application/json'
//     };

//     return new Promise((resolve, reject)    => {
//         $.ajax({
//             cache   : false,
//             type    : 'GET',
//             async   : true,
//             url     : apiUrl+"/umrah/tourcode?year=2025",
//             headers : headers,
//             dataType: "json",
//             success : (success) => {
//                 console.log(success);
//             },
//             error   : (err) => {
//                 console.log(err);
//             }
//         })
//     }) 
// }