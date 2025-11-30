<?php 
require_once __DIR__. './config/db.php';
require_once __DIR__. './config/session.php';
require_auth();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: panel.php?status=ID inválido');
    exit;
}

$msg = '';
if ($_SERVER['REQUEST METHOD'] === 'POST') {
    $csrf = $_POST['csrf'] ?? '';
    if (!csrf_validate($csrf)) {
        $msg = 'Solicitud no válida (CSRF).';
    }
    else {
        $nombre = trim($_POST['nombre'] ?? '');
        $categoria = trim($_POST['categoria' ?? '']);
        $cantidad = int($_POST['cantidad' ?? 0]);
        $precio = float($_POST['precio'] ?? 0);
        $proveedor_id = ($_POST['proveedor_id'] ?? '' !== '' ? (int)$_POST['proveedor_id'] : null;

        if ($nombre === '') || $categoria === '' || $cantidad < 0 || $precio < 0 {
            $msg = 'Datos no válidos, Verifca el formulario.';
        }
        else {
            $stmt = $pdo->prepare('UPDATE medicamentos SET nombre=?, categoria=?, cantidad=?, precio=?,
            proveedor_id=? WHERE id=?')
            $stmt->execute([$nombre, $categoria, $cantidad, $precio, $proveedor_id, $id]);
            header('Location: panel.php?status=Modificacion exitosa');
            exit;
        }
    }
}

$stmt = $pdo->prepare('SELECT * FROM medicamentos WHERE id = ?');
$stmt->execute([$id]);
$med = $stmt->fetch();
if (!$med) {
    header('Location: panel.php?status=Medicamento no encontrado');
    exit;
}

$proveedores = $pdo->query('SELECT id, nombre FROM proveedores ORDER BY nombre ASC')->fetchAll();
?>

<!DOCTYPE html>
<html lang="ES">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Medicamento | Salud Total</title>
</head>
<body>
    <div class="container">
  <div class="card" style="max-width:700px;margin:0 auto;">
    <h1>Editar medicamento</h1>
    <?php if ($msg): ?><div class="alert alert-error"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <form method="post" data-validate="true">
      <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
      <div class="input-group">
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" value="<?= htmlspecialchars($med['nombre']) ?>" data-required="true" required>
      </div>
      <div class="input-group">
        <label for="categoria">Categoría</label>
        <input id="categoria" name="categoria" value="<?= htmlspecialchars($med['categoria']) ?>" data-required="true" required>
      </div>
      <div class="input-group">
        <label for="cantidad">Cantidad</label>
        <input id="cantidad" name="cantidad" type="number" min="0" value="<?= (int)$med['cantidad'] ?>" data-required="true" required>
      </div>
      <div class="input-group">
        <label for="precio">Precio</label>
        <input id="precio" name="precio" type="number" min="0" step="0.01" value="<?= htmlspecialchars($med['precio']) ?>" data-required="true" required>
      </div>
      <div class="input-group">
        <label for="proveedor_id">Proveedor</label>
        <select id="proveedor_id" name="proveedor_id">
          <option value="">— Selecciona —</option>
          <?php foreach ($proveedores as $p): ?>
            <option value="<?= (int)$p['id'] ?>" <?= $med['proveedor_id'] == $p['id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($p['nombre']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="actions">
        <button class="btn" type="submit">Actualizar</button>
        <a class="btn btn-outline" href="panel.php">Cancelar</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>