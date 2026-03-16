<?php
session_start();
require_once('/var/www/config/db.php');
$q = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$q->execute([$_POST['email']]);
$user = $q->fetch();
if ($user && password_verify($_POST['password'], $user['mot_de_passe'])) {
$_SESSION['utilisateur_id'] = $user['id'];
$_SESSION['pseudo'] = $user['pseudo'];
$_SESSION['email'] = $user['email'];
$pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?")->execute([$user['id']]);
echo "OK";
} else {
echo "ERREUR";
}
?>