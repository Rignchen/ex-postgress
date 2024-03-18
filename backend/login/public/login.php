<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$data = match ($_SERVER['REQUEST_METHOD']) {
	'GET' => $_GET,
	'POST' => $_POST,
	default => [],
};

if (!isset($data['username']) || !isset($data['password'])) {
	http_response_code(400);
	echo json_encode(['error' => 'Username and password are required']);
	exit;
}

// Load environment variables
$dotenv = new Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/../.env');

// Connect to the database
$dns = "pgsql:host=" . $_ENV['DB_HOST'] . ";port=" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_NAME'];
$pdo = new PDO($dns, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

// Check if the password is correct
$stmt = $pdo->prepare("SELECT * FROM api.users WHERE username = :username");
$stmt->execute(['username' => $data['username']]);
$user = $stmt->fetch();

if (!$user || !password_verify($data['password'], $user['password'])) {
	http_response_code(401);
	echo json_encode(['error' => 'Invalid username or password']);
	exit;
}

// Create a token
$payload = [
	"role" => "web_user",
	"exp" => time() + 3600,
	"id" => $user['id']
];
$jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

echo json_encode(['token' => $jwt]);
