<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../private/lib.php';

use Symfony\Component\Dotenv;

// get the authentication token from the authorization header
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = str_replace('Bearer ', '', $authHeader);

// Load environment variables
$dotenv = new Dotenv\Dotenv();
$dotenv->load(__DIR__ . '/../.env');

// Decode the token
$decoded = checkToken($token, $_ENV['JWT_SECRET'], ['web_user']);

// Check if data has been sent
$data = match ($_SERVER['REQUEST_METHOD']) {
    'GET' => $_GET,
    'POST' => $_POST,
    default => output(['error' => 'Unsupported method'], 405),
};

if (!isset($data['username']))
    output(['error' => 'The username of the user to remove is required'], 400);

// Connect to the database
$dns = "pgsql:host=" . $_ENV['DB_HOST'] . ";port=" . $_ENV['DB_PORT'] . ";dbname=" . $_ENV['DB_NAME'];
$pdo = new PDO($dns, $_ENV['DB_USER'], $_ENV['DB_PASSWORD']);

// Check if the user exists
$stmt = $pdo->prepare("SELECT * FROM api.users WHERE username = :username");
$stmt->execute(['username' => $data['username']]);
$user = $stmt->fetch();

if (!$user)
    output(['error' => 'User not found'], 404);

// Check if user isn't trying to remove themselves
if ($user['id'] == $decoded->id)
    output(['error' => 'You cannot remove yourself'], 400);
