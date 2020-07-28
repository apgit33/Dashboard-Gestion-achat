<?php
require_once "db.php";
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

//  Requête sql d'éxtracton d'informations de la base de donnée
$sql = "SELECT id, localisation, name, reference, categorie, date, guarantee, price, maintenance, picture, manual FROM products";
$sth = $dbh->prepare($sql);
$sth->execute();

/* Affichage et protection des valeurs extraites*/
$datas = $sth->fetchAll(PDO::FETCH_ASSOC);

$template = $twig->load('produit.html.twig');
echo $template->render(['liste_produits' => $datas]);
