moment.locale('id');
var prsTempData = []; 
var base_url    = window.location.origin;
var latitude;
var longitude;
var today       = moment().format('YYYY-MM-DD');
var jamKerjaTemp;

const getDiffTime   = (time_1, time_2) => {
    const countDiff     = moment(time_2, 'HH:mm:ss').diff(moment(time_1, 'HH:mm:ss'), 'seconds');
    const hours         = Math.floor(countDiff / 3600) < 10 ? "0"+Math.floor(countDiff / 3600) : Math.floor(countDiff / 3600);
    const minute        = Math.floor((countDiff % 3600) / 60) < 10 ? "0"+Math.floor((countDiff % 3600) / 60) : Math.floor((countDiff % 3600) / 60);
    const second        = countDiff % 60 < 10 ? "0"+countDiff % 60 : countDiff % 60;

    return hours+":"+minute+":"+second;
}

$(document).ready(()    => {
    // GET DATA KEHADIRAN
    const abs_url   = base_url + "/dashboard/absensi/get_user_presence";
    const abs_type  = "GET";
    const abs_data  = {
        "data"  : {
            "user_id"           : $("#prs_user_id").val(),
            "selected_month"    : moment(today, 'YYYY-MM-DD').format('MM'),
            "selected_year"     : moment(today, 'YYYY-MM-DD').format('YYYY'),
        }
    };

    const ct_url    = base_url + "/pengajuan/listCuti";
    const ct_type   = "GET";
    const ct_data   = {
        "bulan"     : moment(today, 'YYYY-MM-DD').format('MM')
    };

    const jamURL    = base_url + "/divisi/human_resource/jam_kerja/data_jam_kerja";
    const jamType   = "GET";
    const jamData   = {
        "today" : moment().format('YYYY-MM-DD'),
    };
    
    const api_url   = [
        doTrans(abs_url, abs_type, abs_data, "", true),
        doTrans(ct_url, ct_type, ct_data, "", true),
        doTrans(jamURL, jamType, jamData, "", true)
    ];

    Promise.allSettled(api_url)
        .then((success)     => {
            const abs_get_data  = success[0].value.data.length;
            $("#dashboard_total_absen_text").html(abs_get_data);

            const ct_get_data   = success[1].value.data.length;
            $("#dashboard_total_isc_text").html(ct_get_data);

            const jamGetData    = success[2].value.data;
            jamKerjaTemp    = jamGetData;
        })
        .catch((error)      => {
            $("#dashboard_total_absen_text").html(0);
            $("#dashboard_total_isc_text").html(0);
        })
});

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'tbl_total_absen') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "Tidak Ada Data Yang Bisa Ditampilkan",
                zeroRecords : "Data Yang Dicari Tidak Ditemukan"
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1, 2, 3, 4, 5, 6], "className" : "text-center align-middle", "width" : "13%" },
            ],
            pageLength  : -1,
            paging      : false,
            bInfo       : false,
        });
        
        if(data != '') {
            let i = 1;
            let totalOverTime   = moment.duration(0);
            let totalLateTime   = moment.duration(0);
            for(const item of data)
            {
                const seq               = i++;
                const presenceDate      = moment(item['prs_date'], 'YYYY-MM-DD').format('DD MMM YYYY');
                const presenceDayName   = item['prs_day_name'];
                const presenceIn        = item['prs_in'];
                const presenceOut       = item['prs_out'];
                const totalWorkHour     = '00:00:00';
                const lateTime          = item['prs_late_time'];
                const overTime          = item['prs_over_time'];

                $("#"+idTable).DataTable().row.add([
                    `<label class="no-margins">${seq}</label>`,
                    `<label class="no-margins">${presenceDayName}, ${presenceDate}</label>`,
                    `<label class="no-margins">${presenceIn}</label>`,
                    `<label class="no-margins">${presenceOut}</label>`,
                    `<label class="no-margins">${totalWorkHour}</label>`,
                    `<label class="no-margins">${lateTime}</label>`,
                    `<label class="no-margins">${overTime}</label>`
                ]).draw(false);

                totalOverTime.add(moment.duration(overTime));
                totalLateTime.add(moment.duration(lateTime));
            }

            // console.log({totalOverTime, totalLateTime});

            // $("#tbl_total_absen_keterlambatan").html(moment(totalLateTime).format('hh:mm:ss'));
            // $("#tbl_total_absen_lebih_jam").html(totalOverTime);

            // $("#tbl_total_absen_keterlambatan_1").html($("#tbl_total_absen_keterlambatan").text());
            // $("#tbl_total_absen_lebih_jam_1").html($("#tbl_total_absen_lebih_jam").text());
            $("#tbl_total_absensi").html(data.length);

            $("#"+idTable).find('.dataTabels_empty').html('Data Ditemukan');
        } else {
            $("#"+idTable).find('.dataTables_empty').html("Tidak Ada Data Yang Bisa Ditampilkan");
        }

        $("#tbl_total_absen_title").removeClass('text-center');
        $("#tbl_total_absen_title").addClass('text-right');
    } else if(idTable == 'tbl_total_cuti') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan",
            },
            columnDefs  : [
                { "targets" : [0], "className" : "text-center", "width" : "8%" },
                { "targets" : [1], "className" : "text-left", "width" : "25%" },
                { "targets" : [3, 4], "className" : "text-center align-middle", "width" : "15%" },
            ],
            autoWidth   : false,
            pageLength  : -1,
            paging      : false,
            bInfo       : false,
        });

        if(data.length > 0) {
            $("#tbl_total_cuti tbody .dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan");

            let seq         = 1;
            var ct_status   = "";
            for(const item of data)
            {
                const ct_date   = item.emp_act_start_date == item.emp_act_end_date ? moment(item.emp_act_start_date, 'YYYY-MM-DD').format('dddd') + ", " + moment(item.emp_act_start_date, 'YYYY-MM-DD').format('DD/MM/YYYY') : moment(item.emp_act_start_date, 'YYYY-MM-DD').format('DD/MM/YYYY')+" s.d "+moment(item.emp_act_end_date, 'YYYY-MM-DD').format('DD/MM/YYYY');
                const ct_desc   = item.emp_act_title;
                const ct_type   = item.emp_act_type;
                switch(item.emp_act_status) {
                    case '1' :
                        ct_status  = "<span class='badge badge-success'>Diterima</span>";
                    break;
                    case '2' :
                        ct_status  = "<span class='badge badge-danger'>Ditolak</span>"; 
                    break;
                    case '3' :
                        ct_status  = "<span class='badge badge-warning text-dark'>Menunggu Konfirmasi</span>";
                    break;
                }


                $("#"+idTable).DataTable().row.add([
                    seq++,
                    "<label>" + ct_date+ "</label>",    
                    "<label>" + ct_desc + "</label>",    
                    "<label>" + ct_type + "</label>",    
                    "<label>" + ct_status + "</label>",    
                ]).draw(false);
            }
        } else {
            $("#tbl_total_cuti tbody .dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan");
        }
    } else if(idTable == 'tbl_total_absen_admin') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat`,
                zeroRecords : `Data Yang Dicari Tidak Ditemukan`
            },
            columnDefs  : [
                { "targets" : [0], "className" : "text-center", "width" : "8%" },
                { "targets" : [1], "className" : "text-left", "width" : "20%" },
                { "targets" : [2], "className" : "text-left", "width" : "15%" },
                { "targets" : [3, 4, 5, 6, 7], "className" : "text-center align-middle", "width" : "13%" },
            ],
            autoWidth   : false,
            pageLength  : -1,
            paging      : false,
            bInfo       : false,
        });

        if(data != '') {
            for(let i = 0; i < data.length; i++) {
                let ke  = i + 1;
                let namaKaryawan    = data[i].prs_name;
                let tglAbsen        = data[i].prs_date;
                const getJamKerja   = jamKerjaTemp.find(item => {
                    let jamKerja    = moment(tglAbsen, 'YYYY-MM-DD').format('YYYY-MM-DD') >= moment(item.date_start, 'YYYY-MM-DD').format('YYYY-MM-DD') && moment(tglAbsen, 'YYYY-MM-DD').format('YYYY-MM-DD') <= moment(item.date_end, 'YYYY-MM-DD').format('YYYY-MM-DD');
                    return jamKerja;
                });
                let jamMasukMax     = getJamKerja.data_clock[moment(tglAbsen, 'YYYY-MM-DD').isoWeekday() - 1].clock_in;
                let jamKeluarMax    = getJamKerja.data_clock[moment(tglAbsen, 'YYYY-MM-DD').isoWeekday() - 1].clock_out;
                let jamMasuk        = data[i].prs_in_time == null ? '' : moment(data[i].prs_in_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss');
                let jamKeluar       = data[i].prs_out_time == null ? '' : moment(data[i].prs_out_time, 'YYYY-MM-DD HH:mm:ss').format('HH:mm:ss');
                let jamKerja        = jamKeluar != '' ? getDiffTime(jamMasuk, jamKeluar) : '';
                let jamTelat        = jamKeluar != '' ? (jamMasuk > moment(jamMasukMax, 'HH:mm:ss').add(1, 'seconds').format('HH:mm:ss') ? getDiffTime(jamMasukMax, jamMasuk) : "00:00:00") : "";
                let jamLebih        = jamKeluar != '' ? (jamKeluar > moment(jamKeluarMax, 'HH:mm:ss').add(1, 'seconds').format('HH:mm:ss') ? getDiffTime(jamKeluarMax, jamKeluar) : "00:00:00") : "";

                $("#"+idTable).DataTable().row.add([
                    `<label class="fw-normal no-margins">${ke}</label>`,
                    `<label class="fw-normal no-margins">${namaKaryawan}</label>`,
                    `<label class="fw-normal no-margins">${moment(tglAbsen, 'YYYY-MM-DD').format('dddd')}, ${moment(tglAbsen, 'YYYY-MM-DD').format('DD/MM/YYYY')}</label>`,
                    `<label class="fw-normal no-margins">${jamMasuk}</label>`,
                    `<label class="fw-normal no-margins">${jamKeluar}</label>`,
                    `<label class="fw-normal no-margins">${jamKerja}</label>`,
                    `<label class="fw-normal no-margins">${jamTelat}</label>`,
                    `<label class="fw-normal no-margins">${jamLebih}</label>`,
                ]).draw(false);
            }
        }
    }
}

function showModal(idModal, jenis)
{
    if(idModal == 'modal_total_absen')
    {
        const user_id       = $("#prs_user_id").val();
        const current_month = moment(today, 'YYYY-MM-DD').format('MM');
        const current_year  = moment(today, 'YYYY-MM-DD').format('YYYY');

        const abs_url       = base_url + "/dashboard/absensi/get_user_presence";
        const abs_type      = "GET";
        const abs_data      = {
            "data"          : {
                "user_id"       : user_id,
                "selected_month": current_month,
                "selected_year" : current_year,
            }
        };
        const abs_msg       = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();

        doTrans(abs_url, abs_type, abs_data, abs_msg, true)
            .then((success)     => {
                // CLOSE MODAL
                Swal.close();
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                // SHOW TABLE
                showTable('tbl_total_absen', success.data);
                showTable('tbl_total_absen_admin', success.data);
                // SHOW SELECT
                showSelect('tbl_filter_month', '', '');
            })
            .catch((error)      => {
                console.log(error);
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak Ada Data Yang Bisa Ditampilkan'
                });
            })
    } else if(idModal == 'modal_total_ketidakhadiran') {
        // GET DATA CUTI
        const cuti_url  = base_url + "/pengajuan/listCuti";
        const cuti_data = {
            "bulan"     : moment(today, 'YYYY-MM-DD').format('MM'),
        };
        const cuti_type = "GET";
        const cuti_msg  = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();

        doTrans(cuti_url, cuti_type, cuti_data, cuti_msg, true)
            .then((success)     => {
                $("#"+idModal).modal({
                    keyboard    : false,
                    backdrop    : 'static'
                });
                
                const cuti_getData   = success.data;
                showTable('tbl_total_cuti', cuti_getData);

                const selectDataBulanCuti   = moment(today, 'YYYY-MM-DD').format('MM');
                const dataBulanCuti         = moment.months();
                showSelect('filter_bulan_cuti', dataBulanCuti, selectDataBulanCuti);
                Swal.close();
            })
            .catch((error)      => {
                console.log(error);
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Data Izin / Sakit / Cuti Tidak Ada',
                });
            })
    }
}

function closeModal(idModal)
{
    $("#"+idModal).modal('hide');
    clearUrl();
    if(idModal == 'modal_total_absen') {

    }
}

function showSelect(idSelect, data, selectedData)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4'
    });
    
    if(idSelect == 'tbl_filter_month')
    {
        var html        = "<option selected disabled>Pilih Bulan</option>";
        
        data            = moment.months();
        selectedData    = moment().format('MM');

        let seq         = 1;
        for(const item of data)
        {
            const monthNumber   = seq < 10 ? "0"+seq++ : seq++;
            const monthName     = item;

            html    += "<option value='" + monthNumber + "'>" + monthName + "</option>";
        }

        $("#"+idSelect).html(html);
        
        if(selectedData != '') {
            $("#"+idSelect).val(selectedData).trigger('change');
        };
    } else if(idSelect == 'filter_bulan_cuti') {
        let html    = "<option selected disabled>Pilih Bulan</option>";
        let seq     = 1;
        for(const item of data) {
            const monthNumber   = seq < 10 ? "0"+seq++ : seq++;
            const monthName     = item;
            
            html    += `<option value=${monthNumber}>${monthName}</option>`;
        }

        $(`#${idSelect}`).html(html);

        if(selectedData != '') {
            $(`#${idSelect}`).val(selectedData).trigger('change');
        }
    }
}

