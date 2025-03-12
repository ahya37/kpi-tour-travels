var today       = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;

var data_jenis_asset     = [
    { "jenis" : "itinerary", "text" : "Itinerary" },
    { "jenis" : "flyer", "text" : "Flyer" },
    { "jenis" : "banner", "text" : "Banner" },
    { "jenis" : "catalog", "text" : "Catalog" }
];

$(document).ready(function(){
    showTable('table_management_asset_umrah', []);

    // GET DATA UMRAH
    const jadwalUmrahURL    = "website/master/jadwal";
    const jadwalUmrahType   = "GET";
    const jadwalUmrahData   = {
        'tahun' : moment(today, 'YYYY-MM-DD').format('YYYY'),
    }

    const getData           = [
        doTransaction(jadwalUmrahURL, jadwalUmrahType, jadwalUmrahData)
    ];

    Promise.allSettled(getData)
        .then((success)     => {
            const jadwalGetData     = success[0].status == 'fulfilled' ? success[0].value.data : [];
            showTable('table_management_asset_umrah', jadwalGetData);
            
            if(jadwalGetData.length < 1) {
                $("#table_management_asset_umrah").find('.dataTables_empty').html('Tidak Ada Data Jadwal Umrah');
            }
        })
        .catch((error)      => {
            showTable('table_management_asset_umrah', []);
            $("#table_managemnt_asset_umrah").find('.dataTables_empty').html(`Tidak Ada Dat Jadwal Umrah`);
        })
});

function showModal(idModal, type = '', data = '')
{
    if(idModal == 'modal_form_asset_umrah')
    {
        // GET DATA 
        const tourCodeURL   = "website/master/asset_umrah";
        const tourCodeType  = "GET";
        const tourCodeData  = {
            "tour_code" : data,
        };
        const tourCodeMsg   = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

        doTransaction(tourCodeURL, tourCodeType, tourCodeData, tourCodeMsg)
            .then((success)     => {
                Swal.close();
                // TITLE
                $("#tour_code_title").html(data);
                // OPEN MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

                // DATA HEADER
                const dataHeader    = success.data.header[0];
                
                $("#ast_tour_code").val(data);
                $("#ast_depature_date").val(moment(dataHeader['jdw_depature_date'], 'YYYY-MM-DD').format('DD/MM/YYYY'));
                $("#ast_arrival_date").val(moment(dataHeader['jdw_arrival_date'], 'YYYY-MM-DD').format('DD/MM/YYYY'));
                $("#ast_mentor_name").val(dataHeader['jdw_mentor_name']);
                // DATA DETAIL
                const dataDetail    = success.data.detail;
                showTable('table_form_asset_umrah', dataDetail);
                
            })
            .catch((error)      => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : error.responseJSON.message,
                })
            })
        $("#btn_simpan_form_asset").val(type);
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_form_asset_umrah')
    {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#tour_code_title").html('');
            $("#ast_form").trigger('reset');

            $("#btn_simpan_form_asset").val();
            $("#btn_tambah_baris_form_asset").val(1);
        });
    }
}

function showTable(idTable = '', data = [])
{
    // REDRAW TABLE
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'table_management_asset_umrah') {
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat..`,
                "zeroRecords"   : `Data Yang Dicari Tidak Ditemukan`,
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "8%" },
                { "targets" : [1], "className" : "text-left align-middle" },
                { "targets" : [2], "className" : "text-left align-middle" },
                { "targets" : [3], "className" : "text-left align-middle" },
                { "targets" : [4], "className" : "text-center align-middle", "width" : "5%" },
            ],
            ordering    : false,
        });

        if(data.length > 0) {
            // SORT DATA
            data.sort((a, b)    => {
                return new Date(b.jdw_depature_date) - new Date(a.jdw_depature_date)
            })
            // SHOW DATA
            for(let i = 0; i < data.length; i++) {
                let seq             = i + 1;
                let tourCode        = data[i]['jdw_tour_code'];
                let depatureDate    = data[i]['jdw_depature_date'];
                let mentorName      = data[i]['jdw_mentor_name'];
                let buttonEdit      = `<button class="btn btn-sm btn-success" value="${tourCode}" title="Lihat Data" onclick="showModal('modal_form_asset_umrah', 'edit', this.value)"><i class="fa fa-edit"></i></button>`

                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${seq}</label>`,
                    `<label class="font-weight-normal no-margins">${tourCode}</label>`,
                    `<label class="font-weight-normal no-margins">${moment(depatureDate, 'YYYY-MM-DD').format('DD MMM YYYY')}</label>`,
                    `<label class="font-weight-normal no-margins">${mentorName}</label>`,
                    buttonEdit
                ]).draw(false);
            }
        }
    } else if(idTable == 'table_form_asset_umrah') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "Tekan Tambah Baris Untuk Menambahkan Baris",
            },
            pageLength  : -1,
            searching   : false,
            ordering    : false,
            autoWidth   : false,
            bInfo       : false,
            paging      : false,
            columnDefs  : [
                { "targets" : [0, 4], "className" : "text-center align-top", "width" : "8%" },
                { "targets" : [1], "className" : "text-center align-top", "width" : "10%" },
                { "targets" : [2], "className" : "text-left align-top", "width" : "30%" },
                { "targets" : [3], "className" : "text-left align-middle" },
            ],
        });

        let currentSeq  = $("#btn_tambah_baris_form_asset").val();
        if(data.length > 0) {
            // LOOP DATA
            for(let i = 0; i < data.length; i++)
            {
                addRowTable(idTable, parseInt(currentSeq) + i, data[i]);
            }
        } else {
            addRowTable(idTable, parseInt(currentSeq), '');
        }
    }

    $("#"+idTable+"_wrapper").css('padding-bottom', '0px');
}

