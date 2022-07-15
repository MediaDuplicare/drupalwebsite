let toggle = document.getElementsByClassName('js-toggle');
let toggleMobileNav = document.querySelector('.js-toggle-mobile');


for (let i = 0; i < toggle.length; i++) {
    toggle[i].addEventListener('click', () => {
        toggle[i].parentElement.classList.toggle('is-active');
    }, false);
}

document.addEventListener('click', (event) => {
    for (let i = 0; i < toggle.length; i++) {
        let isClickInsideElement = toggle[i].parentElement.contains(event.target);
        if (!isClickInsideElement) {
            if (toggle[i].parentElement.classList.contains('is-active')) {
                toggle[i].parentElement.classList.toggle('is-active');
            }
        }
    }
});

toggleMobileNav.addEventListener('click', () => {
    document.getElementById('mobile-nav').classList.toggle('show');
    document.querySelector('.site-header').classList.toggle('is-open');
});

let subMenuBut = document.getElementsByClassName('sidebar-title');
let subMenuTarget = document.getElementsByClassName('menu-sidebar');

for (let i = 0; i < subMenuBut.length; i++) {
    subMenuBut[i].addEventListener('click', () => {
        subMenuTarget[i].classList.toggle('hidden');
    });
}

let hideShowSub = (menu, button, sizeWidth) => {
    for (let i = 0; i < subMenuBut.length; i++) {
        if (sizeWidth < 1024) {
            button[i].classList.remove('web');
            menu[i].classList.add('hidden');
        } else {
            button[i].classList.add('web');
            menu[i].classList.remove('hidden');
        }
    }
};


window.addEventListener('load', () => {
    hideShowSub(subMenuTarget, subMenuBut, window.innerWidth);
});

window.addEventListener('resize', () => {
    if (window.innerWidth > 1200) {
        //navigation
        document.getElementById('mobile-nav').classList.remove('show');
        document.querySelector('.site-header').classList.remove('is-open');
    }
    //sidebar menu
    hideShowSub(subMenuTarget, subMenuBut, window.innerWidth);
});
