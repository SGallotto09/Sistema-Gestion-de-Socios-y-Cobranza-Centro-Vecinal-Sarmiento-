<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['cobrador_autenticado']) || 
    $_SESSION['cobrador_autenticado'] !== true || 
    !isset($_SESSION['id_cobrador']) ||
    !filter_var($_SESSION['id_cobrador'], FILTER_VALIDATE_INT)) {
        
    http_response_code(403);
    exit('Acceso no autorizado.');
}

?>