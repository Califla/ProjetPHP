<?php
session_start();
if (isset($_SESSION["id_user"]) && $_SESSION["id_user"] !== "") {
    extract($_SESSION);
     if ($role !== 'stagiaire' && $role !== 'mentor') {
        header('Location: ../../pagelogin/connexion/index.php');
        exit();
    }
    try{
        include('../../database/config.php');
        $id_badge = $_GET['id_badge'];
        $stmt = $db->prepare("INSERT INTO obtention_badges (id_user, id_badge, date_obtention) VALUES (?, ?, NOW())");
        $stmt->execute([$id_user, $id_badge]);
        if($stmt==false){
            header("Location: index.php?error=Erreur lors de la réclamation du badge.");
        }else{
            #ajouter 50 point qand l'utilsateru prend un nouveaux badghe 
            $stmt = $db->prepare("UPDATE utilisateurs SET score = score + 50 WHERE id_user = ?");
            $stmt->execute([$id_user]);
            header("Location: index.php?msg=Badge réclamé avec succès ! Vous avez gagné 50 points.");
        }
        exit();
        }catch(Exception $e){
            header("Location: index.php?error=Erreur : " . $e->getMessage());
            exit();
        }
}
?>