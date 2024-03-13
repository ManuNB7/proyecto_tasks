<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $controlador->titulo ?></title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<?php if (!empty($_SESSION)) { ?>
    <nav class="menu">
        <h1 id="titulin">TASKS</h1>
        <a href='index.php?controller=tarea&action=menu_tareas' class="menu-contenido">Inicio</a>
        <a href='index.php?controller=tarea&action=form_tarea' class="menu-contenido">Añadir tareas</a>
        <a href='index.php?controller=tarea&action=listar_tarea' class="menu-contenido">Listar tareas</a>
        <a href='index.php?controller=tarea&action=listar_completadas' class="menu-contenido">Subtareas pendientes</a>
        <a href='index.php?controller=tarea&action=ver_sugerencias' class="menu-contenido">Sugerencias</a>
        <a href="index.php?controller=sesion&action=cerrar_sesion" class="menu-contenido" id="cerrar">Cerrar sesión</a>
    </nav>
<?php } ?>

<main>

