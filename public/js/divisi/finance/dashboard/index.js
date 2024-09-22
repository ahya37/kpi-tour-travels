moment.locale('id');

var today       = moment().format('YYYY-MM-DD');
var isActive    = 0;
var base_url    = window.location.origin;
$(document).ready(() => {
    // dataDashboard();

    const actUser_url   = "/divisi/finance/eventsFinance";
    const actUser_data  = {
        "start_date"    : moment(today, 'YYYY-MM-DD').startOf('year').format('YYYY-MM-DD'),
        "end_date"      : moment(today, 'YYYY-MM-DD').endOf('year').format('YYYY-MM-DD'),
    };

    const financeRKAP_url   = "/divisi/finance/rkap/listRKAP";
    const financeRKAP_data  = {
        "rkap_id"   : '%',
    };

    const gpkEmployee_url   = base_url + "/divisi/finance/master/gaji_pokok_employee";
    const gpkEmployee_data  = "";

    const getDatadDashboard     = [
        doTrans(actUser_url, 'GET', actUser_data, '', true),
        doTrans(financeRKAP_url, 'GET', financeRKAP_data, '', true),
        doTransV2(gpkEmployee_url, 'GET', gpkEmployee_data, '', true)
    ];

    Promise.all(getDatadDashboard)
        .then((success) => {
            const actUser_getData       = success[0].data;
            const financeRKAP_getData   = success[1].data;
            const gpkEmployee_getData   = success[2].total_data;
            
            // HIDE LOADING ACT USER
            $("#act_user_loading").addClass('d-none');
            $("#act_user_text").removeClass('d-none');
            $("#act_user_text").html(actUser_getData.length);

            // HIDE LOADING RKAP
            $("#act_rkap_loading").addClass('d-none');
            $("#act_rkap_text").removeClass('d-none');
            $("#act_rkap_text").html(financeRKAP_getData.length);

            // HIDE ABS LOADING
            $("#abs_loading").addClass('d-none');
            $("#abs_text").removeClass('d-none');
            $("#abs_text").html("<label class='no-margins font-weight-light'>"+moment().format('YYYY-MM-DD')+"</label>");
            
            $("#kar_text").html("<label class='no-margins font-weight-light'>" + gpkEmployee_getData + "</label>");
        })
        .catch((err)    => {
            // HIDE LOADING ACT USER
            $("#act_user_loading").addClass('d-none');
            $("#act_user_text").removeClass('d-none');
            $("#act_user_text").html(0);

            // HIDE LOADING RKAP
            $("#act_rkap_loading").addClass('d-none');
            $("#act_rkap_text").removeClass('d-none');
            $("#act_rkap_text").html(0);

            // HIDE ABS LOADING
            $("#abs_loading").addClass('d-none');
            $("#abs_text").removeClass('d-none');
            $("#abs_text").html("<label class='no-margins font-weight-light'>"+moment().format('YYYY-MM-DD')+"</label>");
        })
});

// function dataDashboard()
// {
//     let current_year    = moment().format('YYYY');
//     let start_date      = current_year+"-01-01";
//     let end_date        = current_year+"-12-31";

//     // GET DATA
//     const url           = "/divisi/finance/eventsFinance";
//     const type          = "GET";
//     const message       = "";
//     const data          = {
//         "start_date"    : start_date,
//         "end_date"      : end_date
//     };
//     $("#act_rkap_loading").removeClass('d-none');
//     $("#act_user_loading").removeClass('d-none');
//     $("#act_rkap_text").addClass('d-none');
//     $("#act_user_text").addClass('d-none');
//     doTrans(url, type, data, message, true)
//         .then((success) => {
//             $("#act_rkap_loading").addClass('d-none');
//             $("#act_user_loading").addClass('d-none');

//             $("#act_rkap_text").html(0);
//             $("#act_user_text").html(success.data.length);
//             $("#act_rkap_text").removeClass('d-none');
//             $("#act_user_text").removeClass('d-none');
//         })
//         .catch((err)    => {
//             $("#act_rkap_loading").addClass('d-none');
//             $("#act_user_loading").addClass('d-none');

//             $("#act_rkap_text").html(0);
//             $("#act_user_text").html(0);
//             $("#act_rkap_text").removeClass('d-none');
//             $("#act_user_text").removeClass('d-none');
//         })
// }

