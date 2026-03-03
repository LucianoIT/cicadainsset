<?php
session_start();
require_once('/var/www/config/db.php');
$erreur = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$q = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$q->execute([$_POST['email']]);
$user = $q->fetch();
if ($user && password_verify($_POST['password'], $user['mot_de_passe'])) {
$_SESSION['utilisateur_id'] = $user['id'];
$_SESSION['pseudo'] = $user['pseudo'];
$_SESSION['email'] = $user['email'];
$pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?")->execute([$user['id']]);
header('Location: /tableau-bord.php');
exit();
}
$erreur = "Email ou mot de passe incorrect";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Connexion</title>
<link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
<div class="form-box">
<h1>🔐 Connexion</h1>
<?php if ($erreur) echo '<p style="color:red">' . $erreur . '</p>'; ?>
<form method="POST">
<div class="form-group">
<label>EMAIL</label>
<input type="email" name="email" required>
</div>
<div class="form-group">
<label>MOT DE PASSE</label>
<input type="password" name="password" required>
</div>
<button type="submit" class="btn btn-primary">SE CONNECTER</button>
</form>
<p class="text-center"><a href="/inscription.php">Pas de compte ?</a></p>
</div>
</div>
</body>
</html>