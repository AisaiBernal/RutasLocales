<?php
$titulo = 'Explorar';
require 'includes/header.php';

// Filtros (GET) -> consulta a MySQL con parámetros
$q = trim($_GET['q'] ?? '');
$cat = (int)($_GET['categoria'] ?? 0);
$max = (int)($_GET['precio_max'] ?? 0);
$orden = $_GET['orden'] ?? 'nuevo';

$sql = "SELECT e.*, c.nombre AS categoria, c.icono, u.nombre AS guia
        FROM experiencias e
        JOIN usuarios u ON u.id_usuario = e.id_guia
        LEFT JOIN categorias c ON c.id_categoria = e.id_categoria
        WHERE e.activa = 1 AND e.cupos_disponibles > 0";
$p = [];
if ($q !== '')  { $sql .= " AND (e.titulo LIKE ? OR e.ubicacion LIKE ? OR e.descripcion LIKE ?)"; array_push($p, "%$q%", "%$q%", "%$q%"); }
if ($cat > 0)   { $sql .= " AND e.id_categoria = ?"; $p[] = $cat; }
if ($max > 0)   { $sql .= " AND e.precio <= ?"; $p[] = $max; }
$sql .= match ($orden) { 'barato' => " ORDER BY e.precio ASC", 'caro' => " ORDER BY e.precio DESC", default => " ORDER BY e.fecha_creacion DESC" };
$stmt = $pdo->prepare($sql); $stmt->execute($p);
$lista = $stmt->fetchAll();
$categorias = $pdo->query("SELECT * FROM categorias ORDER BY nombre")->fetchAll();
$total_guias = $pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol='guia'")->fetchColumn();
?>
<section class="hero">
    <div class="hero-txt">
        <h1>Caminos que solo conocen los de aquí</h1>
        <p>Reserva experiencias con <?= (int)$total_guias ?> guía(s) locales de Bogotá y Cundinamarca. Sin intermediarios, con cupos reales.</p>
    </div>
    <form class="filtros" method="GET" action="index.php" id="filtros">
        <label class="campo-buscar"><i class="fa-solid fa-magnifying-glass"></i>
            <input type="search" name="q" id="buscador" value="<?= e($q) ?>" placeholder="Destino, ciudad o actividad" data-live-filter=".card-exp">
        </label>
        <select name="categoria" aria-label="Categoría">
            <option value="0">Todas las categorías</option>
            <?php foreach ($categorias as $c): ?>
                <option value="<?= $c['id_categoria'] ?>" <?= $cat === (int)$c['id_categoria'] ? 'selected' : '' ?>><?= e($c['nombre']) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="precio_max" aria-label="Precio máximo">
            <option value="0">Cualquier precio</option>
            <?php foreach ([40000, 60000, 80000, 120000] as $pm): ?>
                <option value="<?= $pm ?>" <?= $max === $pm ? 'selected' : '' ?>>Hasta <?= money($pm) ?></option>
            <?php endforeach; ?>
        </select>
        <select name="orden" aria-label="Ordenar">
            <option value="nuevo" <?= $orden === 'nuevo' ? 'selected' : '' ?>>Más recientes</option>
            <option value="barato" <?= $orden === 'barato' ? 'selected' : '' ?>>Menor precio</option>
            <option value="caro" <?= $orden === 'caro' ? 'selected' : '' ?>>Mayor precio</option>
        </select>
        <button class="btn btn-primario" type="submit">Buscar</button>
    </form>
</section>

<main class="contenedor">
    <p class="resumen" id="resumen"><strong><?= count($lista) ?></strong> experiencia(s) disponible(s)</p>
    <div class="grid-cards">
        <?php foreach ($lista as $x): ?>
        <article class="card-exp" data-texto="<?= e(mb_strtolower($x['titulo'] . ' ' . $x['ubicacion'] . ' ' . $x['categoria'])) ?>">
            <div class="card-img" style="<?= $x['imagen_url'] ? "background-image:url('" . e($x['imagen_url']) . "')" : '' ?>">
                <?php if (!$x['imagen_url']): ?><i class="fa-solid <?= e($x['icono'] ?? 'fa-compass') ?>"></i><?php endif; ?>
                <?php if ($x['categoria']): ?><span class="chip"><?= e($x['categoria']) ?></span><?php endif; ?>
            </div>
            <div class="card-body">
                <h3><?= e($x['titulo']) ?></h3>
                <p class="meta"><i class="fa-solid fa-location-dot"></i> <?= e($x['ubicacion']) ?> · <i class="fa-regular fa-clock"></i> <?= (int)$x['duracion_horas'] ?> h</p>
                <p class="meta">Guía: <?= e($x['guia']) ?> · <?= (int)$x['cupos_disponibles'] ?> cupos</p>
                <div class="card-pie">
                    <span class="precio"><?= money($x['precio']) ?> <small>/ persona</small></span>
                    <a class="btn btn-borde" href="experiencia.php?id=<?= $x['id_experiencia'] ?>">Ver y reservar</a>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    </div>
    <?php if (!$lista): ?><p class="vacio">No hay experiencias con esos filtros. <a href="index.php">Ver todas</a></p><?php endif; ?>
</main>
<?php require 'includes/footer.php'; ?>
