// import 'bootstrap/dist/css/bootstrap.css';
// import 'bootstrap-icons/font/bootstrap-icons.css';
import {
    Calendar
} from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import bootstrap5Plugin from '@fullcalendar/bootstrap5';
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min';

document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var eventModal = new bootstrap.Modal(document.getElementById('eventModal'));
    var modalTitle = document.getElementById('eventModalLabel');
    var modalContent = document.getElementById('modalContent');

    var eventsUrl = calendarEl.getAttribute('data-url');
    console.log(eventsUrl);

    var calendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, interactionPlugin, bootstrap5Plugin],
        themeSystem: 'bootstrap5',
        initialView: 'dayGridMonth',
        events: eventsUrl,
        eventClick: function (info) {
            info.jsEvent.preventDefault(); // Prevent the default action (redirect)
            fetch(`/en/student/events/${info.event.id}`)
                .then(response => response.json())
                .then(data => {
                    modalTitle.textContent = data.eventTitle;
                    modalContent.innerHTML = data.content;
                    eventModal.show();
                });
        }
    });

    calendar.render();
});