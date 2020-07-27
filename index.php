<?php

require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

/*

Début Code PHP

*/

$nom = 'Jean';

/*

Fin Code PHP

*/
$template = $twig->load('base.html.twig');
echo $template->render(['user_name' => $nom]);