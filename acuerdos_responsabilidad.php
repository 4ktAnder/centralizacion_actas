<?php require_once 'db.php';
$self = 'acuerdos_responsabilidad.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['toggle'])) {
            db()->prepare("UPDATE acuerdos_responsabilidad SET estado = NOT estado WHERE id_acuerdo = ?")->execute([(int)$_POST['toggle']]);
            redirect($self, 'Estado actualizado');
        } elseif (isset($_POST['eliminar'])) {
            db()->prepare("DELETE FROM acuerdos_responsabilidad WHERE id_acuerdo = ?")->execute([(int)$_POST['eliminar']]);
            redirect($self, 'Acuerdo eliminado');
        } elseif (!empty($_POST['id_acuerdo'])) {
            $st = db()->prepare("UPDATE acuerdos_responsabilidad SET id_usuario = ?, fecha_firma = ?, evidencia_firmada = ?, estado = ? WHERE id_acuerdo = ?");
            $st->execute([(int)$_POST['id_usuario'], $_POST['fecha_firma'] ?: null, trim($_POST['evidencia_firmada']) ?: null, $_POST['estado'], (int)$_POST['id_acuerdo']]);
            redirect($self, 'Acuerdo actualizado');
        } else {
            $st = db()->prepare("INSERT INTO acuerdos_responsabilidad (id_usuario, fecha_firma, evidencia_firmada, estado) VALUES (?,?,?,?)");
            $st->execute([(int)$_POST['id_usuario'], $_POST['fecha_firma'] ?: null, trim($_POST['evidencia_firmada']) ?: null, $_POST['estado']]);
            redirect($self, 'Acuerdo creado correctamente');
        }
    } catch (PDOException $e) {
        $c = $e->getCode();
        redirect($self, $c === '23505' ? 'Error: ese usuario ya tiene un acuerdo'
                     : ($c === '23503' ? 'Error: no se puede eliminar, el acuerdo está usado en actas'
                     : 'Error al guardar'));
    }
}

$edit = null;
if (isset($_GET['editar'])) {
    $s = db()->prepare("SELECT * FROM acuerdos_responsabilidad WHERE id_acuerdo = ?");
    $s->execute([(int)$_GET['editar']]);
    $edit = $s->fetch() ?: null;
}
$editActivo = $edit ? in_array($edit['estado'], [true, 't', 'true', '1', 1], true) : true;

require 'header.php';
$usuarios = db()->query("SELECT id_usuario, nombre_completo FROM usuarios ORDER BY id_usuario")->fetchAll();
$rows = db()->query("SELECT a.*, u.nombre_completo FROM acuerdos_responsabilidad a JOIN usuarios u USING (id_usuario) ORDER BY id_acuerdo")->fetchAll();
?>
<h1>Acuerdos de responsabilidad</h1>
<p class="sub">Un acuerdo firmado por cada usuario que recibe equipos.</p>

<form method="post" class="card <?= $edit ? 'editando' : '' ?>">
  <?php if ($edit): ?>
    <input type="hidden" name="id_acuerdo" value="<?= (int)$edit['id_acuerdo'] ?>">
    <p class="aviso error full">✏️ Editando acuerdo #<?= (int)$edit['id_acuerdo'] ?></p>
  <?php endif; ?>
  <label>Usuario <select name="id_usuario" required>
    <?php foreach ($usuarios as $u): ?>
      <option value="<?= $u['id_usuario'] ?>" <?= $edit && (int)$edit['id_usuario'] === (int)$u['id_usuario'] ? 'selected' : '' ?>>
        <?= $u['id_usuario'] ?> · <?= h($u['nombre_completo']) ?></option>
    <?php endforeach; ?>
  </select></label>
  <label>Fecha de firma <input type="date" name="fecha_firma" value="<?= h($edit['fecha_firma'] ?? '') ?>"></label>
  <label>Evidencia firmada <input name="evidencia_firmada" value="<?= h($edit['evidencia_firmada'] ?? '') ?>"></label>
  <label>Estado <select name="estado">
    <option value="true"  <?= $editActivo ? 'selected' : '' ?>>Activo</option>
    <option value="false" <?= !$editActivo ? 'selected' : '' ?>>Inactivo</option>
  </select></label>
  <div class="botones">
    <button><?= $edit ? '💾 Guardar cambios' : '➕ Insertar acuerdo' ?></button>
    <?php if ($edit): ?><a class="btn sec" href="<?= $self ?>">Cancelar</a><?php endif; ?>
  </div>
</form>

<div class="scroll"><table>
<tr><th>#</th><th>Usuario</th><th>Firma</th><th>Evidencia</th><th>Estado</th><th>Acciones</th></tr>
<?php foreach ($rows as $r):
  $activo = in_array($r['estado'], [true, 't', 'true', '1', 1], true); ?>
<tr>
  <td><?= $r['id_acuerdo'] ?></td>
  <td><strong><?= h($r['nombre_completo']) ?></strong> <span class="sub">#<?= $r['id_usuario'] ?></span></td>
  <td><?= $r['fecha_firma'] ? date('d/m/Y', strtotime($r['fecha_firma'])) : '—' ?></td>
  <td><?= h($r['evidencia_firmada']) ?: '—' ?></td>
  <td><form method="post">
    <button class="etiqueta <?= $activo ? 'verde' : 'gris' ?>" name="toggle" value="<?= $r['id_acuerdo'] ?>" title="Clic para cambiar estado">
      <?= $activo ? 'Activo' : 'Inactivo' ?>
    </button></form></td>
  <td><div class="acciones">
    <a class="btn sec chico" href="<?= $self ?>?editar=<?= $r['id_acuerdo'] ?>">✏️ Editar</a>
    <form method="post" onsubmit="return confirm('¿Eliminar este acuerdo?')">
      <button class="peligro chico" name="eliminar" value="<?= $r['id_acuerdo'] ?>">🗑 Eliminar</button>
    </form>
  </div></td>
</tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="6" class="vacio">Sin registros.</td></tr><?php endif; ?>
</table></div>
<?php require 'footer.php'; ?>