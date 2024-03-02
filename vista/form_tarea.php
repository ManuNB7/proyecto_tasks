<h2>Formulario de Tarea Principal</h2>

<?php if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"] ?>">
        <?php echo $_GET["msg"] ?>
    </h2>
<?php } ?>

<form action="index.php?controller=tarea&action=guardar_tarea" method="POST" class="box1">
    <label for="titulo">Título:</label>
    <input type="text" name="titulo">
    <label for="num_subtareas">Número de Subtareas:</label>
    <input type="number" name="num_subtareas" required>
    <input type="submit" value="Continuar">
</form>
