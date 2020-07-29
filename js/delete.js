//on récupère tous les boutons delete
const btnsDelete = document.getElementsByClassName('btn_delete');

//Pour chaque button Supprimer on ajoute un déclencheur d'évènement au click, qui toggle la classe is-active de la modal
for (let btnDelete of btnsDelete) {
    btnDelete.addEventListener('click', function(){
        this.nextElementSibling.classList.toggle('is-active');
    });
}

//on récupère les buttons de fermeture des modals
const modalsClose = document.getElementsByClassName('modal-close');

//Pour chaque button modal-close on ajoute un déclencheur d'évènement au click, qui toggle la classe is-active de la modal
for (let modalClose of modalsClose) {
    modalClose.addEventListener('click', function(){
        this.parentElement.classList.toggle('is-active');
    });
}