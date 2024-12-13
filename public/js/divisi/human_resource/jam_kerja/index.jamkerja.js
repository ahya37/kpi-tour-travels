var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
var dataURL     = [];

const msg       = () => {
    return Swal.fire({ title : "Data Sedang Dimuat", allowOutsideClick: false }); Swal.showLoading();
}

// TAMPUNG URL FILE DISINI
dataURL.push(
    {
        "title" : "get_jam_kerja",
        "url"   : "divisi/human_resource/jam_kerja/data_jam_kerja",
        "type"  : "GET",
        "data"  : {
            "today" : today,
        },
    },
)

$(document).ready(function() {
    console.log('Hey, You Found Me!');
    const getJamKerja   = dataURL.find(item => item['title'] == "get_jam_kerja");

    const getData       = [
        doTransaction(getJamKerja.url, getJamKerja.type, getJamKerja.data, ''),
    ];

    Promise.allSettled(getData)
        .then((success)     => {
            console.log(success);
            let dataJamKerja    = success[0].status == 'fulfilled' ? success[0].value.data : [];
            showTable('table_jam_kerja', dataJamKerja, '');
            
            showCurrentJamKerja(dataJamKerja);
            // SHOW CURRENT_TIME
            $("#current_time_loading").addClass('d-none');
            $("#current_time_loading").removeClass('d-flex flex-column align-items-center');
            $("#current_time").removeClass('d-none');
            $("#current_time").addClass('d-flex flex-row align-items-center justify-content-between');
        })
        .catch((err)        => {
            console.log(err);
            showTable('table_jam_kerja', [], '');
            $("#table_jam_kerja").find('.dataTables_empty').html(err.responseJSON.message);
        })
});

