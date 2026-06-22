<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include("../../database/config.php");

    extract($_POST);
    $err = [];

    if (empty($lenom)) $err['nom'] = "le nom est obligatoire";
    if (empty($lpoints) || !is_numeric($lpoints)) $err['points'] = "les points requis sont obligatoires";
    if (empty($licone)) $err['icone'] = "l'icône est obligatoire";

    if (!empty($err)) {
        $query = http_build_query([
            'edit' => $id_badge,
            'e' => $err,
            'old' => [
                'id_badge' => $id_badge,
                'lenom' => $lenom,
                'lpoints' => $lpoints,
                'licone' => $licone
            ]
        ]);
        header("Location: badges.php?$query");
        exit();
    }

    try {
        $lenom = htmlspecialchars($lenom);
        $licone = htmlspecialchars($licone);
        $lpoints = (int) $lpoints;

        $stmt = $db->prepare("UPDATE badges SET nom = ?, points_requis = ?, icone = ? WHERE id_badge = ?");
        $stmt->execute([$lenom, $lpoints, $licone, $id_badge]);

        header("Location: badges.php?msg=badge modifié avec succès");
        exit();
    } catch (PDOException $e) {
        echo "Erreur : " . $e->getMessage();
        exit();
    }
} else {
    header("Location: badges.php");
    exit();
}
?>