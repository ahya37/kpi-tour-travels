$(document).ready(function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    $('#data_1 .input-group.date').datepicker({
        todayBtn: "linked",
        keyboardNavigation: false,
        forceParse: false,
        calendarWeeks: true,
        autoclose: true,
        format: 'dd-mm-yyyy'
    });

    let createdBy = '';
    let date = '';

    $(".select2_demo_2").select2({
        theme: 'bootstrap4',
    });

    const callApi = async () => {
        try {

            const response = await fetch('/marketings/rencanakerja/report/data', {
                method: 'GET'
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

    const intialShowtable = async () => {

        try {
            console.log('Loading..');

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
            const responses = await callApi();
            $(`.spiner-example`).remove();
            $('#dataBody').append(responses.data.rencanakerja);
            console.log('Done..');
        } catch (error) {
            console.log(error);
        }
    }

    intialShowtable();

    // $('#submitFilter').click(function () {
    //     createdBy = $('#created_by').val();
    //     // date = $('#date').val();

    //     intialShowtable();
    // });
    // $('#submitClear').click(function () {
    //     createdBy = '';
    //     // date = $('#date').val();
    //     intialShowtable();
    // });

})



