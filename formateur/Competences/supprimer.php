<?php
include("../../database/config.php");
if(isset($_GET["id"])){
    try{
        $req=$db->prepare("DELETE FROM competences WHERE id_competence=?");
        $r=$req->execute([$_GET["id"]]);
        if($r==false){
            header("Location: compe.php?error=Suppression a echoue");
        }else{
            header("Location: compe.php?msg=Suppression a reussi");
        }
    }catch(PDOException $e){
        echo "Erreur: ".$e->getMessage();
    }
}
?>