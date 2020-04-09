function initSwipers() {
    var projectsSwiper = new Swiper('.projects-slider--swiper', {
        slidesPerView: 1,
        spaceBetween: 15,
        grabCursor: true,
        loop: false,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            1025: {
                slidesPerView: 3,
                spaceBetween: 30,
            },
            992: {
                slidesPerView: 3,
                spaceBetween: 20,
            },
            620: {
                slidesPerView: 2,
                spaceBetween: 20,
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', function(event) {
    initSwipers();
});
