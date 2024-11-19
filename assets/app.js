"use strict";
import 'core-js/stable';
import './styles/app.scss';

// Importing Stimulus controllers
// import './bootstrap';
import 'bootstrap';

document.addEventListener('DOMContentLoaded', () => {
    const counters = document.querySelectorAll('#counters .number');

    const animateCounter = (element) => {
        const target = parseInt(element.textContent);
        let count = 0;
        const duration = 2000;
        const increment = target / (duration / 16);

        const timer = setInterval(() => {
            count += increment;
            if (count >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(count);
            }
        }, 16);
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate');
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    counters.forEach(counter => observer.observe(counter));
});


