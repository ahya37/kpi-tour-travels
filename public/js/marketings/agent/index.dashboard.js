$(document).ready(()    => {
    console.log('test')

    $("#agent_text").html("<label class='font-weight-bold no-margins'>0</label>");
})

function showModal(idModal, data, action)
{
    if(idModal == 'modal_agent') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
        showTable('table_list_agent', '');
    } else if(idModal == 'modal_tarik_data_agent') {
        closeModal('modal_agent');
        // GET DATA
        let agentURL    = "marketings/agent/tarik_data_agent";
        Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
        doTransaction(agentURL, 'GET', [], '', true)
            .then((success)     => {
                console.log(success)
                Swal.fire({
                    icon    : 'success',
                    title   : 'Berhasil',
                    text    : success.message,
                }).then((res)   => {
                    if(res.isConfirmed) {
                        showModal('modal_agent', '', '')
                    }
                })
            })
            .catch((err)        => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : err.responseJSON.message,
                }).then((res)   => {
                    if(res.isConfirmed) {
                        showModal('modal_agent', '', '');
                    }
                })
            })
    } else if(idModal == 'modal_simulasi') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false, });

        showTable('table_simulasi');
        addColumnTable('table_simulasi', 1, []);
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_agent') {
        $("#"+idModal).modal('hide');
    } else if(idModal == 'modal_simulasi') {
        $("#"+idModal).modal('hide');
    }
}

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();
    
    if(idTable == 'table_list_agent')
    {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "Tidak Ada Data Yang Bisa Dimuat",
                zeroRecords : "Data Yang Dicari Tidak Ditemukan"
            },
        })
    } else if(idTable == 'table_list_agent_umhaj') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "Tidak Ada Data Yang Bisa Dimuat",
                zeroRecords : "Data Yang Dicari Tidak Ditemukan",
            },
            pageLength  : -1,
            ordering    : false,
            bInfo       : false,
            paging      : false,
        });

        if(data.length > 0) {
            console.table(data);
        }
    } else if(idTable == 'table_simulasi') {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "Silahkan Klik Tombol Tambah Data Untuk Menambah Data",
            },
            pageLength  : -1,
            ordering    : false,
            bInfo       : false,
            paging      : false,
            searching   : false,
            columnDefs  : [
                { "targets" : [0, 1], "width" : "8%", "className" : "text-center" },
                { "targets" : [2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13], "width" : "5%",},
            ],
        })
        $("#table_simulasi_wrapper").css("padding-bottom", "0px");
    }
}

