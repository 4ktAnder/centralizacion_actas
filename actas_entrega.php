<?php require_once 'db.php';
$self = 'actas_entrega.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['devolver'])) {
            db()->prepare("UPDATE actas_entrega SET fecha_devolucion = NOW() WHERE id_acta = ? AND fecha_devolucion IS NULL")->execute([(int)$_POST['devolver']]);
            redirect($self, 'Devolución registrada');
        } elseif (isset($_POST['eliminar'])) {
            db()->prepare("DELETE FROM actas_entrega WHERE id_acta = ?")->execute([(int)$_POST['eliminar']]);
            redirect($self, 'Acta eliminada');
        } elseif (!empty($_POST['id_acta'])) {
            $dev = !empty($_POST['fecha_devolucion']) ? str_replace('T', ' ', $_POST['fecha_devolucion']) : null;
            $st = db()->prepare("UPDATE actas_entrega SET id_usuario = ?, id_equipo = ?, id_acuerdo = ?, fecha_entrega = ?, fecha_devolucion = ?,
                                 cargo_al_momento = ?, gerencia_al_momento = ?, evidencia_firmada = ? WHERE id_acta = ?");
            $st->execute([(int)$_POST['id_usuario'], (int)$_POST['id_equipo'], $_POST['id_acuerdo'] ?: null,
                str_replace('T', ' ', $_POST['fecha_entrega']), $dev,
                trim($_POST['cargo_al_momento']), trim($_POST['gerencia_al_momento']), trim($_POST['evidencia_firmada']) ?: null,
                (int)$_POST['id_acta']]);
            redirect($self, 'Acta actualizada');
        } else {
            $st = db()->prepare("INSERT INTO actas_entrega (id_usuario, id_equipo, id_acuerdo, fecha_entrega, cargo_al_momento, gerencia_al_momento, evidencia_firmada) VALUES (?,?,?,?,?,?,?)");
            $st->execute([(int)$_POST['id_usuario'], (int)$_POST['id_equipo'], $_POST['id_acuerdo'] ?: null,
                str_replace('T', ' ', $_POST['fecha_entrega']), trim($_POST['cargo_al_momento']), trim($_POST['gerencia_al_momento']), trim($_POST['evidencia_firmada']) ?: null]);
            redirect($self, 'Acta creada correctamente');
        }
    } catch (PDOException $e) {
        $c = $e->getCode();
        redirect($self, $c === '23505' ? 'Error: ese equipo ya está entregado'
                     : ($c === '23503' ? 'Error: usuario, equipo o acuerdo no válido'
                     : 'Error al guardar'));
    }
}

$edit = null;
if (isset($_GET['editar'])) {
    $s = db()->prepare("SELECT * FROM actas_entrega WHERE id_acta = ?");
    $s->execute([(int)$_GET['editar']]);
    $edit = $s->fetch() ?: null;
}
$curEq = $edit ? (int)$edit['id_equipo'] : 0;
$curAc = $edit ? (int)($edit['id_acuerdo'] ?? 0) : 0;

require 'header.php';
$usuarios = db()->query("SELECT id_usuario, nombre_completo FROM usuarios ORDER BY id_usuario")->fetchAll();

