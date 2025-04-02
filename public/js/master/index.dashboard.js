var today   = moment().format('YYYY-MM-DD');
var base_url    = window.location.origin;
var roleData    = [];

$(document).ready(function(){
    console.log(today);

    const getData   = [
        doTransaction('master/data/trans/get/dataRoles', 'GET')
    ];

    Promise.allSettled(getData)
        .then((results)     => {
            let dataRoles     = results[0].status == 'fulfilled' ? results[0].value.data : [];
            if(dataRoles.length > 0 && roleData.length < 1) {
                roleData.push(dataRoles);
            }
        })
        .catch((error)      => {
            console.log(error);
        })
});

const showModal     = (idModal = '', jenis = '', data = '') => {
    if(idModal == 'modal_role') {
        $("#"+idModal).modal({ backdrop: 'static', keyboard: false });

        showTable('table_role', roleData[0]);
        if(roleData[0].length < 1) {
            $("#table_role").find('.dataTables_empty').html(`Tidak Ada Data Yang Bisa Dimuat`);
        }
    }
}

const closeModal    = (idModal = '') => {
    if(idModal == 'modal_role') {
        $("#"+idModal).modal('hide');

        $("#"+idModal).on('hidden.bs.modal', () => {
            
        })
    }
}

const showTable     = (idTable = '', data = []) => {
    if(idTable == 'table_role') {
        destroyTable(idTable);

        $("#"+idTable).DataTable({
            language    : {
                'emptyTable'     : `<i class="fa fa-spinner fa-spin"></i> Data Sedang Dimuat..`,
            },
            searching   : false,
            bInfo       : false,
            pageLength  : 5,
            lengthMenu  : [
                [-1, 5, 10, 25],
                ['All', 5, 10, 25]
            ],
            columnDefs  : [
                { "targets" : [0, 2], "className" : "text-center align-middle", "width" : "15%" },
                { "targets" : [1], "className" : "align-middle" },
            ],
            autoWidth   : false,
        });

        if(data.length > 0) {
            $.each(data, (i, item)  => {
                let seq     = i + 1;
                let roleID  = item['role_id'];
                let roleName= item['role_name'];

                $("#"+idTable).DataTable().row.add([
                    `<label class="font-weight-normal no-margins">${seq++}</label>`,
                    `<label class="font-wieght-normal no-margins">${roleName}</label>`,
                    `<button class="btn btn-sm btn-primary" type="button" value="${roleID}" title="Edit Data"><i class="fa fa-edit"></i></button>`
                ]).draw(false);
            })
        }
    }

    $("#"+idTable+"_wrapper").css('padding-bottom', '0px');
}

const destroyTable  = async (idTable = '') => {
    if(idTable != '') {
        return await $("#"+idTable).DataTable().clear().destroy();
    }
}

const doTransaction     = (url = '', type = '', data = [], message = '', processData = true, contentType = 'application/x-www-form-urlencoded') => {
    return new Promise((resolve, reject)    => {
        $.ajax({
            cache   : true,
            url     : base_url + '/' + url,
            type    : type,
            headers : {
                'X-CSRF-TOKEN'  : CSRF_TOKEN,
            },
            data    : data,
            beforeSend  : () => {
                return message;
            },
            processData     : processData,
            contentType     : contentType,
            success         : (isSuccess)   => {
                resolve(isSuccess);
            },
            error           : (isError)     => {
                reject(isError);
            }
        })
    })
}