function addColumnTable(idTable, seq, data)
{
    if(idTable == 'table_simulasi') {
        let isBtnDeleteDisabled = seq > 1 ? "" : "disabled";
        let isBtnDeleteDisabledCursor   = seq > 1 ? "pointer" : "no-drop";
        let monthName           = moment.monthsShort();
        const btnDelete     = "<button class='btn btn-sm btn-danger' "+isBtnDeleteDisabled+" onclick='deleteColumnTable(`table_simulasi`, "+seq+")' style='cursor: "+isBtnDeleteDisabledCursor+"'><i class='fa fa-trash'></i></button>";
        const inputTahun    = "<input type='text' class='form-control form-control-sm text-center' id='tahun"+seq+"'value='2022' readonly>";
        const inputJan      = "<input type='text' class='form-control form-control-sm text-right' id='Jan"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Jan`, "+seq+")'>";
        const inputFeb      = "<input type='text' class='form-control form-control-sm text-right' id='Feb"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Feb`, "+seq+")'>";
        const inputMar      = "<input type='text' class='form-control form-control-sm text-right' id='Mar"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Mar`, "+seq+")'>";
        const inputApr      = "<input type='text' class='form-control form-control-sm text-right' id='Apr"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Apr`, "+seq+")'>";
        const inputMei      = "<input type='text' class='form-control form-control-sm text-right' id='Mei"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Mei`, "+seq+")'>";
        const inputJun      = "<input type='text' class='form-control form-control-sm text-right' id='Jun"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Jun`, "+seq+")'>";
        const inputJul      = "<input type='text' class='form-control form-control-sm text-right' id='Jul"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Jul`, "+seq+")'>";
        const inputAgt      = "<input type='text' class='form-control form-control-sm text-right' id='Agt"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Agt`, "+seq+")'>";
        const inputSep      = "<input type='text' class='form-control form-control-sm text-right' id='Sep"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Sep`, "+seq+")'>";
        const inputOkt      = "<input type='text' class='form-control form-control-sm text-right' id='Okt"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Okt`, "+seq+")'>";
        const inputNov      = "<input type='text' class='form-control form-control-sm text-right' id='Nov"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Nov`, "+seq+")'>";
        const inputDes      = "<input type='text' class='form-control form-control-sm text-right' id='Des"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Des`, "+seq+")'>";
        
        $("#"+idTable).DataTable().row.add([
            btnDelete,
            inputTahun,
            inputJan,
            inputFeb,
            inputMar,
            inputApr,
            inputMei,
            inputJun,
            inputJul,
            inputAgt,
            inputSep,
            inputOkt,
            inputNov,
            inputDes
        ]).draw(false);

        if(seq > 1) {
            let valueTahunSebelumnya    = $("#tahun"+ (seq - 1)).val();
            $("#tahun"+seq).val(parseFloat(valueTahunSebelumnya) + 1);
        }

        if(seq >= 3) {
            $("#btn_table_simulasi").prop('disabled', true);
            $("#btn_table_simulasi").css('cursor', 'no-drop');
        }

        let next_seq    = parseInt(seq) + 1;
        $("#btn_table_simulasi").val(next_seq);
    }
}

function deleteColumnTable(idTable, seq)
{
    console.log(seq, idTable);
    if(idTable == 'table_simulasi') {
        let currentSeq  = $("#btn_table_simulasi").val();
        let selectedSeq = seq;
        let diffSeq     = parseInt(currentSeq) -  parseInt(selectedSeq);
        if(diffSeq > 1) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Hanya Bisa Menghapus Data Terakhir',
            })
        } else {
            $("#"+idTable).DataTable().row(parseInt(seq) - 1).remove().draw(false);
            $("#btn_table_simulasi").val(parseInt(currentSeq) - 1);

            if(parseInt($("#btn_table_simulasi").val()) < 4) {
                $("#btn_table_simulasi").prop('disabled', false);
                $("#btn_table_simulasi").css('cursor', 'pointer');
            }
        }
    }
}

