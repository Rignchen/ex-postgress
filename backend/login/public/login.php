<?php
require_once __DIR__ . '/../vendor/autoload.php';

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
