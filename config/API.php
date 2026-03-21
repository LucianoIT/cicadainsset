<?php
session_start();
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'db.php';

$method = $_SERVER['REQUEST_METHOD'];
$request = $_GET['endpoint'] ?? '';
$input = json_decode(file_get_contents('php://input'), true);

function sendResponse($data, $code = 200) {
http_response_code($code);
echo json_encode($data);
exit;
}

function sendError($message, $code = 400) {
sendResponse(['error' => $message], $code);
}

switch($request) {

case 'register':
if ($method !== 'POST') sendError('Method not allowed', 405);
$pseudo = $input['pseudo'] ?? '';
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';
if (!$pseudo || !$email || !$password) sendError('Tous les champs sont requis');
if (strlen($password) < 6) sendError('Mot de passe trop court (min 6 caractères)');
$q = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ? OR pseudo = ?");
$q->execute([$email, $pseudo]);
if ($q->fetch()) sendError('Email ou pseudo déjà utilisé');
$hash = password_hash($password, PASSWORD_DEFAULT);
$pdo->prepare("INSERT INTO utilisateurs (pseudo, email, mot_de_passe) VALUES (?, ?, ?)")->execute([$pseudo, $email, $hash]);
sendResponse(['success' => true, 'message' => 'Inscription réussie'], 201);
break;

case 'login':
if ($method !== 'POST') sendError('Method not allowed', 405);
$email = $input['email'] ?? '';
$password = $input['password'] ?? '';
if (!$email || !$password) sendError('Email et mot de passe requis');
$q = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$q->execute([$email]);
$user = $q->fetch();
if (!$user || !password_verify($password, $user['mot_de_passe'])) sendError('Identifiants incorrects', 401);
$_SESSION['utilisateur_id'] = $user['id'];
$_SESSION['pseudo'] = $user['pseudo'];
$pdo->prepare("UPDATE utilisateurs SET derniere_connexion = NOW() WHERE id = ?")->execute([$user['id']]);
sendResponse(['success' => true, 'user' => ['id' => $user['id'], 'pseudo' => $user['pseudo'], 'email' => $user['email']]]);
break;

case 'logout':
if ($method !== 'POST') sendError('Method not allowed', 405);
session_destroy();
sendResponse(['success' => true, 'message' => 'Déconnexion réussie']);
break;

case 'user':
if ($method !== 'GET') sendError('Method not allowed', 405);
if (!estConnecte()) sendError('Non authentifié', 401);
$q = $pdo->prepare("SELECT id, pseudo, email, date_inscription FROM utilisateurs WHERE id = ?");
$q->execute([$_SESSION['utilisateur_id']]);
sendResponse(['user' => $q->fetch()]);
break;

case 'enigmes':
if ($method !== 'GET') sendError('Method not allowed', 405);
$enigmes = $pdo->query("SELECT * FROM enigmes ORDER BY numero")->fetchAll();
sendResponse(['enigmes' => $enigmes]);
break;

case 'progression':
if ($method !== 'GET') sendError('Method not allowed', 405);
if (!estConnecte()) sendError('Non authentifié', 401);
$q = $pdo->prepare("SELECT * FROM progression WHERE utilisateur_id = ?");
$q->execute([$_SESSION['utilisateur_id']]);
sendResponse(['progression' => $q->fetchAll()]);
break;

case 'check_answer':
if ($method !== 'POST') sendError('Method not allowed', 405);
if (!estConnecte()) sendError('Non authentifié', 401);
$userId = $_SESSION['utilisateur_id'];
$enigmeId = $input['enigme_id'] ?? 0;
$reponse = $input['reponse'] ?? '';
if (!$enigmeId || !$reponse) sendError('Données manquantes');
$q = $pdo->prepare("SELECT * FROM enigmes WHERE id = ?");
$q->execute([$enigmeId]);
$enigme = $q->fetch();
if (!$enigme) sendError('Énigme introuvable', 404);
$q = $pdo->prepare("SELECT id FROM progression WHERE utilisateur_id = ? AND enigme_id = ?");
$q->execute([$userId, $enigmeId]);
if (!$q->fetch()) $pdo->prepare("INSERT INTO progression (utilisateur_id, enigme_id) VALUES (?, ?)")->execute([$userId, $enigmeId]);
if (hash('sha256', strtoupper(trim($reponse))) === $enigme['solution']) {
$pdo->prepare("UPDATE progression SET resolu = 1, date_resolution = NOW() WHERE utilisateur_id = ? AND enigme_id = ?")->execute([$userId, $enigmeId]);
sendResponse(['success' => true, 'message' => 'Bonne réponse !']);
} else {
$pdo->prepare("UPDATE progression SET tentatives = tentatives + 1 WHERE utilisateur_id = ? AND enigme_id = ?")->execute([$userId, $enigmeId]);
sendError('Mauvaise réponse', 400);
}
break;

case 'use_hint':
if ($method !== 'POST') sendError('Method not allowed', 405);
if (!estConnecte()) sendError('Non authentifié', 401);
$enigmeId = $input['enigme_id'] ?? 0;
$indiceNum = $input['indice_num'] ?? 0;
if (!$enigmeId || !$indiceNum) sendError('Données manquantes');
$pdo->prepare("UPDATE progression SET indices_utilises = ? WHERE utilisateur_id = ? AND enigme_id = ?")->execute([$indiceNum, $_SESSION['utilisateur_id'], $enigmeId]);
sendResponse(['success' => true]);
break;

case 'leaderboard':
if ($method !== 'GET') sendError('Method not allowed', 405);
$leaderboard = $pdo->query("
SELECT u.pseudo, COUNT(p.id) as enigmes_resolues, SUM(e.points) as score_total
FROM utilisateurs u
LEFT JOIN progression p ON u.id = p.utilisateur_id AND p.resolu = 1
LEFT JOIN enigmes e ON p.enigme_id = e.id
GROUP BY u.id
ORDER BY score_total DESC, enigmes_resolues DESC
LIMIT 10
")->fetchAll();
sendResponse(['leaderboard' => $leaderboard]);
break;

case 'stats':
if ($method !== 'GET') sendError('Method not allowed', 405);
if (!estConnecte()) sendError('Non authentifié', 401);
$q = $pdo->prepare("
SELECT COUNT(CASE WHEN resolu = 1 THEN 1 END) as enigmes_resolues, SUM(e.points) as score_total
FROM progression p
LEFT JOIN enigmes e ON p.enigme_id = e.id
WHERE p.utilisateur_id = ?
");
$q->execute([$_SESSION['utilisateur_id']]);
sendResponse(['stats' => $q->fetch()]);
break;

default:
sendError('Endpoint introuvable', 404);
}
?>