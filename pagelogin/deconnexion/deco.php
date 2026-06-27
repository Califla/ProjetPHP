<?php
session_start();
if (isset($_SESSION) && !empty($_SESSION)){
    session_unset();
    session_destroy();
    session_gc();
    setcookie('remember_email', '', time() - 3600, '/', '', false, true);
    header("location:../connexion/index.php");
}
?>