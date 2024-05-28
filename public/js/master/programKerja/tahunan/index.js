$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': CSRF_TOKEN
    }
});

$(document).ready(function(){
    show_table('tableProgramKerjaTahunan');
});

function show_table(id_table)
{
    if(id_table == 'tableProgramKerjaTahunan') {
        var groupDivision     = "%";
        $("#tableProgramKerjaTahunan").DataTable().clear().destroy();
        $("#tableProgramKerjaTahunan").DataTable({
            language    : {
                "zeroRecords"   : "Tidak ada program kerja, silahkan buat terlebih dahulu..",
                "emptyTable"    : "Tidak ada program kerja, silahkan buat terlebih dahulu..",
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang diproses",
            },
            serverSide  : false,
            processing  : true,
            autoWidth   : false,
            ajax        : {
                type    : "GET",
                dataType: "json",
                data    : {
                    groupDivisionID     : groupDivision,
                },
                url     : "/master/programkerja/tahunan/trans/get/listDataProkerTahunan/%",
            },
            columnDefs  : [
                { "targets" : [0, 5], "width":"5%", "className":"text-center" },
                { "targets" : [2, 3, 4], "width":"15%"},
            ],
        });
    } else if(id_table == 'tblSubProk') {
        $("#tblSubProk").DataTable().clear().destroy();
        $("#tblSubProk").DataTable({
            ordering    : false,
            paging      : false,
            pageLength  : -1,
            autoWidth   : false,
            searching   : false,
            bInfo       : false,
            columnDefs  : [
                { "targets":[0], "width":"5%", "className":"text-center" },
                { "targets":[1], "width":"15%","className":"text-center" },
            ],
        });
    }
}

function show_modal(id_modal, value)
{
    $("#"+id_modal).modal({backdrop: 'static', keyboard: false});
    $("#"+id_modal).modal('show');
    if(id_modal == 'modalTambahDataProkerTahunan')
    {
        show_select('prokTahunanGroupDivision','');
        show_select('prokTahunanPIC','','');
        
        show_table('tblSubProk');
        tambahBaris('tblSubProk','');

        $("#prokTahunanTime").yearpicker();
        $("#prokTahunanTime").val(2024);

        // var table   = $("#tblSubProk").DataTable();
        // $("#tblSubProk").on('click', '#btnHapus', function(){
        //     var row         = $(this).closest('tr');
        //     var barisKe     = table.row(row).index();
        //     var hitungBaris     = table.rows().count();

        //     if(barisKe > 0) {
        //         var hitung  = parseInt(hitungBaris) - parseInt(barisKe);
        //         table.row(row).remove().draw('false');
        //     }
        // });

        $("#btnTambahData").val('add');
        if(value != '') {
            $("#btnTambahData").val('edit');
            var url         = "/master/programkerja/tahunan/trans/get/getDataProkerTahunanDetail/"+value;
            var type        = "GET";
            var data        = value;
            var customMessage   =   Swal.fire({
                                        title   : 'Data Sedang Dimuat',
                                    });
                                    Swal.showLoading();
            getData(url, type, data, customMessage)
                .then((xhr) => {
                    var header  = xhr.data.header;
                    var detail  = xhr.data.detail;

                    // HEADER
                    $("#prokTahunanID").val(value);
                    $("#prokTahunanTitle").val(header['program_kerja_title']);
                    $("#prokTahunanDesc").val(header['program_kerja_description']);
                    $("#prokTahunanTime").val(header['program_kerja_periode']);
                    show_select('prokTahunanGroupDivision',header['program_kerja_group_div_id'],'');
                    show_select('prokTahunanPIC',header['program_kerja_group_div_id'], header['program_kerja_pic_id'])
                    
                    // DETAIL
                    for(var i = 0; i < detail.length; i++) {
                        tambahBaris('tblSubProk', detail[i]);
                    }
                    $("#btnTambahData").show();
                })
                .catch((xhr) => {
                    // console.log(xhr);
                });
        }
        $("#"+id_modal).on('shown.bs.modal', function(){
            $("#prokTahunanTitle").focus();
        });
    }
}

function close_modal(id_modal)
{
    $("#"+id_modal).modal('hide');
    if(id_modal == 'modalTambahDataProkerTahunan') {
        $("#modalTambahDataProkerTahunan").on('hidden.bs.modal', function(){
            $("#formProkerAdd").trigger('reset');
            $("#btnTambahBarisSubProk").val(1);

            $("#btnTambahData").show();
            $("#btnTambahData").val('');
            $("#prokTahunanID").val(null)
        });
    }
}

