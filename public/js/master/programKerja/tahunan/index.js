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
        $("#tableProgramKerjaTahunan").DataTable().clear().destroy();
        $("#tableProgramKerjaTahunan").DataTable({
            language    : {
                "zeroRecords"   : "Tidak ada program kerja, silahkan buat terlebih dahulu..",
                "emptyTable"    : "Tidak ada program kerja, silahkan buat terlebih dahulu..",
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang diproses",
            },
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
            ],
        });
    }
}

function show_modal(id_modal)
{
    $("#"+id_modal).modal('show');
    if(id_modal == 'modalTambahDataProkerTahunan')
    {
        show_select('prokTahunanGroupDivision','');
        show_select('prokTahunanPIC','');

        $("#"+id_modal).on('shown.bs.modal', function(){
            $("#prokTahunanTitle").focus();
        });
        
        show_table('tblSubProk');
        tambahBaris('tblSubProk');
    }
}

function close_modal(id_modal)
{
    $("#"+id_modal).modal('hide');
    if(id_modal == 'modalTambahDataProkerTahunan') {
        $("#modalTambahDataProkerTahunan").on('hidden.bs.modal', function(){
            $("#formProkerAdd").trigger('reset');
            $("#btnTambahBarisSubProk").val(1);
        });
    }
}

function show_select(id_select, value)
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

            getData(url, type, data)
                .then(function(xhr){
                    var data    = xhr.data;
                    $.each(data, function(i,item){
                        html    += "<option value='" + item['employee_id'] + "'>" + item['employee_name'] + "</option>";
                    });
                    $("#"+id_select).html(html);
                    $("#"+id_select).select2('open');
                }).catch(function(xhr){
                    $("#"+id_select).html(html);
                });
        }
    }
}

function tambahBaris(id_table, data)
{
    if(id_table == 'tblSubProk') {
        var seq     = $("#btnTambahBarisSubProk").val();
        var button  = "<button type='button' class='btn btn-danger btn-sm' onclick='hapusBaris(`tblSubProk`)'><i class='fa fa-trash'></i></button>";
        var inputJudul  = "<input type='text' class='form-control form-control-sm' name='subProkTitle"+seq+"' id='subProkTitle"+seq+"' placeholder='Sub. Program Kerja'>";
        $("#tblSubProk").DataTable().row.add([
            button,
            inputJudul
        ]).draw('false');
        var tempData    = {
            "seq"   : seq,
            "subProkerTitle"    :$("#subProkTitle"+seq).val(),
        };
        $("#subProkTitle"+seq).focus();
        $("#subProkTitle"+seq).on('keyup', function(e){
            if(e.which == 13) {
                tambahBaris('tblSubProk');
            }
        });
        $("#btnTambahBarisSubProk").val(parseInt(seq) + 1);
    }
}

function do_simpan(jenis)
{
    if(jenis == 'add')
    {
        var DataSubProkerTahunan    = [];
        var tblSubProkCount         = $("#tblSubProk").DataTable().row().count();
        for(var i = 0; i < tblSubProkCount; i++) {
            var ke      = i + 1;
            var subProk     = {
                "subProkSeq"    : ke,
                "subProkTitle"  : $("#subProkTitle"+ke).val(),
            };

            DataSubProkerTahunan.push(subProk);
        }

        var dataKirim     = {
            "prtTitle"          : $("#prokTahunanTitle").val(),
            "prtDescription"    : $("#prokTahunanDesc").val(),
            "prtPeriode"        : $("#prokTahunanTime").val(),
            "prtGroupDivisionID": $("#prokTahunanGroupDivision").val(),
            "prtPICEmployeeID"  : $("#prokTahunanPIC").val(),
            "prtSub"            : DataSubProkerTahunan,
        };

        var url     = "/master/programkerja/tahunan/trans/store/dataProkerTahunan/add";
        var type    = "POST";
        var sendData    = dataKirim;

        getData(url, type, sendData)
            .then(function(xhr){
                console.log(xhr);
            })
            .catch(function(xhr){
                console.log(xhr);
            });
    }
}


function getData(url, type, sendData)
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