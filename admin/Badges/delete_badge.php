<?php
require_once("../../database/config.php");

if (isset($_GET['id'])) {

    $id = $_GET['id'];
    
    $stmt = $db->prepare("DELETE FROM badges WHERE id_badge = ?");
    $stmt->execute([$id]);

}

header("Location: badges.php");
exit;
?>