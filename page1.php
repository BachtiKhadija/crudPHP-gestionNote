<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
   $nom="Tod";
   $prenom="Ted";


?>
    <a href="page2.php?n=<?php echo $nom;?>&p=<?php echo $prenom;?>">Cliquer ici</a>
<!--les données envoyées dans l'url , donc la méthode d'envoi est GET-->
</body>
</html>