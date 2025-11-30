<?php
require_once __DIR__ . '/../config/session.php';
// Cerrar sesión
session_unset();
session_destroy();
// Redirigir al login con mensaje
echo "Sesión cerrada. Redirigiendo...";
header('Location: login.php?msg=Sesión cerrada');
exit;