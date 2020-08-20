<?php
session_start();
require_once "../db.php";

// initialisation/récupération des variables
$flag=false;
$validation = false;
$erreurs[]= "";
$path_ticket = "";
$extensionUpload_ticket = "";
$extensionUpload_manual = "";


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
$ticket = (isset($_POST['ticket']) ? trim($_POST['ticket']):"");
$manual = (isset($_POST['manual']) ? trim($_POST['manual']):"");

/* **************
Début des tests 
************** */
//nom
if (strlen($name) < 3 || strlen($name) > 45) {
    $erreurs[]["name"] = "Entre 3 et 45 caractères autorisé";
}

//référence
if(!preg_match("/^[\p{L}\p{N}-]*$/u",$reference)) {
    $erreurs[]["reference"] = "Référence incorrecte";
}elseif (strlen($reference) !== 6){
    $erreurs[]["reference"] = "Taille non autorisée (6 caractères)";
}else{
    //teste si la référence est déjà présente dans la BDD en omettant sa ref de base en edition
    if(isset($_POST['id']) && $_POST['id'] !== "") {
        $flag = true;
        $sth = $dbh->prepare("SELECT reference FROM products WHERE `reference`= :ref AND NOT `id`= :id");
        $sth->bindParam(":id", $_POST['id'], PDO::PARAM_STR);
    }else {
        $sth = $dbh->prepare("SELECT reference FROM products WHERE `reference`= :ref");
    }
    $sth->bindParam(":ref", $reference, PDO::PARAM_STR);
    $sth->execute();
    if ($sth->fetch(PDO::FETCH_ASSOC)) {
        $erreurs[]["reference"] = "Référence déjà existante";
    }
}

//localisation
if($localisation == "") {
    $erreurs[]['localisation'] = "Localisation incorrecte";
}

//adresse
if(strlen($adresse) > 45) {
    $erreurs[]['adresse'] = "45 caractères maximum : ".strlen($adresse)." actuel(s)";
}

//catégorie
if(($categorie == -1 && ( $new_categorie =="" )) || ($categorie==0)){
    $erreurs[]["categorie"] = "Catégorie incorrecte";
}else{
    //teste si la catégorie est déjà présente dans la BDD
    $sth = $dbh->prepare("SELECT id FROM categorie WHERE `nom`= :name");
    $sth->bindParam(":name", $new_categorie, PDO::PARAM_STR);
    $sth->execute();
    if ($sth->fetch(PDO::FETCH_ASSOC)) {
        $erreurs[]["categorie"] = "Catégorie déjà existante";
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
if(!preg_match("/^([0-9]{1,3}){1}(\.[0-9]{1,2})?$/",$price)) {
    $erreurs[]["price"] = "Prix incorrect";
}elseif ($price == 0) {
    $erreurs[]["price"] = "Entre 0 et 1000€ non inclu";
}

//maintenance
if(strlen($maintenance) < 5 || strlen($maintenance) > 255){ 
    $erreurs[]['maintenance'] = "Entre 5 et 255 caractères seulement : ".strlen($maintenance)." actuel";
}

//ticket d'achat
if(isset($_FILES['ticket']) && !empty($_FILES['ticket']['name'])){
    $maxsize = 2097152;
    $extensions = array('jpg', 'jpeg', 'png');
    if($_FILES['ticket']['size'] <= $maxsize){
        $extensionUpload_ticket = strtolower(substr(strrchr($_FILES['ticket']['name'], '.'), 1));
        if(!in_array($extensionUpload_ticket, $extensions)){ //création des fichiers plus bas (avant insertion sql)
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
if(isset($_FILES['manual']) && !empty($_FILES['manual']['name'])){
    $maxsize = 20971520;
    $extensions = array('pdf');
    if($_FILES['manual']['size'] <= $maxsize){
        $extensionUpload_manual = strtolower(substr(strrchr($_FILES['manual']['name'], '.'), 1));
        if(!in_array($extensionUpload_manual, $extensions)){//création des fichiers plus bas (avant insertion sql)
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
    if(move_uploaded_file($_FILES['ticket']['tmp_name'], "../medias/ticket_achat/$reference.$extensionUpload_ticket")){
        $ticket = "$reference.$extensionUpload_ticket";
    }
    if(move_uploaded_file($_FILES['manual']['tmp_name'], "../medias/manual/$reference.$extensionUpload_manual")){
        $manual = "$reference.$extensionUpload_manual";
    }

    //si on a changé la ref du produit on rename les médias
    if($flag===true) {
        $sth = $dbh->prepare("SELECT ticket,manual FROM products WHERE id=:id");
        $sth->bindParam(':id', $_POST['id'], PDO::PARAM_STR);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        $ticket = $data['ticket'];
        $manual = $data['manual'];
        if ($ticket!="") {
            rename("../medias/ticket_achat/$ticket","../medias/ticket_achat/$reference.".explode(".",$ticket)[1]);
            $ticket = "$reference.".explode(".",$ticket)[1];
        }
        if ($manual!="") {
            rename("../medias/manual/$manual","../medias/manual/$reference.".explode(".",$manual)[1]);
            $manual = "$reference.".explode(".",$manual)[1];
        }
    }
    //si une nouvelle catégorie est crée, on l'insere dans la BDD
    if ($new_categorie != ""){
        $sth = $dbh->prepare("INSERT INTO categorie(nom) VALUES (:nom)");
        $sth->bindParam(':nom', $new_categorie, PDO::PARAM_STR);
        $sth->execute();
        $categorie = $dbh->lastInsertId(); //on récupère l'id de la nouvelle catégorie
    }
    if(isset($_POST['id']) && $_POST['id'] !== "") {
        $sql = 'UPDATE products SET categorie=:categorie,ticket=:ticket,manual=:manual,localisation=:localisation, name=:name, reference=:reference,adresse=:adresse, date=:date, guarantee=:guarantee, price=:price, maintenance=:maintenance  WHERE id=:id';
    }else {
        $sql = "INSERT INTO products(categorie,localisation,name,reference,adresse,date,guarantee,price,maintenance,ticket,manual) VALUES(:categorie,:localisation,:name,:reference,:adresse,:date,:guarantee,:price,:maintenance,:ticket,:manual)";
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
    $sth->bindValue(':guarantee', strftime("%Y-%m-%d", strtotime($guarantee)), PDO::PARAM_STR);
    $sth->bindParam(':price', $price, PDO::PARAM_STR);
    $sth->bindParam(':maintenance', $maintenance, PDO::PARAM_STR);
    $sth->bindParam(':ticket', $ticket, PDO::PARAM_STR);
    $sth->bindParam(':manual', $manual, PDO::PARAM_STR);
    if(isset($_POST['id']) && $_POST['id'] !== "") {
        $sth->bindParam('id', $id, PDO::PARAM_INT);
    }

    $sth->execute();

}

echo json_encode(array('validation' => $validation,'erreurs' => $erreurs));