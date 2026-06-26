<?php
session_start();
if (!isset($_SESSION['role'])) {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
}
include("../../database/config.php");

if (!isset($_GET["id"])) {
    header("Location: marketplace.php?error=missing_id");
    exit();
} else {
    try {
        $req = $db->prepare("UPDATE aide SET `signal` = `signal` + 1 WHERE id_demande = ?");
        $req->execute([$_GET["id"]]);
        $redir = match ($_SESSION['role']) {
            'admin' => '../../admin/Marketplace/market.php',
            'mentor' => '../../stagiaire/marketplace/index.php',
            'stagiaire' => '../../stagiaire/marketplace/index.php',
            default => 'marketplace.php',
        };
        if ($req == false) {
            header("Location: $redir?error=signal_failed");
            exit();
        } else {
            header("Location: $redir?msg=signal_success");
            exit();
        }
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        exit();
    }
}
?>