<?php if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"] ?>">
        <?php echo $_GET["msg"] ?>
    </h2>
<?php } ?>
<h2>Formulario de Tarea Prinicipal y Subtareas</h2>
<form action="index.php?controller=tarea&action=guardar_subtareas" method="POST" enctype="multipart/form-data">
    <!-- Agregar campo oculto para el idUsuario -->
    <h3><?php echo $datos['titulo']; ?></h3>
    <input type="hidden" name="titulo" value="<?php echo $datos['titulo']; ?>">
    <label for="detalle">Detalle:</label>
    <textarea name="detalle"></textarea>
    <label for="fecha">Fecha:</label>
    <input type="date" name="fecha">
    <label for="archivo_principal">Archivo: (se admiten JPG, PNG, JPEG, GIF, PDF, HTML.)</label>
    <input type="file" name="archivo_principal">
    <br>
    <hr>
    <?php for ($i = 1; $i <= $datos['num_subtareas']; $i++): ?>
        <h3>Subtarea <?php echo $i; ?></h3>
        <label for="subtitulo<?php echo $i; ?>">Título:</label>
        <input type="text" name="subtarea[<?php echo $i; ?>][titulo]">
        <label for="subdescripcion<?php echo $i; ?>">Descripción:</label>
        <textarea name="subtarea[<?php echo $i; ?>][detalle]"></textarea>
        <label for="subfecha<?php echo $i; ?>">Fecha:</label>
        <input type="date" name="subtarea[<?php echo $i; ?>][fecha]">
    <?php endfor; ?>
    <input type="submit" value="Guardar">
    <a href="index.php?controller=tarea&action=form_tarea" class="boton-forms">Volver atrás</a>
</form>
