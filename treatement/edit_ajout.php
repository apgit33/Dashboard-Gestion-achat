<?php
session_start();
require_once "../db.php";

// Variables vide à remplir
$validation = false;
$erreurs[]= "";

$ticket = '';
$manual = '';

$name = (isset($_POST['name']) ? trim($_POST['name']):"");
$reference = (isset($_POST['reference']) ? trim($_POST['reference']):"");
$categorie = (isset($_POST['categorie']) ? trim($_POST['categorie']):"");
$localisation = (isset($_POST['localisation']) ? trim($_POST['localisation']):"");
$adresse = (isset($_POST['adresse']) ? trim($_POST['adresse']):"");
$date = (isset($_POST['date_achat']) ? trim($_POST['date_achat']):"");
$guarantee = (isset($_POST['date_guarantee']) ? trim($_POST['date_guarantee']):"");
$price = (isset($_POST['price']) ? trim($_POST['price']):"");
$maintenance = (isset($_POST['maintenance']) ? trim($_POST['maintenance']):"");

$new_categorie = (isset($_POST['new_categorie']) ? trim($_POST['new_categorie']):"");

if(($categorie == -1 && ( $new_categorie =="" || !preg_match('^[A-Za-z]+((\s)?((\'|\-|\.)?([A-Za-z])+))*$^',$new_categorie))) || ($categorie==0)){
    $erreurs[]["categorie"] = "categorie incorrect";
}
if(!preg_match('^[A-Za-z]+((\s)?((\'|\-|\.)?([A-Za-z])+))*$^',$name)) {
    $erreurs[]["name"] = "Nom incorrect";
}
if(!preg_match('^[A-Za-z0-9]$^',$reference)) {
    $erreurs[]["reference"] = "reference incorrect";
}
if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date) || $date == "") {
    $erreurs[]["date"] = "Date d'achat invalide";
}
if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date)) {
    $erreurs[]["guarantee"] = "Date de garentie invalide";
}
if(!preg_match("/^-?(?:\d+|\d*\.\d+)$/",$price)) {
    $erreurs[]["price"] = "Prix incorrect";
}
if($localisation == "") {
    $erreurs[]['localisation'] = "localisation non renseignée";
} 
    
if(strlen(trim($_POST['maintenance'])) == 0){
    $erreurs[]['maintenance'] = "Champ vide";
}


// Vérifie la valeur de l'id
if(isset($_POST['id']) && $_POST['id'] !== "") {
    $id = htmlentities($_POST['id']);
}

if(isset($_FILES['ticket_file']) && !empty($_FILES['ticket_file']['name'])){
    $maxsize = 2097152;
    $extensions = array('jpg', 'jpeg', 'png');
    if($_FILES['ticket_file']['size'] <= $maxsize){
        $extensionUpload = strtolower(substr(strrchr($_FILES['ticket_file']['name'], '.'), 1));
        if(in_array($extensionUpload, $extensions)){
            $path = "../medias/ticket_achat/".$_SESSION['user']."_".$_FILES['ticket_file']['name'];
            $result = move_uploaded_file($_FILES['ticket_file']['tmp_name'], $path);
            if($result){
                $ticket = $_SESSION['user']."_".$_FILES['ticket_file']['name'];
            }else{
                $erreurs[]['ticket'] = "Une erreur a eu lieu";
            }
        }else{
            $erreurs[]['ticket'] = "L'image n'est pas au bon format!";
        }
    }
    else{
        $erreurs[]['ticket'] = "Votre image dépasse 2mo!";
    }
    
} elseif (!isset($_POST['edit_prod'])) {
    $erreurs[]['ticket'] = "Fichier non renseigné";
}

