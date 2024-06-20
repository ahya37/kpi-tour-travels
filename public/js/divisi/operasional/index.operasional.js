$(document).ready(function(){
    console.log('test');

    var currMonth   = moment().format('MM');
    var currYear    = moment().format('YYYY');
    var currPaket   = '%';

    // SHOW DATA DASHBOARD
    var url     = site_url + "/getDataDashboard/"+currYear;
    var type    = "GET";

    doTrans(url, type, '', '', false)
        .then((xhr)=>{
            var getData     = xhr.data[0];
            $("#dashboard_jadwal_umrah").html(getData.grand_total_jadwal_umrah);
            $("#dashboard_rules").html(getData.grand_total_rule);
        })
        .catch((xhr)=>{
            $("#dashboard_jadwal_umrah").html(0);
            $("#dashboard_rules").html(0);
        })

    showSelect('programFilterBulan', '%', '%', '');
    showSelect('programFilterTahun', '%', currYear, '');
    showSelect('programFlterPaket', '%', currPaket, true);

    var inputCurrMonth  = $("#programFilterBulan").val();

    showTable('table_jadwal_umrah', [inputCurrMonth, currYear, '%', currPaket]);

    $("#programFilterBtnCari").on('click', function(){
        var selectedMonth   = $("#programFilterBulan").val();
        var selectedYear    = $("#programFilterTahun").val();
        var selectedPaket   = $("#programFlterPaket").val();
        showTable('table_jadwal_umrah', [selectedMonth, selectedYear, '%', selectedPaket])
    });
});

var site_url    = window.location.pathname;

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
                url     : '/divisi/operasional/dataTableGenerateJadwalUmrah'
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
                { "targets" : [0, 5], "className":"text-center" },
                { "targets" : [0], "width": "5%" },
                { "targets" : [2], "width": "18%" },
                { "targets" : [3], "width" : "10%" },
            ],
            pageLength : -1,
            autoWidth   : false,
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
    } else if(idSelect == 'programFlterPaket') {
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
            console.log(xhr);
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
                        ]).draw('false');
                    }
                }
            }) 
            .catch((xhr)=>{
                Swal.fire({
                    icon    : 'error',
                    title   : xhr.statusText
                });
            })

    }
}

function closeModal(idForm) {
    if(idForm == 'modalForm') {
        $("#"+idForm).modal('hide');
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