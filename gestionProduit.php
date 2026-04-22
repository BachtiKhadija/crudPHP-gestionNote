<?php
session_start();
//initialiser la liste des produits
if(!isset($_SESSION['products'])) {
    $_SESSION['products'] = [];
}
$errors = [];
$success = "";
$editMode = false;
$product=null;
$ref=$libelle=$categorie=$prix=$disponible="";
$origine=[];
//liste des catégories
$categories = ["alimentaire", "bureautique", "menage"];
//liste des pays
$paysList = ["Maroc", "France", "Espagne", "Italie"];
//suppression
if (isset($_GET['delete'])) {
    unset($_SESSION['products'][$_GET['delete']]);
    $_SESSION['products']=array_values($_SESSION['products']);
}
//edit dans le sens Get data of item to Edit
if (isset($_GET['edit'])) {
    $editMode = true;
    $product = $_SESSION['products'][$_GET['edit']];
}
//show or details
$detailProduct = null;
if (isset($_GET['detail'])) {
    $detailProduct = $_SESSION['products'][$_GET['detail']];
}
//aprés le clic sur submit 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //or if (isset($_POST["save"]))

    $ref = trim($_POST['ref']);
    $categorie = $_POST['categorie'] ?? "";
    $prix = $_POST['prix'];
    $origine = $_POST['origine'] ?? [];
    $disponible = $_POST['disponible'] ?? "";

    // Validation
    if (empty($ref)) {
        $errors['ref'] = "Ref obligatoire";
    }

    if (!is_numeric($prix) || $prix <= 0) {
        $errors['prix'] = "Prix invalide";
    }

    if (empty($categorie)) {
        $errors['categorie'] = "Choisir une catégorie";
    }

    if (empty($origine)) {
        $errors['origine'] = "Choisir au moins un pays";
    }

    if (empty($disponible)) {
        $errors['disponible'] = "Choisir disponibilité";
    }

    // Upload image
    $imageName = "";
    if (!empty($_FILES['image']['name'])) {
        $imageName = "images/".$ref . "_".time();
          //avoir un nom unique  time => diff en ms entre 1/1/1970 et today
        move_uploaded_file($_FILES['image']['tmp_name'], $imageName);
        
        }

    if (empty($errors)) {
        
        $libelle = "prod" . $ref;

        $_SESSION['products'][] = [
            "ref" => $ref,
            "libelle" => $libelle,
            "categorie" => $categorie,
            "prix" => $prix,
            "origine" => $origine,
            "disponible" => $disponible,
            "image" => $imageName
        ];

        $success = "Prodiut enregistré";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion Produits</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-4">

<h2>Formulaire Produit</h2>

<?php if (!empty($success)): ?>
<div class="alert alert-success"><?= $success ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="mb-4">

    <input type="text" name="ref" placeholder="Référence"
        class="form-control mb-2"
       >
    <div class="text-danger"><?= $errors['ref'] ?? "" ?></div>

    <select name="categorie" class="form-control mb-2">
        <option value="">--Catégorie--</option>
        <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat ?>" >
                <?= $cat ?>
            </option>
        <?php endforeach; ?>
    </select>
  
 <div class="text-danger"><?= $errors['categorie'] ?? "" ?></div>
    <input type="number" name="prix" placeholder="Prix"
        class="form-control mb-2"
        value="">
     <div class="text-danger"><?= $errors['prix'] ?? "" ?></div>

    <label>Pays d'origine :</label><br>
    <?php foreach ($paysList as $p){ ?>
        <input type="checkbox" name="origine[]" value="<?= $p ?>">
        <?= $p ?>
    <?php } ?>
    <div class="text-danger"><?= $errors['origine'] ?? "" ?></div>

    <br><label>Disponible :</label><br>
    <input type="radio" name="disponible" value="oui"> Oui
    <input type="radio" name="disponible" value="non"> Non
    <div class="text-danger"><?= $errors['disponible'] ?? "" ?></div>
 <div class="text-danger"><?= $errors['disponible'] ?? "" ?></div>
    <br><input type="file" name="image" class="form-control mb-2">

    <button type="submit" name="save" class="btn btn-primary">Enregistrer</button>
</form>

<hr>

<h2>Liste Produits</h2>

<div class="row">
<?php foreach ($_SESSION['products'] as $i=>$p): ?>
    <div class="col-md-3">
        <div class="card mb-3">
            <img src="<?= $p['image'] ?>" class="card-img-top" height="150">

            <div class="card-body">
                <h5><?= $p['libelle'] ?></h5>

                <a href="?edit=<?= $i; ?>" class="btn btn-warning btn-sm">Edit</a>
                <a href="?delete=<?= $i; ?>" class="btn btn-danger btn-sm">Delete</a>
                <a href="?detail=<?= $i; ?>" class="btn btn-info btn-sm">Detail</a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php if (!is_null($detailProduct)): ?>
<hr>
<h3>Détail Produit</h3>
<ul>
    <li>Ref : <?= $detailProduct['ref'] ?></li>
    <li>Libelle : <?= $detailProduct['libelle'] ?></li>
    <li>Catégorie : <?= $detailProduct['categorie'] ?></li>
    <li>Prix : <?= $detailProduct['prix'] ?></li>
    <li>Pays : <?= implode(", ", $detailProduct['origine']) ?></li>
    <li>Disponible : <?= $detailProduct['disponible'] ?></li>
</ul>
<?php endif; ?>

</body>
</html>