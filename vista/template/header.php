<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $controlador->titulo ?></title>
        <link rel="stylesheet" href="css/estilos.css">
    </head>
    <body>
    <nav class="menu">
    <h1 id="titulin">TASKS</h1>
    <a href='index.php?controller=menu' class="menu-contenido">Inicio</a>
    <a href='index.php?controller=tarea' class="menu-contenido">Añadir tareas</a>
    <a href='index.php?controller=tarea&action=listar_tarea' class="menu-contenido">Listar tareas</a>
    <a href='index.php?controller=tarea&action=listar_completadas' class="menu-contenido">Subtareas completadas</a>
    <a href='index.php?controller=tarea&action=listar_sugerencias' class="menu-contenido">Sugerencias</a>
    <?php if (!empty($_SESSION)) { ?>
       <a href="index.php?controller=sesion&action=cerrar_sesion" class="menu-contenido">Cerrar sesión</a>
    <?php } ?>
</nav>

<main>