function addRowTable(idTable, seq = '', data = '')
{
    if(idTable == 'table_form_asset_umrah') {
        // FORM
        let ke  = parseInt(seq);
        let inputDelete = `<button class="btn btn-danger" id="ast_btnDelete${ke}" title="Hapus Baris" onclick="deleteRowTable('${idTable}', ${ke})"><i class="fa fa-trash"></i></button>`;
        let inputNo     = `<input type="text" class="form-control text-center" id="ast_seq${ke}" placeholder="Ke" readonly>`;
        let inputJenis  = `<select class="form-control" id="ast_type${ke}" style="width: 100%;"></select>`;
        let inputURL    = `<span id="ast_input_url${ke}"><textarea class="form-control" id="ast_link${ke}" rows="3" placeholder="URL Link" style="resize: none;"></textarea></span>`;
        let linkURL     = `<span id="ast_url${ke}"><a href="${data['jdw_det_link']}" title="Download File" target="_blank">Lihat</a></span>`
        let inputAksi   = `<button class="btn btn-success" ${data != '' ? '' : 'disabled'} id="ast_btnEdit${ke}" title="Edit Data"><i class="fa fa-edit"></i></button>`;

        $("#"+idTable).DataTable().row.add([
            inputDelete,
            inputNo,
            inputJenis,
            inputURL+""+linkURL,
            inputAksi
        ]).draw(false);

        $("#ast_seq"+ke).val(parseInt(ke));
        $("#ast_seq"+ke).focus();

        if(data == '') {
            showSelect('ast_type', data_jenis_asset, '', ke);
            $("#ast_input_url"+ke).removeClass('d-none');
            $("#ast_url"+ke).addClass('d-none');
        } else {
            $("#ast_input_url"+ke).addClass('d-none');
            $("#ast_url"+ke).removeClass('d-none');
            showSelect('ast_type', data_jenis_asset, data['jdw_det_description'], ke);

            $("#ast_link"+ke).val(data['jdw_det_link']);

            $("#ast_btnEdit"+ke).on('click', () => {
                $("#ast_input_url"+ke).removeClass('d-none');
                $("#ast_url"+ke).addClass('d-none');

                $("#ast_btnEdit"+ke).prop('disabled', true);
            });
        }

        $("#btn_tambah_baris_form_asset").val(parseInt(seq) + 1);
    }
}

function deleteRowTable(idTable, seq = '')
{
    if(idTable == 'table_form_asset_umrah')
    {
        let btnSeq  = $("#btn_tambah_baris_form_asset").val()
        let currentSeq = parseInt(btnSeq);

        if(parseInt(seq) == 1) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Tidak Bisa Menghapus Data Awal'
            })
        } else {
            if(parseInt(currentSeq) - seq > 1) {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Hanya Baris Terakhir Yang Bisa Dihapus',
                })
            } else {
                $("#"+idTable).DataTable().row(parseInt(seq) - 1).remove().draw();
                $("#ast_seq"+ (parseInt(seq) - 1)).focus();
                $("#btn_tambah_baris_form_asset").val(parseInt(currentSeq) - 1);
            }
        }
    }
}

function showSelect(idSelect, data = [], selectedData = '', seq = '')
{
    let newSelect   = idSelect + "" + seq;
    $("#"+newSelect).select2({
        theme   : 'bootstrap4',
    });
    if(idSelect == 'ast_type') {
        let html    = `<option selected disabled>Jenis</option>`;

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value="${item['jenis']}">${item['text']}</option>`;
            });
        }
        
        $("#"+newSelect).html(html);

        if(selectedData != '') {
            $("#"+newSelect).val(selectedData);
        }
    }
}

function doSimpan(idForm, jenis)
{
    if(idForm == 'asset_umrah')
    {
        let tourCode    = $("#ast_tour_code").val();

        let detail      = [];
        let detailTable     = $("#table_form_asset_umrah").DataTable().rows().count();
        for(let i = 0; i < detailTable; i++) {
            let seq     = i + 1;
            detail.push({
                'asd_seq'       : $("#ast_seq"+seq).val(),
                'asd_jenis'     : $("#ast_type"+seq).val(),
                'asd_url'       : $("#ast_link"+seq).val(),
            });
        }

        const simpanAssetURL    = "website/assets/simpan_data_asset";
        const simpanAssetType   = "POST";
        const simpanAssetData   = {
            "tour_code" : tourCode,
            "detail"    : detail,
        };
        const simpanAssetMsg    = Swal.fire({ title : "Data Sedang Diproses" }); Swal.showLoading();

        doTransaction(simpanAssetURL, simpanAssetType, simpanAssetData, simpanAssetMsg)
            .then((success)     => {
                Swal.fire({
                    icon    : 'success',
                    title   : 'Berhasil',
                    text    : success.message,
                }).then((res)   => {
                    if(res.isConfirmed) {
                        closeModal('modal_form_asset_umrah');
                    }
                })
            })
            .catch((error)      => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : error.responseJSON.message,
                })
            })
    }
}

function doTransaction(url, type, data = [], message = '', processData = true, contentType = 'application/x-www-form-urlencoded; charset=UTF-8')
{
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : false,
            url     : base_url + '/' + url,
            type    : type,
            data    : data,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            processData     : processData,
            contentType     : contentType,
            beforeSend      : () => {
                message;
            },
            success         : (isSuccess)   => {
                resolve(isSuccess);
            },
            error           : (isError)     => {
                reject(isError);
            }
        })
    })
}