function showCalendar(today)
{
    $("#current_date").val(today);
    $("#calendar").show();
    var idCalendar  = document.getElementById('calendar');
    var calendar    = new FullCalendar.Calendar(idCalendar,{
        themeSystem : 'bootstrap',
        headerToolbar   : {
        left    : 'prevCustomButton nextCustomButton',
            right   : 'todayCustomButton dayGridMonth',
        },
        locale          : 'id',
        eventDisplay    : 'block',
        initialDate     : today,
        navLinks        : false,
        selectable      : true,
        selectMirror    : true,
        editable        : false,
        dayMaxEvents    : true,
        contentHeight   : 600,
        events          : function(fetchInfo, successCallback, failureCallback)
        {
            let start_date  = moment(today).startOf('month').format('YYYY-MM-DD');
            let end_date    = moment(today).endOf('month').format('YYYY-MM-DD');
            let url         = "/divisi/finance/eventsFinance";
            let type        = "GET";
            let message     = Swal.fire({ title : 'Data Sedang Dimuat' }); Swal.showLoading();
            let data        = {
                "start_date"    : start_date,
                "end_date"      : end_date,
            };

            doTrans(url, type, data, message, true)
                .then((success) => {
                    const getData   = success.data;
                    const tempData  = [];
                    for(let i = 0; i < getData.length; i++) {
                        tempData.push({
                            start   : getData[i].pkb_start_date,
                            end     : getData[i].pkb_end_date,
                            id      : getData[i].pkb_uid,
                            allDay  : true,
                            title   : getData[i].pkb_title,
                        });
                    }

                    successCallback(tempData);
                    Swal.close();
                })
                .catch((err)    => {
                    console.log(err);
                    Swal.close();
                })
        },
        moreLinkContent     : (arg) => {
            return '+ '+arg.num+' Lainnya';
        },
        select      : function(arg) {
            showModal('modal_daily_trans', arg, 'add');
        },
        eventClick  : (arg) => {
            showModal('modal_daily_trans', arg.event.id, 'edit');
        },
        customButtons : {
            prevCustomButton    : {
                click : () => {
                    const hari_ini_bulan_lalu   = moment(today, 'YYYY-MM-DD').subtract(1, 'month').format('YYYY-MM-DD');
                    today  = hari_ini_bulan_lalu;
                    // VISUAL UPDATE
                    $("#modal_daily_calendar_title").html("Aktivitas Bulan "+moment(today).format('MMMM')+" Tahun "+moment(today).format('YYYY'));
                    showCalendar(today);
                    $("#current_date").val(today);
                },
            },
            nextCustomButton    : {
                click   : () => {
                    const hari_ini_bulan_depan  = moment(today, 'YYYY-MM-DD').add(1, 'month').format('YYYY-MM-DD');
                    today   = hari_ini_bulan_depan;
                    $("#modal_daily_calendar_title").html("Aktivitas Bulan "+moment(today).format('MMMM')+" Tahun "+moment(today).format('YYYY'));
                    showCalendar(today);
                    $("#current_date").val(today);
                }
            },
            todayCustomButton   : {
                click   : () => {
                    const hari_ini  = moment().format('YYYY-MM-DD');
                    today       = hari_ini;
                    $("#modal_daily_calendar_title").html("Aktivitas Bulan "+moment(today).format('MMMM')+" Tahun "+moment(today).format('YYYY'));
                    showCalendar(today);
                    $("#current_date").val(today);
                }
            }
        },
    });
    calendar.render();
    $(".fc-nextCustomButton-button").html("<i class='fa fa-chevron-right'></i>").prop('title', 'Bulan Selanjutnya');
    $(".fc-prevCustomButton-button").html("<i class='fa fa-chevron-left'></i>").prop('title', 'Bulan Sebelumnya');
    $(".fc-todayCustomButton-button").html("Today").prop('title','Hari ini');
}

function showSelect(idSelect, data)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
    if(idSelect == 'daily_trans_category')
    {
        const html  = [
            "<option selected disabled>Pilih Kategori</option>",
            "<option value='jpk_finance'>Keuangan</option>",
            "<option value='jpk_operasional'>Operasional</option>"
        ];

        $("#"+idSelect).html(html);
    } else if(idSelect == 'daily_trans_tour_code') {
        var html  = "<option selected disabled>Pilih Tour Code</option>";

        if(data != '') {
            if(data.length > 0) {
                $.each(data, (i, item)  => {
                    html    += "<option value='" + item.tour_code + "'>" + item.tour_code + "</option>";
                });
            }
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'daily_trans_opr_pkb_id') {
        var html    = "<option selected disabled>Pilih Jenis Pekerjaan Operasional</option>";

        if(data != '') {
            $.each(data, (i, item)  => {
                html    += "<option value='" + item.pkb_id + "'>" + item.pkb_title + "</option>";
            });

            $("#"+idSelect).on('change', (i, item)  => {
                const pkb_id    = $("#"+idSelect).val();
                for(const item of data)
                {
                    if(item.pkb_id == pkb_id) {
                        const pkb_title     = item.pkb_title;
                        const pkb_date      = item.pkb_start_date == item.pkb_end_date ? moment(item.pkb_start_date, 'YYYY-MM-DD').format('DD/MM/YYYY') : moment(item.pkb_start_date, 'YYYY-MM-DD').format('DD/Mm/YYYY')+" s/d "+moment(item.pkb_end_date, 'YYYY-MM-DD').format('DD/Mm/YYYY');

                        $("#daily_trans_opr_pkb_title").val(pkb_title);
                        $("#daily_trans_opr_pkb_date").val(pkb_date);
                    }
                }
            })
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    } else if(idSelect == 'sml_emp_id') {
        var html    = "<option selected disabled>Pilih Karyawan</option>";

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += "<option value='" + item.emp_id + "'>" + item.emp_name + "</option>";
            });
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    }
}

