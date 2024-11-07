var base_url    = window.location.origin;
var today       = moment().format('YYYY-MM-DD');
var dataBulan   = [];

if(dataBulan.length == 0) {
    for(let i = 0; i < 12; i++) {
        dataBulan.push({
            "bulan_ke"  : moment(i + 1, 'M').format('MM'),
            "bulan_name": moment(i + 1, 'M').format('MMMM')
        })
    }
}

$(document).ready(()    => {
    let currMonth   = moment(today, 'YYYY-MM-DD').format('MM');
    showTable('table_list_lembur', currMonth);

    // SHOW SELECT
    let selectedBulan   = moment(today, 'YYYY-MM-DD').format('MM');
    showSelect('pgj_lmb_select_month', dataBulan, selectedBulan, '')
})

function showTable(idTable, data)
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
                { "targets" : [2], "className" : "txt-left align-middle" },
                { "targets" : [3], "className" : "text-center align-middle", "width" : "15%" },
            ],
        });

        // GET DATA
        const lmb_url   = base_url + "/pengajuan/lembur/list_lembur";
        const lmb_type  = "GET";
        const lmb_data  = {
            "bulan"     : data,
        };
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
                            break;r
                        }

                        let startTime   = moment(emp_item['emp_start_time'], 'YYYY-MM-DD HH:mm:ss').format('HH:mm');
                        let endTime     = moment(emp_item['emp_end_time'], 'YYYY-MM-DD HH:mm:ss').format('HH:mm');

                        $("#"+idTable).DataTable().row.add([
                            emp_seq++,
                            moment(emp_item['emp_act_date'], 'YYYY-MM-DD').format('DD/MM/YYYY') + " (" + startTime + " - " + endTime + ")",
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
    }
}

function showSelect(idSelect, data, selectedData, seq)
{
    // DEFAULT SETTING
    $("#"+idSelect).select2({
        theme   : 'bootstrap4'
    })

    if(idSelect == 'pgj_lmb_select_month') {
        let html    = "<option selected disabled>Pilih Bulan</option>";

        for(const item of data)
        {
            html    += `<option value="${item['bulan_ke']}">${item['bulan_name']}</option>`;
        }

        $("#"+idSelect).html(html);

        if(selectedData != '') {
            $("#"+idSelect).val(selectedData);
        }
    }
}

