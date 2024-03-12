<?php if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"]; ?>">
        <?php echo $_GET["msg"]; ?>
    </h2>
<?php } ?>
<div class="contenedor">
    <?php if (empty($datos['subtareas'])) { ?>
        <h2>No hay subtareas disponibles para esta tarea.</h2>
    <?php } else { ?>
        <div class="titulo-tarea">
            <h1 class="subtarea-celda">Tarea: <?php echo htmlspecialchars($datos['titulo'], ENT_QUOTES); ?></h1>
            <h3 class="subtarea-celda">Detalles de la tarea: <?php echo htmlspecialchars($datos['detalle'], ENT_QUOTES); ?></h3>
            <h3>Fecha: <?php echo !empty($datos['fecha']) ? date('d/m/Y', strtotime($datos['fecha'])) : 'Sin fecha'; ?></h3>
            <br>
        </div>
        <table class="tabla-subtareas">
            <thead>
                <tr>
                    <th>Subtarea</th>
                    <th>Detalle</th>
                    <th>Fecha</th>
                    <th>Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos['subtareas'] as $subtarea) { ?>
                    <tr>
                        <td class="subtarea-celda"><?php echo htmlspecialchars($subtarea['titulo'], ENT_QUOTES); ?></td>
                        <td class="subtarea-celda"><?php echo htmlspecialchars($subtarea['detalle'], ENT_QUOTES); ?></td>
                        <?php if (!empty($subtarea['fecha'])) { ?> 
                            <td><?php echo date('d/m/Y', strtotime($subtarea['fecha'])); ?></td>
                        <?php } else { ?>
                            <td>Sin fecha</td>
                        <?php } ?>
                        <td>
                            <a href="index.php?controller=tarea&action=marcar_completada&idTarea=<?php echo $datos['idTar']; ?>&idSub=<?php echo $subtarea['idSub']; ?>" class="boton-forms" id="boton-com">Completar</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } ?>
</div>
<div class="botones">
    <a href="index.php?controller=tarea&action=modificar_tarea&id=<?php echo $datos['idTar']; ?>" class="boton-forms" id="boton-mod">Modificar</a>
    <a href="index.php?controller=tarea&action=borrado&id=<?php echo $datos['idTar']; ?>" class="boton-forms" id="boton-del">Eliminar</a>
    <a href="index.php?controller=tarea&action=listar_tarea" class="boton-forms">Volver atrás</a>
</div>