function simpanData(jenis) 
{
}

function cariData(idForm)
{
    if(idForm == 'tbl_total_absen')
    {
        const user_id           = $("#prs_user_id").val();
        const selected_month    = $("#tbl_filter_month").val();
        const selected_year     = moment(today, 'YYYY-MM-DD').format('YYYY');

        const abs_url           = base_url + "/dashboard/absensi/get_user_presence";
        const abs_type          = "GET";
        const abs_data          = {
            "data"  : {
                "user_id"       : user_id,
                "selected_month": selected_month,
                "selected_year" : selected_year,
            }
        };
        const abs_msg           = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();

        doTrans(abs_url, abs_type, abs_data, abs_msg, true)
            .then((success)     => {
                showTable('tbl_total_absen', success.data);
                showTable('tbl_total_absen_admin', success.data);
                Swal.close();
            })
            .catch((error)      => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak Ada Absen Pada Bulan yang Dipilih',
                });
            })
    } else if(idForm == 'tbl_total_cuti') {
        const cuti_url  = base_url + "/pengajuan/listCuti";
        const cuti_data = {
            "bulan"     : $("#filter_bulan_cuti").val(),
        };
        const cuti_type = "GET";
        const cuti_msg  = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();

        doTrans(cuti_url, cuti_type, cuti_data, cuti_msg, true)
            .then((success)     => {
                const cuti_getData  = success.data;
                showTable(`${idForm}`, cuti_getData);
                Swal.close();
            })
            .catch((err)        => {
                showTable(`${idForm}`, []);
                Swal.close();
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
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
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


function clearUrl()
{
    var url     = window.location.href;
    var cleanUrl= url.split('#')[0];
    window.history.replaceState({}, document.title, cleanUrl);
}