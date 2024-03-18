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
if ($decoded->role !== 'web_user')
	output(['error' => 'Forbidden'], 403);
