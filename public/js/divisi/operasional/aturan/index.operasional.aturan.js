$(document).ready(function(){
    console.log('test');

    showTable('table_list_aturan', '%');
});

var site_url    = window.location.pathname;

function showTable(idTable, valueCari)
{
    if(idTable == 'table_list_aturan') {
        $("#"+idTable).DataTable().clear().destroy();
        $("#"+idTable).DataTable({
            language  : {
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang dimuat..",
                "emptyTable"    : "Tidak ada data yang bisa ditampilkan...",
                "zeroRecords"   : "Tidak ada data yang bisa ditampilkan..."
            },
            processing  : true,
            serverSide  : false,
            ajax    : {
                type    : "GET",
                dataType: "json",
                url     : site_url + "/listRules",
            },
            // scrollY     : "30vh",
            pageLength  : -1,
            paging      : false,
            ordering    : false,
            columnDefs  : [
                { targets: [0, 2, 3, 5], className: "text-center"},
                { targets: [0, 5], width: "7%" },
            ],
        })
    }
}

function showModal(idModal, jenisTrans, valueCari)
{
    $("#btnSimpan").val(jenisTrans);
    if(jenisTrans == 'add') {
        $("#"+idModal).modal({backdrop: 'static', keyboard: false});
        $("#"+idModal).modal('show');
        $("#"+idModal).on('shown.bs.modal', function(){
            $("#ruleDescription").focus();
        });
        
        showSelect('rulePktID', '%', '', true);
        showSelect('rulePicID', '%', '', true);
        showSelect('rulePlusMin', '%','', true);
        showSelect('ruleCondition', '', '', true);
    } else if(jenisTrans == 'edit') {
        var url     = site_url + "/getRulesDetail/" + valueCari; 
        var type    = "GET";
        var isAsync = true;
        var customMessage   = Swal.fire({title:'Data Sedang Dimuat'});Swal.showLoading();
        
        doTrans(url, type, '', '', isAsync)
            .then((xhr)=>{
                console.log({xhr});
                Swal.close();
                $("#"+idModal).modal({backdrop: 'static', keyboard: false});
                $("#"+idModal).modal('show');

                $("#rulesID").val(valueCari);
                $("#ruleDescription").val(xhr.data[0].rul_title);
                $("#ruleDurationDay").val(parseInt(xhr.data[0].rul_duration_day));
                $("#rulesSLADay").val(parseInt(xhr.data[0].rul_length_day));
                $("#rulesBobot").val(parseInt(xhr.data[0].rul_bobot));

            showSelect('rulePktID', '%', xhr.data[0].rul_pkt, false);
                showSelect('rulePicID', '%', xhr.data[0].rul_pic, false);
                showSelect('rulePlusMin', '%', xhr.data[0].rul_length_day_condition, false);
                showSelect('ruleCondition', xhr.data[0].rul_length_day_condition, xhr.data[0].rul_condition, true);

                $("#"+idModal).on('shown.bs.modal', function(){
                    $("#ruleDescription").focus();
                })
                
            })
            .catch((xhr)=>{
                Swal.fire({
                    icon    : 'error',
                    title   : 'Terjadi Kesalahan',
                    text    : 'Tidak ada data yang bisa dimuat'
                });
                console.log(xhr)
            })
    }
}

function showSelect(idSelect, valueCari, valueSelect, isAsync)
{
    $("#"+idSelect).select2({
        theme   : 'bootstrap4',
    });

    if(idSelect == 'rulePktID') {
        var html    = "<option selected disabled>Pilih Program Kerja Tahunan</option>";
        // GET DATA
        var url     = "/divisi/master/getDataProkerTahunan";
        var type    = "GET";
        var data    = valueCari;

        doTrans(url, type, data, '', isAsync)
            .then((xhr)=>{
                $.each(xhr.data, function(i,item){
                    html    += "<option value='" + item['pkt_id'] + "'>" + (i + 1)+". "+item['pktd_title']+"</option>";
                });
                $("#"+idSelect).html(html);

                if(valueSelect != '') {
                    $("#"+idSelect).val(valueSelect);
                }
            })
            .catch((xhr)=>{
                console.log(xhr);
                $("#"+idSelect).html(html);
            });

        $("#"+idSelect).html(html);
    } else if(idSelect == 'rulePicID') {
        var html    = "<option selected disabled>Pilih PIC / Penanggung jawab</option>";

        var url     = "/divisi/master/getDataSubDivision";
        var type    = "GET";
        var data    = "%";

        doTrans(url, type, data, '', isAsync)
            .then((xhr)=>{
                $.each(xhr.data, function(i,item){
                    html    += "<option value='" + item['sub_division_id'] + "'>" + item['sub_division_name'] + "</option>";
                });
                $("#"+idSelect).html(html);
                
                if(valueSelect != '') {
                    $("#"+idSelect).val(valueSelect);
                }
            })
            .catch((xhr)=>{
                console.log(xhr);
                $("#"+idSelect).html(html);
            });

        $("#"+idSelect).html(html);
    } else if(idSelect == 'rulePlusMin') {
        var html    = [
            "<option value='-'>-</option>",
            "<option value='+'>+</option>"
        ];
        $("#"+idSelect).html(html);

        if(valueSelect != '') {
            $("#"+idSelect).val(valueSelect).trigger('change');
        }
    } else if(idSelect == 'ruleCondition') {
        var html    = "<option selected disabled>Pilih Kondisi</option>";

        if(valueCari != '') {
            if(valueCari == '+') {
                html    += "<option value='af-dpt'>Setelah Keberangkatan</option>";
                html    += "<option value='af-arv'>Setelah Kepulangan</option>";
            } else if(valueCari == '-') {
                html    += "<option value='bf-dpt'>Sebelum Keberangkatan</option>";
                html    += "<option value='bf-arv'>Sebelum Kepulangan</option>";
            }
        }
    
        $("#"+idSelect).html(html);

        if(valueSelect != '') {
            $("#"+idSelect).val(valueSelect).trigger('change');
        }
    }
}

