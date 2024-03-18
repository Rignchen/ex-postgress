<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../private/lib.php';

use Symfony\Component\Dotenv;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

// get the authentication token from the authorization header
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = str_replace('Bearer ', '', $authHeader);

// Load environment variables
$dotenv = new Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/../.env');

// Decode the token
try {
	$decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));
}
catch (SignatureInvalidException|DomainException|UnexpectedValueException $e) {
	match ($e->getMessage()) {
		'Expired token' => output(['error' => 'The token has expired'], 401),
		default => output(['error' => 'Invalid authentication token'], 401),
	};
}
if (!isset($decoded->role) || $decoded->role !== 'web_user')
	output(['error' => 'Forbidden'], 403);

// Check if data has been sent
$data = match ($_SERVER['REQUEST_METHOD']) {
	'GET' => $_GET,
	'POST' => $_POST,
	default => output(['error' => 'Unsupported method'], 405),
};

if (!isset($data['username']) || !isset($data['password']))
	output(['error' => 'Username and password are required'], 400);

// Connect to the database
$dns = "pgsql:host=" . $_ENV['DB_HOST'] . ";port=" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_NAME'];
$pdo = new PDO($dns, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

// Create a user
$stmt = $pdo->prepare("INSERT INTO api.users (username, password) VALUES (:username, :password)");
try {
	$stmt->execute([
		'username' => $data['username'],
		'password' => password_hash($data['password'], PASSWORD_DEFAULT),
	]);
}
catch (PDOException $e) {
	output(['error' => 'Username already exists'], 400);
}

output(['message' => 'User created']);
