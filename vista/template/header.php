<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $controlador->titulo ?></title>
        <script src="https://kit.fontawesome.com/1596d518db.js" crossorigin="anonymous"></script>
        <link rel="stylesheet" href="css/estilos.css">
    </head>
    <body>
    <nav class="menu">
    <h1 id="titulin">TASKS</h1>
    <a href='index.php?controller=menu' class="menu-contenido">Inicio</a>
    <a href='index.php?controller=tarea' class="menu-contenido">Añadir tarea</a>
    <a href='index.php?controller=tarea&action=listar_tarea' class="menu-contenido">Listar tarea</a>
    <a href='index.php?controller=tarea&action=listar_sugerencias' class="menu-contenido">Sugerencias</a>
    <a href='index.php?controller=tarea&action=listar_completadas' class="menu-contenido">Subtareas completadas</a>
    <?php if (!empty($_SESSION)) { ?>
       <a href="index.php?controller=sesion&action=cerrar_sesion" class="menu-contenido">Cerrar sesión</a>
    <?php } ?>
    </nav>
<main>