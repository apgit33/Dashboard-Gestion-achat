<?php
//Identifiants de connexion à la databse wamp
define('DATABASE', 'gestion_achat');
define('USER', 'root');
define('PWD', '');
define('HOST', 'localhost');
//requête sql de connexion
    try{
        $dbh = new PDO('mysql:host='.HOST.';dbname='.DATABASE, USER, PWD, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    } catch (PDOException $e) {
        print "Erreur !:" . $e->getMessage() . "<br/>";
        die();
    }