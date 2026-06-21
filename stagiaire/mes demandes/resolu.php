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
        if (isset($_POST['resoudre']) && isset($_POST['id_demande'])) {
            $id_demande = $_POST['id_demande'];
            $note_mentor = $_POST['note_mentor'] ?? 0;
            $commentaire = $_POST['commentaire'] ?? '';

            $stmt = $db->prepare("SELECT * FROM aide WHERE id_demande = ? AND id_user = ?");
            $stmt->execute([$id_demande, $id_user]);
            $demande = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($demande) {
                $db->prepare("UPDATE aide SET status = 'resolu' WHERE id_demande = ?")->execute([$id_demande]);

                $propStmt = $db->prepare("SELECT id_proposition, id_user FROM propositions_aide WHERE id_demande = ? LIMIT 1");
                $propStmt->execute([$id_demande]);
                $proposition = $propStmt->fetch(PDO::FETCH_ASSOC);

                if ($proposition) {
                    $db->prepare("UPDATE propositions_aide SET status = 'acceptee' WHERE id_proposition = ?")->execute([$proposition['id_proposition']]);

                    $db->prepare("INSERT INTO aide_effectuee (id_proposition, id_mentor, id_beneficiaire, date_intervention, note_mentor, commentaire) VALUES (?, ?, ?, NOW(), ?, ?)")->execute([$proposition['id_proposition'], $proposition['id_user'], $id_user, $note_mentor, $commentaire]);
                }

                header('Location: index.php?message=Demande marquée comme résolue avec succès');
                exit();
            } else {
                header('Location: index.php?error=Vous n\'êtes pas autorisé à modifier cette demande');
                exit();
            }
        } else {
            header('Location: index.php?error=Aucune demande spécifiée');
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        exit();
    }
}
?>
