<?php /* Tabla reutilizable de reservas. Espera $reservas y $u. */ ?>
<div class="filtro-tabs" role="tablist">
    <button type="button" class="tab activo" data-estado="todas">Todas</button>
    <button type="button" class="tab" data-estado="pendiente">Pendientes</button>
    <button type="button" class="tab" data-estado="confirmada">Confirmadas</button>
    <button type="button" class="tab" data-estado="cancelada">Canceladas</button>
</div>
<div class="tabla-wrap">
<table class="tabla" id="tablaReservas">
    <thead><tr><th>Experiencia</th><?php if ($u['rol'] !== 'turista'): ?><th>Turista</th><?php endif; ?><th>Fecha</th><th>Pers.</th><th>Total</th><th>Estado</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach ($reservas as $r): ?>
        <tr data-estado="<?= e($r['estado']) ?>">
            <td><?= e($r['titulo']) ?></td>
            <?php if ($u['rol'] !== 'turista'): ?><td><?= e($r['turista']) ?></td><?php endif; ?>
            <td><?= e(date('d/m/Y', strtotime($r['fecha_reserva']))) ?></td>
            <td><?= (int)$r['cantidad_personas'] ?></td>
            <td><?= money($r['total']) ?></td>
            <td><?= estado_badge($r['estado']) ?></td>
            <td><?= acciones_reserva($r, $u['rol']) ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if (!$reservas): ?><tr><td colspan="7" class="vacio">Sin reservas todavía.</td></tr><?php endif; ?>
    </tbody>
</table>
</div>
