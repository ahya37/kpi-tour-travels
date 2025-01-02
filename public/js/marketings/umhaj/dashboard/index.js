var base_url    = window.location.origin;
var today       = moment().format('YYYY-MM-DD');
var chartUmrah;
var chartMember;
var chartPie;
var dataAgent   = [];
var dataSumber  = [];
var dataProvinsi= [];
var dataCS      = [];

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

const getAge    = (date1, date2)    => {
    let compareYear     = moment(date2, 'DD/MM/YYYY').diff(moment(date1, 'DD/MM/YYYY'), 'months');
    let tahun           = Math.floor(compareYear / 12);
    let bulan           = compareYear % 12;
    
    let output          = {
        "tahun" : tahun,
        "bulan" : bulan
    };

    return output;
}

$(document).ready(function(){
    // GET DATA FROM API
    const programUmrahURL   = base_url + "/umhaj/master/master_data_program";
    const customerServiceURL= base_url + "/umhaj/master/master_data_user_cs";
    
    const umrahURL          = base_url + "/umhaj/umrah/get_data_chart_umrah"
    const umrahType         = "POST";
    const umrahSendData     = {
        "jenis"         : "semua",
        "tahun_cari"    : moment(today).format('YYYY'),
        "bulan_cari"    : "",
    };

    const jadwalUmrahURL    = base_url + "/umhaj/master/master_data_jadwal_umrah";
    const jadwalUmrahType   = "POST";
    const jadwalUmrahData   = {
        "tahun"     : moment(today, 'YYYY-MM-DD').year(),
    };
    
    const agentURL          = base_url + "/umhaj/agent/get_data_agent";
    const agentType         = "POST";
    const agentData         = [];

    const memberURL         = base_url + "/umhaj/member/get_data_chart_member";
    const memberType        = "POST";
    const memberData        = {
        "cs_name"       : "semua",
        "tahun_cari"    : moment(today, 'YYYY-MM-DD').format('YYYY'),
        "bulan_cari"    : ""
    };

    const memberDataURL     = base_url + "/umhaj/member/get_data_member_v2";
    const memberDataType    = "POST";
    const memberDataData    = {
        "draw"      : 1,
        "start"     : 0,
        "length"    : 10,
    };

    const masterSumberURL   = base_url + "/umhaj/master/master_data_sumber";
    const masterSumberType  = "GET";

    const apiGetData        = [
        doTransaction(programUmrahURL, "GET", [], "", true),
        doTransaction(customerServiceURL, "GET", [], "", true),
        doTransaction(umrahURL, umrahType, umrahSendData, "", true),
        doTransaction(agentURL, agentType, agentData, "", true),
        doTransaction(jadwalUmrahURL, jadwalUmrahType, jadwalUmrahData, "", true),
        doTransaction(memberURL, memberType, memberData, '', true),
        doTransaction(memberDataURL, memberDataType, memberDataData, "", true),
        doTransaction(masterSumberURL, masterSumberType, [], "", true),
    ];

    Promise.allSettled(apiGetData)
        .then((success)     => {
            const programUmrahData  = success[0].status == 'fulfilled' ? success[0].value.data : [];
            showSelect('g_umrah_filter_package', programUmrahData, 'semua');

            const customerServiceData   = success[1].status == "fulfilled" ? success[1].value.data : [];
            dataCS  = customerServiceData;
            showSelect('g_member_filter_cs', customerServiceData, 'semua');

            const umrahData         = success[2].status == "fulfilled" ? success[2].value.data : [];
            const umrahDataChart    = [];
            for(let i = 0; i < umrahData.length; i++) {
                umrahDataChart.push(umrahData[i]['total_data']);
            }
            $("#chart_umrah_loading").addClass('d-none');
            $("#chart_umrah_view").removeClass('d-none');
            showChart('chart_umrah', umrahDataChart);

            // LIST AGENT
            const agentGetData  = success[3].status == "fulfilled" ? success[3].value.data : [];
            dataAgent   = agentGetData;
            $("#dashboard_agent_total_data").html(`<h2 class='no-margins font-weight-bold'>${agentGetData.length}</h2>`);

            // LIST JADWAL UMRAH
            const jadwalUmrahGetData    = success[4].status == "fulfilled" ? success[4].value.data : [];
            $("#dashboard_umrah_total_data").html(`<h2 class='no-margins font-weight-bold'>${jadwalUmrahGetData.length}</h2>`)
            
            // CHART MEMBER
            const memberGetData     = success[5].status == 'fulfilled' ? success[5].value.data : [];
            const memberSendData    = [];
            for(let i = 0; i < memberGetData.length; i++) {
                memberSendData.push(memberGetData[i]['total_data']);
            }
            $("#chart_member_loading").addClass('d-none');
            $("#chart_member_view").removeClass('d-none');
            showChart('chart_member', memberSendData);

            // LIST HAJI
            $("#dashboard_haji_total_data").html("<h2 class='no-margins font-weight-bold'>0</h2>");

            // LIST MEMBER
            let memberDataGetData   = success[6].value.recordsTotal;
            $("#dashboard_member_total_data").html(`<h2 class="no-margins font-weight-bold">${memberDataGetData}</h2>`)
            
            // MASTER SUMBER
            let masterSumberGetData     = success[7].status == "fulfilled" ? success[7].value.data : [];
            dataSumber  =  masterSumberGetData;
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
    } else if(idSelect == 'mb_nm_mhr') {
        let html    = "<option selected disabled>Pilih Mahram</option>";

        $("#"+idSelect).html(html);
    } else if(idSelect == 'mb_st_mhr') {
        let html    = `<option selected disabled>Status Mahram</option>`;
        data    = [
            [1, 'Ayah / Ibu'],
            [2, 'Adik / Kakak'],
            [3, 'Suami / Istri'],
            [4, 'Kakek / Nenek'],
            [5, 'Lainnya']
        ];

        $.each(data, (i, item)  => {
            html    += `<option value='${item[0]}'>${item[1]}</option>`;
        })

        $("#"+idSelect).html(html);

        console.log(selectedData);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'mb_npwp_stat') {
        let html    = [
            "<option selected disabled>Status Pemilik</option>",
            "<option value='Suami'>Suami</option>",
            "<option value='Ayah'>Ayah</option>",
            "<option value='Anak'>Anak</option>",
            "<option value='Cucu'>Cucu</option>",
            "<option value='Saudara'>Saudara</option>",
            "<option value='Mertua'>Mertua</option>",
            "<option value='Menantu'>Menantu</option>",
            "<option value='Lain-Lain'>Lain-Lain</option>",
        ];

        $("#"+idSelect).html(html);
    } else if(idSelect == 'mb_pl_prv') {
        let html    = "<option selected disabled>Pilih Provinsi</option>";

        if(data.length > 0) {
            $.each(data, (i, item) => {
                html    += `<option value='${item['provinces_id']}'>${item['provinces_name']}</option>`;
            });
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'mb_pl_ct') {
        let html    = "<option selected disabled>Pilih Kota / Kabupaten</option>";

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value='${item['city_id']}'>${item['city_name']}</option>`;
            });
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'mb_pl_kc') {
        let html    = "<option selected disabled>Pilih Kecamatan</option>";

        if(data.length > 0) {
            $.each(data, (i, item) => {
                html    += `<option value='${item['district_id']}'>${item['district_name']}</option>`;
            })
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'mb_pl_kl') {
        let html    = "<option selected disabled>Pilih Kelurahan</option>";

        if(data.length > 0) {
            $.each(data, (i, item) => {
                html    += `<option value='${item['village_id']}'>${item['village_name']}</option>`;
            })
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'mb_sz_bd') {
        let html    = [
            "<option selected disabled>Pilih Ukuran Baju</option>",
            "<option value='XS'>XS</option>",
            "<option value='S'>S</option>",
            "<option value='M'>M</option>",
            "<option value='L'>L</option>",
            "<option value='XL'>XL</option>",
            "<option value='XXL'>XXL</option>",
            "<option value='XXXL'>XXXL</option>",
        ];

        $("#"+idSelect).html(html);
    } else if(idSelect == 'mb_job') {
        let html    = "<option selected disabled>Pilih Pekerjaan</option>";
        data        = [
            'PNS',
            'Swasta',
            'Wiraswasta',
            'Pengusaha',
            'BUMN',
            'TNI',
            'Polri',
            'Ibu Rumah Tangga',
            'Mahasiswa',
            'Pelajar',
            'Dosen',
            'Pensiuan',
            'Dokter',
            'Lainnya'
        ];

        data.sort();

        $.each(data, (i, item)  => {
            html    += `<option value="${item}">${item}</option>`;
        });

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData).trigger('change');
        }
    } else if(idSelect == 'mb_acd') {
        let html    = "<option selected disabled>Pilih Pendidikan</option>";
        
        data    = [
            'Tidak Sekolah',
            'SD',
            'SMP',
            'SMA',
            'D1',
            'D2',
            'D3',
            'D4',
            'S1',
            'S2',
            'S3'
        ];

        $.each(data, (i, item) => {
            html    += `<option value='${item}'>${item}</option>`;
        });

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'mb_sc') {
        // SELECT SUMBER INFORMASI
        let html    = "<option selected disabled>Sumber Informasi</option>";

        if(data.length > 0) {
            $.each(data, (i, item) => {
                html    += `<option value='${item}'>${item}</option>`;
            });
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData).trigger('change');
        }

    } else if(idSelect == 'mb_agt_name') {
        let html    = "<option selected disabled>Pilih Nama Agen</option>";

        if(data.length > 0) {
            data.sort((a, b) => {
                if(a['agent_name'] < b['agent_name']) return -1;
                if(a['agent_name'] > b['agent_name']) return 1;
                return 0;
            })

            $.each(data, (i, item) => {
                html    += `<option value='${item['agent_id']}'>${item['agent_name']}</option>`;
            });
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    } else if(idSelect == 'mb_cs_name') {
        let html    = "<option selected disabled>Pilih Customer Service</option>";

        if(data.length > 0) {
            $.each(data, (i, item) => {
                html    += `<option value='${item['cs_name']}'>${item['cs_name'].toUpperCase()}</option>`;
            })
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    }
}

function showSelectDetail(idSelect, seq = null, data)
{
    if(idSelect == 'mb_pl_prv') {
        if(data != '') {
            const ctyURL    = base_url + "/umhaj/master/data_wilayah/kota";
            const ctyType   = "GET";
            const ctyData   = {
                "province_id"   : data,
            };
            
            showSelect('mb_pl_ct', [], '');
            showSelect('mb_pl_kc', [], '');
            showSelect('mb_pl_kl', [], '');

            doTransaction(ctyURL, ctyType, ctyData, '', true)
                .then((success)     => {
                    const ctyGetData    = success.data;
                    showSelect('mb_pl_ct', ctyGetData, '');
                })
                .catch((err)        => {
                    console.log(err);
                })
        }
    } else if(idSelect == 'mb_pl_ct') {
        if(data != '') {
            const kctURL    = base_url + "/umhaj/master/data_wilayah/kecamatan";
            const kctType   = "GET";
            const kctData   = {
                "city_id"       : data,
            };

            showSelect('mb_pl_kc', [], '');
            showSelect('mb_pl_kl', [], '');

            doTransaction(kctURL, kctType, kctData, '', true)
                .then((success)     => {
                    const kctGetData    = success.data;
                    showSelect('mb_pl_kc', kctGetData, '');
                })
                .catch((err)        => {
                    console.log(err);
                })
        }
    } else if(idSelect == 'mb_pl_kc') {
        if(data != '') {
            const klrURL    = base_url + "/umhaj/master/data_wilayah/kelurahan";
            const klrType   = "GET";
            const krlData   = {
                "district_id"   : data,
            };

            showSelect('mb_pl_kl', [], '');

            doTransaction(klrURL, klrType, krlData, '', true)
                .then((success)     => {
                    const klrGetData    = success.data;
                    showSelect('mb_pl_kl', klrGetData, '');
                })
                .catch((err)        => {
                    console.log(err);
                })
        }
    } else if(idSelect == 'mb_sc') {
        if(data != '') {
            if(data == 'Agen') {
                $("#mb_agt_form").removeClass('d-none');
                showSelect('mb_agt_name', dataAgent, '');
            } else {
                $("#mb_agt_form").addClass('d-none');
            }
        }
    }
}

function showDate(idDate, data)
{
    let status;
    if(data == '') {
        status  = false;
    } else {
        status  = true;
    }
    $("#"+idDate).daterangepicker({
        showDropdowns   : true,
        singleDatePicker: true,
        autoApply       : true,
        minYear         : 1901,
        autoUpdateInput : status,
        format          : 'DD/MM/YYYY'
    });

    

    if(data != '') {
        $("#"+idDate).data('daterangepicker').setStartDate(moment(data, 'YYYY-MM-DD').format('DD/MM/YYYY'));
        $("#"+idDate).data('daterangepicker').setEndDate(moment(data, 'YYYY-MM-DD').format('DD/MM/YYYY'));
    } else {
        $("#"+idDate).val('');
    }

    $("#"+idDate).on('apply.daterangepicker', (ev, picker) => {
        let selectedDate    = picker.startDate.format('DD/MM/YYYY');
        let currentDate     = moment(today, 'YYYY-MM-DD').format('DD/MM/YYYY');
        $("#"+idDate).val(selectedDate);

        // DAPATKAN UMUR JAMAAH
        if(idDate == 'mb_tgl_lh') {
            let age     = getAge(selectedDate, currentDate);

            $("#mb_tgl_ag").removeClass('d-none');
            $("#mb_tgl_ag").text(`${age.tahun} Tahun ${age.bulan} Bulan`);
        }
    });
}

function showModal(idModal, data)
{
    if(idModal == 'modal_member') {
        const member_url    = base_url + "/umhaj/member/get_data_detail";
        const member_type   = "POST";
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
        const agentUrl  = base_url + "/umhaj/agent/get_data_agent";
        const agentType = "POST";
        const agentMsg  = Swal.fire({ title : 'Data Sedang Dimuat..' }); Swal.showLoading();

        doTransaction(agentUrl, agentType, [], agentMsg, true)
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
    } else if(idModal == 'modal_member_all') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
        showTable('tableModalMemberAll', []);
        $("#tableModalMemberAll").find('.dataTables_empty').text('Tidak Ada Data Yang Bisa Dimuat');
    } else if(idModal == 'modal_form_member') {
        // DEFAULT TAB ACTIVE
        $("#nav_data_pribadi").addClass('active');
        $("#tab_data_pribadi").addClass('active show');

        $("#nav_data_pribadi").on('shown.bs.tab', () => {
            $("#mb_nm_dp").focus();
        });

        $("#nav_data_passport").on('shown.bs.tab', () => {
            $("#mb_nm_psp").focus();
        })

        $("#nav_data_alamat").on('shown.bs.tab', () => {
            $("#mb_adr").focus();
        })

        $("#nav_data_keterangan").on('shown.bs.tab', () => {
            $("#mb_dsc").focus();
        })

        // GET DATA
        const prvURL    = base_url + "/umhaj/master/data_wilayah/provinsi";
        const prvType   = "GET";
        if(dataProvinsi.length < 1) {
            // GET DATA PROVINSI
            doTransaction(prvURL, prvType, [], '', true)
                .then((success)     => {
                    dataProvinsi = success.data;
                    showSelect('mb_pl_prv', dataProvinsi, '');
                })
                .catch((err)        => {
                    console.log(err);
                    showSelect('mb_pl_prv', [], '');
                })
        } else {
            showSelect('mb_pl_prv', dataProvinsi, '');
        }

        // STYLE
        // SELECT2
        showSelect('mb_nm_mhr', [], '');
        showSelect('mb_st_mhr', [], '');
        showSelect('mb_npwp_stat', [], '');
        showSelect('mb_pl_prv', dataProvinsi, '');
        showSelect('mb_pl_ct', [], '');
        showSelect('mb_pl_kc', [], '');
        showSelect('mb_pl_kl', [], '');
        showSelect('mb_sz_bd', [], '');
        showSelect('mb_job', [], '');
        showSelect('mb_acd', [], '');
        showSelect('mb_sc', dataSumber, '');
        showSelect('mb_agt_name', dataAgent, '');
        showSelect('mb_cs_name', dataCS, '');
        // DATEPICKER
        showDate('mb_tgl_lh', '');
        showDate('mb_tgl_psp', '');
        showDate('mb_tgl_psp_exp', '');

        if($("#mb_sc").val() != 'Agen') {
            $("#mb_agt_form").addClass('d-none');
        } else {
            $("#mb_agt_form").removeClass('d-none');
        }

        if(data == '') {
            $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            $("#modal_form_member_jenis").text('Tambah');
            $("#modal_form_member_btnSimpan").val('add');

            $("#"+idModal).on('shown.bs.modal', () => {
                $("#mb_nm_dp").focus();
            })
        } else {
            $("#modal_form_member_jenis").text('Ubah');
            $("#modal_form_member_btnSimpan").val('edit');

            // GET DATA FROM DATABASE
            const memberURL     = base_url + "/umhaj/member/get_jemaah_2_detail";
            const memberType    = "GET";
            const memberData    = {
                "jemaahID"      : data,
            };
            const memberMsg     = Swal.fire({ title : 'Data Sedang Dimuat..' }); Swal.showLoading();

            doTransaction(memberURL, memberType, memberData, memberMsg, true)
                .then((success)     => {
                    Swal.close();
                    $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                    const dataPribadi   = success.data.data_pribadi;
                    const dataPassport  = success.data.data_passport;
                    const dataAlamat    = success.data.data_alamat;
                    const dataPekerjaan = success.data.data_pekerjaan;
                    const catatan       = success.data.catatan;

                    // TAB DATA PRIBADI
                    $("#mb_nm_dp").val(dataPribadi.jemaah_nama_awal);
                    $("#mb_nm_tg").val(dataPribadi.jemaah_nama_tengah);
                    $("#mb_nm_bl").val(dataPribadi.jemaah_nama_akhir);
                    $("#mb_nm").val(dataPribadi.jemaah_nama);
                    $("#mb_nm_ay").val(dataPribadi.jemaah_nama_ayah);
                    $("#mb_nik").val(dataPribadi.jemaah_nik);
                    $("#mb_tmp_lh").val(dataPribadi.jemaah_tempat_lahir);
                    showDate('mb_tgl_lh', dataPribadi.jemaah_tanggal_lahir);
                    $("input[type='radio'][name='mb_jk'][value='" + dataPribadi.jemaah_jenis_kelamin + "']").prop('checked', true);
                    $("input[type='radio'][name='mb_st'][value='" + dataPribadi.jemaah_status + "']").prop('checked', true);
                    showSelect('mb_st_mhr', [], dataPribadi.jemaah_status_mahram);
                    
                    // AMBIL UMUR
                    let age     = getAge(moment(dataPribadi.jemaah_tanggal_lahir, 'YYYY-MM-DD').format('DD/MM/YYYY'), moment(today).format('DD/MM/YYYY'));
                    $("#mb_tgl_ag").removeClass('d-none');
                    $("#mb_tgl_ag").text(`${age.tahun} Tahun ${age.bulan} Bulan`);

                    // TAB DATA PASSPORT
                    $("#mb_nm_psp").val(dataPassport.jemaah_passport_id);
                    showDate('mb_tgl_psp', dataPassport.jemaah_passport_tanggal_mulai);
                    $("#mb_tmp_psp").val(dataPassport.jemaah_passport_tempat_terbit);
                    showDate('mb_tgl_psp_exp', dataPassport.jemaah_passport_tanggal_akhir);
                    $("#mb_npwp_num").val(dataPassport.jemaah_npwp);
                    $("#mb_npwp_name").val(dataPassport.jemaah_npwp_nama);
                    showSelect('mb_npwp_stat', [], dataPassport.jemaah_npwp_status);

                    // GET DATA KOTA
                    if(dataAlamat.jemaah_provinsi_id != '') {
                        const ctyURL    = base_url + "/umhaj/master/data_wilayah/kota";
                        const ctyType   = "GET";
                        const ctyData   = {
                            "province_id"   : dataAlamat.jemaah_provinsi_id,
                        };

                        doTransaction(ctyURL, ctyType, ctyData, "", true)
                            .then((res) => {
                                showSelect('mb_pl_ct', res.data, dataAlamat.jemaah_kota_id);
                            })
                            .catch((err)=> {
                                showSelect('mb_pl_ct', [], '');
                            })
                    } else {
                        showSelect('mb_pl_ct', [], '');
                    }
                    // GET DATA KECAMATAN
                    if(dataAlamat.jemaah_kota_id != '') {
                        const kctURL    = base_url + "/umhaj/master/data_wilayah/kecamatan";
                        const kctType   = "GET";
                        const kctData   = {
                            "city_id"       : dataAlamat.jemaah_kota_id,
                        };

                        doTransaction(kctURL, kctType, kctData, "", true)
                            .then((res) => {
                                showSelect('mb_pl_kc', res.data, dataAlamat.jemaah_kecamatan_id);
                            })
                            .catch(((err)   => {
                                showSelect('mb_pl_kc', [], '')
                            }))
                    } else {
                        showSelect('mb_pl_kc', [], '');
                    }
                    // GET DATA KELURAHAN
                    if(dataAlamat.jemaah_kecamatan_id != '') {
                        const klrURL    = base_url + "/umhaj/master/data_wilayah/kelurahan";
                        const klrType   = "GET";
                        const krlData   = {
                            "district_id"   : dataAlamat.jemaah_kecamatan_id,
                        };

                        doTransaction(klrURL, klrType, krlData, '', true)
                            .then((res)     => {
                                showSelect('mb_pl_kl', res.data, dataAlamat.jemaah_kelurahan_id)
                            })
                            .catch((err)    => {
                                showSelect('mb_pl_kl', [], '')
                            })
                    } else {
                        showSelect('mb_pl_kl', [], '');
                    }

                    $("#mb_adr").val(dataAlamat.jemaah_alamat);
                    $("#mb_adr_ltr").val(dataAlamat.jemaah_alamat_surat);
                    showSelect('mb_pl_prv', dataProvinsi, dataAlamat.jemaah_provinsi_id);
                    showSelect('mb_pl_ct', [], dataAlamat.jemaah_kota_id);
                    showSelect('mb_pl_kc', [], dataAlamat.jemaah_kecamatan_id);
                    showSelect('mb_pl_kl', [], dataAlamat.jemaah_kelurahan_id);
                    $("#mb_pl_rt").val(dataAlamat.jemaah_rt);
                    $("#mb_pl_rw").val(dataAlamat.jemaah_rw);
                    $("#mb_pl_pc").val(dataAlamat.jemaah_kode_pos);
                    $("#mb_ct_tlp").val(dataAlamat.jemaah_no_telp);
                    $("#mb_ct_hp").val(dataAlamat.jemaah_no_hp);
                    $("#mb_ct_em").val(dataAlamat.jemaah_email);
                    showSelect('mb_sz_bd', [], dataAlamat.jemaah_ukuran_baju);

                    // TAB PEKERJAAN
                    showSelect('mb_job', [], dataPekerjaan.jemaah_pekerjaan);
                    $("#mb_cp_name").val(dataPekerjaan.jemaah_nama_perusahaan);
                    showSelect('mb_acd', [], dataPekerjaan.jemaah_pendidikan);
                    showSelect('mb_sc', dataSumber, dataPekerjaan.jemaah_sumber_informasi);
                    showSelect('mb_cs_name', dataCS, dataPekerjaan.jemaah_customer_service);


                    // TAB CATATAN
                    $("#mb_dsc").val(catatan);

                    $("#"+idModal).on('shown.bs.modal', () => {
                        $("#mb_nm_dp").focus();
                    })
                })
                .catch((err)        => {
                    Swal.close();
                    console.log(err);
                })
        }

        closeModal('modal_member_all');
    } else if(idModal == 'modal_delete_member') {
        Swal.fire({
            icon    : 'question',
            title   : 'Hapus Data',
            text    : 'Anda ingin menghapus data member ini?',
            showConfirmButton   : true,
            showCancelButton    : true,
            confirmButtonText   : 'Ya, Hapus',
            cancelButtonText    : 'Batal',
            confirmButtonColor  : '#ED5565',
            cancelButtonColor   : '#6c757d',
        });
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_member') {
        $("#"+idModal).modal('hide');
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#modal_member_title_month").html("");
            $("#table_modal_total_member_footer_total").html(0);
            $("#table_modal_member_footer_total").html(0);
        })
    } else if(idModal == 'modal_umrah') {
        $("#"+idModal).modal('hide');
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#table_modal_umrah_total").html(0);
            $("#modal_umrah_title_month").html("");
        })
    } else if(idModal == 'modal_list_umrah') {
        $("#"+idModal).modal('hide');
        clearUrl();
        $("#"+idModal).on('hidden.bs.modal', () => {
            
        })
    } else if(idModal == 'modal_list_umrah_detail') {
        $("#"+idModal).modal('hide');
        showModal('modal_list_umrah');
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#umrah_list_detail_tour_code").html();
            $("#umrah_list_detail_date").html();
            $("#umrah_list_detail_mentor").html();
            $("#table_modal_list_umrah_detail_total_banyaknya").html(0);
        })
    } else if(idModal == 'modal_agent') {
        $("#"+idModal).modal('hide');
        clearUrl();
    } else if(idModal == 'modal_member_all') {
        $("#"+idModal).modal('hide');
        clearUrl();
    } else if(idModal == 'modal_form_member') {
        $("#"+idModal).modal('hide');
        showModal('modal_member_all', '');

        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#nav_data_pribadi").removeClass('active');
            $("#nav_data_passport").removeClass('active');
            $("#nav_data_alamat").removeClass('active');
            $("#nav_data_pekerjaan").removeClass('active');
            $("#nav_data_keterangan").removeClass('active');

            $("#tab_data_pribadi").removeClass('active show');
            $("#tab_data_passport").removeClass('active show');
            $("#tab_data_alamat").removeClass('active show');
            $("#tab_data_pekerjaan").removeClass('active show');
            $("#tab_data_keterangan").removeClass('active show');

            // RESET FORM
            $("#mb_nm_dp").val('');
            $("#mb_nm_tg").val('');
            $("#mb_nm_bl").val('');
            $("#mb_nm").val('');
            $("#mb_nm_ay").val('');
            $("#mb_nik").val('');
            $("#mb_tmp_lh").val('');
            showDate('mb_tgl_lh', '');
            $("input[type='radio'][name='mb_jk']").prop('checked', false);
            $("input[type='radio'][name='mb_st']").prop('checked', false);
            showSelect('mb_nm_mhr', [], '');
            showSelect('mb_st_mhr', [], '');

            $("#mb_nm_psp").val('');
            showDate('mb_tgl_psp', '');
            $("#mb_tmp_psp").val('');
            showDate('mb_tgl_psp_exp', '');
            $("#mb_npwp_num").val('');
            $("#mb_npwp_name").val('');
            showSelect('mb_npwp_stat', [], '');

            $("#mb_adr").val('');
            $("#mb_adr_ltr").val('');
            showSelect('mb_pl_prv', [], '');
            showSelect('mb_pl_ct', [], '');
            showSelect('mb_pl_kc', [], '');
            showSelect('mb_pl_kl', [], '');
            $("#mb_pl_rt").val('');
            $("#mb_pl_rw").val('');
            $("#mb_pl_pc").val('');
            $("#mb_ct_tlp").val('');
            $("#mb_ct_hp").val('');
            $("#mb_ct_em").val('');
            showSelect('mb_sz_bd', [], '');

            showSelect('mb_job', [], '');
            $("#mb_cp_name").val('');
            showSelect('mb_acd', [], '');
            showSelect('mb_sc', [], '');
            showSelect('mb_cs_name', [], '');

            $("#mb_dsc").val('');

            $("#mb_tgl_ag").addClass('d-none');
            $("#mb_tgl_ag").text('');
        });
    }
}

function cariData(idForm, data)
{
    if(idForm == 'chart_umrah')
    {
        const umrahURL      = base_url + "/umhaj/umrah/get_data_chart_umrah";
        const umrahType     = "POST";
        const umrahSendData     = {
            "jenis"     : data,
            "tahun_cari": moment(today).format('YYYY'),
            "bulan_cari": "",
        };

        $("#chart_umrah_loading").removeClass('d-none');
        $("#chart_umrah_view").addClass('d-none');

        doTransaction(umrahURL, umrahType, umrahSendData, "", true)
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
        // LOADING
        $("#chart_member_loading").removeClass('d-none');
        $("#chart_member_view").addClass('d-none');

        // GET DATA
        const memberURL         = base_url + "/umhaj/member/get_data_chart_member";
        const memberType        = "POST";
        const memberData        = {
            "cs_name"           : data,
            "tahun_cari"        : moment(today, 'YYYY-MM-DD').format('YYYY'),
            "bulan_cari"        : "",
        };
        doTransaction(memberURL, memberType, memberData, '', true)
            .then((success)     => {
                const memberGetData     = success.data;
                const memberSendData    = [];
                for(const item of memberGetData) {
                    memberSendData.push(item['total_data']);
                }

                $("#chart_member_loading").addClass('d-none');
                $("#chart_member_view").removeClass('d-none');
                showChart('chart_member', memberSendData);
            })
            .catch((err)        => {
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
            autoWidth       : false,
            columnDefs      : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "30%" },
                { "targets" : [2], "className" : "text-left align-middle" },
                { "targets" : [3], "className" : "text-left align-middle", "width" : "30%" },
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
    } else if(idTable == 'tableModalMemberAll') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : `Tidak Ada Data Yang Bisa Dimuat`,
                zeroRecords : `Data Yang Dicari Tidak Ditemukan`,
                processing  : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat..`
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "25%" },
                { "targets" : [2], "className" : "text-left align-middle", "width" : "18%" },
                { "targets" : [3], "className" : "text-left align-middle" },
                { "targets" : [4], "className" : "text-center align-middle", "width" : "10%" },
            ],
            processing  : true,
            serverSide  : true,
            ordering    : false,
            ajax        : {
                url     : base_url + "/umhaj/member/get_data_member_v2", 
                headers : {
                    'X-CSRF-TOKEN'  : CSRF_TOKEN,
                },
                type    : "POST",
                async   : true
            },
        });
        
        $(".dataTables_wrapper").css('padding-bottom', '0px');
    }
}

