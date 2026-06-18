<?php
session_start();

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../pagelogin/connexion/index.php');
    exit();
}
include('../../database/config.php');
try{
$id = $_GET['id'] ?? null;
$statut = $_GET['statut'] ?? null;
$liste_des_statuts = ['actif', 'en_attente', 'suspendu'];
if (!$id || !ctype_digit($id) || !in_array($statut, $liste_des_statuts)) {
    header('Location: utili.php?merr=ERREUR DE CHANGEMENT DE STATUT');
    exit();
}
$req = $db->prepare('UPDATE utilisateurs SET statut = ? WHERE id_user = ?');
$req->execute([$statut, $id]);
header('Location: utili.php?msucc=STATUT CHANGÉ AVEC SUCCEÉ');
exit();
}
catch(PDOException $e){
    die("ERREUR DE CHANGEMENT DU STATUT:".$e->getMessage());
}
?>