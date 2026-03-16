<?php
session_start();
require_once('/var/www/config/db.php');
$enigmeId = $_POST['enigme_id'];
$reponse = strtoupper($_POST['reponse']);
$userId = $_SESSION['utilisateur_id'];
$q = $pdo->prepare("SELECT solution FROM enigmes WHERE id = ?");
$q->execute([$enigmeId]);
$enigme = $q->fetch();
$q = $pdo->prepare("SELECT id FROM progression WHERE utilisateur_id = ? AND enigme_id = ?");
$q->execute([$userId, $enigmeId]);
if (!$q->fetch()) $pdo->prepare("INSERT INTO progression (utilisateur_id, enigme_id) VALUES (?, ?)")->execute([$userId, $enigmeId]);
if ($reponse === $enigme['solution']) {
$pdo->prepare("UPDATE progression SET resolu = 1, date_resolution = NOW() WHERE utilisateur_id = ? AND enigme_id = ?")->execute([$userId, $enigmeId]);
echo "CORRECT";
} else {
$pdo->prepare("UPDATE progression SET tentatives = tentatives + 1 WHERE utilisateur_id = ? AND enigme_id = ?")->execute([$userId, $enigmeId]);
echo "FAUX";
}
?>