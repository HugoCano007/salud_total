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
        if (isset($_POST['confirm']) && $_POST['confirm'] === 'sí') {
            $stmt = $pdo->prepare('DELETE FROM medicamentos WHERE id = ?');
            $stmt->execute([$id]);
            header('Location: panel.php?status=Eliminación exitosa');
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
<html lang="ES">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Medicamento | Salud Total</title>
    <link rel="stylesheet" href="assets/styles.css">
</head>
<body>
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