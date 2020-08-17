<?php
session_start();
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);


//si l'action logout est récupéré on détruit la session
if(isset($_GET['action'])=='logout') {
    session_destroy();
}

$user = (isset($_SESSION['user']))? $_SESSION['user'] : "";


$template = $twig->load('index.html.twig');
echo $template->render(['user' => $user]);
