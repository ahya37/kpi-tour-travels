var base_url    = window.location.origin;
var today       = moment().format('YYYY-MM-DD');
var chartUmrah;
var chartMember;
var chartPie;

const showChart     = (idChart, data)   => {
    var chartOptions = {
        responsive : true,
        scales  : {
            y       : {
                beginAtZero     : true,
                steps           : 10,
            }
        },
        onClick     : (e, element) => {
            if(element.length > 0) {
                if(idChart == 'chart_member') {
                    const dataMember    = {
                        "bulan_ke"      : moment(element[0]._model.label, 'MMMM').format('MM'),
                        "pic"           : $("#g_member_filter_cs").val(),
                    };
                    showModal('modal_member', dataMember);
                } else if(idChart == 'chart_umrah') {
                    const dataUmrah     = {
                        "bulan_ke"      : moment(element[0]._model.label, 'MMMM').format('MM'),
                    };
                    showModal('modal_umrah', dataUmrah);
                }
            }
        },
        onHover     : (e, element) => {
            if(element.length > 0) {
                $("#"+idChart).css('cursor', 'pointer');
            } else {
                $("#"+idChart).css('cursor', 'default');
            }
        }
    };

    var chart       = document
                        .getElementById(idChart)
                        .getContext('2d');
    if(idChart == 'chart_umrah') {
        var chartData   = {
            labels  : moment.months(),
            datasets: [{
                label   : 'Jumlah Pendaftar',
                data    : data,
                backgroundColor     : 'rgba(26, 179, 148, 0.7)',
                borderColor         : '#1AB394',
                borderWidth         : 1
            }],
        };
        
        // RENDER CHART
        if(chartUmrah) {
            chartUmrah.destroy();
        }

        chartUmrah   = new Chart(chart, {
            type    : 'bar',
            data    : chartData,
            options : chartOptions
        });
    } else if(idChart == 'chart_member') {
        var chartData   = {
            labels  : moment.months(),
            datasets: [{
                label   : 'Jumlah Member',
                data    : data,
                backgroundColor     : 'rgba(26, 179, 148, 0.7)',
                borderColor         : '#1AB394',
                borderWidth         : 1
            }],
        };

        if(chartMember) {
            chartMember.destroy()
        }

        chartMember     = new Chart(chart, {
            type    : 'bar',
            data    : chartData,
            options : chartOptions,
        });
    }
}

const showChartPie  = (idChart, data) => {
    if(idChart == 'chart_modal_total_member') {
        $("#modal_total_member_chart_loading").addClass('d-none');
        $("#modal_total_member_chart_view").removeClass('d-none');

        if(data.length > 0) {
            var dataMember   = [];
            data.forEach(item   => {
                if(dataMember.length === 0) {
                    dataMember.push({
                        "pic_name"      : item['PIC_NAME'],
                        "total_data"    : item['TOTAL_DATA'],
                    })
                } else {
                    let found   = false;
                    for(let i = 0; i < dataMember.length; i++) {
                        if(dataMember[i]['pic_name'] === item['PIC_NAME']) {
                            dataMember[i]['total_data'] += item['TOTAL_DATA'];
                            found   = true;
                            break;
                        }
                    }

                    if(!found) {
                        dataMember.push({
                            "pic_name"      : item['PIC_NAME'],
                            "total_data"    : item['TOTAL_DATA']
                        })
                    }
                }
            })

            const dataPIC   = [];
            const dataTotal = [];
            $.each(dataMember, (i, item) => {
                dataPIC.push(item.pic_name == '' ? 'Tidak Ada PIC' : item.pic_name);
                dataTotal.push(item.total_data);
            });

            const chartData     = {
                labels  : dataPIC,
                datasets: [{
                    label               : 'Total Data',
                    data                : dataTotal,
                    backgroundColor     : ['#007bff', '#1ab394', '#17a2b8', '#6610f2', '#6f42c1', '#e83e8c', '#dc3545', '#fd7e14', '#ffc107']
                }],
            };

            const ctx   = document.getElementById(idChart).getContext('2d');
            if(chartPie) {
                chartPie.destroy();
            }

            chartPie     = new Chart(ctx, {
                type    : 'doughnut',
                data    : chartData,
                options : {
                    title   : {
                        display     : true,
                        text        : 'Total Member Baru Per '+ $("#modal_member_title_month").text() + " " + moment().format('YYYY'),
                    }
                }
            })
        }
    }
}