function showTable(idTable, data)
{
    if(idTable == 'table_rkap_finance') {
        $("#"+idTable+"_wrapper").prop('style', 'width: 100%;');
        $("#"+idTable).DataTable().clear().destroy();

        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat...",
                "zeroRecords"   : "Tidak Ada Data Yang Dicari" 
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "7%" },
                { "targets" : [1], "className" : "text-left align-middle" },
                { "targets" : [2, 3], "className" : "text-center align-middle", "width" : "10%" },
            ],
        });

        if(data.length > 0) {
            let num = 1;
            for(const item of data)
            {
                const rkap_id       = item.pkt_id;
                const rkap_title    = item.pkt_title;
                const rkap_year     = item.pkt_year;
                const rkap_total    = item.pkt_total_detail;
                const rkap_button   = "<button type='button' class='btn btn-sm btn-primary' id='Ubah Data' value='" + rkap_id + "' onclick='showModal(`modal_create_rkap`, this.value, `edit`)'><i class='fa fa-eye'></i></button>";
                $("#"+idTable).DataTable().row.add([
                    "<label class='no-margins' style='font-weight: normal;'>" + (num++) + "</label>",
                    "<label class='no-margins' style='font-weight: normal;'>" + rkap_title + "</label>",
                    "<label class='no-margins' style='font-weight: normal;'>" + rkap_year + "</label>",
                    "<label class='no-margins' style='font-weight: normal;'>" + rkap_button + "</label>",
                ]).draw(false);
            }
        } else {
            $(".dataTables_empty").html("Tidak Ada Data Yang Bisa Dimuat");
        }
    } else if(idTable == 'table_create_rkap') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "Tidak Ada Data Yang Bisa Dimuat",
            },
            bInfo       : false,
            searching   : false,
            paging      : false,
            pageLength  : -1,
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "text-center", "width" : "15%" },
            ],
        })
    } else if(idTable == 'table_update_gapok_karyawan') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan..",
            },
            pageLength  : -1,
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "width" : "8%", "className" : "text-center align-middle" },
                { "targets" : [1], "className" : "text-left align-middle" },
                { "targets" : [2], "width" : "25%", "className" : "text-left align-middle" },
                { "targets" : [3], "width" : "25%", "className" : "text-center align-middle" },
                { "targets" : [4], "width" : "8%", "className" : "text-center align-middle" },
            ],
        });

        // GET DATA
        const gpk_url   = base_url + "/divisi/finance/master/gaji_pokok_employee";
        const gpk_data  = "";
        const gpk_type  = "GET";
        const gpk_msg   = "";

        doTransV2(gpk_url, gpk_type, gpk_data, gpk_msg, true)
            .then((success)     => {
                const gpk_getData   = success.data;
                let i = 1;
                for(const gpk_item of gpk_getData)
                {
                    const gpk_item_seq  = i++;
                    const gpk_item_id   = gpk_item['emp_id'];
                    const gpk_item_btn  = "<button class='btn btn-sm btn-primary' title='ubah data' value='" + gpk_item_id + "' onclick='doUpdate(`gapok_karyawan`, this.value, `"+ gpk_item_seq +"`)'><i class='fa fa-edit'></i></button>";
                    $("#"+idTable).DataTable().row.add([
                        gpk_item_seq,
                        gpk_item['emp_name'],
                        gpk_item['emp_division'],
                        "<input type='text' class='form-control form-control-sm text-right' id='gpk_value"+gpk_item_seq+"' name='gpk_value" + gpk_item_seq + "' value='" + gpk_item['emp_fee'] + "' onclick='this.select();'>",
                        gpk_item_btn,
                    ]).draw(false)
                }
            })
            .catch((err)        => {
                console.log(err);
                $(".dataTables_empty").html("Tidak Ada Data Yang Bisa Dimuat");
            })
    } else if(idTable == 'table_emp_ovt') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "Pilih Karyawan dan Tanggal Lalu Klik 'Cari' untuk menampilkan data..",
                zeroRecords : "Tidak ada data yang bisa ditampilkan"
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : 'text-left align-middle', "width" : "15%" },
                { "targets" : [2, 3, 4], "className" : "text-center align-middle", "width" : "20%"},
                { "targets" : [5], "className" : "text-center align-middle", "width" : "8%" }
            ],
        });

        if(data != '') {
            let seq                 = 1;
            let overtimeOne         = 0;
            let overtimeTwo         = 0;
            let overtimeThree       = 0;
            let totalOvertimeOne    = 0;
            let totalOvertimeTwo    = 0;
            let totalOvertimeThree  = 0;

            let amountOverTime      = parseFloat($("#sml_emp_fee_ovt_input").val());
            let amountOverTimeOne   = 0;
            let amountOverTimeTwo   = 0;
            let amountOverTimeThree = 0;
            let amountTotalOverTime = 0;

            for(const item of data)
            {
                var prs_date    = item.emp_prs_date;
                var prs_in      = item.emp_prs_in_time;
                var prs_out     = item.emp_prs_out_time;

                // SHOW TIME ONLY
                var prs_in_time = moment(prs_in, 'YYYY-MM-DD HH:mm:ss').format('HH:mm');
                var prs_out_time= moment(prs_out, 'YYYY-MM-DD HH:mm:ss').format('HH:mm');
                                
                // FORMATED TANGGAL
                var prs_date_formatted  = prs_out != null ? moment(prs_date, 'YYYY-MM-DD').format('DD/MM/YYYY')+" ("+moment(prs_in, 'YYYY-MM-DD HH:mm:ss').format('HH:mm')+" - "+moment(prs_out, 'YYYY-MM-DD HH:mm:ss').format('HH:mm')+")" : moment(prs_date, 'YYYY-MM-DD').format('DD/MM/YYYY')+" ("+moment(prs_in, 'YYYY-MM-DD HH:mm:ss').format('HH:mm')+")";

                if(prs_out != null && prs_out_time > "16:59") {
                    prs_out_time >= "17:00" ? overtimeOne = 1: "";
                    prs_out_time >= "17:59" ? overtimeTwo = 1: "";
                    prs_out_time >= "18:59" && prs_out_time <= "23:59" ? overtimeThree = 1 : "";
                    
                    // FOR TOTAL
                    prs_out_time >= "17:00" ? totalOvertimeOne++: "";
                    prs_out_time >= "17:59" ? totalOvertimeTwo++: "";
                    prs_out_time >= "18:59" && prs_out_time <= "23:59" ? totalOvertimeThree++ : "";
                    
                    $("#"+idTable).DataTable().row.add([
                        seq++,
                        prs_date_formatted,
                        overtimeOne,
                        overtimeTwo,
                        overtimeThree,
                        "<i class='fa fa-times'></fa>"
                    ]).draw(false);
                }

                overtimeOne = 0;
                overtimeTwo = 0;
                overtimeThree = 0;
            }

            $("#table_emp_ovt_total_ot1").html(totalOvertimeOne);
            $("#table_emp_ovt_total_ot2").html(totalOvertimeTwo);
            $("#table_emp_ovt_total_ot3").html(totalOvertimeThree);

            amountOverTimeOne   = amountOverTime * totalOvertimeOne;
            amountOverTimeTwo   = amountOverTime * totalOvertimeTwo;
            amountOverTimeThree = amountOverTime * totalOvertimeThree;

            amountTotalOverTime = amountOverTimeOne + amountOverTimeTwo + amountOverTimeThree;
            $("#sml_emp_fee_ovt").html(new Intl.NumberFormat('id-ID', {style : 'currency', currency: 'IDR'}).format(amountTotalOverTime));

            $("#sml_emp_ot1").html(totalOvertimeOne);
            $("#sml_emp_ot2").html(totalOvertimeTwo);
            $("#sml_emp_ot3").html(totalOvertimeThree);
        }
    }
}

