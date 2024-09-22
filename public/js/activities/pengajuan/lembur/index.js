var base_url    = window.location.origin;
var today       = moment().format('YYYY-MM-DD');
$(document).ready(()    => {
    console.log('test');

    showTable('table_list_lembur');
})

function showTable(idTable)
{
    $("#"+idTable).DataTable().clear().destroy();
    if(idTable == 'table_list_lembur')
    {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat.."
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0, 4], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "text-center align-middle", "width" : "20%" },
                { "targets" : [2], "className" : "text-left align-middle" },
                { "targets" : [3], "className" : "text-center align-middle", "width" : "15%" },
            ],
        });

        // GET DATA
        const lmb_url   = base_url + "/pengajuan/lembur/list_lembur";
        const lmb_type  = "GET";
        const lmb_data  = "";
        const lmb_msg   = "";
        
        doTrans(lmb_url, lmb_type, lmb_data, lmb_msg, true)
            .then((success)     => {

                const emp_data  = success.data;
                let emp_seq     = 1;
                if(emp_data.length > 0) {
                    $(".dataTables_empty").html("Data Berhasil Dimuat");

                    for(const emp_item of emp_data) {
                        
                        switch(emp_item['emp_trans_status'])
                        {
                            case '1' :
                                var emp_status  = "<span class='badge badge-pills badge-primary'>Diterima</span>";
                            break;
                            case '2' : 
                                var emp_status  = "<span class='badge badge-pills badge-danger'>Ditolak</span>";
                            break;
                            case '3' :
                                var emp_status  = "<span class='badge badge-pills badge-warning'>Menunggu Konfirmasi</span>"; 
                            break;
                        }


                        $("#"+idTable).DataTable().row.add([
                            emp_seq++,
                            moment(emp_item['emp_act_date'], 'YYYY-MM-DD').format('DD/MM/YYYY'),
                            emp_item['emp_description'],
                            emp_status,
                            "<button type='button' class='btn btn-sm btn-primary' value='"+emp_item[`emp_act_id`]+"' onclick='showModal(`modal_buat_lemburan`, this.value)' title='Lihat Data'><i class='fa fa-eye'></i></button>"
                        ]).draw(false)
                    }
                } else {
                    $(".dataTables_empty").html("Tidak Ada Data Yang Bisa Ditampilkan..");
                }
            })
            .catch((err)        => {
                console.log(err);
            })
    } else if(idTable == 'table_list_lembur_detail') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "Tambahkan Beberapa Data",
            },
            searching   : false,
            pageLength  : -1,
            bInfo       : false,
            paging      : false,
            ordering    : false,
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0, 5], "width" : "10%", "className" : "text-center align-middle" },
                { "targets" : [1, 3, 4], "width" : "15%", "className" : "text-center align-middle" },
            ],
        });
    }
}

function showModal(idModal, data)
{
    if(idModal == 'modal_buat_lemburan')
    {
        // FILL FORM
        $("#lmb_name_id").val($("#emp_id").val());
        $("#lmb_name").val($("#emp_name").val());
        $("#lmb_divisi").val($("#emp_divisi").val());
        // SHOW TABLE
        showTable('table_list_lembur_detail');

        if(data == '') {
            $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            $("#btn_simpan").val('add');
            addRow('table_list_lembur_detail', 1, '');
        } else {
            $("#btn_simpan").val('edit');
            const lmb_id    = data;
            const lmb_url   = base_url + "/pengajuan/lembur/get_data/";
            const lmb_data  = {
                "lmb_id"    : lmb_id,
            };
            const lmb_type  = "GET";
            const lmb_msg   = Swal.fire({ title : 'Data Sedang Dimuat', allowOutsideClick : false }); Swal.showLoading();

            doTrans(lmb_url, lmb_type, lmb_data, lmb_msg, true)
                .then((success)     => {    
                    Swal.close();
                    // HEADER
                    const lmb_data_header   = success.data['header'][0];

                    $("#lmb_id").val(lmb_data_header['emp_act_id']);
                    $("#lmb_name_id").val(lmb_data_header['emp_user_id']);
                    $("#lmb_name").val(lmb_data_header['emp_user_name']);
                    $("#lmb_divisi").val(lmb_data_header['emp_group_division']);
                    $("#lmb_keterangan").val(lmb_data_header['emp_act_description']);

                    // DETAIL
                    const lmb_data_detail   = success.data['detail'];
                    let total_data          = lmb_data_detail.length;
                    for(let i = 0; i < total_data; i++)
                    {
                        addRow('table_list_lembur_detail', i + 1, lmb_data_detail[i]);
                    }
                    addRow('table_list_lembur_detail', total_data + 1, '');
                    $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                })
                .catch((err)        => {
                    console.log(err);
                    Swal.close();
                    addRow('table_list_lembur_detail', 1, '');
                    $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                })
        }

        $("#"+idModal).on('shown.bs.modal', () => {
            if(data == "")
            {
                $("#lmb_desc1").focus();
            }
        })
    }
}

