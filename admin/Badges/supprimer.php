<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
}
include("../../database/config.php");
if(isset($_GET["id"])){
    try{
        $req=$db->prepare("DELETE FROM badges WHERE id_badge=?");
        $r=$req->execute([$_GET["id"]]);
        if($r==false){
            header("Location: badges.php?error=Suppression a echoue");
        }else{
            header("Location: badges.php?msg=Suppression a reussi");
        }
    }catch(PDOException $e){
        echo "Erreur: ".$e->getMessage();
    }
}
?>