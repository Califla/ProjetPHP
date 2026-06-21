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

        $id_proposition = $_GET['id_proposition'] ?? null;
        $action = $_GET['action'] ?? null;
        $id_demande = $_GET['id_demande'] ?? null;

        if (!$id_proposition || !$action || !$id_demande) {
            header('Location: index.php?error=Paramètres manquants');
            exit();
        }

        $stmt = $db->prepare("SELECT id_demande FROM propositions_aide WHERE id_proposition = ?");
        $stmt->execute([$id_proposition]);
        $prop = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$prop) {
            header('Location: index.php?error=Proposition introuvable');
            exit();
        }

        $stmt = $db->prepare("SELECT id_demande FROM aide WHERE id_demande = ? AND id_user = ?");
        $stmt->execute([$id_demande, $id_user]);
        $demande = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$demande) {
            header('Location: index.php?error=Demande introuvable');
            exit();
        }

        if ($action === 'accept') {
            $db->prepare("UPDATE propositions_aide SET status = 'acceptee' WHERE id_proposition = ?")->execute([$id_proposition]);
            $db->prepare("UPDATE propositions_aide SET status = 'refusee' WHERE id_demande = ? AND id_proposition != ?")->execute([$id_demande, $id_proposition]);
            $db->prepare("UPDATE aide SET status = 'en_coure' WHERE id_demande = ?")->execute([$id_demande]);
            header('Location: index.php?message=Proposition acceptée avec succès');
            exit();
        } elseif ($action === 'refuse') {
            $db->prepare("UPDATE propositions_aide SET status = 'refusee' WHERE id_proposition = ?")->execute([$id_proposition]);
            header('Location: index.php?message=Proposition refusée');
            exit();
        } else {
            header('Location: index.php?error=Action invalide');
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        exit();
    }
}
?>