function closeModal(idModal)
{
    $("#"+idModal).modal('hide');

    if(idModal == 'modal_buat_lemburan') {
        $("#btn_tambah_baris").val(1);
    }
}

function addRow(idTable, seq, data)
{
    if(idTable == 'table_list_lembur_detail')
    {
        let current_row     = parseInt($("#btn_tambah_baris").val());
        let next_row        = current_row + 1;

        const lmb_act_del   = "<button class='btn btn-danger' title='Hapus Baris' value='" +seq+ "' onclick='deleteRow(`"+idTable+"`, "+seq+")'><i class='fa fa-trash'></i></button>";
        const lmb_date      = "<input type='text' class='form-control' id='lmb_date"+seq+"' placeholder='DD/MM/YYYY' readonly style='background: white; cursor: pointer;'>";
        const lmb_desc      = "<input type='text' class='form-control' id='lmb_desc"+seq+"' placeholder='Uraian Pekerjaan' autocomplete='off'>";
        const lmb_t_start   = "<input type='text' class='form-control' id='lmb_t_start"+seq+"' placeholder='HH:MM:SS' readonly style='background: white; cursor: pointer;'>";
        const lmb_t_end     = "<input type='text' class='form-control' id='lmb_t_end"+seq+"' placeholder='HH:MM:SS' readonly style='background: white; cursor: pointer;'>";
        const lmb_act_acc   = "";
        $("#"+idTable).DataTable().row.add([
            lmb_act_del,
            lmb_date,
            lmb_desc,
            lmb_t_start,
            lmb_t_end,
            lmb_act_acc
        ]).draw(false);

        $("#lmb_date"+seq).daterangepicker({
            singleDatePicker    : true,
            minDate             : moment(today, 'YYYY-MM-DD').subtract(1, 'years').format('DD/MM/YYYY'),
            maxDate             : moment(today, 'YYYY-MM-DD').add(1, 'years').format('DD/MM/YYYY'),
            locale              : {
                format  : 'DD/MM/YYYY'
            },
            autoApply           : true,
        });
        
        $("#lmb_t_start"+seq).daterangepicker({
            singleDatePicker    : true,
            timePicker          : true,
            timePicker24Hour    : true,
            timePickerIncerement: 1,
            locale              : {
                format: 'HH:mm'
            },
            autoApply           : true,
        }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find(".calendar-table").hide();
        });

        $("#lmb_t_end"+seq).daterangepicker({
            singleDatePicker    : true,
            timePicker          : true,
            timePicker24Hour    : true,
            timePickerIncerement: 1,
            locale              : {
                format: 'HH:mm'
            },
            autoApply           : true,
        }).on('show.daterangepicker', function (ev, picker) {
            picker.container.find(".calendar-table").hide();
        });

        $("#lmb_desc"+seq).on('keyup', () => {
            const description   = $("#lmb_desc"+seq).val();
            $("#lmb_desc"+seq).val(description.toUpperCase());
        })

        if(data != '') {
            $("#lmb_date"+seq).data('daterangepicker').setStartDate(moment(data['empd_date'], 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#lmb_date"+seq).data('daterangepicker').setEndDate(moment(data['empd_date'], 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#lmb_desc"+seq).val(data['empd_description']);
            $("#lmb_t_start"+seq).val(moment(data['empd_start_time'], 'YYYY-MM-DD HH:mm:ss').format('HH:mm'));
            $("#lmb_t_end"+seq).val(moment(data['empd_end_time'], 'YYYY-MM-DD HH:mm:ss').format('HH:mm'));
        } else {
            $("#lmb_desc"+seq).focus();
        }

        $("#btn_tambah_baris").val(parseInt(next_row));
    }
}

function deleteRow(idTable, seq)
{
    if(idTable == 'table_list_lembur_detail')
    {
        let current_seq     = $("#btn_tambah_baris").val();
        let column_row      = seq - 1;
        if(seq  == 1) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Baris Pertama Tidak Bisa Dihapus..',
            });
        } else {
            if(parseInt(current_seq) - seq == 1) {
                $("#"+idTable).DataTable().row(column_row).remove().draw(false);
                $("#btn_tambah_baris").val(parseInt(current_seq) - 1 );
                $("#lmb_desc"+(seq - 1)).focus();
            } else {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Hanya Baris Pertama yang bisa dihapus..'
                })
            }
        }
    }
}

