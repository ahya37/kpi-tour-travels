$(document).ready(()    => {
    // GET DATA FOR DASHBOARD
    const agentURL  = "marketings/agent/tarik_data_agent_local";
    
    const getData   = [
        doTransaction(agentURL, "GET", [], "", true)
    ];

    Promise.allSettled(getData)
        .then((success)     => {
            const agentGetData  = success[0].value.data;
            $("#agent_text").html("<label class='font-weight-bold no-margins'>" + agentGetData.length + "</label>");
        })
        .catch((err)        => {
            $("#agent_text").html("<label class='font-weight-bold no-margins'>0</label>");
            console.log({err})
        })
})

function showModal(idModal, data, action)
{
    if(idModal == 'modal_agent') {
        // GET DATA AGENT
        let agentURL    = "marketings/agent/tarik_data_agent_local";
        let agentMsg    = Swal.fire({ title : 'Data Sedang Dimuat..', allowOutsideClick: false }); Swal.showLoading();
        doTransaction(agentURL, 'GET', [], agentMsg, true)
            .then((success)     => {
                Swal.close();
                const agentGetData  = success.data;
                showTable('table_list_agent', agentGetData);
                // SHOW MODAL
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
            })
            .catch((err)        => {
                console.log(err)
                Swal.close();
                $("#"+idModal).modal({ backdrop: 'static', keyboard: false });
                showTable('table_list_agent', []);
            })
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
    } else if(idModal == 'modal_data_agent') {
        closeModal('modal_agent');
        if(action == 'add') {            
            $("#"+idModal).modal({backdrop: 'static', keyboard: false});
            $("#modal_data_agent_title").html('Tambah Data Agent Baru');
            
            $("#"+idModal).on('shown.bs.modal', () => {
                $("#agt_name").focus();
                $("#modal_data_agent_simpan").val(action);
            })
        } else if(action == 'edit') {
            $("#modal_data_agent_title").html('Ubah Data Agent');

            const agentURL  = "marketings/agent/ambil_data/"+data;
            const agentMsg  = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();
            doTransaction(agentURL, "GET", [], agentMsg, true)
                .then((success)     => {
                    const agentGetData  = success.data;
                    $("#"+idModal).modal({backdrop : 'static', keyboard: false});
                    
                    // FILL FORM
                    $("#agt_id").val(agentGetData.agt_id);
                    $("#agt_name").val(agentGetData.agt_name);
                    $("#agt_pic").val(agentGetData.agt_pic);
                    $("#agt_address").val(agentGetData.agt_address);
                    $("#agt_contact_1").val(agentGetData.agt_contact_1);
                    $("#agt_contact_2").val(agentGetData.agt_contact_2);
                    $("#agt_fax").val(agentGetData.agt_fax);
                    $("#agt_email").val(agentGetData.agt_email);
                    $("#agt_note").val(agentGetData.agt_note);

                    $("#"+idModal).on('shown.bs.modal', () => {
                        $("#modal_data_agent_simpan").val(action);
                    })
                    
                    Swal.close();
                })
                .catch((err)        => {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : err.responseJSON.message,
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            showTable('table_list_agent', []);
                        }
                    })
                })
        }
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_agent') {
        $("#"+idModal).modal('hide');
    } else if(idModal == 'modal_simulasi') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            for(let i = 0; i < 3; i++) {
                let seq     = i + 1;
                $("#point_"+seq).html(0);
                $("#bonus_"+seq).html(0);
                $("#total_"+seq).html(0);
            }
            
            $("#card_umrah_reward").addClass('d-none');
            $("#total_reward").html(0);
        })
    } else if(idModal == 'modal_data_agent') {
        $("#"+idModal).modal('hide');
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#agt_id").val("");
            $("#agt_name").val("");
            $("#agt_pic").val("");
            $("#agt_address").val("");
            $("#agt_contact_1").val("");
            $("#agt_contact_2").val("");
            $("#agt_fax").val("");
            $("#agt_email").val("");
            $("#agt_note").val("");
        })
        showModal('modal_agent', '', 'view');
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
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0, 4], "className" : "text-center", "width" : "5%" },
                { "targets" : [1, 2], "className" : "text-left", "width" : "30%" },
            ],
        })

        if(data.length > 0) {
            let seq = 1;
            for(const item of data)
            {
                $("#"+idTable).DataTable().row.add([
                    `<label class='font-weight-normal no-margins'>${seq++}</label>`,
                    `<label class='font-weight-normal no-margins'>${item.agt_name}</label>`,
                    `<label class='font-weight-normal no-margins'>${item.agt_pic}</label>`,
                    item.agt_contact_1.length < 2 ? `<label class='font-weight-normal no-margins'>${item.agt_contact_2}</label>` : `<label class='font-weight-normal no-margins'>${item.agt_contact_1+" & "+item.agt_contact_2}</label>`,
                    `<button class='btn btn-sm btn-primary' title='Edit Data' value='${item.agt_id}' onclick='showModal("modal_data_agent", this.value, "edit")'><i class='fa fa-edit'></i></button>`
                ]).draw(false);
            }
        }
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
        const inputJan      = "<input type='text' class='form-control form-control-sm text-right' id='Jan"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Jan`, "+seq+")' autocomplete='off'>";
        const inputFeb      = "<input type='text' class='form-control form-control-sm text-right' id='Feb"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Feb`, "+seq+")' autocomplete='off'>";
        const inputMar      = "<input type='text' class='form-control form-control-sm text-right' id='Mar"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Mar`, "+seq+")' autocomplete='off'>";
        const inputApr      = "<input type='text' class='form-control form-control-sm text-right' id='Apr"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Apr`, "+seq+")' autocomplete='off'>";
        const inputMei      = "<input type='text' class='form-control form-control-sm text-right' id='Mei"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Mei`, "+seq+")' autocomplete='off'>";
        const inputJun      = "<input type='text' class='form-control form-control-sm text-right' id='Jun"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Jun`, "+seq+")' autocomplete='off'>";
        const inputJul      = "<input type='text' class='form-control form-control-sm text-right' id='Jul"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Jul`, "+seq+")' autocomplete='off'>";
        const inputAgt      = "<input type='text' class='form-control form-control-sm text-right' id='Agt"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Agt`, "+seq+")' autocomplete='off'>";
        const inputSep      = "<input type='text' class='form-control form-control-sm text-right' id='Sep"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Sep`, "+seq+")' autocomplete='off'>";
        const inputOkt      = "<input type='text' class='form-control form-control-sm text-right' id='Okt"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Okt`, "+seq+")' autocomplete='off'>";
        const inputNov      = "<input type='text' class='form-control form-control-sm text-right' id='Nov"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Nov`, "+seq+")' autocomplete='off'>";
        const inputDes      = "<input type='text' class='form-control form-control-sm text-right' id='Des"+seq+"' value='0' onkeyup='simulasiHitung(`table_simulasi`, `Des`, "+seq+")' autocomplete='off'>";
        
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

        // HITUNG TOTAL UNTUK FOOTER
        let totalJan    = 0;
        let totalFeb    = 0;
        let totalMar    = 0;
        let totalApr    = 0;
        let totalMei    = 0;
        let totalJun    = 0;
        let totalJul    = 0;
        let totalAgt    = 0;
        let totalSep    = 0;
        let totalOkt    = 0;
        let totalNov    = 0;
        let totalDes    = 0;

        let prevJan     = 0;
        let prevFeb    = 0;
        let prevMar    = 0;
        let prevApr    = 0;
        let prevMei    = 0;
        let prevJun    = 0;
        let prevJul    = 0;
        let prevAgt    = 0;
        let prevSep    = 0;
        let prevOkt    = 0;
        let prevNov    = 0;
        let prevDes    = 0;

        const tableData     = $("#"+idTable).DataTable().rows().count();

        for(let i = 0; i < tableData; i++)
        {
            let currentSeq  = i + 1;
            if(currentSeq != seq) {
                prevJan     += parseInt($("#Jan"+currentSeq).val());
                prevFeb     += parseInt($("#Feb"+currentSeq).val());
                prevMar     += parseInt($("#Mar"+currentSeq).val());
                prevApr     += parseInt($("#Apr"+currentSeq).val());
                prevMei     += parseInt($("#Mei"+currentSeq).val());
                prevJun     += parseInt($("#Jun"+currentSeq).val());
                prevJul     += parseInt($("#Jul"+currentSeq).val());
                prevAgt     += parseInt($("#Agt"+currentSeq).val());
                prevSep     += parseInt($("#Sep"+currentSeq).val());
                prevOkt     += parseInt($("#Okt"+currentSeq).val());
                prevNov     += parseInt($("#Nov"+currentSeq).val());
                prevDes     += parseInt($("#Des"+currentSeq).val());
            }
        }

        totalJan    = parseInt(jan) + parseInt(prevJan);
        totalFeb    = parseInt(feb) + parseInt(prevFeb);
        totalMar    = parseInt(mar) + parseInt(prevMar);
        totalApr    = parseInt(apr) + parseInt(prevApr);
        totalMei    = parseInt(mei) + parseInt(prevMei);
        totalJun    = parseInt(jun) + parseInt(prevJun);
        totalJul    = parseInt(jul) + parseInt(prevJul);
        totalAgt    = parseInt(agt) + parseInt(prevAgt);
        totalSep    = parseInt(sep) + parseInt(prevSep);
        totalOkt    = parseInt(okt) + parseInt(prevOkt);
        totalNov    = parseInt(nov) + parseInt(prevNov);
        totalDes    = parseInt(des) + parseInt(prevDes);
        
        $("#total_Jan").html(totalJan);
        $("#total_Feb").html(totalFeb);
        $("#total_Mar").html(totalMar);
        $("#total_Apr").html(totalApr);
        $("#total_Mei").html(totalMei);
        $("#total_Jun").html(totalJun);
        $("#total_Jul").html(totalJul);
        $("#total_Agt").html(totalAgt);
        $("#total_Sep").html(totalSep);
        $("#total_Okt").html(totalOkt);
        $("#total_Nov").html(totalNov);
        $("#total_Des").html(totalDes);

        totalJan    = jan;
        totalFeb    = feb;
        totalMar    = mar;
        totalApr    = apr;
        totalMei    = mei;
        totalJun    = jun;
        totalJul    = jul;
        totalAgt    = agt;
        totalSep    = sep;
        totalOkt    = okt;
        totalNov    = nov;
        totalDes    = des;

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

        var q1      = 0;
        var q2      = 0;
        var q3      = 0;
        var q4      = 0;
        let q1Temp  = [];
        let q2Temp  = [];
        let q3Temp  = [];
        let q4Temp  = [];

        // KELOMPOKAN TERLEBIH DAHULU AGAR BISA GROUPING 3 BULAN
        for(let i = 0; i < tempData.length; i++) {
            let seq  = 1;
            if(tempData[i] >= 7) {
                if(seq == 1 && tempData2.length < 3) {
                    tempData2.push({
                        "seq"       : seq,
                        "bulan_ke"  : i + 1,
                        "total_data": tempData[i]
                    })
                } else {
                    tempData2.push({
                        "seq"       : tempData2.length < 6 ? seq + 1 : (tempData2.length < 9 ? seq + 2 : seq + 3),
                        "bulan_ke"  : i + 1,
                        "total_data": tempData[i]
                    })
                }
            }
        }

        if(q1Temp.length < 1 && tempData2.length >= 3) {
            q1Temp.push(tempData2.slice(0, 3));
            tempData2.splice(0, 3);
        }
        
        if(q2Temp.length < 1 && tempData2.length >= 3) {
            q2Temp.push(tempData2.slice(0, 3));
            tempData2.splice(0, 3);
        }

        if(q3Temp.length < 1 && tempData2.length >= 3) {
            q3Temp.push(tempData2.slice(0, 3));
            tempData2.splice(0, 3);
        }
        if(q4Temp.length < 1 && tempData2.length >= 3) {
            q4Temp.push(tempData2.slice(0, 3));
            tempData2.splice(0, 3);
        }   

        if(q1Temp.length > 0) {
            if(q1Temp[0].length > 0) {
                const bulan_ke      = q1Temp[0][0]['bulan_ke'];
                const bulan_ke_1    = q1Temp[0][1]['bulan_ke'];
                const bulan_ke_2    = q1Temp[0][2]['bulan_ke'];

                if((bulan_ke == bulan_ke_1 - 1) && (bulan_ke == bulan_ke_2 -2)) {
                    q1  = rumus1;
                } else {
                    q1  = rumus2;
                }
            } else {
                q1  = 0;
            }
        } else {
            q1  = 0;
        }

        if(q2Temp.length > 0) {
            if(q2Temp[0].length > 0) {
                const bulan_ke      = q2Temp[0][0]['bulan_ke'];
                const bulan_ke_1    = q2Temp[0][1]['bulan_ke'];
                const bulan_ke_2    = q2Temp[0][2]['bulan_ke'];

                if((bulan_ke == bulan_ke_1 - 1) && (bulan_ke == bulan_ke_2 -2)) {
                    q2  = rumus1;
                } else {
                    q2  = rumus2;
                }
            } else {
                q2  = 0;
            }
        } else {
            q2  = 0;
        }

        if(q3Temp.length > 0) {
            if(q3Temp[0].length > 0) {
                const bulan_ke      = q3Temp[0][0]['bulan_ke'];
                const bulan_ke_1    = q3Temp[0][1]['bulan_ke'];
                const bulan_ke_2    = q3Temp[0][2]['bulan_ke'];

                if((bulan_ke == bulan_ke_1 - 1) && (bulan_ke == bulan_ke_2 -2)) {
                    q3  = rumus1;
                } else {
                    q3  = rumus2;
                }
            } else {
                q3  = 0;
            }
        } else {
            q3  = 0;
        }

        if(q4Temp.length > 0) {
            if(q4Temp[0].length > 0) {
                const bulan_ke      = q4Temp[0][0]['bulan_ke'];
                const bulan_ke_1    = q4Temp[0][1]['bulan_ke'];
                const bulan_ke_2    = q4Temp[0][2]['bulan_ke'];

                if((bulan_ke == bulan_ke_1 - 1) && (bulan_ke == bulan_ke_2 -2)) {
                    q4  = rumus1;
                } else {
                    q4  = rumus2;
                }
            } else {
                q4  = 0;
            }
        } else {
            q4  = 0;
        }

        let hitungPoint   = jan + feb + mar + apr + mei + jun + jul + agt + sep + okt + nov + des;
        let hitungBonus     = q1 + q2 + q3 + q4;
        let hitungTotal     = parseInt(hitungPoint) + parseInt(hitungBonus);

        $("#bonus_"+seq).html(hitungBonus);
        $("#point_"+seq).html(hitungPoint);
        $("#total_"+seq).html(hitungTotal);

        if(seq == 1) {
            if(hitungTotal >= 50) {
                $("#sisa_1").html(hitungTotal - 50);
                $("#card_umrah_reward").removeClass('d-none');
                $("#total_reward").html(1);
            } else {
                $("#sisa_1").html(0);
                $("#total_reward").html(0);
            }
        } else if(seq == 2) {
            let hitungTotalPrev     = $("#total_1").text();
            let hitungTotalCurr     = $("#total_2").text();
            let currentTotalReward  = $("#total_reward").text();

            if(parseInt(hitungTotalPrev) >= 50) {
                hitungTotalPrev     = hitungTotalPrev - 50;
                $("#total_reward").html(1);
            }
            
            if(parseInt(hitungTotalCurr) >= 50) {
                $("#card_umrah_reward").removeClass('d-none');
                if(parseInt($("#total_reward").text()) > 0) {
                    let newTotalReward  = parseInt($("#total_reward").text()) + 1;
                    $("#total_reward").html(parseInt(newTotalReward));
                    $("#sisa_2").html(hitungTotalCurr - 50);
                }
            } else if(parseInt(hitungTotalCurr) < 50) {
                $("#sisa_2").html(0);
                // CHECK DULU SISA POINT SEBELUMNYA
                let hitungPointBaru     = parseInt(hitungTotalPrev) + parseInt(hitungTotalCurr);
                if(hitungPointBaru >= 80) {
                    $("#card_umrah_reward").removeClass('d-none');
                    let newTotalReward  = parseInt($("#total_reward").text()) + 1;
                    $("#total_reward").html(parseInt(newTotalReward));
                }
            }
        } else if(seq == 3) {
            let totalPoint_1    = $("#total_1").text();
            let totalPoint_2    = $("#total_2").text();
            let totalPoint_3    = $("#total_3").text();

            let sisaPoint_1     = $("#sisa_1").text();
            let sisaPoint_2     = $("#sisa_2").text();

            if(parseInt(totalPoint_1) >= 50) {
                $("#card_umrah_reward").removeClass('d-none');
                $("#total_reward").html(1);
            }

            if(parseInt(totalPoint_2) >= 50) {
                $("#card_umrah_reward").removeClass('d-none');
                $("#total_reward").html(2);
            }

            if(parseInt(totalPoint_3) >= 50) {
                $("#card_umrah_reward").removeClass('d-none');
                
                if(parseInt($("#total_reward").text()) < 3 && parseInt($("#total_reward").text()) >= 2) {
                    $("#total_reward").html(3);
                } else {
                    $("#total_reward").html(parseInt($("#total_reward").text()) + 1);
                }

                $("#sisa_3").html(parseInt(totalPoint_3) - 50);
            } else {
                $("#sisa_3").html(0);
            }
        }
    }
}

function doSimpanData(idForm, jenis)
{
    if(idForm == 'modal_data_agent')
    {
        const agtID         = $("#agt_id");
        const agtName       = $("#agt_name");
        const agtPIC        = $("#agt_pic");
        const agtAddress    = $("#agt_address");
        const agtContact1   = $("#agt_contact_1");
        const agtContact2   = $("#agt_contact_2");
        const agtFax        = $("#agt_fax");
        const agtEmail      = $("#agt_email");
        const agtNote       = $("#agt_note");

        if(agtName.val() == "") {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Nama Agent Tidak Boleh Kosong',
            }).then((results)   => {
                if(results.isConfirmed) {
                    agtName.addClass('is-invalid');
                }
            })
        } else if(agtPIC.val() == "") {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'PIC Tidak Boleh Kosong',
            }).then((results)   => {
                if(results.isConfirmed) {
                    agtPIC.addClass('d-none');
                }
            })
        } else if(agtAddress.val() == "") {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Alamat Harus Diisi',
            }).then((results)   => {
            if(results.isConfirmed) {
                    agtAddress.addClass('is-invalid');
                }
            })
        } else {
            const agtSendData   = {
                "agent_id"      : agtID.val(),
                "agent_name"    : agtName.val(),
                "agent_pic"     : agtPIC.val(),
                "agent_address" : agtAddress.val(),
                "agent_contact1": agtContact1.val(),
                "agent_contact2": agtContact2.val(),
                "agent_fax"     : agtFax.val(),
                "agent_email"   : agtEmail.val(),
                "agent_note"    : agtNote.val(),
            };

            const agtURL        = "marketings/agent/simpan_data/"+jenis;
            const agtData       = agtSendData;
            const agtMsg        = Swal.fire({ title : "Data Sedang Diproses" }); Swal.showLoading();

            doTransaction(agtURL, "POST", agtData, agtMsg, true)
                .then((success)     => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text,
                    }).then((results)   => {
                        if(results.isConfirmed) {
                            closeModal('modal_data_agent');
                        }
                    })
                })
                .catch((err)        => {
                    Swal.fire({
                        icon    : err.responseJSON.alert.icon,
                        title   : err.responseJSON.alert.message.title,
                        text    : err.responseJSON.alert.message.text,
                    })
                })
        }
    }
}

function uppercase(idForm, value)
{
    $("#"+idForm).val(value.toUpperCase());
}

function removeInvalid(idForm)
{
    $("#"+idForm).removeClass('is-invalid');
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