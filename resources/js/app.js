import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const el = document.querySelector('.reviewSwiper');
    if (!el || typeof Swiper === 'undefined') return;

    const swiper = new Swiper(el, {
        slidesPerView: 1,
        spaceBetween: 16,
        navigation: {
            nextEl: '.review-next',
            prevEl: '.review-prev',
        },
        on: {
            init() {
                const currentEl = document.querySelector('.review-current');
                const totalEl = document.querySelector('.review-total');
                if (currentEl) currentEl.textContent = String(this.realIndex + 1);
                if (totalEl) totalEl.textContent = String(this.slides.length);
            },
            slideChange() {
                const currentEl = document.querySelector('.review-current');
                if (currentEl) currentEl.textContent = String(this.realIndex + 1);
            }
        }
    });
});
