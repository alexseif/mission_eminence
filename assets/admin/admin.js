"use strict";
import 'core-js/stable';
import './styles/admin.scss';
import 'bootstrap';

import 'bootstrap-icons/font/bootstrap-icons.css';


document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('.needs-validation');
    if (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    }
});