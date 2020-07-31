<?php

require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

/*

Début Code PHP

*/

// Lien vers la database 
    require_once('db.php');

// Création de variables utilisateur et mot de passe
    $utilisateur = '';
    $mdp = '';

// Requête sql d'extraction de données pour comparer aux valeurs inscrites
    if(isset($_POST['Connexion']) && !empty($_POST['nom']) && !empty($_POST['email']) && !empty($_POST['password'])){   

        $sql='SELECT nom, email password FROM user WHERE nom=:nom email=:email';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':nom', $_POST['nom'], PDO::PARAM_STR);
        $sth->execute();
        $data = $sth->fetch();
    //Insertions des identifiants extraits dans les variables
    $user= $data['nom'];
    $password = $data['password'];
    //Ouverture de la session
    session_start();
    // Comparaison des valeurs extraites et celle des formulaires
        if($_POST['nom'] == $user && $_POST['password'] == $password){
            //Si elles sont bonne, création d'une session
            $_SESSION['nom'] = $user;
            header('Location: index.php');
        }
    }

/*

Fin Code PHP

*/
$template = $twig->load('index.html.twig');
echo $template->render();
