<?php
include('../../database/config.php');
try{
    $req=$db->prepare("UPDATE aide SET `signal` = 0 WHERE id_demande = ?");
    $req->execute([$_GET['id']]);
    if($req == false){
        header("Location: mode.php?error=keep_failed");
        exit();
    }else{
        header("Location: mode.php?msg=keep_success");
        exit();
    }
}catch(PDOException $e){
    echo "ERREUR DE CONNEXION À LA BASE DE DONNÉES: " . $e->getMessage();
}
?>