<?php
// El "marco" que se repite en TODAS las páginas: menú de arriba y pie de página.
// Así no copiamos el mismo HTML cinco veces.

// Atajo para escribir menos: e($x) = htmlspecialchars($x)
// (protege la página si alguien guarda texto con < o > en la base)
function e($texto): string
{
    return htmlspecialchars((string) $texto);
}

// Convierte 2026-01-12 en 12/01/2026
function fecha($valor, bool $conHora = false): string
{
    if (!$valor) {
        return '—';
    }
    return date($conHora ? 'd/m/Y H:i' : 'd/m/Y', strtotime($valor));
}

// Parte de arriba de la página. $activo = cuál botón del menú se ve resaltado.
function encabezado(string $titulo, string $activo): void
{
    $menu = [
        'inicio'   => ['index.php',    'Inicio'],
        'usuarios' => ['usuarios.php', 'Usuarios'],
        'equipos'  => ['equipos.php',  'Equipos'],
        'acuerdos' => ['acuerdos.php', 'Acuerdos'],
        'actas'    => ['actas.php',    'Actas'],
    ];
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= e($titulo) ?> · Helpdesk</title>
  <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
  <header class="barra">
    <div class="contenedor barra-interna">
      <a class="logo" href="index.php">🖥️ Helpdesk</a>
      <nav>
        <?php foreach ($menu as $clave => [$enlace, $texto]): ?>
          <a href="<?= $enlace ?>" class="<?= $clave === $activo ? 'activo' : '' ?>"><?= $texto ?></a>
        <?php endforeach; ?>
      </nav>
    </div>
  </header>
  <main class="contenedor">
    <h1><?= e($titulo) ?></h1>
<?php
}

// Parte de abajo de la página.
function pie(): void
{
    ?>
  </main>
  <footer class="pie">Proyecto Helpdesk · PHP + PostgreSQL (Neon)</footer>
</body>
</html>
<?php
}
