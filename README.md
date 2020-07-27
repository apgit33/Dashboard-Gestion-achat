# Dashboard-Gestion-achat

Projet de dashboard réalisé en équipe :
    - Front : Adrien Paturot (apgit33)
    - Back  : Arnaud (Arnaud1709) et Jessica (JessicaDaSilvaC)

Ce dashboard devra être **sécurisé par un système de login**.

## Fonctionnalités

    - lister les produits
    - Modifier un produit
    - Supprimer un produit
    - Ajouter un produit


### Informations disponibles pour un produit

    - Lieux d'achat (En vente directe ou e-commerce)
        Si vente directe - Possibilité de saisir l'adresse
        Si e-commerce - Possibilté de saisir l'url du e-commerce
    - Nom du produit
    - Référence du produit
    - Catégorie (Electroménager, TV-HIFI, Bricolage, Voiture....)
        Le produit n'appartiendra qu'à une seule catégorie
    - Date d'achat
    - Date de fin de garantie
    - Prix
    - Zone de saisie pour rentrer toutes les conseils d'entretiens du produit
    - Photo du tiket d'achat
    - Manuel d'utlisation (Pas obligatoire si existe pas)


### Bonus

    - Tâche cron qui envoie un email lorsque le produit arrive à terme de sa garantie
    - Une page ou l'on peut voir un graphique comparant les sommes dépensées par catégorie entre deux dates.


**Les formulaires seront validés par JS**

## Technos

PHP, TWIG, SASS, GIT, JS, HTML, CSS, FRAMEWORK CSS (optionnel)