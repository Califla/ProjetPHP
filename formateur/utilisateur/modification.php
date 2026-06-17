<?php
include("../../database/config.php");
if (!isset($_GET['id'])) {
    header("Location: index.php?error=missing_id");
    exit();
}
else{
    try {
    $req = $db->prepare("UPDATE utilisateurs SET role = ? WHERE id_user = ?");
    $req->execute([$_GET['role'], $_GET['id']]);
   if($req==false){
    header("Location: index.php?error=update_failed");
    exit();
}
    
else{
    header("Location: index.php?success=role_updated");
    exit();}
} catch(PDOException $e) {
    die("Erreur de base de données : " . $e->getMessage());
}
}
?>