function showSelectDetail(idSelect, value)
{
    
    if(idSelect == "pgj_lmb_select_month") {
        showTable('table_list_lembur', value);
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

        $("#lmb_date").daterangepicker({
            singleDatePicker    : true,
            minDate             : moment(today, 'YYYY-MM-DD').subtract(1, 'years').format('DD/MM/YYYY'),
            maxDate             : moment(today, 'YYYY-MM-DD').add(1, 'years').format('DD/MM/YYYY'),
            locale              : {
                format  : 'DD/MM/YYYY'
            },
            autoApply           : true,
        });

        $("#lmb_start_time").daterangepicker({
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

        $("#lmb_end_time").daterangepicker({
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

        $("#"+idModal).on('shown.bs.modal', () => {
            $("#lmb_keterangan").focus();
        });

        if(data == '') {
            $("#btn_save_modal_buat_pengajuan").val('add');
            $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            $("#btn_simpan").val('add');
        } else {
            $("#btn_save_modal_buat_pengajuan").val('edit');
            const lmb_id    = data;
            const lmb_url   = base_url + "/pengajuan/lembur/get_data/";
            const lmb_data  = {
                "lmb_id"    : lmb_id,
            };
            const lmb_type  = "GET";
            const lmb_msg   = Swal.fire({ title : 'Data Sedang Dimuat'}); Swal.showLoading();

            doTrans(lmb_url, lmb_type, lmb_data, lmb_msg, true)
                .then((success)     => { 
                    Swal.close();
                    $("#"+idModal).modal({backdrop: 'static', keyboard: false});

                    // FILL FOFRM
                    $("#lmb_id").val(success.data.header[0].emp_act_id);
                    $("#lmb_keterangan").val(success.data.header[0].emp_act_description);
                    
                    $("#lmb_date").data('daterangepicker').setStartDate(moment(success.data.header[0].emp_act_date, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                    $("#lmb_date").data('daterangepicker').setEndDate(moment(success.data.header[0].emp_act_date, 'YYYY-MM-DD').format('DD/MM/YYYY'));
                    $("#lmb_start_time").data('daterangepicker').setStartDate(moment(success.data.detail[0].empd_start_time));
                    $("#lmb_start_time").data('daterangepicker').setEndDate(moment(success.data.detail[0].empd_start_time));
                    $("#lmb_end_time").data('daterangepicker').setStartDate(moment(success.data.detail[0].empd_end_time));
                    $("#lmb_end_time").data('daterangepicker').setEndDate(moment(success.data.detail[0].empd_end_time));

                    $("#lmb_keterangan_length").html(success.data.header[0].emp_act_description.length+"/100");


                })
                .catch((err)        => {
                    console.log(err);
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : 'Data Yang Dicari Tidak Ditemukan'
                    })
                })
        }
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_buat_lemburan') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {

            $("#lmb_keterangan_length").html("0/100");
            $("#lmb_name_id").val('');
            $("#lmb_name").val('');
            $("#lmb_divisi").val('');
            $("#lmb_keterangan").val('');
            $("#lmb_date").data('daterangepicker').setStartDate(moment(today, 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#lmb_date").data('daterangepicker').setEndDate(moment(today, 'YYYY-MM-DD').format('DD/MM/YYYY'));
            $("#lmb_start_time").val('00:00');
            $("#lmb_end_time").val('00:00');
        })
    }
}

function simpanData(idForm, jenisSimpan)
{
    if(idForm == 'lemburan') {
        let lmb_keterangan      = $("#lmb_keterangan");
        let lmb_tanggal         = $("#lmb_date");
        let lmb_waktu_mulai     = $("#lmb_start_time");
        let lmb_waktu_akhir     = $("#lmb_end_time");

        if(lmb_keterangan.val() == '') {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Keterangan Tidak Boleh Kosong',
                didClose    : () => {
                    lmb_keterangan.focus();
                }
            })
        } else if(lmb_keterangan.val().length > 100) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Hanya Bisa Menampung 100 Karakter',
                didClose    : () => {
                    lmb_keterangan.focus();
                }
            })
        } else {
            const lmb_sendData = {
                "lmb_act_id"    : $("#lmb_id").val(),
                "lmb_user_id"   : $("#lmb_id").val(),
                "lmb_keterangan": lmb_keterangan.val(),
                "lmb_tanggal"   : moment(lmb_tanggal.val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "lmb_t_start"   : moment(lmb_tanggal.val(), 'DD/MM/YYYY').format('YYYY-MM-DD')+" "+lmb_waktu_mulai.val()+":00",
                "lmb_t_end"     : moment(lmb_tanggal.val(), 'DD/MM/YYYY').format('YYYY-MM-DD')+" "+lmb_waktu_akhir.val()+":00",
            };

            const lmb_url       = base_url + "/pengajuan/lembur/simpan/"+jenisSimpan;
            const lmb_msg       = Swal.fire({ title : 'Data Sedang Diproses..' }); Swal.showLoading();
            const lmb_type      = "POST";

            doTrans(lmb_url, lmb_type, lmb_sendData, lmb_msg, true)
                .then((success) => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text,
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            closeModal('modal_buat_lemburan');
                            showTable('table_list_lembur');
                        }
                    })
                })
                .catch((err)    => {
                    console.log(err)
                })
        }
    }
}

// ADDITIONAL
function textToUppercase(idForm, value)
{
    if(idForm == 'lmb_keterangan') {
        $("#"+idForm).val(value.toUpperCase());

        $("#lmb_keterangan_length").html(value.length+"/100");

        $("#"+idForm).val().length > 100 ? $("#"+idForm).addClass('is-invalid') : $("#"+idForm).removeClass('is-invalid');
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