function tambahBaris(idTable, data)
{
    if(idTable == 'table_create_rkap')
    {
        const seq           = parseInt($("#btnHapusBaris").val());
        const input_no      = "<input type='text' class='form-control text-center' id='rkapd_seq"+seq+"' readonly autocomplete='off'>";
        const input_btn     = "<button type='button' class='btn btn-sm btn-danger' value='" + seq + "' onclick='hapusBaris(`table_create_rkap`, "+seq+")'><i class='fa fa-trash'></i></button>";
        const input_title   = "<input type='text' class='form-control text-left' id='rkapd_title"+seq+"' placeholder='Uraian' autocomplete='off'>";

        $("#"+idTable).DataTable().row.add([
            input_btn,
            input_no,
            input_title,
        ]).draw(false);

        if(data != '') {
            const rkapd_seq     = data.pktd_seq;
            const rkapd_title   = data.pktd_title;

            $("#rkapd_seq"+seq).val(rkapd_seq);
            $("#rkapd_title"+seq).val(rkapd_title);
        } else {
            $("#rkapd_seq"+seq).val(seq);
            $("#rkapd_title"+seq).val('');

            if(seq != 1) {
                $("#rkapd_title"+seq).focus();
            }
        }

        $("#rkapd_title"+seq).on('keyup', (e) => {
            if(e.key === 'Enter') {
                tambahBaris('table_create_rkap', '');
            }
        });

        $("#btnHapusBaris").val(seq + 1);
    }
}

function hapusBaris(idTable, seq)
{
    if(idTable == 'table_create_rkap') {
        const current_seq   = parseInt($("#btnHapusBaris").val());
        const hitung        = current_seq - seq;

        if(seq == 1) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Baris Pertama Tidak Bisa Dimuat',
            });
        } else {
            if(hitung === 1) {
                $("#"+idTable).DataTable().row(seq - 1).remove().draw(false);
                $("#btnHapusBaris").val(current_seq - 1);
                $("#rkapd_title"+(seq - 1)).focus();
            } else {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Hanya Baris Terakhir Yang Bisa Dihapus', 
                });
            }
        }
    }
}

