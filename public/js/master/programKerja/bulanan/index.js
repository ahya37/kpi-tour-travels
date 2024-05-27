$(document).ready(function(){
    console.log('test');
    showCalendar();
});

function showCalendar()
{
    var idCalendar  = document.getElementById('calendar');

    var calendar    = new FullCalendar.Calendar(idCalendar, {
        themeSystem: 'bootstrap',
        headerToolbar: {
            left    : 'prev,next today',
            center  : 'title',
            right   : 'dayGridMonth',
        },
        // initialDate: moment().tz('Asia/Jakarta').format('YYYY-MM-DD'), // GET CURRENT DATE
        initialDate     : '2023-01-01',
        navLinks: true, // can click day/week names to navigate views
        selectable: true,
        selectMirror: true,
        contentHeight: 600,
        select: function(arg) {
            var idForm  = 'modalForm';
            var jenis   = 'add';
            var val     = '';
            showModal(idForm, jenis, val);
            $("#"+'modalForm').on('shown.bs.modal', function(){
                $("#modalTitle").html("Tambah Scheduler Tgl. "+moment(arg.startStr,'YYYY-MM-DD').format('DD/MM/YYYY'));
                $("#monthlyTitle").val(null);
                $("#monthlyTitle").focus();
                $("#btnSimpan").val(jenis);
            });

            $("#btnSimpan").on('click', function(){
                var dataSimpan  = [];
                var getDataEvents   = calendar.getEvents();
                for(var i = 0; i < getDataEvents.length; i++) {
                    
                }

                console.log(dataSimpan);
                // calendar.addEvent({
                //     title   : $("#monthlyTitle").val(),
                //     start   : arg.start,
                //     end     : arg.end,
                //     allDay  : arg.allDay,
                // });
                // $("#monthlyTitle").val(null);
                // $("#monthlyTitle").focus();
                // close_modal(idForm);
            })
            // var title   = prompt('Even Title: ');
            // if(title) {
            //     calendar.addEvent({
            //         title: title,
            //         start: arg.start,
            //         end: arg.end,
            //         allDay: arg.allDay
            //     });
            // }
        calendar.unselect()
        },
        eventClick: function(arg) {
            console.log(arg.event.startStr.split('T')[0]);
        },
        editable: true,
        fixedMirrorParent: document.body,
        dayMaxEvents: true, // allow "more" link when too many events
        events: [
            {
                title: 'All Day Event',
                start: '2023-01-01'
            },
            {
                title: 'Long Event',
                start: '2023-01-07',
                end: '2023-01-10'
            },
            {
                groupId: 999,
                title: 'Repeating Event',
                start: '2023-01-09T16:00:00'
            },
            {
                groupId: 999,
                title: 'Repeating Event',
                start: '2023-01-16T16:00:00'
            },
            {
                title: 'Conference',
                start: '2023-01-11',
                end: '2023-01-13'
            },
            {
                title: 'Meeting',
                start: '2023-01-12T10:30:00',
                end: '2023-01-12T12:30:00'
            },
            {
                title: 'Lunch',
                start: '2023-01-12T12:00:00'
            },
            {
                title: 'Meeting',
                start: '2023-01-12T14:30:00'
            },
            {
                title: 'Happy Hour',
                start: '2023-01-12T17:30:00'
            },
            {
                title: 'Dinner',
                start: '2023-01-12T20:00:00'
            },
            {
                title: 'Birthday Party',
                start: '2023-01-13T07:00:00'
            },
            {
                title: 'Click for Google',
                url: 'http://google.com/',
                start: '2023-01-28'
            },
            {
                color   : 'yellow',
                textColor   : 'black',
            }
        ]
    });

    calendar.render();
}

function showModal(idModal, type, value)
{
    $("#"+idModal).modal({backdrop: 'static', keyboard: false});
    $("#"+idModal).modal('show');
}

function closeModal(idModal) {
    $("#"+idModal).modal('hide');
}