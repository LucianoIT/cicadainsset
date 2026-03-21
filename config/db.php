<?php
$host = getenv('DB_HOST') ?: 'mysql';
$dbname = getenv('DB_NAME') ?: 'cicada';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: 'root';

try {
$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
http_response_code(500);
echo "ERREUR_BDD : " . $e->getMessage();
exit();
}

if (session_status() === PHP_SESSION_NONE) session_start();

function estConnecte() {
return isset($_SESSION['utilisateur_id']);
}
?>