function showModal(idModal, value, jenis)
{
    if(idModal == 'modal_rkap_finance') {
        const rkapFin_url   = "/divisi/finance/rkap/listRKAP";
        const rkapFin_data  = {
            "rkap_id"    : "%",
        };
        const rkapFin_type  = "GET";

        const sendData      = [
            doTrans(rkapFin_url, rkapFin_type, rkapFin_data, '', true),
        ];

        // SHOW SWAL
        Swal.fire({
            title   : 'Data Sedang Dimuat',
        });
        Swal.showLoading();

        Promise.all(sendData)
            .then((success) => {
                Swal.close();
                const rkapFin_getData   = success[0].data;
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                // SHOW TABLE
                showTable('table_rkap_finance', rkapFin_getData);
                $("#modal_rkap_title").html("RKAP Finance Tahun"+moment().format('YYYY'));
            })
            .catch((err)    => {
                Swal.fire({
                    icon    : 'warning',
                    title   : "Terjadi Kesalahan",
                    text    : "Tidak Ada Data Yang Bisa Dimuat",
                }).then((res)   => {
                    if(res.isConfirmed) {
                        console.log(err);
                        Swal.close();
                        $("#"+idModal).modal({backdrop: 'static', keyboard: false});
                        showTable('table_rkap_finance', []);
                        $("#modal_rkap_title").html("RKAP Finance Tahun"+moment().format('YYYY'));
                    }
                })
            })
    } else if(idModal == 'modal_create_rkap') {
        closeModal('modal_rkap_finance');
        
        $("#rkap_year").yearpicker({
            autoHide: true,
            year    : parseInt(moment().format('YYYY')),
        });

        $("#btnSimpanRKAP").val(jenis);

        if(jenis == 'add') {
            // ADD TITLE
            $("#modal_create_rkap_title").html("Tambah Data RKAP");

            // FILL FORM
            $("#rkap_year").val(moment().format('YYYY')).trigger('change');

            // FOCUS
            $("#"+idModal).on('shown.bs.modal', () => {
                $("#rkap_title").focus();
            });

            // SHOW TABLE DETAIL
            showTable('table_create_rkap', '');
            tambahBaris('table_create_rkap', '');
        } else if(jenis == 'edit') {
            // GET DATA RKAP DETAIL
            const rkap_url      = "/divisi/finance/rkap/getRKAPData";
            const rkap_sendData = {
                "rkap_id"   : value,
            };
            const rkap_type     = "GET";
            const rkap_message  = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();
            
            doTrans(rkap_url, rkap_type, rkap_sendData, rkap_message, true)
                .then((success) => {
                    // GET DATA
                    const header    = success.data.header;
                    const detail    = success.data.detail;

                    // FILL FORM HEADER
                    $("#rkap_id").val(header.pkt_id);
                    $("#rkap_title").val(header.pkt_title);
                    $("#rkap_description").val(header.pkt_description);
                    $("#rkap_year").val(header.pkt_year).trigger('change');

                    // FILL TABLE
                    showTable('table_create_rkap', '');
                    for(const item of detail) {
                        tambahBaris('table_create_rkap', item);
                    }
                    tambahBaris('table_create_rkap', '');
                    // SHOW MODAL 
                    $("#modal_create_rkap_title").html("Ubah Data RKAP");
                    $("#"+idModal).modal({backdrop : 'static', keyboard: false});

                     // FOCUS
                    $("#"+idModal).on('shown.bs.modal', () => {
                        $("#rkap_title").focus();
                    });

                    Swal.close();
                    
                })
                .catch((err)    => {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : 'Data Yang Dipilih Tidak Ditemukan',
                    }).then((results)  => {
                        if(results.isConfirmed) {
                            console.log(err);
                        }
                    });
                })
        }
    } else if(idModal == 'modal_daily_activity') {
        $("#"+idModal).modal({ backdrop : 'static', keyboard : false });
        $("#modal_daily_title").html("List Aktivitas");
        $("#modal_daily_calendar_title").html("Aktivitas Bulan "+moment().format('MMMM')+" Tahun "+moment().format('YYYY'));
        $("#"+idModal).on('shown.bs.modal', function(){
            if(isActive == 0) {
                $("#calendar-loading").addClass('d-none');
                $("#calendar-show").removeClass('d-none');
                showCalendar(today);
            }
            isActive = 1;
        });
    } else if(idModal == 'modal_daily_trans') {
        $("#btnSimpan").val(jenis);

        $("#daily_trans_start_date").daterangepicker({
            singleDatePicker : true,
            locale : {
                format  : 'DD/MM/YYYY',
            },
            minYear     : moment().subtract(10, 'years'),
            maxYear     : moment().add(10, 'years'),
            autoApply    : true,
            showDropdowns: true,
        });

        showSelect('daily_trans_category', '');
        showSelect('daily_trans_tour_code', '');
        showSelect('daily_trans_opr_pkb_id', '');

        if(jenis == 'add') {
            $("#"+idModal).modal({ backdrop : 'static', keyboard : false });
            $("#modal_daily_trans_title").html("Tambah Data Aktivitas Harian");
            $("#daily_trans_start_date").data('daterangepicker').setStartDate(moment(value.startStr, 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#daily_trans_start_date").data('daterangepicker').setEndDate(moment(value.startStr, 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#btnHapus").hide();
        } else if(jenis == 'edit') {
            // GET DATA
            Swal.fire({ title : 'Data Sedang Dimuat' }); Swal.showLoading();
            const getDataDetail     = [
                doTrans('/divisi/finance/getEventsFinanceDetail/'+value, 'GET', '', '', true)
            ];

            Promise.all(getDataDetail)
                .then((success) => {
                    const dataDetail    = success[0].data;
                    // SHOW MODAL
                    $("#"+idModal).modal({ backdrop : 'static', keyboard : false });
                    $("#modal_daily_trans_title").html("Ubah Data Aktivitas Harian");
                    $("#btnHapus").show();

                    // FILL FORM
                    $("#daily_trans_jenis").val(value);
                    $("#daily_trans_title").val(dataDetail.pkb_title);
                    $("#daily_trans_start_date").data('daterangepicker').setStartDate(moment(dataDetail.pkb_start_date, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                    $("#daily_trans_start_date").data('daterangepicker').setEndDate(moment(dataDetail.pkb_start_date, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                    $("#daily_trans_description").val(dataDetail.pkb_description);
                    $("#daily_trans_pkb_id").val(dataDetail.ref_id);

                    // UNTUK OPREASIONAL
                    $("#daily_trans_category").val(dataDetail.jpk_status).trigger('change');
                    $("#daily_trans_category").prop('disabled', true);
                    if(dataDetail.jpk_status == 'jpk_operasional') {
                        $("#v_opr_pkb_id").addClass('d-none');
                        $("#v_trans_code_select").addClass('d-none');

                        $("#v_trans_code_text").removeClass('d-none');

                        $("#daily_trans_tour_code_text").val(dataDetail.jdw_tour_code);
                        $("#daily_trans_opr_pkb_title").val(dataDetail.ref_title);
                        const tanggal   = dataDetail.ref_date_start == dataDetail.ref_date_end ? moment(dataDetail.ref_date_start, 'YYYY-MM-DD').format('DD/MM/YYYY') : moment(dataDetail.ref_date_start, 'YYYY-MM-DD').format('DD/MM/YYYY')+" s/d "+moment(dataDetail.ref_date_end, 'YYYY-MM-DD').format('DD/MM/YYYY');
                        $("#daily_trans_opr_pkb_date").val(tanggal);
                    }

                    Swal.close();
                })
                .catch((err)    => {
                    Swal.close();
                    console.log(err);
                })
        }

        $("#daily_trans_category").on('change', () => {
            const transCategory    = $("#daily_trans_category").val();
            if(transCategory == 'jpk_operasional') {
                $("#v_aktivitas_operasional").removeClass('d-none');
                const getData   = [
                    doTrans('/divisi/finance/getTourCode/semua', 'GET', '', '', true)
                ];
                Promise.all(getData)
                    .then((success) => {
                        const header    = success[0].data.header;
                        const detail    = success[0].data.detail;
                        showSelect('daily_trans_tour_code', header);
                        $("#daily_trans_tour_code").on('change', () => {
                            const tourCode     = $("#daily_trans_tour_code").val();
                            const tempDetail    = [];

                            for(const item of detail) {
                                if(item.tour_code == tourCode) {
                                    tempDetail.push(item);
                                }
                            }

                            showSelect('daily_trans_opr_pkb_id', tempDetail);
                        })
                    })
                    .catch((err)    => {
                        console.log(err);
                    })
            } else {
                $("#v_aktivitas_operasional").addClass('d-none');
                showSelect('daily_trans_tour_code', '');
                showSelect('daily_trans_opr_pkb_id', '');

                $("#daily_trans_opr_pkb_title").val(null);
                $("#daily_trans_opr_pkb_date").val(null);
            }
        });
    } else if(idModal == 'modal_absensi') {
        $("#"+idModal).modal({backdrop: 'static', keyboard: false});

        // SHOW DATERANGEPICKER
        $("#abs_tgl_cari").daterangepicker({
            minDate     : moment(today, 'YYYY-MM-DD').subtract(1, 'year'),
            maxDate     : moment(today, 'YYYY-MM-DD').add(1, 'year'),
            autoApply   : false,
            format      : 'DD/MM/YYYY',
            setStartDate    : moment(today, 'YYYY-MM-DD'),
            locale  : {
                separator   : ' s/d ',
                cancelLabel : 'Batal',
                applyLabel  : 'Simpan',
            },
        })
    } else if(idModal == 'modal_update_gapok_karyawan') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
        
        showTable('table_update_gapok_karyawan', '');
    } else if(idModal == 'modal_simulasi_lemburan') {

        // GET DATA EMPLOYEES
        const sml_emp_url   = base_url + "/divisi/human_resource/employee/list";
        const sml_emp_type  = "GET";
        const sml_emp_data  = { "cari" : '%' };
        const sml_emp_msg   = Swal.fire({ title : 'Data Sedang Diambil' }); Swal.showLoading();
        
        doTransV2(sml_emp_url, sml_emp_type, sml_emp_data, sml_emp_msg, true)
            .then((success)     => {
                Swal.close();
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                const sml_emp_getData   = success.data;
                // SHOW SELECT
                showSelect('sml_emp_id', sml_emp_getData);
                // SHOW DATERANGEPICKER
                $("#sml_emp_date").daterangepicker({
                    locale  : {
                        separator   : ' s/d ',
                        format      : 'DD/MM/YYYY'
                    },
                    minYear     : moment().subtract(10, 'years'),
                    maxYear     : moment().add(10, 'years'),
                    autoApply    : true,
                    showDropdowns: true,
                });
                // SHOW TABLE
                showTable('table_emp_ovt', '');
            })
            .catch((err)        => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Data Tidak Ditemukan'
                })
            })
    }
}

function closeModal(idModal) {
    if(idModal == 'modal_rkap_finance') {
        $("#"+idModal).modal('hide');
        var url     = window.location.href;
        var cleanUrl= url.split('#')[0];
        window.history.replaceState({}, document.title, cleanUrl);
    } else if(idModal == 'modal_daily_activity') {
        $("#"+idModal).modal('hide');
        var url     = window.location.href;
        var cleanUrl= url.split('#')[0];
        window.history.replaceState({}, document.title, cleanUrl);
        isActive = 0;
        $("#"+idModal).on('hidden.bs.modal', () => {
            today   = moment().format('YYYY-MM-DD');

            $("#calendar").hide();
            $(".fc-prevCustomButton-button").empty();
            $(".fc-nextCustomButton-button").empty();
            $(".fc-todayCustomButton-button").empty();

            
            $("#calendar-loading").removeClass('d-none');
            $("#calendar-show").addClass('d-none');
        });
    } else if(idModal == 'modal_daily_trans') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#modal_daily_trans_title").html(null);
            $("#btnSimpan").val(null);
            $("#btnHapus").show();

            $("#daily_trans_opr_pkb_title").val(null);
            $("#daily_trans_opr_pkb_date").val(null);
            $("#daily_trans_title").val(null);
            $("#daily_trans_description").val(null);
            $("#daily_trans_start_date").val(moment().format('DD/MM/YYYY'));

            $("#v_trans_code_text").addClass('d-none');
            $("#v_trans_code_select").removeClass('d-none');
            $("#v_opr_pkb_id").removeClass('d-none');
            $("#daily_trans_tour_code_text").val(null);

            $("#daily_trans_jenis").val(null);

            $("#v_aktivitas_operasional").addClass('d-none');
            $("#daily_trans_category").prop('disabled', false);
        })
    } else if(idModal == 'modal_create_rkap') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#rkap_id").val(null);
            $("#rkap_title").val(null);
            $("#rkap_year").val(null);
            $("#rkap_description").val(null);
            $("#btnHapusBaris").val(1);
        });

        showModal('modal_rkap_finance','','');
    } else if(idModal == 'modal_absensi') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            clearUrl();
            $("#abs_tgl_cari").data('daterangepicker').setStartDate(moment().format('DD/MM/YYYY'));
            $("#abs_tgl_cari").data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
        })
    } else if(idModal == 'modal_update_gapok_karyawan') {
        $("#"+idModal).modal('hide');

        clearUrl();
    } else if(idModal == 'modal_simulasi_lemburan') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#sml_emp_name").val(null);
            $("#sml_emp_division").val(null);

            $("#sml_emp_fee").html("Rp. 0,00");
            $("#sml_emp_fee_ovt").html("Rp. 0,00");
            $("#sml_emp_ot1").html("0");
            $("#sml_emp_ot2").html("0");
            $("#sml_emp_ot3").html("0");

            $("#sml_emp_date").data('daterangepicker').setStartDate(moment().format('DD/MM/YYYY'));
            $("#sml_emp_date").data('daterangepicker').setEndDate(moment().format('DD/MM/YYYY'));
        })

        clearUrl();
    }
}

