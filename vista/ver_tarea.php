<?php if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"] ?>">
        <?php echo $_GET["msg"] ?>
    </h2>
<?php } ?>
<div class="contenedor">
    <?php if (empty($datos)) { ?>
        <h2>No hay subtareas disponibles.</h2>
    <?php } else { ?>
        <div class="titulo-tarea">
            <h1><?php echo htmlspecialchars($datos['titulo'], ENT_QUOTES); ?></h1>
            <div class="botones">
                <a href="index.php?controller=tarea&action=modificar_tarea&id=<?php echo $datos['idTar']; ?>" class="boton-forms" id="boton-mod">Modificar</a>
                <a href="index.php?controller=tarea&action=borrado&id=<?php echo $datos['idTar']; ?>" class="boton-forms" id="boton-del">Eliminar</a>
            </div>
        </div>
        <div class="menu-contenido">
            <?php if (!empty($datos['subtareas'])) { ?>
                <?php foreach ($datos['subtareas'] as $subtarea) { ?>
                    <div class="subtarea">
                        <h3><?php echo htmlspecialchars($subtarea['titulo'], ENT_QUOTES); ?></h3>
                        <p><?php echo htmlspecialchars($subtarea['detalle'], ENT_QUOTES); ?></p>
                        <p>Fecha: <?php echo date('d/m/Y', strtotime($subtarea['fecha'])); ?></p>
                        <a href="index.php?controller=tarea&action=marcar_completada&idTarea=<?php echo $datos['idTar']; ?>&idSub=<?php echo $subtarea['idSub']; ?>" class="boton-forms" id="boton-com">Completar</a>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <h3>No hay subtareas disponibles.</h3>
            <?php } ?>
        </div>
    <?php } ?>
</div>
<a href="index.php?controller=tarea&action=listar_tarea" class="boton-forms">Volver atrás</a>
