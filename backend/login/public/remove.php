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
checkToken($token, $_ENV['JWT_SECRET'], ['web_user']);

// Check if data has been sent
$data = match ($_SERVER['REQUEST_METHOD']) {
    'GET' => $_GET,
    'POST' => $_POST,
    default => output(['error' => 'Unsupported method'], 405),
};

if (!isset($data['username']))
    output(['error' => 'The username of the user to remove is required'], 400);