function doSimpan(jenis)
{
    if(jenis == 'add')
    {
        const fin_ID            = $("#daily_trans_jenis");
        const fin_category      = $("#daily_trans_category");
        const fin_title         = $("#daily_trans_title");
        const fin_date          = $("#daily_trans_start_date");
        const fin_description   = $("#daily_trans_description");
        const opr_tour_code     = $("#daily_trans_tour_code");
        const opr_pkb_id        = $("#daily_trans_opr_pkb_id");

        if(fin_category.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Kategori Harus Dipilih',
            }).then((res)   => {
                if(res.isConfirmed) {
                    fin_category.select2('open');
                }
            })
        } else if(fin_title.val() == '') {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Uraian Aktitivas Harian Tidak Boleh Kosong',
            }).then((res)   => {
                if(res.isConfirmed) {
                    fin_title.focus();
                }
            })
        } else if(fin_date.val() == '') {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Tanggal Aktivitas Tidak Boleh Kosong',
            }).then((res)   => {
                if(res.isConfirmed) {
                    fin_date.focus();
                }
            })
        } else if(fin_category.val() == 'jpk_operasional' && opr_tour_code.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Tour Code Harus Dipilih',
            }).then((res)   => {
                if(res.isConfirmed) {
                    opr_tour_code.select2('open');
                }
            })
        } else if(fin_category.val() == 'jpk_operasional' && opr_pkb_id.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Jenis Pekerjaan Operasiona Harus Dipilih',
            }).then((res)   => {
                if(res.isConfirmed) {
                    opr_pkb_id.select2('open')
                }
            })
        } else {
            const sendData  = {
                "fin_ID"            : $("#daily_trans_jenis").val(),
                "fin_category"      : $("#daily_trans_category").val(),
                "fin_title"         : $("#daily_trans_title").val(),
                "fin_date"          : moment($("#daily_trans_start_date").val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "fin_description"   : $("#daily_trans_description").val(),
                "opr_tour_code"     : $("#daily_trans_tour_code").val(),
                "opr_pkb_id"        : $("#daily_trans_opr_pkb_id").val(),
            };

            const url       = "/divisi/finance/simpanAktivitas/"+jenis;
            const message   = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
            const data      = sendData;
            const type      = "POST";

            doTrans(url, type, data, message, true)
                .then((success) => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text,
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            closeModal('modal_daily_trans');
                            showCalendar($("#current_date").val());
                        }
                    })
                })
                .catch((err)    => {
                    console.log(err);
                    Swal.close();
                })
        }
    } else if(jenis == 'edit') {
        const fin_ID            = $("#daily_trans_jenis");
        const fin_category      = $("#daily_trans_category");
        const fin_title         = $("#daily_trans_title");
        const fin_date          = $("#daily_trans_start_date");
        const fin_description   = $("#daily_trans_description");
        const opr_tour_code     = $("#daily_trans_tour_code");
        const opr_pkb_id        = $("#daily_trans_opr_pkb_id");

        if(fin_title.val() == '') {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Uraian Aktitivas Harian Tidak Boleh Kosong',
            }).then((res)   => {
                if(res.isConfirmed) {
                    fin_title.focus();
                }
            })
        } else if(fin_date.val() == '') {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Tanggal Aktivitas Tidak Boleh Kosong',
            }).then((res)   => {
                if(res.isConfirmed) {
                    fin_date.focus();
                }
            })
        } else {
            const sendData  = {
                "fin_ID"            : $("#daily_trans_jenis").val(),
                "fin_category"      : $("#daily_trans_category").val(),
                "fin_title"         : $("#daily_trans_title").val(),
                "fin_date"          : moment($("#daily_trans_start_date").val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "fin_description"   : $("#daily_trans_description").val(),
                "opr_tour_code"     : $("#daily_trans_tour_code").val(),
                "opr_pkb_id"        : $("#daily_trans_opr_pkb_id").val(),
            };

            const url       = "/divisi/finance/simpanAktivitas/"+jenis;
            const message   = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
            const data      = sendData;
            const type      = "POST";

            doTrans(url, type, data, message, true)
                .then((success) => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text,
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            closeModal('modal_daily_trans');
                            showCalendar($("#current_date").val());
                        }
                    })
                })
                .catch((err)    => {
                    console.log(err);
                    Swal.close();
                })
        }
    } else if(jenis == 'hapus') {
        const fin_ID    = $("#daily_trans_jenis");
        const pkb_ID    = $("#daily_trans_pkb_id");

        Swal.fire({
            icon    : 'question',
            title   : 'Hapus Data Ini?',
            text    : 'Data yang telah dihapus tidak akan muncul kembali di kalendar, apakah anda yakin?',
            showConfirmButton   : true,
            confirmButtonText   : 'Ya, Hapus',
            confirmButtonColor  : '#dc3545',
            showCancelButton    : true,
            cancelButtonText    : 'Batal',
        }).then((results)   => {
            if(results.isConfirmed) {
                // TRANS HAPUS
                const sendData   = {
                    "fin_ID"    : fin_ID.val(),
                    "pkb_ID"    : pkb_ID.val(),
                };

                const url       = "/divisi/finance/simpanAktivitas/"+jenis;
                const message   = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
                const data      = sendData;
                const type      = "POST";

                doTrans(url, type, data, message, true)
                    .then((success) => {
                        Swal.fire({
                            icon    : success.alert.icon,
                            title   : success.alert.message.title,
                            text    : success.alert.message.text
                        }).then((res)   => {
                            if(res.isConfirmed) {
                                closeModal('modal_daily_trans');
                                showCalendar($("#current_date").val());
                            }
                        })
                    })
                    .catch((err)    => {
                        console.log(err);
                        Swal.close();
                    })
            }
        })
    }
}

