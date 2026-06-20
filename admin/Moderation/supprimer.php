<?php
include('../../database/config.php');
try{
    $req=$db->prepare("DELETE FROM aide WHERE id_demande = ?");
    $req->execute([$_GET['id']]);
    if($req == false){
        header("Location: mode.php?error=delete_failed");
        exit();
    }else{
        header("Location: mode.php?msg=delete_success");
        exit();
    }
}catch(PDOException $e){
    echo "ERREUR DE CONNEXION À LA BASE DE DONNÉES: " . $e->getMessage();
}
?>