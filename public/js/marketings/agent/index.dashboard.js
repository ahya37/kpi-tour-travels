var dataAgent       = [];
var dataAgentSelect = [];
var dataAgentActivity   = [];
var dataTourCode    = [];
var today           = moment().format('YYYY-MM-DD');
$(document).ready(()    => {
    showTable('table_list_agent', []);
    clearUrl();
    // GET DATA FOR DASHBOARD
    const agentURL  = "marketings/agent/tarik_data_agent_local";
    const agentType = "GET";

    // GET DATA TOURCODE
    const tourCode_url  = "marketings/agent/ambil_data_tour_code/"+moment(today).format('YYYY');
    const tourCode_type = "GET";
    const tourCode_data = [];
    const tourCode_msg  = "";

    const agentActivityURL  = "marketings/agent/ambil_data_act_agent/semua";
    const agentActivityType = "GET";
    const agentActivityData = [];
    const agentActivityMsg  = "";
    
    const getData   = [
        doTransaction(agentURL, agentType, [], "", true),
        doTransaction(tourCode_url, tourCode_type, tourCode_data, tourCode_msg, true),
        doTransaction(agentActivityURL, agentActivityType, agentActivityData, agentActivityMsg, true)
    ];

    Promise.allSettled(getData)
        .then((success)     => {
            // AGENT
            if(dataAgent.length == 0) {
                success[0].value.data.length > 0 ? dataAgent.push(success[0].value.data) : [];
            }
            // SHOW TABLE AGENT
            showTable('table_list_agent', dataAgent.length == 0 ? [] : dataAgent[0]);
            // SHOW TEXT TOTAL AGENT
            $("#dashboard_total_agent").html(dataAgent.length > 0 ? dataAgent[0].length : 0);

            // GET TOUR CODE
            if(dataTourCode.length == 0) {
                dataTourCode.push(success[1].value.data);
            }

            // GET AGENT ACTIVITY
            const agentActivityData     = success[2].value.data;
            dataAgentActivity.push(agentActivityData);
            $("#dashboard_total_aktivitas").html(dataAgentActivity.length > 0 ? dataAgentActivity[0].length : 0);

            // REWARD AGENT
            $("#dashboard_total_reward_agent").html(0);
        })
        .catch((err)        => {
            $("#agent_text").html("<label class='font-weight-bold no-margins'>0</label>");
            $("#dashboard_total_agent").html(0);
            $("#dashboard_total_aktivitas").html(0);
            $("#dashboard_total_reward_agent").html(0);
            console.error(err);
        })
})

function showModal(idModal, data, action)
{
    if(idModal == 'modal_tarik_data_agent') {
        let agentURL    = "marketings/agent/tarik_data_agent";
        Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();
        doTransaction(agentURL, 'GET', [], '', true)
            .then((success)     => {
                Swal.fire({
                    icon    : 'success',
                    title   : 'Berhasil',
                    text    : success.message,
                })
            })
            .catch((err)        => {
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : err.responseJSON.message,
                })
            })
    } else if(idModal == 'modal_simulasi') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false, });

        showTable('table_simulasi');
        addColumnTable('table_simulasi', 1, []);
    } else if(idModal == 'modal_data_agent') {
        if(action == 'add') {            
            $("#"+idModal).modal({backdrop: 'static', keyboard: false});
            $("#modal_data_agent_title").html('Tambah Data Agent Baru');
            
            $("#"+idModal).on('shown.bs.modal', () => {
                $("#agt_mkk_code").focus();
                $("#agt_mkk_code").prop('readonly', false);
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
                    $("#agt_mkk_code").prop('readonly', true);
                    $("#agt_mkk_code").val(agentGetData.agt_mkk_id);
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
    } else if(idModal == 'modal_pengaturan_agen') {
        if(dataAgent[0].length > 0) {
            $("#"+idModal).modal({ backdrop : 'static', keyboard: false });
            for(const agtItem of dataAgent[0])
            {
                dataAgentSelect.push({
                    "agent_id"  : agtItem['agent_id'],
                    "agent_name": agtItem['agent_name'],
                })
            }

            showSelect('sl_agt_id', dataAgentSelect, '', '');
        } else {
            // GET DATA
            const agtURL    = "marketings/agent/tarik_data_agent_local";
            const agtMsg    = Swal.fire({ title : "Data Sedang Dimuat.." }); Swal.showLoading();

            doTransaction(agtURL, "GET", [], agtMsg, true)
                .then((success)     => {
                    for(const agtItem of success.data)
                    {
                        dataAgentSelect.push({
                            "agent_id"  : agtItem['agent_id'],
                            "agent_name": agtItem['agent_name'],
                        })
                    }

                    showSelect('sl_agt_id', dataAgentSelect, '', '');
                    Swal.close();
                    $("#"+idModal).modal({ backdrop : 'static', keyboard: false });
                })
                .catch((err)        => {
                    console.log(err);
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : 'Tidak ada data agent yang bisa dimuat'
                    })
                })
        }
        showTable('table_pengaturan_agen', []);
    } else if(idModal == 'modal_pengaturan_agent_jemaah') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

        $("#modal_pengaturan_agent_jemaah_tour_code").html(data.split("&")[0]);

        showTable('table_list_pengaturan_agent_jemaah', []);
    }
}