function doSimpanRKAP(jenis)
{
    const rkap_ID       = $("#rkap_id");
    const rkap_title    = $("#rkap_title");
    const rkap_desc     = $("#rkap_description");
    const rkap_year     = $("#rkap_year");
    const rkap_detail   = [];
    for(let i = 0; i < $("#table_create_rkap").DataTable().rows().count(); i++) {
        const seq   = i + 1;
        rkap_detail.push({
            "rkapd_seq"     : $("#rkapd_seq"+seq).val(),
            "rkapd_title"   : $("#rkapd_title"+seq).val(),
        });
    }

    if(rkap_title.val() == '')
    {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Uraian Harus Diisi',
            didClose: () => {
                rkap_title.focus();
            }
        })
    } else {
        const rkap_sendData = {
            "rkap_id"       : rkap_ID.val(),
            "rkap_title"    : rkap_title.val(),
            "rkap_desc"     : rkap_desc.val(),
            "rkap_year"     : rkap_year.val(),
            "rkap_detail"   : rkap_detail,
        };
        const rkap_url      = "/divisi/finance/rkap/simpanRKAP/"+jenis;
        const rkap_type     = "POST";
        const rkap_message  = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
        
        // SIMPAN MENGGUNAKAN FETCH
        doTrans(rkap_url, rkap_type, rkap_sendData, rkap_message, true)
            .then((success) => {
                Swal.fire({
                    icon    : success.alert.icon,
                    title   : success.alert.message.title,
                    text    : success.alert.message.text,
                }).then((results)   => {
                    if(results.isConfirmed) {
                        closeModal('modal_create_rkap')
                    }
                });
            })
            .catch((err)    => {
                Swal.fire({
                    icon    : err.responseJSON.alert.icon,
                    title   : err.responseJSON.alert.message.title,
                    text    : err.responseJSON.alert.message.text,
                }).then((results)   => {
                    if(results.isConfirmed) {
                        console.log(err.responseJSON.errMsg);
                    }
                })
            })
    }
}

