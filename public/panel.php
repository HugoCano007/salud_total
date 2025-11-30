<?php
require_once __DIR__. './config/db.php';
require_once __DIR__. './config/sessions.php';
reuqire_auth();

$categoria = trim($_GET['categoria'] ?? '');
$query = 'SELECT m.id, m.nombre, m.categoria, m.cantidad, m.precio, p.nombre
AS proveedor FROM medicamentos m
LEFT JOIN proveedores p ON p.id = m.proveedor_id';
$params = [];
if ($categoria !== '') {
    $query .= ' WHERE m.categoria = ?';
    $params[] = $categoria;
}
$query .= ' ORDER BY m.nombre ASC';
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$medicamentos = $stmt->fetchAll();

//Filtro de Categorias Únicas
$catsStmt = $pdo->query('SELECT DISTINCT categoria FROM medicamentos ORDER BY categoria ASC');
$categorias = $catsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ES">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario | Salud Total</title>
</head>
<body>
    <div class="container">
  <div class="header">
    <h1>Inventario de medicamentos</h1>
    <div>
      <span>Hola, <?= htmlspecialchars($_SESSION['user_name']) ?> (<?= htmlspecialchars($_SESSION['user_role']) ?>)</span>
      <a class="btn btn-outline" href="logout.php">Cerrar sesión</a>
    </div>
  </div>

  <?php if (!empty($_GET['status'])): ?>
    <div class="alert alert-success"><?= htmlspecialchars($_GET['status']) ?></div>
  <?php endif; ?>

  <div class="card">
    <div class="filter-bar">
      <form method="get">
        <label for="categoria">Filtrar por categoría:</label>
        <select name="categoria" id="categoria">
          <option value="">Todas</option>
          <?php foreach ($categorias as $c): ?>
            <option value="<?= htmlspecialchars($c['categoria']) ?>"
              <?= $categoria === $c['categoria'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($c['categoria']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <button class="btn btn-outline" type="submit">Aplicar</button>
      </form>
      <a class="btn btn-success" href="registro.php">Registrar medicamento</a>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>Nombre</th>
          <th>Categoría</th>
          <th>Cantidad</th>
          <th>Precio</th>
          <th>Proveedor</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$medicamentos): ?>
          <tr><td colspan="6">No hay registros disponibles.</td></tr>
        <?php else: foreach ($medicamentos as $m): ?>
          <tr>
            <td><?= htmlspecialchars($m['nombre']) ?></td>
            <td><?= htmlspecialchars($m['categoria']) ?></td>
            <td><?= (int)$m['cantidad'] ?></td>
            <td>$<?= number_format((float)$m['precio'], 2) ?></td>
            <td><?= htmlspecialchars($m['proveedor'] ?? '—') ?></td>
            <td class="actions">
              <a class="btn btn-outline" href="editar.php?id=<?= (int)$m['id'] ?>">Editar</a>
              <a class="btn btn-danger" href="eliminar.php?id=<?= (int)$m['id'] ?>">Eliminar</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>