function generateAddressLetter()
{
    let alamat      = $("#mb_adr").val() != '' ? $("#mb_adr").val()+", " : '';
    let provinsi    = $("#mb_pl_prv option:selected").text() != 'Pilih Provinsi' ? $("#mb_pl_prv option:selected").text(): '';
    let kota        = $("#mb_pl_ct option:selected").text() != 'Pilih Kota / Kabupaten' ? $("#mb_pl_ct option:selected").text()+", " : '';
    let kecamatan   = $("#mb_pl_kc option:selected").text() != 'Pilih Kecamatan' ? "Kec. " + $("#mb_pl_kc option:selected").text()+", " : '';
    let kelurahan   = $("#mb_pl_kl option:selected").text() != 'Pilih Kelurahan' ? "Kel. " + $("#mb_pl_kl option:selected").text()+", " : '';
    let rt          = $("#mb_pl_rt").val() != '' ? "RT. " + $("#mb_pl_rt").val()+", " : '';
    let rw          = $("#mb_pl_rw").val() != '' ? "RW. " + $("#mb_pl_rw").val()+", " : '';
    let kodePos     = $("#mb_pl_pc").val() != '' ? " ("+ $("#mb_pl_pc").val() +")" : '';

    let text        = `${alamat}${rt}${rw}${kelurahan}${kecamatan}${kota}${provinsi}${kodePos}`;

    $("#mb_adr_ltr").val(text);
}

