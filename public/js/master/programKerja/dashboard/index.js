$(document).ready(function(){
    // GET DATA
    dashboard();
    // showSelect('dashboard_bulan', '%', '', true);
    // showSelect('dashboard_divisi', '', '', true);
    // showSelect('dashboard_create', '', '', true);
})

var base_url    = window.location.pathname;
var group_division  = $("#group_division").val();
var sub_division    = $("#sub_division").val();
var user_id         = sub_division == 'manager' || sub_division == 'pic' || sub_division == '%' ? '%' : $("#user_id").val();
var user_role       = $("#user_role").val();

function dashboard() {
    // GET SUMMARY DASHBOARD
    var url     = base_url+"/getDataTotalProgramKerja";
    var type    = "GET";
    var message =   NProgress.start();
    var isAsync = true;

    transData(url, type, '', message, isAsync)
        .then(function(xhr){
            $("#pk_tahunan").text(xhr.data[0].grand_total_proker_tahunan+" Program Kerja");
            $("#pk_bulanan").text(xhr.data[0].grand_total_proker_bulanan+" Program Kerja");
            $("#pk_harian").text(xhr.data[0].grand_total_proker_harian+" Program Kerja");
            $("#summary_header").show();
            showData('table_programKerja_bulanan', true, [group_division, user_id, moment().format('MM')]);
            NProgress.done();
        })
        .catch(function(err){
            console.log(err.responseJSON);
            $("#pk_tahunan").text("0 Program Kerja");
            $("#pk_bulanan").text("0 Program Kerja");
            $("#pk_harian").text("0 Program Kerja");
            $("#summary_header").show();
            showData('table_programKerja_bulanan', true, [group_division, user_id, moment().format('MM')]);
            NProgress.done();
        })
}

function showTable(idTable)
{
    if(idTable == 'table_programKerja_bulanan') {
        $("#v_table_programKerja_bulanan").show();
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language   : {
                "emptyTable"    : "Tidak ada data yang bisa ditampilkan",
                "zeroRecords"   : "Tidak ada data yang bisa ditampilkan",
            },
            columnDefs : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [2, 3, 4], "className" : "text-left align-middle", "width" : "14%" },
                { "targets" : [1], "className" : "text-left" },
                { "targets" : [5], "className" : "text-left", "width" : "7%" },
            ],
        });
    }
}

function showData(id, asyncStatus, sendData)
{
    if(id == 'table_programKerja_bulanan') {
        moment.locale('id');
        showTable(id);
        var url     = base_url + "/getDataTableDashboard";
        var type    = "GET";
        var data    = {
            "groupDivisi"   : sendData[0],
            "createdBy"     : sendData[1],
            "createdMonth"  : sendData[2],
        };
        if( asyncStatus === true ) { var message = Swal.fire({ title : 'Table Sedang Dimuat' }); Swal.showLoading(); } else { var message = ""; }

        transData(url, type, data, message, asyncStatus)
            .then((xhr)=>{
                Swal.close();

                var getData     = xhr.data;
                $.each(getData, function(i,item){
                    if(item['status_active'] == 't') {
                        var badge   = "<span class='badge badge-pill badge-primary'>Aktif</span>";
                    } else {
                        var badge   = "<span class='badge badge-pill badge-danger'>Tidak Aktif</span>";
                    }
                    $("#"+id).DataTable().row.add([
                        i + 1,
                        item['pkb_title'],
                        item['pkb_start_date'] == item['pkb_end_date'] ? moment(item['pkb_start_date'], 'YYYY-MM-DD').format('DD-MMM-YYYY') : moment(item['pkb_start_date'], 'YYYY-MM-DD').format('DD-MMM-YYYY')+" s/d "+moment(item['pkb_end_date'], 'YYYY-MM-DD').format('DD-MMM-YYYY'),
                        item['group_division_name'],
                        item['created_by_name'],
                        badge
                    ]).draw('false');
                })
            })
            .catch((xhr)=>{
                Swal.close();
                console.log(xhr);
            })
    } else if(id == 'btnFilter') {
        var current_month   = moment().format('MM');
        var areFilterOpen   = $("#filter").data('isopen');
        if(areFilterOpen == "f") {
            showSelect('dashboard_divisi', '%', '', true);
            $("#filter").data('isopen', "t");
        }
        $("#filter").on('shown.bs.collapse', function(){
            showSelect('dashboard_bulan', '%', current_month, true);
            showSelect('dashboard_create', '', '', true);
        })

        $("#filter").on('hidden.bs.collapse', function(){
            showSelect('dashboard_bulan', '%', '', true);
            showSelect('dashboard_create', '', '', true);
            $("#filter").data('isopen', "f");
        })
    }
}

