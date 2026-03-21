<?php
$erreur = '';
$succes = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$ch = curl_init('http://localhost/api/inscription.php');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $_POST);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$reponse = curl_exec($ch);
curl_close($ch);
if ($reponse == 'OK') $succes = "Inscription réussie !";
elseif ($reponse == 'EMAIL_EXISTANT') $erreur = "Email déjà utilisé";
elseif ($reponse == 'PSEUDO_EXISTANT') $erreur = "Pseudo déjà utilisé";
else $erreur = "Tous les champs sont requis";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Inscription</title>
<link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
<div class="form-box">
<h1>📝 Inscription</h1>
<?php if ($erreur) echo '<p style="color:red">' . $erreur . '</p>'; ?>
<?php if ($succes): ?>
<p style="color:green"><?= $succes ?></p>
<a href="/connexion.php" class="btn btn-primary">Se connecter</a>
<?php else: ?>
<form method="POST">
<div class="form-group"><label>Pseudo</label><input type="text" name="pseudo" required></div>
<div class="form-group"><label>Email</label><input type="email" name="email" required></div>
<div class="form-group"><label>Mot de passe</label><input type="password" name="password" required></div>
<button type="submit" class="btn btn-primary">S'inscrire</button>
</form>
<p class="text-center"><a href="/connexion.php">Déjà un compte ?</a></p>
<?php endif; ?>
</div>
</div>
</body>
</html>