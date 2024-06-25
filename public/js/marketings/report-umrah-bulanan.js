
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

   function callAPI(data){
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
            id: id,
            start: startDate,
            end: endDate
        })

    }).then(response => {

        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        Swal.close();
        return response.json();

    })
    }

    // hello()

   function sayHello() {
       callAPI('eko')
   }

   sayHello();

   
    
    function  initialGrafikJamaahPerbulan() {
    
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
                id: id,
                start: startDate,
                end: endDate
            })

        }).then(response => {

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            Swal.close();
            return response.json();

        }).then(data => {

            const results = data.data.chart_umrah_bulan;
            // initialGrafikJamaahPerPic(results.chart_umrah_per_pic);
            createChart('jamaahperbulan', 'bar', results);
            
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

    initialGrafikJamaahPerbulan();

    const initialGrafikJamaahPerProgram = () => {
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
                id: id,
                start: startDate,
                end: endDate
            })

        }).then(response => {

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            Swal.close();
            return response.json();
        }).then(data => {

            const results = data.data.chart_umrah_program;
            // initialGrafikJamaahPerPic(results.chart_umrah_per_pic);
            createChart('jamaahperprogram', 'bar', results);
            
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

    initialGrafikJamaahPerProgram();

    const initialGrafikJamaahPerPic = () => {

        $('#jamaahperpic').remove();
		$('#graph-container-jamaahperpic').append('<canvas id="jamaahperpic" height="70"></canvas>');
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
                id: id,
                start: startDate,
                end: endDate
            })

        }).then(response => {

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            Swal.close();
            return response.json();
        }).then(data => {

            const results = data.data.chart_umrah_per_pic;
            // initialGrafikJamaahPerPic(results.chart_umrah_per_pic);
            createChart('jamaahperpic', 'bar', results);

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

    initialGrafikJamaahPerPic();

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
                id: id,
                start: startDate,
                end: endDate
            })

        }).then(response => {

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            Swal.close();
            return response.json();
        }).then(data => {

            const results = data.data;
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
    });
