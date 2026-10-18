<?php require_once 'db.php';
$self = 'equipos.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['toggle'])) {
            db()->prepare("UPDATE equipos SET estado = NOT estado WHERE id_equipo = ?")->execute([(int)$_POST['toggle']]);
            redirect($self, 'Estado actualizado');
        } elseif (isset($_POST['eliminar'])) {
            db()->prepare("DELETE FROM equipos WHERE id_equipo = ?")->execute([(int)$_POST['eliminar']]);
            redirect($self, 'Equipo eliminado');
        } elseif (!empty($_POST['id_equipo'])) {
            $st = db()->prepare("UPDATE equipos SET placa_equipo = ?, serial_equipo = ?, observaciones = ?, estado = ? WHERE id_equipo = ?");
            $st->execute([trim($_POST['placa_equipo']), trim($_POST['serial_equipo']), trim($_POST['observaciones']), $_POST['estado'], (int)$_POST['id_equipo']]);
            redirect($self, 'Equipo actualizado');
        } else {
            $st = db()->prepare("INSERT INTO equipos (placa_equipo, serial_equipo, observaciones, estado) VALUES (?,?,?,?)");
            $st->execute([trim($_POST['placa_equipo']), trim($_POST['serial_equipo']), trim($_POST['observaciones']), $_POST['estado']]);
            redirect($self, 'Equipo creado correctamente');
        }
    } catch (PDOException $e) {
        $c = $e->getCode();
        redirect($self, $c === '23505' ? 'Error: ya existe un equipo con esa placa o serial'
                     : ($c === '23503' ? 'Error: no se puede eliminar, el equipo tiene actas de entrega'
                     : 'Error al guardar'));
    }
}

$edit = null;
if (isset($_GET['editar'])) {
    $s = db()->prepare("SELECT * FROM equipos WHERE id_equipo = ?");
    $s->execute([(int)$_GET['editar']]);
    $edit = $s->fetch() ?: null;
}
$editActivo = $edit ? in_array($edit['estado'], [true, 't', 'true', '1', 1], true) : true;

require 'header.php';
$rows = db()->query("SELECT * FROM equipos ORDER BY id_equipo")->fetchAll();
?>
<h1>Equipos</h1>
<p class="sub">Inventario de equipos de cómputo.</p>

<form method="post" class="card <?= $edit ? 'editando' : '' ?>">
  <?php if ($edit): ?>
    <input type="hidden" name="id_equipo" value="<?= (int)$edit['id_equipo'] ?>">
    <p class="aviso error full">✏️ Editando equipo #<?= (int)$edit['id_equipo'] ?></p>
  <?php endif; ?>
  <label>Placa <input name="placa_equipo" required value="<?= h($edit['placa_equipo'] ?? '') ?>"></label>
  <label>Serial <input name="serial_equipo" value="<?= h($edit['serial_equipo'] ?? '') ?>"></label>
  <label>Observaciones <input name="observaciones" value="<?= h($edit['observaciones'] ?? '') ?>"></label>
  <label>Estado <select name="estado">
    <option value="true"  <?= $editActivo ? 'selected' : '' ?>>Activo</option>
    <option value="false" <?= !$editActivo ? 'selected' : '' ?>>Inactivo</option>
  </select></label>
  <div class="botones">
    <button><?= $edit ? '💾 Guardar cambios' : '➕ Insertar equipo' ?></button>
    <?php if ($edit): ?><a class="btn sec" href="<?= $self ?>">Cancelar</a><?php endif; ?>
  </div>
</form>

<div class="scroll"><table>
<tr><th>#</th><th>Placa</th><th>Serial</th><th>Observaciones</th><th>Estado</th><th>Acciones</th></tr>
<?php foreach ($rows as $r):
  $activo = in_array($r['estado'], [true, 't', 'true', '1', 1], true); ?>
<tr>
  <td><?= $r['id_equipo'] ?></td>
  <td><strong><?= h($r['placa_equipo']) ?></strong></td>
  <td><?= h($r['serial_equipo']) ?: '—' ?></td>
  <td><?= h($r['observaciones']) ?: '—' ?></td>
  <td><form method="post">
    <button class="etiqueta <?= $activo ? 'verde' : 'gris' ?>" name="toggle" value="<?= $r['id_equipo'] ?>" title="Clic para cambiar estado">
      <?= $activo ? 'Activo' : 'Inactivo' ?>
    </button></form></td>
  <td><div class="acciones">
    <a class="btn sec chico" href="<?= $self ?>?editar=<?= $r['id_equipo'] ?>">✏️ Editar</a>
    <form method="post" onsubmit="return confirm('¿Eliminar este equipo?')">
      <button class="peligro chico" name="eliminar" value="<?= $r['id_equipo'] ?>">🗑 Eliminar</button>
    </form>
  </div></td>
</tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="6" class="vacio">Sin registros.</td></tr><?php endif; ?>
</table></div>
<?php require 'footer.php'; ?>