$q = db()->prepare("SELECT id_equipo, placa_equipo FROM equipos e
  WHERE (estado AND NOT EXISTS (SELECT 1 FROM actas_entrega a WHERE a.id_equipo = e.id_equipo AND a.fecha_devolucion IS NULL))
     OR id_equipo = ? ORDER BY id_equipo");
$q->execute([$curEq]);
$equipos = $q->fetchAll();

$q = db()->prepare("SELECT a.id_acuerdo, u.nombre_completo FROM acuerdos_responsabilidad a
  JOIN usuarios u USING (id_usuario) WHERE a.estado OR a.id_acuerdo = ? ORDER BY a.id_acuerdo");
$q->execute([$curAc]);
$acuerdos = $q->fetchAll();

$rows = db()->query("SELECT a.*, u.nombre_completo, e.placa_equipo FROM actas_entrega a
  JOIN usuarios u USING (id_usuario) JOIN equipos e USING (id_equipo) ORDER BY id_acta")->fetchAll();

$fEntrega = $edit ? date('Y-m-d\TH:i', strtotime($edit['fecha_entrega'])) : date('Y-m-d\TH:i');
$fDev = ($edit && $edit['fecha_devolucion']) ? date('Y-m-d\TH:i', strtotime($edit['fecha_devolucion'])) : '';
?>
<h1>Actas de entrega</h1>
<p class="sub">Registro de cada equipo entregado y su devolución.</p>

<form method="post" class="card <?= $edit ? 'editando' : '' ?>">
  <?php if ($edit): ?>
    <input type="hidden" name="id_acta" value="<?= (int)$edit['id_acta'] ?>">
    <p class="aviso error full">✏️ Editando acta #<?= (int)$edit['id_acta'] ?></p>
  <?php endif; ?>
  <label>Usuario <select name="id_usuario" required>
    <?php foreach ($usuarios as $u): ?>
      <option value="<?= $u['id_usuario'] ?>" <?= $edit && (int)$edit['id_usuario'] === (int)$u['id_usuario'] ? 'selected' : '' ?>>
        <?= $u['id_usuario'] ?> · <?= h($u['nombre_completo']) ?></option>
    <?php endforeach; ?></select></label>
  <label>Equipo <select name="id_equipo" required>
    <?php foreach ($equipos as $e): ?>
      <option value="<?= $e['id_equipo'] ?>" <?= $curEq === (int)$e['id_equipo'] ? 'selected' : '' ?>>
        <?= $e['id_equipo'] ?> · <?= h($e['placa_equipo']) ?></option>
    <?php endforeach; ?></select></label>
  <label>Acuerdo <select name="id_acuerdo"><option value="">Sin acuerdo</option>
    <?php foreach ($acuerdos as $a): ?>
      <option value="<?= $a['id_acuerdo'] ?>" <?= $curAc === (int)$a['id_acuerdo'] ? 'selected' : '' ?>>
        <?= $a['id_acuerdo'] ?> · <?= h($a['nombre_completo']) ?></option>
    <?php endforeach; ?></select></label>
  <label>Fecha de entrega <input type="datetime-local" name="fecha_entrega" value="<?= $fEntrega ?>" required></label>
  <?php if ($edit): ?>
    <label>Fecha de devolución (vacía = en uso) <input type="datetime-local" name="fecha_devolucion" value="<?= $fDev ?>"></label>
  <?php endif; ?>
  <label>Cargo al momento <input name="cargo_al_momento" value="<?= h($edit['cargo_al_momento'] ?? '') ?>"></label>
  <label>Gerencia al momento <input name="gerencia_al_momento" value="<?= h($edit['gerencia_al_momento'] ?? '') ?>"></label>
  <label>Evidencia firmada <input name="evidencia_firmada" value="<?= h($edit['evidencia_firmada'] ?? '') ?>"></label>
  <div class="botones">
    <button><?= $edit ? '💾 Guardar cambios' : '➕ Insertar acta' ?></button>
    <?php if ($edit): ?><a class="btn sec" href="<?= $self ?>">Cancelar</a><?php endif; ?>
  </div>
</form>

<div class="scroll"><table>
<tr><th>#</th><th>Usuario</th><th>Equipo</th><th>Acuerdo</th><th>Entrega</th><th>Devolución</th><th>Estado</th><th>Cargo</th><th>Gerencia</th><th>Evidencia</th><th>Acciones</th></tr>
<?php foreach ($rows as $r): ?>
<tr>
  <td><?= $r['id_acta'] ?></td>
  <td><strong><?= h($r['nombre_completo']) ?></strong></td>
  <td><strong><?= h($r['placa_equipo']) ?></strong></td>
  <td><?= $r['id_acuerdo'] ? '#' . $r['id_acuerdo'] : '—' ?></td>
  <td><?= date('d/m/Y H:i', strtotime($r['fecha_entrega'])) ?></td>
  <td>
    <?php if ($r['fecha_devolucion']): ?>
      <?= date('d/m/Y H:i', strtotime($r['fecha_devolucion'])) ?>
    <?php else: ?>
      <form method="post" onsubmit="return confirm('¿Registrar devolución?')">
        <button class="sec chico" name="devolver" value="<?= $r['id_acta'] ?>">↩ Devolver</button>
      </form>
    <?php endif; ?>
  </td>
  <td>
    <?php if ($r['fecha_devolucion']): ?><span class="etiqueta gris">Devuelto</span>
    <?php else: ?><span class="etiqueta naranja">En uso</span><?php endif; ?>
  </td>
  <td><?= h($r['cargo_al_momento']) ?: '—' ?></td>
  <td><?= h($r['gerencia_al_momento']) ?: '—' ?></td>
  <td><?= h($r['evidencia_firmada']) ?: '—' ?></td>
  <td><div class="acciones">
    <a class="btn sec chico" href="<?= $self ?>?editar=<?= $r['id_acta'] ?>">✏️ Editar</a>
    <form method="post" onsubmit="return confirm('¿Eliminar esta acta?')">
      <button class="peligro chico" name="eliminar" value="<?= $r['id_acta'] ?>">🗑 Eliminar</button>
    </form>
  </div></td>
</tr>
<?php endforeach; if (!$rows): ?><tr><td colspan="11" class="vacio">Sin registros.</td></tr><?php endif; ?>
</table></div>
<?php require 'footer.php'; ?>