if(isset($_FILES['manual_file']) && !empty($_FILES['manual_file']['name'])){
    $maxsize = 20971520;
    $extensions = array('pdf');
    if($_FILES['manual_file']['size'] <= $maxsize){
        $extensionUpload = strtolower(substr(strrchr($_FILES['manual_file']['name'], '.'), 1));
        if(in_array($extensionUpload, $extensions)){
            $path = "../medias/manual/".$_SESSION['user']."_".$_FILES['manual_file']['name'];
            $result = move_uploaded_file($_FILES['manual_file']['tmp_name'], $path);
            if($result){
                $manual = $_SESSION['user']."_".$_FILES['manual_file']['name'];
            }else{
                $erreurs[]['manual'] = "Une erreur a eu lieu";
            }
        }else{
            $erreurs[]['manual'] = "Le fichier n'est pas au bon format!";
        }
    }
    else{
        $erreurs[]['manual'] = "Votre fichier dépasse 20mo!";
    }
} elseif (!isset($_POST['edit_prod'])) {
    $erreurs[]['manual'] = "Fichier non renseigné";
}

// Si il n'y a pas d'erreur de remplissage, vérifie si on est en état d'édition ou d'ajout et créé une requête sql
if (!isset($erreurs[1])) {
    $validation = true;

    if ($new_categorie != ""){
        $sth = $dbh->prepare("INSERT INTO categorie(nom) VALUES (:nom)");
        $sth->bindParam(':nom', $new_categorie, PDO::PARAM_STR);
        $sth->execute();
        $categorie = $dbh->lastInsertId();
    }

    if(isset($_POST['id']) && $_POST['id'] !== "") {
        if (($ticket =="") && ($manual =="")){
            $sql = 'UPDATE products SET categorie=:categorie,localisation=:localisation, name=:name, reference=:reference,adresse=:adresse, date=:date, guarantee=:guarantee, price=:price, maintenance=:maintenance  WHERE id=:id';
        }elseif($ticket =="") {
            $sql = 'UPDATE products SET categorie=:categorie,manual=:manual, localisation=:localisation, name=:name, reference=:reference,adresse=:adresse, date=:date, guarantee=:guarantee, price=:price, maintenance=:maintenance  WHERE id=:id';
        }elseif($manual==""){
            $sql = 'UPDATE products SET categorie=:categorie,picture=:picture, localisation=:localisation, name=:name, reference=:reference,adresse=:adresse, date=:date, guarantee=:guarantee, price=:price, maintenance=:maintenance  WHERE id=:id';
        }else{
            $sql = 'UPDATE products SET categorie=:categorie,picture=:picture,manual=:manual,localisation=:localisation, name=:name, reference=:reference,adresse=:adresse, date=:date, guarantee=:guarantee, price=:price, maintenance=:maintenance  WHERE id=:id';
        }
    }else {
        $sql = "INSERT INTO products(categorie,localisation,name,reference,adresse,date,guarantee,price,maintenance,picture,manual) VALUES(:categorie,:localisation,:name,:reference,:adresse,:date,:guarantee,:price,:maintenance,:picture,:manual)";
    }
    // Préparation, protection et execution de la requête sql
    $sth = $dbh->prepare($sql);
    //Protection des requêtes sql
    $sth->bindParam(':categorie', $categorie, PDO::PARAM_STR);
    $sth->bindParam(':localisation', $localisation, PDO::PARAM_STR);
    $sth->bindParam(':name', $name, PDO::PARAM_STR);
    $sth->bindParam(':reference', $reference, PDO::PARAM_STR);
    $sth->bindParam(':adresse', $adresse, PDO::PARAM_STR);
    $sth->bindValue(':date', strftime("%Y-%m-%d", strtotime($date)), PDO::PARAM_STR);
    $sth->bindValue(':guarantee', strftime("%Y-%m-%d", strtotime($date)), PDO::PARAM_STR);
    $sth->bindParam(':price', $price, PDO::PARAM_STR);
    $sth->bindParam(':maintenance', $maintenance, PDO::PARAM_STR);
    if ($ticket !=""){$sth->bindParam(':picture', $ticket, PDO::PARAM_STR);}
    if ($manual !=""){$sth->bindParam(':manual', $manual, PDO::PARAM_STR);}
    if(isset($_POST['id']) && $_POST['id'] !== "") {
        $sth->bindParam('id', $id, PDO::PARAM_INT);
    }
    $sth->execute();
}

echo json_encode(array('validation' => $validation,'erreurs' => $erreurs));