function showDataTable(idTable)
{
    if(idTable == 'table_programKerja_bulanan') {
        var groupDivisi     = $("#dashboard_divisi");
        var createdBy       = $("#dashboard_create");
        var createdMonth    = $("#dashboard_bulan");

        if(groupDivisi.val() == null) {
            groupDivisi.select2('open');
        } else if(createdBy.val() == null) {
            createdBy.select2('open');
        } else {
            showData(idTable, true, [groupDivisi.val(), createdBy.val(), createdMonth.val()]);
        }
    }
}

function showSelect(idSelect, valueCari, valueSelect, isAsync)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
    
    if(idSelect == 'dashboard_bulan') {
        var html    = "<option selected disabled>Pilih Bulan</option>";
        if(valueCari != '') {
            $("#"+idSelect).html(html);
            var allMonth    = moment.months();
            $.each(allMonth, function(i,item){
                var monthNumber     = moment(item, 'MMM').format('MM');
                var monthName       = item;
                html    += "<option value='" + monthNumber + "'>" + monthName + "</option>"
            });
            $("#"+idSelect).html(html);

            if(valueSelect != '') {
                $("#"+idSelect).val(valueSelect);
            }
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'dashboard_divisi') {
        var html    = "<option selected disabled>Pilih Divisi</option>";
        if(valueCari != '') {
            var optAll  = "<option value='%'>Semua</option>";
            html    += optAll;
            var url     = "/master/data/trans/get/groupDivision";
            var type    = "GET";
            var message = "";
            var data    = valueCari;
            transData(url, type, data, message, isAsync)
                .then((success)=>{
                    var getData     = success.data;
                    $.each(getData, function(i,item){
                        html        += "<option value='" + item['id'] + "'>" + item['name'] + "</option>";
                    })
                    $("#"+idSelect).html(html);
                })
                .catch((err)=>{
                    console.log(err);
                    $("#"+idSelect).html(html);
                })
            
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'dashboard_create') {
        var html    = "<option selected disabled>Pilih User</option>";
        if(valueCari != '') {
            var optAll  = "<option value='%'>Semua</option>";

            // GET DATA USER
            if(valueSelect == '%') {
                html    += optAll;
                $("#"+idSelect).html(html);
                $("#"+idSelect).val(valueSelect).trigger('change');
            } else {
                html    += optAll;
                var url     = base_url + "/getDatatableDashboardListUser";
                var type    = "GET";
                var data    = {
                    "groupDivisionID"   : valueSelect,
                };
                var message = Swal.fire({ title : 'Data Sedang Dimuat' }); Swal.showLoading();
                transData(url, type, data, message, isAsync)
                    .then((success)=>{
                        $.each(success.data, function(i,item){
                            html    += "<option value='" + item.user_id + "'>" + item.name + "</option>";
                        })
                        Swal.close();
                        $("#"+idSelect).html(html);
                        $("#"+idSelect).select2('open');
                    })
                    .catch((err)=>{
                        $("#"+idSelect).html(html);
                        Swal.close();
                    })
            }
        } else {
            $("#"+idSelect).html(html);
        }
    }
}

function transData(url, type, data, customMessage, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            cache   : false,
            type    : type,
            dataType: "json",
            data    : {
                _token  : CSRF_TOKEN,
                sendData : data,
            },
            async   : isAsync,
            url     : url,
            beforeSend  : function() {
                customMessage;
            },
            success : function(xhr) {
                resolve(xhr);
            },
            error   : function(xhr) {
                reject(xhr);
            },
        })
    });
}