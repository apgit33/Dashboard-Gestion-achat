<?php

require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

/*

Début Code PHP

*/



/*

Fin Code PHP

*/
$template = $twig->load('index.html.twig');
echo $template->render();