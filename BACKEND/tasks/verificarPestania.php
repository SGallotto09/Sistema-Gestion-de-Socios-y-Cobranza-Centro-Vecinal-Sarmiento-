<?php

session_start();

header("Content-Type: application/json");

$input= json_decode(file_get_contents("php://input"), true);

$tokenPestania = $input["tokenPestania"] ?? null;

if (!isset($_SESSION["id"]) ||!isset($_SESSION["token_pestania"])) {
    echo json_encode([
        "valida" => false
    ]);

    exit;
}

if (!hash_equals($_SESSION["token_pestania"], $tokenPestania)) {
    echo json_encode([
        "valida" => false
    ]);

    exit;
}

echo json_encode([
    "valida" => true
]);