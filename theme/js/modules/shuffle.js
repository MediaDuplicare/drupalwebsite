let tab = document.getElementsByClassName('tab-link');
let tabContent = document.getElementsByClassName('tab-content');

for (let i = 0; i < tab.length; i++) {
    tab[i].addEventListener('click', function(e) {
        e.preventDefault();
        for (let j = 0; j < tab.length; j++) {
            tab[j].classList.add('text-secondary', 'bg-secondary-100');
            tab[j].classList.remove('text-white', 'bg-secondary');
        }
        for (let k = 0; k < tabContent.length; k++) {
            tabContent[k].classList.add('hidden');
        }

        this.classList.remove('text-secondary', 'bg-secondary-100');
        this.classList.add('text-white', 'bg-secondary');

        let matchingTab = this.getAttribute('data-tab');
        document.getElementById(matchingTab).classList.remove('hidden');
    }, false);
}


let modulesTab = document.getElementsByClassName('tab-link--modules');

for (let i = 0; i < modulesTab.length; i++) {
    modulesTab[i].addEventListener('click', function(e) {
        e.preventDefault();
        for (let j = 0; j < modulesTab.length; j++) {
            modulesTab[j].parentElement.classList.remove('is-active');
        }
        for (let k = 0; k < tabContent.length; k++) {
            tabContent[k].classList.add('hidden');
        }

        this.parentElement.classList.add('is-active');

        let matchingTab = this.getAttribute('data-tab');
        document.getElementById(matchingTab).classList.remove('hidden');
    }, false);
}