function closeModal(idModal, jenisTrans)
{
    $("#"+idModal).modal('hide');
    $("#"+idModal).on('hidden.bs.modal', function(){
        $("#ruleDescription").val(null);
        $("#ruleDurationDay").val(0);
    })
}

function simpanData(tipe)
{
    // DATA
    var dataTitle   = $("#ruleDescription");
    var dataPktID   = $("#rulePktID");
    var dataPIC     = $("#rulePicID");
    var dataDuration= $("#ruleDurationDay");
    var dataSLA     = $("#rulesSLADay");
    var dataPlusMin = $("#rulePlusMin");
    var dataID      = $("#rulesID");
    var dataCondition   = $("#ruleCondition");
    var dataBobot   = $("#rulesBobot");

    if(dataTitle.val() == '') {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Uraian Tidak Boleh Kosong',
        }).then((results)=>{
            if(results.isConfirmed) {
                dataTitle.focus();
            }
        })
    } else if(dataPktID.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Pilih Program Kerja Tahunan',
        }).then((results)=>{
            if(results.isConfirmed) {
                dataPktID.select2('open');
            }
        })
    } else if(dataPIC.val() == null) {
        Swal.fire({
            icon    : 'error',
            title   : 'Terjadi Kesalahan',
            text    : 'Pilih PIC',
        }).then((results)=>{
            if(results.isConfirmed) {
                dataPIC.select2('open');
            }
        })
    } else {
        var data    = {
            "dataID"        : dataID.val(), 
            "dataTitle"     : dataTitle.val(),
            "dataPktID"     : dataPktID.val(),
            "dataPIC"       : dataPIC.val(),
            "dataDuration"  : dataDuration.val(),
            "dataSLA"       : dataPlusMin.val()+""+dataSLA.val(),
            "dataCondition" : dataCondition.val(),
            "dataBobot"     : dataBobot.val(),
        };
        var url     = site_url + "/simpanDataRules/"+tipe;
        var type    = "POST";
        var customMessage   = Swal.fire({title:'Data Sedang Diproses'});Swal.showLoading();
        doTrans(url, type, data, customMessage, true)
            .then((xhr)=>{
                Swal.fire({
                    icon    : xhr.alert.icon,
                    title   : xhr.alert.message.title,
                    text    : xhr.alert.message.text,
                }).then((results)=>{
                    closeModal('modalForm', 'add');
                    if(results.isConfirmed) {
                        if(tipe == 'add') {
                            Swal.fire({
                                icon                : 'question',
                                title               : 'Tambah data lagi?',
                                showConfirmButton   : true,
                                showCancelButton    : true,
                                cancelButtonText    : 'Tidak',
                                confirmButtonText   : 'Ya, Tambah lagi',
                            }).then((results)=>{
                                if(results.isConfirmed) {
                                    showModal('modalForm', 'add');
                                } else {
                                    closeModal('modalForm', 'add');
                                    showTable('table_list_aturan', '%');
                                }
                            })
                        } else {
                            showTable('table_list_aturan', '%');
                        }
                    }
                })
            })
            .catch((xhr)=>{
                console.log(xhr);
            });
    }
}

function doTrans(url, type, data, customMessage, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            cache   : false,
            type    : type,
            data    : {
                _token  : CSRF_TOKEN,
                sendData: data,
            },
            url     : url,
            beforeSend   : function() {
                customMessage;
            },
            async   : isAsync,
            success : function(xhr) {
                resolve(xhr);
            },
            error   : function(xhr) {
                reject(xhr);
            }
        })
    });
}