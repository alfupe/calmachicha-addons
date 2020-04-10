class PostSliderHandlerClass extends elementorModules.frontend.handlers.Base {
    constructor(props) {
        super(props);
        this.$swiper = this.initSwiper();
    }

    initSwiper = () => {
        const postSwiper = new Swiper('.post-slider--swiper', {
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
}

jQuery(window).on('elementor/frontend/init', () => {
    const addHandler = $element => {
        elementorFrontend.elementsHandler.addHandler(PostSliderHandlerClass, {
            $element,
        });
    };

    elementorFrontend.hooks.addAction('frontend/element_ready/cca-post-slider.default', addHandler);
});
