var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
var dataTahun   = [];

for(let i = 0; i < 10; i++)
{
    dataTahun.push(moment(today, 'YYYY-MM-DD').subtract(i, 'years').format('YYYY'));
}

showSelect('filter_jadwal_umrah_tahun', dataTahun, moment(today, 'YYYY-MM-DD').format('YYYY'));
showTable('table_jadwal_umrah', []);

$(document).ready(()    => {
    // GET DATA TABLE
    let jadwal_URL  = "divisi/digital/umrah/data_jadwal_umrah";
    let jadwal_type = "GET";
    let jadwal_data = {
        "tahun"     : moment(today, 'YYYY-MM-DD').format('YYYY'),
        "tour_code" : null
    };

    const transactions = [
        doTransaction(jadwal_URL, jadwal_type, jadwal_data, "", true)
    ];

    Promise.allSettled(transactions)
        .then((success)     => {
            let jadwal_getData  = success[0].status == "fulfilled" ? success[0].value.data : [];

            showTable('table_jadwal_umrah', jadwal_getData);
        })
        .catch((err)        => {
            console.log(err);
        })
})

function showModal(idModal, value)
{
    if(idModal == 'modal_detail_jadwal_umrah')
    {
        // GET DATA
        let umrah_URL   = "divisi/digital/umrah/data_jadwal_umrah";
        let umrah_type  = "GET";
        let umrah_data  = {
            "tahun"     : moment(today).year(),
            "tour_code" : value,
        };
        let umrah_msg   = Swal.fire({ title : "Data Sedang Dimuat..", allowOutsideClick : false }); Swal.showLoading();
        
        doTransaction(umrah_URL, umrah_type, umrah_data, umrah_msg, true)
            .then((success)     => {
                Swal.close();
                $("#"+idModal).modal({backdrop: 'static', keyboard: false});
                let umrah_getData           = success.data['header'][0];
                let umrah_getDataDetail     =  success.data['detail'];

                // FILL FORM
                $("#jadwal_detail_tour_code").val(umrah_getData.umrah_tour_code);
                $("#jadwal_detail_uuid").val(value);
                $("#jadwal_detail_tour_leader").val(umrah_getData.umrah_tour_leader);
                $("#jadwal_detail_depature_date").val(moment(umrah_getData.umrah_depature_date).format('DD-MMM-YYYY'));
                $("#jadwal_detail_arrival_date").val(moment(umrah_getData.umrah_arrival_date).format('DD-MMM-YYYY'));

                $("#table_detail_btn_simpan_data").val('edit');
                showTable('table_detail_jadwal_umrah', umrah_getDataDetail);
            })
            .catch((err)        => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak Ada Data Yang Bisa Ditampilkan..',
                })
            })
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_detail_jadwal_umrah')
    {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#table_detail_btn_tambah_data").val(0);
        })
    }
}

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();

    if(idTable == 'table_jadwal_umrah') 
    {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable      : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                zeroRecords     : "Data Yang Dicari Tidak Ditemukan",
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "align-middle", "width" : "15%" },
                { "targets" : [2], "className" : "align-middle" },
                { "targets" : [3, 4], "className" : "text-center align-middle", "width" : "18%" },
                { "targets" : [5], "className" : "text-center align-middle", "width" : "8%" },
            ],
        })

        if(data.length > 0) {
            let seq = 1;
            for(const item of data)
            {
                let jadwal_uuid     = item['umrah_uuid'];
                let jadwal_tourCode = item['umrah_tour_code'];
                let jadwal_tourLeader   = item['umrah_tour_leader'];
                let jawdal_dptDate  = item['umrah_depature_date'];
                let jadwal_arvDate  = item['umrah_arrival_date'];
                
                $("#"+idTable).DataTable().row.add([
                    `<label class="no-margins font-weight-normal">${seq++}</label>`,
                    `<label class="no-margins font-weight-normal">${jadwal_tourCode}</label>`,
                    `<label class="no-margins font-weight-normal">${jadwal_tourLeader}</label>`,
                    `<label class="no-margins font-weight-normal">${moment(jawdal_dptDate, 'YYYY-MM-DD').format('DD-MMM-YYYY')}</label>`,
                    `<label class="no-margins font-weight-normal">${moment(jadwal_arvDate, 'YYYY-MM-DD').format('DD-MMM-YYYY')}</label>`,
                    `<button type="button" class="btn btn-sm btn-primary" title="Lihat Detail Jadwal Umrah" value="${jadwal_uuid}" onclick="showModal('modal_detail_jadwal_umrah', this.value)"><i class="fa fa-eye"></i></button>`
                ]).draw(false);
            }
        }
    } else if(idTable == 'table_detail_jadwal_umrah') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "Silahkan Klik Tomnbol 'Tambah Data' Yang Berada di Bawah",
            },
            autoWidth   : false,
            bInfo       : false,
            searching   : false,
            paging      : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center", "width" : "5%" },
                { "targets" : [1], "width" : "8%" },
                { "targets" : [3], "width" : "30%" },
            ]
        });

        let seq = 1;
        if(data.length > 0)
        {
            for(let i = 0; i < data.length; i++)
            {
                addColumnTable(idTable, seq++, data[i]);
            }
        }
        addColumnTable(idTable, seq, '');
    }
}

