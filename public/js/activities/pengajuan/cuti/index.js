moment.locale('id');
var today   = moment().format('YYYY-MM-DD');
var url     = window.location.origin;

$(document).ready(() => {
    Swal.fire({
        icon    : 'info',
        title   : 'Perhatian',
        text    : 'Halaman ini sedang dalam tahap pengembangan'
    });

    showTable('table_list_pengajuan');
});

function showTable(idTable)
{
    if(idTable == 'table_list_pengajuan')
    {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan..",
            },
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "text-left align-middle", "width" : "20%" },
                { "targets" : [2], "className" : "text-left align-middle"},
                { "targets" : [3], "className" : "text-center align-middle", "width" : "14%" },
                { "targets" : [4], "className" : "text-center align-middle", "width" : "15%" },
            ],
        });

        // GET DATA
        const pgj_url   = "/pengajuan/listCuti";
        const pgj_type  = "GET";
        
        doTrans(pgj_url, pgj_type, "", "", true)
            .then(success   => {
                const pgj_getData   = success.data;

                if(pgj_getData.length > 0) {
                    let  i = 1;
                    for(const item of pgj_getData)
                    {
                        const tblSeq        = i++;
                        const tblDate       = item.emp_act_start_date == item.emp_act_end_date ? moment(item.emp_act_start_date, 'YYYY-MM-DD').format('DD-MMM-YYYY') : moment(item.emp_act_start_date, 'YYYY-MM-DD').format('DD-MMM-YYYY')+" s/d "+moment(item.emp_act_end_date, 'YYYY-MM-DD').format('DD-MMM-YYYY');
                        const tblTitle      = item.emp_act_title;
                        const tblType       = item.emp_act_type;
                        const tblStatusCode = item.emp_act_status;

                        switch(tblStatusCode) {
                            case "1" :
                                tblStatusName     = "<h4><span class='badge badge-pills badge-primary'>Disetujui</span></h4>";
                            break;
                            case "2" :
                                tblStatusName   = "<h4><span class='badge badge-pills badge-danger'>Ditolak</span></h4>";
                            break;
                            case "3" :
                                tblStatusName   = "<h4><span class='badge badge-pills badge-warning text-dark'>Menunggu Konfirmasi</span></h4>";
                            break;
                        }
        
                        $("#"+idTable).DataTable().row.add([
                            tblSeq,
                            tblDate,
                            "<label class='no-margins' title='"+tblTitle+"'>" + tblTitle + "</label>",
                            tblType,
                            tblStatusName
                        ]).draw(false);
                    }
                } else {
                    $(".dataTables_empty").html("Tidak Ada Data Yang Bisa Dimuat..");
                }
            })
            .catch(err      => {
                $(".dataTables_empty").html("Tidak Ada Data Yang Bisa Dimuat..");
            });
    }
}

function showModal(idModal, jenisTrans)
{
    if(idModal == 'modal_pengajuan')
    {
        // AGAR MODAL TIDAK BISA KLIK OUTSIDE   
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

        $("#"+idModal).on('shown.bs.modal', () => {
            $("#pgj_title").focus();
            $("#pgj_title").prop('autocomplete', 'off');
        });

        // SHOW DATERANGEPICKER
        $("#pgj_date").daterangepicker({
            minDate     : moment(today, 'YYYY-MM-DD').subtract(1, 'year'),
            maxDate     : moment(today, 'YYYY-MM-DD').add(1, 'year'),
            autoApply   : false,
            format      : 'DD/MM/YYYY',
            setStartDate    : moment(today, 'YYYY-MM-DD'),
            locale  : {
                separator   : ' s/d ',
                cancelLabel : 'Batal',
                applyLabel  : 'Simpan',
            },
        });

        $("#pgj_date").on('apply.daterangepicker', () => {
            const startDate = $("#pgj_date").val().split(' s/d ')[0];
            const endDate   = $("#pgj_date").val().split(' s/d ')[1];
            const dateDiff  = moment(moment(endDate, 'DD/MM/YYYY')).diff(moment(startDate, 'DD/MM/YYYY'), 'days');
            
            $("#pgj_count_day").val(dateDiff+" Hari");
        });

        // ONKEYUP TITLE
        $("#pgj_title").on('keyup', (e) => {
            const pgjTitleVal  = $("#pgj_title").val();
            $("#pgj_title").val(pgjTitleVal.toUpperCase());
        })

        // FORM 
        var form    =  $("#pgj_form");
        form.on('keydown', (e) => {
            if(e.key === 'Enter') {
                e.preventDefault();
            }
        });
        
        // SHOW SELECT
        const pgj_type_data     = [
            { "id" : "Cuti", "name" : "Cuti" },
            { "id" : "Izin", "name" : "Izin" },
            { "id" : "Sakit", "name" : "Sakit" },
            { "id" : "Lainnya", "name" : "Lainnya" },
        ];

        showSelect('pgj_type', pgj_type_data, '');
    }
}

