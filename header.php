<?php
require_once __DIR__ . '/db.php';
$pag = basename($_SERVER['SCRIPT_NAME'], '.php');
$menu = [
    'index'                    => 'Inicio',
    'usuarios'                 => 'Usuarios',
    'equipos'                  => 'Equipos',
    'acuerdos_responsabilidad' => 'Acuerdos',
    'actas_entrega'            => 'Actas',
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= h($menu[$pag] ?? $pag) ?> · Helpdesk</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<header class="barra">
  <div class="contenedor barra-interna">
    <a class="logo" href="index.php">🖥 Helpdesk</a>
    <nav>
      <?php foreach ($menu as $archivo => $texto): ?>
        <a href="<?= $archivo ?>.php" class="<?= $pag === $archivo ? 'activo' : '' ?>"><?= $texto ?></a>
      <?php endforeach; ?>
    </nav>
  </div>
</header>
<main class="contenedor">
<?php if (!empty($_GET['msg'])): ?><p class="aviso"><?= h($_GET['msg']) ?></p><?php endif; ?>