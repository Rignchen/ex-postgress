<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../private/lib.php';

// get the authentication token from the authorization header
$authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = str_replace('Bearer ', '', $authHeader);
