// *********** Début Input file ***********
//on récupère tous les input de type file
const filesInput = document.querySelectorAll('input[type=file]');

//pour chaque file on ajoute un event au changement de l'input
for (let fileInput of filesInput) {
    fileInput.addEventListener('change', function() {
        if (fileInput.files.length > 0) {
            //on récupère le champ file-name de l'input utilisé
            const fileName = fileInput.parentElement.lastElementChild;
            //on change le content de file-name par le nom du fichier selectioné
            fileName.textContent = fileInput.files[0].name;
        }
    });
}
// *********** Fin Input file ***********


// *********** Début Form ***********
//on récupère le formulaire
const f_product = document.getElementById('form_product');

//on récupère les div d'erreurs
const f_erreur = document.getElementsByClassName('verif');

//on ajoute un écouteur d'évènement sur la soumision du formulaire
f_product.addEventListener('submit', e => {
    //on empêche l'envoie naturel du formulaire
    e.preventDefault();

    //on vide le champ des erreurs
    for (let erreur of f_erreur) {
        erreur.innerHTML ="";
    }

    //on récupère les données du formulaire sous forme de data (clé=>valeur)
    const formData = new FormData(f_product);

    //on envoie les données du formulaire en ajax
    fetch('./treatement/edit_ajout.php', {
        body: formData,
        method: "POST"
    })
    .then(response => response.json())
    .then(datas => {
        if (datas.validation === true) {
            location.href = "produit.php"
        }
        datas.erreurs.forEach((data) => {
            if(data.name) {
                let champ = document.createElement("p");
                champ.innerHTML = data.name;
                document.getElementById("check_name").appendChild(champ);
            }
            if(data.reference) {
                let champ = document.createElement("p");
                champ.innerHTML = data.reference;
                document.getElementById("check_reference").appendChild(champ);
            }
            if(data.localisation) {
                let champ = document.createElement("p");
                champ.innerHTML = data.localisation;
                document.getElementById("check_localisation").appendChild(champ);
            }
            if(data.adresse) {
                let champ = document.createElement("p");
                champ.innerHTML = data.adresse;
                document.getElementById("check_adresse").appendChild(champ);
            }
            if(data.categorie) {
                let champ = document.createElement("p");
                champ.innerHTML = data.categorie;
                document.getElementById("check_categorie").appendChild(champ);
            }
            if(data.date) {
                let champ = document.createElement("p");
                champ.innerHTML = data.date;
                document.getElementById("check_date_achat").appendChild(champ);
            }
            if(data.guarantee) {
                let champ = document.createElement("p");
                champ.innerHTML = data.guarantee;
                document.getElementById("check_date_garantie").appendChild(champ);
            }
            if(data.price) {
                let champ = document.createElement("p");
                champ.innerHTML = data.price;
                document.getElementById("check_price").appendChild(champ);
            }
            if(data.maintenance) {
                let champ = document.createElement("p");
                champ.innerHTML = data.maintenance;
                document.getElementById("check_maintenance").appendChild(champ);
            }
            if(data.ticket) {
                let champ = document.createElement("p");
                champ.innerHTML = data.ticket;
                document.getElementById("check_ticket").appendChild(champ);
            }
            if(data.manual) {
                let champ = document.createElement("p");
                champ.innerHTML = data.manual;
                document.getElementById("check_manual").appendChild(champ);
            }
        });
    });
});
// *********** Fin Form ***********


// *********** Début Form -> catégorie ***********
//on récupère le select
const selectForm = document.querySelector('select[name="categorie"]');
//on récupère le champ prévu pour la nouvelle catégorie
const newCat = document.getElementById('new_cat');

//on écoute le changement du select, si la valeur est -1 on ajoute la nouvelle catégorie sinon on la retire
selectForm.addEventListener('change',function(){
    if (this.value != -1) {
        newCat.innerHTML=""
    }else {
        newCat.innerHTML ="<label class=\"label\">Nouvelle catégorie :</label><div class=\"control\"><input class=\"input is-medium\" type=\"text\" name=\"new_categorie\"></div>";
    }
})
// *********** Fin Form -> catégorie ***********
