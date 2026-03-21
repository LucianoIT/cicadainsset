<?php
session_start();
require_once('/var/www/config/db.php');
if (!isset($_SESSION['utilisateur_id'])) { header('Location: /connexion.php'); exit(); }
$userId = $_SESSION['utilisateur_id'];
$enigmes = $pdo->query("SELECT * FROM enigmes ORDER BY numero")->fetchAll();
$prog = $pdo->prepare("SELECT enigme_id, resolu FROM progression WHERE utilisateur_id = ?");
$prog->execute([$userId]);
$progressions = [];
foreach ($prog->fetchAll() as $p) $progressions[$p['enigme_id']] = $p['resolu'];
$score = $pdo->prepare("SELECT SUM(e.points) as total FROM progression p JOIN enigmes e ON p.enigme_id = e.id WHERE p.utilisateur_id = ? AND p.resolu = 1");
$score->execute([$userId]);
$totalPoints = $score->fetch()['total'] ?? 0;
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Tableau de bord</title>
<link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
<header class="dashboard-header">
<div>
<h1>👋 Salut <?= $_SESSION['pseudo'] ?> !</h1>
<p>Score : <?= $totalPoints ?> points</p>
</div>
<a href="/deconnexion.php" class="btn btn-danger">Déconnexion</a>
</header>
<h2>Les 5 Énigmes</h2>
<div class="enigmes-grid">
<?php foreach ($enigmes as $enigme):
$resolu = !empty($progressions[$enigme['id']]);
$debloque = $enigme['numero'] == 1 || !empty($progressions[$enigme['id'] - 1]);
?>
<div class="enigme-card <?= $resolu ? 'resolu' : '' ?> <?= !$debloque ? 'verrouille' : '' ?>">
<h3>Énigme <?= $enigme['numero'] ?> : <?= $enigme['titre'] ?></h3>
<p class="points"><?= $enigme['points'] ?> points</p>
<?php if ($resolu): ?>
<span class="badge badge-success">✅ Résolue</span>
<?php elseif (!$debloque): ?>
<span class="badge badge-locked">🔒 Verrouillée</span>
<?php else: ?>
<span class="badge badge-available">🔓 Disponible</span>
<a href="/enigme<?= $enigme['numero'] ?>.php" class="btn btn-primary">Commencer</a>
<?php endif; ?>
</div>
<?php endforeach; ?>
</div>
<p style="margin-top: 30px;"><a href="/classement.php" class="btn btn-secondary">🏆 Voir le classement</a></p>
</div>
</body>
</html>