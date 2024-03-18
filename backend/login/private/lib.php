<?php

use JetBrains\PhpStorm\NoReturn;

#[NoReturn] function output(array $messages, int $code = 200): void {
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode($messages);
    exit;
}