function closeModal(idModal)
{
    if(idModal == 'modal_simulasi') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            for(let i = 0; i < 3; i++) {
                let seq     = i + 1;
                $("#point_"+seq).html(0);
                $("#bonus_"+seq).html(0);
                $("#total_"+seq).html(0);
                $("#sisa_"+seq).html(0);
            }

            $("#total_Jan").html(0);
            $("#total_Feb").html(0);
            $("#total_Mar").html(0);
            $("#total_Apr").html(0);
            $("#total_Mei").html(0);
            $("#total_Jun").html(0);
            $("#total_Jul").html(0);
            $("#total_Agt").html(0);
            $("#total_Sep").html(0);
            $("#total_Okt").html(0);
            $("#total_Nov").html(0);
            $("#total_Des").html(0);

            $("#total_reward").html(0);
        })
        clearUrl();
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
            $("#agt_mkk_code").val("");
            $("#agt_mkk_code").prop('readonly', false);
        })
    } else if(idModal == 'modal_pengaturan_agen') {
        $("#"+idModal).modal('hide');
        clearUrl();
        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#btn_tambah_data_modal_pengaturan_agen").prop('disabled', true);
        })
    } else if(idModal == 'modal_pengaturan_agent_jemaah') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            $("#modal_pengaturan_agent_jemaah_tour_code").html("");
        })
    }
}