function showTable(idTable, data, id = null)
{
    let tableLanguage   = {
        "emptyTable"    : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat`,
        "zeroRecords"   : `Data Yang Dicari Tidak Ditemukan`
    };

    // DESTROY TABLE BEFORE INIT
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'table_jam_kerja') {
        $("#"+idTable).DataTable({
            language    : tableLanguage,
            ordering    : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1, 2], "className" : "align-middle", "width" : "10%"},
                { "targets" : [3, 4, 5, 6, 7, 8, 9], "className" : "text-center align-middle"}
            ],
            autoWidth   : false,
        });

        if(data.length > 0) {
            let seq = 1;
            for(const item of data)
            {
                let ke          = `<label class="fw-normal no-margins">${seq++}</label>`;
                let startDate   = `<label class="fw-normal no-margins">${item['date_start']}</label>`;
                let endDate     = `<label class="fw-normal no-margins">${item['date_end']}</label>`;
                let day1        = `<label class="fw-normal no-margins">${item['data_clock'][0]['clock_in']+" s/d "+item['data_clock'][0]['clock_out']}</label>`;
                let day2        = `<label class="fw-normal no-margins">${item['data_clock'][1]['clock_in']+" s/d "+item['data_clock'][1]['clock_out']}</label>`;
                let day3        = `<label class="fw-normal no-margins">${item['data_clock'][2]['clock_in']+" s/d "+item['data_clock'][2]['clock_out']}</label>`;
                let day4        = `<label class="fw-normal no-margins">${item['data_clock'][3]['clock_in']+" s/d "+item['data_clock'][3]['clock_out']}</label>`;
                let day5        = `<label class="fw-normal no-margins">${item['data_clock'][4]['clock_in']+" s/d "+item['data_clock'][4]['clock_out']}</label>`;
                let day6        = `<label class="fw-normal no-margins">${item['data_clock'][5]['clock_in']+" s/d "+item['data_clock'][5]['clock_out']}</label>`;
                let day7        = `<label class="fw-normal no-margins">${item['data_clock'][6]['clock_in']+" s/d "+item['data_clock'][6]['clock_out']}</label>`;

                $("#"+idTable).DataTable().row.add([
                    ke,
                    startDate,
                    endDate,
                    day1,
                    day2,
                    day3,
                    day4,
                    day5,
                    day6,
                    day7
                ]).draw(false);
            }
        }
    }

    // REMOVE FOOTER
    $("#"+idTable+"_wrapper").css('padding-bottom', '0px');
}

function showDate(type, idForm, data)
{
    if(type == "tanggal") {
        let startDate   = data != '' ? moment(data).format('DD/MM/YYYY') : moment(today).format('DD/MM/YYYY');
        let endDate     = data != '' ? moment(data).format('DD/MM/YYYY') : moment(today).format('DD/MM/YYYY');
        $("#"+idForm).daterangepicker({
            autoApply           : false,
            format              : 'DD/MM/YYYY',
            singleDatePicker    : true,
            locale              : {
                format  : 'DD/MM/YYYY'
            }
        }).on('cancel.daterangepicker', () => {
            $("#"+idForm).data('daterangepicker').setStartDate(startDate);
            $("#"+idForm).data('daterangepicker').setEndDate(endDate);
            if(data == '') {
                $("#"+idForm).val('');
            }
        }).on('')
    } else if(type == 'jam') {
        let startDate   = data != '' ? moment(today+" "+data, 'YYYY-MM-DD HH:mm:ss') : moment(today+" 00:00:00", 'YYYY-MM-DD HH:mm:ss');
        let endDate     = data != '' ? moment(today+" "+data, 'YYYY-MM-DD HH:mm:ss') : moment(today+" 00:00:00", 'YYYY-MM-DD HH:mm:ss');
        $("#"+idForm).daterangepicker({
            autoApply   : false,
            timePicker  : true,
            timePicker24Hour  : true,
            timePickerSeconds : false,
            locale      : {
                format      : 'HH:mm',
                cancelLabel : 'Batal',
                applyLabel  : 'Simpan',
            },
            singleDatePicker    : true,
            showDropdowns       : true,
            drops               : "down",
        }).on('show.daterangepicker', (ev, picker) => {
            picker.container.find('.calendar-table').hide();
        }).on('cancel.daterangepicker', (ev, picker) => {
            $("#"+idForm).data('daterangepicker').setStartDate(startDate);
            $("#"+idForm).data('daterangepicker').setEndDate(endDate);
            if(data == '') {
                $("#"+idForm).val('');
            }
        });
    }
}

function showCurrentJamKerja(data)
{
    if(data.length > 0) {
        const getCurrentDate    = data.find(item => moment(item.date_start).isBefore(moment('2024-12-12')));
        for(let i = 0; i < getCurrentDate.data_clock.length; i++)
        {
            let ke  = i + 1;
            $("#day"+ke).html(`(${getCurrentDate.data_clock[i].clock_in} s/d ${getCurrentDate.data_clock[i].clock_out})`);
            $("#day_"+ke+"_mobile").html(`${getCurrentDate.data_clock[i].clock_in} s/d ${getCurrentDate.data_clock[i].clock_out}`);
        }
        $("#table_jam_kerja").find('.dataTables_empty').html(`Data Berhasil Dimuat`);
    } else {
        $("#table_jam_kerja").find('.dataTables_empty').html(`Tidak Ada Data Jam Kerja`);
        for(let i = 0; i < 7; i++) {
            let ke  = i + 1;
            $("#day"+ke).html('Tidak Ada Jadwal');
            $("#day_"+ke+"_mobile").html('Tidak Ada Jadwal');
        }
    }
}

function showModal(idModal, type, data)
{
    const openModal     = $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
    if(idModal == 'modal_jam_kerja')
    {
        openModal;
        // DESKTOP
        showDate('tanggal', 'd_start_desktop', '');
        showDate('tanggal', 'd_end_desktop', '');
        for(let i = 0; i < 7; i++) {
            let ke = i + 1;
            showDate('jam', 't_d'+ke+'_start_desktop');
            showDate('jam', 't_d'+ke+'_end_desktop');
        }
        // MOBILE
        showDate('tanggal', 'd_start_mobile', '');
        showDate('tanggal', 'd_end_mobile', '');
        for(let i = 0; i < 7; i++) {
            let ke = i +1 ;
            showDate('jam', 't_d'+ke+'_start_mobile');
            showDate('jam', 't_d'+ke+'_end_mobile');
        }

        if(type == 'add') {
            $("#d_end_desktop").val('');
            $("#d_end_mobile").val('');

            for(let i = 0; i < 7; i++) {
                let ke  = i + 1;
                $("#t_d"+ke+"_start_desktop").val('');
                $("#t_d"+ke+"_end_desktop").val('');
            }
        }

        $("#btn_form_jam_kerja").val(type);
    }
}

function closeModal(idModal)
{
    const closeModal    = $("#"+idModal).modal('hide');
    if(idModal == 'modal_jam_kerja') {
        closeModal;
    }
}

function doSimpan(idForm, type)
{
    if(idForm == 'form_jam_kerja') {
        // GET DATA FORM
        let dateStart   = $("#d_start_desktop");
        let dateEnd     = $("#d_end_desktop");
        let day1Start   = $("#t_d1_start_desktop");
        let day1End     = $("#t_d1_end_desktop");
        let day2Start   = $("#t_d2_start_desktop");
        let day2End     = $("#t_d2_end_desktop");
        let day3Start   = $("#t_d3_start_desktop");
        let day3End     = $("#t_d3_end_desktop");
        let day4Start   = $("#t_d4_start_desktop");
        let day4End     = $("#t_d4_end_desktop");
        let day5Start   = $("#t_d5_start_desktop");
        let day5End     = $("#t_d5_end_desktop");
        let day6Start   = $("#t_d6_start_desktop");
        let day6End     = $("#t_d6_end_desktop");
        let day7Start   = $("#t_d7_start_desktop");
        let day7End     = $("#t_d7_end_desktop");

        
        let m_day1Start   = $("#t_d1_start_mobile");
        let m_day1End     = $("#t_d1_end_mobile");
        let m_day2Start   = $("#t_d2_start_mobile");
        let m_day2End     = $("#t_d2_end_mobile");
        let m_day3Start   = $("#t_d3_start_mobile");
        let m_day3End     = $("#t_d3_end_mobile");
        let m_day4Start   = $("#t_d4_start_mobile");
        let m_day4End     = $("#t_d4_end_mobile");
        let m_day5Start   = $("#t_d5_start_mobile");
        let m_day5End     = $("#t_d5_end_mobile");
        let m_day6Start   = $("#t_d6_start_mobile");
        let m_day6End     = $("#t_d6_end_mobile");
        let m_day7Start   = $("#t_d7_start_mobile");
        let m_day7End     = $("#t_d7_end_mobile");
        
        if(dateStart.val() == '') {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Tanggal Awal Tidak Boleh Kosong',
            }).then((res)   => {
                if(res.isConfirmed) {
                    dateStart.click();
                }
            })
        } else {
            let dateForm    = {
                "date_start"    : moment(dateStart.val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "date_end"      : dateEnd.val(),
                "day_1_start"   : day1Start.val() == '' ? (m_day1Start.val() == '' ? "00:00:00" : m_day1Start.val()+":00") : day1Start.val()+":00",
                "day_1_end"     : day1End.val() == '' ? (m_day1End.val() == '' ? "00:00:00" : m_day1End.val()+":00") : day1End.val()+":00",
                "day_2_start"   : day2Start.val() == '' ? (m_day2Start.val() == '' ? "00:00:00" : m_day2Start.val()+":00") : day2Start.val()+":00",
                "day_2_end"     : day2End.val() == '' ? (m_day2End.val() == '' ? "00:00:00" : m_day2End.val()+":00") : day2End.val()+":00",
                "day_3_start"   : day3Start.val() == '' ? (m_day3Start.val() == '' ? "00:00:00" : m_day3Start.val()+":00") : day3Start.val()+":00",
                "day_3_end"     : day3End.val() == '' ? (m_day3End.val() == '' ? "00:00:00" : m_day3End.val()+":00") : day3End.val()+":00",
                "day_4_start"   : day4Start.val() == '' ? (m_day4Start.val() == '' ? "00:00:00" : m_day4Start.val()+":00") : day4Start.val()+":00",
                "day_4_end"     : day4End.val() == '' ? (m_day4End.val() == '' ? "00:00:00" : m_day4End.val()+":00") : day4End.val()+":00",
                "day_5_start"   : day5Start.val() == '' ? (m_day5Start.val() == '' ? "00:00:00" : m_day5Start.val()+":00") : day5Start.val()+":00",
                "day_5_end"     : day5End.val() == '' ? (m_day5End.val() == '' ? "00:00:00" : m_day5End.val()+":00") : day5End.val()+":00",
                "day_6_start"   : day6Start.val() == '' ? (m_day6Start.val() == '' ? "00:00:00" : m_day6Start.val()+":00") : day6Start.val()+":00",
                "day_6_end"     : day6End.val() == '' ? (m_day6End.val() == '' ? "00:00:00" : m_day6End.val()+":00") : day6End.val()+":00",
                "day_7_start"   : day7Start.val() == '' ? (m_day7Start.val() == '' ? "00:00:00" : m_day7Start.val()+":00") : day7Start.val()+":00",
                "day_7_end"     : day7End.val() == '' ? (m_day7End.val() == '' ? "00:00:00" : m_day7End.val()+":00") : day7End.val()+":00",
            };

            let jamURL      = "divisi/human_resource/jam_kerja/simpan_jam_kerja/"+type;
            let jamType     = "POST";
            let jamData     = dateForm;
            let jamMsg      = Swal.fire({ title : 'Data Sedang Diproses', allowOutsideClick: false }); Swal.showLoading();

            doTransaction(jamURL, jamType, jamData, jamMsg)
                .then((success)     => {
                    Swal.fire({
                        icon    : 'success',
                        title   : 'Berhasil',
                        text    : success.message
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            closeModal('modal_jam_kerja');

                            // REFRESH TABLE
                            const getJamKerja   = dataURL.find(item => item['title'] == "get_jam_kerja");
                            showTable('table_jam_kerja', [], '');
                            doTransaction(getJamKerja.url, getJamKerja.type, getJamKerja.data, '')
                                .then((success) => {
                                    let dataJamKerja    = success.data;
                                    showTable('table_jam_kerja', dataJamKerja, '');
                                    showCurrentJamKerja(dataJamKerja);
                                    $("#table_jam_kerja").find('.dataTables_empty').html(`Data Berhasil Dimuat`);
                                })
                                .catch((err)    => {
                                    showTable('table_jam_kerja', [], '');
                                    showCurrentJamKerja([]);
                                    $("#table_jam_kerja").find('.dataTables_empty').html(`Data Gagal Dimuat`);
                                })
                        }
                    })
                })
                .catch((error)      => {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Keslahan',
                        text    : error.responseJSON.message,
                    })
                })
        }
    }
}

function doTransaction(url, type, data, message, isAsync = true)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            type    : type,
            headers : {
                'X-CSRF-TOKEN' : CSRF_TOKEN,
            },
            data    : data,
            url     : base_url + "/" + url,
            beforeSend  : () => {
                message;
            },
            async   : isAsync,
            success     : (success) => {
                resolve(success)
            },
            error       : (err)     => {
                reject(err)
            }
        })
    })
}