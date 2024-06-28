$(document).ready(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    console.log(csrfToken);
    $(".tahunHaji").select2({
        theme: "bootstrap4",
        width: $(this).data("width")
            ? $(this).data("width")
            : $(this).hasClass("w-100")
                ? "100%"
                : "style",
        placeholder: "Pilih Tahun",
        allowClear: Boolean($(this).data("allow-clear")),
        containerCssClass: "form-control",
        ajax: {
            dataType: "json",
            url: `${percikUrl}/haji/tahun/list`,
            delay: 250,
            headers: {
                'x-api-key': percikKey
            },
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            text: item.year,
                            id: item.year,
                        };
                    }),
                };
            },
        },
    });

    const optionsChart = () => {
        const barOptions = {
            responsive: true,
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    formatter: function (value, context) {
                        return value;
                    }
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    },
                    stacked: true
                }],
                xAxes: [{
                    ticks: {
                        beginAtZero: true
                    },
                    stacked: true

                }]
            }
        };

        return barOptions;
    }

    const createChart = (elementId, type, data) => {
        const ctx = document.getElementById(elementId).getContext("2d");
        new Chart(ctx, {
            type: type,
            data: data,
            options: optionsChart()
        });
    }

    const callApi = async (year) => {
        try {

            const response = await fetch(`${percikUrl}/haji/realisasi/report`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'x-api-key': percikKey
                },
                body: JSON.stringify({
                    year: year
                })
            });
            if (!response.ok) {
                Swal.fire({
                    title: "Gagal, Pilih terlebih dahulu !",
                    position: "center",
                    type: "danger",
                    text: '',
                    showConfirmButton: false,
                    width: 500,
                    timer: 1500,
                });
                throw new Error('Network response was not ok');
            }
            const data = await response.json();
            return data;
        } catch (error) {
            throw error;
        }
    };

    const showLoading = (element) => {
        $(`#${element}`).append(
            ` <div class="spiner-example-${element}">
                            <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                </div>`
        )
    }

    const closeLoading = (classElement) => {
        $(`.spiner-example-${classElement}`).remove();
    }

    const initialGrafikPerbulan = async (year) => {
        try {
            $('#jamaahperbulan').remove();
            showLoading('graph-container-jamaahperbulan');
            const responses = await callApi(year);
            closeLoading('graph-container-jamaahperbulan')
            $('#graph-container-jamaahperbulan').append('<canvas id="jamaahperbulan"  width="100"></canvas>');
            createChart('jamaahperbulan', 'bar', responses.data.chart_haji_bulan);
        } catch (error) {
            closeLoading('graph-container-jamaahperbulan')
        }
    }

    const initialGrafikPerPic = async (year) => {
        try {
            showLoading('graph-container-jamaahperpic');
            $('#jamaahperpic').remove();
            const responses = await callApi(year);
            closeLoading('graph-container-jamaahperpic')
            $('#graph-container-jamaahperpic').append('<canvas id="jamaahperpic"  width="100"></canvas>');
            createChart('jamaahperpic', 'bar', responses.data.chart_haji_pic);
        } catch (error) {
            closeLoading('graph-container-jamaahperpic')
        }
    }

    const initialGrafikPerProgram = async (year) => {
        try {
            showLoading('graph-container-jamaahperprogram');
            $('#jamaahperprogram').remove();
            const responses = await callApi(year);
            closeLoading('graph-container-jamaahperprogram')
            $('#graph-container-jamaahperprogram').append('<canvas id="jamaahperprogram"  width="100"></canvas>');
            createChart('jamaahperprogram', 'bar', responses.data.chart_haji_program);
        } catch (error) {
            closeLoading('graph-container-jamaahperprogram')
        }
    }

    const populateTable = (data) => {
        const tableBody = $('#dataBody');
        tableBody.empty(); // Bersihkan isi tbody sebelum menambahkan data baru

        // Loop melalui data dan tambahkan ke dalam tabel
        $.each(data, function (index, row) {
            const newRow = '<tr>' +
                '<td align="center">' + row.no + '</td>' +
                '<td >' + row.month + '</td>' +
                '<td align="right">' + row.target + '</td>' +
                '<td align="right">' + row.realisasi_jamaah + '</td>' +
                '<td align="right">' + row.selisih + '</td>' +
                '<td align="right">' + row.persentage + '</td>' +
                '</tr>';
            tableBody.append(newRow);
        });

    }

    const initialTable = async (year) => {
        try {
            $('#dataBody').empty();
            $('#divLoading').append(`
                <div class="col text-center">
                     <div class="spiner-example">
                            <div class="sk-spinner sk-spinner-wave">
                                <div class="sk-rect1"></div>
                                <div class="sk-rect2"></div>
                                <div class="sk-rect3"></div>
                                <div class="sk-rect4"></div>
                                <div class="sk-rect5"></div>
                            </div>
                    </div>
                </div>
                `)
            const responses = await callApi(year);
            $(`.spiner-example`).remove();
            const dataFooter = $('#dataFooter');
            dataFooter.empty(); // Bersihkan isi tbody sebelum menambahkan data baru
            const newFooterData = `
                                <tr>
                                    <th style=" text-align: right;" colspan="2">Jumlah</th>
                                    <th style=" text-align: right;">${responses.data.total_target_jamaah}</th>
                                    <th style=" text-align: right;">${responses.data.total_realisasi_jamaah}</th>
                                    <th style=" text-align: right;">${responses.data.total_selisih_jamaah}</th>
                                    <th style=" text-align: right;">${responses.data.total_persentaese_jamaah}</th>
                                </tr>
                            `;
            dataFooter.append(newFooterData)

            populateTable(responses.data.haji);

        } catch (error) {
        }
    }

    const initialAllTotal = async (year) => {
        try {
            $('#totalTarget').empty();
            $('#totalRealisasi').empty();
            $('#totalSelisih').empty();
            $('#totalPersentage').empty();
            showLoading('graph-container-totalTarget');
            showLoading('graph-container-totalRealisasi');
            showLoading('graph-container-totalSelisih');
            showLoading('graph-container-totalPersentage');
            const responses = await callApi(year);
            closeLoading('graph-container-totalTarget')
            closeLoading('graph-container-totalRealisasi')
            closeLoading('graph-container-totalSelisih')
            closeLoading('graph-container-totalPersentage')
            $('#totalTarget').text(responses.data.total_target_jamaah);
            $('#totalRealisasi').text(responses.data.total_realisasi_jamaah);
            $('#totalSelisih').text(responses.data.total_selisih_jamaah);
            $('#totalPersentage').text(`${responses.data.total_persentaese_jamaah} %`);
        } catch (error) {
            console.log(error);
        }
    }

    $('#submitFilter').click(function () {
        const year = $('#tahunHaji').val();
        initialGrafikPerbulan(year);
        initialGrafikPerPic(year);
        initialTable(year);
        initialAllTotal(year);
        initialGrafikPerProgram(year);
    });


   
});

