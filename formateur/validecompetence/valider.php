<?php
if (isset($_GET['iduser']) && isset($_GET['idcompetence'])) {
    include("../../database/config.php");
    try {
        #ajoute l'id de validateur et la date de validation dans la table validation_competence et change le status en validée
        $stmt = $db->prepare("UPDATE validation_competence SET status = 'validee', date_validation = NOW(), id_validateur = ? WHERE id_user = ? AND id_competence = ?");
        $stmt->execute([$_GET['id_validateur'], $_GET['iduser'], $_GET['idcompetence']]);
        if($stmt==false) {
            $stmt = $db->prepare("UPDATE utilisateurs SET score = score + 100 WHERE id_user = ?");
            $stmt->execute([$id_user]);
            header("Location: validecompetence.php?msg=valide_success");
        } else {
            header("Location: validecompetence.php?error=update_failed");
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