function closeModal(idModal)
{
    $("#"+idModal).modal('hide');

    if(idModal == 'modal_pengajuan')
    {
        $("#"+idModal).on('hidden.bs.modal', () => {
            // RESET FORM
            $("#pgj_title").val(null);
            $("#pgj_count_day").val(null);

            $("#pgj_date").data('daterangepicker').setStartDate(moment(today, 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#pgj_date").data('daterangepicker').setEndDate(moment(today, 'YYYY-MM-DD').format('DD/MM/YYYY'));
        });
    }
}



function showSelect(idSelect, data, seq)
{
    // DEFAULT
    $("#"+idSelect).select2({
        theme   : 'bootstrap4'
    });

    if(idSelect == 'pgj_type')
    {
        var html    = "<option selected disabled>Pilih Jenis Pengajuan</option>";
        
        if(data != '') {
            for(const item of data)
            {
                html    += "<option value='" + item.id + "'>" + item.name + "</option>";
            }
            $("#"+idSelect).html(html);
        } else {
            $("#"+idSelect).html(html);
        }
    }
}

function doSimpan(idForm, e)
{
    e.preventDefault();
    
    if(idForm == 'pgj_form')
    {
        // GET DATA
        const pgj_title         = $("#pgj_title");
        const pgj_date          = $("#pgj_date");
        const pgj_date_length   = $("#pgj_date_length");
        const pgj_type          = $("#pgj_type");

        if(pgj_title.val() == "") {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Uraian Tidak Boleh Kosong',
                didClose: () => {
                    pgj_title.focus();
                }
            })
        } else if(pgj_type.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Jenis Pengajuan Harus Dipilih',
                didClose: () => {
                    pgj_type.select2('open');
                    pgj_type.focus();
                }
            })
        } else {
            // DO SIMPAN
            const pgj_sendData  = {
                "pgj_id"            : "",
                "pgj_title"         : pgj_title.val(),
                "pgj_date_start"    : moment(pgj_date.val().split(' s/d ')[0], 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "pgj_date_end"      : moment(pgj_date.val().split(' s/d ')[1], 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "pgj_type"          : pgj_type.val(),
                "pgj_status"        : "3",
            }

            const pgj_url       = url + "/pengajuan/simpanCuti";
            
            const sendData  = [
                doTrans(pgj_url, 'POST', pgj_sendData, '', true)
            ];

            Swal.fire({
                title   : 'Data Sedang Diproses',
            });
            Swal.showLoading();

            Promise.all(sendData)
                .then(success => {
                    const pgj_getData   = success[0];
                    Swal.fire({
                        icon    : pgj_getData.alert.icon,
                        title   : pgj_getData.alert.message.title,
                        text    : pgj_getData.alert.message.text,
                    }).then(res => {
                        if(res.isConfirmed) {
                            // SHOW TABLE
                            closeModal('modal_pengajuan');
                            showTable('table_list_pengajuan');
                        }
                    })
                })
                .catch(err => {
                    Swal.fire({
                        icon    : err.responseJSON.alert.icon,
                        title   : err.responseJSON.alert.message.title,
                        text    : err.responseJSON.alert.message.text,
                    });
                })
        }
    }
}

function doTrans(url, type, data, message, isAsync)
{
    return new Promise((resolve, reject) => {
        $.ajax({
            url     : url,
            async   : isAsync,
            cache   : false,
            type    : type,
            headers : {
                'X-CSRF-TOKEN' : CSRF_TOKEN
            },
            data    : data,
            beforeSend  : () => {
                message;
            },
            success : (success) => {
                resolve(success);
            },
            error   : (err)     => {
                reject(err);
            }
        })
    })
}