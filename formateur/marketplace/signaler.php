<?php
session_start();
include("../../database/config.php");

if(!isset($_GET["id"])){
    header("Location: marketplace.php?error=missing_id");
    exit();
}else{
    try{
        $req = $db->prepare("UPDATE aide SET signal = ? WHERE id_demande = ?");
        $req->execute([1, $_GET["id"]]);
        if ($_SESSION['role'] !== 'formateur'){
        if($req==false){
            header("Location: marketplace.php?error=signal_failed");
            exit();
        }else{
        header("Location: marketplace.php?msg=signal_success");
        exit();
        }
        }else if ($_SESSION['role'] !== 'admin'){
            if($req==false){
            header('Location:../../admin/Marketplace/market.php');
            exit();
        }else{
            header('Location:../../formateur/marketplace/marketplace.php');
            exit();
        }
        }else if($_SESSION['role'] === 'admin'){
            if($req==false){
            header('Location:../../admin/Marketplace/market.php');
            exit();
            }else{
            header('Location:../../admin/Marketplace/market.php');
        }
    }
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        exit();
    }
}

?>