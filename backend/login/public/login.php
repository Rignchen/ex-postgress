<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\Dotenv;

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

