<?php
// Ouverture de session
session_start();

require_once "db.php";
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

//Vérifie si on est en état d'ajout ou d'édition
if( isset($_GET['id']) && isset($_GET['edit'])){
        $titre='Modification du produit';
    }else{
        $titre='Ajouter un produit';
    }


// Variables vide à remplir
$data="";
$user = (isset($_SESSION['user']))? $_SESSION['user'] : "";

// Requête sql pour éxtraire les valeurs du tableau si on est en état d'édition
if(isset($_GET['id']) && isset($_GET['edit'])){
    $sql = 'SELECT id, localisation, name, reference, categorie, date, guarantee, price, maintenance, picture, manual FROM products WHERE id=:id';
    $sth = $dbh->prepare($sql);
    $sth->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
    $sth->execute();
    $data = $sth->fetch(PDO::FETCH_ASSOC);
}




$template = $twig->load('edit.html.twig');
echo $template->render([
    'titre' => $titre,
    'produit' => $data,
    'user' => $user
]);