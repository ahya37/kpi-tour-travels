$(document).ready(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

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

            const response = await fetch('/marketings/rencanakerja/report/data', {
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

    const intialShowtable = async (month, year) => {

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
            const responses = await callApi(month, year);
            $(`.spiner-example`).remove();
            $('#dataBody').append(responses.data.rencanakerja);
            $('#title').text('Daftar Rencana Kerja Marketing Bulan '+responses.data.bulan)
        } catch (error) {
            console.log(error);
        }
    }

    $('#submitFilter').click(function () {
        const date = $('#month-start').val();
        // date = $('#date').val();
        const dateParts = date.split("-");
        const month = dateParts[1]; // 06
        const year = dateParts[2];  // 2024

        intialShowtable(month, year);
    });
    // $('#submitClear').click(function () {
    //     createdBy = '';
    //     // date = $('#date').val();
    //     intialShowtable();
    // });

})