function showSelect(idSelect, data, value, seq)
{
    $("#"+idSelect+seq).select2({
        theme   : 'bootstrap4',
    })
    if(idSelect == 'sl_agt_id') {
        let html    = "<option selected disabled>List Agen</option>";
        if(data.length > 0) {
            for(const item of data) {
                html += `<option value='${item.agent_id}'>${item.agent_name}</option>`;
            }
        }

        if(value != '') {
            $("#"+idSelect).val(value);
        }

        $("#"+idSelect).html(html);
    } else if(idSelect == 'agt_tourCode') {
        let html    = "<option selected disabled>Pilih Tour Code</option>";

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value=${item['tour_code']}>${item['tour_code']}</option>`
            })
        }

        $("#"+idSelect+seq).html(html);

        if(value != '') {
            $("#"+idSelect+seq).val(value);
        }
    } else if(idSelect == 'agt_jenis') {
        let html    = "<option selected disabled>Jenis</option>";

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                html    += `<option value=${item['id']}>${item['name']}</option>`;
            })
        }

        $("#"+idSelect+seq).html(html);

        if(value != '') {
            $("#"+idSelect+seq).val(value);
        }
    } else if(idSelect == 'namaJemaah') {
        $("#"+idSelect+""+seq).select2({
            theme   : 'bootstrap4',
            ajax : {
                url     : base_url + "/marketings/agent/ambil_member_umhaj",
                delay   : 250,
                data    : (params) => {
                    return {
                        search  : params.term || '',
                    }
                },
                processResults  : (data)    => {
                    return {
                        results     : data.data.map((item)  => {
                            return {
                                id      : item.member_id,
                                text    : item.member_name 
                            }
                        })
                    }
                },
                error           : (err) => {
                    console.log(err);
                }
            },
            minimumInputLength  : 3, 
            language    : {
                errorLoading    : () => {
                    return 'Tidak Ditemukan Hasil Pencarian';
                },
                searching       : () => {
                    return 'Data Sedang Dicari';
                },
                inputTooShort   : (args)    => {
                    let remainingChars  = args.minimum - args.input.length;
                    return `Ketik ${remainingChars} Kata Lagi Untuk Mencari Data`;
                }
            }
        })
    }
}

function showSelectDetail(idSelect, data)
{
    if(idSelect == 'sl_agt_id')
    {
        $("#btn_tambah_data_modal_pengaturan_agen").prop('disabled', false);
        // HARUSNYA GET DATA
        const actAgentUrl   = "marketings/agent/ambil_data_act_agent/"+data;
        const actAgentType  = "GET";
        const actAgentMsg   = Swal.fire({ title : "Data Sedang Dimuat..", allowOutsideClick: false }); Swal.showLoading();
        
        doTransaction(actAgentUrl, actAgentType, [], actAgentMsg, true)
            .then((success)     => {
                Swal.close();
                showTable('table_pengaturan_agen', success.data);
            })
            .catch((err)        => {
                Swal.close();
                showTable('table_pengaturan_agen', []);
            })
        
    }
}

function showTable(idTable, data)
{
    $("#"+idTable).DataTable().clear().destroy();
    
    if(idTable == 'table_list_agent')
    {
        $("#"+idTable).DataTable({
            language    : {
                emptyTable  : "<i class='fa fa-spinner fa-spin'></i> Data Sedang Dimuat..",
                zeroRecords : "Data Yang Dicari Tidak Ditemukan"
            },
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0, 6], "className" : "text-center align-middle", "width" : "5%" },
                { "targets" : [1], "className" : "align-middle", "width" : "10%" },
                { "targets" : [2], "className" : "text-center align-middle", "width" : "8%"},
                { "targets" : [3, 4], "className" : "text-left align-middle", "width" : "25%" },
            ],
            lengthMenu  : [
                [5, 10, 25, 50, -1],
                [5, 10, 25, 50, 'All'],
            ],
            pageLength  : 5,
        })

        if(data.length > 0) {
            let seq  = 1;
            for(const item of data)
            {
                let agentID         = item['agent_id'];
                let agentUniqueID   = item['agent_id_2'];
                let agentMKKID      = item['agent_mkk_kode'];
                let agentName       = item['agent_name'];
                let agentPIC        = item['agent_pic'];
                let agentContact1   = item['agent_contact1'];
                let agentContact2   = item['agent_contact2'];
                let agentContact    = agentContact1 != '-' || agentContact1 != '' ? agentContact1 + " / " + agentContact2 : agentContact1;
                let buttonAct       = `<button type="button" class="btn btn-sm btn-primary" value="${agentID}" onclick="showModal('modal_data_agent', this.value, 'edit')" title="Ubah Data"><i class='fa fa-edit'></i></button>`;

                $("#"+idTable).DataTable().row.add([
                    `<label class="no-margins font-weight-normal">${seq++}</label>`,
                    `<label class="no-margins font-weight-normal">${agentMKKID}</label>`,
                    `<label class="no-margins font-weight-normal">${agentUniqueID}</label>`,
                    `<label class="no-margins font-weight-normal">${agentName}</label>`,
                    `<label class="no-margins font-weight-normal">${agentPIC}</label>`,
                    `<label class="no-margins font-weight-normal">${agentContact}</label>`,
                    buttonAct,
                ]).draw(false);
            }
        } else {
            // $("#table_list_agent .dataTables_empty").html('Tidak Ada Data Yang Bisa Ditampilkan');
        }
        
        $("#"+idTable+"_wrapper").css('padding-bottom', '20px');
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
    } else if(idTable == 'table_pengaturan_agen') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "Pilih Agent Untuk Menampilkan Data",
                "zeroRecords"   : "Data Yang Dicari Tidak Ditemukan",
            },
            searching   : false,
            bInfo       : false,
            paging      : false,
            pageLength  : -1,
            autoWidth   : true,
            ordering    : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center", "width" : "8%" },
                { "targets" : [1], "width" : "15%" },
                { "targets" : [3], "width" : "13%" },
                { "targets" : [4], "width" : "14%" },
                { "targets" : [5], "className" : "align-middle", "width" : "15%" },
            ]
        })

        const agent     = $("#sl_agt_id").val();
        if(agent !== null) {
            if(data.length > 0) {
                for(let i = 0; i < data.length; i++)
                {
                    addColumnTable(idTable, i, data[i]);
                }
                addColumnTable(idTable, data.length, '');
            } else {
                addColumnTable(idTable, 0, '');
            }
        }

        $("#"+idTable+"_wrapper").css("padding-bottom", "0px");
    } else if(idTable == 'table_list_pengaturan_agent_jemaah') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language    : {
                "emptyTable"    : "Tidak Ada Data Yang Bisa Dimuat.."
            },
            pageLength  : -1,
            ordering    : false,
            paging      : false,
            searching   : false,
            bInfo       : false,
            autoWidth   : false,
            columnDefs  : [
                { "targets" : [0], "className" : "text-center align-middle", "width" : "10%", },
                { "targets" : [1], "className" : "text-left align-middle" },
                { "targets" : [2], "className" : "text-center align-middle", "width" : "8%" },
            ],
        });

        $("#table_list_pengaturan_agent_jemaah_wrapper").css('padding-bottom', '0px');
        $("#table_list_pengaturan_agent_jemaah_wrapper").css('padding-top', '12px');

        addColumnTable(idTable, 1, []);
    }
}

function addColumnTable(idTable, seq, data)
{
    if(idTable == 'table_simulasi') {
        let isBtnDeleteDisabled = seq > 1 ? "" : "disabled";
        let isBtnDeleteDisabledCursor   = seq > 1 ? "pointer" : "no-drop";
        let monthName           = moment.monthsShort();
        const btnDelete     = "<button class='btn btn-sm btn-danger' "+isBtnDeleteDisabled+" onclick='deleteColumnTable(`table_simulasi`, "+seq+")' style='cursor: "+isBtnDeleteDisabledCursor+"'><i class='fa fa-trash'></i></button>";
        const inputTahun    = "<input type='text' class='form-control form-control-sm text-center' id='tahun"+seq+"'value='2025' readonly>";
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
    } else if(idTable == 'table_pengaturan_agen') {
        let ke              = parseInt(seq) + 1;
        let inputNo         = "<input type='text' class='form-control text-center' id='agt_no"+ke+"' disabled placeholder='No' style='height: 38px;'>";
        let inputTanggal    = "<input type='text' class='form-control' id='agt_tgl"+ke+"' placeholder='DD/MM/YYY' readonly style='height: 38px;'>";
        let inputTourCode   = "<select class='form-control' style='width: 100%;' id='agt_tourCode"+ke+"'></select>";
        let inputBanyaknya  = "<input type='number' inputmode='numeric' class='form-control' id='agt_banyaknya"+ke+"' placeholder='Banyaknya' min='0' max='999' step='1' style='height: 38px;'>";
        let inputJenis      = "<select class='form-control' style='width: 100%;' id='agt_jenis"+ke+"'></select>";
        let inputAksi       = `<button class="btn btn-sm btn-primary" title="Simpan Data" value='add' onclick="doSimpanData('${idTable}', this.value, '${ke}')" id="btn_act_agen${ke}"><i class="fa fa-check"></i></button>`
        let inputDelete     = `<button class="btn btn-sm btn-danger" title="Hapus Baris" value="${ke}" onclick="deleteColumnTable('${idTable}', '${ke}')" id="btn_delete_agen${ke}"><i class="fa fa-trash"></i></button>`;
        let inputPaid       = `<button class="btn btn-sm btn-primary d-none" title="Konfirmasi Pembayaran" value="unpaid" onclick="doSimpanData('${idTable}', this.value, '${ke}')" id="btn_act_paid${ke}"><i class="fa fa-dollar-sign"></i></button>`;
        let inputPerson     = `<button class="btn btn-sm btn-primary d-none" type="button" id="btn_act_person${ke}" title="Masukkan Nama Jemaah" onclick="showModal('modal_pengaturan_agent_jemaah', this.value, '')"><i class="fa fa-user"></i></button>`
        $("#"+idTable).DataTable().row.add([
            inputNo,
            inputTanggal,
            inputTourCode,
            inputBanyaknya,
            inputJenis,
            inputAksi+" "+inputDelete+" "+inputPaid+" "+inputPerson
        ]).draw(false);

        $("#agt_no"+ke).val(ke);
        $("#agt_banyaknya"+ke).val(0);

        $("#agt_tgl"+ke).daterangepicker({
            drops       : 'up',
            minDate     : moment(today, 'YYYY-MM-DD').subtract(1, 'year'),
            maxDate     : moment(today, 'YYYY-MM-DD').add(1, 'year'),
            autoApply   : true,
            format      : 'DD/MM/YYYY',
            setStartDate    : moment(today, 'YYYY-MM-DD'),
            singleDatePicker    : true,
            locale  : {
                cancelLabel : 'Batal',
                applyLabel  : 'Simpan',
            },
        });

        const dataJenis     = [
            { "id" : "act", "name" : "Aktif" },
            { "id" : "ref", "name" : "Referral" },
            { "id" : "psv", "name" : "Passif" },
        ];

        $("#agt_banyaknya"+(ke)).focus();

        if(data.length != '')
        {
            let actAgent_date       = moment(data['agt_act_date'], 'YYYY-MM-DD').format('DD/MM/YYYY');
            let actAgent_qty        = parseInt(data['agt_act_qty']);
            let actAgent_tourCode   = data['agt_act_tour_code'];
            let actAgent_type       = data['agt_act_status'];
            let actAgent_paidStatus = data['agt_act_is_paid'];

            $("#btn_act_agen"+ke).val('edit');
            $("#btn_act_agen"+ke).html("<i class='fa fa-edit'></i>");
            $("#btn_act_agen"+ke).prop('title', 'Ubah Data');
            
            // DISABLED DATE
            $("#agt_tourCode"+ke).prop('disabled', true);
            $("#agt_tgl"+ke).prop('disabled', true);
            
            // FILL FORM
            $("#agt_tgl"+ke).data('daterangepicker').setStartDate(actAgent_date);
            $("#agt_tgl"+ke).data('daterangepicker').setEndDate(actAgent_date);

            $("#agt_banyaknya"+ke).val(actAgent_qty);

            showSelect('agt_tourCode', dataTourCode[0], actAgent_tourCode, ke);
            showSelect('agt_jenis', dataJenis, actAgent_type, ke);

            // SHOW BUTTON
            $("#btn_act_paid"+ke).removeClass('d-none');
            $("#btn_act_person"+ke).removeClass('d-none');
            $("#btn_act_person"+ke).val(actAgent_tourCode+"&"+actAgent_date);

            if(actAgent_paidStatus == "1") {
                $("#btn_act_paid"+ke).val('paid');
                $("#btn_act_paid"+ke).prop('disabled', true);
                $("#btn_act_paid"+ke).removeClass('btn-primary');
                $("#btn_act_paid"+ke).addClass('btn-secondary');
                $("#btn_act_paid"+ke).prop('title', 'Sudah Dibayarkan');
            }
        } else {
            $("#btn_act_paid"+ke).addClass('d-none');
            showSelect('agt_tourCode', dataTourCode[0], '', ke);
            showSelect('agt_jenis', dataJenis, '', ke);
        }

        if(ke > 1) {
            $("#btn_delete_agen"+(ke - 1)).prop('disabled', true);
        }

        $("#btn_tambah_data_modal_pengaturan_agen").val(ke);
    } else if(idTable == 'table_list_pengaturan_agent_jemaah') {
        let ke  = seq;
        let inputSeq    = `<input type="text" class="form-control text-center" disabled id="seqJemaah${ke}" style="height: 38px;">`;
        let inputJemaah = `<select class="form-control" id="namaJemaah${ke}" style="width: 100%;" data-placeholder='Nama Jemaah'></select>`;
        let buttonDelete= `<button type="button" class="btn btn-sm btn-danger" title="Hapus Baris"><i class="fa fa-trash"></i></button>`;

        $("#"+idTable).DataTable().row.add([
            inputSeq,
            inputJemaah,
            buttonDelete
        ]).draw(false);

        // FILL FORM
        $(`#seqJemaah${ke}`).val(parseInt(ke));
        showSelect('namaJemaah', [], '', seq);
        // GET NEXT SEQ FOR BUTTON
        let nextKe  = parseInt(seq) + 1;
        $("#btn_tambah_baris_pengaturan_agent_jemaah").val(nextKe);
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
    } else if(idTable == 'table_pengaturan_agen') {
        let currentSeq  = parseInt($("#btn_tambah_data_modal_pengaturan_agen").val());
        let selectedSeq = parseInt(seq);
        let diffSeq     = currentSeq - selectedSeq;

        if(selectedSeq == 1) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Baris Pertama Tidak Bisa Dihapus',
            })
        } else if(diffSeq > 1) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Hanya Bisa Menghapus Data Terakhir',
            })
        } else {
            // CHECK BUTTON VALUE
            if($("#btn_act_agen"+seq).val() == 'edit')
            {
                // HAPUS JUGA DI DATABASE
                let actAgent_url    = "marketings/agent/simpan_data/type_agent/delete";
                let actAgent_data   = {
                    "agt_id"                : $("#sl_agt_id").val(),
                    "agt_detail_tourCode"   : $("#agt_tourCode"+seq).val(),
                    "agt_detail_date"       : $("#agt_tgl"+seq).val(),
                    "agt_detail_qty"        : $("#agt_banyaknya"+seq).val(),
                    "agt_detail_type"       : $("#agt_jenis"+seq).val(),
                };
                let actAgent_type   = "POST";
                let actAgent_msg    = Swal.fire({ title : "Data Sedang Diproses" }); Swal.showLoading();

                doTransaction(actAgent_url, actAgent_type, actAgent_data, actAgent_msg, true)
                    .then((success)     => {
                        Swal.close();
                        $("#"+idTable).DataTable().row(selectedSeq - 1).remove().draw(false);
                        $("#btn_tambah_data_modal_pengaturan_agen").val(currentSeq - 1);
                        $("#btn_delete_agen"+(currentSeq-1)).prop('disabled', false);
                        $("#agt_banyaknya"+(currentSeq-1)).focus();
                    })
                    .catch((err)        => {
                        Swal.fire({
                            icon    : 'error',
                            title   : 'Terjadi Kesalahan',
                            text    : 'Tidak Bisa Menghapus Baris'
                        })
                        console.log(err)
                    })
            } else {
                $("#"+idTable).DataTable().row(selectedSeq - 1).remove().draw(false);
                $("#btn_tambah_data_modal_pengaturan_agen").val(currentSeq - 1);
                $("#btn_delete_agen"+(currentSeq-1)).prop('disabled', false);
                $("#agt_banyaknya"+(currentSeq-1)).focus();
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

        let fee         = 1000000;

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

        let hitungPoint     = jan + feb + mar + apr + mei + jun + jul + agt + sep + okt + nov + des;
        let hitungBonus     = q1 + q2 + q3 + q4;
        let hitungTotal     = parseInt(hitungPoint) + parseInt(hitungBonus);

        $("#bonus_"+seq).html(hitungBonus);
        $("#point_"+seq).html(hitungPoint);
        $("#total_"+seq).html(hitungTotal);

        let total_1         = parseInt($("#total_1").text());
        let total_2         = parseInt($("#total_2").text());
        let total_3         = parseInt($("#total_3").text());
        let grandTotal      = 0;

        let sisa_1          = 0;
        let sisa_2          = 0;
        let sisa_3          = 0;

        let bonus_1         = 0;
        let bonus_2         = 0;
        let bonus_3         = 0;
        let totalBonus      = 0;

        let rupiah          = 0;

        // UNTUK SEQ 1
        if(total_1 >= 50) {
            bonus_1     = Math.floor(total_1 / 50);
            sisa_1      = total_1 - (Math.floor(total_1 / 50) * 50);
        } else {
            bonus_1     = 0;
            sisa_1      = total_1
        }
        $("#sisa_1").html(sisa_1);

        // UNTUK SEQ 2
        if(total_2 >= 50) {
            bonus_2     = Math.floor(total_2 / 50);
            sisa_2      = total_2 - (Math.floor(total_2 / 50) * 50);
        } else {
            if(sisa_1 + total_2 >= 80) {
                bonus_2     = Math.floor((sisa_1 + total_2) / 80);
                sisa_2      = (sisa_1 + total_2) - (Math.floor((sisa_1 + total_2) / 80) * 80);
            } else {
                bonus_2     = 0;
                sisa_2      = total_2
            }
        }
        $("#sisa_2").html(sisa_2);

        if(total_3 >= 50) {
            bonus_3     = Math.floor(total_3 / 50);
            sisa_3      = total_3 - (Math.floor(total_3 / 50) * 50);
        } else {
            if(sisa_2 + total_3 >= 110) {
                bonus_3     = Math.floor((sisa_2 + total_3) / 110);
                sisa_3      = (sisa_2 + total_3) - (Math.floor((sisa_1 + total_2) / 110) * 110);
            } else {
                bonus_3     = 0;
                sisa_3      = total_3;
            }
        }
        $("#sisa_3").html(sisa_3);

        // HITUNG BONUS / REWARD
        totalBonus  = bonus_1 + bonus_2 + bonus_3;
        $("#total_reward").html(totalBonus);
        // HITUNG FEE
        let pendapatan_1    = new Intl.NumberFormat("id-ID", { style: "currency", currency : "IDR" }).format(total_1 * 1000000);
        let pendapatan_2    = new Intl.NumberFormat("id-ID", { style: "currency", currency : "IDR" }).format(total_2 * 1000000);
        let pendapatan_3    = new Intl.NumberFormat("id-ID", { style: "currency", currency : "IDR" }).format(total_3 * 1000000);

        $("#total_pendapatan_tahun_pertama").html(pendapatan_1);
        $("#total_pendapatan_tahun_kedua").html(pendapatan_2);
        $("#total_pendapatan_tahun_ketiga").html(pendapatan_3);
    }
}