$(document).ready(function(){
    // GET DATA UMRAH
    const umrah_url     = base_url + "/umhaj/umrah/get_data";
    const umrah_type    = "GET";
    const umrah_data    = {
        "data"      : {
            "jenis"     : "semua",
            "tahun_cari": moment(today).format('YYYY'),
            "bulan_cari": "",
        }
    };

    // GET DATA MEMBER
    const member_url    = base_url + "/umhaj/member/get_data";
    const member_type   = "GET";
    const member_data   = {
        "data"  : {
            "cs_name"   : "semua",
            "tahun_cari": moment(today).format('YYYY'),
            "bulan_cari": "",
        }
    };

    // GET DATA UMRAH
    const list_umrah_url    = base_url + "/umhaj/umrah/get_data_umrah_list/tahun/"+moment(today, 'YYYY-MM-DD').format('YYYY');
    const list_umrah_type   = "GET";
    const list_umrah_data   = "";

    const send_data     = [
        // doTransaction(umrah_url, umrah_type, umrah_data, '', true),
        doTransaction(member_url, member_type, member_data, '', true),
        doTransaction(list_umrah_url, list_umrah_type, list_umrah_data, "", true)
    ];

    Promise.allSettled(send_data)
        .then((success)     => {
            // MEMBER ZONE
            const member_getData    = success[0].value.data;
            const member_sendData   = [];
            for(let i = 0; i < member_getData.length; i++) {
                member_sendData.push(member_getData[i]['total_data']);
            }
            $("#chart_member_loading").addClass('d-none');
            $("#chart_member_view").removeClass('d-none');
            showChart('chart_member', member_sendData);

            // LIST UMRAH
            const list_umrah_getData    = success[1].value.data;
            $("#dashboard_umrah_total_data").html("<h2 class='no-margins font-weight-bold'>"+ list_umrah_getData['total_data'] +"</h2>");

            // LIST HAJI
            $("#dashboard_haji_total_data").html("<h2 class='no-margins font-weight-bold'>0</h2>")
        })
        .catch((error)      => {
            console.log(error)
        })

    // GET DATA FROM API
    const programUmrahURL   = "api/umhaj/master/program";
    const customerServiceURL= "api/umhaj/master/user/cs";
    
    const umrahURL          = "api/umhaj/umrah/get_data_umrah";
    const umrahSendData     = {
        "jenis"         : "semua",
        "tahun_cari"    : moment(today).format('YYYY'),
        "bulan_cari"    : "",
    };
    
    const agentURL          = "api/umhaj/agent/list";

    const apiGetData        = [
        doTransactionAPI(programUmrahURL, "GET", [], "", true),
        doTransactionAPI(customerServiceURL, "GET", [], "", true),
        doTransactionAPI(umrahURL, "POST", umrahSendData, "", true),
        doTransactionAPI(agentURL, "GET", [], "", true)
    ];

    Promise.allSettled(apiGetData)
        .then((success)     => {
            const programUmrahData  = success[0].value.data;
            showSelect('g_umrah_filter_package', programUmrahData, 'semua');

            const customerServiceData   = success[1].value.data;
            showSelect('g_member_filter_cs', customerServiceData, 'semua');

            const umrahData         = success[2].value.data;
            const umrahDataChart    = [];
            for(let i = 0; i < umrahData.length; i++) {
                umrahDataChart.push(umrahData[i]['total_data']);
            }
            $("#chart_umrah_loading").addClass('d-none');
            $("#chart_umrah_view").removeClass('d-none');
            showChart('chart_umrah', umrahDataChart);

            // LIST AGENT
            const agentGetData  = success[3].value.data;
            $("#dashboard_agent_total_data").html(`<h2 class='no-margins font-weight-bold'>${agentGetData.length}</h2>`)
            
        })
        .catch((err)        => {
            console.log(err)
        })
});



