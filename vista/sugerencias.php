<h1>Sugerencias</h1>

<h2>Última tarea consultada:</h2>
<?php if ($ultima_tarea): ?>
    <p><?php echo $ultima_tarea['titulo']; ?></p>
    <!-- Mostrar más detalles de ser necesario -->
<?php else: ?>
    <p>No hay ninguna tarea consultada recientemente.</p>
<?php endif; ?>

<h2>Tarea asignada para hoy:</h2>
<?php if ($tarea_actual): ?>
    <p><?php echo $tarea_actual['titulo']; ?></p>
    <!-- Mostrar más detalles de ser necesario -->
<?php else: ?>
    <p>No hay ninguna tarea asignada para hoy.</p>
<?php endif; ?>
