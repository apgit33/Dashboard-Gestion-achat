<?php
 //Ouverture de session et vérification de l'utilisateur
 session_start();
 require_once('db.php');
 
 if(empty($_SESSION['user'])){
     header('Location: index.php');
 }    
// Vérifie si il peut récupérer un id
    if (isset( $_GET['id'])){
        // Requête sql de supression
        $sql = 'DELETE FROM products WHERE id=:id';
        $sth = $dbh->prepare($sql);
        $sth->bindParam(':id', $_GET['id'], PDO::PARAM_INT);
        $sth->execute();

    }
    // Redirection vers le header
    header('Location: index.php');

    ?>