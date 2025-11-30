<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/session.php';
require_auth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: panel.php?status=ID inválido');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $csrf = $_POST['csrf'] ?? '';
    if (!csrf_validate($csrf)) {
        $msg = 'Solicitud no válida (CSRF).';
    } else {
        if (isset($_POST['confirm']) && $_POST['confirm'] === 'si') {
            $stmt = $pdo->prepare('DELETE FROM medicamentos WHERE id = ?');
            $stmt->execute([$id]);
            header('Location: panel.php?status=Eliminación exitosa');
            exit;
        } else {
            header('Location: panel.php?status=Eliminación cancelada');
            exit;
        }
    }
}

$stmt = $pdo->prepare('SELECT nombre FROM medicamentos WHERE id = ?');
$stmt->execute([$id]);
$med = $stmt->fetch();
if (!$med) {
    header('Location: panel.php?status=Medicamento no encontrado');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<link rel="icon" type="image/png" href="assets/img/salud total.png">
<title>Eliminar medicamento | Salud Total</title>
<link rel="stylesheet" href="assets/styles.css">
</head>
<body>
<div class="encabezado" style="display: flex; align-items: center; gap: 12px; padding: 16px; background-color: var(--card); border-bottom: 1px solid var(--border); box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
    <img src="assets/img/salud total.png" alt="logo" height="80" class="me-2">
    <h1>SALUD TOTAL</h1>
</div>
<div class="container">
<div class="card" style="max-width:600px;margin:0 auto;">
    <h1>Confirmar eliminación</h1>
    <?php if ($msg): ?><div class="alert alert-error"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <p>¿Deseas eliminar definitivamente el medicamento <strong><?= htmlspecialchars($med['nombre']) ?></strong>?</p>
    <form method="post">
    <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
    <div class="actions">
        <button class="btn btn-danger" name="confirm" value="si" type="submit">Sí, eliminar</button>
        <a class="btn btn-outline" href="panel.php">No, regresar</a>
    </div>
    </form>
</div>
</div>
</body>
</html>
