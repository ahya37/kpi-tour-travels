$(document).ready(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const query = document.URL;
    const id = query.substring(query.lastIndexOf("/") + 1);

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

    const initialGrafikJamaahPerbulan = (data) => {
        createChart('jamaahperbulan','bar', data);
    }

    const initialGrafikJamaahPerProgram = (data) => {
        createChart('jamaahperprogram','bar', data);
    }
    const initialGrafikJamaahPerPic = (data) => {
        createChart('jamaahperpic','bar', data);
    }

    const fetchApi = () => {
        Swal.fire({
            title: 'Menampilkan data',
        });

        Swal.showLoading();

        fetch('/marketings/pencapaian/bulanan', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                id: id
            })
        }).then(response => {

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            Swal.close();
            return response.json();

        }).then(data => {
            const results = data.data;
            initialGrafikJamaahPerbulan(results.chart_umrah_program);
            initialGrafikJamaahPerProgram(results.chart_umrah_bulan);
            initialGrafikJamaahPerPic(results.chart_umrah_per_pic);
        }).catch(error => {
            Swal.close();
            Swal.fire({
                title: "Gagal!",
                position: "center",
                type: "danger",
                text: error.responseJSON.data.message,
                showConfirmButton: false,
                width: 500,
                timer: 900,
            });
        }).finally(() => {
            Swal.close();
        });
    }

    fetchApi();
});