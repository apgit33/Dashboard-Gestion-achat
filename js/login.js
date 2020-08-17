//on récupère le formulaire
const f_login = document.getElementById('f_login');

//on récupère la div d'erreurs
const login_erreur = document.getElementById('check_login');

//on ajoute un écouteur d'évènement sur la soumision du formulaire
f_login.addEventListener('submit', e => {
    //on empêche l'envoie naturel du formulaire
    e.preventDefault();

    //on vide le champ des erreurs
    login_erreur.innerHTML ="";

    //on récupère les données du formulaire sous forme de data (clé=>valeur)
    const formData = new FormData(f_login);

    //on envoie les données du formulaire en ajax
    fetch('./treatement/login.php', {
        body: formData,
        method: "POST"
    })
    .then(response => response.json())
    .then(datas => {
        //si il n'y a pas d'erreur, on redirige vers la page produit.php
        if (datas.validation === true) {
            location.href = "produit.php"
        }
        //s'il y a une erreur, crée un nouveau champ html et l'ajoute à la div d'erreur
        if (datas.erreur !== "") {
            let champ = document.createElement("p");
            champ.innerHTML = datas.erreur;
            login_erreur.appendChild(champ);
        }
    });
});