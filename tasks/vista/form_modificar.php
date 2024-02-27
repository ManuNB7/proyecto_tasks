<?php if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"] ?>">
        <?php echo $_GET["msg"] ?>
    </h2>
<?php } ?>
<h2>Formulario de Modificación de Tarea</h2>
<form action="index.php?controller=tarea&action=guardar_modificacion&id=<?php echo $datos['idTar']; ?>" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="idTarea" value="<?php echo $datos['idTar']; ?>">
    <label for="titulo">Título:</label>
    <input type="text" name="titulo" value="<?php echo htmlspecialchars($datos['titulo'], ENT_QUOTES); ?>">
    <label for="detalle">Detalle:</label>
    <textarea name="detalle"><?php echo htmlspecialchars($datos['detalle'], ENT_QUOTES); ?></textarea>
    <label for="fecha">Fecha:</label>
    <input type="date" name="fecha" value="<?php echo htmlspecialchars($datos['fecha'], ENT_QUOTES); ?>">
    <label for="archivo_principal">Archivo:</label>
    <input type="file" name="archivo_principal">
    <?php foreach ($datos['subtareas'] as $index => $subtarea): ?>
        <h3>Subtarea <?php echo $index + 1; ?></h3>
        <input type="hidden" name="subtarea[<?php echo $index; ?>][idSub]" value="<?php echo $subtarea['idSub']; ?>">
        <label for="subtitulo<?php echo $index; ?>">Título:</label>
        <input type="text" name="subtarea[<?php echo $index; ?>][titulo]" value="<?php echo htmlspecialchars($subtarea['titulo'], ENT_QUOTES); ?>" required>
        <label for="subdescripcion<?php echo $index; ?>">Descripción:</label>
        <textarea name="subtarea[<?php echo $index; ?>][detalle]"><?php echo htmlspecialchars($subtarea['detalle'], ENT_QUOTES); ?></textarea>
        <label for="fecha">Fecha:</label>
        <input type="date" name="subtarea[<?php echo $index; ?>][fecha]" value="<?php echo htmlspecialchars($subtarea['fecha'], ENT_QUOTES); ?>">
    <?php endforeach; ?>
    <input type="submit" value="Guardar">
    <a href="index.php?controller=tarea&action=vista_subtarea&id=<?php echo $datos['idTar']; ?>">Añadir subtarea</a>
    <a href="index.php?controller=tarea&action=listar_tarea">Volver atrás</a>
</form>
