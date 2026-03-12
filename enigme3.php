<?php
session_start();
if (!isset($_SESSION['utilisateur_id'])) { header('Location: /connexion.php'); exit(); }
require_once('/var/www/config/db.php');
$enigmeId = 3;
$q = $pdo->prepare("SELECT * FROM enigmes WHERE id = ?");
$q->execute([$enigmeId]);
$enigme = $q->fetch();
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$reponse = strtoupper($_POST['reponse']);
$userId = $_SESSION['utilisateur_id'];
$q = $pdo->prepare("SELECT id FROM progression WHERE utilisateur_id = ? AND enigme_id = ?");
$q->execute([$userId, $enigmeId]);
if (!$q->fetch()) $pdo->prepare("INSERT INTO progression (utilisateur_id, enigme_id) VALUES (?, ?)")->execute([$userId, $enigmeId]);
if ($reponse === $enigme['solution']) {
$pdo->prepare("UPDATE progression SET resolu = 1, date_resolution = NOW() WHERE utilisateur_id = ? AND enigme_id = ?")->execute([$userId, $enigmeId]);
header('Location: /tableau-bord.php');
exit();
}
$pdo->prepare("UPDATE progression SET tentatives = tentatives + 1 WHERE utilisateur_id = ? AND enigme_id = ?")->execute([$userId, $enigmeId]);
$message = "❌ Mauvaise réponse !";
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= $enigme['titre'] ?></title>
<link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
<div class="enigme-page">
<a href="/tableau-bord.php" class="btn-back">← Retour</a>
<header>
<h1>Énigme <?= $enigme['numero'] ?> : <?= $enigme['titre'] ?></h1>
<p class="points"><?= $enigme['points'] ?> points</p>
</header>
<div class="enigme-content">
<p><?= nl2br($enigme['description']) ?></p>
<div class="enigme-visual"><p style="font-size: 3em;">👾 01000100 01000001 01001110 01001011</p></div>
</div>
<?php if ($message) echo '<p style="color:red;font-weight:bold">' . $message . '</p>'; ?>
<div class="reponse-section">
<form method="POST">
<div class="form-group">
<label>Ta réponse (en MAJUSCULES)</label>
<input type="text" name="reponse" required placeholder="Ex: ABCD1234">
</div>
<button type="submit" class="btn btn-primary">Valider</button>
</form>
</div>
</div>
</div>
</body>
</html>