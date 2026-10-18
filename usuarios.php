<?php require_once 'db.php';
$self = 'usuarios.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['eliminar'])) {
            db()->prepare("DELETE FROM usuarios WHERE id_usuario = ?")->execute([(int)$_POST['eliminar']]);
            redirect($self, 'Usuario eliminado');
        } elseif (!empty($_POST['id_usuario'])) {
            $st = db()->prepare("UPDATE usuarios SET nombre_completo = ?, correo = ?, documento_identidad = ? WHERE id_usuario = ?");
            $st->execute([trim($_POST['nombre_completo']), trim($_POST['correo']), trim($_POST['documento_identidad']) ?: null, (int)$_POST['id_usuario']]);
            redirect($self, 'Usuario actualizado');
        } else {
            $st = db()->prepare("INSERT INTO usuarios (nombre_completo, correo, documento_identidad) VALUES (?,?,?)");
            $st->execute([trim($_POST['nombre_completo']), trim($_POST['correo']), trim($_POST['documento_identidad']) ?: null]);
            redirect($self, 'Usuario creado correctamente');
        }
    } catch (PDOException $e) {
        $c = $e->getCode();
        redirect($self, $c === '23505' ? 'Error: el documento de identidad ya existe'
                     : ($c === '23503' ? 'Error: no se puede eliminar, el usuario tiene acuerdos o actas'
                     : 'Error al guardar'));
    }
}

$edit = null;
if (isset($_GET['editar'])) {
    $s = db()->prepare("SELECT * FROM usuarios WHERE id_usuario = ?");
    $s->execute([(int)$_GET['editar']]);
    $edit = $s->fetch() ?: null;
}

require 'header.php';
$rows = db()->query("SELECT * FROM usuarios ORDER BY id_usuario")->fetchAll();
?>
<h1>Usuarios</h1>
<p class="sub">Personas a las que se les asignan equipos.</p>

<form method="post" class="card <?= $edit ? 'editando' : '' ?>">
  <?php if ($edit): ?>
    <input type="hidden" name="id_usuario" value="<?= (int)$edit['id_usuario'] ?>">
    <p class="aviso error full">✏️ Editando usuario #<?= (int)$edit['id_usuario'] ?></p>
  <?php endif; ?>
  <label>Nombre completo <input name="nombre_completo" required value="<?= h($edit['nombre_completo'] ?? '') ?>"></label>
  <label>Correo <input name="correo" type="email" required value="<?= h($edit['correo'] ?? '') ?>"></label>
  <label>Documento de identidad <input name="documento_identidad" value="<?= h($edit['documento_identidad'] ?? '') ?>"></label>
  <div class="botones">
    <button><?= $edit ? '💾 Guardar cambios' : '➕ Insertar usuario' ?></button>
    <?php if ($edit): ?><a class="btn sec" href="<?= $self ?>">Cancelar</a><?php endif; ?>
  </div>
</form>

<div class="scroll"><table>
<tr><th>#</th><th>Nombre</th><th>Correo</th><th>Documento</th><th>Acciones</th></tr>
<?php foreach ($rows as $r): ?>
<tr>
  <td><?= $r['id_usuario'] ?></td>
  <td><strong><?= h($r['nombre_completo']) ?></strong></td>
  <td><?= h($r['correo']) ?></td>
  <td><?= h($r['documento_identidad']) ?: '—' ?></td>
  <td><div class="acciones">
    <a class="btn sec chico" href="<?= $self ?>?editar=<?= $r['id_usuario'] ?>">✏️ Editar</a>
    <form method="post" onsubmit="return confirm('¿Eliminar este usuario?')">
      <button class="peligro chico" name="eliminar" value="<?= $r['id_usuario'] ?>">🗑 Eliminar</button>
    </form>
  </div></td>
</tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="5" class="vacio">Sin registros.</td></tr><?php endif; ?>
</table></div>
<?php require 'footer.php'; ?>