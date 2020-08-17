<?php
session_start();
require_once "../db.php";

//initialisation/récupération des valeurs
$validation = false;
$erreur = "Mauvais identifiants";
$user_email = (isset($_POST['user_email']))?trim($_POST['user_email']) : "";
$user_password = (isset($_POST['user_password']))? $_POST['user_password'] : "";


//si l'email n'est pas vide, on récupère les champs dans la table users
if ($user_email != '') {
    $query = "SELECT password,nom FROM users WHERE email=:email";
    $sth = $dbh->prepare($query);
    $sth->bindParam(':email', $user_email, PDO::PARAM_STR);
    $sth->execute();
    $donnee = $sth->fetch(PDO::FETCH_ASSOC);

    //si le mdp rentré correspond au mdp récupéré on crée une variable
    //de session pour le nom et on change les valeurs de $validation et $erreur
    if(password_verify($user_password,$donnee['password'])){
        $_SESSION['user'] = $donnee['nom'];
        $validation = true;
        $erreur = "";
    }
}

echo json_encode(array('validation' => $validation,'erreur' => $erreur));