<?php
require_once __DIR__ . '/db.php';

$totales = [
    ['usuarios.php',                 '👤', 'Usuarios',          db()->query("SELECT COUNT(*) FROM usuarios")->fetchColumn()],
    ['equipos.php',                  '💻', 'Equipos',           db()->query("SELECT COUNT(*) FROM equipos")->fetchColumn()],
    ['acuerdos_responsabilidad.php', '📝', 'Acuerdos',          db()->query("SELECT COUNT(*) FROM acuerdos_responsabilidad")->fetchColumn()],
    ['actas_entrega.php',            '📦', 'Actas de entrega',  db()->query("SELECT COUNT(*) FROM actas_entrega")->fetchColumn()],
];

require 'header.php';
?>
<h1>Panel de control</h1>
<p class="sub">Gestión y control de equipos, usuarios, acuerdos y actas.</p>

<div class="tarjetas">
  <?php foreach ($totales as [$enlace, $icono, $nombre, $cantidad]): ?>
    <a class="tarjeta" href="<?= $enlace ?>">
      <div class="icono"><?= $icono ?></div>
      <div class="numero"><?= h($cantidad) ?></div>
      <div class="nombre"><?= h($nombre) ?></div>
    </a>
  <?php endforeach; ?>
</div>
<?php require 'footer.php'; ?>