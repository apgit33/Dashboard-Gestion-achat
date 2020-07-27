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
?>
<section>
<div class="">
    <div class="">
        <!-- Lien vers l'ajout d'une achat -->
        <a href="edit.php" title="Ajouter une achat" type="submit" class=""></a>       
    </div>
</div>

<!-- Création du tableau -->
<table class="">
    <thead class="">
        <tr>
            <th>Lieu d'achat</th>
            <th>Produit</th>
            <th>Référence</th>
            <th>Catégorie</th>
            <th>Date d'achat</th>
            <th>Fin de garantie</th>
            <th>Prix</th>
            <th>Conseils d'entretiens</th>
            <th>Ticket d'achat</th>
            <th>Manuel</th>
        </tr>
    </thead>

<!-- Requête sql d'éxtracton d'informations de la base de donnée-->    
<?php
    $sql = '';// Je préparerai la valeur sql plus tard
    $sth = $dbh->prepare($sql);
    $sth->execute();
/* Affichage et protection des valeurs extraites*/
$datas = $sth->fetchAll(PDO::FETCH_ASSOC);

/* Date mise au format français*/
$intlDateFormatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::SHORT, IntlDateFormatter::NONE);

    /* On affiche les $data pour chaque valeur extraites */
    foreach( $datas as $data){
        echo'<tbody>';
            echo'<td>'.$data['id'].'</td>';
            echo'<td>'.$data['localisation'].'</td>';
            echo'<td>'.$data['name'].'</td>';
            echo'<td>'.$data['reference'].'</td>';
            echo'<td>'.$data['categorie'].'</td>';
            echo'<td>'.$intlDateFormatter->format(strtotime($data['date'])).'</td>';
            echo'<td>'.$intlDateFormatter->format(strtotime($data['guarantee'])).'</td>';
            echo'<td>'.$data['price'].'</td>';
            echo'<td>'.$data['picture'].'</td>'; //J'ai mis ça pour le moment, il faudrait des liens avec "target="_blank" qui afficheraient les documents de la base de donnée
            echo'<td>'.$data['manual'].'</td>'; //J'ai mis ça pour le moment, il faudrait des liens avec "target="_blank" qui afficheraient les documents de la base de donnée
            echo'<td><a class="" title="Modifier" href="edit.php?edit=1&id='.$data['id'].'"></a> <a class="" title="Supprimer" ('.$data['id'].')" href="delete.php?id='.$data['id'].'"></a></td>';
        echo'</tbody>';
    }
    
//}
    ?>
</table>
<?php        
/* Message à afficher su le tableau est vide */
    if(count($datas) === 0){
        echo'<p class="> Ancun Produit </p>';
    }
?> 
<!-- Modal de supression de ligne-->
<div id="modal">
    <div class="">
        <h1 class="">Suppression</h1>
        <p>Êtes-vous sur de vouloir supprimer cette ligne?</p>
        <p class="">
            <button class="" type="button">Oui</button>
            <button class="" type="button">Non</button>
        </p>
    </div>
</div>
</section>
<section class="">
    <div class="">
        <!-- Lien vers l'ajout d'un achat -->
        <a href="edit.php" title="Ajouter un achat" type="submit" class=""></a>       
    </div>
</section>
/*

Fin Code PHP

*/
$template = $twig->load('base.html.twig');
echo $template->render(['user_name' => $nom]);