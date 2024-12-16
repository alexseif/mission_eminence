import 'bootstrap/dist/css/bootstrap.css';
import 'bootstrap-icons/font/bootstrap-icons.css';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrap5Plugin from '@fullcalendar/bootstrap5';
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min';
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    var eventForm = document.getElementById('eventForm');
    var eventTitle = document.getElementById('eventTitle');
    var eventDescription = document.getElementById('eventDescription');
    var eventStart = document.getElementById('eventStart');
    var eventEnd = document.getElementById('eventEnd');
    var editEventBtn = document.getElementById('editEventBtn');

    var calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin],
        initialView: 'dayGridMonth',
        events: '/admin/calendar/api/events',
        eventClick: function (info) {
            console.log(info);
            console.log(info.event);
            console.log(info.event.extendedProps);
            eventTitle.value = info.event.title;
            eventDescription.value = info.event.extendedProps.description;
            eventStart.value = info.event.extendedProps.start;
            eventEnd.value = info.event.extendedProps.end;
            eventModal.show();
        }
    });

    calendar.render();
});
