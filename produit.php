<?php
session_start();
require_once "db.php";
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);
$user = (isset($_SESSION['user']))? $_SESSION['user'] : "";

//Requete pour le nombre d'articles total
$query = "SELECT count(id) as total_article FROM products";
$sth = $dbh->prepare($query);
$sth->execute();
$data_number = $sth->fetch(PDO::FETCH_ASSOC);
$total_article = $data_number['total_article'];

// ********** Pagination **********
$nombre = 5;
$nbPage = ceil($total_article/$nombre);
$cPage = (isset($_GET['page']) && $_GET['page']<=$nbPage && $_GET['page']>0 )? ceil($_GET['page']) : 1;

$limit = ($cPage-1)*$nombre;
$first_article_page = $limit + 1;  
$last_article_page = $limit + $nombre;

//  Requête sql d'éxtracton d'informations de la base de donnée
$sql = "SELECT id, localisation, name, reference, categorie, date, guarantee, price, maintenance, picture, manual FROM products LIMIT $limit,$nombre";
$sth = $dbh->prepare($sql);
$sth->execute();

/* Affichage et protection des valeurs extraites*/
$datas = $sth->fetchAll(PDO::FETCH_ASSOC);




$template = $twig->load('produit.html.twig');
echo $template->render([
    'liste_produits' => $datas,
    'user' => $user,
    'total_article' => $total_article,
    'nbPage' => $nbPage,
    'cPage' => $cPage,
    'first_article_page' => $first_article_page,
    'last_article_page' => $last_article_page,

]);