function doSimpanData(idForm, jenis, data)
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
        const agtMkkCode    = $("#agt_mkk_code");

        if(agtMkkCode.val() == "") {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Kode MKK Harus Diisi',
                didClose    : () => {
                    agtMkkCode.focus();
                }
            })
        } else if(agtName.val() == "") {
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
                    agtPIC.addClass('is-invalid');
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
                "agent_mkk_code": agtMkkCode.val(),
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
                            // GET DATA AGENT
                            dataAgent    = [];
                            showTable('table_list_agent', dataAgent);
                            const agentURL  = "marketings/agent/tarik_data_agent_local";
                            const agentType = "GET";

                            doTransaction(agentURL, agentType, [], "", true)
                                .then((success)     => {
                                    dataAgent.push(success.data);
                                    showTable('table_list_agent', dataAgent[0]);
                                })
                                .catch((err)        => {
                                    console.log(err);
                                    showTable('table_list_agent', dataAgent[0]);
                                })
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
    } else if(idForm == 'table_pengaturan_agen') {
        let seq                 = data;
        let agt_id              = $("#sl_agt_id");
        let agt_detail_date     = $("#agt_tgl"+seq);
        let agt_detail_tourCode = $("#agt_tourCode"+seq);
        let agt_detail_qty      = $("#agt_banyaknya"+seq);
        let agt_detail_type     = $("#agt_jenis"+seq);

        if(agt_detail_tourCode.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Pilih Tour Code Terlebih Dahulu',
                didClose    : () => {
                    agt_detail_tourCode.select2('open');
                }
            })
        } else if(agt_detail_type.val() == null) {
            Swal.fire({
                icon    : 'error',
                title   : 'Terjadi Kesalahan',
                text    : 'Jenis Agen Harus Dipilih',
                didClose    : () => {
                    agt_detail_type.select2('open')
                }
            })
        } else {
            const agt_option_data   = {
                "agt_id"                : agt_id.val(),
                "agt_detail_date"       : moment(agt_detail_date.val(), 'DD/MM/YYYY').format('YYYY-MM-DD'),
                "agt_detail_tourCode"   : agt_detail_tourCode.val(),
                "agt_detail_qty"        : agt_detail_qty.val(),
                "agt_detail_type"       : agt_detail_type.val(),
            };

            const agt_type          = "POST";
            const agt_url           = "marketings/agent/simpan_data/type_agent/"+jenis;
            const agt_msg           = Swal.fire({ title : 'Data Sedang Diproses' }); Swal.showLoading();

            doTransaction(agt_url, agt_type, agt_option_data, agt_msg, true)
                .then((success)     => {
                    Swal.fire({
                        icon    : success.alert.icon,
                        title   : success.alert.message.title,
                        text    : success.alert.message.text,
                    }).then((res)   => {
                        if(res.isConfirmed) {
                            // CARI DATA
                            showSelectDetail('sl_agt_id', agt_id.val());
                        }
                    });
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

function clearUrl()
{
    var url     = window.location.href;
    var cleanUrl= url.split('#')[0];
    window.history.replaceState({}, document.title, cleanUrl);
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