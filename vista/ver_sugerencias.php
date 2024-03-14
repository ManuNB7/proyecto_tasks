<h1>Sugerencias</h1>

<?php if(isset($datos['titulo'])): ?>
    <h2>Datos de la última tarea consultada:</h2>
    <p>Título: <?php echo $datos['titulo']; ?></p>
    <p>Detalle: <?php echo $datos['detalle']; ?></p>
    <?php if(isset($datos['fecha'])): ?>
        <p>Fecha: <?php echo date('d/m/Y', strtotime($datos['fecha'])); ?></p>
    <?php endif; ?>
<?php else: ?>
    <p>No hay información disponible sobre la última tarea consultada.</p>
<?php endif; ?>

<?php if(isset($_COOKIE['tarea_cercana_id']) && isset($_COOKIE['tarea_cercana_fecha'])): ?>
    <?php
    $tareaCercanaId = $_COOKIE['tarea_cercana_id'];
    $tareaCercanaFecha = $_COOKIE['tarea_cercana_fecha'];
    ?>
    <h2>Tarea más cercana:</h2>
    <p>ID de la tarea: <?php echo $tareaCercanaId; ?></p>
    <?php if(isset($tareaCercanaFecha)): ?>
        <p>Fecha de entrega: <?php echo date('d/m/Y', strtotime($tareaCercanaFecha)); ?></p>
    <?php endif; ?>
    <a href="index.php?controller=tarea&action=ver_tarea&id=<?php echo $tareaCercanaId; ?>" class="boton-forms">Ver tarea</a>
<?php else: ?>
    <p>No hay información sobre la tarea más cercana.</p>
<?php endif; ?>