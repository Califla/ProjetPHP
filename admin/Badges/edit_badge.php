<?php
require_once("../../database/config.php");

$id = $_GET['id'];

$stmt = $db->prepare("SELECT * FROM badges WHERE id_badge=?");
$stmt->execute([$id]);

$badge = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$badge){
    die("Badge introuvable");
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modifier badge</title>
</head>
<body>

<h2>Modifier un badge</h2>

<form action="update_badge.php" method="POST" enctype="multipart/form-data">

    <input type="hidden" name="id_badge"
           value="<?= $badge['id_badge'] ?>">

    <input type="text"
           name="nom"
           value="<?= htmlspecialchars($badge['nom']) ?>"
           required>

    <input type="number"
           name="points_requis"
           value="<?= $badge['points_requis'] ?>"
           required>

    <p>Image actuelle :</p>

    <img src="../../uploads/<?= $badge['image'] ?>"
         width="100">

    <br><br>

    <input type="file" name="image">

    <button type="submit">
        Modifier
    </button>

</form>

</body>
</html>