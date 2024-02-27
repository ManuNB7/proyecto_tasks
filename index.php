<?php 

    require_once __DIR__.'/config/config.php';

    //session_start();

    if(isset($_GET["controller"])) {
        $nombreControlador = $_GET["controller"];
    } else{
        //Si no se ha enviado un controlador por url se toma el controlador por defecto (el menú principal)
        $nombreControlador = constant("DEFAULT_CONTROLLER");
    }

    //Si no se envia un método se deja la variable en blanco
    $nombreMetodo = $_GET["action"] ?? '';

    $rutaControlador = __DIR__.'/controlador/'.$nombreControlador.'.php';

    /* Comprueba que el controlador enviado por url existe y si no utiliza el controlador por defecto */
    if(!file_exists($rutaControlador)){
        $rutaControlador = __DIR__.'/controlador/'.constant("DEFAULT_CONTROLLER").'.php';
        $nombreControlador = constant("DEFAULT_CONTROLLER");
    }


    // Carga el controlador
    require_once $rutaControlador;
    $nombreClaseControlador = $nombreControlador.'Controller';
    $controlador = new $nombreClaseControlador();

    // Si el método existe lo llama y guarda lo que devuelve en una variable
    $datos = array();
    if(method_exists($controlador,$nombreMetodo)) $datos = $controlador->{$nombreMetodo}();

    /* Carga las vistas */
    require_once __DIR__.'/vista/template/header.php';
    require_once __DIR__.'/vista/'.$controlador->view.'.php';
    require_once __DIR__.'/vista/template/footer.php';

?>