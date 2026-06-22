<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'formateur') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
}
if (isset($_GET['iduser']) && isset($_GET['idcompetence'])) {
    include("../../database/config.php");
    try {
        #ajoute l'id de validateur et la date de validation dans la table validation_competence et change le status en validée
        $stmt = $db->prepare("UPDATE validation_competence SET status = 'refusee', date_validation = NOW(), id_validateur = ? WHERE id_user = ? AND id_competence = ?");
        $stmt->execute([$_GET['id_validateur'], $_GET['iduser'], $_GET['idcompetence']]);
        if($stmt==false) {
            header("Location: validecompetence.php?error=update_failed");
        } else {
            header("Location: validecompetence.php?msg=refuse_success");
        }
        exit();
    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        exit();
    }
} else {
    header("Location: validecompetence.php?error=missing_params");
    exit();
}
?>