function simpanData(idForm, jenisSimpan)
{
    if(idForm == 'lemburan') {
        // HEADER
        const lmb_id        = $("#lmb_id").val();
        const lmb_id_user   = $("#lmb_name_id").val();
        const lmb_name      = $("#lmb_name").val();
        const lmb_divisi    = $("#lmb_divisi").val();
        const lmb_desc      = $("#lmb_keterangan").val();

        // DETAIL
        const detail_data   = $("#table_list_lembur_detail").DataTable().rows().count();
        const lmbd_data     = [];
        for(let i = 0; i < detail_data; i++)
        {
            const lmbd_seq  = i + 1;
            const lmbd_date = $("#lmb_date"+lmbd_seq).val();
            const lmbd_desc = $("#lmb_desc"+lmbd_seq).val();
            const lmbd_start_time   = $("#lmb_t_start"+lmbd_seq).val();
            const lmbd_end_time     = $("#lmb_t_end"+lmbd_seq).val();
            
            const lmbd_detail_data  = {
                "lmbd_seq"          : lmbd_seq,
                "lmbd_date"         : moment(lmbd_date, 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "lmbd_desc"         : lmbd_desc,
                "lmbd_start_time"   : moment(lmbd_date, 'DD/MM/YYYY').format('YYYY-MM-DD')+" "+lmbd_start_time+":00",
                "lmbd_end_time"     : moment(lmbd_date, 'DD/MM/YYYY').format('YYYY-MM-DD')+" "+lmbd_end_time+":00",
            };

            lmbd_data.push(lmbd_detail_data);
        }

        const lmb_sendData     = {
            "header"    : {
                "lmb_id"            : lmb_id,
                "lmb_user_id"       : lmb_id_user,
                "lmb_user_name"     : lmb_name,
                "lmb_user_division" : lmb_divisi,
                "lmb_description"   : lmb_desc,
            },
            "detail"    : lmbd_data,
        };
        const lmb_type          = "POST";
        const lmb_url           = base_url + "/pengajuan/lembur/simpan/"+jenisSimpan;
        const lmb_message       = Swal.fire({ title : "Data Sedang Diproses" }); Swal.showLoading();

        doTrans(lmb_url, lmb_type, lmb_sendData, lmb_message, true)
            .then((success) => {
                Swal.fire({
                    icon    : success.alert.icon,
                    title   : success.alert.message.title,
                    text    : success.alert.message.text,
                }).then((results)   => {
                    if(results.isConfirmed) {
                        closeModal('modal_buat_lemburan');
                        showTable('table_list_lembur', '');
                    }
                })
            })
            .catch((err)    => {
                Swal.fire({
                    icon    : err.responseJSON.alert.icon,
                    title   : err.responseJSON.alert.message.title,
                    text    : err.responseJSON.alert.message.text,
                })
            })
    }
}

function doTrans(url, type, data, customMessage, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            async   : isAsync,
            cache   : false,
            type    : type,
            url     : url,
            dataType: "json",
            beforeSend  : function() {
                customMessage;
            },
            headers  : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            success : function(xhr) {
                resolve(xhr);
            },
            error   : function(xhr) {
                reject(xhr);
                console.log(xhr);
            }
        });
    })
}