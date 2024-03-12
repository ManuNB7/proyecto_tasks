<?php if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"] ?>">
        <?php echo $_GET["msg"] ?>
    </h2>
<?php } ?>

<h2>Listado de tareas con subtareas completadas</h2>
<?php foreach ($datos as $index => $subtarea) { ?>
    <?php if ($index === 0 || $subtarea['tarea'] !== $datos[$index - 1]['tarea']) { ?>
        <div class="contenedorT">
            <table>
                <thead>
                    <tr>
                        <th colspan="2">Tarea: <?php echo htmlspecialchars($subtarea['tarea'], ENT_QUOTES); ?></th>
                    </tr>
                    <tr>
                        <th>Subtarea</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
    <?php } ?>
    <tr>
        <td <?php if ($subtarea['completada'] == 1) echo 'class="completed"'; ?>><?php echo htmlspecialchars($subtarea['titulo'], ENT_QUOTES); ?></td>
        <td><?php echo $subtarea['completada'] == 1 ? 'Completada' : 'Pendiente'; ?></td>
    </tr>
    <?php if ($index === count($datos) - 1 || $subtarea['tarea'] !== $datos[$index + 1]['tarea']) { ?>
                <?php 
                    // Verificar si todas las subtareas están completadas
                    $todas_completadas = true;
                    foreach ($datos as $subt) {
                        if ($subt['tarea'] === $subtarea['tarea'] && $subt['completada'] != 1) {
                            $todas_completadas = false;
                            break;
                        }
                    } 
                    // Si todas las subtareas están completadas, agregar fila extra con enlace de eliminación
                    if ($todas_completadas) {
                        echo '<tr>';
                        echo '<td colspan="2">Todas las subtareas están completadas, ¿eliminar tarea?';
                        echo '<a href="index.php?controller=tarea&action=borrado&id=' . $subtarea['idTar'] . '" class="boton-forms" id="boton-del">Eliminar</a></td>';
                        echo '</tr>';
                    }
                ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
<?php } ?>
<a href="index.php?controller=tarea&action=listar_tarea" class="boton-forms">Volver al listado</a>