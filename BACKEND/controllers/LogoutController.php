<?php

session_start();

session_unset();
session_destroy();

http_response_code(200);

echo json_encode([
    'message' => 'Sesión cerrada correctamente.'
]);

?>