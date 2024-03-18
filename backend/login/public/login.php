<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../private/lib.php';

use Symfony\Component\Dotenv;
use Firebase\JWT\JWT;

$data = match ($_SERVER['REQUEST_METHOD']) {
	'GET' => $_GET,
	'POST' => $_POST,
	default => output(['error' => 'Unsupported method'], 405),
};

if (!isset($data['username']) || !isset($data['password']))
    output(['error' => 'Username and password are required'], 400);

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

if (!$user || !password_verify($data['password'], $user['password']))
    output(['error' => 'Invalid username or password'], 401);

// Create a token
$payload = [
	"role" => "web_user",
	"exp" => time() + 3600,
	"id" => $user['id']
];
$jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

output(['token' => $jwt]);
