<?php

session_start();
if(!isset($_SESSION['produits'])):
    $_SESSION['produits']=[];
endif;
$categorie=$prix=$disponible=$image="";
$origine=[];
$categories = ["alimentaire", "bureautique", "menage"];
//liste des pays
$paysList = ["Maroc", "France", "Espagne", "Italie"];
$errors=[];
//message pour flashbag
$message="";
$editIndex=-1;
$editProduct=null;
//vérifier si on est en mode edit 
if(isset($_GET["edit"])){
    
$editIndex=$_GET["edit"];
$editProduct=$_SESSION['produits'][$editIndex];

}
//vérifier si on veut sodium_crypto_box_keypair_from_secretkey_and_publickey
if(isset($_GET["delete"])){
    
$i=$_GET["delete"];
unset($_SESSION['produits'][$i]);
$_SESSION['produits']=array_values($_SESSION['produits']);

}



//si on clique sur submit 
if($_SERVER["REQUEST_METHOD"]=="POST"){
 $categorie=$_POST["categorie"]??"";
 $prix=$_POST["prix"]??0;
 $disponible=$_POST["disponible"]??"";
 $origine=$_POST["origine"]??[];
 $image=$_FILES["image"]["name"]??"";
 if(empty(trim($categorie))){
    $errors["categorie"]="la categorie est obligatoire !!!";
 }
 if(!is_numeric($prix) || $prix<20 || $prix>1000){
    $errors["prix"]="prix invalide !!!";
 }
 if(count($origine)==0){
    $errors["origine"]="vous devez choisir au moins un pays d'origine !!!";
 }
 if($editIndex==-1){
 if(!isset($_FILES["image"])){
     $errors["image"]="vous devez choisir au moins une image !!!";
 }else{
   $oldName=$_FILES["image"]["name"];
   //$extension=explode(".",$oldName)[1]
   $extension=strtolower(pathinfo($oldName,PATHINFO_EXTENSION));
   $taille=$_FILES["image"]["size"];
   if(!in_array($extension,['png','jpeg','jpg'])){
    $errors["image"]="vous devez choisir une image";
   }else if($taille>2000000){
     $errors["image"]="image trop volumineuse !!!!!";
   }
 }

 }
 if(strlen($disponible)==0){
    $errors["disponible"]="vous devez choisir une disponibilité !!!";
 }
 //si on a zéro erreur
 if(empty($errors)){
    echo $editIndex;
  if(isset($_FILES["image"])){
   //recupérer les infos de l'image
   $name=$_FILES["image"]["name"];
   //produit.png
   //$ext=explode(".",$name)[1]
   $ext=pathinfo($name,PATHINFO_EXTENSION);
   $tempDir=$_FILES["image"]["tmp_name"];
   
   //sauvegarder l image dans un dossier upploads
   if(!is_dir("upploads")){
    mkdir("upploads");
   }
   $newName="upploads/".time()."_image".$ext;
   move_uploaded_file($tempDir,$newName);
   //construire le nouveau produit
  }
   if($editIndex>=0){
    $editProduct["categorie"]=$categorie;
    $editProduct["prix"]=$prix;
    $editProduct["disponible"]=$disponible;
    $editProduct["origine"]=$origine;
    if(isset($_FILES["image"])&&!empty($_FILES["image"]["name"])){
         $editProduct["image"]=$newName;
    }
    $_SESSION["produits"][$editIndex]=$editProduct;
    $message="produit modifié avec succés !!!!!";
   }else{
     $ref=uniqid();
     $libelle="prod".$ref;
     $_SESSION["produits"][]=["ref"=>$ref,"libelle"=>$libelle,"origine"=>$origine,"disponible"=>$disponible,"prix"=>$prix,"image"=>$newName,"categorie"=>$categorie];
    $message="produit ajouté avec succés !!!!!";
   }
 
   //array_push($_SESSION["produits"],$data);
 



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
<?php 
   echo (!empty($message))?"<div class='alert alert-success'>".$message."</div>":"";

?>
<form method="POST" enctype="multipart/form-data" class="mb-4 p-3">

    <div class="mb-3">
    <label for="categorie" class="form-label">Categorie</label>
    <select name="categorie" class="form-select mb-2">
        <option value="" selected disabled>--Catégorie--</option>
       <!-- <?php //foreach ($categories as $cat): ?>
            <option value="<?php //echo $cat;?>" >
                <?php //echo $cat; ?>
            </option>-->
        <?php //endforeach; ?>
        <!--autrement-->
      <?php foreach ($categories as $cat){
            $exist=($editProduct["categorie"]==$cat)?"selected":"";
            echo "<option value=".$cat." $exist  >".$cat."</option>";
      }
      ?>

    </select>

    <div class="text-danger"><?= isset($errors["categorie"])?$errors["categorie"]:"";?></div>
  </div>
  <div class="mb-3">
    <label for="prix" class="form-label">Prix:</label>
    <input type="number" name="prix" value=<?= $editProduct["prix"]??""; ?> placeholder="Prix"
        class="form-control mb-2"
        value="">
         <div class="text-danger"><?= $errors["categorie"]??"";?></div>
    
</div>
<div class="mb-3">
    <label for="origine" class="form-label">Pays d'origine :</label><br>
  <?php foreach ($paysList as $p){
        $exist=isset($editProduct["origine"])&&in_array($p,$editProduct["origine"])?"checked":"";
       echo"<input type='checkbox' name='origine[]' $exist value=".$p.">".$p."";
    }
    ?>
     <div class="text-danger"><?php if(isset($errors["origine"])):
                               echo $errors["origine"];
        
                         endif;?></div>
</div>
<div class="mb-3">
    
<label for="disponible" class="form-label">Disponible :</label><br>
  <?php foreach(['oui','non'] as $disp):
    $exist=isset($editProduct["disponible"])&&($editProduct["disponible"]==$disp)?"checked":"";
     echo "<input type='radio' name='disponible' $exist value=".$disp.">".$disp."";
  endforeach;
  ?>
   <div class="text-danger"><?= $errors["disponible"]??"";?></div>
</div>
<div class="mb-3">
    <label for="image" class="form-label">Image</label>
    <?php if(isset($editProduct['image'])):?>
    <img src="<?php echo $editProduct['image']?>" class="img-rounded" width="100" height="100" alt="">
    <?php  endif;?>
    <input type="file" name="image" class="form-control mb-2">
 <div class="text-danger"><?= $errors["image"]??"";?></div>

</div>
    <button type="submit" name="save" class="btn btn-primary">Enregistrer</button>
</form>

<hr>
<?php  if(count($_SESSION["produits"])>0):?>
<table class="table table-bordered my-3 text-center">
<thead><tr><th>Ref</th><th>libelle</th><th>origine</th><th>prix</th><th>Image</th><th>Actions</th></tr></thead>
<tbody>
      <?php
         foreach($_SESSION["produits"] as $pos=>$p):
        echo"<tr><td>".$p["ref"]."</td><td>".$p["libelle"]."</td><td>".implode(", ",$p["origine"])."</td><td>".$p["prix"]."</td>
        <td><img src='".$p["image"]."' class='img-fluid' width='100' height='100'/> </td>
        <td>
          <a class='btn btn-sm btn-info' href='?edit=$pos'>Edit</a>
         <a class='btn btn-sm btn-danger' href='?delete=$pos'>delete</a>
          </td></tr>";



         endforeach;


     ?>
</tbody>

</table>
<?php else:
      echo "<div class='alert alert-warning'>votre tableau est vide !!!!!!</div>";
endif;
?>


</body>
</html>