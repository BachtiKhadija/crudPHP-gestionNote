<?php
$message = "";
if ($_SERVER['REQUEST_METHOD']=="POST" && isset($_FILES['fichier'])) {
    //print_r($_FILES['fichier']);
    
    if ($_FILES['fichier']['error'] == 0) {
        $maxSize = 2000000; // 2 Mo
        $allowed = ['jpg', 'png', 'jpeg'];
        $fileName = $_FILES['fichier']['name'];
        $fileSize = $_FILES['fichier']['size'];
        $tmp = $_FILES['fichier']['tmp_name'];
        $type = $_FILES['fichier']['type'];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        // Vérifier taille
        if ($fileSize > $maxSize) {
            $message = "Fichier trop volumineux";
        }
        // Vérifier extension
        elseif (!in_array($extension, $allowed)) {
            $message = "Extension non autorisée";
        }
        // Vérifier vraie image
        elseif (getimagesize($tmp) === false) {
            $message = "Ce n'est pas une image valide";
        }

        else {
            // Renommer fichier
            $newName = uniqid() . "." . $extension;
            // Créer dossier si n'existe pas
            if (!is_dir("uploads")) {
                mkdir("uploads");
            }
            // Déplacer fichier
            if (move_uploaded_file($tmp, "uploads/" . $newName)) {
                $message = "Upload réussi !";
            } else {
                $message = "Erreur lors du déplacement";
            }
        }

    } else {
        $message = "Erreur lors de l'upload";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload fichier PHP</title>
</head>
<body>

<h2>Uploader une image</h2>

<form action="" method="POST" enctype="multipart/form-data">
    <input type="file" name="fichier" required>
    <br><br>
    <input type="submit" value="Envoyer">
</form>

<br>

<?php
// Affichage message
if (!empty($message)) {
    echo "<strong >$message</strong><br><br>";
}

// Affichage infos fichier
if (isset($_FILES['fichier']) && $_FILES['fichier']['error'] == 0) {
 $ext=pathinfo($_FILES['fichier']['name'],PATHINFO_EXTENSION);
    echo "<h3>Informations :</h3>";
    echo "Nom : " . $_FILES['fichier']['name'] . "<br>";
    echo "Type : " . $_FILES['fichier']['type'] . "<br>";
    echo "Taille : " . $_FILES['fichier']['size'] . " octets<br>";
    echo "Temporaire : " . $_FILES['fichier']['tmp_name'] . "<br>";
    echo "Extension : ".$ext."<br>";
  
    }
?>

</body>
</html>