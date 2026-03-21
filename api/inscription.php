<?php
require_once('/var/www/config/db.php');
$pdo->prepare("INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES (?, ?, ?)")->execute([$_POST['pseudo'], $_POST['email'], password_hash($_POST['password'], PASSWORD_DEFAULT)]);
echo "OK";
?>