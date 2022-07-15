import Swiper from 'swiper/swiper-bundle';

let logoSlider = document.getElementsByClassName('logo-slider')[0];

if (logoSlider) {
    const swiper = new Swiper('.logo-slider', {
        loop: true,
        slidesPerView: 2,
        spaceBetween: 10,
        autoplay: {
            delay: 2500,
            disableOnInteraction: false
        },

        pagination: {
            el: '.swiper-pagination',
            clickable: true,
            renderBullet: (index, className) => {
                return '<span class="' + className + '">' +
                    '<svg width="10" height="3" viewBox="0 0 10 3" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M9.088 0.432C8.704 0.832 8.016 1.376 7.264 1.376C6.48 1.376 5.936 1.024 5.456 0.832C4.944 0.624 4.352 0.256 3.44 0.256C2.416 0.256 1.488 0.896 0.96 1.536L1.568 2.416C1.984 1.952 2.672 1.456 3.424 1.456C4.208 1.456 4.752 1.808 5.232 2C5.76 2.208 6.352 2.576 7.28 2.576C8.288 2.576 9.216 1.952 9.728 1.28L9.088 0.432Z" fill="#E30613"/></svg>'
                    + '</span>';
            }
        },

        breakpoints: {
            768: {
                slidesPerView: 3,
                spaceBetween: 20
            },
            1024: {
                slidesPerView: 4,
                spaceBetween: 30
            }
        }
    });
}

//Hero Swiper
let heroSwiperContainer = document.getElementsByClassName('hero-swiper')[0];
let heroSwiperEl = document.getElementsByClassName('hero-slider')[0];

if (heroSwiperEl) {
    const heroSwiper = new Swiper(heroSwiperEl, {
        loop: false,
        slidesPerView: 1.2,
        spaceBetween: 30,
        centeredSlides: false,
        grabCursor: true,
        direction: 'horizontal',
        mousewheel: true
    });

    heroSwiper.on('reachEnd', () => {
        heroSwiperEl.classList.add('no-pointer-event');
    });

    heroSwiper.on('reachBeginning', () => {
        heroSwiperEl.classList.add('no-pointer-event');
    });

    heroSwiperContainer.addEventListener('mousedown', () => {
        if (heroSwiperEl.classList.contains('no-pointer-event')) {
            heroSwiperEl.classList.remove('no-pointer-event');
        }
    });

    heroSwiperContainer.addEventListener('mouseenter', () => {
        if (heroSwiperEl.classList.contains('no-pointer-event')) {
            heroSwiperEl.classList.remove('no-pointer-event');
        }
    });
    heroSwiperContainer.addEventListener('mouseleave', () => {
        if (heroSwiperEl.classList.contains('no-pointer-event')) {
            heroSwiperEl.classList.remove('no-pointer-event');
        }
    });
}

// General Swiper
let swiperEL = document.getElementsByClassName('media-slider');
let sliderPara = document.getElementsByClassName('paragraph--slider')[0];

let swiper;
let createSwiper = (winWidth) => {
    for (let i = 0; i < swiperEL.length; i++) {
        swiper = new Swiper(swiperEL[i], {
            slidesPerView: winWidth <= 992 ? 1.5 : 2.5,
            spaceBetween: 30,
            scrollbar: {
                el: '.swiper-scrollbar',
                draggable: true,
                hide: false,
                snapOnRelease: true,
                dragSize: 50
            },
            grabCursor: true,
            mousewheel: true,
            loop: false,
            centeredSlides: false
        });
    }
};

let scrollSide = () => {
    for (let i = 0; i < swiperEL.length; i++) {
        swiper.on('reachEnd', () => {
            swiperEL[i].classList.add('no-pointer-event');
        });

        swiper.on('reachBeginning', () => {
            swiperEL[i].classList.add('no-pointer-event');
        });

        sliderPara.addEventListener('mousedown', () => {
            if (swiperEL[i].classList.contains('no-pointer-event')) {
                swiperEL[i].classList.remove('no-pointer-event');
            }
        });

        sliderPara.addEventListener('mouseenter', () => {
            if (swiperEL[i].classList.contains('no-pointer-event')) {
                swiperEL[i].classList.remove('no-pointer-event');
            }
        });
        sliderPara.addEventListener('mouseleave', () => {
            if (swiperEL[i].classList.contains('no-pointer-event')) {
                swiperEL[i].classList.remove('no-pointer-event');
            }
        });
    }
};

document.addEventListener('DOMContentLoaded', () => {
    if (swiperEL) {
        createSwiper(window.innerWidth);
        scrollSide();
    }
});

window.addEventListener('resize', () => {
    if (swiperEL) {
        createSwiper(window.innerWidth);
        scrollSide();
    }
});


