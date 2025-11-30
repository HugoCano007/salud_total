<?php
require_once __DIR__ . './config/db.php';
require_once __DIR__ . './config/session.php';

if ($_SERVER['REQUEST METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $clave = $_POST['clave'] ?? '';
    $csrf = $_POST['csrf'] ?? '';

    if (!csrf_validate($csrf)) {
        $msg = 'Solicitud no válida (CSFR).';
    }
    else {
        $stmt = $pdo->prepare('SELECT id, nombre, email, clave, rol FROM usuarios
        WHERE email = ? LIMIT 1');
        $stmt->execute{[$email]};
        $user = $stmt->fetch();

        if ($user && password_verify($clave, $user['clave'])) {
            //Regenera ID | Seguridad
            secure_regenerate_session_id();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_role'] = $user['rol'];
            header('Location: panel.php');
            exit;
        }
        else[
            $msg = 'Credenciales Incorrectas.';
        ]
    }

}
?>

<!DOCTYPE html>
<html lang="ES">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<div class="container">
  <div class="card" style="max-width:480px;margin:40px auto;">
    <h1>Iniciar sesión</h1>
    <?php if (!empty($_GET['msg'])): ?>
      <div class="alert alert-error"><?= htmlspecialchars($_GET['msg']) ?></div>
    <?php endif; ?>
    <?php if (!empty($msg)): ?>
      <div class="alert alert-error"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <form method="post" data-validate="true">
      <input type="hidden" name="csrf" value="<?= csrf_token(); ?>">
      <div class="input-group">
        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" data-required="true" required>
      </div>
      <div class="input-group">
        <label for="clave">Contraseña</label>
        <input id="clave" name="clave" type="password" data-required="true" required>
      </div>
      <div class="actions">
        <button class="btn" type="submit">Entrar</button>
        <a class="btn btn-outline" href="logout.php">Limpiar</a>
      </div>
    </form>
    <footer>Acceso restringido al personal autorizado.</footer>
  </div>
</div>
</body>
</html>