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
            // new Chart(ctx, {
            //     type    : 'pie',
            //     data    : chartData,
            //     options : {
            //         title   : {
            //             display     : true,
            //             text        : 'Total Member Baru Per Januari 2024',
            //         }
            //     }
            // });
        }
    }
}

$(document).ready(function(){
    // GET DATA PROGRAM UMRAH
    const prog_umrah_url    = base_url + "/umhaj/umrah/list_program";
    const prog_umrah_type   = "GET";
    const prog_umrah_data   = [];

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

    // GET DATA CS
    const cs_url        = base_url + "/umhaj/cs/get_data";
    const cs_type       = "GET";
    const cs_data       = "";

    const send_data     = [
        doTransaction(umrah_url, umrah_type, umrah_data, '', true),
        doTransaction(prog_umrah_url, prog_umrah_type, prog_umrah_data, '', true),
        doTransaction(member_url, member_type, member_data, '', true),
        doTransaction(cs_url, cs_type, cs_data, '', true)
    ];

    Promise.allSettled(send_data)
        .then((success)     => {
            // UMRAH ZONE
            const umrah_getData     = success[0].value.data;
            const umrah_sendData    = [];
            for(let i = 0; i < umrah_getData.length; i++) {
                umrah_sendData.push(umrah_getData[i]['total_data']);
            }
            $("#chart_umrah_loading").addClass('d-none');
            $("#chart_umrah_view").removeClass('d-none');
            showChart('chart_umrah', umrah_sendData);

            // MEMBER ZONE
            const member_getData    = success[2].value.data;
            const member_sendData   = [];
            for(let i = 0; i < member_getData.length; i++) {
                member_sendData.push(member_getData[i]['total_data']);
            }
            $("#chart_member_loading").addClass('d-none');
            $("#chart_member_view").removeClass('d-none');
            showChart('chart_member', member_sendData);

            // SHOW SELECT
            const prog_umrah_sendData   = success[1].value.data;
            const cs_sendData           = success[3].value.data;
            showSelect('g_umrah_filter_package', prog_umrah_sendData, 'semua');
            showSelect('g_member_filter_cs', cs_sendData, 'semua');
        })
        .catch((error)      => {
            console.log(error)
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
            $.each(data, (i, item)  => {
                html    += "<option value='" + item.prog_name + "'>" + item.prog_name.toUpperCase() + "</option>";
            });

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
                html    += "<option value='" + item.cs_name + "'>" + item.cs_name + "</option>";
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
                Swal.close();
                $("#modal_umrah_title_month").html(moment(data['bulan_ke'], 'MM').format('MMMM'));
                const umrah_getData     = success.data;
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

                showTable('table_modal_umrah', umrah_getData);

                showTable('table_modal_umrah_summary', umrah_getData);
            })
            .catch((error)        => {
                console.log(error)
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak Ada Data Yang Bisa Dimuat',
                });
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
    }
}

function cariData(idForm, data)
{
    if(idForm == 'chart_umrah')
    {
        // GET DATA UMRAH
        const umrah_url     = base_url + "/umhaj/umrah/get_data";
        const umrah_type    = "GET";
        const umrah_data    = {
            "data"      : {
                "jenis"     : data,
                "tahun_cari": moment(today).format('YYYY'),
                "bulan_cari": "",
            }
        };
        doTransaction(umrah_url, umrah_type, umrah_data, '', true)
            .then((success)     => {
                const umrah_getData     = success.data;
                const umrah_sendData    = [];
                for(let i = 0; i < umrah_getData.length; i++) {
                    umrah_sendData.push(umrah_getData[i]['total_data']);
                }
                showChart('chart_umrah', umrah_sendData);
            })
            .catch((error)      => {
                console.log(error)
                showChart('chart_umrah', '');
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
                    seq++,
                    dataKategoriUmrah[i]['kategori_nama'],
                    dataKategoriUmrah[i]['total_data'],
                ]).draw(false);
                
                grandTotal  += dataKategoriUmrah[i]['total_data'];
            }
            $("#table_modal_umrah_total").html(grandTotal);
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