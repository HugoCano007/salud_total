<?php
declare(strict_types=1);

session_name('salud_total_sid');
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => false, // true si usas HTTPS
    'cookie_samesite' => 'Lax'
]);

// Regeneración de ID tras login para evitar fijación de sesión
function secure_regenerate_session_id(): void {
    if (!isset($_SESSION['regenerated'])) {
        session_regenerate_id(true);
        $_SESSION['regenerated'] = true;
    }
}

// Helper: verificar sesión activa
function require_auth(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php?msg=Debes iniciar sesión');
        exit;
    }
}

// CSRF sencillo
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}
function csrf_validate(string $token): bool {
    return isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
}