function show_select(id_select, value, value2)
{
    $("#"+id_select).select2({
        theme   : 'bootstrap4',
    });

    if(id_select == 'prokTahunanGroupDivision') {
        var html    = "<option selected disabled>Pilih Grup Divisi</option>";
        var url     = "/master/data/trans/get/groupDivision";
        var data    = "";
        var type    = "GET";

        // GET DATA
        getData(url, type, data)
            .then(function(xhr){
                var data    = xhr.data;
                $.each(data, function(i,item){
                    var gdID    = item['id'];
                    var gdName  = item['name'];

                    html    += "<option value='" + gdID + "'>" + gdName + "</option>";
                });
                $("#"+id_select).html(html);

                if(value != '') {
                    $("#"+id_select).val(value);
                }
            })
            .catch(function(xhr){
                console.log(xhr);
                $("#"+id_select).html(html);
            });
    } else if(id_select == 'prokTahunanPIC') {
        var html    = "<option selected disabled>Pilih Penanggung Jawab</option>";
        if(value == '') {
            $("#"+id_select).html(html);
        } else {
            var url     = "/master/programkerja/get/data/PIC";
            var data    = {
                "groupDivisionID"   : value,
            };
            var type    =  "GET";
            if(value2 == '') {
                var customMessage   =   Swal.fire({
                                            title   : 'Data Sedang Dimuat',
                                        });
                                        Swal.showLoading();
            } else {
                var customMessage   = "";
            }
            getData(url, type, data, customMessage)
                .then(function(xhr){
                    var data    = xhr.data;
                    $.each(data, function(i,item){
                        html    += "<option value='" + item['employee_id'] + "'>" + item['employee_name'] + "</option>";
                    });
                    $("#"+id_select).html(html);
                    Swal.close();
                    if(value2 == '') {
                        $("#"+id_select).select2('open');
                    } else {
                        $("#"+id_select).val(value2).trigger('change');
                    }
                }).catch(function(xhr){
                    $("#"+id_select).html(html);
                });
        }
    }
}

function tambahBaris(id_table, data)
{
    if(id_table == 'tblSubProk') {
        // var barisKe     = $("#tblSubProk").DataTable().rows().count();
        var barisKe     = $("#btnTambahBarisSubProk").val();
        var seq         = parseInt(barisKe);
        var button      = "<button type='button' class='btn btn-danger btn-sm' id='btnHapus' value="+seq+" onclick='hapusBaris(`tblSubProk`, this.value)'><i class='fa fa-trash'></i></button>";
        var inputSeq    = "<input type='text' class='form-control form-control-sm text-center' name='subProkSeq"+seq+"' id='subProkSeq"+seq+"' readonly placeholder='Seq'>";
        var inputJudul  = "<input type='text' class='form-control form-control-sm' name='subProkTitle"+seq+"' id='subProkTitle"+seq+"' placeholder='Sub. Program Kerja' autocomplete='off'>";
        $("#tblSubProk").DataTable().row.add([
            button,
            inputSeq,
            inputJudul
        ]).draw('false');

        $("#subProkTitle"+seq).on('keyup', function(e){
            if(e.which == 13) {
                tambahBaris('tblSubProk','');
            }
        });

        if(data != '') {
            var newSeq  = data['sub_program_kerja_seq'];
            $("#subProkSeq"+newSeq).val(data['sub_program_kerja_seq']);
            $("#subProkTitle"+newSeq).val(data['sub_program_kerja_title']);
        } else {
            $("#subProkTitle"+seq).focus();
        }
        $("#subProkSeq"+seq).val(seq);
        $("#btnTambahBarisSubProk").val(parseInt(seq) + 1);
    }
}

function hapusBaris(id_table, seq)
{
    var current_seq     = $("#btnTambahBarisSubProk").val();
    if(seq > 1) {
        if(current_seq - seq == 1) {
            // REMOVE ROW PADA DATATABLE
            $("#"+id_table).DataTable().row(seq - 1).remove().draw('false')

            $("#btnTambahBarisSubProk").val(current_seq  - 1);
        }
    }
}

function do_simpan(jenis)
{
    var DataSubProkerTahunan    = [];
    var tblSubProkCount         = $("#tblSubProk").DataTable().rows().count();
    
    for(var i = 0; i < tblSubProkCount; i++) {
        var ke      = i + 1;
        var subProk     = {
            "subProkSeq"    : ke,
            "subProkTitle"  : $("#subProkTitle"+ke).val(),
        };

        DataSubProkerTahunan.push(subProk);
    }

    var dataKirim     = {
        "prtID"             : $("#prokTahunanID").val(),
        "prtTitle"          : $("#prokTahunanTitle").val(),
        "prtDescription"    : $("#prokTahunanDesc").val(),
        "prtPeriode"        : $("#prokTahunanTime").val(),
        "prtGroupDivisionID": $("#prokTahunanGroupDivision").val(),
        "prtPICEmployeeID"  : $("#prokTahunanPIC").val(),
        "prtSub"            : DataSubProkerTahunan,
    };

    var url     = "/master/programkerja/tahunan/trans/store/dataProkerTahunan/"+jenis;
    var type    = "POST";
    var sendData    = dataKirim;
    var customMessage   = Swal.fire({title:'Data Sedang Diproses'});Swal.showLoading();;

    getData(url, type, sendData, customMessage)
        .then(function(xhr){
            Swal.fire({
                icon    : xhr.alert.icon,
                title   : xhr.alert.message.title,
                text    : xhr.alert.message.text+" "+xhr.alert.message.errMsg
            }).then((results)   => {
                if(results.isConfirmed) {
                    close_modal('modalTambahDataProkerTahunan');
                    show_table('tableProgramKerjaTahunan');
                }
            });
        })
        .catch(function(xhr){
            Swal.fire({
                icon    : xhr.responseJSON.alert.icon,
                title   : xhr.responseJSON.alert.message.title,
                text    : xhr.responseJSON.alert.message.text
            });
        });
}


function getData(url, type, sendData, beforeSendRules)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            async   : true,
            cache   : false,
            type    : type,
            dataType: "json",
            data    : {
                _token  : CSRF_TOKEN,
                sendData: sendData,
            },
            url     : url,
            beforeSend  : function(){
                beforeSendRules
            },
            success : function(xhr) {
                resolve(xhr);
            },
            error   : function(xhr) {
                Swal.fire({
                    icon     : 'error',
                    text    : xhr.statusText,
                });
                reject(xhr);
            }
        })
    });
}