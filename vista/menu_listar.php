<?php if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"] ?>">
        <?php echo $_GET["msg"] ?>
    </h2>
<?php } ?>
<a href="index.php?controller=tarea&action=exportar_pdf" class="boton-forms" target="_blank">Descargar tareas y subtareas como PDF</a>
<div class="contenedor">
    <?php if (empty($datos)) { ?>
        <h2>No hay tareas disponibles.</h2>
    <?php } else { ?>
        <?php foreach ($datos as $tarea) { ?>
            <div class="contenido">
                <h3>Tarea: <?php echo htmlspecialchars($tarea['titulo'], ENT_QUOTES); ?></h3>
                <p>Detalle: <?php echo htmlspecialchars($tarea['detalle'], ENT_QUOTES); ?></p>
                <p>Fecha: <?php echo htmlspecialchars($tarea['fecha'], ENT_QUOTES); ?></p>
                <?php if (!empty($tarea['archivo'])) { ?>
                    <?php
                    $file_extension = pathinfo($tarea['archivo'], PATHINFO_EXTENSION);
                    if (in_array($file_extension, array("jpg", "jpeg", "png", "gif"))) {
                        echo '<img src="' . $tarea['archivo'] . '" alt="Imagen adjunta" class="img-small">';
                    } else {
                        echo '<p>Archivo: <a href="' . $tarea['archivo'] . '" target="_blank">Descargar archivo</a></p>';
                    }
                    ?>
                <?php } ?>
                <div class="acciones">
                    <!-- Botón para modificar la tarea -->
                    <a href="index.php?controller=tarea&action=modificar_tarea&id=<?php echo $tarea['idTar']; ?>"><i class="fa-regular fa-pen-to-square"></i></a>
                    <!-- Botón para eliminar tareas -->      
                    <a href="index.php?controller=tarea&action=borrado&id=<?php echo $tarea['idTar']; ?>"><i class="fa-regular fa-circle-xmark"></i></a>
                </div>
                <?php if(isset($tarea['subtareas']) && !empty($tarea['subtareas'])) { ?>
                    <div class="subtareas">
                        <hr/>
                        <h3>Subtareas</h3>
                        <?php foreach ($tarea['subtareas'] as $subtarea) { ?>
                            <div class="subtarea">
                                <h4><?php echo htmlspecialchars($subtarea['titulo'], ENT_QUOTES); ?></h4>
                                <p>Detalle: <?php echo htmlspecialchars($subtarea['detalle'], ENT_QUOTES); ?></p>
                                <p>Fecha: <?php echo htmlspecialchars($subtarea['fecha'], ENT_QUOTES); ?></p>
                                <!-- Botón para marcar como completada una subtarea -->
                                <a href="index.php?controller=tarea&action=marcar_completada&idTarea=<?php echo $tarea['idTar']; ?>&idSub=<?php echo $subtarea['idSub']; ?>"class="boton-forms">Completar</a>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
</div>
