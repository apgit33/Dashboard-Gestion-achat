//on récupère les burgers (normalement un seul)
const burgers = document.getElementsByClassName('navbar-burger');

//Pour chaque burger on ajoute un déclencheur d'évènement au click, qui toggle la classe is-active à celui-ci(this) et toggle la classe animated au menu($target)
for (let burger of burgers) {
    burger.addEventListener('click', function(){
        
        //on récupère l'id cible de l'attribut "data-target" du burger
        const target = this.dataset.target;
        //on récupère l'élement du target
        const $target = document.getElementById(target);

        //on toggle la classe is-active au burger
        this.classList.toggle('is-active');
        //on toggle la classe animated au target du burger (menu)
        $target.classList.toggle('animated');
    })
}

//on récupère l'url actuelle qu'on split au caractere ?
const url = document.location.href.split( "?" );
//on selectionne le 1er élément splité
const currentPage = url[0];
//on récupère tous les liens du menu
const menuLinks = document.getElementsByClassName("menu_link");

//pour chaque lien on check si href est égal à la page courante et si oui on active la classe menu_actif
for (let menuLink of menuLinks) {
    if (currentPage === menuLink.href) {
        menuLink.classList.toggle("menu_actif");
    }
}