function showSelect(idSelect, data, selectedData)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });
    
    if(idSelect == 'g_umrah_filter_package') {
        var html    = [
            "<option selected disabled>Pilih Jenis Umrah</option>",
            "<option value='semua'>Semua</option>"
        ];

        if(data.length > 0) {
            for(const item of data) {
                html    += `<option value=${item.program_name}>${item.program_name}</option>`;
            }
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'g_member_filter_cs') {
        var html    = [
            "<option selected disabled>Pilih Customer Service</option>",
            "<option value='semua'>Semua</option>"
        ];
        
        if(data.length  > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value='${item.cs_name}'>${item.cs_name}</option>`;
            });

            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    }
}

function showModal(idModal, data)
{
    if(idModal == 'modal_member') {
        const member_url    = base_url + "/umhaj/member/get_data_detail";
        const member_type   = "GET";
        const member_data   = {
            "data"  : {
                "cs_name"   : data['pic'],
                "tahun_cari": moment(today).format('YYYY'),
                "bulan_cari": data['bulan_ke'],
            }
        };
        const member_msg    = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

        doTransaction(member_url, member_type, member_data, member_msg, true)
            .then((success)     => {
                Swal.close();
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                $("#modal_member_title_month").html(moment(data['bulan_ke'], 'MM').format('MMMM'));
                // GET DATA
                const member_getData    = success.data;
                showTable('table_modal_member', member_getData);

                showTable('table_modal_total_member', member_getData);
                $("#table_modal_total_member_wrapper").css('padding-bottom', '0px');

                // SHOW CHART
                showChartPie('chart_modal_total_member', member_getData);
            })
            .catch((error)      => {
                Swal.close();
                console.log(error)
                $("#modal_member_title_month").html("");
            })
    } else if(idModal == 'modal_umrah') {
        // GET DATA
        const umrah_url     = base_url + "/umhaj/umrah/get_data_detail";
        const umrah_type    = "GET";
        const umrah_data    = {
            "data"      : {
                "jenis"     : $("#g_umrah_filter_package").val(),
                "tahun_cari": moment(today).format('YYYY'),
                "bulan_cari": data['bulan_ke'],
            }
        };

        const umrah_msg     = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

        doTransaction(umrah_url, umrah_type, umrah_data, umrah_msg, true)
            .then((success)     => {
                $("#modal_umrah_title_month").html(moment(data['bulan_ke'], 'MM').format('MMMM'));
                const umrah_getData     = success.data;

                showTable('table_modal_umrah', umrah_getData);

                showTable('table_modal_umrah_summary', umrah_getData);

                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                Swal.close();
            })
            .catch((error)        => {
                console.log(error)
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak Ada Data Yang Bisa Dimuat',
                });
            })
    } else if(idModal == 'modal_list_umrah') {
        // GET DATA
        const list_umrah_url    = base_url + "/umhaj/umrah/get_data_umrah_list/tahun/"+moment(today, 'YYYY-MM-DD').format('YYYY');
        const list_umrah_type   = "GET";
        const list_umrah_data   = "";
        const list_umrah_msg    = Swal.fire({ title : 'Data Sedang Dimuat..', allowOutsideClick: false }); Swal.showLoading();

        doTransaction(list_umrah_url, list_umrah_type, list_umrah_data, list_umrah_msg, true)
            .then((success)     => {
                // GET DATA
                const list_umrah_sendData   = success.data.data;
                showTable('table_list_umrah', list_umrah_sendData);
                
                Swal.close();
                $("#"+idModal).modal({ keyboard: false, backdrop: 'static' });
            })
            .catch((error)      => {
                console.log(error)
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak Ada Data Yang Bisa Dimuat..'
                });
            })
    } else if(idModal == 'modal_list_umrah_detail') {
        closeModal('modal_list_umrah');
        var tourCode    = data;
        $("#modal_list_umrah_detail_tour_code").html(tourCode);
        // GET DATA
        const detailUmrah_url   = base_url + "/umhaj/umrah/get_data_umrah/tour_code";
        const detailUmrah_type  = "GET";
        const detailUmrah_data  = {
            "tourCode"  : tourCode,
        };
        const detailUmrah_msg   = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

        doTransaction(detailUmrah_url, detailUmrah_type, detailUmrah_data, detailUmrah_msg, true)
            .then((success)     => {
                Swal.close();
                const detailUmrah_getData   = success.data;
                // HEADER
                const detailUmrah_getData_header    = detailUmrah_getData['header'];
                $("#umrah_list_detail_tour_code").html(detailUmrah_getData_header['umrah_tour_code']);
                $("#umrah_list_detail_date").html(
                    "<i class='fa fa-plane'></i> &nbsp;"+moment(detailUmrah_getData_header['umrah_depature'], 'YYYY-MM-DD').format('DD/MM/YYYY')+"&nbsp; <i class='fa fa-plane fa-rotate-90'></i> &nbsp;"+moment(detailUmrah_getData_header['umrah_arrival'], 'YYYY-MM-DD').format('DD/MM/YYYY')+""
                );
                $("#umrah_list_detail_mentor").html(
                    "<i class='fa fa-user'></i> &nbsp;"+detailUmrah_getData_header['umrah_mentor']
                );
                // DETAIL
                const detailUmrah_getData_detail    = detailUmrah_getData['detail'];
                showTable('table_modal_list_umrah_detail', detailUmrah_getData_detail);
                
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            })
            .catch((error)      => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Data Tour Code : '+tourCode+' Tidak Ada'
                })
                console.log(error)
            })
    } else if(idModal == 'modal_agent') {
        // GET DATA FROM API
        const agentURL  = "api/umhaj/agent/list";
        const agentMsg  = Swal.fire({ title : 'Data Sedang Dimuat..' }); Swal.showLoading();

        doTransactionAPI(agentURL, "GET", [], agentMsg, true)
            .then((success)     => {
                Swal.close();
                const agentGetData  = success.data;
                showTable('table_list_agent', agentGetData);
                
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            })
            .catch((err)        => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak Ada Data Agent',
                })
            })
    }
}

function closeModal(idModal)
{
    $("#"+idModal).modal('hide');
    if(idModal == 'modal_member') {
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#modal_member_title_month").html("");
            $("#table_modal_total_member_footer_total").html(0);
            $("#table_modal_member_footer_total").html(0);
        })
    } else if(idModal == 'modal_umrah') {
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#table_modal_umrah_total").html(0);
            $("#modal_umrah_title_month").html("");
        })
    } else if(idModal == 'modal_list_umrah') {
        clearUrl();
        $("#"+idModal).on('hidden.bs.modal', () => {
            
        })
    } else if(idModal == 'modal_list_umrah_detail') {
        showModal('modal_list_umrah');
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#umrah_list_detail_tour_code").html();
            $("#umrah_list_detail_date").html();
            $("#umrah_list_detail_mentor").html();
            $("#table_modal_list_umrah_detail_total_banyaknya").html(0);
        })
    } else if(idModal == 'modal_agent') {
        clearUrl();
    }
}

function cariData(idForm, data)
{
    if(idForm == 'chart_umrah')
    {
        const umrahURL      = "api/umhaj/umrah/get_data_umrah";
        const umrahSendData     = {
            "jenis"     : data,
            "tahun_cari": moment(today).format('YYYY'),
            "bulan_cari": "",
        };

        $("#chart_umrah_loading").removeClass('d-none');
        $("#chart_umrah_view").addClass('d-none');

        doTransactionAPI(umrahURL, "POST", umrahSendData, "", true)
            .then((success)     => {
                const umrahGetData  = success.data;
                let umrahChartData= [];

                for(let i = 0; i < umrahGetData.length; i++) {
                    umrahChartData.push(umrahGetData[i]['total_data']);
                }
                
                $("#chart_umrah_loading").addClass('d-none');
                $("#chart_umrah_view").removeClass('d-none');
                showChart('chart_umrah', umrahChartData);
            })
            .catch((err)        => {
                let umrahChartData  = [];
                for(let i = 0; i < 12; i++) {
                    umrahChartData.push({
                        i   : 0,
                    });
                }
                setTimeout(() => {
                    $("#chart_umrah_loading").addClass('d-none');
                    $("#chart_umrah_view").removeClass('d-none');
                }, 1000);
                showChart('chart_umrah', umrahChartData);
            })
    } else if(idForm == 'chart_member') {
        // GET DATA MEMBER
        const member_url    = base_url + "/umhaj/member/get_data";
        const member_type   = "GET";
        const member_data   = {
            "data"  : {
                "cs_name"   : data,
                "tahun_cari": moment(today).format('YYYY'),
                "bulan_cari": "",
            }
        };
        $("#chart_member_loading").removeClass('d-none');
        $("#chart_member_view").addClass('d-none');
        doTransaction(member_url, member_type, member_data, '', true)
            .then((success)     => {
                const member_getData    = success.data;
                const member_sendData   = [];
                for(const item of member_getData) {
                    member_sendData.push(item['total_data']);
                }
                
                $("#chart_member_loading").addClass('d-none');
                $("#chart_member_view").removeClass('d-none');
                showChart('chart_member', member_sendData);
            })
            .catch((error)      => {
                console.log(error);
                $("#chart_member_loading").addClass('d-none');
                $("#chart_member_view").removeClass('d-none');
                showChart('chart_member', '');
            })
    }
}

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'table_modal_member')
    {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "Tidak Ada Data Yang Bisa Dimuat..",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan", 
            },
            autoWidth   : true,
            pageLength  : -1,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "20%" },
                { "targets" : [2], "className" : "text-left align-middle" },
                { "targets" : [3], "className" : "text-center align-middle", "width" : "10%" },
            ],
            ordering    : false,
            paging      : false,
            bInfo       : false,
            searching   : false,
        });

        let seq = 1;
        let totalData   = 0;
        for(let i = 0; i < data.length; i++)
        {
            const dataMember_pic        = data[i]['PIC_NAME'] == "" ? "Tidak Ada PIC" : data[i]['PIC_NAME'];
            const dataMember_createdDate= moment(data[i]['CREATED_DATE'], 'YYYY-MM-DD').format('dddd')+", "+ moment(data[i]['CREATED_DATE'], 'YYYY-MM-DD').format('DD MMMM YYYY');
            const dataMember_totalData  = data[i]['TOTAL_DATA'];

            $("#"+idTable).DataTable().row.add([
                "<label class='no-margins font-weight-normal'>" + (i > 0 ? (data[i]['CREATED_DATE'] == data[i - 1]['CREATED_DATE'] ? "" : seq++) : seq++) + "</label>",
                "<label class='no-margins font-weight-normal'>" + (i > 0 ? (data[i]['CREATED_DATE'] == data[i - 1]['CREATED_DATE'] ? "" : dataMember_createdDate) : dataMember_createdDate) + "</label>",
                "<label class='no-margins font-weight-normal'>" + dataMember_pic + "</label>",
                "<label class='no-margins font-weight-normal'>" + dataMember_totalData + "</label>"
            ]).draw(false);

            totalData   += data[i]['TOTAL_DATA'];
        }

        $("#table_modal_member_footer_total").html(totalData);
    } else if(idTable == 'table_modal_total_member') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "Tidak Ada Data Yang Bisa Dimuat..",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan", 
            },
            autoWidth   : true,
            pageLength  : -1,
            ordering    : false,
            paging      : false,
            bInfo       : false,
            searching   : false,
            columnDefs  : [
                { "targets" : [0, 2], "className" : "text-center align-middle", "width" : "10%" }
            ],
        });

        if(data.length > 0)
        {
            var dataMember   = [];
            data.forEach(item   => {
                if(dataMember.length === 0) {
                    dataMember.push({
                        "pic_name"      : item['PIC_NAME'],
                        "total_data"    : item['TOTAL_DATA'],
                    })
                } else {
                    let found   = false;
                    for(let i = 0; i < dataMember.length; i++) {
                        if(dataMember[i]['pic_name'] === item['PIC_NAME']) {
                            dataMember[i]['total_data'] += item['TOTAL_DATA'];
                            found   = true;
                            break;
                        }
                    }

                    if(!found) {
                        dataMember.push({
                            "pic_name"      : item['PIC_NAME'],
                            "total_data"    : item['TOTAL_DATA']
                        })
                    }
                }
            })

            dataMember.sort((a, b)  => b.total_data - a.total_data);

            let seq = 1;
            let grandTotal  = 0;
            dataMember.forEach(item => {
                const member_picName    = item['pic_name'] == '' ? 'Tidak Ada PIC' : item['pic_name'];
                const member_totalData  = item['total_data'];
                grandTotal  += member_totalData;

                $("#"+idTable).DataTable().row.add([
                    seq++,
                    member_picName,
                    member_totalData
                ]).draw(false);
            });
            $("#table_modal_total_member_footer_total").html(grandTotal);
        }
    } else if(idTable == 'table_modal_umrah') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "Tidak Ada Data Yang Bisa Dimuat..",
                zeroRecords : "Data Yang Dicari Tidak Ditemukan",
            },
            paging      : false,
            bInfo       : false,
            searching   : false,
            autoWidth   : true,
            ordering    : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [3], "className" : "text-center align-middle", "width" : "10%" },
            ],
        });

        if(data.length > 0) {
            let seq  = 1;
            let total = 0;
            for(let i = 0; i < data.length; i++) {
                const umrah_seq         = i > 0 ? (data[i]['TGL_DAFTAR'] == data[i - 1]['TGL_DAFTAR'] ? "" : seq++) : seq++;
                const umrah_tglDaftar   = i > 0 ? (data[i]['TGL_DAFTAR'] == data[i - 1]['TGL_DAFTAR'] ? "" : moment(data[i]['TGL_DAFTAR'], 'YYYY-MM-DD').format('dddd')+", "+moment(data[i]['TGL_DAFTAR'], 'YYYY-MM-DD').format('DD MMMM YYYY') ) : moment(data[i]['TGL_DAFTAR'], 'YYYY-MM-DD').format('dddd')+", "+moment(data[i]['TGL_DAFTAR'], 'YYYY-MM-DD').format('DD MMMM YYYY');
                const umrah_jenisUmrah  = data[i]['JENIS_UMRAH'];
                const umrah_tourCode    = data[i]['KODE_UMRAH'];
                const umrah_totalData   = data[i]['TOTAL_DATA'];

                $("#"+idTable).DataTable().row.add([
                    "<label class='no-margins font-weight-normal'>" + umrah_seq + "</label>",
                    "<label class='no-margins font-weight-normal'>" + umrah_tglDaftar + "</label>",
                    "<label class='no-margins font-weight-normal'>" + umrah_tourCode+" ("+umrah_jenisUmrah+")" + "</label>",
                    "<label class='no-margins font-weight-normal'>" + umrah_totalData + "</label>"
                ]).draw(false);

                total   += umrah_totalData;
            }

            $("#table_modal_umrah_total").html(total);
        }
    } else if(idTable == 'table_modal_umrah_summary') {
        $("#"+idTable).DataTable({
            languagge   : {
                emptyTable  : "Tidak Ada Data Yang Bisa Ditampilkan",
                zeroRecords : "Data Yang Dicari Tidak Ditemukan"
            },
            bInfo       : false,
            searching   : false,
            paging      : false,
            columnDefs  : [
                { "targets" : [0, 2], "className" : "text-center", "width" : "8%" },
                { "targets" : [1], "className" : "text-left" },
            ],
        })

        if(data.length > 0) {
            var dataKategoriUmrah    = [];
            data.forEach(item   => {
                if(dataKategoriUmrah.length === 0) {
                    dataKategoriUmrah.push({
                        "kategori_nama"     : item['JENIS_UMRAH'],
                        "total_data"        : item['TOTAL_DATA'],
                    });
                } else {
                    let found   = false;
                    for(let i = 0; i < dataKategoriUmrah.length; i++) {
                        if(dataKategoriUmrah[i]['kategori_nama'] === item['JENIS_UMRAH']) {
                            dataKategoriUmrah[i]['total_data'] += item['TOTAL_DATA'];
                            found   = true;
                            break;
                        }
                    }

                    if(!found) {
                        dataKategoriUmrah.push({
                            "kategori_nama" : item['JENIS_UMRAH'],
                            "total_data"    : item['TOTAL_DATA'], 
                        })
                    }
                }
            })
            dataKategoriUmrah.sort((a, b)   => b.total_data - a.total_data);

            let seq         = 1;
            let grandTotal  = 0;
            for(let i = 0; i < dataKategoriUmrah.length; i++) {
                $("#"+idTable).DataTable().row.add([
                    "<label class='font-weight-normal no-margins'>" + seq++ + "</label>",
                    "<label class='font-weight-normal no-margins'>" + dataKategoriUmrah[i]['kategori_nama'] + "</label>",
                    "<label class='font-weight-normal no-margins'>" + dataKategoriUmrah[i]['total_data'] + "</label>",
                ]).draw(false);
                
                grandTotal  += dataKategoriUmrah[i]['total_data'];
            }
            $("#table_modal_umrah_summary_total").html(grandTotal);
        }
    } else if(idTable == 'table_list_umrah') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : 'Tidak Ada Data Yang Bisa Ditampilkan..',
                zeroRecords : 'Data Yang Dicari Tidak Ditemukan'
            },
            ordering    : true,
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "18%" },
                { "targets" : [2, 3], "className" : "text-center align-middle", "width" : "10%" },
                { "targets" : [5, 6], "className" : "text-right align-middle", "width" : "8%" },
            ],
        });
        $("#table_list_umrah_wrapper").css('padding-bottom','0px');

        if(data.length > 0) {
            let i = 1;
            let grandTotalTarget        = 0;
            let grandTotalRealization   = 0;
            for(const item of data) {
                var list_umrah_seq            = i++;
                var list_umrah_tourCode       = item['UMRAH_TOUR_CODE'];
                var list_umrah_tourDepature   = item['UMRAH_DEPATURE'];
                var list_umrah_tourArrival    = item['UMRAH_ARRIVAL'];
                var list_umrah_tourLeader     = item['UMRAH_TOUR_MENTOR'];
                var list_umrah_tourTarget     = item['UMRAH_TARGET'];
                var list_umrah_tourRealization= item['UMRAH_TARGET_REALIZATION'];

                $("#"+idTable).DataTable().row.add([
                    "<label class='font-weight-normal no-margins'>" + list_umrah_seq + "</label>",
                    "<label class='font-weight-normal no-margins' style='cursor: pointer; color: #1ab394; text-decoration: underline;' title='Lihat Detail' onclick='showModal(`modal_list_umrah_detail`, `" + list_umrah_tourCode + "`)'>" + list_umrah_tourCode + "</label>",
                    "<label class='font-weight-normal no-margins'>" + list_umrah_tourDepature + "</label>",
                    "<label class='font-weight-normal no-margins'>" + list_umrah_tourArrival + "</label>",
                    "<label class='font-weight-normal no-margins'>" + list_umrah_tourLeader + "</label>",
                    "<label class='font-weight-normal no-margins'>" + list_umrah_tourTarget + "</label>",
                    "<label class='font-weight-normal no-margins'>" + list_umrah_tourRealization + "</label>"
                ]).draw(false);
                
                grandTotalTarget        += list_umrah_tourTarget;
                grandTotalRealization   += list_umrah_tourRealization;
            }
            let persentase  = ((parseInt(grandTotalRealization) / parseInt(grandTotalTarget)) * 100);
            $("#table_list_umrah_total_target").html(grandTotalTarget);
            $("#table_list_umrah_total_realisasi").html(grandTotalRealization);
            $("#table_list_umrah_persentase").html(parseFloat(persentase).toFixed(2));
        }
    } else if(idTable == 'table_modal_list_umrah_detail') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : 'Tidak Ada Data Yang Bisa Ditampilkan..',
                zeroRecords : 'Data Yang Dicari Tidak Ditemukan'
            },
            ordering    : true,
            autoWidth   : false,
            pageLength  : -1,
            paging      : false,
            bInfo       : false,
            searching   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [2], "className" : "text-center align-middle", "width" : "8%" },
            ],
        });

        if(data.length > 0) {
            let seq     = 1;
            let total   = 0;
            for(const item of data)
            {
                $("#"+idTable).DataTable().row.add([
                    "<label class='font-weight-normal no-margins'>" + seq++ + "</label>",
                    "<label class='font-weight-normal no-margins'>" + moment(item.detail_umrah_registry_date, 'YYYY-MM-DD').format('DD MMMM YYYY') + "</label>",
                    "<label class='font-weight-normal no-margins'>" + item.detail_umrah_total_data + "</label>"
                ]).draw(false);

                total   += item.detail_umrah_total_data;
            }

            $("#table_modal_list_umrah_detail_total_banyaknya").html("<label class='font-weight-bold no-margins'>" + total + "</label>");
        }
    } else if(idTable == 'table_list_agent') {
        $("#"+idTable).DataTable({
            language        : {
                emptyTable  : "Tidak Ada Data Yang Bisa Ditampilkan",
                zeroRecords : "Data Yang Dicari Tidak Ditemukan",
            },
            columnDefs      : [
                { "targets" : [0, 4], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "20%" },
                { "targets" : [2], "className" : "text-left align-middle" },
                { "targets" : [3], "className" : "text-left align-middle", "width" : "10%" },
            ],
        })

        $("#table_list_agent_wrapper").css('padding-bottom', '0px');

        if(data.length > 0) {
            let seq  = 1;
            for(const item of data) {
                let agentId     = item.agent_id;
                let agentName   = item.agent_name;
                let agentPIC    = item.agent_pic;
                let agentContact= item.agent_contact_2 != "" ? item.agent_contact_1+" / "+item.agent_contact_2 : item.agent_contact_1;
                let agentAct    = "<button class='btn btn-sm btn-primary' title='Lihat Detail' value='" + agentId + "'><i class='fa fa-eye'></i></button>";

                $("#"+idTable).DataTable().row.add([
                    `<label class='no-margins font-weight-normal'>${seq++}</label>`,
                    `<label class="no-margins font-weight-normal">${agentName}</label>`,
                    `<label class="no-margins font-weight-normal">${agentPIC}</label>`,
                    `<label class="no-margins font-weight-normal">${agentContact}</label>`,
                    agentAct,
                ]).draw(false);
            }
        }
    }
}


function clearUrl()
{
    var url     = window.location.href;
    var cleanUrl= url.split('#')[0];
    window.history.replaceState({}, document.title, cleanUrl);
}

function doTransaction(url, type, data, msg, isAysnc)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            type    : type,
            async   : isAysnc,
            url     : url,
            headers : {
                'X-CSRF-TOKEN' : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                msg;
            },
            success     : (success) => {
                resolve(success)
            },
            error       : (error)   => {
                reject(error)
            }
        })
    })
}

function doTransactionAPI(url, type, data, msg, isAsync)
{
    const newData   = new URLSearchParams(data).toString();
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache           : false,
            type            : type, 
            async           : isAsync,
            // url             : "http://localhost:3001/"+url,
            url             : 'https://apiv2.perciktours.com/'+url,
            headers         : {
                "x-api-key"     : "",
                "Content-Type"  : 'application/x-www-form-urlencoded',
            },
            data            : newData,
            beforeSend      : ()    => {
                msg;
            },
            success         : (success) => {
                resolve(success)
            },
            error           : (error)   => {
                reject(error)
            }
        })
    })
}