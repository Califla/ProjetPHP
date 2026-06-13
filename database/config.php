<?php
try{
    $db=new PDO("mysql:host=127.0.0.1;dbname=skillswaps","root","");
}
catch(PDOException $e){
    echo "erreur de connexion:".$e->getMessage();
}
?>
