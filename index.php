<?php
// Page demo
require_once("./MetaPage.php");
$data = new SocialMetaTags();
$url =  isset($_POST["urlPage"]) ? $_POST["urlPage"] :"https://www.google.ch/";
var_dump($data->getTags($url));
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <title>Demo</title>
</head>
<body>

<form action="" method="post">
<input type="url" name="urlPage">
<button type="submit">Récupère Meta</button>
</form>
    
</body>
</html>