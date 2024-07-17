const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
let month = ''
let year = ''
let week = ''

$('.month-start').datepicker({
    minViewMode: 1,
    keyboardNavigation: false,
    forceParse: false,
    forceParse: false,
    autoclose: true,
    todayHighlight: true,
    format: 'mm-yyyy'
});

$(".select2_demo_2").select2({
    theme: 'bootstrap4',
});

const showTable = (idTable, year, month, week) => {
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
                    d.week = week;
                },
                dataSrc: function (json) {
                    return json.data.rencanakerja.data; // Mengakses data di dalam kunci 'data'
                }
            },
            autoWidth: false,
            columnDefs:
                [
                    { "targets": [0], "className": "text-center", "width": "5%" },
                    { "targets": [1], "width": "15%" },
                    { "targets": [2], "className": "text-right", "width": "2%" },
                    { "targets": [3], "width": "2%", "className": "text-right" },
                    { "targets": [4], "className": "text-right", "width": "2%" },
                    { "targets": [5], "className": "text-right", "width": "3%" },
                    // { "targets": [6], "className": "text-center", "width": "8%" },
                ],
            initComplete: function (settings, json) {
                const currentMonth = json.data.bulan != null ? 'Daftar Rencana Kerja Marketing Bulan ' + json.data.bulan : '';
                $('#title').text(currentMonth);
                $('#lihatPerMinggu').removeClass('d-none')

            }
        });
    }
}


$('#submitFilter').click(function () {
    const date = $('#month-start').val();
    const dateParts = date.split("-");
    month = dateParts[0]; // 06
    year = dateParts[1];  // 2024
    week = $('#weekPicker').val();

    showTable('datatable', year, month, week);


});

$('#submitClear').click(function () {
    $('#month-start').val("");
    $('#weekPicker').val("");
    showTable('datatable', null, null, null);
});

function onRincianKegiatan(data) {
    const pkbid = data.getAttribute('data-pkbid');
    const pkbuuid = data.getAttribute('data-pkbuuid');

    // showTableRincian('dataRincian', pkbid, pkbdid)
    $("#dataRincian").DataTable().clear().destroy();
    $("#dataRincian").DataTable({
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
            url: `/marketings/report/evaluasi/kegiatan/rincian`,
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            data: function (d) {
                d.pkbid = pkbid;
                d.pkbuuid = pkbuuid;
                d.year = year;
                d.month = month;
                d.week = week;
            },
            dataSrc: function (json) {
                return json.data.kegiatan.data; // Mengakses data di dalam kunci 'data'
            }
        },
        autoWidth: false,
        columnDefs:
            [
                { "targets": [0], "className": "text-center", "width": "5%" },
                { "targets": [1], "width": "10%" },
                { "targets": [2], "width": "50%" },
                { "targets": [3], "width": "8%" },
            ],
        initComplete: function (settings, json) {
            const titleRincian = json.data.proker_bulanan != null ? 'Rincian Kegiatan ' + json.data.proker_bulanan : '';
            $('#titleRincian').text(titleRincian);
        }
    });

}

const callApiPerMinggu = async (year, month) => {
    try {

        const response = await fetch('/marketings/report/evaluasi/perbulan/perminggu', {
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

const showTablePerMinggu = (idTable, year, month) => {
    $("#" + idTable).DataTable().clear().destroy();
    if (idTable == 'dataRincianPerminggu') {
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
                url: `/marketings/report/evaluasi/perbulan/perminggu`,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: function (d) {
                    d.year = year,
                        d.month = month
                },
                dataSrc: function (json) {
                    return json.data.perminggu.data; // Mengakses data di dalam kunci 'data'
                }
            },
            autoWidth: false,
            columnDefs:
                [
                    { "targets": [0], "className": "text-center", "width": "5%" },
                    { "targets": [1], "width": "15%" },
                    { "targets": [2], "className": "text-right", "width": "2%" },
                    { "targets": [3], "width": "2%", "className": "text-right" },
                    { "targets": [4], "className": "text-right", "width": "2%" },
                    { "targets": [5], "className": "text-right", "width": "3%" },
                    { "targets": [6], "className": "text-center", "width": "8%" },
                ],
            initComplete: function (settings, json) {
                $('#titleRincianPerMinggu').text("Program Per Minggu Bulan " + json.data.bulan);

                $('#dataRincianPerminggu tbody').on('click', 'a.btn', function () {

                    let table = $('#datamyModalRincianKegiatan').DataTable({
                        "paging": true,
                        "searching": true,
                        "info": true
                    });

                    table.clear().destroy();

                    table.clear().draw();

                    let lsitRinciankegiatan = $(this).data('rinciankegiatan');

                    if (lsitRinciankegiatan.length) {
                        lsitRinciankegiatan.forEach(function (detail) {
                            table.row.add([
                                detail.pkh_dates,
                                detail.pkh_title,
                                detail.name
                            ]).draw(false);
                        });
                    } else {
                        table.row.add(['', 'No details available', '']).draw(false);
                    }

                    $('#myModalRincianKegiatan').modal('show');
                });

            },
        });
    }
}



async function onLihatPerMinggu(data) {
    const date = $('#month-start').val();
    const dateParts = date.split("-");
    month = dateParts[0]; // 06
    year = dateParts[1];  // 2024
    week = $('#weekPicker').val();

    showTablePerMinggu('dataRincianPerminggu', year, month);

    // const responses = await callApiPerMinggu(year, month);
    // console.log(responses.data.results);

}