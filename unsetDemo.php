<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php 
    $arr=[10,20,30,40,50];
    print_r($arr);
    echo"<br/>";
    unset($arr[1]);
    $arr=array_values($arr);
    print_r($arr);








?>



</body>
</html>