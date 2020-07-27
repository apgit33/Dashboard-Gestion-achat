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
        if (datas.validation === true) {
            location.href = "dashboard.php"
        }
        datas.erreurs.forEach((data) => {
            //On créé un créé un élément HTML option
            let champ = document.createElement("p");

            //On affecte la valeur de l'élément créé
            champ.innerHTML = data;

            //On ajoute en noeud enfant à la datalist l'option créé
            login_erreur.appendChild(champ);
        });
    });
});