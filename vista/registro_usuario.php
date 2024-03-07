<br>
<h1>Regístrate si aún no lo has hecho<h1>
<br>
<form action="index.php?controller=registro&action=registrar_usuario" method="POST">
    <?php if(isset($_GET["error"])){ echo "<h2>".$_GET["error"]."</h2>"; } ?>
    <label for="nombre">Nombre de usuario</label>
    <input type="text" name="nombre" id="nombre" placeholder="Nombre completo o nickname">
    <label for="correo">Correo</label>
    <input type="text" name="correo" id="correo" placeholder="correo@gmail.com">
    <label for="pw">Contraseña</label>
    <input type="password" name="pw" id="pw">
    <br>
    <input type="submit" name="enviar">
    <!-- Enlace para ir a la vista de inicio de sesión -->
    <a href="index.php?controller=sesion&action=iniciar_sesion" class="boton-forms">Inicio de sesión</a>
</form>