$(document).ready(function () {

    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const query = document.URL;
    const id = query.substring(query.lastIndexOf("/") + 1);

    let startDate = '';
    let endDate = '';


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

    const callApi = async (startDate, endDate) => {
        try {

            const response = await fetch('/marketings/pencapaian/bulanan', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    id: id,
                    start: startDate,
                    end: endDate
                })
            });
            if (!response.ok) {
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

    const initialAllTotal = async (startDate, endDate) => {
        try {
            $('#totalTarget').empty();
            $('#totalRealisasi').empty();
            $('#totalSelisih').empty();
            $('#totalPersentage').empty();
            showLoading('graph-container-totalTarget');
            showLoading('graph-container-totalRealisasi');
            showLoading('graph-container-totalSelisih');
            showLoading('graph-container-totalPersentage');
            const responses = await callApi(startDate, endDate);
            closeLoading('graph-container-totalTarget')
            closeLoading('graph-container-totalRealisasi')
            closeLoading('graph-container-totalSelisih')
            closeLoading('graph-container-totalPersentage')
            $('#totalTarget').text(responses.data.total_target);
            $('#totalRealisasi').text(responses.data.total_realisasi);
            $('#totalSelisih').text(responses.data.total_selisih);
            $('#totalPersentage').text(`${responses.data.persentage_total_pencapaian} %`);
        } catch (error) {
            console.log(error);
        }
    }


    const initialGrafikJamaahPerPic = async (startDate, endDate) => {

        try {
            $('#jamaahperpic').remove();
            showLoading('graph-container-jamaahperpic');
            const responses = await callApi(startDate, endDate);
            closeLoading('graph-container-jamaahperpic')
            $('#graph-container-jamaahperpic').append('<canvas id="jamaahperpic"  width="100"></canvas>');
            createChart('jamaahperpic', 'bar', responses.data.chart_umrah_per_pic);
        } catch (error) {
            console.log(error);
        }
    }

    const initialGrafikJamaahPerbulan = async (startDate, endDate) => {
        try {
            $('#jamaahperbulan').remove();
            showLoading('graph-container-jamaahperbulan');
            const responses = await callApi(startDate, endDate);
            closeLoading('graph-container-jamaahperbulan')
            $('#graph-container-jamaahperbulan').append('<canvas id="jamaahperbulan"  width="100"></canvas>');
            createChart('jamaahperbulan', 'bar', responses.data.chart_umrah_bulan);
        } catch (error) {
            console.log(error);
        }

    }

    const initialGrafikJamaahPerProgram = async (startDate, endDate) => {
        try {
            $('#jamaahperprogram').remove();
            showLoading('graph-container-jamaahperprogram');
            const responses = await callApi(startDate, endDate);
            closeLoading('graph-container-jamaahperprogram')
            $('#graph-container-jamaahperprogram').append('<canvas id="jamaahperprogram"  width="100"></canvas>');
            createChart('jamaahperprogram', 'bar', responses.data.chart_umrah_program);
        } catch (error) {
            console.log(error);
        }
    }

    initialGrafikJamaahPerProgram(startDate, endDate);
    initialGrafikJamaahPerPic(startDate, endDate);
    initialGrafikJamaahPerbulan(startDate, endDate);
    initialAllTotal(startDate, endDate);

    $('.month-start').datepicker({
        minViewMode: 1,
        keyboardNavigation: false,
        forceParse: false,
        forceParse: false,
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy'
    });

    $('.month-end').datepicker({
        minViewMode: 1,
        keyboardNavigation: false,
        forceParse: false,
        forceParse: false,
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy'
    });
    const submitRangeDatePerPic = $('#submitRangeDatePerPic');
    submitRangeDatePerPic.click(function (e) {
        e.preventDefault();
        startDate = $('#month-start').val();
        endDate = $('#month-end').val();
        initialGrafikJamaahPerbulan(startDate, endDate);
        initialGrafikJamaahPerProgram(startDate, endDate);
        initialGrafikJamaahPerPic(startDate, endDate);
        initialAllTotal(startDate, endDate);

    });
});