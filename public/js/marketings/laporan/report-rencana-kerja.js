$(document).ready(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let currentMonth = '';

    $('.month-start').datepicker({
        minViewMode: 1,
        keyboardNavigation: false,
        forceParse: false,
        forceParse: false,
        autoclose: true,
        todayHighlight: true,
        format: 'dd-mm-yyyy'
    });


    $(".select2_demo_2").select2({
        theme: 'bootstrap4',
    });

    const callApi = async (month, year) => {
        try {

            const response = await fetch('/marketings/report/evaluasi', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    year: year,
                    month: month
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

    const  showTable = (idTable, year, month) => {
        $("#" + idTable).DataTable().clear().destroy();
        if (idTable == 'datatable') {
            $("#" + idTable).DataTable({
                language: {
                    "zeroRecords": "Data Tidak ada, Silahkan tambahkan beberapa data..",
                    "emptyTable": "Data Tidak ada, Silahkan tambahkan beberapa data..",
                    "processing": "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                },
                processing: true,
                serverSide: false,
                ajax: {
                    type: "POST",
                    dataType: "json",
                    url: `/marketings/report/evaluasi`,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: function (d) {
                        d.year = year;
                        d.month = month;
                    },
                    dataSrc: function (json) {
                        currentMonth = json.data.data; // Mengambil nilai bulan
                        return json.data.rencanakerja.data; // Mengakses data di dalam kunci 'data'
                    }
                },
                autoWidth: false,
                columnDefs:
                    [
                        { "targets": [0], "className": "text-center", "width": "5%" },
                        { "targets": [1], "width": "30%" },
                        { "targets": [2], "className": "text-right", "width": "10%" },
                        { "targets": [3], "width": "8%","className": "text-right" },
                        { "targets": [4], "className": "text-right", "width": "8%" },
                        { "targets": [5], "className": "text-right", "width": "8%" },
                        { "targets": [6], "className": "text-center", "width": "8%" },
                    ],
                    initComplete: function(settings, json) {
                        $('#title').text('Daftar Rencana Kerja Marketing Bulan ' + json.data.bulan)

                    }
            });
        }
    }
    

    $('#submitFilter').click(function () {
        const date = $('#month-start').val();
        // date = $('#date').val();
        const dateParts = date.split("-");
        const month = dateParts[1]; // 06
        const year = dateParts[2];  // 2024

        // intialShowtable(month, year);
        showTable('datatable', year, month);

    });
    
    // $('#submitClear').click(function () {
    //     createdBy = '';
    //     // date = $('#date').val();
    //     intialShowtable();
    // });
})