// UNTUK KEBUTUHAN UPDATE GAPOK KARYAWAN
function doUpdate(idForm, data, seq)
{
    if(idForm == 'gapok_karyawan')
    {
        const gpk_sendData  = {
            "emp_fee"   : $("#gpk_value"+seq).val(),
        };
        const gpk_url       = base_url + "/divisi/finance/master/gaji_pokok_employee/"+data;
        const gpk_type      = "PUT";
        const gpk_msg       = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();

        doTransV2(gpk_url, gpk_type, gpk_sendData, gpk_msg, true)
            .then((success)     => {
                Swal.fire({
                    icon    : success.alert.icon,
                    title   : success.alert.message.title,
                    text    : success.alert.message.text,
                }).then((res)   => {
                    if(res.isConfirmed) {
                        showTable('table_update_gapok_karyawan');
                    }
                })
            })
            .catch((err)        => {
                Swal.fire({
                    icon    : err.responseJSON.alert.icon,
                    title   : err.responseJSON.alert.message.title,
                    text    : err.responseJSON.alert.message.text,
                });
            })
    }
}

function doCari(jenis)
{
    if(jenis == 'modal_simulasi_lemburan') {
        const sml_lmb_emp_id    = $("#sml_emp_id").val();
        const sml_lmb_date      = $("#sml_emp_date").val();
        const sml_lmb_date_start= moment(sml_lmb_date.split(' s/d ')[0], 'DD/MM/YYYY').format('YYYY-MM-DD');
        const sml_lmb_date_end  = moment(sml_lmb_date.split(' s/d ')[1], 'DD/MM/YYYY').format('YYYY-MM-DD');

        if(sml_lmb_emp_id == null) {
            $("#sml_emp_id").select2('focus');
        } else {
            // GET DATA 
            const sml_lmb_url   = base_url + "/divisi/finance/simulasi/employees_fee";
            const sml_lmb_type  = "GET";
            const sml_lmb_data  = {
                "emp_id"    : sml_lmb_emp_id,
                "date_start": sml_lmb_date_start,
                "date_end"  : sml_lmb_date_end,
            };
            const sml_lmb_msg   = Swal.fire({ title : "Data Sedang Dicari" }); Swal.showLoading();

            doTransV2(sml_lmb_url, sml_lmb_type, sml_lmb_data, sml_lmb_msg, true)
                .then((success)     => {
                    setTimeout(Swal.close(), 1000);
                    // HEADER
                    const header    = success.data.header[0];
                    let pendapataPerJam     = header['emp_fee'] / 173;
                    $("#sml_emp_name").val(header['emp_name']);
                    $("#sml_emp_division").val(header['emp_division']);
                    $("#sml_emp_fee").html(new Intl.NumberFormat('id-ID', { style: 'currency', currency:'IDR' }).format(header['emp_fee']));
                    $("#sml_emp_fee_hourly").html(new Intl.NumberFormat('id-ID', { style : 'currency', currency: 'IDR' }).format(pendapataPerJam));
                    $("#sml_emp_fee_ovt_input").val(parseFloat(pendapataPerJam).toFixed(2));

                    // DETAIL
                    const detail    = success.data.detail;
                    showTable('table_emp_ovt', detail);
                })
                .catch((err)       => {
                    console.log(err);
                })
        }
    }
}

function downloadAbsen()
{
    const selected_tgl  = $("#abs_tgl_cari").val();
    const tgl_awal      = selected_tgl.split(' s/d ')[0];
    const tgl_akhir     = selected_tgl.split(' s/d ')[1];


    const abs_url   = "/divisi/human_resource/absensi/excelDownload";
    const abs_type  = "GET";
    const abs_data  = {
        "tanggal_awal"  : moment(tgl_awal, 'DD/MM/YYYY').format('YYYY-MM-DD'),
        "tanggal_akhir" : moment(tgl_akhir, 'DD/MM/YYYY').format('YYYY-MM-DD'),
        "user_id"       : "%",
        "jml_hari"      : moment(tgl_akhir, 'DD/MM/YYYY').diff(moment(tgl_awal, 'DD/MM/YYYY'), 'days')+1,
    };
    const abs_msg   = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
    
    doTransV2(abs_url, abs_type, abs_data, abs_msg, true)
        .then((success) => {
            var link = document.createElement('a');
            link.href = base_url+"/"+success.data.file_url+"/"+success.data.file_name;
            document.body.appendChild(link);
            link.click();
            Swal.close();
            
            // DELETE FILE
            setTimeout(()=> {
                const abs_del_data  = {
                    "file_url"  : success.data.file_url+"/"+success.data.file_name,
                };
                const abs_del_type  = "POST";
                const abs_del_url   = "/divisi/human_resource/absensi/excelDelete";
                
                doTransV2(abs_del_url, abs_del_type, abs_del_data, "", true)
                    .then((sc)  => {
                        console.log(sc);
                    })
                    .catch((err)    => {
                        console.log(err);
                    })
            }, 1000);
        })
        .catch((err)    => {
            console.log(err);
            Swal.close();
        })
}

function doTrans(url, type, data, message, isAsync)
{
    return new Promise((resolve, reject)   => {
        $.ajax({
            cache   : false,
            dataType: "json",
            isAsync : isAsync,
            url     : url,
            type    : type,
            data    : {
                '_token'    : CSRF_TOKEN,
                'sendData'  : data,
            },
            beforeSend : () => {
                message;
            },
            success     : (success) => {
                resolve(success);
            },
            error       : (error)   => {
                reject(error);
            },
        })
    })
}

function doTransV2(url, type, data, message, isAsync)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            url     : url,
            dataType: 'json',
            async   : isAsync,
            type    : type,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                message;
            },
            success     : (success) => {
                resolve(success);
            },
            error       : (err)     => {
                reject(err);
            }
        })
    })
}

function clearUrl()
{
    var url     = window.location.href;
    var cleanUrl= url.split('#')[0];
    window.history.replaceState({}, document.title, cleanUrl);
}