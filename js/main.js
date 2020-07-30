//on récupère les burgers (normalement un seul)
const burgers = document.getElementsByClassName('navbar-burger');

//Pour chaque burger on ajoute un déclencheur d'évènement au click, qui toggle la classe is-active à celui-ci(this) et toggle la classe animated au menu($target)
for (let burger of burgers) {
    burger.addEventListener('click', function(){
        
        //on récupère l'id cible de l'attribut "data-target" du burger
        const target = this.dataset.target;
        //on récupère l'élement du target
        const $target = document.getElementById(target);

        this.classList.toggle('is-active');
        $target.classList.toggle('animated');
    })
}