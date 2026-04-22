<?php
session_start();

if (!isset($_SESSION['etudiants'])) {
    $_SESSION['etudiants'] = [];
}

$editIndex = null;
$editData = null;//student To Edit


if (isset($_GET['delete'])) {
    //c'est le clic sur delete qui a recharger la page
    $i = $_GET['delete'];
    unset($_SESSION['etudiants'][$i]);
    $_SESSION['etudiants'] = array_values($_SESSION['etudiants']);
}


if (isset($_GET['edit'])) {
    $editIndex = $_GET['edit'];
    $editData = $_SESSION['etudiants'][$editIndex];
}

if (isset($_POST['save'])) { //if $_SERVER["REQUEST_METHOD"]=="POST"
       //si je clique sur l'input type submit
    $nom = $_POST['nom']??'';
    $note = $_POST['note']??0;
    $genre = $_POST['genre'] ?? '';
    $module = $_POST['module'] ?? '';
    $niveau = $_POST['niveau'] ?? '';
    $skills = $_POST['skills'] ?? [];

    
    $nomValid = preg_match("/^[A-Z][a-z]{3,30}(\s[a-z]{3,})*$/", $nom);
    $noteValid = ($note>=0 && $note<=20);
    if ($nomValid && $noteValid) {
        $data = [
            "nom" => $nom,
            "note" => $note,
            "genre" => $genre,
            "module" => $module,
            "niveau" => $niveau,
            "skills" => $skills
        ];

        // UPDATE
        if (isset($_POST['edit_index'])) {
            //value of hidden field
            $_SESSION['etudiants'][$_POST['edit_index']] = $data;
        }
        // ADD
        else {
            $_SESSION['etudiants'][] = $data;
            //array_push($_SESSION['etudiants'],$data)
        }
    } else {
        echo "<div class='alert alert-danger'>Données invalides (nom ou note)</div>";
    }

     $nom = $note=$genre=$module=$niveau = "";
    $skills = [];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gestion étudiants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h2 class="text-center mb-4">Gestion des Étudiants</h2>


<form method="post" action="" class="card p-3 shadow">

    <input type="hidden" name="edit_index"
           value="<?= $_GET['edit'] ?? '' ?>">

    <div class="mb-2">
        <label>Nom</label>
        <input type="text" name="nom"
               value="<?= $editData['nom'] ?? '' ?>"
               class="form-control" required>
    </div>

    <div class="mb-2">
        <label>Note (/20)</label>
        <input type="number" name="note"
               value="<?= $editData['note'] ?? '' ?>"
               class="form-control" min="0" max="20" required>
    </div>

    <div class="mb-2">
        <label>Genre</label><br>
        <input type="radio" <?php echo (isset($editData['genre'])&&$editData['genre']=="Homme")?"checked":"";?> name="genre" value="Homme"> Homme
        <input type="radio" <?php echo (isset($editData['genre'])&&$editData['genre']=="Femme")?"checked":"";?> name="genre" value="Femme"> Femme
    </div>

    <div class="mb-2">
        <label>Module</label>
        <select name="module" class="form-select">
            <option value="Math" <?php echo (isset($editData['module'])&&$editData['module']=="Math")?"selected":"";?>>Math</option>
            <option value="Informatique">Informatique</option>
            <option value="Physique">Physique</option>
        </select>
    </div>

    <div class="mb-2">
        <label>Niveau</label>
        <select name="niveau" class="form-select">
            <option value="L1" <?php echo (isset($editData['niveau'])&&$editData['niveau']=="L1")?"selected":"";?> >L1</option>
            <option value="L2" <?php echo (isset($editData['niveau'])&&$editData['niveau']=="L2")?"selected":"";?>>L2</option>
            <option value="L3" <?php echo (isset($editData['niveau'])&&$editData['niveau']=="L3")?"selected":"";?>>L3</option>
        </select>
    </div>

    <div class="mb-2">
        <label>Compétences</label><br>
        <input type="checkbox" <?php echo (isset($editData['skills'])&& in_array('PHP',$editData['skills']))?"checked":"";?> name="skills[]" value="PHP"> PHP
        <input type="checkbox" <?php echo (isset($editData['skills'])&& in_array('JS',$editData['skills']))?"checked":"";?> name="skills[]" value="JS"> JS
        <input type="checkbox" <?php echo (isset($editData['skills'])&& in_array('HTML',$editData['skills']))?"checked":"";?> name="skills[]" value="HTML"> HTML
    </div>

    <button type="submit" name="save" class="btn btn-primary">
        <?= isset($_GET['edit']) ? "Modifier" : "Ajouter" ?>
    </button>

</form>


<table class="table table-bordered table-striped mt-4">

    <thead class="table-dark">
        <tr>
            <th>Nom</th>
            <th>Note</th>
            <th>Genre</th>
            <th>Module</th>
            <th>Niveau</th>
            <th>Skills</th>
            <th>Actions</th>
        </tr>
    </thead>

    <tbody>
    <?php foreach ($_SESSION['etudiants'] as $i => $e) { ?>
           <!--  for($i=0;$i<count($_SESSION['etudiants']);$i++)     -->
        <tr>
            <td><?php echo $e['nom'] ?></td>
            <td><?= $e['note'] ?></td>
            <td><?= $e['genre'] ?></td>
            <td><?= $e['module'] ?></td>
            <td><?= $e['niveau'] ?></td>
            <td><?= implode(", ", $e['skills']) ?></td>

            <td>
                <a class="btn btn-warning btn-sm"
                   href="?edit=<?php echo $i ?>">Edit</a>

                <a class="btn btn-danger btn-sm"
                   href="?delete=<?= $i ?>">Delete</a>
            </td>
        </tr>
    <?php } ?>
    </tbody>

</table>

</body>
</html>