function clearUrl()
{
    var url     = window.location.href;
    var cleanUrl= url.split('#')[0];
    window.history.replaceState({}, document.title, cleanUrl);
}

function doSimpan(idForm, data)
{
    if(idForm == 'member') {
        // VALIDASI
        let dataPribadi_namaDepan    = $("#mb_nm_dp");
        let dataPribadi_namaTengah   = $("#mb_nm_tg");
        let dataPribadi_namaBelakang = $("#mb_nm_bl");
        let dataPribadi_namaPenuh    = $("#mb_nm");
        let dataPribadi_namaAyah     = $("#mb_nm_ay");
        let dataPribadi_nik          = $("#mb_nik");
        let dataPribadi_tempatLahir  = $("#mb_tmp_lh");
        let dataPribadi_tanggalLahir = $("#mb_tgl_lh");
        let dataPribadi_jenisKelamin = $("input[type='radio'][name='mb_jk']:checked");
        let dataPribadi_status       = $("input[type='radio'][name='mb_st']:checked");
        let dataPribadi_namaMahram   = $("#mb_nm_mhr");
        let dataPribadi_statusMahram = $("#mb_st_mhr");

        if(dataPribadi_namaDepan.val() == '') {
            Swal.fire({
                icon    : 'warning',
                title   : 'Terjadi Kesalahan',
                text    : 'Kolom Nama Depan Tidak Boleh Kosong',
                didClose    : () => {
                    dataPribadi_namaDepan.focus()
                }
            })
        } else if(dataPribadi_namaPenuh.val() == '') {
            Swal.fire({
                icon    : 'warning',
                title   : 'Terjadi Kesalahan',
                text    : 'Kolom Nama Tidak Boleh Kosong',
                didClose    : () => {
                    dataPribadi_namaPenuh.focus()
                }
            })
        } else if(dataPribadi_nik.val() == '') {
            Swal.fire({
                icon    : 'warning',
                title   : 'Terjadi Kesalahan',
                text    : 'Kolom NIK Tidak Boleh Kosong',
                didClose    : () => {
                    dataPribadi_nik.focus()
                }
            })
        } else {
            // SIMPAN DATA
            const memberURL     = base_url + "/umhaj/member/simpan_data_jemaah/"+data;
            const memberType    = "POST";
            const memberMsg     = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
            const memberData    = {
                "data_pribadi"  : {
                    "nama_depan"    : dataPribadi_namaDepan.val(),
                    "nama_tengah"   : dataPribadi_namaTengah.val(),
                    "nama_belakang" : dataPribadi_namaBelakang.val(),
                    "nama_penuh"    : dataPribadi_namaPenuh.val(),
                    "nama_ayah"     : dataPribadi_namaAyah.val(),
                    "nik"           : dataPribadi_nik.val(),
                    "tempat_lahir"  : dataPribadi_tempatLahir.val(),
                    "tanggal_lahir" : dataPribadi_tanggalLahir.val(),
                    "jenis_kelamin" : dataPribadi_jenisKelamin.val(),
                    "status"        : dataPribadi_status.val(),
                    "mahram"        : dataPribadi_namaMahram.val(),
                    "status_mahram" : dataPribadi_statusMahram.val()
                }
            }

            console.log(memberData);

            doTransaction(memberURL, memberType, [], memberMsg, true)
                .then((res)     => {
                    Swal.close();
                    console.log(res);
                })
                .catch((err)    => {
                    Swal.close();
                    console.log(err);
                })
        }
    }
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
            url             : "http://localhost:3001/"+url,
            // url             : 'https://apiv2.perciktours.com/'+url,
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