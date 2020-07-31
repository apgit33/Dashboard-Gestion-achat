<?php
// Ouverture de session
session_start();

require_once "db.php";
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

    // if(empty($_SESSION['utilisateur'])){
    //     header('Location: index.php');
    // }    

    //Vérifie si on est en état d'ajout ou d'édition
    if( isset($_GET['id']) && isset($_GET['edit'])){
            $titre='Modification du produit';
        }else{
            $titre='Ajouter un produit';
        }


// Variables vide à remplir
$data="";
// $id = '';
// $localisation = '';
// $name = '';    
// $reference = '';
// $categorie = '';
// $date = '';
// $guarantee = '';
// $price = '';
// $maintenance = '';
// $picture = '';
// $manual = '';
// $error = false;

// Requête sql pour éxtraire les valeurs du tableau si on est en état d'édition
    if(isset($_GET['id']) && isset($_GET['edit'])){
        $sql = 'SELECT id, localisation, name, reference, categorie, date, guarantee, price, maintenance, picture, manual FROM products WHERE id=:id';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
//         if( gettype($data) === "boolean"){            
//             header('Location: index.php');
//             exit;
//         }

//         // Remplissage des variables vides
//         $localisation = $data['localisation'];
//         $name = $data['name'];    
//         $reference = $data['reference'];
//         $categorie = $data['categorie'];
//         $date = $data['date'];
//         $guarantee = $data['guarantee'];
//         $price = $data['price'];
//         $maintenance = $data['maintenance'];
//         $picture = $data['picture'];
//         $manual = $data['manual'];
//         $id = htmlentities($_GET['id']);

    }

// Si il n'y a pas d'erreur de remplissage, vérifie si on est en état d'édition ou d'ajout et créé une requête sql
//     if($error === false){
//         if(isset($_POST['edit']) && isset($_POST['id'])){
//             $sql = 'UPDATE products SET localisation=:localisation, name=:name, reference=:reference, categorie=:categorie, date=:date, guarantee=:guarantee, price=:price, maintenance=:maintenance, picture=:picture, manual=:manual WHERE id=:id';
//         }else{
//             $sql = "INSERT INTO products(localisation,name,reference,categorie,date,guarantee,price,maintenance,picture,manual) VALUES(:localisation,:name,:reference,:categorie,:date,:guarantee,:price,:maintenance,:picture,:manual)";
//         }
    
// // Préparation, protection et execution de la requête sql
//         $sth = $dbh->prepare($sql);
//             //Protection des requêtes sql
//         $sth->bindParam(':localisation', $localisation, PDO::PARAM_STR);
//         $sth->bindParam(':name', $name, PDO::PARAM_STR);
//         $sth->bindParam(':reference', $reference, PDO::PARAM_STR);
//         $sth->bindParam(':categorie', $categorie, PDO::PARAM_STR);
//         $sth->bindValue(':date', strftime("%Y-%m-%d", strtotime($date)), PDO::PARAM_STR);
//         $sth->bindValue(':guarantee', strftime("%Y-%m-%d", strtotime($date)), PDO::PARAM_STR);
//         $sth->bindParam(':price', $price, PDO::PARAM_STR);
//         $sth->bindParam(':maintenance', $maintenance, PDO::PARAM_STR);
//         $sth->bindParam(':picture', $picture, PDO::PARAM_STR);
//         $sth->bindParam(':manual', $manual, PDO::PARAM_STR);
//         if(isset($_POST['edit']) && isset($_POST['id'])){
//             $sth->bindParam('id', $id, PDO::PARAM_INT);
//         }
//         $sth->execute();
//         // // Après l'exécution, redirection vers l'index
//         // header('Location: index.php');
//     }
// }



$template = $twig->load('edit.html.twig');
echo $template->render([
    'titre' => $titre,
    'produit' => $data
]);