<?php
require_once("../../database/config.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nom = $_POST['nom'];
    $points = $_POST['points_requis'];
    
    $image = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $folder = "../../uploads/";
    
    if (!is_dir($folder)) {
        mkdir($folder, 0777, true);
    }

    $newImage = time() . "_" . $image;

    if (move_uploaded_file($tmp, $folder . $newImage)) {

        $sql = "INSERT INTO badges (nom, points_requis, image)
                VALUES (?, ?, ?)";

        $stmt = $db->prepare($sql);
        $stmt->execute([$nom, $points, $newImage]);

        header("Location: badges.php?success=1");
        exit;

    } else {
        echo "Image upload failed";
    }
}
?>