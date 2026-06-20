<?php
if (!isset($_GET['id']) || !isset($_GET['statut'])) {
    header("Location: index.php?error=missing_id_or_statut");
    exit();
} else {
    try {
        include('../../database/config.php');
        $req = $db->prepare('UPDATE utilisateurs SET statut = ? WHERE id_user = ?');
        $req->execute([$_GET['statut'], $_GET['id']]);
        header('Location: utili.php?msucc=STATUT CHANGÉ AVEC SUCCEÉ');
        exit();
    } catch (PDOException $e) {
        die("ERREUR DE CHANGEMENT DU STATUT:" . $e->getMessage());
    }
}
?>