function simulasiHitung(idTable, column, seq)
{
    if(idTable == 'table_simulasi')
    {
        const totalData     = $("#"+idTable).DataTable().rows().count();
        let tempData        = [];
        let tempData2       = [];

        let rumus1          = Math.floor((7 * 3) * (50 / 100));
        let rumus2          = Math.floor((7 * 3) * (35 / 100));

        let jan             = $("#Jan"+seq).val() == '' ? 0 : parseInt($("#Jan"+seq).val());
        let feb             = $("#Feb"+seq).val() == '' ? 0 : parseInt($("#Feb"+seq).val());
        let mar             = $("#Mar"+seq).val() == '' ? 0 : parseInt($("#Mar"+seq).val());
        let apr             = $("#Apr"+seq).val() == '' ? 0 : parseInt($("#Apr"+seq).val());
        let mei             = $("#Mei"+seq).val() == '' ? 0 : parseInt($("#Mei"+seq).val());
        let jun             = $("#Jun"+seq).val() == '' ? 0 : parseInt($("#Jun"+seq).val());
        let jul             = $("#Jul"+seq).val() == '' ? 0 : parseInt($("#Jul"+seq).val());
        let agt             = $("#Agt"+seq).val() == '' ? 0 : parseInt($("#Agt"+seq).val());
        let sep             = $("#Sep"+seq).val() == '' ? 0 : parseInt($("#Sep"+seq).val());
        let okt             = $("#Okt"+seq).val() == '' ? 0 : parseInt($("#Okt"+seq).val());
        let nov             = $("#Nov"+seq).val() == '' ? 0 : parseInt($("#Nov"+seq).val());
        let des             = $("#Des"+seq).val() == '' ? 0 : parseInt($("#Des"+seq).val());

        const hitungTotal   = jan + feb + mar + apr + mei + jun + jul + agt + sep + okt + nov + des;

        tempData.push(jan, 
            feb, 
            mar, 
            apr, 
            mei, 
            jun, 
            jul, 
            agt, 
            sep, 
            okt, 
            nov, 
            des
        );

        let q1      = 0;
        let q2      = 0;
        let q3      = 0;
        let q4      = 0;

        for(let i = 0; i < tempData.length; i++) {
            if(tempData[i] >= 7) {
                tempData2.push({
                    "bulan_ke"      : i + 1,
                    "total_data"    : tempData[i]
                })
            }
        }

        if(jan >= 7 && feb >= 7 && mar >= 7) {
            q1  = rumus1;
        } else if(tempData2.length >= 3 && q1 == 0) {
            let temp = 0;
            for(let i = 0; i < tempData2.length; i++)
            {
                temp    += parseInt(tempData2[i]['bulan_ke']);
            }
            if(temp % 3 == 0) {
                q1  = rumus1;
            } else {
                q1  = rumus2;
            }
            tempData2.splice(0, 3);
        } else {
            q1  = 0;
        }

        $("#bonus_"+seq).html(q1 + q2 + q3 + q4);

        // HITUNG TOTAL VERTICAL
        // for(let i = 0; i < totalData; i++)
        // {
        //     let seq_1   = i + 1;
        //     if(seq_1 != seq) {
        //         let jan_1     = $("#Jan"+seq_1).val() == '' ? 0 : parseInt($("#Jan"+seq_1).val());
        //         let feb_1     = $("#Feb"+seq_1).val() == '' ? 0 : parseInt($("#Feb"+seq_1).val());
        //         let mar_1     = $("#Mar"+seq_1).val() == '' ? 0 : parseInt($("#Mar"+seq_1).val());
        //         let apr_1     = $("#Apr"+seq_1).val() == '' ? 0 : parseInt($("#Apr"+seq_1).val());
        //         let mei_1     = $("#Mei"+seq_1).val() == '' ? 0 : parseInt($("#Mei"+seq_1).val());
        //         let jun_1     = $("#Jun"+seq_1).val() == '' ? 0 : parseInt($("#Jun"+seq_1).val());
        //         let jul_1     = $("#Jul"+seq_1).val() == '' ? 0 : parseInt($("#Jul"+seq_1).val());
        //         let agt_1     = $("#Agt"+seq_1).val() == '' ? 0 : parseInt($("#Agt"+seq_1).val());
        //         let sep_1     = $("#Sep"+seq_1).val() == '' ? 0 : parseInt($("#Sep"+seq_1).val());
        //         let okt_1     = $("#Okt"+seq_1).val() == '' ? 0 : parseInt($("#Okt"+seq_1).val());
        //         let nov_1     = $("#Nov"+seq_1).val() == '' ? 0 : parseInt($("#Nov"+seq_1).val());
        //         let des_1     = $("#Des"+seq_1).val() == '' ? 0 : parseInt($("#Des"+seq_1).val());
        //     }
        // }

        $("#point_"+seq).html(hitungTotal);
    }
}

function doTransaction(url, type, data, message, isAsync)
{
    let base_url    = window.location.origin;
    return new Promise((resolve, reject)    => {
        $.ajax({
            type    : type,
            url     : base_url+"/"+url,
            async   : isAsync,
            cache   : false,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                message;
            },
            success : (success) => {
                resolve(success)
            },
            error   : (err)     => {
                reject(err)
            }
        })
    })
}