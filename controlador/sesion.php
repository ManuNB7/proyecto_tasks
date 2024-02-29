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
            // Validate input
            $nombre = $_POST["nombre"];
            $pw = $_POST["pw"];
            
            if ($this->validar($nombre, $pw)) {
                if ($this->modelo->comprobar_usuario($nombre, $pw)) {
                    // Get the user ID from the model after successful login
                    $user_id = $this->modelo->obtener_id_usuario($nombre);
                    
                    // Start the session
                    session_start();

                    // Set the user ID in the session
                    $_SESSION['user_id'] = $user_id;

                    // Redirect to the appropriate view for normal users
                    header('Location: index.php?controller=tarea&action=menu_tareas');
                    exit();
                } else {
                    // Set error message if login fails
                    $_GET["error"] = $this->modelo->error;
                }
            }
        }

        // checkSession method
        public function checkSession() {
            session_start();

            if (!isset($_SESSION['user_id'])) {
                header('Location: index.php?controller=sesion&action=mostrar_inicio_sesion');
                exit();
            }
        }
        

        /**
         * Valida si los campos del formulario no están vacíos.
         */
        function validar($nombre, $pw){
            if(empty($nombre)||empty($pw)){
                $_GET["error"] = "Debes rellenar el nombre y la contraseña";
                return false;
            }
            return true;
        }
        
        /**
         * Cierra la sesión del usuario.
         */
        function cerrar_sesion(){
            $_SESSION = array();
        }
    }

?>