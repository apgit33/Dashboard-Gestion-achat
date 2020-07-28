<?php
// Ouverture de session
    session_start();
    require_once('db.php');
    
    if(empty($_SESSION['utilisateur'])){
        header('Location: login.php');
    }    

    //Vérifie si on est en état d'ajout ou d'édition
    if( isset($_GET['id']) && isset($_GET['edit'])){
            $titre='Modifier des informations';
        }else{
            $titre='Ajouter une achat';
        }

    ?>
<?php
// Variables vide à remplir
$id = '';
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
$error = false;

// Requête sql pour éxtraire les valrus du tableau si on est en état d'édition
    if(isset($_GET['id']) && isset($_GET['edit'])){
        $sql = 'SELECT id, localisation, name, reference, categorie, date, guarantee, price, maintenance, picture, manual FROM products WHERE id=:id';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $sth->execute();
        $data = $sth->fetch(PDO::FETCH_ASSOC);
        if( gettype($data) === "boolean"){            
            header('Location: index.php');
            exit;
        }

    // Remplissage des variables vides
    $localisation = $data['localisation'];
    $name = $data['name'];    
    $reference = $data['reference'];
    $categorie = $data['categorie'];
    $date = $data['date'];
    $guarantee = $data['guarantee'];
    $price = $data['price'];
    $maintenance = $data['maintenance'];
    $picture = $data['picture'];
    $manual = $data['manual'];;
    $id = htmlentities($_GET['id']);

    }

    // Vérifie si des valeurs ont bien été rentrées
    if(count($_POST) > 0 ){
        // Vérifie la valeur de la localisation
        if(strlen(trim($_POST['localisation'])) !== 0){
            $date = trim($_POST['localisation']);
        }else{
            $error = true;
        }
        // Vérifie la valeur du nom
        if(strlen(trim($_POST['name'])) !== 0){
            $etage = trim($_POST['name']);
        }else{
            $error = true;
        }
        // Vérifie la valeur de la reference
        if(strlen(trim($_POST['reference'])) !== 0){
            $position = trim($_POST['reference']);
        }else{
            $error = true;
        }
        // Vérifie la valeur de la categorie
        if(strlen(trim($_POST['categorie'])) !== 0){
            $puissance = trim($_POST['categorie']);
        }else{
            $error = true;
        }
        // Vérifie la valeur de la date
        if(strlen(trim($_POST['date'])) !== 0){
            $marque = trim($_POST['date']);
        }else{
            $error = true;
        }
        // Vérifie la valeur de la garantie
        if(strlen(trim($_POST['guarantee'])) !== 0){
            $marque = trim($_POST['guarantee']);
        }else{
            $error = true;
        }
        // Vérifie la valeur du prix
        if(strlen(trim($_POST['price'])) !== 0){
            $marque = trim($_POST['price']);
        }else{
            $error = true;
        }
        // Vérifie la valeur de la maintenance
        if(strlen(trim($_POST['maintenance'])) !== 0){
            $marque = trim($_POST['maintenance']);
        }else{
            $error = true;
        }
        // Vérifie la valeur de la picture
        if(strlen(trim($_POST['picture'])) !== 0){
            $marque = trim($_POST['picture']);
        }else{
            $error = true;
        }
        // Vérifie la valeur de l'id
        if(isset($_POST['edit']) && isset($_POST['id'])){
            $id = htmlentities($_POST['id']);
        }

// Si il n'y a pas d'erreur de remplissage, vérifie si on est en état d'édition ou d'ajout et créé une requête sql
    if($error === false){
        if(isset($_POST['edit']) && isset($_POST['id'])){
            $sql = 'UPDATE products SET localisation=:localisation, name=:name, reference=:reference, categorie=:categorie, date=:date, guarantee=:guarantee, price=:price, maintenance=:maintenance, picture=:picture, manual=:manual WHERE id=:id';
        }else{
            $sql = "INSERT INTO products(localisation,name,reference,categorie,date,guarantee,price,maintenance,picture,manual) VALUES(:localisation,:name,:reference,:categorie,:date,:guarantee,:price,:maintenance,:picture,:manual)";
        }
    
// Préparation, protection et execution de la requête sql
        $sth = $dbh->prepare($sql);
            //Protection des requêtes sql
        $sth->bindParam(':localisation', $localisation, PDO::PARAM_STR);
        $sth->bindParam(':name', $name, PDO::PARAM_STR);
        $sth->bindParam(':reference', $reference, PDO::PARAM_STR);
        $sth->bindParam(':categorie', $categorie, PDO::PARAM_STR);
        $sth->bindValue(':date', strftime("%Y-%m-%d", strtotime($date)), PDO::PARAM_STR);
        $sth->bindValue(':guarantee', strftime("%Y-%m-%d", strtotime($date)), PDO::PARAM_STR);
        $sth->bindParam(':price', $price, PDO::PARAM_STR);
        $sth->bindParam(':maintenance', $maintenance, PDO::PARAM_STR);
        $sth->bindParam(':picture', $picture, PDO::PARAM_STR);
        $sth->bindParam(':manual', $manual, PDO::PARAM_STR);
        if(isset($_POST['edit']) && isset($_POST['id'])){
            $sth->bindParam('id', $id, PDO::PARAM_INT);
        }
        $sth->execute();
        // Après l'exécution, redirection vers l'index
        header('Location: index.php');
    }
}
    ?>
