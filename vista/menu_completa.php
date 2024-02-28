<?php if(isset($_GET["msg"])){ ?>
    <h2 id="<?php echo $_GET["tipomsg"] ?>">
        <?php echo $_GET["msg"] ?>
    </h2>
<?php } ?>
<div class="contenedorT">
       <?php if (empty($datos)) { ?>
        <h2>No hay subtareas completadas.</h2>
    <?php } else { ?>
    <h2>Listado de tareas con subtareas completadas</h2>
    <table>
        <thead>
            <tr>
                <th>Subtarea</th>
                <th>Tarea</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($datos as $subtarea) { ?>
                <tr>
                    <td <?php if ($subtarea['completada'] == 1) echo 'class="completed"'; ?>><?php echo htmlspecialchars($subtarea['titulo'], ENT_QUOTES); ?></td>
                    <td><?php echo htmlspecialchars($subtarea['tarea'], ENT_QUOTES); ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <?php } ?>
</div>
