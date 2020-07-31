<?php
session_start();
require_once "../db.php";

$user_name = (isset($_POST['user_name']))? trim($_POST['user_name']) : "";
$user_email = (isset($_POST['user_email']))?trim($_POST['user_email']) : "";
$user_password = (isset($_POST['user_password']))? $_POST['user_password'] : "";
$validation = false;
$erreurs[] = "Mauvais identifiants";

if ($user_name != '' && $user_email != '' ) {
    $query = "SELECT password FROM users WHERE nom = :nom AND email=:email";
    $sth = $dbh->prepare($query);
    $sth->bindParam(':nom', $user_name, PDO::PARAM_STR);
    $sth->bindParam(':email', $user_email, PDO::PARAM_STR);
    $sth->execute();
    $donnee = $sth->fetch(PDO::FETCH_ASSOC);
    if(password_verify($user_password,$donnee['password'])){
        $_SESSION['user'] = $user_name;
        $validation = true;
        $erreurs[] = "";
    }
}

echo json_encode(array('validation' => $validation,'erreurs' => $erreurs));
