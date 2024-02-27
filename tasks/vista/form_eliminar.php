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
    <a href="index.php?controller=tarea&action=eliminar_tarea&id=<?php echo $id ?>">Sí</a> | <a href="index.php?controller=tarea&action=listar_tarea">No</a>
</div>