function addColumnTable(idTable, seq, data)
{
    if(idTable == 'table_detail_jadwal_umrah')
    {   
        // FORM
        let input_btn_delete= `<button type='button' class='btn btn-sm btn-danger' id='jadwal_detail_btn_delete${seq}' title='Hapus Baris' onclick="deleteColumnTable('${idTable}', ${seq})" style='height: 35px; width: 35px;'><i class='fa fa-trash'></i></button>`;
        let input_no        = `<input type='text' class='form-control text-center' id='jadwal_detail_input_seq${seq}' title='Nomor Urut' placeholder='#' readonly>`;
        let input_deskripsi = `<input type='text' class='form-control text-left' id='jadwal_detail_input_deskripsi${seq}' title='Deskripsi' placeholder='Tulis Deksripsi'>`;
        let input_link      = `<textarea class='form-control text-left' id='jadwal_detail_input_link${seq}' title='Link' placeholder='www.example.com' style='resize:none;' rows='2'></textarea>`;

        $("#"+idTable).DataTable().row.add([
            input_btn_delete,
            input_no,
            input_deskripsi,
            input_link
        ]).draw(false);

        // FILL FORM
        $(`#jadwal_detail_input_seq${seq}`).val(seq);

        if(seq != 1) {
            $("#jadwal_detail_input_deskripsi"+seq).focus();
        }

        if(data != '')
        {
            $(`#jadwal_detail_input_deskripsi${seq}`).val(data['jdw_det_description']);
            $(`#jadwal_detail_input_link${seq}`).val(data['jdw_det_link']);
        }
        

        $(`#table_detail_btn_tambah_data`).val(parseInt(seq) + 1);
    }
}

function deleteColumnTable(idTable, seq)
{
    if(idTable == 'table_detail_jadwal_umrah')
    {
        if(seq == 1) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Tidak Bisa Menghapus Baris Pertama',
                didClose    : () => {
                    $("#jadwal_detail_input_deskripsi"+seq).focus();
                }
            })
        } else {
            const currentSeq    = parseInt($("#table_detail_btn_tambah_data").val());
            if(currentSeq - seq > 1) {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Hanya Bisa Menghapus Baris Terakhir',
                    didClose    : () => {
                        $("#jadwal_detail_input_deskripsi"+(currentSeq - 1)).focus()
                    }
                })
            } else {
                $("#"+idTable).DataTable().row(seq - 1).remove().draw(false);
                $("#jadwal_detail_input_deskripsi"+(seq - 1)).focus();

                $("#table_detail_btn_tambah_data").val(currentSeq - 1);
            }
        } 
    }
}

function showSelect(idSelect, data, selectedData, seq = null)
{
    $("#"+idSelect).select2({
        theme    : 'bootstrap4',
    });

    if(idSelect == 'filter_jadwal_umrah_tahun')
    {
        let html    = "<option selected disabled></option>";

        if(data.length > 0) {
            for(const item of data)
            {
                html    += `<option value="${item}">${item}</option>`;
            }
        }
        
        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    }
}

function showSelectedDetail(idSelect, value)
{
    if(idSelect == 'filter_jadwal_umrah_tahun')
    {
        showTable('table_jadwal_umrah', []);
        let jadwal_URL  = "divisi/digital/umrah/data_jadwal_umrah";
        let jadwal_type = "GET";
        let jadwal_data = {
            "tahun"     : moment(value, 'YYYY').format('YYYY'),
            "tour_code" : null,
        };
        
        doTransaction(jadwal_URL, jadwal_type, jadwal_data, "", true)
            .then((success) => {
                showTable('table_jadwal_umrah', success.data);
            })
            .catch((err)    => {
                showTable('table_jadwal_umrah', []);
                $("#table_jadwal_umrah .dataTables_empty").html(`Tidak Ada Data Tour Code Pada Tahun ${value}`);
            })
    }
}

function simpanData(idForm, jenis)
{
    if(idForm == 'modal_detail_jadwal_umrah')
    {
        let dataDetail  = [];
        let tourCode    = $("#jadwal_detail_tour_code").val();
        let tourUUID    = $("#jadwal_detail_uuid").val();

        // VALIDATE DETAIL
        let totalDataDetail = $("#table_detail_jadwal_umrah").DataTable().rows().count();
        for(let i = 0; i < totalDataDetail; i++)
        {
            let tourDetailDescription   = $(`#jadwal_detail_input_deskripsi${i + 1}`);
            let tourDetailLink          = $(`#jadwal_detail_input_link${i + 1}`);

            dataDetail.push({
                "detail_description"    : tourDetailDescription.val(),
                "detail_link"           : tourDetailLink.val(),
            })
        }

        // DO SIMPAN
        if(dataDetail.length > 0) {
            let detailURL   = `divisi/digital/umrah/trans/simpan_detail/${jenis}`;
            let detailData  = {
                "tour_code"     : tourCode,
                "tour_uuid"     : tourUUID,
                "tour_detail"   : dataDetail,
            };
            let detailType  = "POST";
            let detailMsg   = Swal.fire({ title : "Data Sedang Diproses.." }); Swal.showLoading();

            doTransaction(detailURL, detailType, detailData, detailMsg, true)
                .then((success)     => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text,
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            closeModal('modal_detail_jadwal_umrah');
                        }
                    })
                })
                .catch((err)        => {
                    Swal.close();
                    Swal.fire({
                        icon    : err.responseJSON.alert.icon,
                        title   : err.responseJSON.alert.message.title,
                        text    : err.responseJSON.alert.message.text,
                    })
                })
        }
    }
}

function doTransaction(url, type, data, message, isAsync)
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            type    : type,
            url     : base_url+"/"+url,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                message;
            },
            async   : isAsync,
            success : (success) => {
                resolve(success)
            },
            error   : (err)     => {
                reject(err)
            }
        })
    })
}