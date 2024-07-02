moment.locale('id');

var temp_rules  = [];
var site_url    = window.location.pathname;

$(document).ready(function(){
    console.log('test');

    var currMonth   = moment().format('MM');
    var currYear    = moment().format('YYYY');
    var currPaket   = '%';

    // SHOW DATA DASHBOARD
    var url     = site_url + "/getDataDashboard/"+currYear;
    var type    = "GET";
    var message = NProgress.start();

    doTrans(url, type, '', message, true)
        .then((xhr)=>{
            var getData     = xhr.data[0];
            $("#dashboard_jadwal_umrah").html(getData.grand_total_jadwal_umrah);
            $("#dashboard_rules").html(getData.grand_total_rule);
            NProgress.done();
        })
        .catch((xhr)=>{
            $("#dashboard_jadwal_umrah").html(0);
            $("#dashboard_rules").html(0);
            NProgress.done();
        })

    showSelect('programFilterBulan', '%', '%', '');
    showSelect('programFilterTahun', '%', currYear, '');
    showSelect('programFilterPaket', '%', currPaket, true);

    var inputCurrMonth  = $("#programFilterBulan").val();

    showTable('table_jadwal_umrah', [inputCurrMonth, currYear, '%', currPaket]);

    $("#programFilterBtnCari").on('click', function(){
        var selectedMonth   = $("#programFilterBulan").val();
        var selectedYear    = $("#programFilterTahun").val();
        var selectedPaket   = $("#programFilterPaket").val();
        showTable('table_jadwal_umrah', [selectedMonth, selectedYear, '%', selectedPaket])
    });
    showDataOperasional();
});

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
                { targets: [0, 5], className: "text-center", width: "7%" },
                { targets: [3, 4], className: "text-center", width: "16%" },
                { targets: [1], className: "text-left", width: "18%" },
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
                url     : site_url + '/dataTableGenerateJadwalUmrah'
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
                { "targets" : [2, 5], "width": "20%" },
                { "targets" : [3], "width" : "10%" },
                { "targets" : [4, 6], "width" : "8%"},
                { "targets" : [0, 1, 2, 3, 4, 5, 6], "className" : "align-middle" },
            ],
            pageLength : -1,
            autoWidth   : false,
            paging  : false,
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

function showModal(idForm, valueCari)
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

                if(getData.length > 0) {
                    for(var i = 0; i < getData.length; i++) {
                        $("#table_list_program_kerja").DataTable().row.add([
                            getData[i][0],
                            getData[i][1],
                            getData[i][2],
                            getData[i][3],
                            getData[i][4],
                            getData[i][5],
                            getData[i][6],
                        ]).draw('false');
                    }
                }
            }) 
            .catch((xhr)=>{
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
    }
}

function closeModal(idForm) {
    if(idForm == 'modalForm') {
        $("#"+idForm).modal('hide');
        
    } else if(idForm = 'modaGenerateRules') {
        $("#selectAll").prop('checked', false);
        
        temp_rules  = [];
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