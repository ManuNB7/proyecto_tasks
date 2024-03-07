<?php
if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"]; ?>">
        <?php echo $_GET["msg"]; ?>
    </h2>
<?php } ?>
<div class="contenedor">
    <?php if (empty($datos)) { ?>
        <h2>No hay tareas disponibles.</h2>
    <?php } else { ?>
        <a href="index.php?controller=tarea&action=exportar_pdf" class="boton-forms" target="_blank" id="pdf">Descargar tareas y subtareas como PDF</a>
        <table class="tabla-tareas">
            <thead>
                <tr id="titulos">
                    <th>TAREA</th>
                    <th>OPCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $tarea) { ?>
                    <tr>
                        <td class="celdas">
                            <strong>Título:</strong> <?php echo htmlspecialchars($tarea['titulo'], ENT_QUOTES); ?><br>
                            <strong>Detalle:</strong> <?php echo htmlspecialchars($tarea['detalle'], ENT_QUOTES); ?><br>
                            <strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($tarea['fecha'])); ?><br>
                            <?php if (!empty($tarea['archivo'])) { ?>
                                <?php
                                $file_extension = pathinfo($tarea['archivo'], PATHINFO_EXTENSION);
                                if (in_array($file_extension, array("jpg", "jpeg", "png", "gif"))) {
                                    echo '<img src="uploads/' . $tarea['archivo'] . '" alt="Imagen adjunta" class="img-small"><br>';
                                } else {
                                    echo '<strong>Archivo adjunto:</strong> ' . htmlspecialchars($tarea['archivo'], ENT_QUOTES) . '<br> <a href="uploads/' . $tarea['archivo'] . '" target="_blank" download>Descargar archivo</a><br>';
                                }
                                ?>
                            <?php } ?>
                        </td>
                        <td class="archivos">
                            <a href="index.php?controller=tarea&action=ver_tarea&id=<?php echo $tarea['idTar']; ?>" class="boton-forms" id="boton-ver">Ver subtareas</a>
                            <a href="index.php?controller=tarea&action=modificar_tarea&id=<?php echo $tarea['idTar']; ?>" class="boton-forms" id="boton-mod">Modificar</a>
                            <a href="index.php?controller=tarea&action=borrado&id=<?php echo $tarea['idTar']; ?>" class="boton-forms" id="boton-del">Eliminar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
