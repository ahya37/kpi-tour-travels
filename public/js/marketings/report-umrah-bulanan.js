$(document).ready(function(){	
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const query = document.URL;
    const id = query.substring(query.lastIndexOf("/") + 1);

    const initialGrafikJamaahPerbulan = (data) => {
          
        // referesh chart jika ada order data
        // $('#jamaahperbulan').remove();
        // $('#graph-container-jamaahperbulan').append('<canvas id="jamaahperbulan" height="70"></canvas>');
    
        let barOptions = {
            responsive: true,
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    formatter: function(value, context) {
                        return value + "%"; 
                    }
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    },
                     stacked: true // Enable stacking for y-axis
                }],
                xAxes: [{
                    ticks: {
                        beginAtZero: true
                    },
                     stacked: true // Enable stacking for y-axis
                    
                }]
            }
        };
    
        const ctx3  = document.getElementById("jamaahperbulan").getContext("2d");
       new Chart(ctx3,{
                            type: 'bar', 
                            data: data, 
                            options:barOptions
                     });
        
    }

    const initialGrafikJamaahPerProgram = (data) => {
          
        // referesh chart jika ada order data
        // $('#jamaahperbulan').remove();
        // $('#graph-container-jamaahperbulan').append('<canvas id="jamaahperbulan" height="70"></canvas>');
    
        let barOptions = {
            responsive: true,
            plugins: {
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    formatter: function(value, context) {
                        return value + "%"; 
                    }
                }
            },
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true
                    },
                     stacked: true // Enable stacking for y-axis
                }],
                xAxes: [{
                    ticks: {
                        beginAtZero: true
                    },
                     stacked: true // Enable stacking for y-axis
                    
                }]
            }
        };
    
        const ctx3  = document.getElementById("jamaahperprogram").getContext("2d");
       new Chart(ctx3,{
                            type: 'bar', 
                            data: data, 
                            options:barOptions
                     });
        
    }
    
    const fetchApi = () => {
        Swal.fire({
            title   : 'Menampilkan data',
        });

        Swal.showLoading();

        fetch('/marketings/pencapaian/bulanan', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken // Menggunakan token CSRF dalam header X-CSRF-TOKEN
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
            initialGrafikJamaahPerbulan(data.data.chart_umrah_program);
            initialGrafikJamaahPerProgram(data.data.chart_umrah_bulan);
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