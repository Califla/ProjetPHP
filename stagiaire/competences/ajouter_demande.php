<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['declarer'])) {
        include("../../database/config.php");
        try {
            #verifie que l'utilisateur n'a pas déjà fait une demande pour cette compétence
            $stmt = $db->prepare("SELECT * FROM validation_competence WHERE id_user = ? AND id_competence = ?");
            $stmt->execute([$_SESSION['id_user'], $_POST['id_competence']]);
            if ($stmt->rowCount() > 0) {
                header("Location: index.php?error=demande_existe_deja");
                exit();
            } else {
                $req = $db->prepare("INSERT INTO validation_competence (id_user, id_competence, niveau, justification) VALUES (?, ?, ?, ?)");
                $req->execute([$_SESSION['id_user'], $_POST['id_competence'], $_POST['niveau'], $_POST['justification']]);
                header("Location: index.php?msg=demande_declaree");
                exit();
            }
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage();
            exit();
        }
    }
}
?>