<?php

// Iniciar sesión si no está iniciada
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    // Redirigir al login
    header('Location: login.php');
    exit();
}

// Opcional: Actualizar tiempo de último acceso
$_SESSION['ultimo_acceso'] = time();
?>