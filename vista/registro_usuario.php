<?php if(isset($_GET["error"])){ echo "<h2>".$_GET["error"]."</h2>"; } ?>
<form action="index.php?controller=registro&action=registrar_usuario" method="POST">
    <h1>Registro de usuario</h1>
    <?php if(isset($_GET["error"])){ echo "<h2>".$_GET["error"]."</h2>"; } ?>
    <label for="nombre">Nombre de usuario</label>
    <input type="text" name="nombre" id="nombre">
    <label for="correo">Correo</label>
    <input type="text" name="correo" id="correo">
    <label for="pw">Contraseña</label>
    <input type="password" name="pw" id="pw">
    <input type="submit" name="enviar">
    <!-- Enlace para ir a la vista de inicio de sesión -->
    <a href="index.php?controller=sesion&action=iniciar_sesion">Inicio de sesión</a>
</form>