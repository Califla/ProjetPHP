<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    extract($_POST);
    include("../../database/config.php");
    try{
        #verifier si le stagiaire a deja le badge
        $stmt = $db->prepare("SELECT * FROM obtention_badges WHERE id_user = ? AND id_badge = ?");
        $stmt->execute([ $id_user, $id_badge]);
        if ($stmt->rowCount() > 0) {
            header("Location: badge.php?error=Le stagiaire a déjà ce badge.");
            exit();
        }
        #attribuer le badge
        $stmt = $db->prepare("INSERT INTO obtention_badges (id_user, id_badge, date_obtention) VALUES (?, ?, NOW())");
        $stmt->execute([ $id_user, $id_badge]);
        if ($stmt->rowCount() > 0) {
            header("Location: badge.php?msg=Badge attribué avec succès.");
            exit();
        } else {
            header("Location: badge.php?error=Erreur lors de l'attribution du badge.");
            exit();
        }
    }catch(PDOException $e){
        echo "Erreur: " . $e->getMessage();
        exit();
    }
}
?>