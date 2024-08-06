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

    const callApi = async (createdBy,date) => {
        try {

            const response = await fetch('/master/programkerja/tahunan/marketing/report/list', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    created_by: createdBy,
                    date: date
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

    const intialShowtable = async (createdBy,date) => {

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
            const responses = await callApi(createdBy,date);
            $(`.spiner-example`).remove();
            $('#dataBody').append(responses.html);
        } catch (error) {
            console.log(error);
        }
    }

    intialShowtable(createdBy,date);

    $('#submitFilter').click(function () {
        createdBy = $('#created_by').val();
        // date = $('#date').val();

        intialShowtable(createdBy,date);
    });
    $('#submitClear').click(function () {
        createdBy = '';
        // date = $('#date').val();
        intialShowtable(createdBy,date);
    });

})



