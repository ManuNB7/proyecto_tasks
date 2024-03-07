<?php

    require_once __DIR__.'/../modelo/sesion.php';
    /**
     * Clase sesionController
     * 
     * Controlador para la gestión de sesiones de usuario.
     */
    class sesionController {

        public $titulo;
        public $modelo;
        public $view;
        /**
         * Constructor de la clase.
         * 
         * Inicializa las propiedades de la clase y crea una instancia del modelo sesionModel.
         */
        public function __construct() {
            $this->modelo = new sesionModel();
            $this->view = "inicio_sesion"; 
            $this->titulo = "Inicio de sesion";
        }

        /**
         * Inicia sesión con el nombre de usuario y contraseña proporcionados.
         */
        // Inicio_sesion method
        function inicio_sesion() {
            $nombre = $_POST["nombre"];
            $pw = $_POST["pw"];
            
            if ($this->validar($nombre, $pw)) {
                if ($this->modelo->comprobar_usuario($nombre, $pw)) {
                    $user_id = $this->modelo->obtener_id_usuario($nombre);
                    
                    if (session_status() === PHP_SESSION_NONE) {
                        session_start();
                    }

                    // Set the user ID in the session
                    $_SESSION['user_id'] = $user_id;
                    // Set the user ID in the session
                    $_SESSION['nombre'] = $nombre;

                    // Redirect para usuarios
                    header('Location: index.php?controller=tarea&action=menu_tareas');
                    exit();
                } else {
                    $_GET["error"] = $this->modelo->error;
                }
            }
        }

        // checkSession método
        public function checkSession() {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            
            if (!isset($_SESSION['user_id'])) {
                header('Location: index.php?controller=sesion&action=mostrar_inicio_sesion');
                exit();
            }
        }
        
        /**
         * Cierra la sesión del usuario.
         */
        function cerrar_sesion(){
            // Empieza sesion si no la hay
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $_SESSION = array();

            // Destruye la sesión
            session_destroy();
        }
        /**
         * Valida si los campos del formulario no están vacíos.
         */
        function validar($nombre, $pw){
            if(empty($nombre)||empty($pw)){
                $_GET["error"] = "Debes rellenar el nombre y la contraseña";
                return false;
            }
        
            if (preg_match('/^\s*$/', $nombre) || preg_match('/^\s*$/', $pw)) {
                $_GET["error"] = "No se permiten campos vacíos";
                return false;
            }
            return true;
        }
    }

?>