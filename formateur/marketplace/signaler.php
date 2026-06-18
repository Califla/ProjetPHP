<?php
include("../../database/config.php");

if (!isset($_GET["id"])) {
    header("Location: marketplace.php?error=missing_id");
    exit();
}

try {
    $req = $db->prepare("UPDATE aide SET `signal` = 1 WHERE id_demande = ?");
    $req->execute([$_GET["id"]]);
    header("Location: marketplace.php?msg=signal_success");
    exit();
} catch (PDOException $e) {
    header("Location: marketplace.php?error=signal_error");
    exit();
}