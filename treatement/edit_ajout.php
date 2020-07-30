<?php
require_once "../db.php";

// Variables vide à remplir
$validation = false;
$erreurs[]= "";
$localisation = '';
$name = '';    
$reference = '';
$categorie = '';
$date = '';
$guarantee = '';
$price = '';
$maintenance = '';
$picture = '';
$manual = '';

// Vérifie si des valeurs ont bien été rentrées
if(count($_POST) > 0 ){
    // Vérifie la valeur de la localisation
    if(strlen(trim($_POST['localisation'])) !== 0){
        $localisation = trim($_POST['localisation']);
    }else{
        $erreurs[]['localisation'] = "je sais pas quoi";
    }
    // Vérifie la valeur du nom
    if(strlen(trim($_POST['name'])) !== 0){
        $name = trim($_POST['name']);
    }else{
        $erreurs[]['name'] = "je sais pas quoi";
    }
    // Vérifie la valeur de la reference
    if(strlen(trim($_POST['reference'])) !== 0){
        $reference = trim($_POST['reference']);
    }else{
        $erreurs[]['reference'] = "je sais pas quoi";
    }
    // // Vérifie la valeur de la categorie
    // if(strlen(trim($_POST['categorie'])) !== 0){
    //     $categorie = trim($_POST['categorie']);
    // }else{
    //     $erreurs[]['categorie'] = "je sais pas quoi";
    // }
    // Vérifie la valeur de la date
    if(strlen(trim($_POST['date_achat'])) !== 0){
        $date = trim($_POST['date_achat']);
    }else{
        $erreurs[]['date_achat'] = "je sais pas quoi";
    }
    // Vérifie la valeur de la garantie
    if(strlen(trim($_POST['date_guarantee'])) !== 0){
        $guarantee = trim($_POST['date_guarantee']);
    }else{
        $erreurs[]['date_guarantee'] = "je sais pas quoi";
    }
    // Vérifie la valeur du prix
    if(strlen(trim($_POST['price'])) !== 0){
        $price = trim($_POST['price']);
    }else{
        $erreurs[]['price'] = "je sais pas quoi";
    }
    // Vérifie la valeur de la maintenance
    if(strlen(trim($_POST['maintenance'])) !== 0){
        $maintenance = trim($_POST['maintenance']);
    }else{
        $erreurs[]['maintenance'] = "je sais pas quoi";
    }
    // // Vérifie la valeur de la picture
    // if(strlen(trim($_POST['picture'])) !== 0){
    //     $picture = trim($_POST['picture']);
    // }else{
    //     $erreurs[]['picture'] = "je sais pas quoi";
    // }
    // Vérifie la valeur de l'id
    if(isset($_POST['id']) && $_POST['id'] !== "") {
        $id = htmlentities($_POST['id']);
    }
}

// Si il n'y a pas d'erreur de remplissage, vérifie si on est en état d'édition ou d'ajout et créé une requête sql
if (!isset($erreurs[1])) {
    $validation = true;
    if(isset($_POST['id']) && $_POST['id'] !== "") {
//             $sql = 'UPDATE products SET localisation=:localisation, name=:name, reference=:reference, categorie=:categorie, date=:date, guarantee=:guarantee, price=:price, maintenance=:maintenance, picture=:picture, manual=:manual WHERE id=:id';

        $sql = 'UPDATE products SET localisation=:localisation, name=:name, reference=:reference, date=:date, guarantee=:guarantee, price=:price, maintenance=:maintenance  WHERE id=:id';
    }else {
            // $sql = "INSERT INTO products(localisation,name,reference,categorie,date,guarantee,price,maintenance,picture,manual) VALUES(:localisation,:name,:reference,:categorie,:date,:guarantee,:price,:maintenance,:picture,:manual)";

        $sql = "INSERT INTO products(localisation,name,reference,date,guarantee,price,maintenance) VALUES(:localisation,:name,:reference,:date,:guarantee,:price,:maintenance)";
    }
    // Préparation, protection et execution de la requête sql
    $sth = $dbh->prepare($sql);
    //Protection des requêtes sql
    $sth->bindParam(':localisation', $localisation, PDO::PARAM_STR);
    $sth->bindParam(':name', $name, PDO::PARAM_STR);
    $sth->bindParam(':reference', $reference, PDO::PARAM_STR);
    // $sth->bindParam(':categorie', $categorie, PDO::PARAM_STR);
    $sth->bindValue(':date', strftime("%Y-%m-%d", strtotime($date)), PDO::PARAM_STR);
    $sth->bindValue(':guarantee', strftime("%Y-%m-%d", strtotime($date)), PDO::PARAM_STR);
    $sth->bindParam(':price', $price, PDO::PARAM_STR);
    $sth->bindParam(':maintenance', $maintenance, PDO::PARAM_STR);
    // $sth->bindParam(':picture', $picture, PDO::PARAM_STR);
    // $sth->bindParam(':manual', $manual, PDO::PARAM_STR);
    if(isset($_POST['id']) && $_POST['id'] !== "") {
        $sth->bindParam('id', $id, PDO::PARAM_INT);
    }
    $sth->execute();
}

echo json_encode(array('validation' => $validation,'erreurs' => $erreurs));
