require('./bootstrap');

import Alpine from 'alpinejs';

if (!window.Alpine) {
    window.Alpine = Alpine;
    Alpine.start();
}

document.addEventListener('DOMContentLoaded', function () {
    var swiperEl = document.querySelector('.reviewSwiper');
    if (swiperEl && typeof Swiper !== 'undefined') {
        new Swiper(swiperEl, {
            slidesPerView: 1,
            spaceBetween: 16,
            navigation: {
                nextEl: '.review-next',
                prevEl: '.review-prev',
            },
            on: {
                init: function () {
                    var current = document.querySelector('.review-current');
                    var total = document.querySelector('.review-total');
                    if (current) current.textContent = String(this.realIndex + 1);
                    if (total) total.textContent = String(this.slides.length);
                },
                slideChange: function () {
                    var current = document.querySelector('.review-current');
                    if (current) current.textContent = String(this.realIndex + 1);
                }
            }
        });
    }
});