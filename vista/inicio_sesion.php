<?php if(isset($_GET["error"])){ echo "<h2>".$_GET["error"]."</h2>"; } ?>
<form action="index.php?controller=sesion&action=inicio_sesion" method="POST">
    <h1>Inicio de sesión</h1>
    <label for="nombre">Correo electrónico o nombre de usuario</label>
    <input type="text" name="nombre" id="nombre">
    <label for="pw">Contraseña</label>
    <input type="password" name="pw" id="pw">
    <input type="submit" name="enviar" value="Iniciar sesión">
    <!-- Enlace para ir a la vista de registro -->
    <a href="index.php?controller=registro&action=mostrar_registro">Registrarse</a>
</form>
