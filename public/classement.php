<?php
require_once('/var/www/config/db.php');
$classement = $pdo->query("
SELECT u.pseudo, COUNT(p.id) as resolues, SUM(e.points) as score
FROM utilisateurs u
LEFT JOIN progression p ON u.id = p.utilisateur_id AND p.resolu = 1
LEFT JOIN enigmes e ON p.enigme_id = e.id
GROUP BY u.id ORDER BY score DESC LIMIT 10
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Classement</title>
<link rel="stylesheet" href="/style.css">
</head>
<body>
<div class="container">
<h1>🏆 Classement</h1>
<table class="leaderboard">
<tr><th>Rang</th><th>Pseudo</th><th>Score</th><th>Énigmes</th></tr>
<?php foreach ($classement as $i => $j): ?>
<tr>
<td><?= $i + 1 ?></td>
<td><?= $j['pseudo'] ?></td>
<td><?= $j['score'] ?? 0 ?> pts</td>
<td><?= $j['resolues'] ?>/5</td>
</tr>
<?php endforeach; ?>
</table>
<p><a href="/tableau-bord.php" class="btn btn-secondary">← Retour</a></p>
</div>
</body>
</html>