
    const burgers = document.getElementsByClassName('navbar-burger');
    for (let burger of burgers) {
        burger.addEventListener('click', function(){
            // Get the target from the "data-target" attribute
            const target = this.dataset.target;
            const $target = document.getElementById(target);

            // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
            this.classList.toggle('is-active');
            // $target.classList.toggle('is-hidden-mobile');
            $target.classList.toggle('animated');
        })
    }