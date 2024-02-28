<?php
$id = $_GET['id'] ?? '';
?>

<?php if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"] ?>">
        <?php echo $_GET["msg"] ?>
    </h2>
<?php } ?>

<div id="eliminar">
    <h2>¿Estás seguro de que deseas eliminar esta tarea?</h2>
    Las subtareas asociadas a esta tarea se eliminarán y no aparecerán en completadas.
    <div class="contenedor-botones">
    <a href="index.php?controller=tarea&action=eliminar_tarea&id=<?php echo $id ?>" class="boton-forms">Sí</a> | <a href="index.php?controller=tarea&action=listar_tarea" class="boton-forms">No</a>
    </div>
</div>