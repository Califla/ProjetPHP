<?php
session_start();
if (isset($_SESSION)) {
    extract($_SESSION);
    if ($role !== 'stagiaire' && $role !== 'mentor') {
        header('Location: ../../pagelogin/connexion/index.php');
        exit();
    }
    try {
        include('../../database/config.php');
        if (isset($_POST['modifier'])) {
            extract($_POST);
            $stmt = $db->prepare("UPDATE aide SET titre = ?, description = ?, tags = ? WHERE id_demande = ? AND id_user = ?");
            $stmt->execute([$titre, $description, $tags, $id_demande, $id_user]);
            header('Location: index.php?message=Demande modifiée avec succès');
            exit();
        } else {
            header('Location: index.php?error=Aucune modification effectuée');
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        exit();
    }
}
?>
