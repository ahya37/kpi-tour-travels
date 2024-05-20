$(document).ready(function(){
    console.log('test');
    show_table('tableLaporanIklan');
    show_select('adsStatusAdd');
});

function show_table(id_table, value)
{
    if(id_table == 'tableLaporanIklan') {
        $("#tableLaporanIklan").DataTable().clear().destroy();
        $("#tableLaporanIklan").DataTable({
            language    : {
                emptyTable  : 'Tidak ada data yang bisa ditampilkan, silahkan masukan beberapa data..',
                zeroRecords : 'Tidak ada data yang bisa ditampilkan, silahkan masukan beberapa data..',
            },
            ordering    : false,
        });
    }
}

function show_modal(id_modal, value)
{
    $("#"+id_modal).modal({backdrop: 'static', keyboard: false});
    $("#"+id_modal).modal('show');

    if(id_modal == 'modalAdd') {
        $("#modalAdd").on('shown.bs.modal', function(){
            $("#adsNameAdd").focus();
        })
        // UBAH KE DATEPICKER
        $("#adsStartDateAdd").daterangepicker({
            singleDatePicker : true,
            locale : {
                format  : 'DD/MM/YYYY',
            },
            autoApply    : true,
        }).attr('readonly','readonly').css({"cursor":"pointer", "background":"white"});;
    }
}

function close_modal(id_modal, value) {
    $("#"+id_modal).modal('hide');
}

function show_select(id_select)
{
    $("#"+id_select).select2({
        theme: 'bootstrap4',
    });
}

function ubahForm(id, val, jenis)
{
    if(jenis == 'ubah_ke_nol') {
        isNaN(parseFloat(val)) === true ? $("#"+id).val(0) : $("#"+id).val(val);
    } else if(jenis == 'ubah_ke_nol_1') {
        isNaN(parseFloat(val)) === true ? $("#"+id).val(parseFloat(0).toFixed(2)) : $("#"+id).val(val);
    } else if(jenis == 'ubah_ke_ribuan') {
        $("#"+id).val(formatRibuan(val));
    }
}

function hitungTanggal(tanggal_awal, lamanya)
{
    var selectedDate                = tanggal_awal;
    var changeFormat                = moment(selectedDate, 'DD/MM/YYYY').format('YYYY-MM-DD');
    var lamanya                     = $("#adsPeriodeAdd").val();
    var selectedDatePlusLamanya     = moment(changeFormat).add(lamanya, 'days').format('DD/MM/YYYY');
    
    $("#adsEndDateAdd").val(selectedDatePlusLamanya);
}

function formatRibuan(val) {
    var number_string   = val.replace(/[^.\d]/g, '').toString(),
    split               = number_string.split('.'),
    sisa                = split[0].length % 3,
    sisa                = split[0].length % 3,
    angka_hasil         = split[0].substr(0, sisa),
    ribuan              = split[0].substr(sisa).match(/\d{3}/gi);

    if(ribuan){
        separator = sisa ? ',' : '';
        angka_hasil += separator + ribuan.join(',');
    }

    angka_hasil = split[1] != undefined ? angka_hasil + '.' + split[1] : angka_hasil;
    return angka_hasil; 
}
