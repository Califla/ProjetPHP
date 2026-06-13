<?php
session_start();
if (isset($_SESSION) && !empty($_SESSION)){
    #supprimer les variables stockes
    session_unset() ;
    #detruie la session
    session_destroy();
    #vider le cache et nettoyer les donnes supprimer
    session_gc();
    #redirection vers laa page de connexion
    header("location:../connexion/index.php");
}
?>