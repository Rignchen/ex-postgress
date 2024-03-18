<?php

use JetBrains\PhpStorm\NoReturn;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

#[NoReturn] function output(array $messages, int $code = 200): void {
	header('Content-Type: application/json');
	http_response_code($code);
	echo json_encode($messages);
	exit;
}
function checkToken(string $token, string $secret, array $allowed = []): object {
	try {
		$decoded = JWT::decode($token, new Key($secret, 'HS256'));
	}
	catch (SignatureInvalidException|DomainException|UnexpectedValueException $e) {
		match ($e->getMessage()) {
			'Expired token' => output(['error' => 'The token has expired'], 401),
			default => output(['error' => 'Invalid authentication token'], 401),
		};
	}
	if (!empty(array_intersect($allowed)) && !(isset($decoded->role) && in_array($decoded->role, $allowed)))
		output(['error' => 'Forbidden'], 403);
	return $decoded;
}
