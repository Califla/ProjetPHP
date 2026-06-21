<?php
session_start();
if (isset($_GET['id_competence']) && isset($_SESSION['id_user'])) {
    include("../../database/config.php");
    try {
        $stmt = $db->prepare("DELETE FROM validation_competence WHERE id_user = ? AND id_competence = ?");
        $stmt->execute([$_SESSION['id_user'], $_GET['id_competence']]);
        if ($stmt->rowCount() > 0) {
            header("Location: index.php?msg=demande_supprimee");
        } else {
            header("Location: index.php?error=suppression_failed");
        }
        exit();
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        exit();
    }
} else {
    header("Location: index.php?error=missing_params");
    exit();
}
?>