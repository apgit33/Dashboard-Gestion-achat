<?php
session_start();
require_once "../db.php";

// initialisation/récupération des variables
$validation = false;
$erreurs[]= "";
$ticket = "";
$manual = "";
$valide_ticket = false;
$valide_manual = false;
$path_ticket = "";

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

/* **************
Début des tests 
************** */
//nom
if(!preg_match("/^[\p{L}-]*$/u",$name)) {
    $erreurs[]["name"] = "Nom incorrect";
}

//référence
if(!preg_match("/^[\p{L}\p{N}_.-]*$/u",$reference)) {
    $erreurs[]["reference"] = "référence incorrect";
}else{
    //teste si la référence est déjà présente dans la BDD
    $sth = $dbh->prepare("SELECT id FROM products WHERE `reference`= :ref");
    $sth->bindParam(":ref", $reference, PDO::PARAM_STR);
    $sth->execute();
    if ($sth->fetch(PDO::FETCH_ASSOC)) {
        $erreurs[]["reference"] = "référence déjà existante";
    }
}

//localisation
if($localisation == "") {
    $erreurs[]['localisation'] = "localisation non renseignée";
}

//catégorie
if(($categorie == -1 && ( $new_categorie =="" || !preg_match("/^[\p{L}\p{N}_.-]*$/u",$new_categorie))) || ($categorie==0)){
    $erreurs[]["categorie"] = "catégorie incorrect";
}else{
    //teste si la catégorie est déjà présente dans la BDD
    $sth = $dbh->prepare("SELECT id FROM categorie WHERE `nom`= :name");
    $sth->bindParam(":name", $new_categorie, PDO::PARAM_STR);
    $sth->execute();
    if ($sth->fetch(PDO::FETCH_ASSOC)) {
        $erreurs[]["categorie"] = "catégorie déjà existante";
    }
}

//date d'achat
if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$date) || $date == "") {
    $erreurs[]["date"] = "Date d'achat invalide";
}

//date de garentie
if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$guarantee) || $guarantee == "") {
    $erreurs[]["guarantee"] = "Date de garentie invalide";
}elseif ($guarantee <= $date) {
    $erreurs[]["guarantee"] = "Date de garentie inférieure à la date d'achat";
}

//prix
if(!preg_match("/^-?(?:\d+|\d*\.\d+)$/",$price)) {
    $erreurs[]["price"] = "Prix incorrect";
}

//maintenance
if(strlen(trim($maintenance)) < 5 || strlen(trim($maintenance)) >= 255){ 
    $erreurs[]['maintenance'] = "Entre 5 et 255 caractères seulement : ".strlen(trim($maintenance))." actuellement";
}

//ticket d'achat
if(isset($_FILES['ticket_file']) && !empty($_FILES['ticket_file']['name'])){
    $maxsize = 2097152;
    $extensions = array('jpg', 'jpeg', 'png');
    if($_FILES['ticket_file']['size'] <= $maxsize){
        $extensionUpload = strtolower(substr(strrchr($_FILES['ticket_file']['name'], '.'), 1));
        if(in_array($extensionUpload, $extensions)){
            $path_ticket = "../medias/ticket_achat/$reference.$extensionUpload";
            $valide_ticket = true; //création des fichiers ligne 121
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

//manuel
if(isset($_FILES['manual_file']) && !empty($_FILES['manual_file']['name'])){
    $maxsize = 20971520;
    $extensions = array('pdf');
    if($_FILES['manual_file']['size'] <= $maxsize){
        $extensionUpload = strtolower(substr(strrchr($_FILES['manual_file']['name'], '.'), 1));
        if(in_array($extensionUpload, $extensions)){
            $path_manual = "../medias/manual/$reference.$extensionUpload";
            $valide_manual = true; //création des fichiers ligne 124
        }else{
            $erreurs[]['manual'] = "Le fichier n'est pas au bon format!";
        }
    }
    else{
        $erreurs[]['manual'] = "Votre fichier dépasse 20mo!";
    }
}

// Vérifie la valeur de l'id
if(isset($_POST['id']) && $_POST['id'] !== "") {
    $id = htmlentities($_POST['id']);
}

/* **************
Fin des tests 
************** */

// Si il n'y a pas d'erreur de remplissage, vérifie si on est en état d'édition ou d'ajout et créé une requête sql
if (!isset($erreurs[1])) {
    $validation = true;

    //on crée les fichiers ici pour éviter d'en créer un à chaque erreur
    if(move_uploaded_file($_FILES['ticket_file']['tmp_name'], $path_ticket)){
        $ticket = "$reference.$extensionUpload";
    }
    if(move_uploaded_file($_FILES['manual_file']['tmp_name'], $path_manual)){
        $manual = "$reference.$extensionUpload";
    }

    //si une nouvelle catégorie est crée, on l'insere dans la BDD
    if ($new_categorie != ""){
        $sth = $dbh->prepare("INSERT INTO categorie(nom) VALUES (:nom)");
        $sth->bindParam(':nom', $new_categorie, PDO::PARAM_STR);
        $sth->execute();
        $categorie = $dbh->lastInsertId(); //on récupère l'id de la nouvelle catégorie
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
    $sth->bindParam(':manual', $manual, PDO::PARAM_STR);
    if(isset($_POST['id']) && $_POST['id'] !== "") {
        $sth->bindParam('id', $id, PDO::PARAM_INT);
    }
    $sth->execute();
}

echo json_encode(array('validation' => $validation,'erreurs' => $erreurs));