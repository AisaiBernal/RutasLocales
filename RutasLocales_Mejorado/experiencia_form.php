<?php
$titulo = 'Experiencia';
require 'includes/header.php';
require_rol('guia');
$u = usuario();
$id = (int)($_GET['id'] ?? 0);
$d = ['titulo' => '', 'id_categoria' => '', 'descripcion' => '', 'precio' => '', 'ubicacion' => '', 'duracion_horas' => 3, 'cupos_disponibles' => 10, 'imagen_url' => ''];

if ($id) {   // modo editar: solo si es del guía
    $s = $pdo->prepare("SELECT * FROM experiencias WHERE id_experiencia = ? AND id_guia = ?");
    $s->execute([$id, $u['id']]);
    $d = $s->fetch() ?: redirect('mis_experiencias.php');
}
$errores = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $d = [
        'titulo' => trim($_POST['titulo'] ?? ''), 'id_categoria' => (int)($_POST['id_categoria'] ?? 0),
        'descripcion' => trim($_POST['descripcion'] ?? ''), 'precio' => $_POST['precio'] ?? '',
        'ubicacion' => trim($_POST['ubicacion'] ?? ''), 'duracion_horas' => (int)($_POST['duracion_horas'] ?? 0),
        'cupos_disponibles' => (int)($_POST['cupos_disponibles'] ?? 0), 'imagen_url' => trim($_POST['imagen_url'] ?? ''),
    ];
    if (mb_strlen($d['titulo']) < 5 || mb_strlen($d['titulo']) > 150) $errores[] = 'El título debe tener entre 5 y 150 caracteres.';
    if (mb_strlen($d['descripcion']) < 30)                             $errores[] = 'La descripción debe tener al menos 30 caracteres.';
    if (!is_numeric($d['precio']) || $d['precio'] < 1000)              $errores[] = 'El precio debe ser mínimo $1.000.';
    if ($d['ubicacion'] === '')                                        $errores[] = 'Indica la ubicación.';
    if ($d['duracion_horas'] < 1 || $d['duracion_horas'] > 72)         $errores[] = 'La duración debe estar entre 1 y 72 horas.';
    if ($d['cupos_disponibles'] < 0 || $d['cupos_disponibles'] > 500)  $errores[] = 'Los cupos deben estar entre 0 y 500.';
    if ($d['imagen_url'] !== '' && !preg_match('#^https?://#i', $d['imagen_url'])) $errores[] = 'La imagen debe ser una URL http(s).';
    if (!$errores) {
        $cat = $d['id_categoria'] ?: null; $img = $d['imagen_url'] ?: null;
        if ($id) {
            $pdo->prepare("UPDATE experiencias SET titulo=?,id_categoria=?,descripcion=?,precio=?,ubicacion=?,duracion_horas=?,cupos_disponibles=?,imagen_url=? WHERE id_experiencia=? AND id_guia=?")
                ->execute([$d['titulo'], $cat, $d['descripcion'], $d['precio'], $d['ubicacion'], $d['duracion_horas'], $d['cupos_disponibles'], $img, $id, $u['id']]);
            flash('Experiencia actualizada.');
        } else {
            $pdo->prepare("INSERT INTO experiencias (id_guia,titulo,id_categoria,descripcion,precio,ubicacion,duracion_horas,cupos_disponibles,imagen_url) VALUES (?,?,?,?,?,?,?,?,?)")
                ->execute([$u['id'], $d['titulo'], $cat, $d['descripcion'], $d['precio'], $d['ubicacion'], $d['duracion_horas'], $d['cupos_disponibles'], $img]);
            flash('Experiencia publicada.');
        }
        redirect('mis_experiencias.php');
    }
}
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll();
?>
<main class="contenedor estrecho">
    <h1 class="titulo-pagina"><?= $id ? 'Editar experiencia' : 'Nueva experiencia' ?></h1>
    <?php foreach ($errores as $er): ?><div class="flash flash-error"><?= e($er) ?></div><?php endforeach; ?>
    <form method="POST" class="form-grid" id="formExp" novalidate>
        <?= csrf_field() ?>
        <label class="full">Título <input name="titulo" value="<?= e($d['titulo']) ?>" maxlength="150" required></label>
        <label>Categoría
            <select name="id_categoria"><option value="0">Sin categoría</option>
            <?php foreach ($categorias as $c): ?><option value="<?= $c['id_categoria'] ?>" <?= (int)$d['id_categoria'] === (int)$c['id_categoria'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option><?php endforeach; ?>
            </select></label>
        <label>Ubicación <input name="ubicacion" value="<?= e($d['ubicacion']) ?>" placeholder="Ej: Guatavita, Cundinamarca" required></label>
        <label class="full">Descripción <textarea name="descripcion" rows="5" maxlength="1000" data-counter required><?= e($d['descripcion']) ?></textarea><small class="contador"></small></label>
        <label>Precio por persona (COP) <input type="number" name="precio" value="<?= e($d['precio']) ?>" min="1000" step="500" required></label>
        <label>Duración (horas) <input type="number" name="duracion_horas" value="<?= (int)$d['duracion_horas'] ?>" min="1" max="72" required></label>
        <label>Cupos disponibles <input type="number" name="cupos_disponibles" value="<?= (int)$d['cupos_disponibles'] ?>" min="0" max="500" required></label>
        <label>Imagen (URL, opcional) <input type="url" name="imagen_url" value="<?= e($d['imagen_url']) ?>"></label>
        <p class="error-campo full" id="errForm" hidden></p>
        <div class="full acciones"><button class="btn btn-primario" type="submit">Guardar</button><a class="btn btn-borde" href="mis_experiencias.php">Cancelar</a></div>
    </form>
</main>
<?php require 'includes/footer.php'; ?>
