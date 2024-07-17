$(document).ready(function(){
    show_table('table_sasaran');
});

var site_url    = window.location.pathname;

function show_table(id_table)
{
    if(id_table == 'table_sasaran') {
        $("#table_sasaran").DataTable().clear().destroy();
        $("#table_sasaran").DataTable({
            language    : {
                "zeroRecords"   : "Tidak ada program kerja, silahkan buat terlebih dahulu..",
                "emptyTable"    : "Tidak ada program kerja, silahkan buat terlebih dahulu..",
                "processing"    : "<i class='fa fa-spinner fa-spin'></i> Data Sedang diproses",
            },
            serverSide  : false,
            processing  : true,
            autoWidth   : false,
            ajax        : {
                async   : true,
                type    : "GET",
                dataType: "json",
                data    : {
                    "_token"    : CSRF_TOKEN,
                    "sendData"  : {
                        "id"    : '%',
                    },
                },
                url     : site_url + "/listSasaranMarketing",
            },
            columnDefs  : [
                { "targets" : [0, 4], "width":"5%", "className":"text-center" },
                { "targets" : [2, 3], "width":"15%"},
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
                { "targets":[3], "width":"15%", "className":"text-right" },
            ],
        });
    }
}

function show_modal(id_modal, jenis, value)
{
    if(id_modal == 'modalForm')
    {
        show_table('tblSubProk');

        if(jenis == 'add') {
            $("#"+id_modal).modal({backdrop : 'static', keyboard : false});
            $("#"+id_modal).modal('show');
            $("#prokTahunanTime").yearpicker({
                autoHide: true,
                year    : parseInt(moment().format('YYYY')),
            });
            $("#prokTahunanTime").val(moment().format('YYYY'));
            tambahBaris('tblSubProk', '');
            $("#btnSave").val(jenis);
        } else if(jenis == 'edit') {
            // GET DATA
            var url     = site_url + "/dataSasaran/"+value;
            var type    = "GET";
            var message = Swal.fire({ title : "Data Sedang Dimuat" }); Swal.showLoading();

            getData(url, type, '', message, true)
                .then((success) => {
                    $("#"+id_modal).modal({backdrop : 'static', keyboard : false});
                    $("#"+id_modal).modal('show');
                    Swal.close();
                    console.log(success);

                    // SHOW DATA TO VIEW
                    var getDataHeader   = success.data.header[0];
                    var getDataDetail   = success.data.detail;

                    // HEADER
                    $("#prokTahunanID").val(value);
                    $("#prokTahunanTitle").val(getDataHeader.pkt_title);
                    $("#prokTahunanDesc").val(getDataHeader.pkt_description);
                    $("#prokTahunanTime").val(getDataHeader.pkt_year);
                    
                    // DETAIL
                    for(var i = 0; i < getDataDetail.length; i++) {
                        tambahBaris('tblSubProk', getDataDetail[i]);
                    }
                })
                .catch((err)    => {
                    Swal.fire({
                        icon    : 'error',
                        title   : 'Terjadi Kesalahan',
                        text    : 'Tidak ada data',
                    });
                });
            $("#btnSave").val(jenis);
        }
    }
}

function close_modal(id_modal)
{
    $("#"+id_modal).modal('hide');
    if(id_modal == 'modalForm') {
        $("#modalForm").on('hidden.bs.modal', function(){
            $("#formProkerAdd").trigger('reset');
            $("#btnTambahBarisSubProk").val(1);

            $("#btnTambahData").show();
            $("#btnTambahData").val('');
            $("#prokTahunanID").val(null)
            isCalendarLoaded = 0;
        });
    }
}

function show_select(id_select, valueCari, valueSelect, isAsync)
{
    $("#"+id_select).select2({
        theme   : 'bootstrap4',
    });

    if( id_select == 'prokTahunanGroupDivision' ) {
        var html    = "<option selected disabled>Pilih Grup Divisi</option>";
        
        if(valueCari != '') {
            var url     = site_url + '/listGroupDivision';
            var type    = "GET";
            var data    = '%';
            // if( isAsync === true ) { var message = Swal.fire({ title : 'Data Sedang Dimuat', }); Swal.showLoading(); } else { var mesasge = ""; }

            getData(url, type, data, '', isAsync)
                .then((success) => {
                    Swal.close();
                    if(success.data.length > 0) {
                        $.each(success.data, function(i,item){
                            html    += "<option value='" + item.group_division_id + "'>" + item.group_division_name + "</option>";
                        });
                        $("#"+id_select).html(html);
                    } else {
                        $("#"+id_select).html(html);
                    }
                })
                .catch((err)    => {
                    Swal.close();
                    console.log(err.responseJSON);
                })

            $("#"+id_select).html(html);
        } else {
            $("#"+id_select).html(html);
        }
    } else if( id_select == 'prokTahunanPIC' ) {
        var html    = "<option selected disabled>Pilih PIC / Penanggung Jawab</option>";

        if(valueCari != '') {
            $("#"+id_select).html(html);
        } else {
            $("#"+id_select).html(html);
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
        var inputTarget = "<input type='number' class='form-control form-control-sm text-right' name='subProkTarget"+seq+"' id='subProkTarget"+seq+"' min='0' max='9999' step='1' id='subProkTarget"+seq+"' placeholder='Target' value='0' onclick='this.select()'>";
        $("#tblSubProk").DataTable().row.add([
            button,
            inputSeq,
            inputJudul,
            inputTarget
        ]).draw('false');

        $("#subProkTitle"+seq).on('keyup', function(e){
            if(e.which == 13) {
                tambahBaris('tblSubProk','');
            }
        });

        if(data != '') {
            var newSeq      = data['pktd_seq'];
            $("#subProkTitle"+newSeq).val(data['pktd_title']);
            $("#subProkTarget"+newSeq).val(data['pktd_target']);
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
            "subProkTarget" : $("#subProkTarget"+ke).val(),
        };

        DataSubProkerTahunan.push(subProk);
    }

    var dataKirim     = {
        "prtID"             : $("#prokTahunanID").val(),
        "prtTitle"          : $("#prokTahunanTitle").val(),
        "prtDescription"    : $("#prokTahunanDesc").val(),
        "prtPeriode"        : $("#prokTahunanTime").val(),
        "prtSub"            : DataSubProkerTahunan
    };

    var url     = site_url + "/simpanSasaran/"+jenis;
    var type    = "POST";
    var data    = dataKirim;
    var message = Swal.fire({ title : "Data Sedang Diproses" }); Swal.showLoading();
    var isAsync = true;

    getData(url, type, data, message, isAsync)
        .then((success) => {
            Swal.fire({
                icon    : success.alert.icon,
                title   : success.alert.message.title,
                text    : success.alert.message.text,
            }).then((results)=>{
                if(results.isConfirmed) {
                    close_modal('modalForm');
                    show_table('table_sasaran');
                }
            })
        })
        .catch((error)  => {
            Swal.fire({
                icon    : error.responseJSON.alert.icon,
                title   : error.responseJSON.alert.message.title,
                text    : error.responseJSON.alert.message.text,
            })
            console.log(error.responseJSON);
        })
}


function getData(url, type, sendData, beforeSendRules, isAsync)
{
    return new Promise(function(resolve, reject){
        $.ajax({
            async   : isAsync,
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