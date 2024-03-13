<h1>Sugerencias</h1>

<?php if(isset($_COOKIE['ultima_tarea_consultada'])): ?>
    <?php
    $ultimaTareaConsultada = $_COOKIE['ultima_tarea_consultada'];
    ?>
    <h2>Última tarea consultada:</h2>
    <p>ID de la tarea: <?php echo $ultimaTareaConsultada; ?></p>
    <!-- Aquí podrías agregar más detalles de la última tarea consultada si lo deseas -->
    <a href="index.php?controller=tarea&action=ver_tarea&id=<?php echo $ultimaTareaConsultada; ?>" class="boton-forms">Ver tarea</a>
<?php else: ?>
    <p>No hay información sobre la última tarea consultada.</p>
<?php endif; ?>

<?php if(isset($_COOKIE['tarea_cercana_id']) && isset($_COOKIE['tarea_cercana_fecha'])): ?>
    <?php
    $tareaCercanaId = $_COOKIE['tarea_cercana_id'];
    $tareaCercanaFecha = $_COOKIE['tarea_cercana_fecha'];
    ?>
    <h2>Tarea más cercana:</h2>
    <p>ID de la tarea: <?php echo $tareaCercanaId; ?></p>
    <p>Fecha de entrega: <?php echo $tareaCercanaFecha; ?></p>
    <!-- Aquí podrías agregar más detalles de la tarea más cercana si lo deseas -->
    <a href="index.php?controller=tarea&action=ver_tarea&id=<?php echo $tareaCercanaId; ?>" class="boton-forms">Ver tarea</a>
<?php else: ?>
    <p>No hay información sobre la tarea más cercana.</p>
<?php endif; ?>
