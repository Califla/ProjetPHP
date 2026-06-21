<?php
session_start();
if ($_SESSION['role']!="mentor"){
    header("Location: index.php");
    exit();
}else{
    
    include("../../database/config.php");
    if (isset($_GET['id_demande'])) {
        $id_demande = $_GET['id_demande'];
        $id_mentor = $_SESSION['id_user'];
        try {
            #verifier si le mentor a deja proposé son aide
            $stmt = $db->prepare("SELECT * FROM propositions_aide WHERE id_user = ? AND id_demande = ?");
            $stmt->execute([$id_mentor, $id_demande]);
            if ($stmt->rowCount() > 0) {
                header("Location: index.php?error=Vous avez déjà proposé votre aide pour cette demande.");
                exit();
            }
            #proposer l'aide
            $stmt = $db->prepare("INSERT INTO propositions_aide ( id_user,`status`, id_demande, date_prop) VALUES (?, ?,?, NOW())");
            $stmt->execute([$id_mentor,"en_attente", $id_demande]);
            if ($stmt->rowCount() > 0) {
                header("Location: index.php?msg=Votre aide a été proposée avec succès.");
                exit();
            } else {
                header("Location: index.php?error=Erreur lors de la proposition de votre aide.");
                exit();
            }
        } catch (PDOException $e) {
            echo "Erreur: " . $e->getMessage();
            exit();
        }
    } else {
        header("Location: index.php?error=ID de demande manquant.");
        exit();
    }
        
}
?>