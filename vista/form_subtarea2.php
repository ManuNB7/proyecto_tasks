<?php if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"]; ?>">
        <?php echo $_GET["msg"]; ?>
    </h2>
<?php } ?>
<h2>Añadir Subtarea para la tarea "<?php echo htmlspecialchars($datos['titulo'], ENT_QUOTES); ?>"</h2>
<form action="index.php?controller=tarea&action=agregar_subtarea" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="idTarea" value="<?php echo htmlspecialchars($datos['idTarea'], ENT_QUOTES); ?>">
    <h3>Subtarea</h3>
    <label for="subtitulo">Título:</label>
    <input type="text" name="titulo" required>
    <label for="subdescripcion">Descripción:</label>
    <textarea name="detalle"></textarea>
    <label for="fecha">Fecha:</label>
    <input type="date" name="fecha">
    <input type="submit" value="Guardar">
    <a href="index.php?controller=tarea&action=listar_tarea" class="boton-forms">Cancelar</a>
</form>
