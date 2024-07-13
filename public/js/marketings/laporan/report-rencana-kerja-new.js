const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// select sasaran 

const sasaran = $(".sasaran");

sasaran.select2({
    theme: "bootstrap4",
    width: $(this).data("width")
        ? $(this).data("width")
        : $(this).hasClass("w-100")
            ? "100%"
            : "style",
    placeholder: "Pilih Sasaran",
    allowClear: Boolean($(this).data("allow-clear")),
    ajax: {
        headers: {
            'X-CSRF-TOKEN': csrfToken
        },
        dataType: "json",
        url: '/marketings/sasaran',
        delay: 250,
        processResults: function (data) {
            return {
                results: $.map(data, function (item) {
                    return {
                        text: item.pktd_title,
                        id: `${item.uid} | ${item.pktd_seq}`,
                    };
                }),
            };
        },
    },
});

const showTableEvaluasiSasaranUmum = (idTable, year, idSasaran) => {
    $("#" + idTable).DataTable().clear().destroy();
    if (idTable == 'datatableEvaluasiSasaranUmum') {
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
                url: `/marketings/sasaran/programs`,
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                data: function (d) {
                    d.year = year,
                    d.idSasaran = idSasaran
                },
                dataSrc: function (json) {
                    return json.data.sasaran_umum.data; // Mengakses data di dalam kunci 'data'
                }
            },
            autoWidth: false,
            columnDefs:
                [
                    { "targets": [0], "className": "text-center", "width": "2%" },
                    { "targets": [1], "width": "8%" },
                    { "targets": [2], "className": "text-right", "width": "5%" },
                    { "targets": [3], "className": "text-right", "width": "5%" },
                    { "targets": [4], "width": "5%", "className": "text-right" },
                    { "targets": [5], "className": "text-right", "width": "5%" },
                ],
            initComplete: function (settings, json) {
                $('#pencapaian').text('Realisasi Umrah')
                $('#target').text('Target Umrah')
                console.log('bulanan ', json)
            }
        });
    }
}

$('#goFilter').click(async function () {
    const idSasaran = sasaran.val();
    const year      = $('#year-start').val();
    console.log(idSasaran);
    console.log('Loading..');
    // const response = await getCallApi(idSasaran);
    console.log('Done');
    // console.log(response);
    showTableEvaluasiSasaranUmum('datatableEvaluasiSasaranUmum',year, idSasaran);

});

const getCallApi = async (yearInProgram, monthInProgram) => {
    try {

        const response = await fetch('/marketings/report/evaluasi', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                year: yearInProgram,
                month: monthInProgram
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

const showTableProgramBulan = (idTable, yearInProgram, monthInProgram) => {
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
                    d.year = yearInProgram;
                    d.month = monthInProgram;
                },
                dataSrc: function (json) {
                    return json.data.rencanakerja.data; // Mengakses data di dalam kunci 'data'
                }
            },
            autoWidth: false,
            columnDefs:
                [
                    { "targets": [0], "className": "text-center", "width": "2%" },
                    { "targets": [1], "width": "15%" },
                    { "targets": [2], "className": "text-right", "width": "2%" },
                    { "targets": [3], "width": "2%", "className": "text-right" },
                    { "targets": [4], "className": "text-right", "width": "2%" },
                    // { "targets": [5], "className": "text-right", "width": "3%" },
                    // { "targets": [6], "className": "text-center", "width": "8%" },
                ],
            initComplete: function (settings, json) {
                const currentMonth = json.data.bulan != null ? 'Program Bulan ' + json.data.bulan : '';
                $('#title').text(currentMonth);
                $('#pencapaianProgram').text('Pencapaian Program');
                $('#targetProgram').text('Target Program');
                $('#hasilProgram').text('Realisasi Program');
                // $('#lihatPerMinggu').removeClass('d-none')
                $('#totalPersentasePerbulan').text(json.data.persentage_total_pencapaian_progam +' %')
                $('#totalHasilPerbulan').text(json.data.total_hasil)
                $('#totalTargetPerbulan').text(json.data.total_target)


            }
        });
    }
}

async function showMonth(element){
    const yearInProgram = element.getAttribute('data-year'); 
    const monthInProgram = element.getAttribute('data-month'); 

    console.log(yearInProgram)
    console.log(monthInProgram)

    console.log('Loading..');
    const response = await getCallApi(yearInProgram, monthInProgram);
    console.log('Done');
    console.log(response);
    showTableProgramBulan('datatable', yearInProgram, monthInProgram);
}
