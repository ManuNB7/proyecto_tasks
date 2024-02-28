<?php if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"]; ?>">
        <?php echo $_GET["msg"]; ?>
    </h2>
<?php } ?>
<a href="index.php?controller=tarea&action=exportar_pdf" class="boton-forms" target="_blank" id="pdf">Descargar tareas y subtareas como PDF</a>
<div class="contenedor">
    <?php if (empty($datos)) { ?>
        <h2>No hay tareas disponibles.</h2>
    <?php } else { ?>
        <table class="tabla-tareas">
            <thead>
                <tr>
                    <th>Tarea</th>
                    <th>Detalle</th>
                    <th>Fecha</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $tarea) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($tarea['titulo'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($tarea['detalle'], ENT_QUOTES); ?></td>
                        <td><?php echo htmlspecialchars($tarea['fecha'], ENT_QUOTES); ?></td>
                        <td>
                            <?php if (!empty($tarea['archivo'])) { ?>
                                <?php
                                $file_extension = pathinfo($tarea['archivo'], PATHINFO_EXTENSION);
                                if (in_array($file_extension, array("jpg", "jpeg", "png", "gif"))) {
                                    echo '<img src="img/archivos/' . $tarea['archivo'] . '" alt="Imagen adjunta" class="img-small">';
                                } else {
                                    echo '<a href="' . $tarea['archivo'] . '" target="_blank">Descargar archivo</a>';
                                }
                                ?>
                            <?php } ?>
                            <a href="index.php?controller=tarea&action=ver_tarea&id=<?php echo $tarea['idTar']; ?>" class="boton-forms" id="boton-ver">Ver más</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
